@extends('layouts.admin')

@section('title', 'Assign Users')

@section('content')
@php
    $selectedUserIds = old('user_ids', []);
    $removeUserIds = old('remove_user_ids', []);
@endphp
<div class="page-title-head d-flex align-items-center mb-3">
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-light border">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div>
                <h4 class="page-main-title m-0">Assign Users: {{ $role->display_name }}</h4>
                <p class="text-muted mb-0">Add or remove users from this role</p>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('roles.update-users', $role) }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <h5 class="card-title mb-0">Available Users</h5>
                        <span class="badge badge-soft-info" id="availableCount">{{ count($usersWithoutRole) }} users</span>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" id="searchAvailable" placeholder="Search by name, email or phone...">
                    </div>
                </div>

                @if($usersWithoutRole->count() > 0)
                <div class="px-3 pt-3 pb-2 border-bottom user-list-toolbar" id="availableToolbar">
                    <div class="form-check mb-0">
                        <input type="checkbox" class="form-check-input" id="selectAllAvailable">
                        <label class="form-check-label fw-semibold" for="selectAllAvailable">Select All</label>
                    </div>
                </div>
                @endif

                <div class="overflow-auto p-3" style="max-height: 420px; overflow-y: auto;">
                    <div id="availableUserList">
                        @forelse($usersWithoutRole as $user)
                        <div class="form-check mb-2 user-search-item"
                             data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . ($user->phone ?? '')) }}">
                            <input type="checkbox" name="user_ids[]" class="form-check-input available-user"
                                   value="{{ $user->id }}" id="user_{{ $user->id }}"
                                   {{ in_array($user->id, $selectedUserIds) ? 'checked' : '' }}>
                            <label class="form-check-label" for="user_{{ $user->id }}">
                                <span class="fw-semibold d-block">{{ $user->name }}</span>
                                <small class="text-muted">{{ $user->email }}</small>
                                @if($user->phone)
                                    <small class="text-muted d-block">{{ $user->phone }}</small>
                                @endif
                            </label>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3 mb-0">No users available to assign</p>
                        @endforelse
                    </div>
                    <p class="text-muted text-center py-3 mb-0 d-none" id="availableNoResults">No users match your search</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <h5 class="card-title mb-0">Users with this Role</h5>
                        <span class="badge badge-soft-primary" id="assignedCount">{{ count($usersWithRole) }} users</span>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" id="searchAssigned" placeholder="Search by name, email or phone...">
                    </div>
                </div>

                @if($usersWithRole->count() > 0)
                <div class="px-3 pt-3 pb-2 border-bottom user-list-toolbar" id="assignedToolbar">
                    <div class="form-check mb-0">
                        <input type="checkbox" class="form-check-input" id="selectAllAssigned">
                        <label class="form-check-label fw-semibold" for="selectAllAssigned">Select All</label>
                    </div>
                </div>
                @endif

                <div class="overflow-auto p-3" style="max-height: 420px; overflow-y: auto;">
                    <div id="assignedUserList">
                        @forelse($usersWithRole as $user)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2 user-search-item"
                             data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . ($user->phone ?? '')) }}">
                            <div>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->email }}</small>
                                @if($user->phone)
                                    <small class="text-muted d-block">{{ $user->phone }}</small>
                                @endif
                            </div>
                            <div class="form-check mb-0">
                                <input type="checkbox" name="remove_user_ids[]" class="form-check-input assigned-user"
                                       value="{{ $user->id }}" id="remove_user_{{ $user->id }}"
                                       {{ in_array($user->id, $removeUserIds) ? 'checked' : '' }}>
                                <label class="form-check-label text-danger" for="remove_user_{{ $user->id }}">Remove</label>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3 mb-0">No users have this role</p>
                        @endforelse
                    </div>
                    <p class="text-muted text-center py-3 mb-0 d-none" id="assignedNoResults">No users match your search</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex flex-wrap gap-2 mb-4" >
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Update Assignments
        </button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function filterUsers(inputId, listId, noResultsId, countId, toolbarId) {
        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        const noResults = document.getElementById(noResultsId);
        const countBadge = document.getElementById(countId);
        const toolbar = document.getElementById(toolbarId);

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
                    ? items.length + ' users'
                    : visible + ' of ' + items.length + ' shown';
            }

            if (toolbar) {
                toolbar.classList.toggle('d-none', query !== '' && visible === 0);
            }
        });
    }

    function getVisibleCheckboxes(selector) {
        return Array.from(document.querySelectorAll(selector)).filter(function(cb) {
            const row = cb.closest('.user-search-item');
            return row && !row.classList.contains('d-none');
        });
    }

    function syncSelectAll(selectAllId, checkboxSelector) {
        const selectAll = document.getElementById(selectAllId);
        if (!selectAll) return;

        const visible = getVisibleCheckboxes(checkboxSelector);
        const checked = visible.filter(cb => cb.checked);
        selectAll.checked = visible.length > 0 && visible.length === checked.length;
        selectAll.indeterminate = checked.length > 0 && checked.length < visible.length;
    }

    document.getElementById('selectAllAvailable')?.addEventListener('change', function() {
        getVisibleCheckboxes('.available-user').forEach(cb => cb.checked = this.checked);
        this.indeterminate = false;
    });

    document.getElementById('selectAllAssigned')?.addEventListener('change', function() {
        getVisibleCheckboxes('.assigned-user').forEach(cb => cb.checked = this.checked);
        this.indeterminate = false;
    });

    document.querySelectorAll('.available-user').forEach(cb => {
        cb.addEventListener('change', function() {
            syncSelectAll('selectAllAvailable', '.available-user');
        });
    });

    document.querySelectorAll('.assigned-user').forEach(cb => {
        cb.addEventListener('change', function() {
            syncSelectAll('selectAllAssigned', '.assigned-user');
        });
    });

    filterUsers('searchAvailable', 'availableUserList', 'availableNoResults', 'availableCount', 'availableToolbar');
    filterUsers('searchAssigned', 'assignedUserList', 'assignedNoResults', 'assignedCount', 'assignedToolbar');
});
</script>
@endpush
