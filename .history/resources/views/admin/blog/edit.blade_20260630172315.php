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

                   

                       {{-- <div class="col-md-4 mb-3">
    <label class="form-label">Category</label>

    <select name="category_id" class="form-select">
        <option value="">Select Category</option>

        @foreach($categories as $cat)
            <option value="{{ $cat->id }}"
                {{ old('category_id', $blog->category_id) == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</div> --}}

                    {{-- <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Author
                        </label>

                        <input type="text"
                            name="author"
                            value="{{ old('author', $blog->author) }}"
                            class="form-control">

                    </div> --}}

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Blog Date
                        </label>

                        <input type="date"
                            name="blog_date"
                            value="{{ old('blog_date', $blog->blog_date) }}"
                            class="form-control">

                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Trending</label>
                    
                        <select name="trending" class="form-select">
                            <option value="0" {{ $blog->trending == 0 ? 'selected' : '' }}>
                                No
                            </option>
                            <option value="1" {{ $blog->trending == 1 ? 'selected' : '' }}>
                                Yes
                            </option>
                        </select>
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

                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Short Description
                        </label>

                        <textarea name="short_description"
                            id="short_description"
                            rows="4"
                            class="form-control">{!! old('short_description', $blog->short_description) !!}</textarea>

                    </div>

                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description"
                            id="description"
                            rows="8"
                            class="form-control">{!! old('description', $blog->description) !!}</textarea>

                    </div>

                    {{-- <div class="col-md-12 mb-3">
                        <label class="form-label">
                            Tags
                        </label>
                        <input type="text" name="tags" id="tags" class="form-control" value="{{ old('tags', $blog->tags) }}">
                    </div> --}}
                    <script>
                        document.getElementById('tags').addEventListener('input', function() {
                            const tags = this.value.split(',').map(tag => tag.trim());
                            document.getElementById('tags-list').innerHTML = tags.map(tag => `<span class="badge bg-primary">${tag}</span>`).join('');
                        });
                    </script>

                        <div id="tags-list" class="mt-2">
                            @if(!empty($blog->tags))
                                @foreach(explode(',', $blog->tags) as $tag)
                                    <span class="badge bg-primary">{{ $tag }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Status
                        </label>

                        <select name="status"
                            class="form-select">

                            <option value="1"
                                {{ $blog->status == 1 ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ $blog->status == 0 ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

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

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>

    ClassicEditor
        .create(document.querySelector('#short_description'))
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });

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