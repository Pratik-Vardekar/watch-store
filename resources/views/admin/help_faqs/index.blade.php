@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Home FAQs</h4>
            <p class="text-muted mb-0">Manage frequently asked questions for the home page</p>
        </div>
        <a href="{{ route('admin.home-faqs.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Add FAQ
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Section Info --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.home-faqs.update-section') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-4">
                    <label class="form-label">Section Title</label>
                    <input type="text" name="title" class="form-control" value="{{ $section->title ?? 'Frequently Asked Questions' }}">
                </div>

                <div class="col-md-5">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ $section->description ?? '' }}" placeholder="Section description">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ ($section->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ ($section->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ti ti-upload me-1"></i> Update Section
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- FAQs List --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th width="100">Order</th>
                            <th width="100">Status</th>
                            <th width="120">Image</th>
                            <th width="200">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $index => $faq)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $faq->question }}</td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 80) }}</td>
                                <td>{{ $faq->sort_order }}</td>
                                <td>
                                    <form action="{{ route('admin.home-faqs.toggle-status', $faq) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm {{ $faq->status ? 'btn-success' : 'btn-danger' }}">
                                            {{ $faq->status ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    @if($faq->image)
                                        <img src="{{ asset($faq->image) }}" alt="FAQ Image" style="width:50px; height:50px; object-fit:cover; border-radius:5px;">
                                    @else
                                        <span class="text-muted">No image</span>
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
                                <td colspan="7" class="text-center text-muted">No FAQs found. Click "Add FAQ" to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection