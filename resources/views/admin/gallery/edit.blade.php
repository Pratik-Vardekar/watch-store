@extends('layouts.admin')

@section('content')

<div class="container">

    <h3>Edit Gallery</h3>

    <form action="{{ route('gallery.update',$gallery->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Title</label>
            <input type="text"
                   name="title"
                   class="form-control"
                   value="{{ $gallery->title }}"
                   required>
        </div>

        <div class="mb-3">
            <label>Subtitle</label>
            <input type="text"
                   name="subtitle"
                   class="form-control"
                   value="{{ $gallery->subtitle }}">
        </div>

        <div class="mb-3">
            <label>Content</label>
            <textarea name="content"
                      class="form-control">{{ $gallery->content }}</textarea>
        </div>

        <div class="mb-3">
            <label>Old Image</label><br>
            <img src="{{ asset('backend/assets/images/gallery/'.$gallery->image) }}"
                 width="120">
        </div>

        <div class="mb-3">
            <label>Change Image</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">

                <option value="1" {{ $gallery->status==1?'selected':'' }}>
                    Active
                </option>

                <option value="0" {{ $gallery->status==0?'selected':'' }}>
                    Inactive
                </option>

            </select>
        </div>

        <button class="btn btn-primary">Update</button>

    </form>

</div>

@endsection