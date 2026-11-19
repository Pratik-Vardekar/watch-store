@extends('layouts.admin')

@section('title', 'Role Details')

@section('content')
<div class="page-title-head d-flex align-items-center mb-3">
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-light border">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div>
                <h4 class="page-main-title m-0">Role Details: {{ $role->display_name }}</h4>
                <p class="text-muted mb-0">View role information and assigned users</p>
            </div>
        </div>
    </div>
    <div class="text-end d-flex gap-2 flex-wrap">
        <a href="{{ route('roles.assign-users', $role) }}" class="btn btn-warning btn-sm">
            <i class="ti ti-users me-1"></i> Manage Users
        </a>
        <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary btn-sm">
            <i class="ti ti-edit me-1"></i> Edit
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="text-muted fs-sm mb-1">Role Name</div>
                        <span class="badge bg-{{ $role->color ?? 'secondary' }} text-capitalize">
                            {{ $role->name }}
                        </span>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted fs-sm mb-1">Display Name</div>
                        <div class="fw-semibold">{{ $role->display_name }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted fs-sm mb-1">Status</div>
                        @if($role->is_active)
                            <span class="badge badge-soft-success">Active</span>
                        @else
                            <span class="badge badge-soft-danger">Inactive</span>
                        @endif
                    </div>
                </div>

                @if($role->description)
                <div class="alert alert-info mb-3">
                    <strong>Description:</strong> {{ $role->description }}
                </div>
                @endif

                <div class="card border">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Permissions ({{ count($role->permissions ?? []) }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @php
                                $allPermissions = [
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
                            @endphp
                            @foreach($allPermissions as $key => $label)
                            <div class="col-md-3 col-sm-6">
                                @if(in_array($key, $role->permissions ?? []))
                                    <span class="badge badge-soft-success">
                                        <i class="ti ti-check me-1"></i>{{ $label }}
                                    </span>
                                @else
                                    <span class="badge badge-soft-secondary">
                                        <i class="ti ti-x me-1"></i>{{ $label }}
                                    </span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-h-100">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <h5 class="card-title mb-0">Users with this Role</h5>
                    <span class="badge badge-soft-primary" id="roleUsersCount">{{ $users->total() }}</span>
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                    <input type="text" class="form-control" id="searchRoleUsers" placeholder="Search by name or email...">
                </div>
            </div>
            <div class="card-body" data-simplebar style="max-height: 420px">
                <div id="roleUserList">
                @forelse($users as $user)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2 user-search-item"
                     data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . ($user->phone ?? '')) }}">
                    <div>
                        <div class="fw-semibold">{{ $user->name }}</div>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                    <a href="{{ route('user.edit', $user) }}" class="btn btn-light btn-sm">
                        <i class="ti ti-edit"></i>
                    </a>
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0">No users assigned to this role</p>
                @endforelse
                </div>
                <p class="text-muted text-center py-3 mb-0 d-none" id="roleUsersNoResults">No users match your search</p>
            </div>
            @if($users->hasPages())
            <div class="card-footer border-0">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('searchRoleUsers');
    const list = document.getElementById('roleUserList');
    const noResults = document.getElementById('roleUsersNoResults');
    const countBadge = document.getElementById('roleUsersCount');

    if (!input || !list) return;

    input.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        const items = list.querySelectorAll('.user-search-item');
        let visible = 0;

        items.forEach(function(item) {
            const text = item.getAttribute('data-search') || '';
            const match = query === '' || text.includes(query);
            item.classList.toggle('d-none', !match);
            if (match) visible++;
        });

        if (noResults) {
            noResults.classList.toggle('d-none', visible > 0 || items.length === 0);
        }

        if (countBadge) {
            countBadge.textContent = query === ''
                ? {{ $users->total() }}
                : visible + ' shown';
        }
    });
});
</script>
@endpush
@endsection
