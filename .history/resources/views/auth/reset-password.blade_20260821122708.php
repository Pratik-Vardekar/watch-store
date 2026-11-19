@extends('layouts.front')

@section('title', 'Set New Password')

@section('content')
<main class="pageWrapper d-flex">

   <section class="w-100 d-flex position-relative">
      
      <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

      <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

         <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

            <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center" style="background: #fff;">
               <h1 class="resetTitle mb-3 d-inline-flex align-items-center gap-3">Set New Password</h1>
               <small class="mb-4 d-block lh-sm custom-desc">
                   Enter your email and choose a new password for your account.
               </small>

               @if (session('status'))
                   <div class="alert alert-success mb-3" role="alert">
                       {{ session('status') }}
                   </div>
               @endif

               @if ($errors->any())
                   <div class="alert alert-danger mb-3" role="alert">
                       <ul class="mb-0 ps-3">
                           @foreach ($errors->all() as $error)
                               <li>{{ $error }}</li>
                           @endforeach
                       </ul>
                   </div>
               @endif

               <form method="POST" action="{{ route('password.store') }}" class="row g-3 formFormat" id="setNewPasswordForm">
                   @csrf

                   <input type="hidden" name="token" value="{{ $request->route('token') }}">

                  <div class="col-12">
                     <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror emailInputBox" 
                        name="email" 
                        id="email" 
                        placeholder="Email id"
                        value="{{ old('email', $request->email) }}"
                        required
                        autofocus
                        autocomplete="username"
                        style="background:#fff;"
                     />
                     @error('email')
                         <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                     @enderror
                  </div>

                  <div class="col-12">
                     <div class="position-relative">
                        <input 
                           type="password" 
                           class="form-control @error('password') is-invalid @enderror emailInputBox" 
                           name="password" 
                           id="password" 
                           placeholder="New Password"
                           required
                           autocomplete="new-password"
                           minlength="8"
                           style="background:#fff;"
                        />
                        <button type="button" class="passwordBtn" data-bs-toggle="button" tabindex="-1" aria-label="Show/Hide password"></button>
                     </div>
                     <small class="text-muted" style="font-size: 11px; display: block; margin-top: 3px; margin-bottom: 5px;">
                         Password must be at least 8 characters long.
                     </small>
                     @error('password')
                         <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                     @enderror
                  </div>

                  <div class="col-12">
                     <div class="position-relative">
                        <input 
                           type="password" 
                           class="form-control @error('password_confirmation') is-invalid @enderror emailInputBox" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           placeholder="Confirm New Password"
                           required
                           autocomplete="new-password"
                           minlength="8"
                           style="background:#fff;"
                        />
                        <button type="button" class="passwordBtn" data-bs-toggle="button" tabindex="-1" aria-label="Show/Hide password"></button>
                     </div>
                     @error('password_confirmation')
                         <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                     @enderror
                  </div>

                  <div class="col-12 text-center">
                    <button type="submit" class="custom-login-btn submit-btn-m700">Reset Password</button>
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

    .alert-danger ul {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .progress {
        height: 20px;
        opacity: 0.1;
    }

    .pageWrapper {
        min-height: 100vh;
        background: #f5f5f5;
    }

    .text-muted {
        color: #6c757d !important;
        font-family: 'Manrope', sans-serif;
    }

    /* Password toggle button */
    .passwordBtn {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        z-index: 3;
        transition: opacity 0.2s ease;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        opacity: 0.7;
    }

    .passwordBtn:hover {
        opacity: 1;
    }

    .passwordBtn.active {
        opacity: 1;
    }

    .position-relative {
        position: relative;
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

@push('scripts')
<script>
    // Simple thin/regular eye icon SVG
    const eyeSvg = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23666\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z\'/%3E%3Ccircle cx=\'12\' cy=\'12\' r=\'3.5\'/%3E%3C/svg%3E") center/contain no-repeat';

    const eyeSlashSvg = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%23666\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94\'/%3E%3Cpath d=\'M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M14.11 14.12a3 3 0 1 1-4.24-4.24\'/%3E%3Cline x1=\'1\' y1=\'1\' x2=\'23\' y2=\'23\'/%3E%3C/svg%3E") center/contain no-repeat';

    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle functionality
        const passwordFields = document.querySelectorAll('.position-relative');
        passwordFields.forEach(field => {
            const input = field.querySelector('input[type="password"], input[type="text"]');
            const btn = field.querySelector('.passwordBtn');
            if (!input || !btn) return;

            // Set initial styles
            btn.style.background = eyeSlashSvg;
            btn.style.width = "24px";
            btn.style.height = "24px";
            btn.style.border = "none";
            btn.style.position = "absolute";
            btn.style.top = "50%";
            btn.style.right = "12px";
            btn.style.transform = "translateY(-50%)";
            btn.style.zIndex = "3";
            btn.style.cursor = "pointer";
            btn.style.backgroundColor = "transparent";
            btn.style.padding = "0";
            btn.style.display = "block";

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const input = this.closest('.position-relative').querySelector('input[type="password"], input[type="text"]');
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.add('active');
                        this.style.background = eyeSvg;
                    } else {
                        input.type = 'password';
                        this.classList.remove('active');
                        this.style.background = eyeSlashSvg;
                    }
                }
            });
        });

        // Email validation on blur
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value.length > 0 && !emailRegex.test(this.value)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            emailInput.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        }

        // Form validation
        const form = document.getElementById('setNewPasswordForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const email = document.getElementById('email');
                const password = document.getElementById('password');
                const confirmPassword = document.getElementById('password_confirmation');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                // Validate email
                if (email && !emailRegex.test(email.value)) {
                    e.preventDefault();
                    email.classList.add('is-invalid');
                    alert('Please enter a valid email address.');
                    email.focus();
                    return false;
                }

                // Validate password length
                if (password && password.value.length < 8) {
                    e.preventDefault();
                    password.classList.add('is-invalid');
                    alert('Password must be at least 8 characters long.');
                    password.focus();
                    return false;
                }

                // Validate password match
                if (password && confirmPassword && password.value !== confirmPassword.value) {
                    e.preventDefault();
                    confirmPassword.classList.add('is-invalid');
                    alert('Passwords do not match. Please try again.');
                    confirmPassword.focus();
                    return false;
                }

                return true;
            });
        }

        // Clear validation on input
        document.querySelectorAll('.emailInputBox').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    });
</script>
@endpush