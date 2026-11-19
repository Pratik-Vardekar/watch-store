@extends('layouts.admin')

@section('content')

<div class="card">
    <div class="card-header">
        <h4>Add GST Slab</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('gst.settings.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">GST Percentage</label>
                <input type="number"
                       step="0.01"
                       name="gst_percentage"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Save
            </button>

            <a href="{{ route('gst.settings.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>
</div>

@endsection