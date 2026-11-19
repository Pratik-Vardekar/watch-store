@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Benefit</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('benefits.update', $benefit->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input 
                                type="text" 
                                name="title"
                                id="title"
                                class="form-control"
                                value="{{ old('title', $benefit->title) }}"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea
                                name="description"
                                id="description"
                                class="form-control"
                                rows="4"
                                required>{{ old('description', $benefit->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="svg_logo" class="form-label">SVG Logo</label>
                            <input
                                type="text"
                                name="svg_logo"
                                id="svg_logo"
                                class="form-control"
                                value="{{ old('svg_logo', $benefit->svg_logo) }}">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select
                                name="status"
                                id="status"
                                class="form-select">
                                <option value="1" {{ old('status', $benefit->status) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $benefit->status) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection