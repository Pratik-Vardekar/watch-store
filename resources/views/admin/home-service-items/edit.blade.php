@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Edit Service Item</h4>
        <p class="text-muted mb-0">Update title, description or image for this service card</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.home-service-items.update', $homeServiceItem) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label class="form-label">Title</label>
                    <input type="text" name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $homeServiceItem->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description', $homeServiceItem->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="mt-2">
                        <img src="{{ $homeServiceItem->image_url }}"
                             alt="{{ $homeServiceItem->title }}"
                             class="rounded border"
                             style="max-height:160px;object-fit:cover;"
                             onerror="this.onerror=null;this.src='{{ $homeServiceItem->default_image_url }}';">
                        @if($homeServiceItem->image_changed_at)
                            <div class="small text-muted mt-1">
                                Last changed: {{ $homeServiceItem->image_changed_at->format('d M Y, h:i A') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $homeServiceItem->sort_order) }}">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ (string) old('status', $homeServiceItem->status ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string) old('status', $homeServiceItem->status ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Item</button>
                    <a href="{{ route('admin.home-service-items.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
