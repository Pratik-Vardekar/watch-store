@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Edit Homepage Card</h4>
        <p class="text-muted mb-0">Update the “More Than Just Watches” card</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.home-highlights.update', $homeHighlight) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label class="form-label">Card Title</label>
                    <input type="text" name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $homeHighlight->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Card Image</label>
                    <input type="file" name="icon" class="form-control @error('icon') is-invalid @enderror" accept="image/*">
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($homeHighlight->image_url)
                        <div class="mt-2">
                            <img src="{{ $homeHighlight->image_url }}"
                                 alt="{{ $homeHighlight->title }}"
                                 class="rounded border"
                                 style="max-height:160px;object-fit:cover;"
                                 onerror="this.onerror=null;this.src='{{ $homeHighlight->fallback_image_url }}';">
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $homeHighlight->sort_order) }}">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ (string) old('status', $homeHighlight->status ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string) old('status', $homeHighlight->status ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Card</button>
                    <a href="{{ route('admin.home-highlights.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
