@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Terms and Conditions</h4>
        <p class="text-muted mb-0">Manage content shown on the front-end Terms and Conditions page</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('terms-and-conditions.update') }}" method="POST" id="termsForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Page Content</label>
                    <textarea name="content" id="content" rows="15" class="form-control">{{ old('content', $terms->content) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Terms and Conditions</button>
            </form>
        </div>
    </div>

</div>

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

@endsection
