<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class StripeBillingService
{
    public function isDemoMode(): bool
    {
        return (bool) config('services.stripe.demo_mode', false);
    }

    public function isConfigured(): bool
    {
        return filled(config('services.stripe.secret'));
    }

    public function getDefaultPriceId(): ?string
    {
        return config('services.stripe.default_price_id');
    }

    public function createCheckoutSession(Tenant $tenant, string $successUrl, string $cancelUrl, ?string $priceId = null): array
    {
        if (!$this->isConfigured()) {
            if ($this->isDemoMode() || app()->environment(['local', 'development'])) {
                return $this->createDemoCheckoutSession($tenant, $successUrl);
            }
            throw new RuntimeException('Stripe no está configurado en el backend.');
        }

        $resolvedPriceId = $priceId ?: $this->getDefaultPriceId();
        if (!$resolvedPriceId) {
            throw new RuntimeException('No hay price_id configurado para Stripe.');
        }

        $customerId = $this->ensureCustomer($tenant);

        $response = $this->stripePost('/checkout/sessions', [
            'mode' => 'subscription',
            'customer' => $customerId,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'allow_promotion_codes' => 'true',
            'line_items[0][price]' => $resolvedPriceId,
            'line_items[0][quantity]' => 1,
            'metadata[tenant_id]' => $tenant->id,
        ]);

        return [
            'id' => Arr::get($response, 'id'),
            'url' => Arr::get($response, 'url'),
        ];
    }

    public function createPortalSession(Tenant $tenant, string $returnUrl): array
    {
        if (!$this->isConfigured()) {
            if ($this->isDemoMode() || app()->environment(['local', 'development'])) {
                return $this->createDemoPortalSession($tenant, $returnUrl);
            }
            throw new RuntimeException('Stripe no está configurado en el backend.');
        }

        $customerId = $this->ensureCustomer($tenant);

        $response = $this->stripePost('/billing_portal/sessions', [
            'customer' => $customerId,
            'return_url' => $returnUrl,
        ]);

        return [
            'id' => Arr::get($response, 'id'),
            'url' => Arr::get($response, 'url'),
        ];
    }

    public function verifyWebhookSignature(string $payload, ?string $signatureHeader): bool
    {
        $secret = config('services.stripe.webhook_secret');
        if (!$secret || !$signatureHeader) {
            return false;
        }

        $parts = collect(explode(',', $signatureHeader))
            ->mapWithKeys(function (string $part) {
                $segment = explode('=', trim($part), 2);
                if (count($segment) !== 2) {
                    return [];
                }
                return [$segment[0] => $segment[1]];
            });

        $timestamp = (int) $parts->get('t', 0);
        $signature = (string) $parts->get('v1', '');

        if ($timestamp <= 0 || $signature === '') {
            return false;
        }

        if (abs(time() - $timestamp) > 300) {
            return false;
        }

        $signedPayload = $timestamp . '.' . $payload;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    public function processWebhookEvent(array $event): void
    {
        $eventId = (string) Arr::get($event, 'id');
        $eventType = (string) Arr::get($event, 'type');
        $object = Arr::get($event, 'data.object', []);

        if ($eventId === '' || $eventType === '' || !is_array($object)) {
            throw new RuntimeException('Evento Stripe inválido.');
        }

        $existing = DB::table('billing_events')
            ->where('provider_event_id', $eventId)
            ->exists();
        if ($existing) {
            return;
        }

        $tenant = $this->resolveTenantFromStripeObject($object);

        DB::transaction(function () use ($event, $eventId, $eventType, $object, $tenant): void {
            DB::table('billing_events')->insert([
                'provider' => 'stripe',
                'provider_event_id' => $eventId,
                'type' => $eventType,
                'tenant_id' => $tenant?->id,
                'processed_at' => now(),
                'payload' => json_encode($event),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (str_starts_with($eventType, 'customer.subscription.')) {
                $this->syncSubscription($object, $tenant);
            }

            if (str_starts_with($eventType, 'invoice.')) {
                $this->syncInvoice($object, $tenant);
            }
        });
    }

    public function updateDemoPaymentProfile(Tenant $tenant, array $profile): void
    {
        $canUseDemoProfileUpdate = $this->isDemoMode()
            || app()->environment(['local', 'development'])
            || !$this->isConfigured();

        if (!$canUseDemoProfileUpdate) {
            throw new RuntimeException('La actualización manual solo está disponible en modo demo.');
        }

        $now = now();
        $monthlyAmountCents = (int) ($tenant->billing_monthly_amount_cents ?: 4900);

        $currentProfile = $tenant->getAttribute('billing_demo_profile');
        $existingProfile = is_array($currentProfile)
            ? $currentProfile
            : [];
        $nextProfile = array_merge($existingProfile, $profile, [
            'updated_at' => $now->toISOString(),
        ]);
        $manualUpdateCount = (int) ($tenant->getAttribute('billing_demo_manual_update_count') ?? 0) + 1;

        $tenant->forceFill([
            'billing_provider' => 'demo',
            'billing_status' => 'active',
            'billing_customer_id' => $tenant->billing_customer_id ?: ('demo_cus_' . $tenant->id),
            'billing_subscription_id' => $tenant->billing_subscription_id ?: ('demo_sub_' . $tenant->id),
            'billing_price_id' => $tenant->billing_price_id ?: 'demo_price_monthly',
            'billing_currency' => $tenant->billing_currency ?: 'EUR',
            'billing_monthly_amount_cents' => $monthlyAmountCents,
            'billing_current_period_end' => $tenant->billing_current_period_end ?: $now->copy()->addMonth(),
            'billing_last_invoice_at' => $now,
            'billing_last_invoice_status' => 'paid',
        ]);
        $tenant->setAttribute('billing_demo_profile', $nextProfile);
        $tenant->setAttribute('billing_demo_manual_update_count', $manualUpdateCount);
        $tenant->save();
    }

    private function ensureCustomer(Tenant $tenant): string
    {
        if (!empty($tenant->billing_customer_id)) {
            return (string) $tenant->billing_customer_id;
        }

        $response = $this->stripePost('/customers', [
            'name' => 'Tenant ' . $tenant->id,
            'metadata[tenant_id]' => $tenant->id,
        ]);

        $customerId = (string) Arr::get($response, 'id');
        if ($customerId === '') {
            throw new RuntimeException('No se pudo crear cliente en Stripe.');
        }

        $tenant->forceFill([
            'billing_provider' => 'stripe',
            'billing_customer_id' => $customerId,
            'billing_currency' => 'EUR',
            'billing_status' => $tenant->billing_status ?: 'inactive',
        ])->save();

        return $customerId;
    }

    private function syncSubscription(array $subscription, ?Tenant $tenant): void
    {
        if (!$tenant) {
            return;
        }

        $priceId = Arr::get($subscription, 'items.data.0.price.id');
        $amount = Arr::get($subscription, 'items.data.0.price.unit_amount');
        $currency = strtoupper((string) Arr::get($subscription, 'currency', 'EUR'));
        $status = (string) Arr::get($subscription, 'status', 'inactive');

        $currentStart = $this->toDateTime(Arr::get($subscription, 'current_period_start'));
        $currentEnd = $this->toDateTime(Arr::get($subscription, 'current_period_end'));
        $canceledAt = $this->toDateTime(Arr::get($subscription, 'canceled_at'));

        DB::table('billing_subscriptions')->updateOrInsert(
            ['provider_subscription_id' => (string) Arr::get($subscription, 'id')],
            [
                'tenant_id' => $tenant->id,
                'provider' => 'stripe',
                'provider_customer_id' => (string) Arr::get($subscription, 'customer'),
                'status' => $status,
                'price_id' => $priceId,
                'currency' => $currency,
                'amount_cents' => is_numeric($amount) ? (int) $amount : null,
                'current_period_start' => $currentStart,
                'current_period_end' => $currentEnd,
                'canceled_at' => $canceledAt,
                'raw_payload' => json_encode($subscription),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $tenant->forceFill([
            'billing_provider' => 'stripe',
            'billing_status' => $status,
            'billing_customer_id' => (string) Arr::get($subscription, 'customer'),
            'billing_subscription_id' => (string) Arr::get($subscription, 'id'),
            'billing_price_id' => $priceId,
            'billing_currency' => $currency,
            'billing_monthly_amount_cents' => is_numeric($amount) ? (int) $amount : null,
            'billing_current_period_end' => $currentEnd,
        ])->save();
    }

    private function syncInvoice(array $invoice, ?Tenant $tenant): void
    {
        if (!$tenant) {
            return;
        }

        $status = (string) Arr::get($invoice, 'status', 'open');
        $currency = strtoupper((string) Arr::get($invoice, 'currency', 'EUR'));
        $invoiceCreated = $this->toDateTime(Arr::get($invoice, 'created'));
        $dueDate = $this->toDateTime(Arr::get($invoice, 'due_date'));
        $paidAt = $this->toDateTime(Arr::get($invoice, 'status_transitions.paid_at'));

        DB::table('billing_invoices')->updateOrInsert(
            ['provider_invoice_id' => (string) Arr::get($invoice, 'id')],
            [
                'tenant_id' => $tenant->id,
                'provider' => 'stripe',
                'provider_customer_id' => (string) Arr::get($invoice, 'customer'),
                'provider_subscription_id' => (string) Arr::get($invoice, 'subscription'),
                'status' => $status,
                'currency' => $currency,
                'amount_due_cents' => (int) Arr::get($invoice, 'amount_due', 0),
                'amount_paid_cents' => (int) Arr::get($invoice, 'amount_paid', 0),
                'invoice_created_at' => $invoiceCreated,
                'due_date' => $dueDate,
                'paid_at' => $paidAt,
                'hosted_invoice_url' => Arr::get($invoice, 'hosted_invoice_url'),
                'invoice_pdf' => Arr::get($invoice, 'invoice_pdf'),
                'raw_payload' => json_encode($invoice),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $nextStatus = $tenant->billing_status;
        if ($status === 'paid') {
            $nextStatus = 'active';
        } elseif (in_array($status, ['open', 'uncollectible', 'void'], true)) {
            $nextStatus = 'past_due';
        }

        $tenant->forceFill([
            'billing_status' => $nextStatus,
            'billing_last_invoice_at' => $invoiceCreated ?: now(),
            'billing_last_invoice_status' => $status,
        ])->save();
    }

    private function resolveTenantFromStripeObject(array $object): ?Tenant
    {
        $tenantIdFromMetadata = Arr::get($object, 'metadata.tenant_id')
            ?? Arr::get($object, 'lines.data.0.metadata.tenant_id')
            ?? Arr::get($object, 'subscription_details.metadata.tenant_id');

        if ($tenantIdFromMetadata) {
            $tenant = Tenant::find((string) $tenantIdFromMetadata);
            if ($tenant) {
                return $tenant;
            }
        }

        $customerId = Arr::get($object, 'customer');
        if (!$customerId) {
            return null;
        }

        return Tenant::where('billing_customer_id', (string) $customerId)->first();
    }

    private function stripePost(string $path, array $payload): array
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Stripe no está configurado en el backend.');
        }

        $url = rtrim((string) config('services.stripe.base_url', 'https://api.stripe.com/v1'), '/') . $path;
        $response = Http::withToken((string) config('services.stripe.secret'))
            ->asForm()
            ->acceptJson()
            ->post($url, $payload);

        if ($response->failed()) {
            $message = Arr::get($response->json(), 'error.message', 'Error de Stripe');
            throw new RuntimeException($message);
        }

        return $response->json();
    }

    private function createDemoCheckoutSession(Tenant $tenant, string $successUrl): array
    {
        $now = now();
        $monthlyAmountCents = 4900;
        $subscriptionId = 'demo_sub_' . $tenant->id;
        $customerId = 'demo_cus_' . $tenant->id;
        $priceId = 'demo_price_monthly';

        DB::transaction(function () use ($tenant, $subscriptionId, $customerId, $priceId, $monthlyAmountCents, $now): void {
            DB::table('billing_subscriptions')->updateOrInsert(
                ['provider_subscription_id' => $subscriptionId],
                [
                    'tenant_id' => $tenant->id,
                    'provider' => 'demo',
                    'provider_customer_id' => $customerId,
                    'status' => 'active',
                    'price_id' => $priceId,
                    'currency' => 'EUR',
                    'amount_cents' => $monthlyAmountCents,
                    'current_period_start' => $now,
                    'current_period_end' => $now->copy()->addMonth(),
                    'canceled_at' => null,
                    'raw_payload' => json_encode(['mode' => 'demo_checkout']),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            DB::table('billing_invoices')->updateOrInsert(
                ['provider_invoice_id' => 'demo_inv_' . $tenant->id . '_' . $now->timestamp],
                [
                    'tenant_id' => $tenant->id,
                    'provider' => 'demo',
                    'provider_customer_id' => $customerId,
                    'provider_subscription_id' => $subscriptionId,
                    'status' => 'paid',
                    'currency' => 'EUR',
                    'amount_due_cents' => $monthlyAmountCents,
                    'amount_paid_cents' => $monthlyAmountCents,
                    'invoice_created_at' => $now,
                    'due_date' => $now,
                    'paid_at' => $now,
                    'hosted_invoice_url' => null,
                    'invoice_pdf' => null,
                    'raw_payload' => json_encode(['mode' => 'demo_checkout']),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $tenant->forceFill([
                'billing_provider' => 'demo',
                'billing_status' => 'active',
                'billing_customer_id' => $customerId,
                'billing_subscription_id' => $subscriptionId,
                'billing_price_id' => $priceId,
                'billing_currency' => 'EUR',
                'billing_monthly_amount_cents' => $monthlyAmountCents,
                'billing_current_period_end' => $now->copy()->addMonth(),
                'billing_last_invoice_at' => $now,
                'billing_last_invoice_status' => 'paid',
            ])->save();
        });

        return [
            'id' => 'cs_demo_' . $tenant->id . '_' . $now->timestamp,
            'url' => $this->appendQueryParam($successUrl, 'billing_demo', 'checkout_success'),
        ];
    }

    private function createDemoPortalSession(Tenant $tenant, string $returnUrl): array
    {
        $now = now();
        $tenant->forceFill([
            'billing_provider' => $tenant->billing_provider ?: 'demo',
            'billing_status' => 'active',
            'billing_last_invoice_at' => $now,
            'billing_last_invoice_status' => 'paid',
            'billing_current_period_end' => $tenant->billing_current_period_end ?: $now->copy()->addMonth(),
            'billing_currency' => $tenant->billing_currency ?: 'EUR',
            'billing_monthly_amount_cents' => $tenant->billing_monthly_amount_cents ?: 4900,
        ])->save();

        return [
            'id' => 'bps_demo_' . $tenant->id . '_' . $now->timestamp,
            'url' => $this->appendQueryParam($returnUrl, 'billing_demo', 'payment_info_updated'),
        ];
    }

    private function appendQueryParam(string $url, string $key, string $value): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . rawurlencode($key) . '=' . rawurlencode($value);
    }

    private function toDateTime(int|string|null $timestamp): ?Carbon
    {
        if (is_null($timestamp) || $timestamp === '') {
            return null;
        }
        return Carbon::createFromTimestamp((int) $timestamp);
    }
}
