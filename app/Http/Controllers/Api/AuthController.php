<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new customer
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);

        $customer = Customer::create($data);
        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'customer' => new CustomerResource($customer),
            'token' => $token,
        ], 201);
    }

    /**
     * Login customer
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'customer' => new CustomerResource($customer),
            'token' => $token,
        ]);
    }

    /**
     * Get authenticated customer's profile
     */
    public function profile(Request $request): CustomerResource
    {
        return new CustomerResource($request->user());
    }

    /**
     * Update authenticated customer's profile
     */
    public function updateProfile(UpdateProfileRequest $request): CustomerResource
    {
        $customer = $request->user();

        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
            unset($data['password']);
        }

        $customer->update($data);

        return new CustomerResource($customer);
    }

    /**
     * Logout customer
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
