@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Gallery Images</h4>
            <p class="text-muted mb-0">Manage all images of {{ $gallery->title }}</p>
        </div>

        <a href="{{ route('gallery.images.create', $gallery->id) }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>
            Add Image
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            @if($images->count() > 0)

                <div class="row g-3">

                    @foreach($images as $image)

                        <div class="col-md-3 col-sm-4 col-6">

                            <div class="card h-100">

                                {{-- IMAGE --}}
                                @php
                                    // According to your instruction, image files are located at:
                                    // C:\xampp\htdocs\Cheriks\public\backend\assets\images\gallery
                                    // So the asset URL should be: backend/assets/images/gallery/{image}
                                    $imgPath = $image->image
                                        ? asset('backend/assets/images/gallery/' . $image->image)
                                        : null;
                                @endphp

                                @if($imgPath && file_exists(public_path('backend/assets/images/gallery/' . $image->image)))
                                    <img src="{{ $imgPath }}"
                                         class="card-img-top"
                                         style="height:180px; object-fit:cover;"
                                         alt="{{ $image->title ?? 'Image' }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center"
                                         style="height:180px; background:#f5f5f5;">
                                        <i class="ti ti-photo fs-1 text-muted"></i>
                                    </div>
                                @endif

                                {{-- CARD BODY --}}
                                <div class="card-body p-2">

                                    <h6 class="mb-2">
                                        {{ $image->title ?? 'No Title' }}
                                    </h6>

                                    <div class="d-flex justify-content-end">

                                        <form action="{{ route('gallery.images.destroy', [$gallery->id, $image->id]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this image?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-light btn-icon">
                                                <i class="ti ti-trash text-danger"></i>
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $images->links() }}
                </div>

            @else

                <div class="text-center py-5">
                    <i class="ti ti-photo fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No images found</p>

                    <a href="{{ route('gallery.images.create', $gallery->id) }}"
                       class="btn btn-primary btn-sm mt-2">
                        Add First Image
                    </a>
                </div>

            @endif

        </div>
    </div>

</div>

@endsection