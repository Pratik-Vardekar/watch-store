{{-- resources/views/admin/return-refunds/index.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Returns & Refunds</h1>
        <a href="{{ route('return-refund.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New
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
                        <th>Title</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th style="min-width:160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnRefunds as $returnRefund)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $returnRefund->title }}</td>
                            <td>{{ $returnRefund->excerpt ?? Str::limit($returnRefund->content, 50) }}</td>
                            <td>
                                @if($returnRefund->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center" role="group">
                                    {{-- Edit Icon --}}
                                    <a href="{{ route('return-refund.edit', $returnRefund->id) }}
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    {{-- Toggle Status (Activate/Deactivate) --}}
                                    <form action="{{ route('admin.return-refund.toggle-status', $returnRefund->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm {{ $returnRefund->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $returnRefund->is_active ? 'Deactivate' : 'Activate' }}">
                                            @if($returnRefund->is_active)
                                                <i class="fa fa-toggle-off"></i>
                                            @else
                                                <i class="fa fa-toggle-on"></i>
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Delete Icon --}}
                                    <form action="{{ route('return-refund.destroy', $returnRefund->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No records found. Click "Add New" to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection