@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h4>Contact Settings</h4>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.contact-settings.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label>Address <small class="text-muted">(shown on Contact page)</small></label>
                    <textarea name="contact_address" class="form-control" rows="4" placeholder="Company name and full address">{{ old('contact_address', $setting->contact_address) }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="contact_email" class="form-control"
                        value="{{ old('contact_email', $setting->contact_email) }}">
                </div>

                <div class="mb-3">
                    <label>Contact No.</label>
                    <input type="text" name="contact_phone" class="form-control"
                        value="{{ old('contact_phone', $setting->contact_phone) }}">
                </div>

                <div class="mb-3">
                    <label>Working Days</label>
                    <input type="text" name="business_days" class="form-control"
                        value="{{ old('business_days', $setting->business_days) }}" placeholder="e.g. Monday to Saturday">
                </div>

                <div class="mb-3">
                    <label>Working Hours</label>
                    <input type="text" name="business_hours" class="form-control"
                        value="{{ old('business_hours', $setting->business_hours) }}" placeholder="e.g. 10.00am - 07.00pm">
                </div>

                <hr>
                <p class="text-muted small mb-3">Fields below are used in footer / support sections (not on Contact page).</p>

                {{-- <div class="mb-3">
                    <label>Support Message</label>
                    <textarea name="support_message" class="form-control" rows="3">{{ old('support_message', $setting->support_message) }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Support Timings</label>
                    <input type="text" name="support_timings" class="form-control"
                        value="{{ old('support_timings', $setting->support_timings) }}">
                </div> --}}

                <button type="submit" class="btn btn-success">
                    Save Settings
                </button>

            </form>

        </div>
    </div>

</div>

@endsection