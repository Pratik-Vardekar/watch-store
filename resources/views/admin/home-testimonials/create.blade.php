@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Add Testimonial</h4>
        <p class="text-muted mb-0">Create a new homepage review for "What Parents Say"</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.home-testimonials.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Reviewer Name</label>
                    <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Rating (1 to 5)</label>
                    <select name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                        @foreach([5, 4.5, 4, 3.5, 3, 2.5, 2, 1.5, 1] as $value)
                            <option value="{{ $value }}" {{ (string) old('rating', '4.5') === (string) $value ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('rating')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Review Text</label>
                    <textarea name="review" rows="5"
                        class="form-control @error('review') is-invalid @enderror"
                        required>{{ old('review') }}</textarea>
                    @error('review')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', 0) }}">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save Testimonial</button>
                    <a href="{{ route('admin.home-testimonials.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
