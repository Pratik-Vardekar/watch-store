@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Homepage Cards</h4>
            <p class="text-muted mb-0">Manage the “More Than Just Watches” cards on the homepage</p>
        </div>
        <a href="{{ route('admin.home-highlights.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Card
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
                            <th width="80">Image</th>
                            <th>Title</th>
                            <th width="90">Order</th>
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
                                        <img src="{{ $highlight->image_url }}"
                                             alt="{{ $highlight->title }}"
                                             style="width:70px;height:50px;object-fit:cover;border-radius:4px;"
                                             onerror="this.onerror=null;this.src='{{ $highlight->fallback_image_url }}';">
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td>{{ $highlight->title }}</td>
                                <td>{{ $highlight->sort_order }}</td>
                                <td>
                                    @if($highlight->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.home-highlights.edit', $highlight) }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.home-highlights.destroy', $highlight) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this highlight?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="ti ti-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No cards found. Click Add Card to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
