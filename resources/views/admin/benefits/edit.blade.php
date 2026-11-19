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
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $benefit->title) }}"
                                required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea
                                name="description"
                                id="description"
                                class="form-control @error('description') is-invalid @enderror"
                                rows="4"
                                required>{{ old('description', $benefit->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="svg_logo" class="form-label">SVG Logo</label>
                            <textarea
                                name="svg_logo"
                                id="svg_logo"
                                class="form-control mb-2 @error('svg_logo') is-invalid @enderror"
                                rows="6"
                                placeholder="Paste full SVG code here (e.g. &lt;svg ...&gt;...&lt;/svg&gt;)">{{ old('svg_logo', $benefit->svg_logo) }}</textarea>
                            @error('svg_logo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <label for="svg_logo_file" class="form-label">Or upload SVG file</label>
                            <input
                                type="file"
                                name="svg_logo_file"
                                id="svg_logo_file"
                                class="form-control @error('svg_logo_file') is-invalid @enderror"
                                accept=".svg,image/svg+xml">
                            @error('svg_logo_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($benefit->svg_logo)
                                <div class="mb-2 mt-3">
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
                                <option value="1" {{ (string) old('status', $benefit->status) === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ (string) old('status', $benefit->status) === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('benefits.index') }}" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('svg_logo_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('svgFilePreview');
    const textarea = document.getElementById('svg_logo');

    if (!file) {
        preview.innerHTML = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(evt) {
        const svgContent = evt.target.result;
        // Fill textarea so SVG is submitted even if file field is ignored
        textarea.value = svgContent;
        preview.innerHTML =
            '<div class="mt-2">SVG File Preview:<br><div style="width:48px;height:48px;">' + svgContent + '</div></div>';
    };
    reader.readAsText(file);
});
</script>
@endsection
