@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h4>Contact Settings</h4>
            <p class="text-muted mb-0 small">Manage contact details shown on the Contact Us page.</p>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.contact-settings.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label>Address <small class="text-muted">(Contact Us page)</small></label>
                    <textarea name="contact_address" class="form-control" rows="4" placeholder="Company name and full address">{{ old('contact_address', $setting->contact_address) }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Email <small class="text-muted">(Contact Us page)</small></label>
                    <input type="email" name="contact_email" class="form-control"
                        value="{{ old('contact_email', $setting->contact_email) }}">
                </div>

                <div class="mb-3">
                    <label>Contact No. <small class="text-muted">(Contact Us page)</small></label>
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

                <button type="submit" class="btn btn-success">
                    Save Contact Settings
                </button>

            </form>

        </div>
    </div>

</div>

@endsection