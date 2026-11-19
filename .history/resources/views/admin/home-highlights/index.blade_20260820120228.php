@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Homepage Cards</h4>
            <p class="text-muted mb-0">Manage the "More Than Just Watches" cards on the homepage</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th width="60">SL</th>
                            <th width="80">Image</th>
                            <th>Title</th>
                            <th width="90">Order</th>
                            <th width="160">Image Updated</th>
                            <th width="100">Status</th>
                            <th width="160">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($highlights as $index => $highlight)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($highlight->image_url)
                                        <img
                                            src="{{ $highlight->image_url }}"
                                            alt="{{ $highlight->title }}"
                                            style="width:70px;height:50px;object-fit:cover;border-radius:4px;"
                                        >
                                    @else
                                        <span class="text-muted small">No image</span>
                                    @endif
                                </td>
                                <td>{{ $highlight->title }}</td>
                                <td>{{ $highlight->sort_order }}</td>
                                <td>
                                    @if($highlight->image_changed_at)
                                        {{ $highlight->image_changed_at->format('d M Y, h:i A') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($highlight->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('admin.home-highlights.edit', $highlight->id) }}"
                                        class="btn btn-light btn-icon btn-sm rounded-circle"
                                        title="Edit">
                                         <i class="ti ti-pencil fs-5"></i>
                                     </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No homepage cards found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
