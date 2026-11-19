{{-- resources/views/admin/stores/index.blade.php --}}

@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Stores</h1>
        <a href="{{ route('admin.stores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Store
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Timing</th>
                            <th>Images</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stores as $index => $store)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $store->name }}</td>
                            <td>{{ $store->phone }}</td>
                            <td>{{ $store->timing }}</td>
                            <td>{{ count($store->images ?? []) }}</td>
                            <td>
                                <span class="badge bg-{{ $store->is_active ? 'success' : 'danger' }}">
                                    {{ $store->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('stores.edit', $store) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.stores.destroy', $store) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.stores.toggle-status', $store) }}" class="btn btn-sm btn-{{ $store->is_active ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $store->is_active ? 'pause' : 'play' }}"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection