@extends('layouts.admin')

@section('content')

<div class="card">
    <div class="card-header">
        <h4>Edit GST Slab</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('gst.settings.update',$gstSetting->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">GST Percentage</label>
                <input type="number"
                       step="0.01"
                       name="gst_percentage"
                       value="{{ $gstSetting->gst_percentage }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ $gstSetting->status == 1 ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="0" {{ $gstSetting->status == 0 ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Update
            </button>

        </form>

    </div>
</div>

@endsection