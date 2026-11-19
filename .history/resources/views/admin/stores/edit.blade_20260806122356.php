{{-- resources/views/admin/stores/edit.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Store</h1>
        <div>
            <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <form action="{{ route('admin.stores.toggle-status', $store) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-{{ $store->is_active ? 'warning' : 'success' }}">
                    <i class="fas fa-{{ $store->is_active ? 'pause' : 'play' }}"></i>
                    {{ $store->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.stores.update', $store) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name">Store Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $store->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="phone">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $store->phone) }}" required>
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $store->email) }}">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="timing">Store Timing <span class="text-danger">*</span></label>
                            <input type="text" name="timing" id="timing" class="form-control @error('timing') is-invalid @enderror" value="{{ old('timing', $store->timing) }}" required>
                            @error('timing')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $store->address) }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="latitude">Latitude</label>
                            <input type="text" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $store->latitude) }}" placeholder="e.g., 19.281327">
                            @error('latitude')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="longitude">Longitude</label>
                            <input type="text" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $store->longitude) }}" placeholder="e.g., 72.8590166">
                            @error('longitude')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="google_maps_link">Google Maps Link</label>
                            <input type="url" name="google_maps_link" id="google_maps_link" class="form-control @error('google_maps_link') is-invalid @enderror" value="{{ old('google_maps_link', $store->google_maps_link) }}" placeholder="https://www.google.com/maps/dir/...">
                            @error('google_maps_link')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label>Current Images</label>
                            <div class="row">
                                @php $images = $store->images ?? []; @endphp
                                @forelse($images as $index => $imagePath)
                                <div class="col-md-3 mb-3" id="image-{{ $index }}">
                                    <div class="card">
                                        <img src="{{ asset('storage/' . $imagePath) }}" class="card-img-top" alt="Store Image" style="height: 150px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-{{ $index === 0 ? 'primary' : 'secondary' }}">
                                                    {{ $index === 0 ? 'Primary' : 'Image ' . ($index + 1) }}
                                                </span>
                                                <button type="button" class="btn btn-sm btn-danger delete-image" data-store-id="{{ $store->id }}" data-index="{{ $index }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <p class="text-muted">No images uploaded yet.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="images">Add More Images</label>
                            <input type="file" name="images[]" id="images" class="form-control @error('images.*') is-invalid @enderror" multiple accept="image/*">
                            <small class="text-muted">You can select multiple images to add.</small>
                            @error('images.*')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ $store->is_active ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Store
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.delete-image').on('click', function() {
        if (confirm('Are you sure you want to delete this image?')) {
            let storeId = $(this).data('store-id');
            let imageIndex = $(this).data('index');
            let element = $('#image-' + imageIndex);
            
            $.ajax({
                url: '/admin/stores/' + storeId + '/image',
                type: 'DELETE',
                data: {
                    image_index: imageIndex,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        element.fadeOut('slow', function() {
                            $(this).remove();
                        });
                        alert('Image deleted successfully');
                    }
                },
                error: function() {
                    alert('Failed to delete image');
                }
            });
        }
    });
});
</script>
@endpush
@endsection