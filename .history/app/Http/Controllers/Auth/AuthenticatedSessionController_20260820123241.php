<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show login page
     */
    public function create(Request $request)
    {
        if (! $request->session()->has('url.intended')) {
            $referer = $request->headers->get('referer');
            if ($referer && str_starts_with($referer, $request->root()) && ! str_contains($referer, '/login')) {
                $request->session()->put('url.intended', $referer);
            }
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'min:8', 'max:8'], // Exactly 8 characters
        ], [
            'password.min' => 'Password must be exactly 8 characters long.',
            'password.max' => 'Password must be exactly 8 characters long.',
        ]);
    
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1,
        ])) {
    
            $request->session()->regenerate();

            app(CartService::class)->mergeGuestCartToUser(Auth::id());
            app(WishlistService::class)->mergeGuestWishlistToUser(Auth::id());

            $user = Auth::user();
    
            // Admin Login
            if ($user->role_id == 1) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Welcome Admin!');
            }
    
            // Frontend User Login
            return redirect()->intended('/')
                ->with('success', 'Login successful. Welcome back!');
        }

        $inactiveUser = \App\Models\User::where('email', $request->email)->first();

        if ($inactiveUser && (int) $inactiveUser->status !== 1) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Your account is inactive. Please contact support.')
                ->withErrors([
                    'email' => 'Your account is inactive. Please contact support.',
                ]);
        }
    
        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid email or password.')
            ->withErrors([
                'email' => 'Invalid email or password.',
                'password' => 'Invalid email or password.',
            ]);
    }

    /**
     * Logout
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}