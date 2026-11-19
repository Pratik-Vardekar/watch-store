{{-- resources/views/frontend/pages/contact_us.blade.php --}}

@extends('layouts.front')

@section('content')
<main class="pageWrapper">         

    <div class="cloudWrap position-relative d-none d-lg-block">
       <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-5 overflow-hidden">
       <div class="container">

          <div class="text-center mb-0 animateThis slideTop">
             <nav class="breadcrumb justify-content-center mb-0 fw-semibold">
                <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                <span class="breadcrumb-item active" aria-current="page">Contact Us</span>
             </nav>
             <h1 class="sectTitle fw-bold txtred animateThis slideTop">Contact Us</h1>
          </div>

          @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
          @endif

          @if($errors->any())
              <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                  <ul class="mb-0">
                      @foreach($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
          @endif

          <div class="row g-md-5 g-4">
             <!-- Contact Info -->
             <div class="col-lg-3 formFormat animateThis slideRight">
                <address class="mb-3">
                   <small class="d-block txtteal text-uppercase" style="font-size:.75rem;">Address</small>
                   @if($contactSetting->contact_address ?? false)
                      <p>{!! nl2br(e($contactSetting->contact_address)) !!}</p>
                   @else
                      <strong>Cheriks Innovative Ventures Pvt. Ltd.</strong>
                      <p>D-201, AcharatBaug,<br> Phirozshah Mehta Road, Santacruz West,<br> Mumbai - 400 054. INDIA</p>
                   @endif
                </address>

                <div class="mb-3">
                   <small class="d-block txtteal text-uppercase" style="font-size:.75rem;">Email</small>
                   <a href="mailto:{{ $contactSetting->contact_email ?? 'cheriksinnovativeventures@gmail.com' }}">
                      {{ $contactSetting->contact_email ?? 'cheriksinnovativeventures@gmail.com' }}
                   </a>
                </div>

                <div class="mb-3">
                   <small class="d-block txtteal text-uppercase" style="font-size:.75rem;">Contact No.</small>
                   @php $phone = $contactSetting->contact_phone ?? '+91 98922 51015'; @endphp
                   <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a>
                </div>

                <div class="mb-3">
                   <small class="d-block txtteal text-uppercase" style="font-size:.75rem;">Working Hours</small>
                   <p>
                      {{ $contactSetting->business_days ?? 'Monday to Saturday' }}
                      <br>
                      {{ $contactSetting->business_hours ?? '10.00am - 07.00pm' }}
                   </p>
                </div>
             </div>

             <!-- Contact Form -->
             <div class="col-lg animateThis curtainLeft">
                <div class="contact-form-wrapper">
                    <form action="{{ route('contact.enquiry.store') }}" method="POST" class="contact-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="full_name" 
                                           id="full_name" 
                                           class="form-control @error('full_name') is-invalid @enderror" 
                                           placeholder="Enter your full name"
                                           value="{{ old('full_name') }}"
                                           required>
                                    @error('full_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           placeholder="Enter your email address"
                                           value="{{ old('email') }}"
                                           required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_no" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="contact_no" 
                                           id="contact_no" 
                                           class="form-control @error('contact_no') is-invalid @enderror" 
                                           placeholder="Enter your contact number"
                                           value="{{ old('contact_no') }}"
                                           required>
                                    @error('contact_no')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" 
                                           name="subject" 
                                           id="subject" 
                                           class="form-control" 
                                           placeholder="Enter subject"
                                           value="{{ old('subject') }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="comment" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea name="comment" 
                                              id="comment" 
                                              class="form-control @error('comment') is-invalid @enderror" 
                                              rows="5" 
                                              placeholder="Write your message here..."
                                              required>{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
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

@push('scripts')
<script>
    // Form validation or additional scripts if needed
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