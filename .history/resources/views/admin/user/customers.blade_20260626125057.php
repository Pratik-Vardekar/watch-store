@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="mb-1">Customers</h4>
            <p class="text-muted mb-0">Manage all registered customers</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>#</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Orders</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $key => $user)
                            <tr>
                                <td class="align-middle">{{ $users->firstItem() + $key }}</td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <h5 class="mb-0 fs-6">{{ $user->name }}</h5>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">{{ $user->email }}</td>
                                <td class="align-middle">{{ $user->phone ?? '-' }}</td>
                                <td class="align-middle">
                                    @if($user->status == 1)
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-xxs">
                                            <i class="ti ti-circle-check me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fs-xxs">
                                            <i class="ti ti-circle-x me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="align-middle">
                                    <span class="badge bg-primary">{{ $user->orders->count() }}</span>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('user.edit', $user) }}" class="btn btn-light btn-icon btn-sm rounded-circle">
                                            <i class="ti ti-edit fs-5"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti ti-users fs-1 text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No customers found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-2 border-top">
                    <div class="text-muted small mb-3 mb-md-0">
                        Showing
                        <span class="fw-semibold">{{ $users->firstItem() }}</span>
                        to
                        <span class="fw-semibold">{{ $users->lastItem() }}</span>
                        of
                        <span class="fw-semibold">{{ $users->total() }}</span>
                        entries
                    </div>
                    <nav aria-label="Customer pagination">
                        <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-1">
                            {{ $users->appends(request()->except('page'))->links() }}
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-success-subtle {
        background-color: #d1fae5 !important;
    }
    .bg-danger-subtle {
        background-color: #fee2e2 !important;
    }
    .rounded-pill {
        border-radius: 50rem !important;
    }
    .fs-xxs {
        font-size: 0.7rem;
    }
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        padding: 0;
    }
    .gap-1 {
        gap: 0.25rem;
    }
    .table-centered th,
    .table-centered td {
        vertical-align: middle;
    }
</style>
@endpush