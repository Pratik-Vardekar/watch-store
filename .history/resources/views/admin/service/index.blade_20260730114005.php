@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Services</h4>
            <p class="text-muted mb-0">Manage all services</p>
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
                            <th style="width:50px">#</th>
                            <th style="width:100px">Image</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th style="width:90px">Order</th>
                            <th style="width:110px">Status</th>
                            <th style="width:140px" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $index => $service)
                            <tr>
                                <td>{{ $services->firstItem() + $index }}</td>
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
                                <td>{{ $service->sort_order }}</td>
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
                                        <i class="ti ti-edit fs-5"></i>
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
                                            <i class="ti ti-trash fs-5"></i>
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

            <div class="mt-3">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
