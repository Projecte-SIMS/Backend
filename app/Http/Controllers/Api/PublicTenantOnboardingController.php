<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantOwnerProfile;
use App\Services\Billing\StripeBillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class PublicTenantOnboardingController extends Controller
{
    public function __construct(private readonly StripeBillingService $billingService)
    {
    }

    public function demoComplete(Request $request, TenantController $tenantController): JsonResponse
    {
        try {
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
                'theme' => 'nullable|string|max:30',
                // New entity/personal info fields
                'entity_type' => 'required|in:individual,company',
                'tax_id' => 'required|string|max:20', // NIF/CIF
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
            ]);

            $tenantId = $this->reserveTenantId($validated['company_slug'] ?? $validated['company_name']);
            
            // Generate domain based on central domain from config
            $centralDomain = env('CENTRAL_DOMAIN', $request->getHost());
            $domain = "{$tenantId}.{$centralDomain}";

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
            
            // Technical core attributes
            $tenant->setAttribute('company_plan', $validated['plan']);
            $tenant->setAttribute('company_theme', $validated['theme'] ?? 'indigo');
            $tenant->setAttribute('company_onboarding_source', 'self_service_demo');
            $tenant->save();

            // Create separate Owner Profile
            TenantOwnerProfile::create([
                'tenant_id' => $tenant->id,
                'owner_name' => $validated['admin_name'],
                'owner_email' => $validated['admin_email'],
                'entity_type' => $validated['entity_type'],
                'company_name' => $validated['company_name'],
                'tax_id' => $validated['tax_id'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
            ]);

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

            $selectedMethod = $validated['payment_method'] ?? 'card';
            $this->billingService->updateDemoPaymentProfile($tenant, [
                'billing_name' => $validated['billing_name'] ?? $validated['company_name'],
                'billing_email' => $validated['billing_email'] ?? $validated['admin_email'],
                'payment_method' => $selectedMethod,
                'country' => strtoupper((string) ($validated['country'] ?? 'ES')),
                'card_last4' => $selectedMethod === 'card' ? '4242' : null,
                'expiry_month' => $selectedMethod === 'card' ? 12 : null,
                'expiry_year' => $selectedMethod === 'card' ? ((int) now()->format('Y') + 3) : null,
                'city' => $validated['city'] ?? 'Madrid',
                'address_line' => $validated['address'] ?? 'Calle Demo 123',
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
        } catch (Throwable $e) {
            Log::error('Public tenant onboarding failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo completar el alta de la empresa.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function getPublicSettings(string $id): JsonResponse
    {
        $tenant = Tenant::with('ownerProfile')->find($id);
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tenant->id,
                'name' => $tenant->ownerProfile->company_name ?? $tenant->id,
                'theme' => $tenant->company_theme ?? 'indigo',
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
