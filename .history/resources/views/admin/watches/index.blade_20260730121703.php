@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Watches Banner</h4>
            <p class="text-muted mb-0 mt-1">Update For Him / For Her images on the homepage</p>
        </div>

        <div class="card-body">

            <form action="{{ route('watches.update') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                @foreach($watches as $watch)

                    <div class="card shadow-sm border mb-4">

                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                {{ $watch->name }}
                            </h5>

                            <span class="badge bg-primary fs-6">
                                {{ $watch->recommended_size ?? 'Size Not Set' }}
                            </span>
                        </div>

                        <div class="card-body">

                            <input type="hidden"
                                   name="watch_ids[]"
                                   value="{{ $watch->id }}">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Name</label>
                                    <input type="text"
                                           name="name[{{ $watch->id }}]"
                                           class="form-control"
                                           value="{{ old('name.'.$watch->id, $watch->name) }}"
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Slug</label>
                                    <input type="text"
                                           name="slug[{{ $watch->id }}]"
                                           class="form-control"
                                           value="{{ old('slug.'.$watch->id, $watch->slug) }}"
                                           placeholder="for-him / for-her">
                                    <small class="text-muted">Use <code>for-him</code> or <code>for-her</code> to link Men/Women listing.</small>
                                </div>
                            </div>

                            <div class="row align-items-center">

                                <div class="col-md-6">

                                    <label class="form-label fw-bold mb-2">
                                        Current Image
                                    </label>

                                    <div class="border rounded bg-light p-3 text-center">

                                        <img
                                            id="preview-{{ $watch->id }}"
                                            src="{{ $watch->image ? asset($watch->image) : asset('backend/assets/images/no-image.png') }}"
                                            class="img-fluid rounded shadow-sm"
                                            style="max-height:300px; object-fit:contain;">

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label fw-bold mb-2">
                                        Upload New Image
                                    </label>

                                    <input
                                        type="file"
                                        class="form-control banner-input"
                                        name="image[{{ $watch->id }}]"
                                        data-preview="preview-{{ $watch->id }}"
                                        accept="image/*">

                                    <small class="text-muted mt-2 d-block">
                                        Recommended Size:
                                        <strong>
                                            {{ $watch->recommended_size ?? 'Not Set' }}
                                        </strong>
                                    </small>

                                    <div class="mt-3">
                                        <span class="badge bg-success">
                                            Live Preview Enabled
                                        </span>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="ti ti-device-floppy me-1"></i>
                        Save All Changes
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.banner-input').forEach(input => {

        input.addEventListener('change', function () {

            const file = this.files[0];

            if (!file) return;

            const preview = document.getElementById(
                this.dataset.preview
            );

            preview.src = URL.createObjectURL(file);

        });

    });

});
</script>
@endpush
