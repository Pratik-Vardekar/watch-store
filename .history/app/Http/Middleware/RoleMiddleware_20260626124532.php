<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$params): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized Access');
        }

        $user = auth()->user();
        $userRole = $this->getUserRole($user);

        // Separate roles and permissions
        $roles = [];
        $permissions = [];
        
        foreach ($params as $param) {
            if (str_starts_with($param, 'permission:')) {
                $permissions[] = substr($param, 11);
            } else {
                $roles[] = $param;
            }
        }

        // Check roles if specified
        if (!empty($roles)) {
            $allowed = false;

            foreach ($roles as $role) {
                if ($role === 'staff' && $user->canAccessAdmin()) {
                    $allowed = true;
                    break;
                }

                if ($userRole === $role) {
                    $allowed = true;
                    break;
                }
            }

            if (!$allowed) {
                abort(403, 'Unauthorized Access');
            }
        }

        // Check permissions if specified
        if (!empty($permissions)) {
            $hasPermission = false;
            foreach ($permissions as $permission) {
                if ($this->hasPermission($user, $permission)) {
                    $hasPermission = true;
                    break;
                }
            }
            
            if (!$hasPermission) {
                abort(403, 'You don\'t have permission to access this resource.');
            }
        }

        return $next($request);
    }

    private function getUserRole($user): string
    {
        // Try relationship first
        if ($user->role_id) {
            $role = $user->role()->first();
            if ($role && isset($role->name)) {
                return $role->name;
            }
        }
        
        // Fallback to old column
        if (isset($user->role) && is_string($user->role)) {
            return $user->role;
        }
        
        return 'user';
    }

    private function hasPermission($user, string $permission): bool
    {
        $roleName = $this->getUserRole($user);
        
        // Admin has all permissions
        if ($roleName === 'admin') {
            return true;
        }

        // Get permissions from role
        $permissions = [];
        if ($user->role_id) {
            $role = $user->role()->first();
            if ($role) {
                $perms = $role->permissions;
                if (is_string($perms)) {
                    $permissions = json_decode($perms, true) ?? [];
                } elseif (is_array($perms)) {
                    $permissions = $perms;
                }
            }
        }

        return in_array($permission, $permissions) || in_array('all', $permissions);
    }
}