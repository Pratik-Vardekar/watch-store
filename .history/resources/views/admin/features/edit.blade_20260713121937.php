@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h4>Edit Feature</h4>
    <form method="POST" action="{{ route('features.update', $feature->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input 
                type="text" 
                name="title" 
                id="title"
                class="form-control" 
                value="{{ $feature->title }}">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea 
                name="description" 
                id="description"
                class="form-control" 
                rows="5">{{ $feature->description }}</textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            @if($feature->image)
                <br>
                <img src="{{ asset('uploads/features/'.$feature->image) }}" width="100" alt="Feature Image">
            @endif
            <input 
                type="file" 
                name="image" 
                id="image"
                class="form-control mt-2">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select 
                name="status" 
                id="status"
                class="form-control">
                <option value="1" @if($feature->status == 1) selected @endif>Active</option>
                <option value="0" @if($feature->status == 0) selected @endif>Inactive</option>
            </select>
        </div>

        <button class="btn btn-success" type="submit">Update</button>
    </form>
</div>
@endsection