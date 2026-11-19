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
                        style="background:#e8f0fe;"
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
                           style="background:#e8f0fe;"
                        />
                        <button type="button" class="passwordToggle" aria-label="Show/Hide password"></button>
                     </div>
                     <div class="password-requirements mt-2" style="font-size: 12px; color: #6c757d;">
                         <div class="req-item" id="req-length">
                             <span class="req-icon">✕</span> Minimum 8 characters
                         </div>
                         <div class="req-item" id="req-uppercase">
                             <span class="req-icon">✕</span> At least 1 uppercase (A-Z)
                         </div>
                         <div class="req-item" id="req-lowercase">
                             <span class="req-icon">✕</span> At least 1 lowercase (a-z)
                         </div>
                         <div class="req-item" id="req-number">
                             <span class="req-icon">✕</span> At least 1 number (0-9)
                         </div>
                         <div class="req-item" id="req-special">
                             <span class="req-icon">✕</span> At least 1 special character (@ # $ % &amp; * !)
                         </div>
                     </div>
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
                           style="background:#e8f0fe;"
                        />
                        <button type="button" class="passwordToggle" aria-label="Show/Hide password"></button>
                     </div>
                     @error('password_confirmation')
                         <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                     @enderror
                  </div>

                  <div class="col-12 text-center">
                    <button type="submit" class="ctaBtn">Reset Password</button>
                  </div>

                  <div class="col-12 mt-3 text-center">
                     <a href="{{ route('login') }}" class="fw-medium">Back to Sign In</a>
                  </div>
               </form>
            </div>

            <div class="col-md-6 col-12 d-flex justify-content-center align-items-center bgGray loginImg">
               <iframe src="{{ asset('frontend/assets/watch/index.html') }}" class="watchFrame" title="Watch Display"></iframe>
            </div>

         </div>
            
      </div>

   </section>         

</main>
@endsection

@push('styles')
<style>
   
    /* Password Toggle Button */
    .passwordToggle {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        z-index: 5;
        opacity: 0.6;
        transition: opacity 0.2s ease;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94'/%3E%3Cpath d='M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19'/%3E%3Cpath d='M14.12 14.12a3 3 0 1 1-4.24-4.24'/%3E%3Cline x1='1' y1='1' x2='23' y2='23'/%3E%3C/svg%3E");
    }

    .passwordToggle:hover {
        opacity: 1;
    }

    .passwordToggle.show {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z'/%3E%3Ccircle cx='12' cy='12' r='3.5'/%3E%3C/svg%3E");
    }

    /* Password Requirements Styling */
    .password-requirements {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
        margin-bottom: 10px;
        line-height: 1.6;
    }

    .req-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #999;
        transition: all 0.3s ease;
    }

    .req-item .req-icon {
        display: inline-block;
        width: 16px;
        height: 16px;
        line-height: 16px;
        text-align: center;
        border-radius: 50%;
        font-size: 10px;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .req-item.valid {
        color: #28a745;
    }

    .req-item.valid .req-icon {
        color: #28a745;
        background: #d4edda;
    }

    .req-item.invalid .req-icon {
        color: #dc3545;
        background: #f8d7da;
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
        .password-requirements {
            font-size: 10px;
        }
        .req-item {
            font-size: 10px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle functionality
        const toggleButtons = document.querySelectorAll('.passwordToggle');
        
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const input = this.closest('.position-relative').querySelector('input[type="password"], input[type="text"]');
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.add('show');
                    } else {
                        input.type = 'password';
                        this.classList.remove('show');
                    }
                }
            });
        });

        // Password validation function
        function validatePassword(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[@#$%&*!]/.test(password)
            };
            return requirements;
        }

        // Update password requirements UI
        function updatePasswordRequirements(password) {
            const reqs = validatePassword(password);
            
            const reqMap = {
                'req-length': reqs.length,
                'req-uppercase': reqs.uppercase,
                'req-lowercase': reqs.lowercase,
                'req-number': reqs.number,
                'req-special': reqs.special
            };

            Object.keys(reqMap).forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    const icon = element.querySelector('.req-icon');
                    if (reqMap[id]) {
                        element.classList.remove('invalid');
                        element.classList.add('valid');
                        icon.textContent = '✓';
                    } else {
                        element.classList.remove('valid');
                        element.classList.add('invalid');
                        icon.textContent = '✕';
                    }
                }
            });

            // Check if all requirements are met
            const allValid = Object.values(reqMap).every(v => v === true);
            const passwordInput = document.getElementById('password');
            if (passwordInput) {
                if (allValid && passwordInput.value.length > 0) {
                    passwordInput.classList.remove('is-invalid');
                } else if (passwordInput.value.length > 0) {
                    passwordInput.classList.add('is-invalid');
                }
            }
            return allValid;
        }

        // Real-time password validation
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                updatePasswordRequirements(this.value);
            });
        }

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

                // Validate password with all requirements
                const reqs = validatePassword(password.value);
                const allValid = Object.values(reqs).every(v => v === true);
                
                if (password && !allValid) {
                    e.preventDefault();
                    password.classList.add('is-invalid');
                    alert('Password must meet all requirements:\n- Minimum 8 characters\n- At least 1 uppercase (A-Z)\n- At least 1 lowercase (a-z)\n- At least 1 number (0-9)\n- At least 1 special character (@ # $ % & * !)');
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

        // Initialize password requirements (show as invalid initially)
        const initialPassword = document.getElementById('password');
        if (initialPassword) {
            updatePasswordRequirements('');
        }
    });
</script>
@endpush