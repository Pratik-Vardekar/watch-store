@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Products</h4>
            <p class="text-muted mb-0">Manage all products</p>
        </div>

        <a href="{{ route('product.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>
            Add Product
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
            <form method="GET" action="{{ route('product.index') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">
                            Search
                        </label>
                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search product..."
                            value="{{ request('search') }}"
                            style="width: 250px;">
                    </div>

                    <!-- Brand Filter -->
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">
                            Brand
                        </label>
                        <select name="brand_id" class="form-select" style="width: 200px;">
                            <option value="">All Brands</option>
                            @foreach($brands ?? [] as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">
                            Gender
                        </label>
                        <select name="gender" class="form-select" style="width: 160px;">
                            <option value="">All Genders</option>
                            @foreach(\App\Models\Product::GENDERS as $value => $label)
                                <option value="{{ $value }}"
                                    {{ request('gender') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">
                            Status
                        </label>
                        <select name="status" class="form-select" style="width: 150px;">
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
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">
                            Created Date
                        </label>
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
                                <i class="ti ti-filter me-1"></i>
                                Filter
                            </button>

                            <a href="{{ route('product.index') }}" class="btn btn-secondary">
                                <i class="ti ti-refresh me-1"></i>
                                Reset
                            </a>

                            <!-- CSV Export Button -->
                            <a href="{{ route('product.export', request()->all()) }}" class="btn btn-success">
                                <i class="ti ti-file-export me-1"></i>
                                CSV
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Active Filters Display -->
            @if(request('search') || request('brand_id') || request('gender') || request('status') !== null || request('date'))
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <span class="text-muted small">Active filters:</span>
                    @if(request('search'))
                        <span class="badge bg-secondary">
                            Search: {{ request('search') }}
                            <a href="{{ route('product.index', array_merge(request()->except('search', 'page'))) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                    @if(request('brand_id'))
                        @php
                            $brandName = ($brands ?? collect())->firstWhere('id', request('brand_id'))->name ?? '';
                        @endphp
                        <span class="badge bg-secondary">
                            Brand: {{ $brandName }}
                            <a href="{{ route('product.index', array_merge(request()->except('brand_id', 'page'))) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                    @if(request('gender'))
                        <span class="badge bg-secondary">
                            Gender: {{ \App\Models\Product::GENDERS[request('gender')] ?? request('gender') }}
                            <a href="{{ route('product.index', array_merge(request()->except('gender', 'page'))) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                    @if(request('status') !== null && request('status') !== '')
                        <span class="badge bg-secondary">
                            Status: {{ request('status') == '1' ? 'Active' : 'Inactive' }}
                            <a href="{{ route('product.index', array_merge(request()->except('status', 'page'))) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                    @if(request('date'))
                        <span class="badge bg-secondary">
                            Date: {{ request('date') }}
                            <a href="{{ route('product.index', array_merge(request()->except('date', 'page'))) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th style="width: 50px">#</th>
                            <th style="width: 300px">Product</th>
                            <th>Brand</th>
                            <th>Gender</th>
                            <th style="width: 140px">Price & Discount</th>
                            <th style="width: 100px">Quantity</th>
                            <th style="width: 100px">Status</th>
                            <th style="width: 110px">Created</th>
                            <th style="width: 180px" class="text-center">Actions</th>
                         </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                            <tr>
                                <td class="align-middle">
                                    @if(method_exists($products, 'firstItem'))
                                        {{ $products->firstItem() + $index }}
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                 </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($product->thumbnail)
                                                <img src="{{ asset('backend/assets/images/product/' . $product->thumbnail) }}"
                                                    class="rounded border"
                                                    style="width:50px; height:50px; object-fit:cover; min-width:50px; display:block;">
                                            @else
                                                <div class="rounded border d-flex align-items-center justify-content-center"
                                                    style="width:50px; height:50px; min-width:50px; background:#f5f5f5; font-size:10px;">
                                                    No Image
                                                </div>
                                            @endif
                                        </div>
                                        <div style="min-width:0;">
                                            <h5 class="mb-1 text-wrap fs-6">
                                                {{ $product->name }}
                                            </h5>
                                            <small class="text-muted">SKU: {{ $product->sku ?? '-' }}</small>
                                        </div>
                                    </div>
                                 </td>
                                <td class="align-middle">
                                    {{ $product->brand->name ?? '-' }}
                                 </td>
                                <td class="align-middle">
                                    @if($product->gender && isset(\App\Models\Product::GENDERS[$product->gender]))
                                        <span class="badge bg-primary-subtle text-primary px-2 py-1">
                                            {{ \App\Models\Product::GENDERS[$product->gender] }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                 </td>
                                <td class="align-middle">
                                    <span class="fw-semibold">₹{{ number_format($product->selling_price, 2) }}</span>
                                    @if($product->price > $product->selling_price)
                                        <small class="text-muted text-decoration-line-through d-block">
                                            ₹{{ number_format($product->price, 2) }}
                                        </small>
                                        @php
                                            $diff = $product->price - $product->selling_price;
                                            $percent = $product->price > 0 ? ($diff / $product->price) * 100 : 0;
                                        @endphp
                                        <span class="badge bg-success-subtle text-success mt-1" style="font-size: 0.75rem;">
                                            -{{ round($percent) }}% OFF
                                        </span>
                                    @endif
                                 </td>
                                <td class="align-middle">
                                    @if($product->quantity <= 5 && $product->quantity > 0)
                                        <span class="badge bg-warning text-dark">Low Stock: {{ $product->quantity }}</span>
                                    @elseif($product->quantity <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->quantity }} in stock</span>
                                    @endif
                                 </td>
                                <td class="align-middle">
                                    @if($product->status == 1)
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
                                    {{ $product->created_at->format('d M, Y') }}
                                 </td>
                                <td class="align-middle">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- STATUS TOGGLE -->
                                        <form action="{{ route('product.status', $product->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-light btn-icon btn-sm rounded-circle"
                                                title="{{ $product->status == 1 ? 'Deactivate' : 'Activate' }}">
                                                @if($product->status == 1)
                                                    <i class="ti ti-toggle-right fs-5 text-success"></i>
                                                @else
                                                    <i class="ti ti-toggle-left fs-5 text-danger"></i>
                                                @endif
                                            </button>
                                        </form>

                                        <!-- DUPLICATE -->
                                        <form action="{{ route('product.duplicate', $product->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-light btn-icon btn-sm rounded-circle"
                                                title="Duplicate Product">
                                                <i class="ti ti-copy fs-5"></i>
                                            </button>
                                        </form>

                                        <!-- EDIT -->
                                        <a href="{{ route('product.edit', ['product' => $product->id, 'page' => request('page')]) }}"
                                           class="btn btn-light btn-icon btn-sm rounded-circle"
                                           title="Edit Product">
                                            <i class="ti ti-edit fs-5"></i>
                                        </a>

                                        <!-- DELETE -->
                                        <form action="{{ route('product.destroy', $product->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this product? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-light btn-icon btn-sm rounded-circle"
                                                title="Delete Product">
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
                                        <i class="ti ti-package fs-1 text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No products found</p>
                                        <a href="{{ route('product.create') }}" class="btn btn-sm btn-primary mt-3">
                                            <i class="ti ti-plus me-1"></i>
                                            Add product
                                        </a>
                                    </div>
                                 </td>
                            </tr>
                        @endforelse
                    </tbody>
                 </table>
            </div>

            <!-- Responsive Pagination with Filter Preservation -->
            @if(method_exists($products, 'hasPages') && $products->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-2 border-top">
                    <div class="text-muted small mb-3 mb-md-0">
                        Showing
                        <span class="fw-semibold">{{ $products->firstItem() }}</span>
                        to
                        <span class="fw-semibold">{{ $products->lastItem() }}</span>
                        of
                        <span class="fw-semibold">{{ $products->total() }}</span>
                        entries
                    </div>

                    <nav aria-label="Product pagination">
                        <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-1">
                            {{-- Previous Page Link --}}
                            @if ($products->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link rounded-3"><i class="ti ti-chevron-left"></i></span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $products->appends(request()->query())->previousPageUrl() }}" rel="prev">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $products->currentPage();
                                $lastPage = $products->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp

                            {{-- First page link --}}
                            @if($start > 1)
                                <li class="page-item d-none d-sm-block">
                                    <a class="page-link rounded-3" href="{{ $products->appends(request()->query())->url(1) }}">1</a>
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
                                        <a class="page-link rounded-3" href="{{ $products->appends(request()->query())->url($i) }}">{{ $i }}</a>
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
                                    <a class="page-link rounded-3" href="{{ $products->appends(request()->query())->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($products->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link rounded-3" href="{{ $products->appends(request()->query())->nextPageUrl() }}" rel="next">
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
                        @if (!$products->onFirstPage())
                            <a href="{{ $products->appends(request()->query())->previousPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-chevron-left"></i> Prev
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                        @endif

                        <span class="btn btn-light btn-sm border disabled">
                            Page {{ $products->currentPage() }} of {{ $products->lastPage() }}
                        </span>

                        @if ($products->hasMorePages())
                            <a href="{{ $products->appends(request()->query())->nextPageUrl() }}" class="btn btn-outline-primary btn-sm">
                                Next <i class="ti ti-chevron-right"></i>
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                Next <i class="ti ti-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @elseif(method_exists($products, 'count') && $products->count() > 0)
                <!-- Simple info when only one page (no pagination needed) -->
                <div class="text-center text-muted small mt-3 pt-2 border-top">
                    Showing {{ $products->count() }} entries
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