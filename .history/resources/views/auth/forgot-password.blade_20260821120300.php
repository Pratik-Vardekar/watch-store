@extends('layouts.front')

@section('title', 'Forgot Password')

@section('content')
<main class="pageWrapper d-flex">

   <section class="w-100 d-flex position-relative">
      
      <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

      <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

         <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

            <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center">
               <h1 class="resetTitle mb-4 d-inline-flex align-items-center gap-3">Reset Password</h1>
               <small class="mb-3 d-block lh-sm">Enter your email id below to receive a password reset link.</small>

               @if (session('status'))
                   <div class="alert alert-success mb-3" role="alert">
                       {{ session('status') }}
                   </div>
               @endif

               <form method="POST" action="{{ route('password.email') }}" class="row g-3 formFormat">
                   @csrf

                  <div class="col-12">
                     <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror emailInputBox" 
                        name="email" 
                        id="email" 
                        placeholder="Email Id"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                     />
                     @error('email')
                         <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                     @enderror
                  </div>

                  <div class="col-12 text-center">
                    <button type="submit" class="custom-login-btn submit-btn-m700">Submit</button>
                  </div>

                  <div class="col-12 mt-4 text-center">
                     <a href="{{ route('login') }}" class="fw-medium">Back to Sign In</a>
                  </div>
               </form>

            </div>

            <div class="col-md-6 col-12 py-5 d-flex justify-content-center align-items-center bgGray loginImg">
               <iframe src="{{ asset('frontend/assets/watch/index.html') }}" class="watchFrame" title="Watch Display"></iframe>
            </div>

         </div>
            
      </div>

   </section>         

</main>
@endsection

@push('styles')
<!-- Ensure include for Manrope 700 -->
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&display=swap" rel="stylesheet">
<style>
    .loginWrap {
        max-width: 1000px;
        width: 100%;
    }

    .loginImg {
        min-height: 400px;
        background: #f8f9fa;
    }

    .watchFrame {
        width: 100%;
        height: 450px;
        border: none;
        background: transparent;
    }

    /* Style the email id input box exactly as the image requirements */
    .emailInputBox {
        font-family: 'Manrope', sans-serif;
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 22px;
        color: rgb(33, 37, 41);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px 15px;
        background: #fff;
        transition: all 0.3s;
        height: 48px;
        box-shadow: none;
        outline: none;
    }

    .emailInputBox::placeholder {
        color: #adb5bd;
        font-family: 'Manrope', sans-serif;
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 22px;
    }

    .formFormat .form-control {
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: none;
        font-family: 'Manrope', sans-serif;
    }

    .emailInputBox:focus,
    .formFormat .form-control:focus {
        border-color: #d4a843;
        box-shadow: 0 0 0 0.2rem rgba(212, 168, 67, 0.10);
        outline: none;
    }

    .formFormat .form-control.is-invalid,
    .emailInputBox.is-invalid {
        border-color: #dc3545;
    }

    .formFormat .form-control.is-invalid:focus,
    .emailInputBox.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.08);
    }

    /* Updated button style based on supplied image */
    .custom-login-btn.submit-btn-m700 {
        background: #bd9b7c;
        color: rgb(255, 255, 255);
        border: none;
        border-radius: 8px;
        width: 210px;
        height: 60px;
        font-family: 'Manrope', sans-serif;
        font-style: normal;
        font-weight: 700;
        font-size: 16px;
        line-height: 19px;
        letter-spacing: 0;
        transition: background 0.2s;
        margin: 0 auto;
        display: block;
        outline: none;
        box-shadow: none;
        text-transform: none;
    }

    .custom-login-btn.submit-btn-m700:hover, 
    .custom-login-btn.submit-btn-m700:focus {
        background: #a8866c;
        color: rgb(255, 255, 255);
    }

    .ctaBtn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 35px;
        background: #d4a843;
        color: #fff;
        text-decoration: none;
        border-radius: 30px;
        font-weight: 600;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
    }

    .ctaBtn:hover {
        background: #b8922f;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(212, 168, 67, 0.3);
    }

    .arw {
        display: inline-block;
        width: 20px;
        height: 20px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='5' y1='12' x2='19' y2='12'/%3E%3Cpolyline points='12 5 19 12 12 19'/%3E%3C/svg%3E") center/contain no-repeat;
        transition: transform 0.3s ease;
    }

    .ctaBtn:hover .arw {
        transform: translateX(5px);
    }

    .resetTitle {
        font-family: 'Manrope', sans-serif;
        font-weight: 800;
        font-size: 50px;
        line-height: 60px;
        color: rgb(62, 62, 71);
        margin-bottom: 1.5rem !important;
        letter-spacing: 0;
    }

    .progress {
        height: 20px;
        opacity: 0.3;
    }

    .pageWrapper {
        min-height: 100vh;
        background: #f5f5f5;
    }

    .alert {
        border-radius: 8px;
        font-size: 0.9rem;
    }

    .fw-medium {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .fw-medium:hover {
        color: #d4a843 !important;
        text-decoration: underline !important;
    }

    @media (max-width: 767px) {
        .loginImg {
            min-height: 250px;
        }
        .watchFrame {
            height: 250px;
        }
        .resetTitle {
            font-size: 2rem !important;
            line-height: 2.5rem !important;
        }
        .loginWrap {
            margin: 15px;
        }
        .custom-login-btn.submit-btn-m700 {
            width: 140px;
            height: 48px;
            font-size: 16px;
            line-height: 19px;
        }
        .ctaBtn {
            padding: 12px 25px;
            font-size: 0.75rem;
        }
        .emailInputBox {
            height: 42px;
            font-size: 13px;
        }
    }

    @media (max-width: 576px) {
        .loginImg {
            min-height: 200px;
        }
        .watchFrame {
            height: 200px;
        }
        .resetTitle {
            font-size: 1.2rem !important;
            line-height: 1.4 !important;
        }
        .custom-login-btn.submit-btn-m700 {
            width: 120px;
            height: 38px;
            font-size: 13px;
            line-height: 19px;
        }
        .emailInputBox {
            height: 38px;
            font-size: 12px;
        }
    }
</style>
@endpush