@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h4 class="mb-1">Sliders</h4>
            <p class="text-muted mb-0">Manage sliders</p>
        </div>

        <a href="{{ route('slider.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>
            Add Slider
        </a>
    </div>

    <div class="alert alert-light border mb-4 py-2">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div>
                <strong class="text-dark">Desktop Image:</strong>
                <span class="text-dark">1920 × 825 px</span>
                <span class="text-primary">(aspect ratio 128:55)</span>
            </div>
            <div class="text-muted d-none d-md-inline">|</div>
            <div>
                <strong class="text-dark">Mobile Image:</strong>
                <span class="text-dark">750 × 800 px or 600 × 600 px</span>
                <span class="text-muted">(optional, falls back to desktop image)</span>
            </div>
        </div>
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
            <form method="GET" action="{{ route('slider.index') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">
                            Search
                        </label>
                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by title..."
                            value="{{ request('search') }}">
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-3">
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
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="ti ti-filter me-1"></i>
                                Filter
                            </button>

                            <a href="{{ route('slider.index') }}" class="btn btn-secondary">
                                <i class="ti ti-refresh"></i>
                            </a>

                            <!-- CSV Export Button -->
                            <a href="{{ route('slider.export', request()->all()) }}" class="btn btn-success">
                                <i class="ti ti-file-export"></i>
                                CSV
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
                            <th style="width: 170px">Images (Desk / Mob)</th>
                            <th>Slider Details</th>
                            <th style="width: 100px">Status</th>
                            <th style="width: 110px">Created</th>
                            <th style="width: 130px" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sliders as $index => $slider)
                        <tr>
                            <td class="align-middle">
                                @if(method_exists($sliders, 'firstItem'))
                                    {{ $sliders->firstItem() + $index }}
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Desktop thumbnail -->
                                    <div class="text-center">
                                        @if($slider->image)
                                            <a href="{{ $slider->image_url }}" target="_blank" title="Desktop Image (1920×825)">
                                                <img src="{{ $slider->image_url }}"
                                                    class="rounded border shadow-sm"
                                                    style="width: 80px; height: 46px; object-fit: cover; background: #f8fafc;"
                                                    alt="{{ $slider->title ?? 'Desktop Slider' }}"
                                                    onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/heroBanner_1.jpg') }}';">
                                            </a>
                                        @else
                                            <div class="rounded border d-flex flex-column align-items-center justify-content-center text-muted"
                                                style="width: 80px; height: 46px; background: #f8fafc; font-size: 0.65rem;">
                                                <i class="ti ti-photo-off mb-1"></i>
                                                <span>No Image</span>
                                            </div>
                                        @endif
                                        <div class="text-muted small" style="font-size: 0.68rem;">Desktop</div>
                                    </div>

                                    <!-- Mobile thumbnail -->
                                    <div class="text-center">
                                        @if($slider->mobile_image)
                                            <a href="{{ $slider->mobile_image_url }}" target="_blank" title="Mobile Image">
                                                <img src="{{ $slider->mobile_image_url }}"
                                                    class="rounded border shadow-sm"
                                                    style="width: 46px; height: 46px; object-fit: cover; background: #f8fafc;"
                                                    alt="{{ $slider->title ?? 'Mobile Slider' }}"
                                                    onerror="this.onerror=null;this.src='{{ $slider->image_url }}';">
                                            </a>
                                            <div class="text-success small" style="font-size: 0.68rem;">Mobile</div>
                                        @else
                                            <div class="rounded border d-flex flex-column align-items-center justify-content-center text-muted"
                                                style="width: 46px; height: 46px; background: #f8fafc; font-size: 0.62rem;"
                                                title="Uses Desktop fallback">
                                                <i class="ti ti-device-mobile"></i>
                                                <span>Auto</span>
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.68rem;">Auto</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($slider->title)
                                            <span class="fw-semibold text-dark fs-6">{{ $slider->title }}</span>
                                        @else
                                            <span class="text-muted fst-italic small">No Title</span>
                                        @endif

                                        @if(isset($slider->sort_order))
                                            <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.7rem;">Order: {{ $slider->sort_order }}</span>
                                        @endif
                                    </div>

                                    @if($slider->subtitle)
                                        <div class="small text-secondary">
                                            <span class="fw-medium text-dark">Subtitle:</span> {{ $slider->subtitle }}
                                        </div>
                                    @endif

                                    @if($slider->description)
                                        <div class="small text-muted" style="max-width: 420px;">
                                            <span class="fw-medium text-dark">Desc:</span> {{ Str::limit($slider->description, 80) }}
                                        </div>
                                    @endif

                                    @if($slider->link)
                                        <div class="small">
                                            <a href="{{ $slider->link }}" target="_blank" class="text-primary text-decoration-none">
                                                <i class="ti ti-link me-1"></i>{{ Str::limit($slider->link, 45) }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="align-middle">
                                @if($slider->status == 1)
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
                            <td class="align-middle text-muted">
                                {{ $slider->created_at->format('d M, Y') }}
                            </td>
                            <td class="align-middle">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Status Toggle -->
                                    <form action="{{ route('slider.status', $slider->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="btn btn-light btn-icon btn-sm rounded-circle"
                                            title="{{ $slider->status == 1 ? 'Deactivate' : 'Activate' }}">
                                            @if($slider->status == 1)
                                                <i class="ti ti-toggle-right fs-5 text-success"></i>
                                            @else
                                                <i class="ti ti-toggle-left fs-5 text-danger"></i>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Edit -->
                                    <a href="{{ route('slider.edit', $slider->id) }}"
                                        class="btn btn-light btn-icon btn-sm rounded-circle"
                                        title="Edit Slider">
                                        <i class="ti ti-edit fs-5"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('slider.destroy', $slider->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this slider? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-light btn-icon btn-sm rounded-circle"
                                            title="Delete Slider">
                                            <i class="ti ti-trash fs-5 text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ti ti-photo-off fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No sliders found</p>
                                    <a href="{{ route('slider.create') }}" class="btn btn-sm btn-primary mt-3">
                                        <i class="ti ti-plus me-1"></i>
                                        Add your first slider
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Responsive Pagination -->
            @if(method_exists($sliders, 'hasPages') && $sliders->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-2 border-top">
                    <div class="text-muted small mb-3 mb-md-0">
                        Showing
                        <span class="fw-semibold">{{ $sliders->firstItem() }}</span>
                        to
                        <span class="fw-semibold">{{ $sliders->lastItem() }}</span>
                        of
                        <span class="fw-semibold">{{ $sliders->total() }}</span>
                        entries
                    </div>

                    <nav aria-label="Slider pagination">
                        <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-1">
                            {{-- Previous Page Link --}}
                            @if ($sliders->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link rounded-3"><i class="ti ti-chevron-left"></i></span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $sliders->previousPageUrl() }}" rel="prev">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $sliders->currentPage();
                                $lastPage = $sliders->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp

                            {{-- First page link --}}
                            @if($start > 1)
                                <li class="page-item d-none d-sm-block">
                                    <a class="page-link rounded-3" href="{{ $sliders->url(1) }}">1</a>
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
                                        <a class="page-link rounded-3" href="{{ $sliders->url($i) }}">{{ $i }}</a>
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
                                    <a class="page-link rounded-3" href="{{ $sliders->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($sliders->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $sliders->nextPageUrl() }}" rel="next">
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
                        @if (!$sliders->onFirstPage())
                            <a href="{{ $sliders->previousPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-chevron-left"></i> Prev
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                        @endif

                        <span class="btn btn-light btn-sm border disabled">
                            Page {{ $sliders->currentPage() }} of {{ $sliders->lastPage() }}
                        </span>

                        @if ($sliders->hasMorePages())
                            <a href="{{ $sliders->nextPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                Next <i class="ti ti-chevron-right"></i>
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                Next <i class="ti ti-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @elseif(method_exists($sliders, 'count') && $sliders->count() > 0)
                <div class="text-center text-muted small mt-3 pt-2 border-top">
                    Showing {{ $sliders->count() }} entries
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
