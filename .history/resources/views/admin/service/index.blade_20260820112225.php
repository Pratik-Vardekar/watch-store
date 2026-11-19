@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Services</h4>
            <p class="text-muted mb-0">Drag rows to change display order</p>
        </div>
        <a href="{{ route('services.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Service
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
                            <th style="width:40px"></th>
                            <th style="width:50px">#</th>
                            <th style="width:100px">Image</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th style="width:110px">Status</th>
                            <th style="width:140px" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-services">
                        @forelse($services as $index => $service)
                            <tr data-id="{{ $service->id }}">
                                <td class="text-center drag-handle" style="cursor: grab;">
                                    <i class="ti ti-grip-vertical text-muted"></i>
                                </td>
                                <td class="row-number">{{ $index + 1 }}</td>
                                <td>
                                    <img src="{{ $service->first_image_url }}"
                                         alt="{{ $service->name }}"
                                         class="rounded border"
                                         style="width:60px;height:60px;object-fit:cover;">
                                </td>
                                <td>
                                    <h5 class="mb-0 fs-6">{{ $service->name }}</h5>
                                    <small class="text-muted">{{ $service->images->count() }} image(s)</small>
                                </td>
                                <td><code>{{ $service->slug }}</code></td>
                                <td>
                                    @if($service->status)
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('services.edit', $service) }}"
                                       class="btn btn-light btn-icon btn-sm rounded-circle"
                                       title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('services.destroy', $service) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this service?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-light btn-icon btn-sm rounded-circle"
                                                title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No services found. Add your first service.</td>
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('sortable-services');
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

            fetch("{{ route('services.reorder') }}", {
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
                alert('Could not save service order. Please try again.');
                window.location.reload();
            });
        }
    });
});
</script>
@endpush
