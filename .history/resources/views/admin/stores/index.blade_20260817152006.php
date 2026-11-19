{{-- resources/views/admin/stores/index.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Stores List</h1>
        <a href="{{ route('stores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Store
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Store Name</th>
                        <th>Phone</th>
                        <th>Timing</th>
                        <th>Primary Image</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($stores as $store)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $store->name }}</td>
                        <td>{{ $store->phone }}</td>
                        <td>{{ $store->timing }}</td>
                        <td>
                            @if($store->primary_image_url)
                                <img src="{{ $store->primary_image_url }}"
                                     alt="{{ $store->name }}"
                                     width="80"
                                     height="80"
                                     class="rounded border"
                                     style="object-fit:cover;"
                                     onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/no-image.png') }}';">
                            @else
                                <div class="d-flex align-items-center justify-content-center border rounded"
                                     style="width:80px;height:80px;background:#f8f9fa;">
                                    <span class="text-muted" style="font-size:12px;">No Image</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($store->is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i> Active
                                </span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                {{-- Edit Icon --}}
                                <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                {{-- Delete Icon --}}
                                <form action="{{ route('stores.destroy', $store->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this store?')" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                {{-- Deactivate/Activate Icon (Show eye for active (will deactivate), show eye-slash for inactive (will activate)) --}}
                                <form action="{{ route('admin.stores.toggle-status', $store->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button 
                                        class="btn btn-sm btn-outline-{{ $store->is_active ? 'warning' : 'success' }}" 
                                        title="{{ $store->is_active ? 'Deactivate' : 'Activate' }}"
                                    >
                                        @if($store->is_active)
                                            <i class="fas fa-eye-slash"></i>
                                        @else
                                            <i class="fas fa-eye"></i>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-store fa-3x d-block mb-2"></i>
                            No Stores Found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection