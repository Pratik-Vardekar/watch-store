@extends('layouts.admin')

@section('content')

<div class="container-fluid my-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Add Images</h4>
            <p class="text-muted mb-0">Gallery: {{ $gallery->title }}</p>
        </div>

        <a href="{{ route('gallery.images.index', $gallery->id) }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('gallery.images.store', $gallery->id) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-3">
                    <label>Images</label>
                    <input type="file" name="images[]" class="form-control" required multiple>
                    <small class="text-muted">You can select and upload multiple images.</small>
                </div>

                <button class="btn btn-primary">
                    Upload Images
                </button>

            </form>

        </div>
    </div>

</div>

@endsection