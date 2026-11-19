@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
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
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $benefit->title }}">
                        </div>
                    </form>
                </div>
                <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control">{{ $benefit->description }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="svg_logo">SVG Logo</label>
                            <input type="text" name="svg_logo" class="form-control" value="{{ $benefit->svg_logo }}">
                        </div>
                    </form>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ $benefit->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $benefit->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Update</button>
            </div>
            
            </div>
        </div>
    </div>
</div>
@endsection