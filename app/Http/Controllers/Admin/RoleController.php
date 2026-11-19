<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('sort_order')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show form to create new role
     */
    public function create()
    {
        $permissions = $this->getAllPermissions();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
            'is_active' => $request->boolean('is_active'),
            'icon' => 'ti-user',
            'color' => $request->color ?? 'primary',
            'sort_order' => Role::max('sort_order') + 1,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role ' . $role->display_name . ' created successfully!');
    }

    /**
     * Show role details
     */
    public function show(Role $role)
    {
        $users = $role->users()->paginate(20);
        $permissions = $this->getAllPermissions();
        return view('admin.roles.show', compact('role', 'users', 'permissions'));
    }

    /**
     * Show form to edit role
     */
    public function edit(Role $role)
    {
        if ($role->is_system && $role->name === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot edit system admin role!');
        }

        $permissions = $this->getAllPermissions();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role)
    {
        if ($role->is_system && $role->name === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot edit system admin role!');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id . '|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
            'is_active' => $request->boolean('is_active'),
            'icon' => $role->icon ?? 'ti-user',
            'color' => $request->color ?? $role->color,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role ' . $role->display_name . ' updated successfully!');
    }

    /**
     * Delete role
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete system role!');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role ' . $role->display_name . ' because ' . $role->users()->count() . ' user(s) have this role assigned.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role ' . $role->display_name . ' deleted successfully!');
    }

    /**
     * Show form to assign users to role
     */
    public function assignUsers(Role $role)
    {
        $usersWithRole = $role->users;
        $usersWithoutRole = User::whereDoesntHave('role', function($query) use ($role) {
            $query->where('id', $role->id);
        })->get();

        return view('admin.roles.assign-users', compact('role', 'usersWithRole', 'usersWithoutRole'));
    }

    /**
     * Update user role assignments
     */
    public function updateUsers(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
            'remove_user_ids' => 'array',
            'remove_user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->route('roles.assign-users', $role)
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $role) {
            // Assign selected users to this role
            if ($request->has('user_ids')) {
                User::whereIn('id', $request->user_ids)->update(['role_id' => $role->id]);
            }

            // Remove role from users
            if ($request->has('remove_user_ids')) {
                $defaultRole = Role::where('name', 'user')->first();
                User::whereIn('id', $request->remove_user_ids)->update(['role_id' => $defaultRole?->id]);
            }
        });

        return redirect()->route('roles.assign-users', $role)
            ->with('success', 'Users assigned to role ' . $role->display_name . ' successfully!');
    }

    /**
     * Toggle role status
     */
    public function toggleStatus(Role $role)
    {
        if ($role->is_system && $role->name === 'admin') {
            return response()->json(['error' => 'Cannot deactivate admin role'], 403);
        }

        $role->update(['is_active' => !$role->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'Role status updated successfully!',
            'is_active' => $role->is_active
        ]);
    }

    /**
     * Get role permissions (AJAX)
     */
    public function getPermissions(Role $role)
    {
        try {
            // Ensure permissions is always an array
            $permissions = $role->permissions;
            
            // If permissions is a string (JSON), decode it
            if (is_string($permissions)) {
                $permissions = json_decode($permissions, true) ?? [];
            }
            
            // If it's null or not an array, set to empty array
            if (!is_array($permissions)) {
                $permissions = [];
            }
            
            return response()->json([
                'success' => true,
                'permissions' => $permissions,
                'is_active' => (bool) $role->is_active,
                'display_name' => $role->display_name ?? $role->name ?? 'Role',
                'color' => $role->color ?? 'secondary',
                'icon' => $role->icon ?? 'ti-user',
                'name' => $role->name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk assign users to role (AJAX)
     */
    public function bulkAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        User::whereIn('id', $request->user_ids)->update(['role_id' => $request->role_id]);

        return response()->json([
            'success' => true,
            'message' => 'Users assigned successfully'
        ]);
    }

    /**
     * Get all available permissions
     */
    private function getAllPermissions()
    {
        return [
            'dashboard' => 'Dashboard Access',
            'orders' => 'Order Management',
            'transactions' => 'Transaction Management',
            'refunds' => 'Refund Management',
            'products' => 'Product Management',
            'categories' => 'Category Management',
            'subcategories' => 'Subcategory Management',
            'reviews' => 'Review Management',
            'cms' => 'CMS Management',
            'sliders' => 'Slider Management',
            'blogs' => 'Blog Management',
            'banners' => 'Banner Management',
            'coupons' => 'Coupon Management',
            'customers' => 'Customer Management',
            'users' => 'User Management',
            'roles' => 'Role Management',
            'reports' => 'Report Management',
            'settings' => 'Settings Management',
            'profile' => 'Profile Management',
            'all' => 'All Permissions (Full Access)',
        ];
    }
}