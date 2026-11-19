@extends('layouts.admin')

@section('content')
@php
    $reviewBody = trim((string) $review->review);
    $reviewLines = preg_split("/\R+/", $reviewBody) ?: [];
    $heading = count($reviewLines) > 1 ? $reviewLines[0] : '';
    $body = count($reviewLines) > 1 ? trim(implode("\n", array_slice($reviewLines, 1))) : $reviewBody;
@endphp
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Edit Review</h4>
        <p class="text-muted mb-0">Update product review details</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.review.update', $review) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                        <option value="">Select product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ (string) old('product_id', $review->product_id) === (string) $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">User / Reviewer <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">Select user</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (string) old('user_id', $review->user_id) === (string) $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Rating <span class="text-danger">*</span></label>
                    <select name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                        @foreach([5, 4, 3, 2, 1] as $star)
                            <option value="{{ $star }}" {{ (string) old('rating', $review->rating) === (string) $star ? 'selected' : '' }}>
                                {{ $star }} Star{{ $star > 1 ? 's' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('rating')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="1" {{ (string) old('status', $review->status) === '1' ? 'selected' : '' }}>Approved</option>
                        <option value="0" {{ (string) old('status', $review->status) === '0' ? 'selected' : '' }}>Pending</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Review Image</label>
                    <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                    @if($review->image || !empty($review->image_urls))
                        <div class="mt-2">
                            <img src="{{ $review->image_urls[0] ?? asset('backend/assets/images/review/' . $review->image) }}"
                                alt="Review image"
                                class="rounded border"
                                style="width:64px;height:64px;object-fit:cover;">
                        </div>
                    @endif
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Review Heading</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $heading) }}" maxlength="255">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Review Text <span class="text-danger">*</span></label>
                    <textarea name="review" rows="5" class="form-control @error('review') is-invalid @enderror"
                        required>{{ old('review', $body) }}</textarea>
                    @error('review')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Review</button>
                    <a href="{{ route('admin.review.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
