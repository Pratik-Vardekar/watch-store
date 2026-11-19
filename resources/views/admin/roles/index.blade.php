@extends('layouts.admin')

@section('title', 'Role Management')

@section('content')
<div class="page-title-head d-flex align-items-center mb-3">
    <div class="flex-grow-1">
        <h4 class="page-main-title m-0">Role Management</h4>
        <p class="text-muted mb-0">Manage all roles and permissions</p>
    </div>
    <div class="text-end">
        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus me-1"></i> Add Role
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-custom table-nowrap table-centered table-hover w-100 mb-0">
            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                <tr class="text-uppercase fs-xxs">
                    <th>#</th>
                    <th>Role</th>
                    <th>Display Name</th>
                    <th>Description</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th>Permissions</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <span class="badge bg-{{ $role->color ?? 'secondary' }} text-capitalize">
                            <i class="ti {{ $role->icon ?? 'ti-user' }} me-1"></i>
                            {{ $role->name }}
                        </span>
                    </td>
                    <td class="fw-semibold">{{ $role->display_name }}</td>
                    <td class="text-muted">{{ Str::limit($role->description, 50) }}</td>
                    <td>
                        <span class="badge badge-soft-primary">{{ $role->users_count }}</span>
                    </td>
                    <td>
                        @if($role->is_active)
                            <span class="badge badge-soft-success">
                                <i class="ti ti-circle-check me-1"></i> Active
                            </span>
                        @else
                            <span class="badge badge-soft-danger">
                                <i class="ti ti-circle-x me-1"></i> Inactive
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-soft-info">{{ count($role->permissions ?? []) }}</span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('roles.show', $role) }}" class="btn btn-light btn-sm" title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('roles.assign-users', $role) }}" class="btn btn-light btn-sm" title="Assign Users">
                                <i class="ti ti-users"></i>
                            </a>
                            @if(!($role->is_system && $role->name == 'admin'))
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-light btn-sm" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            @endif
                            @if(!$role->is_system && $role->users_count == 0)
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm" title="Delete">
                                    <i class="ti ti-trash text-danger"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="ti ti-shield-off fs-1 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">No roles found</p>
                        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary mt-3">
                            <i class="ti ti-plus me-1"></i> Add your first role
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
