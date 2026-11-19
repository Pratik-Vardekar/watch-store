<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Google\Client as GoogleClient;
use Illuminate\Support\Str;
use App\Services\SmsAlertService;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Send OTP (Moglix Style)
    |--------------------------------------------------------------------------
    */

    public function auth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|digits:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $otp = rand(100000, 999999);

        $user = User::where('phone', $request->phone)->first();

        if ($user) {

            $flow = 'login';

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
                'auth_type' => $flow
            ]);
        } else {

            $flow = 'register';

            $dummyEmail = 'user_' . uniqid() . '_' . time() . '@temp.com';

            $user = User::create([
                'name' => 'User_' . rand(1000, 9999),
                'email' => $dummyEmail,
                'phone' => $request->phone,
                'plain_password' => '123456',
                'password' => Hash::make('123456'),
                'status' => 1,
                'role' => 'user',
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
                'is_phone_verified' => 0,
                'auth_type' => $flow
            ]);
        }

        $message = "Dear User, Your Agriwow Web verification OTP code is {$otp}. Thanks Agriwow";

        SmsAlertService::send($request->phone, $message);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully',
            'flow' => $flow
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Verify OTP
    |--------------------------------------------------------------------------
    */

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|digits_between:1,15',
            'otp' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ], 401);
        }

        if (!$user->otp_expires_at || now()->gt($user->otp_expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired'
            ], 401);
        }

        $flow = $user->auth_type;

        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'is_phone_verified' => 1
        ]);

        $userData = $user->toArray();
        $userData['auth_type'] = $flow;

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => $flow === 'login'
                ? 'Login successful'
                : 'Registration successful',
            'flow' => $flow,
            'user' => $userData,
            'token' => $token
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Resend OTP
    |--------------------------------------------------------------------------
    */

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|digits_between:1,15'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $otp = rand(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5)
        ]);

        $message = "Dear User, Your The Watch Store verification OTP code is {$otp}. Thanks Watch-Store";

        SmsAlertService::send($request->phone, $message);

        return response()->json([
            'status' => true,
            'message' => 'OTP resent successfully'
        ]);
    }

    public function logout(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // logout only current device (BEST PRACTICE)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $client = new GoogleClient([
            'client_id' => env('GOOGLE_CLIENT_ID')
        ]);

        $payload = $client->verifyIdToken($request->id_token);
        \Log::info('Google login payload', (array)$payload);
        if (!$payload) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Google token'
            ], 401);
        }

        $user = User::updateOrCreate(
            [
                'email' => $payload['email'],
            ],

            [
                'name' => $payload['name'] ?? '',
                'google_id' => $payload['sub'],
                'avatar' => $payload['picture'] ?? null,
                'role' => 'user',
                'password' => Hash::make(Str::random(16)),
                'plain_password' => null,
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }
}
