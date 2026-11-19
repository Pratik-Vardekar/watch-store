@extends('layouts.front')
@section('content')

<main class="pageWrapper">

   <section class="py-5">
      <div class="container">

          <div class="row justify-content-between mb-5">
              @include('frontend.partials.myaccount_nav', ['active' => 'password', 'user' => $user])

              <div class="col-lg-9 mb-lg-4">

                  <h2 class="sectTitle lh-1 mb-4">Change Password</h2>

                  @if(session('success'))
                      <div class="alert alert-success">{{ session('success') }}</div>
                  @endif
                  @if(session('error'))
                      <div class="alert alert-danger">{{ session('error') }}</div>
                  @endif
                  @if($errors->any())
                      <div class="alert alert-danger">
                          <ul class="mb-0 ps-3">
                              @foreach($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
                  @endif

                  <div style="max-width:400px;">
                     <form action="{{ route('profile.changepassword.update') }}" method="POST" class="row g-4 formFormat" id="changePasswordForm">
                        @csrf
                        <div class="col-12">
                           <small class="">Current Password <span class="txtRed">*</span></small>
                           <div class="position-relative">
                              <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" id="current_password" required/>
                              <button type="button" class="passwordBtn" data-bs-toggle="button"></button>
                           </div>
                           @error('current_password')
                               <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                           @enderror
                        </div>

                        <div class="col-12">
                           <small class="">New Password <span class="txtRed">*</span></small>
                           <div class="position-relative">
                              <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required/>
                              <button type="button" class="passwordBtn" data-bs-toggle="button"></button>
                           </div>
                           <div class="password-requirements mt-2">
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
                           <small class="">Re-enter New Password <span class="txtRed">*</span></small>
                           <div class="position-relative">
                              <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" required/>
                              <button type="button" class="passwordBtn" data-bs-toggle="button"></button>
                           </div>
                           @error('password_confirmation')
                               <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                           @enderror
                        </div>

                        <div class="col-12 mt-4 text-center">
                           <button type="submit" class="ctaBtn">
                              <span class="btnTxt">Save Changes</span>
                              <span class="arw"></span>
                           </button>
                        </div>
                     </form>
                  </div>

              </div>
          </div>

      </div>
   </section>

</main>

@push('styles')
<style>
    .ctaBtn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 35px;
        background: #bd9b7c;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
    }

    .ctaBtn:hover {
        background: #a8866c;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(189, 155, 124, 0.3);
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

    .sectTitle {
        font-family: 'Manrope', sans-serif;
        font-weight: 800;
        font-size: 2rem;
        color: #3e3e47;
    }

    .txtRed {
        color: #dc3545;
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
        height: 42px;
    }

    .formFormat .form-control:focus {
        border-color: #bd9b7c;
        box-shadow: 0 0 0 0.1rem #f7ede5;
        outline: none;
    }

    .formFormat .form-control.is-invalid {
        border-color: #dc3545;
    }

    .formFormat .form-control.is-invalid:focus {
        box-shadow: 0 0 0 0.11rem rgba(220, 53, 69, 0.08);
    }

    .position-relative {
        position: relative;
    }

    .passwordBtn {
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

    .passwordBtn:hover {
        opacity: 1;
    }

    .passwordBtn.active {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z'/%3E%3Ccircle cx='12' cy='12' r='3.5'/%3E%3C/svg%3E");
    }

    /* Password Requirements Styling */
    .password-requirements {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
        margin-bottom: 10px;
        line-height: 1.8;
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
        width: 18px;
        height: 18px;
        line-height: 18px;
        text-align: center;
        border-radius: 50%;
        font-size: 10px;
        font-weight: bold;
        transition: all 0.3s ease;
        flex-shrink: 0;
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

    .alert {
        border-radius: 6px;
        font-size: 0.9rem;
        padding: 10px 15px;
    }

    .alert-danger ul {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    @media (max-width: 767px) {
        .sectTitle {
            font-size: 1.5rem;
        }
        .ctaBtn {
            padding: 10px 25px;
            font-size: 0.75rem;
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
        const passwordButtons = document.querySelectorAll('.passwordBtn');
        
        passwordButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const input = this.closest('.position-relative').querySelector('input[type="password"], input[type="text"]');
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.add('active');
                    } else {
                        input.type = 'password';
                        this.classList.remove('active');
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

        // Form validation
        const form = document.getElementById('changePasswordForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const currentPassword = document.getElementById('current_password');
                const password = document.getElementById('password');
                const confirmPassword = document.getElementById('password_confirmation');

                // Validate current password is not empty
                if (currentPassword && currentPassword.value.trim() === '') {
                    e.preventDefault();
                    currentPassword.classList.add('is-invalid');
                    alert('Please enter your current password.');
                    currentPassword.focus();
                    return false;
                }

                // Validate new password with all requirements
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
        document.querySelectorAll('.form-control').forEach(input => {
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
