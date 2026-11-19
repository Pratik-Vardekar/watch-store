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
                        <th>Image</th>
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
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                {{-- Edit --}}
                                <a href="{{ route('stores.edit', $store->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>

                                {{-- Toggle Status --}}
                                <form action="{{ route('admin.stores.toggle-status', $store->id) }}"
                                      method="POST"
                                      style="display:inline-block;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="btn btn-sm {{ $returnRefund->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            title="{{ $returnRefund->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fa {{ $returnRefund->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form action="{{ route('stores.destroy', $store->id) }}"
                                      method="POST"
                                      style="display:inline-block;"
                                      onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete">
                                        <i class="fa fa-trash"></i>
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