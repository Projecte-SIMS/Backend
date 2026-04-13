<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\StripeBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PublicTenantOnboardingController extends Controller
{
    public function __construct(private readonly StripeBillingService $billingService)
    {
    }

    public function demoComplete(Request $request, TenantController $tenantController)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:120',
            'company_slug' => 'nullable|string|max:60|regex:/^[a-z0-9-]+$/',
            'admin_name' => 'required|string|max:120',
            'admin_email' => 'required|email|max:180',
            'admin_password' => 'required|string|min:8|max:120',
            'plan' => 'required|in:base,pro',
            'payment_demo_confirmed' => 'accepted',
            'billing_name' => 'nullable|string|max:120',
            'billing_email' => 'nullable|email|max:180',
            'payment_method' => 'nullable|in:card,sepa,transfer,wallet',
            'country' => 'nullable|string|size:2',
        ]);

        $tenantId = $this->reserveTenantId($validated['company_slug'] ?? $validated['company_name']);
        $domain = "{$tenantId}.tenant.local";

        $createTenantRequest = Request::create('/api/tenants', 'POST', [
            'id' => $tenantId,
            'domain' => $domain,
        ]);

        $createResponse = $tenantController->store($createTenantRequest);
        if ($createResponse->getStatusCode() >= 400) {
            return $createResponse;
        }

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear el tenant.',
            ], 500);
        }

        $monthlyAmountCents = $validated['plan'] === 'pro' ? 7900 : 4900;
        $tenant->billing_monthly_amount_cents = $monthlyAmountCents;
        $tenant->setAttribute('company_name', $validated['company_name']);
        $tenant->setAttribute('company_plan', $validated['plan']);
        $tenant->setAttribute('company_onboarding_source', 'self_service_demo');
        $tenant->save();

        $tenant->run(function () use ($validated) {
            $admin = User::role('Admin')->first() ?? User::where('email', 'admin@sims.com')->first();
            if (!$admin) {
                return;
            }

            $usernameBase = Str::slug(Str::before($validated['admin_email'], '@'), '_');
            $username = $usernameBase ?: 'admin';
            $counter = 1;

            while (
                User::where('username', $username)->where('id', '!=', $admin->id)->exists()
            ) {
                $username = "{$usernameBase}_{$counter}";
                $counter++;
            }

            $admin->name = $validated['admin_name'];
            $admin->email = strtolower((string) $validated['admin_email']);
            $admin->username = $username;
            $admin->password = Hash::make($validated['admin_password']);
            $admin->active = true;
            $admin->save();
        });

        $this->billingService->updateDemoPaymentProfile($tenant, [
            'billing_name' => $validated['billing_name'] ?? $validated['company_name'],
            'billing_email' => $validated['billing_email'] ?? $validated['admin_email'],
            'payment_method' => $validated['payment_method'] ?? 'card',
            'country' => strtoupper((string) ($validated['country'] ?? 'ES')),
            'card_last4' => ($validated['payment_method'] ?? 'card') === 'card' ? '4242' : null,
            'expiry_month' => ($validated['payment_method'] ?? 'card') === 'card' ? 12 : null,
            'expiry_year' => ($validated['payment_method'] ?? 'card') === 'card' ? ((int) now()->format('Y') + 3) : null,
            'city' => 'Madrid',
            'address_line' => 'Calle Demo 123',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Empresa creada y pago demo confirmado.',
            'data' => [
                'tenant_id' => $tenantId,
                'tenant_domain' => $domain,
                'login' => [
                    'tenant' => $tenantId,
                    'email' => strtolower((string) $validated['admin_email']),
                ],
            ],
        ]);
    }

    private function reserveTenantId(string $input): string
    {
        $base = Str::slug(Str::lower(trim($input)));
        if (!$base) {
            $base = 'tenant';
        }

        $base = Str::substr($base, 0, 40);
        $candidate = $base;
        $counter = 1;

        while (Tenant::where('id', $candidate)->exists()) {
            $suffix = '-' . $counter;
            $candidate = Str::substr($base, 0, 40 - strlen($suffix)) . $suffix;
            $counter++;
        }

        return $candidate;
    }
}

