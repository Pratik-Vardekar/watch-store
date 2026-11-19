@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">
            Add Gallery
        </h4>
        <p class="text-muted mb-0">
            Create new gallery
        </p>
    </div>

    <div class="card">

        <div class="card-body">

            <form action="{{ route('gallery.store') }}"
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
                               placeholder="Enter Gallery Title"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Subtitle
                        </label>
                        <input type="text"
                               name="subtitle"
                               id="subtitle"
                               class="form-control"
                               placeholder="Enter Gallery Subtitle">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">
                            Content
                        </label>
                        <textarea name="content"
                                  id="content"
                                  rows="6"
                                  class="form-control"
                                  placeholder="Enter gallery description"></textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">
                            Image
                        </label>
                        <input type="file"
                               name="image"
                               class="form-control"
                               id="imageInput"
                               accept="image/*"
                               required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <img id="previewImage"
                             class="rounded border d-none"
                             style="width:300px;height:180px;object-fit:cover;">
                    </div>

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
                    Save Gallery
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
    // CKEditor for Content
    ClassicEditor
        .add(document.querySelector('#content'))
        .catch(error => {
            console.error(error);
        });
</script>

@endsection