@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
<div class="page-title-head d-flex align-items-center mb-3">
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-light border">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div>
                <h4 class="page-main-title m-0">Create New Role</h4>
                <p class="text-muted mb-0">Add a new role with permissions</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label class="form-label">Role Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g., content_manager" required>
                <small class="text-muted">Use lowercase with underscores</small>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Display Name <span class="text-danger">*</span></label>
                <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror"
                       value="{{ old('display_name') }}" placeholder="e.g., Content Manager" required>
                @error('display_name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="2" placeholder="Describe what this role can do">{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Color</label>
                <select name="color" class="form-select">
                    <option value="primary" {{ old('color', 'primary') == 'primary' ? 'selected' : '' }}>Primary</option>
                    <option value="secondary" {{ old('color') == 'secondary' ? 'selected' : '' }}>Secondary</option>
                    <option value="success" {{ old('color') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="info" {{ old('color') == 'info' ? 'selected' : '' }}>Info</option>
                    <option value="warning" {{ old('color') == 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="danger" {{ old('color') == 'danger' ? 'selected' : '' }}>Danger</option>
                </select>
            </div>

            <div class="col-md-6">
                <div class="form-check mt-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1"
                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
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
                                    <input type="checkbox" name="permissions[]" class="form-check-input permission-checkbox"
                                           value="{{ $key }}" id="perm_{{ $key }}"
                                           {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
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
                    <i class="ti ti-device-floppy me-1"></i> Create Role
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
