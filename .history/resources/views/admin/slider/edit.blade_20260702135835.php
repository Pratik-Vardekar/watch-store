@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="mb-4">

        <h4 class="mb-1">Edit Slider</h4>
        <p class="text-muted mb-2">Update slider details</p>
        <p class="mb-0 text-dark"><strong>Recommended size:</strong> 1920 × 825 px <span class="text-secondary">(aspect ratio 128:55)</span></p>

    </div>

    <div class="card">

        <div class="card-body">

            <form action="{{ route('slider.update', $slider->id) }}"
                method="POST"
                enctype="multipart/form-data"
                class="row g-3">

                @csrf
                @method('PUT')

                <div class="col-md-6">

                    <label class="form-label">Title</label>

                    <input type="text"
                        name="title"
                        class="form-control"
                        value="{{ $slider->title }}"
                        required>

                </div>

                <div class="col-md-6">

                    <label class="form-label">Status</label>

                    <select name="status"
                        class="form-select">

                        <option value="1"
                            {{ $slider->status == 1 ? 'selected' : '' }}>

                            Active

                        </option>

                        <option value="0"
                            {{ $slider->status == 0 ? 'selected' : '' }}>

                            Inactive

                        </option>

                    </select>

                </div>

                <div class="col-12">

                    <label class="form-label">Description</label>

                    <textarea name="description"
                        class="form-control"
                        rows="4">{{ $slider->description }}</textarea>

                </div>

                <div class="col-md-6">

                    <label class="form-label">Link</label>

                    <input type="text"
                        name="link"
                        class="form-control"
                        value="{{ old('link', $slider->link) }}"
                        placeholder="https://example.com">
                </div>

                <div class="col-md-6">

                    <label class="form-label">Change Image</label>

                    <input type="file"
                        name="image"
                        id="image"
                        class="form-control"
                        accept="image/*">

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        Current Image
                    </label>

                    <div>

                        <img id="preview-image"
                            src="{{ asset('backend/assets/images/slider/' . $slider->image) }}"
                            class="rounded border"
                            style="
                                width:120px;
                                height:120px;
                                object-fit:cover;
                            ">

                    </div>

                </div>

                <div class="col-12">

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="ti ti-device-floppy me-1"></i>
                        Update Slider

                    </button>

                    <a href="{{ route('slider.index') }}"
                        class="btn btn-light">

                        Cancel

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

document.getElementById('image').addEventListener('change', function(e) {

    let file = e.target.files[0];

    if(file) {

        let reader = new FileReader();

        reader.onload = function(event) {

            document.getElementById('preview-image').src =
                event.target.result;

        }

        reader.readAsDataURL(file);

    }

});

</script>

@endsection