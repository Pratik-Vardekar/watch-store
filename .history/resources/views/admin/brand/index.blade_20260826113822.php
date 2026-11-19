@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Brands</h4>
            <p class="text-muted mb-0">Manage brand logos and display order</p>
        </div>

        <a href="{{ route('brands.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>
            Add Brand
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
            <form method="GET" action="{{ route('brands.index') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search brand..."
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
                            <a href="{{ route('brands.index') }}" class="btn btn-secondary">
                                <i class="ti ti-refresh me-1"></i>Reset
                            </a>
                            <a href="{{ route('brands.export', request()->all()) }}" class="btn btn-success">
                                <i class="ti ti-file-export me-1"></i>CSV
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="alert alert-light border mb-3 py-2">
                <i class="ti ti-grip-vertical me-1"></i>
                Drag rows to change brand display order.
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-hover w-100 mb-0 align-middle">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th class="text-center" style="width: 40px"></th>
                            <th class="text-center" style="width: 60px">#</th>
                            <th class="text-center" style="width: 100px">Logo</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="text-center" style="width: 120px">Status</th>
                            <th class="text-center" style="width: 140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-brands">
                        @forelse($brands as $index => $brand)
                            <tr data-id="{{ $brand->id }}">
                                <td class="text-center drag-handle" style="cursor: grab;">
                                    <i class="ti ti-grip-vertical text-muted"></i>
                                </td>
                                <td class="text-center row-number">
                                    @if(method_exists($brands, 'firstItem'))
                                        {{ $brands->firstItem() + $index }}
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div style="width: 60px; height: 60px; margin: 0 auto;">
                                        <img src="{{ $brand->logo_url }}"
                                            class="rounded border"
                                            alt="{{ $brand->name }}"
                                            style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                </td>
                                <td>
                                    <h5 class="mb-0 fs-6 fw-semibold">{{ $brand->name }}</h5>
                                    @if($brand->link)
                                        <small class="text-muted">{{ Str::limit($brand->link, 40) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($brand->description))
                                        <div class="small text-muted" title="{{ $brand->description }}">
                                            {{ Str::limit($brand->description, 60) }}
                                        </div>
                                    @else
                                        <div class="small text-body-secondary text-opacity-75">—</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($brand->status)
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
                                        <a href="{{ route('brands.edit', $brand->id) }}"
                                           class="btn btn-light btn-icon btn-sm rounded-circle"
                                           title="Edit">
                                            <i class="ti ti-pencil fs-5"></i>
                                        </a>
                                        <form action="{{ route('brands.destroy', $brand->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-light btn-icon btn-sm rounded-circle text-danger"
                                                title="Delete"
                                                onclick="return confirm('Delete this brand?')">
                                                <i class="ti ti-trash fs-5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No Brands Found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($brands, 'links') && $brands->lastPage() > 1)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <span class="text-muted small">
                            Showing
                            {{ $brands->firstItem() ?? 0 }}
                            -
                            {{ $brands->lastItem() ?? 0 }}
                            of
                            {{ $brands->total() }}
                            Brands
                        </span>
                    </div>
                    <div>
                        {{-- Use Laravel's built-in pagination links if available --}}
                        {{ $brands->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            @elseif($brands->total() > 0)
                <div class="text-center text-muted small mt-3 pt-2 border-top">
                    Showing {{ $brands->total() }} entries
                </div>
            @endif

        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    .sortable-ghost {
        opacity: 0.4;
        background: #f1f5f9;
    }

    .drag-handle:active {
        cursor: grabbing;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('sortable-brands');
    if (!tbody || !tbody.querySelector('tr[data-id]')) {
        return;
    }

    const pageOffset = {{ method_exists($brands, 'firstItem') && $brands->firstItem() ? $brands->firstItem() - 1 : 0 }};

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

                const numberCell = row.querySelector('.row-number');
                if (numberCell) {
                    numberCell.textContent = sortOrder;
                }
            });

            fetch("{{ route('brands.reorder') }}", {
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
                alert('Could not save brand order. Please try again.');
                window.location.reload();
            });
        }
    });
});
</script>
@endpush