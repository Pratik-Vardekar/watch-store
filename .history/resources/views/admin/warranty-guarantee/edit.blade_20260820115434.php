@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Warranty & Guarantee Page</h1>

        <div>
            <a href="{{ route('warranty-guarantee.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>

            <form
            action="{{ route('admin.warranty-guarantee.toggle-status', ['warrantyGuarantee' => $warranty_guarantee->id]) }}"            >
                @csrf
                @method('PUT')

                <button
                    type="submit"
                    class="btn btn-{{ $warranty_guarantee->is_active ? 'warning' : 'success' }}"
                >
                    <i class="fas fa-{{ $warranty_guarantee->is_active ? 'pause' : 'play' }}"></i>
                    {{ $warranty_guarantee->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <form
                action="{{ route('warranty-guarantee.update', ['warranty_guarantee' => $warranty_guarantee->id]) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="title">
                                Title <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="title"
                                id="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $warranty_guarantee->title) }}"
                                required
                            >

                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="content">
                                Content <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="content"
                                id="content"
                                class="form-control @error('content') is-invalid @enderror"
                                rows="10"
                                required
                            >{{ old('content', $warranty_guarantee->content) }}</textarea>

                            @error('content')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="meta_title">Meta Title</label>

                            <input
                                type="text"
                                name="meta_title"
                                id="meta_title"
                                class="form-control @error('meta_title') is-invalid @enderror"
                                value="{{ old('meta_title', $warranty_guarantee->meta_title) }}"
                            >

                            @error('meta_title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="meta_description">Meta Description</label>

                            <textarea
                                name="meta_description"
                                id="meta_description"
                                class="form-control @error('meta_description') is-invalid @enderror"
                                rows="2"
                            >{{ old('meta_description', $warranty_guarantee->meta_description) }}</textarea>

                            @error('meta_description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="meta_keywords">Meta Keywords</label>

                            <input
                                type="text"
                                name="meta_keywords"
                                id="meta_keywords"
                                class="form-control @error('meta_keywords') is-invalid @enderror"
                                value="{{ old('meta_keywords', $warranty_guarantee->meta_keywords) }}"
                                placeholder="keyword1, keyword2, keyword3"
                            >

                            @error('meta_keywords')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mt-2 mb-3">

                            <input
                                type="checkbox"
                                name="is_active"
                                id="is_active"
                                class="form-check-input"
                                value="1"
                                {{ $warranty_guarantee->is_active ? 'checked' : '' }}
                            >

                            <label for="is_active" class="form-check-label">
                                Active
                            </label>

                        </div>
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('warranty-guarantee.index') }}"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@push('scripts')

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    let termsEditor;
    ClassicEditor.create(document.querySelector('#content'))
        .then(editor => { termsEditor = editor; })
        .catch(console.error);

    document.getElementById('termsForm').addEventListener('submit', function () {
        if (termsEditor) {
            document.querySelector('#content').value = termsEditor.getData();
        }
    });
</script>

@endpush

@endsection