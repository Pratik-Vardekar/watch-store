<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized Access');
        }

        $user = auth()->user();
        
        // Check if role relationship exists and has data
        if (method_exists($user, 'role')) {
            $role = $user->role;
            
            // If role is a relationship object
            if ($role && !is_string($role) && isset($role->name)) {
                if ($role->name === 'admin') {
                    return $next($request);
                }
            }
            
            // If role is a string (old column)
            if (is_string($role) && $role === 'admin') {
                return $next($request);
            }
        }
        
        // Fallback to old role column
        if (isset($user->role) && is_string($user->role) && $user->role === 'admin') {
            return $next($request);
        }

        abort(403, 'Unauthorized Access');
    }
}