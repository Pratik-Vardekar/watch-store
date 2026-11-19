@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4>Header Top Bar Settings</h4>
            <p class="text-muted mb-0 small">Manage delivery message, email, and address shown in the frontend header top bar.</p>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.header-top-bar-settings.update') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Delivery Message</label>
                        <input type="text" name="delivery_message" class="form-control"
                            value="{{ old('delivery_message', $setting->delivery_message) }}"
                            placeholder="Get free home delivery (Order more than ₹300)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $setting->email) }}"
                            placeholder="cheriksinnovativeventures@gmail.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control"
                            value="{{ old('address', $setting->address) }}"
                            placeholder="Santacruz West, Mumbai 400 054">
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Save Header Settings</button>
            </form>
        </div>
    </div>

</div>

@endsection
