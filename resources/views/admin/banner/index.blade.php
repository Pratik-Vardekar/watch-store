@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Watches Section</h4>
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
            <form method="GET" action="{{ route('blog.index') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">
                            Search
                        </label>
                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by title, category or author..."
                            value="{{ request('search') }}">
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

                            <a href="{{ route('blog.index') }}" class="btn btn-secondary">
                                <i class="ti ti-refresh"></i>
                                Reset
                            </a>

                            <!-- CSV Export Button -->
                            <a href="{{ route('blog.export', request()->all()) }}" class="btn btn-success">
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
                            <th style="width: 100px">Image</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th style="width: 100px">Status</th>
                            <th style="width: 100px">Created</th>
                            <th style="width: 130px" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $index => $blog)
                        <tr>
                            <td class="align-middle">
                                @if(method_exists($blogs, 'firstItem'))
                                    {{ $blogs->firstItem() + $index }}
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($blog->image && file_exists(public_path('backend/assets/images/blog/'.$blog->image)))
                                    <img src="{{ asset('backend/assets/images/blog/'.$blog->image) }}"
                                        style="width: 60px; height: 60px; object-fit: cover;"
                                        class="rounded border"
                                        alt="{{ $blog->title }}">
                                @else
                                    <div class="rounded border d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px; background: #f5f5f5;">
                                        <i class="ti ti-photo text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <h5 class="mb-1 fs-6">{{ $blog->title }}</h5>
                                @if($blog->short_description)
                                    <small class="text-muted d-block">{{ Str::limit($blog->short_description, 60) }}</small>
                                @endif
                            </td>
                            <td class="align-middle">
                                {{-- Blog dynamic content show --}}
                                @if($blog->content)
                                    <div class="text-truncate" style="max-width:250px;" title="{{ strip_tags($blog->content) }}">
                                        {{ Str::limit(strip_tags($blog->content), 100) }}
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($blog->status == 1)
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
                            <td class="align-middle text-muted small">
                                {{ $blog->created_at->format('d M, Y') }}
                            </td>
                            <td class="align-middle">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Status Toggle -->
                                    <form action="{{ route('blog.status', $blog->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn btn-light btn-icon btn-sm rounded-circle"
                                            title="{{ $blog->status == 1 ? 'Deactivate' : 'Activate' }}">
                                            @if($blog->status == 1)
                                                <i class="ti ti-toggle-right fs-5 text-success"></i>
                                            @else
                                                <i class="ti ti-toggle-left fs-5 text-danger"></i>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Edit -->
                                    <a href="{{ route('blog.edit', $blog->id) }}"
                                        class="btn btn-light btn-icon btn-sm rounded-circle"
                                        title="Edit Blog">
                                        <i class="ti ti-edit fs-5"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('blog.destroy', $blog->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this blog? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-light btn-icon btn-sm rounded-circle"
                                            title="Delete Blog">
                                            <i class="ti ti-trash fs-5 text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ti ti-news fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No blogs found</p>
                                    <a href="{{ route('blog.create') }}" class="btn btn-sm btn-primary mt-3">
                                        <i class="ti ti-plus me-1"></i>
                                        Add your first blog
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Responsive Pagination -->
            @if(method_exists($blogs, 'hasPages') && $blogs->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-2 border-top">
                    <div class="text-muted small mb-3 mb-md-0">
                        Showing
                        <span class="fw-semibold">{{ $blogs->firstItem() }}</span>
                        to
                        <span class="fw-semibold">{{ $blogs->lastItem() }}</span>
                        of
                        <span class="fw-semibold">{{ $blogs->total() }}</span>
                        entries
                    </div>

                    <nav aria-label="Blog pagination">
                        <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-1">
                            {{-- Previous Page Link --}}
                            @if ($blogs->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link rounded-3"><i class="ti ti-chevron-left"></i></span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $blogs->previousPageUrl() }}" rel="prev">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $blogs->currentPage();
                                $lastPage = $blogs->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp

                            {{-- First page link --}}
                            @if($start > 1)
                                <li class="page-item d-none d-sm-block">
                                    <a class="page-link rounded-3" href="{{ $blogs->url(1) }}">1</a>
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
                                        <a class="page-link rounded-3" href="{{ $blogs->url($i) }}">{{ $i }}</a>
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
                                    <a class="page-link rounded-3" href="{{ $blogs->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($blogs->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $blogs->nextPageUrl() }}" rel="next">
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
                        @if (!$blogs->onFirstPage())
                            <a href="{{ $blogs->previousPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-chevron-left"></i> Prev
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                        @endif

                        <span class="btn btn-light btn-sm border disabled">
                            Page {{ $blogs->currentPage() }} of {{ $blogs->lastPage() }}
                        </span>

                        @if ($blogs->hasMorePages())
                            <a href="{{ $blogs->nextPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                Next <i class="ti ti-chevron-right"></i>
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                Next <i class="ti ti-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @elseif(method_exists($blogs, 'count') && $blogs->count() > 0)
                <div class="text-center text-muted small mt-3 pt-2 border-top">
                    Showing {{ $blogs->count() }} entries
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