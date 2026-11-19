@extends('layouts.front')

@section('title', 'Forgot Password')

@section('content')
<main class="pageWrapper d-flex">

   <section class="w-100 d-flex position-relative">
      
      <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

      <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

         <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

            <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center" style="background: #fff;">
               <h1 class="resetTitle mb-3 d-inline-flex align-items-center gap-3">Reset Password</h1>
               <small class="mb-4 d-block lh-sm custom-desc">
                   Enter your email id below to receive a password reset link.
               </small>

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
                        placeholder="Email id"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        style="background:#fff;"
                     />
                     @error('email')
                         <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                     @enderror
                  </div>

                  <div class="col-12 text-center">
                    <button type="submit" class="custom-login-btn submit-btn-m700">Submit</button>
                  </div>

                  <div class="col-12 mt-3 text-center">
                     <a href="{{ route('login') }}" class="fw-medium">Back to Sign In</a>
                  </div>
               </form>
            </div>

            <div class="col-md-6 col-12 d-flex justify-content-center align-items-center bgGray loginImg" style="background: #fafbfc;">
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
        max-width: 970px;
        min-width: 280px;
        width: 100%;
        min-height: 520px;
    }

    .loginImg {
        min-height: 420px;
        background: #fafbfc !important;
        /* Ensures side border split like image */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .watchFrame {
        width: 250px;
        height: auto;
        aspect-ratio: .9/1;
        border: none;
        background: transparent;
        display: block;
        max-width: 100%;
        margin: 0 auto;
    }

    .resetTitle {
        font-family: 'Manrope', sans-serif;
        font-weight: 800;
        font-size: 2.2rem;
        line-height: 1.1;
        color: #3e3e47;
        margin-bottom: 0.5rem !important;
        letter-spacing: 0;
    }

    .custom-desc {
        font-size: 13px;
        color: #353535;
        font-family: 'Manrope',sans-serif;
        font-weight: 400;
        margin-bottom: 1.1rem;
        margin-top: 0.3rem;
        letter-spacing: 0;
        line-height: 1.1;
    }

    .formFormat {
        width: 100%;
        margin-top: 0.7rem;
    }

    .emailInputBox {
        font-family: 'Manrope', sans-serif;
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 22px;
        color: rgb(33, 37, 41);
        border: 1px solid #eee;
        border-radius: 6px;
        padding: 10px 12px 10px 10px;
        background: #fff;
        transition: all 0.3s;
        height: 42px;
        box-shadow: none;
        outline: none;
        margin-bottom: 20px;
    }

    .emailInputBox::placeholder {
        color: #b9b7b7;
        font-family: 'Manrope', sans-serif;
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 22px;
        letter-spacing: 0;
        opacity: 1;
    }

    .formFormat .form-control {
        padding: 10px 12px 10px 10px;
        border-radius: 6px;
        border: 1px solid #eee;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: none;
        font-family: 'Manrope', sans-serif;
        background: #fff;
    }

    .emailInputBox:focus,
    .formFormat .form-control:focus {
        border-color: #bd9b7c;
        box-shadow: 0 0 0 0.1rem #f7ede5;
        outline: none;
    }

    .formFormat .form-control.is-invalid,
    .emailInputBox.is-invalid {
        border-color: #dc3545;
    }

    .formFormat .form-control.is-invalid:focus,
    .emailInputBox.is-invalid:focus {
        box-shadow: 0 0 0 0.11rem rgba(220, 53, 69, 0.08);
    }

    .custom-login-btn.submit-btn-m700 {
        background: #bd9b7c;
        color: rgb(255, 255, 255);
        border: none;
        border-radius: 8px;
        width: 210px;
        height: 48px;
        font-family: 'Manrope', sans-serif;
        font-style: normal;
        font-weight: 700;
        font-size: 17px;
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

    .fw-medium {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.3s ease;
        font-size: 13px;
        font-family: 'Manrope',sans-serif;
        font-weight: 400;
    }

    .fw-medium:hover {
        color: #bd9b7c !important;
        text-decoration: underline !important;
    }

    .alert {
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .progress {
        height: 20px;
        opacity: 0.1;
    }

    .pageWrapper {
        min-height: 100vh;
        background: #f5f5f5;
    }

    @media (max-width: 991px) {
        .loginImg {
            min-height: unset;
        }
        .resetTitle {
            font-size: 1.6rem !important;
            line-height: 2.1rem !important;
        }
        .loginWrap {
            margin: 8px;
            min-height: unset;
        }
        .custom-login-btn.submit-btn-m700 {
            width: 140px;
            height: 42px;
            font-size: 15px;
        }
        .emailInputBox {
            height: 38px;
            font-size: 12.5px;
        }
        .watchFrame {
            width: 155px;
        }
    }
    @media (max-width: 767px) {
        .loginWrap {
            margin: 5px;
        }
        .loginImg {
            min-height: 180px;
            padding: 16px 0;
            background: #fafbfc;
        }
        .watchFrame {
            width: 120px;
        }
        .resetTitle {
            font-size: 1.1rem !important;
            line-height: 1.5rem !important;
        }
        .custom-login-btn.submit-btn-m700 {
            width: 100px;
            height: 34px;
            font-size: 12px;
        }
        .emailInputBox {
            height: 34px;
            font-size: 11.5px;
        }
        .custom-desc {
            font-size: 11px;
        }
    }
</style>
@endpush