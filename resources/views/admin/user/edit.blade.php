@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="mb-4">
        <h4 class="mb-1">Edit User: {{ $user->name }}</h4>
        <p class="text-muted mb-0">Update user details and role</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('user.update', $user) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                           value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Leave blank to keep current password">
                    <small class="text-muted">Leave blank to keep current password</small>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" 
                           placeholder="Confirm new password">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Select the role for this user</small>
                    @error('role_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-header">
                            <h5 class="mb-0">Current Role Permissions</h5>
                        </div>
                        <div class="card-body" id="permissions-preview">
                            @php
                                // Handle both relationship and string role
                                $roleData = $user->role;
                                $roleName = 'No Role';
                                $roleDisplayName = 'No Role';
                                $roleColor = 'secondary';
                                $rolePermissions = [];
                                
                                if (is_object($roleData) && isset($roleData->display_name)) {
                                    // Relationship object
                                    $roleName = $roleData->name ?? 'No Role';
                                    $roleDisplayName = $roleData->display_name;
                                    $roleColor = $roleData->color ?? 'secondary';
                                    $rolePermissions = $roleData->permissions ?? [];
                                } elseif (is_string($roleData)) {
                                    // String from old column
                                    $roleName = $roleData;
                                    $roleDisplayName = ucwords(str_replace('_', ' ', $roleData));
                                    $colors = [
                                        'admin' => 'danger',
                                        'web_designer' => 'info',
                                        'order_manager' => 'warning',
                                        'manager' => 'success',
                                        'user' => 'secondary',
                                    ];
                                    $roleColor = $colors[$roleData] ?? 'secondary';
                                } elseif ($user->role_id) {
                                    // Try to load from relationship
                                    $role = $user->role()->first();
                                    if ($role) {
                                        $roleName = $role->name;
                                        $roleDisplayName = $role->display_name;
                                        $roleColor = $role->color ?? 'secondary';
                                        $rolePermissions = $role->permissions ?? [];
                                    }
                                }
                            @endphp

                            @if($roleName != 'No Role')
                                <div class="mb-2">
                                    <strong>Role: {{ $roleDisplayName }}</strong>
                                    <span class="badge bg-{{ $roleColor }} ms-2">
                                        {{ $roleName }}
                                    </span>
                                </div>
                                @if(count($rolePermissions) > 0)
                                    <div>
                                        @foreach($rolePermissions as $permission)
                                            <span class="badge bg-success me-1 mb-1 px-3 py-2">{{ $permission }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">This role has no specific permissions</p>
                                @endif
                            @else
                                <p class="text-muted">No role assigned or no permissions</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Update User
                    </button>
                    <a href="{{ route('user.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role_id');
    const permissionsPreview = document.getElementById('permissions-preview');

    // Load role permissions via AJAX
    async function loadRolePermissions(roleId) {
        if (!roleId) {
            permissionsPreview.innerHTML = '<p class="text-muted">Select a role to see its permissions</p>';
            return;
        }

        try {
            permissionsPreview.innerHTML = '<div class="text-center"><i class="ti ti-loader ti-spin"></i> Loading permissions...</div>';
            
            // Build the URL correctly
            const baseUrl = window.location.origin + '/agriwow/public';
            const url = baseUrl + '/admin/roles/' + roleId + '/permissions';
            
            console.log('Fetching from URL:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Permissions data:', data);

            if (data.success === false) {
                throw new Error(data.message || 'Failed to load permissions');
            }

            if (data.permissions && data.permissions.length > 0) {
                const permissionsList = data.permissions.map(perm => 
                    `<span class="badge bg-success me-1 mb-1 px-3 py-2">${perm}</span>`
                ).join('');
                permissionsPreview.innerHTML = `
                    <div class="mb-2">
                        <strong>Permissions (${data.permissions.length}):</strong>
                        <span class="badge bg-${data.color || 'secondary'} ms-2">${data.display_name}</span>
                    </div>
                    <div>${permissionsList}</div>
                    <div class="mt-2">
                        <small class="text-muted">Status: ${data.is_active ? '✅ Active' : '❌ Inactive'}</small>
                    </div>
                `;
            } else {
                permissionsPreview.innerHTML = `
                    <p class="text-muted">This role has no specific permissions</p>
                `;
            }
        } catch (error) {
            console.error('Error loading permissions:', error);
            permissionsPreview.innerHTML = `
                <div class="alert alert-warning mb-0">
                    <i class="ti ti-alert-triangle me-1"></i>
                    Could not load permissions. Please try again.
                    <br>
                    <small class="text-muted">Error: ${error.message}</small>
                </div>
            `;
        }
    }

    // Load permissions on page load if role is selected (for edit page)
    if (roleSelect.value) {
        loadRolePermissions(roleSelect.value);
    }

    // Load permissions on role change
    roleSelect.addEventListener('change', function() {
        loadRolePermissions(this.value);
    });
});
</script>
@endpush
@endsection