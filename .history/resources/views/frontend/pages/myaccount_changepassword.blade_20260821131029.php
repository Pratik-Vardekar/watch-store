@extends('layouts.front')

@section('content')

<main class="pageWrapper passwordPage">

    <section class="py-5">
        <div class="container">

            <div class="row justify-content-between mb-5">

                {{-- My Account Navigation --}}
                @include('frontend.partials.myaccount_nav', [
                    'active' => 'password',
                    'user' => $user
                ])

                {{-- Change Password Content --}}
                <div class="col-lg-9 mb-lg-4">

                    <h2 class="sectTitle lh-1 mb-4">
                        Change Password
                    </h2>

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="changePasswordWrap">

                        <form
                            action="{{ route('profile.changepassword.update') }}"
                            method="POST"
                            class="row g-4 formFormat"
                            id="changePasswordForm"
                        >

                            @csrf

                            {{-- Current Password --}}
                            <div class="col-12">

                                <label for="current_password" class="passwordLabel">
                                    Current Password
                                    <span class="txtRed">*</span>
                                </label>

                                <div class="password-field">

                                    <input
                                        type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        name="current_password"
                                        id="current_password"
                                        autocomplete="current-password"
                                        required
                                    >

                                    <button
                                        type="button"
                                        class="passwordBtn"
                                        aria-label="Show current password"
                                        aria-pressed="false"
                                    ></button>

                                </div>

                                @error('current_password')
                                    <div class="text-danger validationMessage">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- New Password --}}
                            <div class="col-12">

                                <label for="password" class="passwordLabel">
                                    New Password
                                    <span class="txtRed">*</span>
                                </label>

                                <div class="password-field">

                                    <input
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password"
                                        id="password"
                                        autocomplete="new-password"
                                        required
                                    >

                                    <button
                                        type="button"
                                        class="passwordBtn"
                                        aria-label="Show new password"
                                        aria-pressed="false"
                                    ></button>

                                </div>


                                {{-- Password Requirements --}}
                                <div class="password-requirements">

                                    <div class="req-item" id="req-length">
                                        <span class="req-icon">✕</span>
                                        <span>Minimum 8 characters</span>
                                    </div>

                                    <div class="req-item" id="req-uppercase">
                                        <span class="req-icon">✕</span>
                                        <span>At least 1 uppercase (A-Z)</span>
                                    </div>

                                    <div class="req-item" id="req-lowercase">
                                        <span class="req-icon">✕</span>
                                        <span>At least 1 lowercase (a-z)</span>
                                    </div>

                                    <div class="req-item" id="req-number">
                                        <span class="req-icon">✕</span>
                                        <span>At least 1 number (0-9)</span>
                                    </div>

                                    <div class="req-item" id="req-special">
                                        <span class="req-icon">✕</span>
                                        <span>At least 1 special character (@ # $ % &amp; * !)</span>
                                    </div>

                                </div>

                                @error('password')
                                    <div class="text-danger validationMessage">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Confirm Password --}}
                            <div class="col-12">

                                <label for="password_confirmation" class="passwordLabel">
                                    Re-enter New Password
                                    <span class="txtRed">*</span>
                                </label>

                                <div class="password-field">

                                    <input
                                        type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        autocomplete="new-password"
                                        required
                                    >

                                    <button
                                        type="button"
                                        class="passwordBtn"
                                        aria-label="Show password confirmation"
                                        aria-pressed="false"
                                    ></button>

                                </div>

                                <div
                                    id="passwordMatchMessage"
                                    class="password-match-message"
                                ></div>

                                @error('password_confirmation')
                                    <div class="text-danger validationMessage">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Submit --}}
                            <div class="col-12 mt-4 text-center">

                                <button
                                    type="submit"
                                    class="ctaBtn"
                                    id="savePasswordBtn"
                                >
                                    <span class="btnTxt">
                                        Save Changes
                                    </span>

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

@endsection


{{-- =========================================================
   STYLES
========================================================= --}}

@push('styles')

<style>

    /* =====================================================
       PAGE
    ===================================================== */

    .passwordPage {
        display: block;
        width: 100%;
        min-height: 100%;
        transform: none !important;
    }


    /* =====================================================
       TITLE
    ===================================================== */

    .passwordPage .sectTitle {
        font-family: 'Manrope', sans-serif;
        font-weight: 800;
        font-size: 2rem;
        line-height: 1.2;
        color: #3e3e47;
    }


    /* =====================================================
       FORM WRAPPER
    ===================================================== */

    .changePasswordWrap {
        width: 100%;
        max-width: 420px;
    }


    /* =====================================================
       LABEL
    ===================================================== */

    .passwordLabel {
        display: block;
        margin-bottom: 7px;
        font-family: 'Manrope', sans-serif;
        font-size: 0.875rem;
        font-weight: 500;
        color: #3e3e47;
    }

    .txtRed {
        color: #dc3545;
    }


    /* =====================================================
       FORM INPUT
    ===================================================== */

    .formFormat .form-control {
        width: 100%;
        height: 44px;

        padding: 10px 45px 10px 12px;

        border-radius: 6px;
        border: 1px solid #eeeeee;

        background: #ffffff;

        color: #3e3e47;

        font-family: 'Manrope', sans-serif;
        font-size: 0.95rem;

        box-shadow: none;

        transition:
            border-color 0.3s ease,
            box-shadow 0.3s ease;
    }

    .formFormat .form-control:focus {
        border-color: #bd9b7c;

        box-shadow:
            0 0 0 0.1rem rgba(189, 155, 124, 0.15);

        outline: none;
    }

    .formFormat .form-control.is-invalid {
        border-color: #dc3545;
    }

    .formFormat .form-control.is-invalid:focus {
        border-color: #dc3545;

        box-shadow:
            0 0 0 0.1rem rgba(220, 53, 69, 0.08);
    }


    /* =====================================================
       PASSWORD FIELD

       IMPORTANT:
       Do NOT use .position-relative globally.
       This class is scoped only to password fields.
    ===================================================== */

    .password-field {
        position: relative;
        width: 100%;
    }


    /* =====================================================
       PASSWORD SHOW/HIDE BUTTON
    ===================================================== */

    .passwordBtn {
        position: absolute;

        top: 50%;
        right: 12px;

        width: 24px;
        height: 24px;

        padding: 0;

        border: 0;
        outline: none;

        transform: translateY(-50%);

        background-color: transparent;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;

        cursor: pointer;

        opacity: 0.6;

        z-index: 5;

        transition: opacity 0.2s ease;
    }

    .passwordBtn:hover,
    .passwordBtn:focus {
        opacity: 1;
    }


    /* Eye OFF */
    .passwordBtn {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94'/%3E%3Cpath d='M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19'/%3E%3Cpath d='M14.12 14.12a3 3 0 1 1-4.24-4.24'/%3E%3Cline x1='1' y1='1' x2='23' y2='23'/%3E%3C/svg%3E");
    }


    /* Eye ON */
    .passwordBtn.active {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z'/%3E%3Ccircle cx='12' cy='12' r='3.5'/%3E%3C/svg%3E");
    }


    /* =====================================================
       PASSWORD REQUIREMENTS
    ===================================================== */

    .password-requirements {
        margin-top: 10px;
        margin-bottom: 5px;

        font-family: 'Manrope', sans-serif;
        font-size: 12px;
        line-height: 1.7;
    }

    .req-item {
        display: flex;
        align-items: center;

        gap: 7px;

        color: #999999;

        font-size: 12px;

        transition:
            color 0.25s ease,
            opacity 0.25s ease;
    }

    .req-icon {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        width: 17px;
        height: 17px;

        flex: 0 0 17px;

        border-radius: 50%;

        font-size: 9px;
        font-weight: 700;

        color: #dc3545;
        background: #f8d7da;

        transition:
            color 0.25s ease,
            background-color 0.25s ease;
    }


    /* Valid requirement */

    .req-item.valid {
        color: #28a745;
    }

    .req-item.valid .req-icon {
        color: #28a745;
        background: #d4edda;
    }


    /* Invalid requirement */

    .req-item.invalid {
        color: #dc3545;
    }

    .req-item.invalid .req-icon {
        color: #dc3545;
        background: #f8d7da;
    }


    /* =====================================================
       PASSWORD MATCH
    ===================================================== */

    .password-match-message {
        display: none;

        margin-top: 6px;

        font-family: 'Manrope', sans-serif;
        font-size: 12px;
    }

    .password-match-message.success {
        display: block;
        color: #28a745;
    }

    .password-match-message.error {
        display: block;
        color: #dc3545;
    }


    /* =====================================================
       VALIDATION MESSAGE
    ===================================================== */

    .validationMessage {
        margin-top: 5px;

        font-family: 'Manrope', sans-serif;
        font-size: 0.8rem;
    }


    /* =====================================================
       ALERT
    ===================================================== */

    .passwordPage .alert {
        border-radius: 6px;

        padding: 10px 15px;

        margin-bottom: 20px;

        font-family: 'Manrope', sans-serif;
        font-size: 0.875rem;
    }

    .passwordPage .alert-danger ul {
        list-style: none;

        padding-left: 0;
        margin-bottom: 0;
    }


    /* =====================================================
       SAVE BUTTON
    ===================================================== */

    .ctaBtn {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        gap: 10px;

        padding: 12px 35px;

        min-height: 46px;

        border: none;
        border-radius: 8px;

        background: #bd9b7c;
        color: #ffffff;

        font-family: 'Manrope', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;

        letter-spacing: 1px;
        text-transform: uppercase;

        text-decoration: none;

        cursor: pointer;

        transition:
            background-color 0.3s ease,
            transform 0.3s ease,
            box-shadow 0.3s ease;
    }

    .ctaBtn:hover {
        background: #a8866c;
        color: #ffffff;

        transform: translateY(-2px);

        box-shadow:
            0 10px 30px rgba(189, 155, 124, 0.3);
    }

    .ctaBtn:active {
        transform: translateY(0);
    }

    .ctaBtn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }


    /* =====================================================
       ARROW
    ===================================================== */

    .arw {
        display: inline-block;

        width: 20px;
        height: 20px;

        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='5' y1='12' x2='19' y2='12'/%3E%3Cpolyline points='12 5 19 12 12 19'/%3E%3C/svg%3E");

        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;

        transition: transform 0.3s ease;
    }

    .ctaBtn:hover .arw {
        transform: translateX(5px);
    }


    /* =====================================================
       MOBILE
    ===================================================== */

    @media (max-width: 991.98px) {

        .changePasswordWrap {
            max-width: 100%;
        }

    }

    @media (max-width: 767.98px) {

        .passwordPage .sectTitle {
            font-size: 1.5rem;
        }

        .changePasswordWrap {
            max-width: 100%;
        }

        .password-requirements {
            font-size: 11px;
        }

        .req-item {
            font-size: 11px;
        }

        .ctaBtn {
            padding: 10px 25px;

            min-height: 42px;

            font-size: 0.75rem;
        }

    }

</style>

@endpush


{{-- =========================================================
   SCRIPTS
========================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Password Show / Hide
    |--------------------------------------------------------------------------
    */

    const passwordButtons = document.querySelectorAll('.passwordBtn');

    passwordButtons.forEach(function (button) {

        button.addEventListener('click', function (event) {

            event.preventDefault();

            const wrapper = this.closest('.password-field');

            if (!wrapper) {
                return;
            }

            const input = wrapper.querySelector('input');

            if (!input) {
                return;
            }

            if (input.type === 'password') {

                input.type = 'text';

                this.classList.add('active');

                this.setAttribute('aria-pressed', 'true');

            } else {

                input.type = 'password';

                this.classList.remove('active');

                this.setAttribute('aria-pressed', 'false');

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Password Validation
    |--------------------------------------------------------------------------
    */

    function validatePassword(password) {

        return {
            length: password.length >= 8,

            uppercase: /[A-Z]/.test(password),

            lowercase: /[a-z]/.test(password),

            number: /[0-9]/.test(password),

            special: /[@#$%&*!]/.test(password)
        };

    }


    /*
    |--------------------------------------------------------------------------
    | Update Password Requirement UI
    |--------------------------------------------------------------------------
    */

    function updatePasswordRequirements(password) {

        const requirements = validatePassword(password);

        const requirementMap = {

            'req-length': requirements.length,

            'req-uppercase': requirements.uppercase,

            'req-lowercase': requirements.lowercase,

            'req-number': requirements.number,

            'req-special': requirements.special

        };


        Object.keys(requirementMap).forEach(function (id) {

            const element = document.getElementById(id);

            if (!element) {
                return;
            }

            const icon = element.querySelector('.req-icon');

            if (!icon) {
                return;
            }


            if (requirementMap[id]) {

                element.classList.remove('invalid');

                element.classList.add('valid');

                icon.textContent = '✓';

            } else {

                element.classList.remove('valid');

                element.classList.add('invalid');

                icon.textContent = '✕';

            }

        });


        return Object.values(requirementMap).every(function (value) {

            return value === true;

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Password Match Validation
    |--------------------------------------------------------------------------
    */

    function updatePasswordMatch() {

        const password = document.getElementById('password');

        const confirmation = document.getElementById('password_confirmation');

        const message = document.getElementById('passwordMatchMessage');


        if (!password || !confirmation || !message) {
            return false;
        }


        if (confirmation.value.length === 0) {

            message.className = 'password-match-message';

            message.textContent = '';

            return false;

        }


        if (password.value === confirmation.value) {

            message.className = 'password-match-message success';

            message.textContent = '✓ Passwords match';

            confirmation.classList.remove('is-invalid');

            return true;

        } else {

            message.className = 'password-match-message error';

            message.textContent = '✕ Passwords do not match';

            confirmation.classList.add('is-invalid');

            return false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const passwordInput = document.getElementById('password');

    const confirmationInput =
        document.getElementById('password_confirmation');

    const currentPassword =
        document.getElementById('current_password');

    const form =
        document.getElementById('changePasswordForm');


    /*
    |--------------------------------------------------------------------------
    | New Password Input
    |--------------------------------------------------------------------------
    */

    if (passwordInput) {

        passwordInput.addEventListener('input', function () {

            updatePasswordRequirements(this.value);

            if (confirmationInput && confirmationInput.value.length > 0) {

                updatePasswordMatch();

            }

            this.classList.remove('is-invalid');

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Confirm Password Input
    |--------------------------------------------------------------------------
    */

    if (confirmationInput) {

        confirmationInput.addEventListener('input', function () {

            updatePasswordMatch();

            this.classList.remove('is-invalid');

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Current Password Input
    |--------------------------------------------------------------------------
    */

    if (currentPassword) {

        currentPassword.addEventListener('input', function () {

            this.classList.remove('is-invalid');

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Form Submit Validation
    |--------------------------------------------------------------------------
    */

    if (form) {

        form.addEventListener('submit', function (event) {

            /*
            |--------------------------------------------------------------------------
            | Current Password
            |--------------------------------------------------------------------------
            */

            if (
                !currentPassword ||
                currentPassword.value.trim() === ''
            ) {

                event.preventDefault();

                if (currentPassword) {

                    currentPassword.classList.add('is-invalid');

                    currentPassword.focus();

                }

                alert('Please enter your current password.');

                return false;

            }


            /*
            |--------------------------------------------------------------------------
            | New Password
            |--------------------------------------------------------------------------
            */

            if (!passwordInput) {

                event.preventDefault();

                return false;

            }


            const requirements =
                validatePassword(passwordInput.value);


            const passwordIsValid =
                Object.values(requirements).every(function (value) {

                    return value === true;

                });


            if (!passwordIsValid) {

                event.preventDefault();

                passwordInput.classList.add('is-invalid');

                passwordInput.focus();

                alert(
                    'Password must meet all requirements:\n\n' +
                    '• Minimum 8 characters\n' +
                    '• At least 1 uppercase (A-Z)\n' +
                    '• At least 1 lowercase (a-z)\n' +
                    '• At least 1 number (0-9)\n' +
                    '• At least 1 special character (@ # $ % & * !)'
                );

                return false;

            }


            /*
            |--------------------------------------------------------------------------
            | Confirm Password
            |--------------------------------------------------------------------------
            */

            if (
                !confirmationInput ||
                passwordInput.value !== confirmationInput.value
            ) {

                event.preventDefault();

                if (confirmationInput) {

                    confirmationInput.classList.add('is-invalid');

                    confirmationInput.focus();

                }

                updatePasswordMatch();

                alert(
                    'Passwords do not match. Please enter the same password.'
                );

                return false;

            }


            /*
            |--------------------------------------------------------------------------
            | Prevent Double Submit
            |--------------------------------------------------------------------------
            */

            const submitButton =
                document.getElementById('savePasswordBtn');


            if (submitButton) {

                submitButton.disabled = true;

                const buttonText =
                    submitButton.querySelector('.btnTxt');

                if (buttonText) {

                    buttonText.textContent = 'Saving...';

                }

            }


            return true;

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Initialize Password Requirements
    |--------------------------------------------------------------------------
    */

    updatePasswordRequirements(
        passwordInput ? passwordInput.value : ''
    );


    /*
    |--------------------------------------------------------------------------
    | Existing Confirmation Value
    |--------------------------------------------------------------------------
    */

    if (
        confirmationInput &&
        confirmationInput.value.length > 0
    ) {

        updatePasswordMatch();

    }

});

</script>

@endpush