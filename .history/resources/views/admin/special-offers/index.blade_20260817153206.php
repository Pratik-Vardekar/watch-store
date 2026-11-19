@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Special Offers</h4>
            <p class="text-muted mb-0">Manage special offers displayed on the homepage</p>
        </div>
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
                            <th width="100">Image</th>
                            <th>Title</th>
                            <th>Badge</th>
                            <th>Features</th>
                            <th width="100">Status</th>
                            <th width="160">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($offers as $index => $offer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{-- ✅ Show image if exists --}}
                                    @if($offer->image_url)
                                        <img
                                            src="{{ $offer->image_url }}"
                                            alt="{{ $offer->title }}"
                                            style="width:70px; height:50px; object-fit:cover; border-radius:4px;"
                                        >
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $offer->title }}</strong>
                                    @if($offer->subtitle)
                                        <br><small class="text-muted">{{ $offer->subtitle }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($offer->badge_text)
                                        <span class="badge bg-warning">{{ $offer->badge_text }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($offer->features && count($offer->features) > 0)
                                        @foreach($offer->features as $feature)
                                            <span class="badge bg-info me-1 mb-1">{{ $feature }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($offer->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.special-offers.edit', $offer) }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.special-offers.toggle-status', $offer) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm {{ $offer->is_active ? 'btn-warning' : 'btn-success' }}">
                                            <i class="ti ti-{{ $offer->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    No special offers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection