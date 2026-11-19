@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Blog Categories</h4>
            <p class="text-muted mb-0">Manage all categories</p>
        </div>

        <a href="{{ route('blog-category.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>
            Add Category
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($categories as $index => $cat)
                        <tr>
                            <td>{{ $categories->firstItem() + $index }}</td>

                            <td>
                                <strong>{{ $cat->name }}</strong>
                            </td>

                            <td>
                                <span class="text-muted">{{ $cat->slug }}</span>
                            </td>

                            <td>
                                <form action="{{ route('blog-category.status', $cat->id) }}" method="POST">
    @csrf
    @method('PATCH')

    <button type="submit"
        class="btn btn-sm {{ $cat->status ? 'btn-success' : 'btn-danger' }}">
        {{ $cat->status ? 'Active' : 'Inactive' }}
    </button>
</form>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('blog-category.edit', $cat->id) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('blog-category.destroy', $cat->id) }}"
                                      method="POST"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this category?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No categories found
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