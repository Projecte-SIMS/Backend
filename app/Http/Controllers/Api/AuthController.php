<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Incorrect credentials.'],
                ]);
            }

            if (!$user->active) {
                return response()->json([
                    'message' => 'User inactive.'
                ], 403);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error during login',
                'error' => $e->getMessage(),
                'tenant' => tenancy()->initialized ? tenancy()->tenant->id : 'not initialized',
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'active' => true,
        ]);

        // Asignar rol por defecto (Client o normal)
        $user->assignRole('Client');

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('roles.permissions')
        ]);
    }

    /**
     * Central login for super admin (tenant management)
     * Uses environment variables for credentials
     */
    public function centralLogin(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $centralEmail = trim((string) env('CENTRAL_ADMIN_EMAIL', 'superadmin@sims.com'));
            $centralPassword = trim((string) env('CENTRAL_ADMIN_PASSWORD', 'supersecret'));
            $inputEmail = trim((string) $validated['email']);
            $inputPassword = trim((string) $validated['password']);

            if ($inputEmail !== $centralEmail || $inputPassword !== $centralPassword) {
                throw ValidationException::withMessages([
                    'email' => ['Credenciales incorrectas.'],
                ]);
            }

            // For central auth, we use a simple token approach
            $token = base64_encode($centralEmail . ':' . now()->timestamp . ':' . \Illuminate\Support\Str::random(32));

            // Store token in cache for validation
            cache()->put('central_token:' . $token, true, now()->addHours(24));

            return response()->json([
                'message' => 'Login central exitoso',
                'token' => $token,
                'user' => [
                    'name' => 'Super Admin',
                    'email' => $centralEmail,
                    'role' => 'super_admin',
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
