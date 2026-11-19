<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'phone' => 'nullable|string|max:20',
            'password' => [
                'required',
                'confirmed',
                'min:8', // Add minimum 8 characters validation
                Rules\Password::defaults(),
            ],
        ], [
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        // Soft-deleted rows still hold the unique email index — remove them so signup can proceed
        User::onlyTrashed()
            ->where('email', $request->email)
            ->forceDelete();

        $phone = $request->phone;
        if ($phone) {
            $digits = preg_replace('/\D+/', '', $phone);
            if (str_starts_with($digits, '91') && strlen($digits) > 10) {
                $digits = '91' . ltrim(substr($digits, 2), '0');
            } else {
                $digits = ltrim($digits, '0');
            }
            $phone = str_starts_with($digits, '91') ? '+' . $digits : '+91' . $digits;
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $phone ?: null,
            'password' => Hash::make($request->password),
            'status'   => 1,
            'role_id'  => 5, // Default User Role
        ]);

        event(new Registered($user));

        Auth::login($user);

        app(CartService::class)->mergeGuestCartToUser($user->id);
        app(WishlistService::class)->mergeGuestWishlistToUser($user->id);

        return redirect()->intended('/')
            ->with('success', 'Registration successful. Welcome!');
    }
}