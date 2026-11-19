@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Home FAQs</h4>
        <p class="text-muted mb-0">Edit homepage FAQ questions and the side image</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">FAQ Section Side Image</h5>
                <small class="text-muted">Shown on the right of “Ask Anything / All You Need to Know”</small>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ url('/admin/home-faqs/image') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Upload Image</label>
                    <input type="file"
                        name="image"
                        class="form-control @error('image') is-invalid @enderror"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        required>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">PNG / JPG / WEBP. Max 5MB.</small>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-upload me-1"></i> Update Image
                    </button>
                </div>

                <div class="col-md-3">
                    <img src="{{ $section->imageUrl() }}"
                        alt="FAQ kid"
                        class="rounded border"
                        style="max-height: 120px;">
                    @unless($section->image)
                        <small class="text-muted d-block mt-1">Using default image</small>
                    @endunless
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">FAQ Questions</h5>
                <small class="text-muted">{{ $faqs->count() }} FAQ{{ $faqs->count() === 1 ? '' : 's' }}</small>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60">SL</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th width="90">Order</th>
                            <th width="100">Status</th>
                            <th width="120">Action</th>
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No FAQs found. Run migrations to seed defaults.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
