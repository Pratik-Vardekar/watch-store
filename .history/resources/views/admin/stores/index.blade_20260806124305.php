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

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                        <th>Email</th>
                        <th>Timing</th>
                        <th>Address</th>
                        <th>Primary Image</th>
                        <th>Status</th>
                        <th style="min-width:80px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $store)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $store->name }}</td>
                            <td>{{ $store->phone }}</td>
                            <td>{{ $store->email }}</td>
                            <td>{{ $store->timing }}</td>
                            <td>{{ Str::limit($store->address, 30) }}</td>
                            <td>
                                @if(isset($store->images[0]) && !empty($store->images[0]))
                                    <img src="{{ asset('storage/' . $store->images[0]) }}" alt="Store Image" width="60" height="60" style="object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                @else
                                    <span class="text-muted d-flex flex-column align-items-center justify-content-center" style="min-width:60px;min-height:60px;">
                                        <i class="ti ti-photo" style="font-size:28px;opacity:.5;"></i>
                                        <span style="font-size:12px;">No image</span>
                                    </span>
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
                            <td colspan="9" class="text-center">No stores found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection