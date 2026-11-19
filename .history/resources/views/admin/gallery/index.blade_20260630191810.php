@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Gallery</h4>
            <p class="text-muted mb-0">Manage all galleries</p>
        </div>
        <a href="{{ route('gallery.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>
            Add Gallery Category
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

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th style="width: 100px">Image</th>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th style="width: 110px">Status</th>
                            <th style="width: 130px" class="text-center">Action</th>
                            <th style="width: 130px" class="text-center">Images</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($galleries as $index => $gallery)
                        <tr>
                            <td class="align-middle">
                                @if(method_exists($galleries, 'firstItem'))
                                    {{ $galleries->firstItem() + $index }}
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($gallery->image && file_exists(public_path('backend/assets/images/gallery/'.$gallery->image)))
                                    <img src="{{ asset('backend/assets/images/gallery/'.$gallery->image) }}"
                                         style="width: 60px; height: 60px; object-fit: cover;"
                                         class="rounded border"
                                         alt="{{ $gallery->title }}">
                                @else
                                    <div class="rounded border d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 60px; background: #f5f5f5;">
                                        <i class="ti ti-photo text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <h5 class="mb-1 fs-6">{{ $gallery->title }}</h5>
                            </td>
                            <td class="align-middle">
                                <span class="text-muted">{{ $gallery->subtitle }}</span>
                            </td>
                            <td class="align-middle">
                                @if($gallery->status)
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
                                    <!-- Edit -->
                                    <a href="{{ route('gallery.edit', $gallery->id) }}"
                                       class="btn btn-light btn-icon btn-sm rounded-circle"
                                       title="Edit Gallery">
                                        <i class="ti ti-edit fs-5"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('gallery.destroy', $gallery->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this gallery? This action cannot be undone.')" style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-light btn-icon btn-sm rounded-circle"
                                                title="Delete Gallery">
                                            <i class="ti ti-trash fs-5 text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                <a href="{{ route('gallery.images.index', $gallery->id) }}"
                                   class="btn btn-info btn-icon btn-sm rounded-circle"
                                   title="Manage Images">
                                    <i class="ti ti-photo fs-5"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ti ti-photo fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No galleries found</p>
                                    <a href="{{ route('gallery.create') }}" class="btn btn-sm btn-primary mt-3">
                                        <i class="ti ti-plus me-1"></i>
                                        Add your first gallery
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Responsive Pagination -->
            @if(method_exists($galleries, 'hasPages') && $galleries->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-2 border-top">
                    <div class="text-muted small mb-3 mb-md-0">
                        Showing
                        <span class="fw-semibold">{{ $galleries->firstItem() }}</span>
                        to
                        <span class="fw-semibold">{{ $galleries->lastItem() }}</span>
                        of
                        <span class="fw-semibold">{{ $galleries->total() }}</span>
                        entries
                    </div>

                    <nav aria-label="Gallery pagination">
                        <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-1">
                            {{-- Previous Page Link --}}
                            @if ($galleries->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link rounded-3"><i class="ti ti-chevron-left"></i></span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $galleries->previousPageUrl() }}" rel="prev">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $galleries->currentPage();
                                $lastPage = $galleries->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp

                            {{-- First page link --}}
                            @if($start > 1)
                                <li class="page-item d-none d-sm-block">
                                    <a class="page-link rounded-3" href="{{ $galleries->url(1) }}">1</a>
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
                                        <a class="page-link rounded-3" href="{{ $galleries->url($i) }}">{{ $i }}</a>
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
                                    <a class="page-link rounded-3" href="{{ $galleries->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($galleries->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $galleries->nextPageUrl() }}" rel="next">
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
                        @if (!$galleries->onFirstPage())
                            <a href="{{ $galleries->previousPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-chevron-left"></i> Prev
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                        @endif

                        <span class="btn btn-light btn-sm border disabled">
                            Page {{ $galleries->currentPage() }} of {{ $galleries->lastPage() }}
                        </span>

                        @if ($galleries->hasMorePages())
                            <a href="{{ $galleries->nextPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                Next <i class="ti ti-chevron-right"></i>
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                Next <i class="ti ti-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @elseif(method_exists($galleries, 'count') && $galleries->count() > 0)
                <div class="text-center text-muted small mt-3 pt-2 border-top">
                    Showing {{ $galleries->count() }} entries
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