<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\QrAccessLog;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
    public function profile(Request $request): JsonResponse
    {
        $customer = Auth::user();
        if (!$customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $totalBatches = $customer->batches()->count();
        $activeBatches = $customer->batches()->where('status', 'active')->count();
        $totalScans = QrAccessLog::whereHas('batch', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->count();
        $averageRating = Review::whereHas('batch', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->avg('rating');

        return response()->json([
            'data' => [
                'id' => (string) $customer->id,
                'full_name' => $customer->full_name,
                'email' => $customer->email,
                'phone_number' => $customer->phone_number,
                'address' => $customer->address,
                'role' => $customer->role,
                'profile_image' => $customer->profile_image,
                'stats' => [
                    'total_batches' => $totalBatches,
                    'active_batches' => $activeBatches,
                    'total_scans' => $totalScans,
                    'average_rating' => $averageRating ? round((float) $averageRating, 1) : 0.0,
                ],
            ],
        ]);
    }

    /**
     * Update authenticated customer's profile
     */
    public function updateProfile(UpdateProfileRequest $request): CustomerResource
    {
        $customer = Auth::user();
        if (!$customer) {
            abort(401, 'Unauthenticated');
        }

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
        $user = Auth::user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
