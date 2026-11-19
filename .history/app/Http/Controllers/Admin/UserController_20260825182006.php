<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('role')->withCount('addresses');

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // Role Filter (using role_id)
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Status Filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Sorting
        if ($request->sort == 'oldest') {
            $query->orderBy('created_at', 'ASC');
        } elseif ($request->sort == 'name_asc') {
            $query->orderBy('name', 'ASC');
        } elseif ($request->sort == 'name_desc') {
            $query->orderBy('name', 'DESC');
        } else {
            $query->orderBy('created_at', 'DESC');
        }

        $users = $query->paginate(10);
        $roles = Role::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.user.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.user.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'plain_password' => $request->password, // Optional: for reference
                'role_id' => $request->role_id,
                'status' => $request->status,
                'email_verified_at' => now(),
                'is_phone_verified' => 1,
            ]);

            return redirect()->route('user.index')
                ->with('success', 'User "' . $user->name . '" created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load('role', 'addresses', 'orders');
        return view('admin.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.user.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role_id' => $request->role_id,
                'status' => $request->status,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
                $data['plain_password'] = $request->password;
            }

            $user->update($data);

            return redirect()->route('user.index')
                ->with('success', 'User "' . $user->name . '" updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('user.index')
                ->with('error', 'You cannot delete your own account!');
        }

        try {
            $userName = $user->name;
            $user->delete();

            return redirect()->route('user.index')
                ->with('success', 'User "' . $userName . '" deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(Request $request, User $user)
    {
        try {
            $user->status = !$user->status;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully!',
                'status' => $user->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display customers (users with orders)
     */
    public function customers(Request $request)
    {
        $query = User::withCount('orders')
            ->whereHas('orders')
            ->with('role');

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // Status Filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(20);

        return view('admin.user.customers', compact('users'));
    }

    /**
     * Get user details for API/AJAX
     */
    public function getUserDetails(User $user)
    {
        $user->load('role', 'addresses', 'orders');
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Bulk assign users to role
     */
    public function bulkAssignRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            User::whereIn('id', $request->user_ids)->update(['role_id' => $request->role_id]);

            $role = Role::find($request->role_id);
            return response()->json([
                'success' => true,
                'message' => count($request->user_ids) . ' users assigned to "' . $role->display_name . '" successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Prevent deleting current user
            $userIds = array_diff($request->user_ids, [auth()->id()]);
            
            if (empty($userIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account!'
                ], 422);
            }

            $count = User::whereIn('id', $userIds)->delete();

            return response()->json([
                'success' => true,
                'message' => $count . ' users deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $query = User::with('role');

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $users = $query->get();

        // Generate CSV
        $filename = 'users_export_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');

        // Add CSV headers
        fputcsv($handle, [
            'ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Created At'
        ]);

        // Add data rows
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->name,
                $user->email,
                $user->phone ?? '',
                $user->role->display_name ?? 'No Role',
                $user->status == 1 ? 'Active' : 'Inactive',
                $user->created_at->format('Y-m-d H:i:s')
            ]);
        }

        fclose($handle);

        return response()->stream(function() use ($handle) {
            // This is handled by the fputcsv above
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get users by role (AJAX)
     */
    public function getUsersByRole(Request $request)
    {
        $roleId = $request->role_id;
        $users = User::where('role_id', $roleId)
            ->where('status', 1)
            ->select('id', 'name', 'email')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function userexport(Request $request)
    {
        $fileName = 'users_' . date('Y-m-d_H-i-s') . '.csv';
    
        $users = \App\Models\User::query()
            ->orderBy('id', 'desc')
            ->get();
    
        return response()->streamDownload(function () use ($users) {
    
            $file = fopen('php://output', 'w');
    
            // UTF-8 support for Excel
            fprintf($file, "\xEF\xBB\xBF");
    
            // Header
            if ($users->isNotEmpty()) {
                fputcsv($file, array_keys($users->first()->getAttributes()));
            }
    
            // All users from all pagination pages
            foreach ($users as $user) {
                fputcsv($file, array_values($user->getAttributes()));
            }
    
            fclose($file);
    
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}