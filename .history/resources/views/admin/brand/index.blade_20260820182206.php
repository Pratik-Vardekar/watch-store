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
                                <td class="text-center row-number">{{ $index + 1 }}</td>
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
                                    @if($brand->description)
                                        <div class="small text-muted">{{ Str::limit($brand->description, 60) }}</div>
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

    new Sortable(tbody, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function () {
            const rows = tbody.querySelectorAll('tr[data-id]');
            const orders = [];

            rows.forEach(function (row, index) {
                const position = index + 1;
                orders.push({
                    id: parseInt(row.dataset.id, 10),
                    position: position
                });

                const numberCell = row.querySelector('.row-number');
                if (numberCell) {
                    numberCell.textContent = position;
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
