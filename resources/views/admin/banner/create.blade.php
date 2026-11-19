@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="mb-4">

        <h4 class="mb-1">
            Add Blog
        </h4>

        <p class="text-muted mb-0">
            Create new blog
        </p>

    </div>

    <div class="card">

        <div class="card-body">

            <form action="{{ route('blog.store') }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Title
                        </label>

                        <input type="text"
                            name="title"
                            id="title"
                            class="form-control"
                            placeholder="Enter Blog Title">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Slug
                        </label>

                        <input type="text"
                            name="slug"
                            id="slug"
                            class="form-control"
                            placeholder="Auto Generated">

                    </div>

                    

                    {{-- <div class="col-md-4 mb-3">
    <label class="form-label">Category</label>

    <select name="category_id" class="form-select" required>
        <option value="">Select Category</option>

        @foreach($categories as $cat)
            <option value="{{ $cat->id }}">
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</div> --}}

                 
{{-- 
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Author
                        </label>

                        <input type="text"
                            name="author"
                            class="form-control">

                    </div> --}}

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Blog Date
                        </label>

                        <input type="date"
                            name="blog_date"
                            class="form-control">

                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Trending
                        </label>
                        <select name="trending" class="form-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

             <div class="col-md-12 mb-3">

    <label class="form-label">
        Image
    </label>

    <input type="file"
        name="image"
        class="form-control"
        id="imageInput"
        accept="image/*">

</div>

<div class="col-md-12 mb-3">

    <img id="previewImage"
        class="rounded border d-none"
        style="width:300px;height:180px;object-fit:cover;">

</div>

                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Short Description
                        </label>

                        <textarea name="short_description"
                            id="short_description"
                            rows="4"
                            class="form-control"></textarea>

                    </div>

                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description"
                            id="description"
                            rows="8"
                            class="form-control"></textarea>
                    </div>

                    {{-- <div class="col-md-12 mb-3">
                        <label class="form-label">
                            Tags
                        </label>
                        <input type="text" name="tags" id="tags" class="form-control">
                    </div> --}}
                    <script>
                        document.getElementById('tags').addEventListener('input', function() {
                            const tags = this.value.split(',').map(tag => tag.trim());
                            document.getElementById('tags-list').innerHTML = tags.map(tag => `<span class="badge bg-primary">${tag}</span>`).join('');
                        });
                    </script>

                    {{-- <div id="tags-list" class="mt-2">
                        <span class="badge bg-primary">Tag 1</span>
                        <span class="badge bg-primary">Tag 2</span>
                        <span class="badge bg-primary">Tag 3</span>
                    </div> --}}

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status"
                            class="form-select">

                            <option value="1">
                                Active
                            </option>

                            <option value="0">
                                Inactive
                            </option>

                        </select>

                    </div>

                </div>

                <button type="submit"
                    class="btn btn-primary">

                    Save Blog

                </button>

            </form>

        </div>

    </div>

</div>
{{-- CKEditor CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>

    document.getElementById('imageInput').addEventListener('change', function(e) {

        const file = e.target.files[0];

        if(file) {

            const reader = new FileReader();

            reader.onload = function(event) {

                let preview = document.getElementById('previewImage');

                preview.src = event.target.result;

                preview.classList.remove('d-none');

            }

            reader.readAsDataURL(file);

        }

    });

</script>
<script>

    // Auto Slug Generate

    document.getElementById('title').addEventListener('keyup', function () {

        let slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\u0900-\u097F\s]/g, '')
            .replace(/\s+/g, '-');

        document.getElementById('slug').value = slug;

    });

    // CKEditor for Short Description

    ClassicEditor
        .create(document.querySelector('#short_description'))
        .catch(error => {
            console.error(error);
        });

    // CKEditor for Description

    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });

</script>






@endsection