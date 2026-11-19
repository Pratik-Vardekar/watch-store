{{-- resources/views/admin/coupon/index.blade.php --}}

@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Coupons</h4>
            <p class="text-muted mb-0">Manage all coupons</p>
        </div>

        {{-- <a href="{{ route('coupon.create') }}" class="btn ">
            <i class="ti ti-plus me-1"></i>
            Add Coupon
        </a> --}}
        <a href="{{ route('coupon.create') }}"
        class="btn"
        style="background:#eb6c16; border-color:#eb6c16; color:#fff;">
         <i class="ti ti-plus me-1"></i>
         Add Coupon
     </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <!-- FILTERS -->
            <form method="GET" action="{{ route('coupon.index') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">
                            Search
                        </label>
                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by code or title..."
                            value="{{ request('search') }}">
                    </div>

                    <!-- Type Filter -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">
                            Type
                        </label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="percent" {{ request('type') == 'percent' ? 'selected' : '' }}>
                                Percentage
                            </option>
                            <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>
                                Fixed
                            </option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">
                            Status
                        </label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">
                            Sort By
                        </label>
                        <select name="sort" class="form-select">
                            <option value="">Latest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                Oldest First
                            </option>
                            <option value="code_asc" {{ request('sort') == 'code_asc' ? 'selected' : '' }}>
                                Code (A-Z)
                            </option>
                            <option value="code_desc" {{ request('sort') == 'code_desc' ? 'selected' : '' }}>
                                Code (Z-A)
                            </option>
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">
                            Created Date
                        </label>
                        <input type="date"
                            name="date"
                            class="form-control"
                            value="{{ request('date') }}">
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-filter me-1"></i>
                                Filter
                            </button>

                            <a href="{{ route('coupon.index') }}" class="btn btn-secondary">
                                <i class="ti ti-refresh"></i>
                                Reset
                            </a>

                            <!-- CSV Export Button -->
                            <a href="{{ route('coupon.export', request()->all()) }}" class="btn btn-success">
                                <i class="ti ti-file-export"></i>
                                Export CSV
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Minimum Amount</th>
                            <th>Usage</th>
                            <th>Validity</th>
                            <th style="width: 100px">Status</th>
                            <th style="width: 130px" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $index => $coupon)
                        <tr>
                            <td class="align-middle">
                                @if(method_exists($coupons, 'firstItem'))
                                    {{ $coupons->firstItem() + $index }}
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="fw-semibold fs-6">{{ $coupon->code }}</span>
                                <br>
                                <small class="text-muted">{{ $coupon->title ?? 'No title' }}</small>
                            </td>
                            <td class="align-middle">
                                @if($coupon->type == 'percent')
                                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                        <i class="ti ti-percent me-1"></i> Percentage
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">
                                        <i class="ti ti-currency-rupee me-1"></i> Fixed
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($coupon->type == 'percent')
                                    <span class="fw-semibold">{{ $coupon->value }}%</span>
                                    @if($coupon->maximum_discount)
                                        <br>
                                        <small class="text-muted">Max: ₹{{ $coupon->maximum_discount }}</small>
                                    @endif
                                @else
                                    <span class="fw-semibold">₹{{ number_format($coupon->value, 2) }}</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($coupon->minimum_amount && $coupon->minimum_amount > 0)
                                    <span class="text-muted">₹{{ number_format($coupon->minimum_amount, 2) }}</span>
                                @else
                                    <span class="text-muted">No minimum</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="fw-semibold">{{ $coupon->used_count }}</span>
                                @if($coupon->usage_limit)
                                    <span class="text-muted"> / {{ $coupon->usage_limit }}</span>
                                @else
                                    <span class="text-muted"> / Unlimited</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($coupon->start_date)
                                    <div class="small">
                                        <div>From: {{ date('d M Y', strtotime($coupon->start_date)) }}</div>
                                        <div>To: {{ date('d M Y', strtotime($coupon->end_date)) }}</div>
                                        @if(now()->lt($coupon->start_date))
                                            <span class="badge bg-secondary mt-1">Upcoming</span>
                                        @elseif(now()->gt($coupon->end_date))
                                            <span class="badge bg-danger mt-1">Expired</span>
                                        @else
                                            <span class="badge bg-success mt-1">Active Period</span>
                                        @endif
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($coupon->status == 1)
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-xxs">
                                        <i class="ti ti-circle-check me-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fs-xxs">
                                        <i class="ti ti-circle-x me-1"></i>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Status Toggle -->
                                    <form action="{{ route('coupon.status', $coupon->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="btn btn-light btn-icon btn-sm rounded-circle"
                                            title="{{ $coupon->status == 1 ? 'Deactivate' : 'Activate' }}">
                                            @if($coupon->status == 1)
                                                <i class="ti ti-toggle-right fs-5 text-success"></i>
                                            @else
                                                <i class="ti ti-toggle-left fs-5 text-danger"></i>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Edit -->
                                    <a href="{{ route('coupon.edit', $coupon->id) }}"
                                        class="btn btn-light btn-icon btn-sm rounded-circle"
                                        title="Edit Coupon">
                                        <i class="ti ti-edit fs-5"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('coupon.destroy', $coupon->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this coupon? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-light btn-icon btn-sm rounded-circle"
                                            title="Delete Coupon">
                                            <i class="ti ti-trash fs-5 text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ti ti-ticket-off fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No coupons found</p>
                                    <a href="{{ route('coupon.create') }}" class="btn btn-sm btn-primary mt-3">
                                        <i class="ti ti-plus me-1"></i>
                                        Add your first coupon
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Responsive Pagination -->
            @if(method_exists($coupons, 'hasPages') && $coupons->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-2 border-top">
                    <div class="text-muted small mb-3 mb-md-0">
                        Showing
                        <span class="fw-semibold">{{ $coupons->firstItem() }}</span>
                        to
                        <span class="fw-semibold">{{ $coupons->lastItem() }}</span>
                        of
                        <span class="fw-semibold">{{ $coupons->total() }}</span>
                        entries
                    </div>

                    <nav aria-label="Coupon pagination">
                        <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-1">
                            {{-- Previous Page Link --}}
                            @if ($coupons->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link rounded-3"><i class="ti ti-chevron-left"></i></span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $coupons->previousPageUrl() }}" rel="prev">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $coupons->currentPage();
                                $lastPage = $coupons->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp

                            {{-- First page link --}}
                            @if($start > 1)
                                <li class="page-item d-none d-sm-block">
                                    <a class="page-link rounded-3" href="{{ $coupons->url(1) }}">1</a>
                                </li>
                                @if($start > 2)
                                    <li class="page-item disabled d-none d-sm-block">
                                        <span class="page-link rounded-3">...</span>
                                    </li>
                                @endif
                            @endif

                            {{-- Page numbers --}}
                            @for ($i = $start; $i <= $end; $i++)
                                @if ($i == $currentPage)
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link rounded-3">{{ $i }}</span>
                                    </li>
                                @else
                                    <li class="page-item d-none d-sm-block">
                                        <a class="page-link rounded-3" href="{{ $coupons->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Last page link --}}
                            @if($end < $lastPage)
                                @if($end < $lastPage - 1)
                                    <li class="page-item disabled d-none d-sm-block">
                                        <span class="page-link rounded-3">...</span>
                                    </li>
                                @endif
                                <li class="page-item d-none d-sm-block">
                                    <a class="page-link rounded-3" href="{{ $coupons->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($coupons->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $coupons->nextPageUrl() }}" rel="next">
                                        <i class="ti ti-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link rounded-3"><i class="ti ti-chevron-right"></i></span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>

                <!-- Mobile simplified pagination -->
                <div class="d-flex d-sm-none justify-content-center mt-3">
                    <div class="btn-group" role="group">
                        @if (!$coupons->onFirstPage())
                            <a href="{{ $coupons->previousPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-chevron-left"></i> Prev
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                        @endif

                        <span class="btn btn-light btn-sm border disabled">
                            Page {{ $coupons->currentPage() }} of {{ $coupons->lastPage() }}
                        </span>

                        @if ($coupons->hasMorePages())
                            <a href="{{ $coupons->nextPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                Next <i class="ti ti-chevron-right"></i>
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                Next <i class="ti ti-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @elseif(method_exists($coupons, 'count') && $coupons->count() > 0)
                <div class="text-center text-muted small mt-3 pt-2 border-top">
                    Showing {{ $coupons->count() }} entries
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
    .bg-info-subtle {
        background-color: #d1e9ff !important;
    }
    .bg-warning-subtle {
        background-color: #fff3cd !important;
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
    .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        color: #4f5d73;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    .page-link:hover {
        background-color: #f1f5f9;
        color: #1e293b;
        border-color: #cbd5e1;
    }
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    .page-item.disabled .page-link {
        color: #94a3b8;
        background-color: #f8fafc;
        border-color: #e2e8f0;
    }
    .rounded-3 {
        border-radius: 0.5rem !important;
    }
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.85rem;
        }
        .btn-icon {
            width: 28px;
            height: 28px;
        }
        .fs-5 {
            font-size: 1rem !important;
        }
    }
</style>
@endpush