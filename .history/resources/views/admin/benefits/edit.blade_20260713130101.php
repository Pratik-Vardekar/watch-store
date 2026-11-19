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
                    <form action="{{ route('benefits.update', $benefit->id) }}" method="POST" enctype="multipart/form-data">
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
                            {{-- SVG text input --}}
                            <input
                                type="text"
                                name="svg_logo"
                                id="svg_logo"
                                class="form-control mb-2"
                                value="{{ old('svg_logo', $benefit->svg_logo) }}"
                                placeholder="Paste SVG code or upload file">
                            <div class="mb-2">
                                <input
                                    type="file"
                                    name="svg_logo_file"
                                    id="svg_logo_file"
                                    class="form-control"
                                    accept=".svg">
                            </div>
                            @if ($benefit->svg_logo)
                                <div class="mb-2">
                                    <label class="form-label">Current SVG Preview:</label>
                                    <div style="width:48px;height:48px;">{!! $benefit->svg_logo !!}</div>
                                </div>
                            @endif
                            <div id="svgFilePreview" class="mt-2"></div>
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

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Optional: JS for live SVG file preview --}}
<script>
document.getElementById('svg_logo_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if(file && file.type === 'image/svg+xml') {
        const reader = new FileReader();
        reader.onload = function(evt) {
            document.getElementById('svgFilePreview').innerHTML = 
                `<div class="mt-2">SVG File Preview:<br><div style="width:48px;height:48px;">${evt.target.result}</div></div>`;
        };
        reader.readAsText(file);
    } else {
        document.getElementById('svgFilePreview').innerHTML = '';
    }
});
</script>
@endsection