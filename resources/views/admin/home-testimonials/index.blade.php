@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Home Testimonials</h4>
            <p class="text-muted mb-0">Manage "What Parents Say" reviews shown on the homepage (latest 8 active)</p>
        </div>
        <a href="{{ route('admin.home-testimonials.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Testimonial
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th width="60">SL</th>
                            <th>Name</th>
                            <th>Review</th>
                            <th width="80">Rating</th>
                            <th width="90">Order</th>
                            <th width="100">Status</th>
                            <th width="140">Added</th>
                            <th width="160">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($testimonials as $index => $testimonial)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $testimonial->name }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($testimonial->review, 80) }}</td>
                                <td>{{ $testimonial->rating }}</td>
                                <td>{{ $testimonial->sort_order }}</td>
                                <td>
                                    @if($testimonial->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $testimonial->created_at?->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.home-testimonials.edit', $testimonial) }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.home-testimonials.destroy', $testimonial) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this testimonial?');">
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
                                <td colspan="8" class="text-center">No testimonials found. Click Add Testimonial to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
