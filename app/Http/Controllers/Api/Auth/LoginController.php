<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Tenant;
use App\Models\User; // Ensure this points to your Stancl Tenant model
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $this->logInfo('Login attempt received', [
                'email' => $request->input('email'),
                'device_name' => $request->input('device_name'),
            ]);

            // 1. Validate the data (handled by LoginRequest)
            $validated = $request->validated();

            // 2. Search for the user (Assumes Central User)
            $user = User::where('email', $validated['email'])->first();

            // 3. Verify credentials
            if (! $user || ! Hash::check($validated['password'], $user->password)) {
                $this->logWarning('Failed login attempt', [
                    'email' => $validated['email'],
                    'reason' => 'Invalid credentials',
                ]);

                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            // 4. Initialize the user's Tenancy (Stancl)
            if ($user->tenant_id) {
                $tenant = Tenant::find($user->tenant_id);

                if ($tenant) {
                    // This switches the DB connection to the tenant's database
                    tenancy()->initialize($tenant);
                    $this->logDebug('Tenancy initialized', [
                        'tenant_id' => $tenant->id,
                        'tenant_name' => $tenant->name ?? null,
                    ]);
                } else {
                    $this->logWarning('Tenant not found for user', [
                        'user_id' => $user->id,
                        'tenant_id' => $user->tenant_id,
                    ]);
                }
            }

            // 5. Login the user
            // Manually set the user to the auth guard for the current request context
            Auth::login($user);

            // Generate the Sanctum token
            $deviceName = $validated['device_name'] ?? 'api-login';
            $token = $user->createToken($deviceName)->plainTextToken;

            $this->logInfo('Login successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id,
            ]);

            // Return the response with the UserResource
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ]);
        } catch (ValidationException $e) {
            $this->logValidationError($e);
            throw $e;
        } catch (\Exception $e) {
            $this->logException($e, [
                'email' => $request->input('email'),
            ]);
            throw $e;
        }
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $email = $request->user()->email;

            // Revoke the token that was used to authenticate the current request
            $request->user()->currentAccessToken()->delete();

            $this->logInfo('Logout successful', [
                'user_id' => $userId,
                'email' => $email,
            ]);

            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        } catch (\Exception $e) {
            $this->logException($e);
            throw $e;
        }
    }
}
