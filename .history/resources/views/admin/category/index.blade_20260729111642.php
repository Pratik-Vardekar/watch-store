@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Categories</h4>
            <p class="text-muted mb-0">Manage all categories</p>
        </div>

        <a href="{{ route('category.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>
            Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <!-- FILTERS with AUTO WIDTH -->
            <form method="GET" action="{{ route('category.index') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search category..."
                            value="{{ request('search') }}"
                            style="width: 250px;">
                    </div>

                    <!-- Status Filter -->
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="status" class="form-select" style="width: 150px;">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">Created Date</label>
                        <input type="date"
                            name="date"
                            class="form-control"
                            value="{{ request('date') }}"
                            style="width: 180px;">
                    </div>

                    <!-- Buttons -->
                    <div class="col-auto">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('category.index') }}" class="btn btn-secondary">
                                <i class="ti ti-refresh me-1"></i>Reset
                            </a>
                            <a href="{{ route('category.export', request()->all()) }}" class="btn btn-success">
                                <i class="ti ti-file-export me-1"></i>CSV
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="alert alert-light border mb-3 py-2">
                <i class="ti ti-grip-vertical me-1"></i>
                Drag rows to change category display order (same as product gallery ordering).
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-hover w-100 mb-0 align-middle">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th class="text-center" style="width: 40px"></th>
                            <th class="text-center" style="width: 60px">#</th>
                            <th class="text-center" style="width: 80px">Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Slug</th>
                            <th class="text-center" style="width: 110px">Menu</th>
                            <th class="text-center" style="width: 80px">Order</th>
                            <th class="text-center" style="width: 120px">Products</th>
                            <th class="text-center" style="width: 100px">Status</th>
                            <th class="text-center" style="width: 140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-categories">
                        @forelse($categories as $index => $category)
                            <tr data-id="{{ $category->id }}">
                                <td class="text-center drag-handle" style="cursor: grab;">
                                    <i class="ti ti-grip-vertical text-muted"></i>
                                </td>
                                <td class="text-center">
                                    @if(method_exists($categories, 'firstItem'))
                                        {{ $categories->firstItem() + $index }}
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="category-img-wrapper" style="width: 50px; height: 50px; margin: 0 auto;">
                                        @if($category->image)
                                            <img src="{{ asset('backend/assets/images/category/' . $category->image) }}"
                                                class="rounded border"
                                                alt="{{ $category->name }}"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('backend/assets/images/no-image.png') }}"
                                                class="rounded border"
                                                alt="No image"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <h5 class="mb-0 fs-6 fw-semibold">{{ $category->name }}</h5>
                                </td>
                                <td class="text-muted">
                                    <small>
                                        {{ $category->description ? Str::limit(strip_tags($category->description), 60) : '-' }}
                                    </small>
                                </td>
                                <td class="text-muted">
                                    <small>{{ $category->slug }}</small>
                                </td>
                                <td class="text-center">
                                    @if($category->gender && isset(\App\Models\Category::GENDERS[$category->gender]))
                                        <span class="badge bg-primary-subtle text-primary px-2 py-1">
                                            {{ \App\Models\Category::GENDERS[$category->gender] }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 sort-order-badge">
                                        {{ $category->sort_order }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $productCount = $category->products_count ?? $category->products->count();
                                    @endphp
                                    @if($productCount > 0)
                                        <a href="{{ route('product.index', ['category_id' => $category->id]) }}" 
                                           class="text-decoration-none">
                                            <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                                <i class="ti ti-package me-1"></i>
                                                {{ $productCount }} {{ Str::plural('Product', $productCount) }}
                                            </span>
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                                            <i class="ti ti-package-off me-1"></i>
                                            0 Products
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($category->status == 1)
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-xxs">
                                            <i class="ti ti-circle-check me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fs-xxs">
                                            <i class="ti ti-circle-x me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Status Toggle -->
                                        <form action="{{ route('category.status', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="btn btn-light btn-icon btn-sm rounded-circle"
                                                title="{{ $category->status == 1 ? 'Deactivate' : 'Activate' }}">
                                                @if($category->status == 1)
                                                    <i class="ti ti-toggle-right fs-5 text-success"></i>
                                                @else
                                                    <i class="ti ti-toggle-left fs-5 text-danger"></i>
                                                @endif
                                            </button>
                                        </form>

                                        <!-- Edit -->
                                        <a href="{{ route('category.edit', $category->id) }}"
                                            class="btn btn-light btn-icon btn-sm rounded-circle"
                                            title="Edit Category">
                                            <i class="ti ti-edit fs-5"></i>
                                        </a>

                                        <!-- Delete -->
                                        @if(($category->products_count ?? $category->products->count()) > 0)
                                            <button type="button"
                                                class="btn btn-light btn-icon btn-sm rounded-circle"
                                                title="Cannot delete - Category has products"
                                                disabled>
                                                <i class="ti ti-trash fs-5 text-muted"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('category.destroy', $category->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Delete this category? This action cannot be undone.')"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm rounded-circle"
                                                    title="Delete Category">
                                                    <i class="ti ti-trash fs-5 text-danger"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti ti-category fs-1 text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No categories found</p>
                                        <a href="{{ route('category.create') }}" class="btn btn-sm btn-primary mt-3">
                                            <i class="ti ti-plus me-1"></i> Add category
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Responsive Pagination Area -->
            @if(method_exists($categories, 'hasPages') && $categories->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-2 border-top">
                    <!-- Showing Entries -->
                    <div class="text-muted small mb-3 mb-md-0">
                        Showing
                        <span class="fw-semibold">{{ $categories->firstItem() }}</span>
                        to
                        <span class="fw-semibold">{{ $categories->lastItem() }}</span>
                        of
                        <span class="fw-semibold">{{ $categories->total() }}</span>
                        entries
                    </div>

                    <!-- Custom Responsive Pagination Controls -->
                    <nav aria-label="Category pagination">
                        <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-1">
                            {{-- Previous Page Link --}}
                            @if ($categories->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link rounded-3"><i class="ti ti-chevron-left"></i></span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $categories->previousPageUrl() }}" rel="prev">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements with responsive behavior --}}
                            @php
                                $currentPage = $categories->currentPage();
                                $lastPage = $categories->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp

                            {{-- First page link --}}
                            @if($start > 1)
                                <li class="page-item d-none d-sm-block">
                                    <a class="page-link rounded-3" href="{{ $categories->url(1) }}">1</a>
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
                                        <a class="page-link rounded-3" href="{{ $categories->url($i) }}">{{ $i }}</a>
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
                                    <a class="page-link rounded-3" href="{{ $categories->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($categories->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $categories->nextPageUrl() }}" rel="next">
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
                        @if (!$categories->onFirstPage())
                            <a href="{{ $categories->previousPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-chevron-left"></i> Prev
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                        @endif

                        <span class="btn btn-light btn-sm border disabled">
                            Page {{ $categories->currentPage() }} of {{ $categories->lastPage() }}
                        </span>

                        @if ($categories->hasMorePages())
                            <a href="{{ $categories->nextPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                Next <i class="ti ti-chevron-right"></i>
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                Next <i class="ti ti-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @elseif($categories->total() > 0)
                <!-- Simple info when only one page -->
                <div class="text-center text-muted small mt-3 pt-2 border-top">
                    Showing {{ $categories->total() }} entries
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Force all table cells to be vertically middle aligned */
    .table.align-middle > :not(caption) > * > * {
        vertical-align: middle !important;
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

    /* Badge styling */
    .bg-success-subtle {
        background-color: #d1fae5 !important;
    }
    .bg-danger-subtle {
        background-color: #fee2e2 !important;
    }
    .bg-info-subtle {
        background-color: #d1e9ff !important;
    }
    .bg-secondary-subtle {
        background-color: #e9ecef !important;
    }
    .rounded-pill {
        border-radius: 50rem !important;
    }
    .fs-xxs {
        font-size: 0.7rem;
    }
    
    /* Category image wrapper */
    .category-img-wrapper {
        background-color: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
        margin: 0 auto;
    }
    
    /* Button icon styling */
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
    
    /* Ensure proper spacing and alignment */
    .table-custom td, 
    .table-custom th {
        vertical-align: middle;
    }
    
    /* Fix for name column alignment */
    .table-custom td:not(.text-center) {
        vertical-align: middle;
    }
    
    /* Responsive table handling */
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

    .sortable-ghost {
        opacity: 0.4;
        background: #f1f5f9;
    }

    .drag-handle:active {
        cursor: grabbing;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('sortable-categories');
    if (!tbody || !tbody.querySelector('tr[data-id]')) {
        return;
    }

    const pageOffset = {{ method_exists($categories, 'firstItem') && $categories->firstItem() ? $categories->firstItem() - 1 : 0 }};

    new Sortable(tbody, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function () {
            const rows = tbody.querySelectorAll('tr[data-id]');
            const orders = [];

            rows.forEach(function (row, index) {
                const sortOrder = pageOffset + index + 1;
                orders.push({
                    id: parseInt(row.dataset.id, 10),
                    sort_order: sortOrder
                });

                const badge = row.querySelector('.sort-order-badge');
                if (badge) {
                    badge.textContent = sortOrder;
                }
            });

            fetch("{{ route('category.reorder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ orders: orders })
            })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Failed to update order');
                }
                return response.json();
            })
            .catch(function () {
                alert('Could not save category order. Please try again.');
                window.location.reload();
            });
        }
    });
});
</script>
@endpush