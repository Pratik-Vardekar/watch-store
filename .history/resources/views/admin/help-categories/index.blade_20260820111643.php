@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Help Categories</h4>
            <p class="text-muted mb-0">Manage Help Center accordion sections (Orders, Shipping, etc.)</p>
        </div>
        <a href="{{ route('admin.help-categories.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Category
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
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th width="60">SL</th>
                            <th>Title</th>
                            <th width="120">Color</th>
                            <th width="90">FAQs</th>
                            <th width="90">Order</th>
                            <th width="100">Status</th>
                            <th width="160">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $category)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $category->title }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($category->color) }}</span>
                                </td>
                                <td>{{ $category->faqs_count }}</td>
                                <td>{{ $category->sort_order }}</td>
                                <td>
                                    @if($category->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.help-categories.edit', $category) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                
                                        {{-- Toggle Status --}}
                                        <form action="{{ route('admin.help-categories.toggle-status', $category) }}"
                                              method="POST"
                                              style="display:inline-block;">
                                            @csrf
                                            @method('PUT')
                                
                                            <button type="submit"
                                                    class="btn btn-sm {{ $category->status ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $category->status ? 'Deactivate' : 'Activate' }}">
                                                <i class="fa {{ $category->status ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                            </button>
                                        </form>
                                
                                        {{-- Delete --}}
                                        <form action="{{ route('admin.help-categories.destroy', $category) }}"
                                              method="POST"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('Delete this category and its FAQs?');">
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
                                <td colspan="7" class="text-center">No categories found. Click Add Category to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
