@extends('layouts.admin')

@section('content')

    <div class="container-fluid my-2">

        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
            <div>
                <h4 class="mb-1">Sub Categories</h4>
                <p class="text-muted mb-0">Manage all sub categories</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('subcategory.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    Add Sub Category
                </a>
                <a href="{{ route('subcategory.export', request()->all()) }}" class="btn btn-success">
                    <i class="ti ti-file-export me-1"></i>
                    Export CSV
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <!-- FILTERS - FULLY RESPONSIVE WITH AUTO WIDTH -->
                <form method="GET" action="{{ route('subcategory.index') }}" class="mb-4">
                    <!-- Responsive flexbox layout that wraps naturally -->
                    <div class="filters-responsive">

                        <!-- Search - Auto width -->
                        <div class="filter-item filter-search">
                            <label class="form-label small text-muted mb-1">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search..."
                                value="{{ request('search') }}">
                        </div>

                        <!-- Category Filter - Auto width -->
                        <div class="filter-item filter-category">
                            <label class="form-label small text-muted mb-1">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status - Auto width -->
                        <div class="filter-item filter-status">
                            <label class="form-label small text-muted mb-1">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Buttons - Auto width, aligns properly -->
                        <div class="filter-item filter-buttons">
                            <label class="form-label small text-muted mb-1 d-none d-md-block">&nbsp;</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit"
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-1">
                                    <i class="ti ti-filter"></i> Filter
                                </button>

                                <a href="{{ route('subcategory.index') }}"
                                    class="btn btn-secondary d-flex align-items-center justify-content-center gap-1">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Active Filters Display -->
                @if (request('search') || request('category_id') || request('status'))
                    <div class="alert alert-light border mb-3 py-2">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="small fw-semibold">Active Filters:</span>
                            @if (request('search'))
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ route('subcategory.index', array_merge(request()->except('search', 'page'), ['page' => 1])) }}"
                                        class="text-primary ms-1">✕</a>
                                </span>
                            @endif
                            @if (request('category_id') && ($selectedCategory = $categories->where('id', request('category_id'))->first()))
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    Category: {{ $selectedCategory->name }}
                                    <a href="{{ route('subcategory.index', array_merge(request()->except('category_id', 'page'), ['page' => 1])) }}"
                                        class="text-info ms-1">✕</a>
                                </span>
                            @endif
                            @if (request('status') !== null && request('status') !== '')
                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                    Status: {{ request('status') == '1' ? 'Active' : 'Inactive' }}
                                    <a href="{{ route('subcategory.index', array_merge(request()->except('status', 'page'), ['page' => 1])) }}"
                                        class="text-warning ms-1">✕</a>
                                </span>
                            @endif
                            <a href="{{ route('subcategory.index') }}" class="btn btn-link btn-sm p-0 ms-auto">Clear
                                All</a>
                        </div>
                    </div>
                @endif

                <div class="table-responsive">

                    <table class="table table-custom table-centered table-hover w-100 mb-0">

                        <thead class="bg-light align-middle bg-opacity-25 thead-sm">

                            <tr class="text-uppercase fs-xxs">
                                <th>#</th>
                                <th>Image</th>
                                <th>Category</th>
                                <th>Name</th>
                                <th>Explore Menu</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($subcategories as $key => $subcategory)
                                <tr>
                                    <td class="align-middle">{{ $subcategories->firstItem() + $key }}</td>

                                    <td class="align-middle">
                                        <div class="category-img-wrapper" style="width: 50px; height: 50px;">
                                            @if ($subcategory->image)
                                                <img src="{{ asset('backend/assets/images/subcategory/' . $subcategory->image) }}"
                                                    class="rounded border" alt="{{ $subcategory->name }}"
                                                    style="width:100%;height:100%;object-fit:cover;">
                                            @else
                                                <div
                                                    class="rounded border bg-light d-flex align-items-center justify-content-center h-100 w-100">
                                                    <span class="text-muted fs-xxs">No img</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="align-middle">{{ $subcategory->category->name ?? '-' }}</td>

                                    <td class="align-middle">
                                        <h5 class="mb-0 fs-6">{{ $subcategory->name }}</h5>
                                    </td>

                                    <td class="align-middle">
                                        @if ($subcategory->show_in_explore == 1)
                                            <span
                                                class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fs-xxs">
                                                Yes
                                            </span>
                                        @else
                                            <span class="text-muted small">No</span>
                                        @endif
                                    </td>

                                    <td class="align-middle">
                                        @if ($subcategory->status == 1)
                                            <span
                                                class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-xxs">
                                                <i class="ti ti-circle-check me-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fs-xxs">
                                                <i class="ti ti-circle-x me-1"></i> Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Status Toggle - Preserve page and filters -->
                                            <form action="{{ route('subcategory.status', $subcategory->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <!-- Preserve all query parameters -->
                                                @if (request('page'))
                                                    <input type="hidden" name="page" value="{{ request('page') }}">
                                                @endif
                                                @if (request('search'))
                                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                                @endif
                                                @if (request('category_id'))
                                                    <input type="hidden" name="category_id"
                                                        value="{{ request('category_id') }}">
                                                @endif
                                                @if (request('status') !== null && request('status') !== '')
                                                    <input type="hidden" name="status"
                                                        value="{{ request('status') }}">
                                                @endif
                                                @if (request('date'))
                                                    <input type="hidden" name="date" value="{{ request('date') }}">
                                                @endif
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm rounded-circle">
                                                    @if ($subcategory->status == 1)
                                                        <i class="ti ti-toggle-right fs-5 text-success"></i>
                                                    @else
                                                        <i class="ti ti-toggle-left fs-5 text-danger"></i>
                                                    @endif
                                                </button>
                                            </form>

                                            <!-- Edit - Preserve page and filters -->
                                            <a href="{{ route('subcategory.edit', $subcategory->id) . '?' . http_build_query(request()->except('_token')) }}"
                                                class="btn btn-light btn-icon btn-sm rounded-circle">
                                                <i class="ti ti-edit fs-5"></i>
                                            </a>

                                            <!-- Delete - Preserve page and filters -->
                                            <form action="{{ route('subcategory.destroy', $subcategory->id) }}"
                                                method="POST" onsubmit="return confirm('Delete this sub category?')">
                                                @csrf
                                                @method('DELETE')
                                                <!-- Preserve all query parameters -->
                                                @if (request('page'))
                                                    <input type="hidden" name="page" value="{{ request('page') }}">
                                                @endif
                                                @if (request('search'))
                                                    <input type="hidden" name="search"
                                                        value="{{ request('search') }}">
                                                @endif
                                                @if (request('category_id'))
                                                    <input type="hidden" name="category_id"
                                                        value="{{ request('category_id') }}">
                                                @endif
                                                @if (request('status') !== null && request('status') !== '')
                                                    <input type="hidden" name="status"
                                                        value="{{ request('status') }}">
                                                @endif
                                                @if (request('date'))
                                                    <input type="hidden" name="date" value="{{ request('date') }}">
                                                @endif
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm rounded-circle">
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
                                            <i class="ti ti-category-empty fs-1 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No sub categories found</p>
                                            <a href="{{ route('subcategory.create') }}"
                                                class="btn btn-sm btn-primary mt-3">
                                                <i class="ti ti-plus me-1"></i> Add your first sub category
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                <!-- Responsive Pagination with preserved filters - FIXED for empty query strings -->
                @if ($subcategories->hasPages())
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-2 border-top">
                        <!-- Showing Entries -->
                        <div class="text-muted small mb-3 mb-md-0">
                            Showing
                            <span class="fw-semibold">{{ $subcategories->firstItem() }}</span>
                            to
                            <span class="fw-semibold">{{ $subcategories->lastItem() }}</span>
                            of
                            <span class="fw-semibold">{{ $subcategories->total() }}</span>
                            entries
                        </div>

                        <!-- Custom Responsive Pagination Controls with filter preservation -->
                        <nav aria-label="Subcategory pagination">
                            <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-1">
                                {{-- Previous Page Link --}}
                                @if ($subcategories->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link rounded-3"><i class="ti ti-chevron-left"></i></span>
                                    </li>
                                @else
                                    @php
                                        $queryString = http_build_query(request()->except('page'));
                                        $prevUrl = $subcategories->previousPageUrl();
                                        if ($queryString) {
                                            $prevUrl .= (strpos($prevUrl, '?') !== false ? '&' : '?') . $queryString;
                                        }
                                    @endphp
                                    <li class="page-item">
                                        <a class="page-link rounded-3" href="{{ $prevUrl }}" rel="prev">
                                            <i class="ti ti-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements with responsive behavior --}}
                                @php
                                    $currentPage = $subcategories->currentPage();
                                    $lastPage = $subcategories->lastPage();
                                    $start = max(1, $currentPage - 2);
                                    $end = min($lastPage, $currentPage + 2);
                                    $baseQueryString = http_build_query(request()->except('page'));
                                @endphp

                                {{-- First page link --}}
                                @if ($start > 1)
                                    <li class="page-item d-none d-sm-block">
                                        @php
                                            $firstUrl = $subcategories->url(1);
                                            if ($baseQueryString) {
                                                $firstUrl .=
                                                    (strpos($firstUrl, '?') !== false ? '&' : '?') . $baseQueryString;
                                            }
                                        @endphp
                                        <a class="page-link rounded-3" href="{{ $firstUrl }}">1</a>
                                    </li>
                                    @if ($start > 2)
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
                                            @php
                                                $pageUrl = $subcategories->url($i);
                                                if ($baseQueryString) {
                                                    $pageUrl .=
                                                        (strpos($pageUrl, '?') !== false ? '&' : '?') .
                                                        $baseQueryString;
                                                }
                                            @endphp
                                            <a class="page-link rounded-3"
                                                href="{{ $pageUrl }}">{{ $i }}</a>
                                        </li>
                                    @endif
                                @endfor

                                {{-- Last page link --}}
                                @if ($end < $lastPage)
                                    @if ($end < $lastPage - 1)
                                        <li class="page-item disabled d-none d-sm-block">
                                            <span class="page-link rounded-3">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item d-none d-sm-block">
                                        @php
                                            $lastUrl = $subcategories->url($lastPage);
                                            if ($baseQueryString) {
                                                $lastUrl .=
                                                    (strpos($lastUrl, '?') !== false ? '&' : '?') . $baseQueryString;
                                            }
                                        @endphp
                                        <a class="page-link rounded-3"
                                            href="{{ $lastUrl }}">{{ $lastPage }}</a>
                                    </li>
                                @endif

                                {{-- Next Page Link --}}
                                @if ($subcategories->hasMorePages())
                                    @php
                                        $queryString = http_build_query(request()->except('page'));
                                        $nextUrl = $subcategories->nextPageUrl();
                                        if ($queryString) {
                                            $nextUrl .= (strpos($nextUrl, '?') !== false ? '&' : '?') . $queryString;
                                        }
                                    @endphp
                                    <li class="page-item">
                                        <a class="page-link rounded-3" href="{{ $nextUrl }}" rel="next">
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

                    <!-- Mobile simplified pagination (visible only on small screens) -->
                    <div class="d-flex d-sm-none justify-content-center mt-3">
                        <div class="btn-group" role="group">
                            @if (!$subcategories->onFirstPage())
                                @php
                                    $queryString = http_build_query(request()->except('page'));
                                    $prevUrl = $subcategories->previousPageUrl();
                                    if ($queryString) {
                                        $prevUrl .= (strpos($prevUrl, '?') !== false ? '&' : '?') . $queryString;
                                    }
                                @endphp
                                <a href="{{ $prevUrl }}" class="btn btn-outline-primary btn-sm">
                                    <i class="ti ti-chevron-left"></i> Prev
                                </a>
                            @else
                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                    <i class="ti ti-chevron-left"></i> Prev
                                </button>
                            @endif

                            <span class="btn btn-light btn-sm border disabled">
                                Page {{ $subcategories->currentPage() }} of {{ $subcategories->lastPage() }}
                            </span>

                            @if ($subcategories->hasMorePages())
                                @php
                                    $queryString = http_build_query(request()->except('page'));
                                    $nextUrl = $subcategories->nextPageUrl();
                                    if ($queryString) {
                                        $nextUrl .= (strpos($nextUrl, '?') !== false ? '&' : '?') . $queryString;
                                    }
                                @endphp
                                <a href="{{ $nextUrl }}" class="btn btn-outline-primary btn-sm">
                                    Next <i class="ti ti-chevron-right"></i>
                                </a>
                            @else
                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                    Next <i class="ti ti-chevron-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @elseif($subcategories->total() > 0)
                    <!-- Simple info when only one page -->
                    <div class="text-center text-muted small mt-3 pt-2 border-top">
                        Showing {{ $subcategories->total() }} entries
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

        .category-img-wrapper {
            background-color: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }

        .table-centered th,
        .table-centered td {
            vertical-align: middle;
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

        /* RESPONSIVE FILTERS - AUTO WIDTH, NATURAL WRAPPING */
        .filters-responsive {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 1rem;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-width: 140px;
        }

        /* Search field - takes more space but still responsive */
        .filter-search {
            flex: 2 1 200px;
            min-width: 160px;
        }

        /* Category field */
        .filter-category {
            flex: 1.5 1 160px;
            min-width: 150px;
        }

        /* Status field */
        .filter-status {
            flex: 1 1 120px;
            min-width: 120px;
        }

        /* Buttons container - doesn't grow unnecessarily */
        .filter-buttons {
            flex: 0 1 auto;
            min-width: auto;
        }

        /* Label spacing for buttons on larger screens */
        .filter-buttons .form-label {
            visibility: hidden;
            height: 0;
            margin-bottom: 0;
        }

        /* On smaller screens, remove hidden label space */
        @media (max-width: 767.98px) {
            .filter-buttons .form-label.d-none {
                display: none !important;
            }
        }

        /* Make buttons consistent width on mobile */
        @media (max-width: 575.98px) {
            .filters-responsive {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-item {
                width: 100%;
            }

            .filter-buttons .d-flex {
                width: 100%;
            }

            .filter-buttons .btn {
                flex: 1;
            }
        }

        /* For tablets, adjust layout */
        @media (min-width: 576px) and (max-width: 991.98px) {
            .filter-search {
                flex: 3 1 200px;
            }

            .filter-category {
                flex: 2 1 160px;
            }

            .filter-status {
                flex: 1 1 120px;
            }

            .filter-buttons .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }

        /* Custom pagination styling */
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

        /* Make table column widths auto-adjust properly */
        .table-responsive .table {
            width: 100%;
            min-width: 600px;
        }

        .table-responsive .table th,
        .table-responsive .table td {
            white-space: nowrap;
        }

        /* For proper wrapping on smaller screens */
        @media (max-width: 768px) {
            .table-responsive .table td {
                white-space: normal;
                word-break: break-word;
            }
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
