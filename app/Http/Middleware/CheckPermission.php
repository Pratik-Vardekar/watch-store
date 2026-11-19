<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $user = auth()->user();
        
        // Get permissions from the role relationship
        $roleName = 'user';
        $permissions = [];
        
        // FIRST: Try to get role from relationship (role_id)
        if ($user->role_id) {
            $role = $user->role()->first();
            if ($role) {
                $roleName = $role->name;
                $permissions = $role->permissions;
                
                // Decode if it's a JSON string
                if (is_string($permissions)) {
                    $permissions = json_decode($permissions, true) ?? [];
                }
                
                // Ensure it's an array
                if (!is_array($permissions)) {
                    $permissions = [];
                }
            }
        }
        
        // If no role_id, fallback to the old role column
        if (empty($roleName) || $roleName === 'user') {
            if (isset($user->role) && is_string($user->role)) {
                $roleName = $user->role;
            }
        }

        // Admin has all permissions
        if ($roleName === 'admin') {
            return $next($request);
        }

        // Check if user has the required permission
        $hasPermission = in_array($permission, $permissions) || in_array('all', $permissions);

        if ($hasPermission) {
            return $next($request);
        }

        abort(403, 'You don\'t have permission to access this resource.');
    }
}