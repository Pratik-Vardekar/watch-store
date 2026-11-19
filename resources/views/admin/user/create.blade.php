@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="mb-4">
        <h4 class="mb-1">Add New User</h4>
        <p class="text-muted mb-0">Create a new user and assign role</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('user.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" placeholder="Enter full name" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" placeholder="Enter email address" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                           value="{{ old('phone') }}" placeholder="Enter phone number">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Enter password (min 8 characters)" required>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" 
                           placeholder="Confirm password" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-header">
                            <h5 class="mb-0">Role Permissions Preview</h5>
                        </div>
                        <div class="card-body" id="permissions-preview">
                            <p class="text-muted">Select a role to see its permissions</p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Create User
                    </button>
                    <a href="{{ route('user.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role_id');
    const permissionsPreview = document.getElementById('permissions-preview');

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

@endsection