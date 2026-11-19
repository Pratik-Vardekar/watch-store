{{-- resources/views/frontend/pages/contact_us.blade.php --}}

@extends('layouts.front')

@section('content')
<main class="pageWrapper classy-contact-page">         
    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-5 overflow-hidden bg-light position-relative">
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10 animateThis slideTop">
                    <nav class="breadcrumb justify-content-center mb-2 fw-semibold bg-transparent px-0">
                        <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                        <span class="breadcrumb-item active" aria-current="page">Contact Us</span>
                    </nav>
                    <h1 class="sectTitle fw-bold txtred animateThis slideTop mb-4 display-5 text-center">Contact Us</h1>
                </div>
            </div>

            <div class="row justify-content-center align-items-start g-lg-5 g-4">

                <!-- Contact Form -->
                <div class="col-lg-7 animateThis curtainLeft">
                    <div class="contact-form-wrapper bg-white shadow rounded-4 p-4 p-md-5 border position-relative">
                        {{-- Alerts --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mt-0 mb-4" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mt-0 mb-4" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        <form action="{{ route('contact.enquiry.store') }}" method="POST" class="contact-form" autocomplete="off" novalidate>
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-2">
                                        <input type="text" 
                                               name="full_name" 
                                               id="full_name" 
                                               class="form-control rounded-3 @error('full_name') is-invalid @enderror" 
                                               placeholder="Full Name"
                                               value="{{ old('full_name') }}"
                                               required>
                                        <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                        @error('full_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-2">
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               class="form-control rounded-3 @error('email') is-invalid @enderror" 
                                               placeholder="Email Address"
                                               value="{{ old('email') }}"
                                               required>
                                        <label for="email">Email Address <span class="text-danger">*</span></label>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-2">
                                        <input type="text" 
                                               name="contact_no" 
                                               id="contact_no" 
                                               class="form-control rounded-3 @error('contact_no') is-invalid @enderror" 
                                               placeholder="Contact Number"
                                               value="{{ old('contact_no') }}"
                                               required>
                                        <label for="contact_no">Contact Number <span class="text-danger">*</span></label>
                                        @error('contact_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-2">
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
                                    <div class="form-floating mb-2">
                                        <textarea name="comment" 
                                                  id="comment" 
                                                  class="form-control rounded-3 @error('comment') is-invalid @enderror" 
                                                  rows="5"
                                                  style="height:130px;"
                                                  placeholder="Write your message here..."
                                                  required>{{ old('comment') }}</textarea>
                                        <label for="comment">Message <span class="text-danger">*</span></label>
                                        @error('comment')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-lg btn-gradient-primary shadow w-100 rounded-pill py-3">
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
    .classy-contact-page {
        background: linear-gradient(110deg, #f7fafd 70%, #fbeee6 100%);
        min-height: 100vh;
    }
    .btn-gradient-primary {
        background: linear-gradient(90deg, #ff5858 0%, #f09819 100%);
        color: #fff;
        border: none;
        transition: box-shadow 0.2s;
    }
    .btn-gradient-primary:hover, .btn-gradient-primary:focus {
        background: linear-gradient(90deg, #f09819 0%, #ff5858 100%);
        color: #fff;
        box-shadow: 0 6px 28px -6px #fa586264, 0 1.5px 8px #f0981955;
    }
    .contact-form-wrapper {
        box-shadow: 0 6px 28px -6px rgba(66, 53, 100, 0.10), 0 1.5px 8px rgba(240,152,25,0.13);
        border-radius: 1.5rem;
        overflow: hidden;
    }
    .sectTitle {
        color: #fa5862;
        letter-spacing: 1px;
    }
    .form-floating label {
        color: #737373;
        font-size: .98rem;
    }
    .form-control:focus {
        border-color: #fa5862;
        box-shadow: 0 0 0 2px #fa586245;
    }
    .alert {
        border-radius: 1.2rem;
    }
    @media (max-width: 991px){
        .contact-form-wrapper {
            padding: 2rem 1rem;
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
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endpush