<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\Billing\StripeBillingService;
use App\Services\Billing\TenantBillingAccessService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RuntimeException;

class BillingController extends Controller
{
    public function __construct(
        private readonly StripeBillingService $billingService,
        private readonly TenantBillingAccessService $billingAccessService
    )
    {
    }

    public function status(string $id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->billingSummary($tenant),
        ]);
    }

    public function checkoutSession(Request $request, string $id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        $validated = $request->validate([
            'success_url' => 'required|url|max:500',
            'cancel_url' => 'required|url|max:500',
            'price_id' => 'nullable|string|max:255',
        ]);

        try {
            $session = $this->billingService->createCheckoutSession(
                $tenant,
                $validated['success_url'],
                $validated['cancel_url'],
                $validated['price_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Checkout session creada',
                'data' => $session,
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function portalSession(Request $request, string $id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        $validated = $request->validate([
            'return_url' => 'required|url|max:500',
        ]);

        try {
            $session = $this->billingService->createPortalSession($tenant, $validated['return_url']);

            return response()->json([
                'success' => true,
                'message' => 'Portal session creada',
                'data' => $session,
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function updateDemoProfile(Request $request, string $id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        $validated = $request->validate([
            'billing_name' => 'required|string|max:120',
            'billing_email' => 'required|email|max:180',
            'payment_method' => 'required|in:card,sepa,transfer,wallet',
            'card_last4' => 'nullable|string|size:4',
            'expiry_month' => 'nullable|integer|min:1|max:12',
            'expiry_year' => 'nullable|integer|min:2024|max:2100',
            'country' => 'required|string|size:2',
            'city' => 'nullable|string|max:120',
            'postal_code' => 'nullable|string|max:20',
            'address_line' => 'nullable|string|max:180',
            'vat_number' => 'nullable|string|max:40',
        ]);

        try {
            $this->billingService->updateDemoPaymentProfile($tenant, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Datos de pago actualizados (demo)',
                'data' => $this->billingSummary($tenant->fresh()),
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (!$this->billingService->verifyWebhookSignature($payload, $signature)) {
            return response()->json([
                'success' => false,
                'message' => 'Firma de webhook inválida',
            ], 400);
        }

        $event = json_decode($payload, true);
        if (!is_array($event)) {
            return response()->json([
                'success' => false,
                'message' => 'Payload de webhook inválido',
            ], 400);
        }

        try {
            $this->billingService->processWebhookEvent($event);

            return response()->json([
                'success' => true,
                'message' => 'Webhook procesado',
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    private function billingSummary(Tenant $tenant): array
    {
        $monthlyCents = (int) ($tenant->billing_monthly_amount_cents ?? 0);
        $profileAttribute = $tenant->getAttribute('billing_demo_profile');
        $demoProfile = is_array($profileAttribute) ? $profileAttribute : null;

        return [
            'provider' => $tenant->billing_provider,
            'status' => $tenant->billing_status ?? 'inactive',
            'customer_id' => $tenant->billing_customer_id,
            'subscription_id' => $tenant->billing_subscription_id,
            'price_id' => $tenant->billing_price_id,
            'currency' => strtoupper((string) ($tenant->billing_currency ?? 'EUR')),
            'monthly_amount_cents' => $monthlyCents,
            'mrr_amount_cents' => $monthlyCents,
            'arr_amount_cents' => $monthlyCents * 12,
            'current_period_end' => $this->toIsoDate($tenant->billing_current_period_end),
            'last_invoice_at' => $this->toIsoDate($tenant->billing_last_invoice_at),
            'last_invoice_status' => $tenant->billing_last_invoice_status,
            'demo_profile' => $demoProfile,
            'access' => $this->billingAccessService->suspensionSnapshot($tenant),
        ];
    }

    private function toIsoDate(mixed $value): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->toISOString();
        }

        return Carbon::parse((string) $value)->toISOString();
    }
}
