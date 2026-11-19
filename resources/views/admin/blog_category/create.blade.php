@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Add Category</h4>
        <p class="text-muted mb-0">Create new blog category</p>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('blog-category.store') }}" method="POST">
                @csrf

                <div class="row">

                    <!-- NAME -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>

                    <!-- SLUG -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control" readonly>
                    </div>

                    <!-- STATUS -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </div>

                <button class="btn btn-primary">
                    Save Category
                </button>

            </form>

        </div>
    </div>

</div>

<script>
document.getElementById('name').addEventListener('keyup', function () {
    let slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-');

    document.getElementById('slug').value = slug;
});
</script>

@endsection