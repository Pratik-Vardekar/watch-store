@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Service Center</h4>
        <p class="text-muted mb-0">Manage homepage service banner images and service card title, description & image.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.home-service-items.update-images') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Service Banner (Hero Section)</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light border mb-4 py-2">
                    <strong class="text-dark">Tip:</strong> Upload the background texture and both watch images shown above the service cards.
                </div>

                <div class="row g-4">
                    {{-- <div class="col-md-4">
                        <label class="form-label">Background Image</label>
                        <input type="file" name="background_image" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img src="{{ $sectionSetting->background_image_url }}" alt="Background" class="rounded border" style="max-height:120px;object-fit:cover;">
                        </div>
                    </div> --}}

                    <div class="col-md-4">
                        <label class="form-label">Watch Image 1</label>
                        <input type="file" name="watch_image_1" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img src="{{ $sectionSetting->watch_image_1_url }}" alt="Watch 1" class="rounded border" style="max-height:120px;object-fit:cover;">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Watch Image 2</label>
                        <input type="file" name="watch_image_2" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img src="{{ $sectionSetting->watch_image_2_url }}" alt="Watch 2" class="rounded border" style="max-height:120px;object-fit:cover;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Service Cards</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th width="60">SL</th>
                                <th width="120">Current Image</th>
                                <th width="180">Title</th>
                                <th>Description</th>
                                <th width="220">Upload New Image</th>
                                <th width="160">Image Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <img src="{{ $item->image_url }}"
                                             alt="{{ $item->title }}"
                                             style="width:90px;height:60px;object-fit:cover;border-radius:4px;"
                                             onerror="this.onerror=null;this.src='{{ $item->default_image_url }}';">
                                    </td>
                                    <td>
                                        <input type="text"
                                               name="item_title[{{ $item->id }}]"
                                               class="form-control form-control-sm @error('item_title.' . $item->id) is-invalid @enderror"
                                               value="{{ old('item_title.' . $item->id, $item->title) }}"
                                               required>
                                        @error('item_title.' . $item->id)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <textarea name="item_description[{{ $item->id }}]"
                                                  rows="3"
                                                  class="form-control form-control-sm @error('item_description.' . $item->id) is-invalid @enderror">{{ old('item_description.' . $item->id, $item->description) }}</textarea>
                                        @error('item_description.' . $item->id)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="file"
                                               name="item_image[{{ $item->id }}]"
                                               class="form-control form-control-sm"
                                               accept="image/*">
                                    </td>
                                    <td>
                                        @if($item->image_changed_at)
                                            {{ $item->image_changed_at->format('d M Y, h:i A') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No service items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <button type="submit" class="btn btn-success">
                <i class="ti ti-device-floppy me-1"></i>
                Save Service Center
            </button>
        </div>
    </form>

</div>
@endsection
