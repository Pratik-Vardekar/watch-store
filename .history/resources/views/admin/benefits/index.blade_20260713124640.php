@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="mb-4">
        <h4 class="mb-1">
            Benefits
        </h4>
        <p class="text-muted mb-0">
            Manage your benefits here
        </p>
    </div>
    <div class="card">
        <div class="card-body">
            <a href="{{ route('benefits.create') }}" class="btn btn-primary mb-3">
                <i class="ti ti-plus"></i> Add Benefit
            </a>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>SVG Logo</th>
                            <th>Status</th>
                            <th width="130">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($benefits as $index => $benefit)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $benefit->title }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($benefit->description, 60) }}</td>
                                <td>
                                    @if($benefit->svg_logo)
                                        <div style="width:40px;height:40px;">{!! $benefit->svg_logo !!}</div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($benefit->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('benefits.edit', $benefit->id) }}" class="btn btn-sm btn-info">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('benefits.destroy', $benefit->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="ti ti-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No benefits found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection