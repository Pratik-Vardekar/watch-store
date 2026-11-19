{{-- resources/views/admin/stores/index.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Stores List</h1>
        <a href="{{ route('admin.stores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Store
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Store Name</th>
                        <th>Phone</th>
                        <th>Timing</th>
                        <th>Primary Image</th>
                        <th>Status</th>
                        <th style="min-width:120px;">Actions</th>
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
                                @php
                                    $images = $store->images ?? [];
                                    $primaryImage = !empty($images) ? $images[0] : null;
                                    // Build URL: /storage/stores/filename
                                    $imageUrl = $primaryImage ? asset('storage/stores/' . $primaryImage) : null;
                                @endphp

                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}"
                                         alt="{{ $store->name }}"
                                         width="80"
                                         height="80"
                                         style="object-fit:cover; border-radius:8px; border:1px solid #e5e7eb;"
                                         onerror="this.style.display='none'; this.parentElement.querySelector('.fallback-image').style.display='flex';">
                                    <div class="fallback-image" style="display:none; width:80px; height:80px; background:#f8f9fa; border-radius:8px; border:1px solid #e5e7eb; align-items:center; justify-content:center;">
                                        <i class="fas fa-image" style="font-size:24px; color:#ccc;"></i>
                                    </div>
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center"
                                         style="width:80px;height:80px; background:#f8f9fa; border-radius:8px; border:1px solid #e5e7eb;">
                                        <i class="fas fa-image" style="font-size:24px; color:#ccc;"></i>
                                        <span style="font-size:11px; color:#999;">No image</span>
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
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.stores.edit', $store->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.stores.destroy', $store->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this store?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.stores.toggle-status', $store->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-{{ $store->is_active ? 'warning' : 'success' }}" title="{{ $store->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $store->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No stores found. Click "Add Store" to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection