@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Home FAQs</h4>
            <p class="text-muted mb-0">Manage homepage FAQ questions and the side kid PNG image</p>
        </div>
        <a href="{{ route('admin.home-faqs.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add FAQ
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">FAQ Section Side Image (PNG)</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.home-faqs.image.update') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Upload Image (PNG preferred)</label>
                    <input type="file"
                        name="image"
                        class="form-control @error('image') is-invalid @enderror"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        required>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Shown on the right of “Ask Anything”. Max 5MB.</small>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-upload me-1"></i> Update Image
                    </button>
                </div>

                <div class="col-md-3">
                    @if($section->image)
                        <img src="{{ asset($section->image) }}"
                            alt="FAQ kid"
                            class="rounded border"
                            style="max-height: 120px;">
                    @else
                        <img src="{{ asset('frontend/assets/images/faqKid.png') }}"
                            alt="Default FAQ kid"
                            class="rounded border"
                            style="max-height: 120px;">
                        <small class="text-muted d-block mt-1">Using default image</small>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th width="60">SL</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th width="90">Order</th>
                            <th width="100">Status</th>
                            <th width="160">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $index => $faq)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $faq->question }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($faq->answer, 80) }}</td>
                                <td>{{ $faq->sort_order }}</td>
                                <td>
                                    @if($faq->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.home-faqs.edit', $faq) }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.home-faqs.destroy', $faq) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this FAQ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No FAQs found. Click Add FAQ to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
