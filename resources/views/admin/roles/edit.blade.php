@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="page-title-head d-flex align-items-center mb-3">
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-light border">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div>
                <h4 class="page-main-title m-0">Edit Role: {{ $role->display_name }}</h4>
                <p class="text-muted mb-0">Update role details and permissions</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('roles.update', $role) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <label class="form-label">Role Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $role->name) }}" required>
                <small class="text-muted">Use lowercase with underscores</small>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Display Name <span class="text-danger">*</span></label>
                <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror"
                       value="{{ old('display_name', $role->display_name) }}" required>
                @error('display_name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="2">{{ old('description', $role->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Color</label>
                <select name="color" class="form-select">
                    <option value="primary" {{ old('color', $role->color) == 'primary' ? 'selected' : '' }}>Primary</option>
                    <option value="secondary" {{ old('color', $role->color) == 'secondary' ? 'selected' : '' }}>Secondary</option>
                    <option value="success" {{ old('color', $role->color) == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="info" {{ old('color', $role->color) == 'info' ? 'selected' : '' }}>Info</option>
                    <option value="warning" {{ old('color', $role->color) == 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="danger" {{ old('color', $role->color) == 'danger' ? 'selected' : '' }}>Danger</option>
                </select>
            </div>

            <div class="col-md-6">
                <div class="form-check mt-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1"
                           {{ old('is_active', $role->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                           {{ ($role->is_system && $role->name == 'admin') ? 'disabled' : '' }}>
                    <label class="form-check-label">Active</label>
                </div>
            </div>

            <div class="col-12">
                <div class="card border">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Permissions</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-info" id="selectAll">Select All</button>
                            <button type="button" class="btn btn-sm btn-secondary" id="deselectAll">Deselect All</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($permissions as $key => $label)
                            <div class="col-md-3 col-sm-6">
                                <div class="form-check">
                                    @php
                                        $selectedPermissions = old('permissions', $role->permissions ?? []);
                                    @endphp
                                    <input type="checkbox" name="permissions[]" class="form-check-input permission-checkbox"
                                           value="{{ $key }}" id="perm_{{ $key }}"
                                           {{ in_array($key, $selectedPermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $key }}">{{ $label }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <button class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i> Update Role
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('selectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
    });
    document.getElementById('deselectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    });
});
</script>
@endpush
