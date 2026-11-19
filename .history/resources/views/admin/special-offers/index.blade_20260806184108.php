{{-- resources/views/admin/special-offers/index.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Special Offers</h4>
            <p class="text-muted mb-0">Manage special offer content</p>
        </div>
        @if($offers->isEmpty())
            <a href="{{ route('admin.special-offers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Offer
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Badge</th>
                        <th>Button Text</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $offer->title }}</td>
                            <td>
                                @if($offer->badge_text)
                                    <span class="badge bg-danger">{{ $offer->badge_text }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $offer->button_text }}</td>
                            <td>
                                @if($offer->background_image)
                                    <img src="{{ $offer->image_url }}" 
                                         alt="{{ $offer->title }}" 
                                         width="80" 
                                         height="50" 
                                         style="object-fit:cover; border-radius:4px;">
                                @else
                                    <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <td>
                                @if($offer->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.special-offers.edit', $offer->id) }}" 
                                       class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.special-offers.toggle-status', $offer->id) }}" 
                                          method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-{{ $offer->is_active ? 'warning' : 'success' }}" 
                                                title="{{ $offer->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $offer->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-tag fa-3x d-block mb-2 text-muted"></i>
                                <p class="text-muted">No special offers found. Click "Add Offer" to create one.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection