<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Billing\StripeBillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWalletController extends Controller
{
    protected StripeBillingService $stripeService;

    public function __construct(StripeBillingService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Get user balance and transactions.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'balance' => $user->wallet_balance,
            'formatted_balance' => $user->formatted_balance,
            'has_payment_method' => !empty($user->stripe_customer_id),
            'transactions' => $user->walletTransactions()->latest()->take(50)->get(),
        ]);
    }

    /**
     * Initiate a Stripe Checkout session for wallet top-up.
     */
    public function topup(Request $request): JsonResponse
    {
        $request->validate([
            'amount_cents' => 'required|integer|min:100', // Mínimo 1€
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
        ]);

        $user = Auth::user();
        $tenant = tenant();

        try {
            $session = $this->stripeService->createUserTopUpSession(
                $tenant,
                $user,
                (int) $request->amount_cents,
                $request->success_url,
                $request->cancel_url
            );

            return response()->json([
                'success' => true,
                'checkout_url' => $session['url'],
                'session_id' => $session['id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la sesión de pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a link to the Stripe Customer Portal for managing payment methods.
     */
    public function portal(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user->stripe_customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Debes realizar al menos una recarga para configurar un método de pago.',
            ], 400);
        }

        try {
            $session = $this->stripeService->createUserPortalSession(
                $user,
                $request->return_url ?: (env('APP_URL') . '/wallet')
            );

            return response()->json([
                'success' => true,
                'url' => $session['url'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al acceder al portal: ' . $e->getMessage(),
            ], 500);
        }
    }
}
