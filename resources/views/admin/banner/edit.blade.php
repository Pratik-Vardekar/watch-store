@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="mb-4">

        <h4 class="mb-1">
            Edit Blog
        </h4>

        <p class="text-muted mb-0">
            Update blog details
        </p>

    </div>

    <div class="card">

        <div class="card-body">

            <form action="{{ route('blog.update', $blog->id) }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Title
                        </label>
                        <input type="text"
                            name="title"
                            id="title"
                            value="{{ old('title', $blog->title) }}"
                            class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Slug
                        </label>
                        <input type="text"
                            name="slug"
                            id="slug"
                            value="{{ old('slug', $blog->slug) }}"
                            class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Image
                        </label>
                        <input type="file"
                            name="image"
                            class="form-control"
                            id="imageInput">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">
                            Preview
                        </label>
                        <img id="previewImage"
                            src="{{ asset('backend/assets/images/blog/' . $blog->image) }}"
                            class="rounded border"
                            style="width:140px;height:100px;object-fit:cover;">
                    </div>

                </div>

                <button type="submit"
                    class="btn btn-primary">

                    Update Blog

                </button>

            </form>

        </div>

    </div>

</div>

<script>
    // Auto Slug Generate
    document.getElementById('title').addEventListener('keyup', function () {
        let slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\u0900-\u097F\s]/g, '')
            .replace(/\s+/g, '-');
        document.getElementById('slug').value = slug;
    });

    // Live Image Preview
    document.getElementById('imageInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(event){
                document.getElementById('previewImage').src = event.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection