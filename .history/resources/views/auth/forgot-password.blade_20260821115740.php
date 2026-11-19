@extends('layouts.front')

@section('title', 'Forgot Password')

@section('content')
<main class="pageWrapper d-flex">

   <section class="w-100 d-flex position-relative">
      
      <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

      <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

         <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

            <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center">
               <h1 class="sectTitle mb-4 d-inline-flex align-items-center gap-3">Forgot Password</h1>
               <small class="mb-3 d-block lh-sm">Enter your email id below to receive a password reset link.</small>

               @if (session('status'))
                   <div class="alert alert-success mb-3" role="alert">
                       {{ session('status') }}
                   </div>
               @endif

               <form method="POST" action="{{ route('password.email') }}" class="row g-4 formFormat">
                   @csrf

                  <div class="col-12">
                     <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
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
                     <button type="submit" class="custom-login-btn">SUBMIT</button>
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

    .formFormat .form-control {
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .formFormat .form-control:focus {
        border-color: #d4a843;
        box-shadow: 0 0 0 0.2rem rgba(212, 168, 67, 0.25);
    }

    .formFormat .form-control.is-invalid {
        border-color: #dc3545;
    }

    .formFormat .form-control.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Custom login button matching the image */
    .custom-login-btn {
        background: #d4a843;
        color: #fff;
        border: none;
        border-radius: 30px;
        width: 180px;
        height: 52px;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        transition: all 0.3s ease;
        margin: 0 auto;
        display: block;
        outline: none;
        box-shadow: none;
        cursor: pointer;
    }

    .custom-login-btn:hover {
        background: #b8922f;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(212, 168, 67, 0.3);
    }

    .sectTitle {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        color: #1a1a1a;
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

    /* Back to Sign In link */
    .fw-medium {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.3s ease;
        font-weight: 500;
    }

    .fw-medium:hover {
        color: #d4a843 !important;
        text-decoration: underline !important;
    }

    /* Small text styling */
    .lh-sm {
        color: #6c757d;
        font-size: 0.95rem;
    }

    @media (max-width: 767px) {
        .loginImg {
            min-height: 250px;
        }
        .watchFrame {
            height: 250px;
        }
        .sectTitle {
            font-size: 1.5rem;
        }
        .loginWrap {
            margin: 15px;
        }
        .custom-login-btn {
            width: 150px;
            height: 46px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .loginImg {
            min-height: 200px;
        }
        .watchFrame {
            height: 200px;
        }
        .custom-login-btn {
            width: 130px;
            height: 42px;
            font-size: 0.75rem;
        }
    }
</style>
@endpush