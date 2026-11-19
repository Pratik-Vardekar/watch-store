{{-- resources/views/admin/stores/index.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Stores List</h1>

        <a href="{{ route('stores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Store
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Store Name</th>
                        <th>Phone</th>
                        <th>Timing</th>
                        <th>Primary Image</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($stores as $store)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $store->name }}</td>

                        <td>{{ $store->phone }}</td>

                        <td>{{ $store->timing }}</td>

                        <td>

                            @php
                                $images = $store->images;

                                if (is_string($images)) {
                                    $images = json_decode($images, true);
                                }

                                $image = is_array($images) && count($images)
                                    ? $images[0]
                                    : null;
                            @endphp

                            @if($image)

                                <img
                                    src="{{ asset('storage/stores/' . basename($image)) }}"
                                    width="80"
                                    height="80"
                                    alt="{{ $store->name }}"
                                    class="rounded border"
                                    style="object-fit:cover;"
                                    onerror="this.onerror=null;this.src='https://placehold.co/80x80?text=No+Image';">

                            @else

                                <div class="border rounded d-flex align-items-center justify-content-center"
                                     style="width:80px;height:80px;background:#f8f9fa;">
                                    No Image
                                </div>

                            @endif

                        </td>

                        <td>

                            @if($store->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif

                        </td>

                        <td>

                            <a href="{{ route('stores.edit',$store->id) }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('stores.destroy',$store->id) }}"
                                  method="POST"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this store?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center">
                            No Stores Found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection