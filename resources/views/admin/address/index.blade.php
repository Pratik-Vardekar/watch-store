@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">Addresses</h4>

        <p class="text-muted mb-0">
            Manage user addresses
        </p>

    </div>

    <a href="{{ url()->previous() }}"
        class="btn btn-light">

        <i class="ti ti-arrow-left me-1"></i>
        Back

    </a>

</div>

    <div class="card">

        <div class="card-body p-0">
<div class="card mb-3">

    <div class="card-body">

        <form method="GET"
            action="{{ route('address.index') }}"
            class="row g-2">

            <div class="col-md-4">

                <input type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search name, phone, city"
                    value="{{ request('search') }}">

            </div>

            <div class="col-md-2">

                <select name="status"
                    class="form-select">

                    <option value="">All Status</option>

                    <option value="1"
                        {{ request('status') == '1' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0"
                        {{ request('status') == '0' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>

            <div class="col-md-3 d-flex gap-2">

                <button type="submit"
                    class="btn btn-primary">

                    <i class="ti ti-search me-1"></i>
                    Filter

                </button>

                <a href="{{ route('address.index') }}"
                    class="btn btn-light">

                    Reset

                </a>

            </div>

        </form>

    </div>

</div>
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <!-- <th>Type</th> removed -->
                            <th>Default</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($addresses as $address)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                          <td>

    <div>

        <h6 class="mb-0 fw-semibold">
            {{ $address->name ?: ($address->user->name ?? '-') }}
        </h6>

        <small class="text-muted">
            {{ $address->user->email ?? '-' }}
        </small>

    </div>

</td>

<td>

    <div>

        <span class="fw-medium">
            {{ $address->phone ?: ($address->user->phone ?? '-') }}
        </span>

        @if($address->alternate_phone)

            <br>

            <small class="text-muted">
                Alt: {{ $address->alternate_phone }}
            </small>

        @endif

    </div>

</td>

<td style="max-width:300px;white-space:normal;word-break:break-word;line-height:1.5;">

    <span class="fw-medium">
        {{ $address->address }}
    </span>

    @if($address->landmark)

        <br>

        <small class="text-muted">
            Landmark: {{ $address->landmark }}
        </small>

    @endif

    <br>

    <small class="text-muted">

        {{ $address->city }},
        {{ $address->state }},
        {{ $address->country }}

        @if($address->pincode)
            - {{ $address->pincode }}
        @endif

    </small>

</td>

                            <!-- Removed Type column -->

                            <td>

                                @if($address->is_default == 1)

                                    <span class="badge bg-success">
                                        Yes
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        No
                                    </span>

                                @endif

                            </td>

                            <td>

                                @if($address->status == 1)

                                    <span class="badge badge-soft-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge badge-soft-danger">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $address->created_at->format('d M Y') }}
                            </td>

                            <td>

                                <form action="{{ route('address.destroy', $address->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete Address?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="btn btn-sm btn-light">

                                        <i class="ti ti-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="8"
                                class="text-center py-4">

                                No Addresses Found

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>
<div class="p-3">
    {{ $addresses->links() }}
</div>
            </div>

        </div>

    </div>

</div>

@endsection