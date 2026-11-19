{{-- resources/views/frontend/pages/contact_us.blade.php --}}

@extends('layouts.front')

@section('title', 'Contact Us')
@section('meta_description', $contactSetting->meta_description ?? 'Get in touch with us for any queries or support.')
@section('meta_keywords', $contactSetting->meta_keywords ?? 'contact, support, enquiries, watch store')

@section('content')
<main class="pageWrapper contact-page">

    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>

    <section class="py-5 overflow-hidden bg-light position-relative">
        <div class="container position-relative">

            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10 text-center">
                    <nav class="breadcrumb justify-content-center mb-2 fw-semibold bg-transparent px-0">
                        <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                        <span class="breadcrumb-item active" aria-current="page">Contact Us</span>
                    </nav>

                    <h1 class="sectTitle fw-bold txtred mb-3 display-5">
                        Get In Touch
                    </h1>
                    <p class="text-muted mb-5">
                        We'd love to hear from you. Drop us a message and we'll respond as soon as possible.
                    </p>
                </div>
            </div>

            @if(session('success'))
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                                <div>
                                    <strong>Success!</strong> {{ session('success') }}
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-5 mt-2">

                <!-- Contact Info Cards -->
                <div class="col-lg-4">
                    <div class="contact-info-wrapper h-100">

                        <div class="contact-card bg-white shadow-sm rounded-4 p-4 mb-4 border">
                            <div class="d-flex align-items-start gap-3">
                                <div class="contact-icon bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-map-marker-alt fa-2x txtred"></i>
                                </div>
                                <div>
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-1">Address</h6>
                                    @if($contactSetting->contact_address ?? false)
                                        <p class="mb-0">{!! nl2br(e($contactSetting->contact_address)) !!}</p>
                                    @else
                                        <p class="mb-0">
                                            <strong>Cheriks Innovative Ventures Pvt. Ltd.</strong><br>
                                            D-201, AcharatBaug,<br>
                                            Phirozshah Mehta Road, Santacruz West,<br>
                                            Mumbai - 400 054. INDIA
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="contact-card bg-white shadow-sm rounded-4 p-4 mb-4 border">
                            <div class="d-flex align-items-start gap-3">
                                <div class="contact-icon bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-envelope fa-2x txtred"></i>
                                </div>
                                <div>
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-1">Email</h6>
                                    <a href="mailto:{{ $contactSetting->contact_email ?? 'cheriksinnovativeventures@gmail.com' }}" class="text-decoration-none">
                                        {{ $contactSetting->contact_email ?? 'cheriksinnovativeventures@gmail.com' }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="contact-card bg-white shadow-sm rounded-4 p-4 mb-4 border">
                            <div class="d-flex align-items-start gap-3">
                                <div class="contact-icon bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-phone fa-2x txtred"></i>
                                </div>
                                <div>
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-1">Contact No.</h6>
                                    @php $phone = $contactSetting->contact_phone ?? '+91 98922 51015'; @endphp
                                    <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="text-decoration-none">
                                        {{ $phone }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="contact-card bg-white shadow-sm rounded-4 p-4 border">
                            <div class="d-flex align-items-start gap-3">
                                <div class="contact-icon bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-clock fa-2x txtred"></i>
                                </div>
                                <div>
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-1">Working Hours</h6>
                                    <p class="mb-0">
                                        <strong>{{ $contactSetting->business_days ?? 'Monday to Saturday' }}</strong><br>
                                        {{ $contactSetting->business_hours ?? '10.00am - 07.00pm' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="mt-4">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Follow Us</h6>
                            <div class="d-flex gap-3">
                                <a href="#" class="social-icon rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fab fa-facebook-f txtred"></i>
                                </a>
                                <a href="#" class="social-icon rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fab fa-instagram txtred"></i>
                                </a>
                                <a href="#" class="social-icon rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fab fa-youtube txtred"></i>
                                </a>
                                <a href="#" class="social-icon rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fab fa-whatsapp txtred"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="contact-form-wrapper bg-white shadow-sm rounded-4 p-4 p-lg-5 border">
                        <h4 class="mb-4 fw-bold">Send Us a Message</h4>
                        <p class="text-muted mb-4">Fill in the form below and we'll get back to you shortly.</p>

                        <form action="{{ route('contact.enquiry.store') }}" method="POST" class="contact-form">
                            @csrf

                            <div class="row g-4">

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               name="full_name" 
                                               id="full_name" 
                                               class="form-control rounded-3 @error('full_name') is-invalid @enderror" 
                                               placeholder="Full Name"
                                               value="{{ old('full_name') }}"
                                               required>
                                        <label for="full_name">
                                            Full Name <span class="text-danger">*</span>
                                        </label>
                                        @error('full_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               class="form-control rounded-3 @error('email') is-invalid @enderror" 
                                               placeholder="Email Address"
                                               value="{{ old('email') }}"
                                               required>
                                        <label for="email">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" 
                                               name="contact_no" 
                                               id="contact_no" 
                                               class="form-control rounded-3 @error('contact_no') is-invalid @enderror" 
                                               placeholder="Contact Number"
                                               value="{{ old('contact_no') }}"
                                               required>
                                        <label for="contact_no">
                                            Contact Number <span class="text-danger">*</span>
                                        </label>
                                        @error('contact_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               name="subject" 
                                               id="subject" 
                                               class="form-control rounded-3" 
                                               placeholder="Subject"
                                               value="{{ old('subject') }}">
                                        <label for="subject">Subject</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea name="comment" 
                                                  id="comment" 
                                                  class="form-control rounded-3 @error('comment') is-invalid @enderror" 
                                                  placeholder="Your Message"
                                                  rows="5"
                                                  style="height: 180px;"
                                                  required>{{ old('comment') }}</textarea>
                                        <label for="comment">
                                            Your Message <span class="text-danger">*</span>
                                        </label>
                                        @error('comment')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-gradient-primary rounded-pill py-3 px-5 w-100 w-md-auto">
                                        <i class="fas fa-paper-plane me-2"></i> Send Message
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </section>

</main>
@endsection

@push('styles')
<style>
.contact-page {
    background: linear-gradient(110deg, #f7fafd 70%, #fbeee6 100%);
    min-height: 100vh;
}

.contact-card {
    transition: all 0.3s ease;
    border-color: rgba(0,0,0,0.05) !important;
}

.contact-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.08) !important;
}

.contact-icon {
    width: 60px;
    height: 60px;
    min-width: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(250, 88, 98, 0.1) !important;
}

.contact-icon i {
    font-size: 24px;
}

.social-icon {
    transition: all 0.3s ease;
    background: rgba(250, 88, 98, 0.1) !important;
}

.social-icon:hover {
    transform: translateY(-3px);
    background: rgba(250, 88, 98, 0.2) !important;
}

.social-icon i {
    font-size: 20px;
}

.contact-form-wrapper {
    border-color: rgba(0,0,0,0.05) !important;
}

.form-floating .form-control {
    border: 2px solid #e8ecf1;
    border-radius: 12px !important;
    background: #fafcff;
    transition: all 0.3s ease;
}

.form-floating .form-control:focus {
    border-color: #fa5862;
    box-shadow: 0 0 0 0.2rem rgba(250, 88, 98, 0.15);
    background: #ffffff;
}

.form-floating .form-control.is-invalid {
    border-color: #dc3545;
}

.form-floating .form-control.is-invalid:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
}

.form-floating label {
    color: #6c757d;
    font-weight: 500;
}

.btn-gradient-primary {
    background: linear-gradient(90deg, #ff5858, #f09819);
    border: none;
    color: #fff;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 1.05rem;
}

.btn-gradient-primary:hover {
    background: linear-gradient(90deg, #f09819, #ff5858);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(250, 88, 98, 0.3);
}

.btn-gradient-primary:active {
    transform: translateY(0);
}

.sectTitle {
    color: #fa5862;
    letter-spacing: 1px;
}

.breadcrumb-item {
    color: #6c757d;
}

.breadcrumb-item.active {
    color: #fa5862;
}

.breadcrumb-item a {
    color: #6c757d;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #fa5862;
}

.alert {
    border: none !important;
    border-radius: 16px !important;
}

.alert-success {
    background: #e8f5e9;
    color: #2e7d32;
}

.alert-danger {
    background: #fbe9e7;
    color: #c62828;
}

@media (max-width: 767px) {
    .btn-gradient-primary {
        width: 100% !important;
    }
    
    .contact-form-wrapper {
        padding: 1.5rem !important;
    }
    
    .contact-card {
        padding: 1.25rem !important;
    }
}

/* Animation for form */
.contact-form-wrapper {
    animation: fadeInUp 0.6s ease;
}

.contact-card {
    animation: fadeInLeft 0.6s ease;
}

.contact-card:nth-child(2) {
    animation-delay: 0.1s;
}
.contact-card:nth-child(3) {
    animation-delay: 0.2s;
}
.contact-card:nth-child(4) {
    animation-delay: 0.3s;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        });
    }, 5000);

    // Add floating label animation enhancement
    const formControls = document.querySelectorAll('.form-floating .form-control');
    formControls.forEach(function(input) {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});
</script>
@endpush