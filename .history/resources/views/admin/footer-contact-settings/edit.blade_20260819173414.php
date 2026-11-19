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
            <h4>Footer Settings</h4>
            <p class="text-muted mb-0 small">Manage footer text, images, contact details, social links, and enroll banner.</p>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.footer-contact-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf


                {{-- Contact Details --}}
                <h5 class="mb-3 text-primary">Contact Details</h5>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Connect Tag Image</label>
                        <input type="file" name="connect_tag_image" class="form-control" accept="image/*">
                        @php $connectImg = $setting->connect_tag_image ?: 'frontend/assets/images/connectTag.svg'; @endphp
                        <div class="mt-2">
                            <img src="{{ asset($connectImg) }}" alt="Connect" style="max-height:80px;" class="rounded border">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact No.</label>
                        <input type="text" name="contact_phone" class="form-control"
                            value="{{ old('contact_phone', $setting->contact_phone) }}" placeholder="e.g. +91 98922 51015">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="contact_email" class="form-control"
                            value="{{ old('contact_email', $setting->contact_email) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="contact_address" class="form-control" rows="3">{{ old('contact_address', $setting->contact_address) }}</textarea>
                    </div>
                </div>

                <hr>

                {{-- Social & Amazon --}}
                <h5 class="mb-3 text-primary">Social & Amazon</h5>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fab fa-facebook text-primary"></i> Facebook URL
                        </label>
                        <input type="url" name="facebook_url" class="form-control"
                            value="{{ old('facebook_url', $setting->facebook_url) }}" placeholder="https://facebook.com/...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fab fa-twitter text-info"></i> Twitter / X URL
                        </label>
                        <input type="url" name="twitter_url" class="form-control"
                            value="{{ old('twitter_url', $setting->twitter_url) }}" placeholder="https://x.com/...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fab fa-instagram text-danger"></i> Instagram URL
                        </label>
                        <input type="url" name="instagram_url" class="form-control"
                            value="{{ old('instagram_url', $setting->instagram_url) }}" placeholder="https://instagram.com/...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fab fa-youtube text-danger"></i> YouTube URL
                        </label>
                        <input type="url" name="youtube_url" class="form-control"
                            value="{{ old('youtube_url', $setting->youtube_url) }}" placeholder="https://youtube.com/...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fab fa-amazon text-warning"></i> Amazon URL
                        </label>
                        <input type="url" name="amazon_url" class="form-control"
                            value="{{ old('amazon_url', $setting->amazon_url) }}" placeholder="https://amazon.in/...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Amazon Logo</label>
                        <input type="file" name="amazon_logo_image" class="form-control" accept="image/png,.png,image/svg+xml,.svg">
                        @php $amazonImg = $setting->amazon_logo_image ?: 'frontend/assets/images/amazonlogo.svg'; @endphp
                        <div class="mt-2">
                            <img src="{{ asset($amazonImg) }}" alt="Amazon" style="max-height:50px;" class="rounded border">
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Copyright --}}
                <h5 class="mb-3 text-primary">Copyright</h5>
                <div class="mb-4">
                    <label class="form-label">Copyright Text</label>
                    <input type="text" name="copyright_text" class="form-control"
                        value="{{ old('copyright_text', $setting->copyright_text) }}" placeholder="© 2026 Watch. All rights reserved.">
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Footer Settings
                </button>
            </form>
        </div>
    </div>

</div>

@endsection