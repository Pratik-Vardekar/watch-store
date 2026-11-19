{{-- resources/views/frontend/pages/contact_us.blade.php --}}

@extends('layouts.front')

@section('title', 'Contact Us')
@section('meta_description', $contactSetting->meta_description ?? 'Get in touch with us for any queries or support.')
@section('meta_keywords', $contactSetting->meta_keywords ?? 'contact, support, enquiries, watch store')

@section('content')

<main class="pageWrapper d-flex">

    <section class="w-100 d-flex position-relative">

        <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

        <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

            <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

                <!-- Contact Form -->
                <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center">
                    <h1 class="sectTitle mb-4 d-inline-flex align-items-center gap-3">Contact Us</h1>

                    @if(session('success'))
                        <div class="alert alert-success py-2 mb-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger py-2 mb-3" role="alert">
                            @foreach($errors->all() as $error)
                                <i class="fas fa-exclamation-circle me-2"></i> {{ $error }}
                            @endforeach
                        </div>
                    @endif

                    <!-- Contact Info Summary -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Email</small>
                            <a href="mailto:{{ $contactSetting->contact_email ?? 'cheriksinnovativeventures@gmail.com' }}" class="fw-semibold text-decoration-none small">
                                {{ $contactSetting->contact_email ?? 'cheriksinnovativeventures@gmail.com' }}
                            </a>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Phone</small>
                            <a href="tel:{{ preg_replace('/\s+/', '', $contactSetting->contact_phone ?? '+919892251015') }}" class="fw-semibold text-decoration-none small">
                                {{ $contactSetting->contact_phone ?? '+91 98922 51015' }}
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('contact.enquiry.store') }}" method="POST" class="row g-3 formFormat">
                        @csrf

                        <!-- Full Name -->
                        <div class="col-12">
                            <input
                                type="text"
                                class="form-control @error('full_name') is-invalid @enderror"
                                name="full_name"
                                id="full_name"
                                placeholder="Full Name*"
                                value="{{ old('full_name') }}"
                                required
                            />
                            @error('full_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-12">
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                id="email"
                                placeholder="Email Id*"
                                value="{{ old('email') }}"
                                required
                            />
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contact Number -->
                        <div class="col-12">
                            <input
                                type="tel"
                                class="form-control @error('contact_no') is-invalid @enderror"
                                name="contact_no"
                                id="contact_no"
                                placeholder="Mobile No.*"
                                value="{{ old('contact_no') }}"
                                inputmode="numeric"
                                maxlength="10"
                                required
                            />
                            @error('contact_no')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="col-12">
                            <textarea
                                class="form-control @error('comment') is-invalid @enderror"
                                name="comment"
                                id="comment"
                                placeholder="Your Message*"
                                rows="4"
                                required
                            >{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <small>We'll get back to you within 24 hours.</small>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 text-center">
                            <button type="submit" class="ctaBtn">
                                <span class="btnTxt">Send Message</span>
                                <span class="arw"></span>
                            </button>
                        </div>

                    </form>

                </div>

                <!-- Watch Animation -->
                <div class="col-md-6 col-12 py-5 d-flex justify-content-center align-items-center bgGray loginImg">
                    <iframe src="{{ asset('frontend/assets/watch/index.html') }}" class="watchFrame" title="Watch Display"></iframe>
                </div>

            </div>

        </div>

    </section>

</main>

@endsection

@push('styles')
<style>
/* Additional styles */
.alert {
    border-radius: 8px;
    font-size: 0.9rem;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

.form-control:focus {
    border-color: #fa5862;
    box-shadow: 0 0 0 0.2rem rgba(250, 88, 98, 0.15);
}

@media (max-width: 767px) {
    .loginWrap {
        border-radius: 12px !important;
    }
    
    .loginImg {
        min-height: 250px;
    }
    
    .watchFrame {
        height: 200px;
    }
}
</style>
@endpush