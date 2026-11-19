@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h4>Shipping Settings</h4>
        </div>

        <div class="card-body">

            <form action="{{ route('shipping.settings.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label>Free Shipping Above Amount</label>

                    <input type="number"
       name="free_shipping_amount"
       value="{{ old('free_shipping_amount', $setting->free_shipping_amount ?? '') }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Shipping Charge</label>

                    <input
                        type="number"
                        step="0.01"
                        name="shipping_charge"
                        class="form-control"
                        value="{{ $setting->shipping_charge ?? 50 }}"
                    >
                </div>

                <button class="btn btn-primary">
                    Save Settings
                </button>

            </form>

        </div>
    </div>

</div>

@endsection