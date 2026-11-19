{{-- resources/views/frontend/auth/register.blade.php --}}

@extends('layouts.front')

@section('title', 'Sign Up')

@section('content')

<main class="pageWrapper classy-register-page">

    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>

    <section class="py-5 overflow-hidden bg-light position-relative">
        <div class="container position-relative">

            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10 text-center">
                    <nav class="breadcrumb justify-content-center mb-2 fw-semibold bg-transparent px-0">
                        <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                        <span class="breadcrumb-item active">Sign Up</span>
                    </nav>

                    <h1 class="sectTitle fw-bold txtred mb-5 display-5">
                        Create Your Account
                    </h1>
                </div>
            </div>

            <div class="row justify-content-center align-items-center g-5">

                {{-- Registration Form --}}
                <div class="col-lg-6">

                    <div class="register-wrapper bg-white shadow rounded-4 p-4 p-lg-5 border">

                        {{-- Success --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4">
                                {{ session('success') }}
                                <button class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Errors --}}
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>

                                <button class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('register') }}"
                              autocomplete="off">

                            @csrf

                            <div class="row g-3">

                                {{-- Name --}}
                                <div class="col-12">
                                    <div class="form-floating">

                                        <input
                                            type="text"
                                            class="form-control rounded-3 @error('name') is-invalid @enderror"
                                            id="name"
                                            name="name"
                                            placeholder="Full Name"
                                            value="{{ old('name') }}"
                                            required>

                                        <label for="name">
                                            Full Name <span class="text-danger">*</span>
                                        </label>

                                        @error('name')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-12">
                                    <div class="form-floating">

                                        <input
                                            type="email"
                                            class="form-control rounded-3 @error('email') is-invalid @enderror"
                                            id="email"
                                            name="email"
                                            placeholder="Email"
                                            value="{{ old('email') }}"
                                            required>

                                        <label for="email">
                                            Email Address <span class="text-danger">*</span>
                                        </label>

                                        @error('email')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                    </div>
                                </div>

                                {{-- Mobile --}}
                                <div class="col-12">
                                    <div class="form-floating">

                                        <input
                                            type="tel"
                                            class="form-control rounded-3 @error('phone') is-invalid @enderror"
                                            id="phone"
                                            name="phone"
                                            maxlength="10"
                                            inputmode="numeric"
                                            placeholder="Mobile Number"
                                            value="{{ old('phone') ? ltrim(preg_replace('/\D+/', '', preg_replace('/^(\+?91)/', '', old('phone'))), '0') : '' }}">

                                        <label for="phone">
                                            Mobile Number
                                        </label>

                                        @error('phone')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="col-12">

                                    <div class="position-relative">

                                        <div class="form-floating">

                                            <input
                                                type="password"
                                                class="form-control rounded-3 @error('password') is-invalid @enderror"
                                                id="password"
                                                name="password"
                                                placeholder="Password"
                                                required>

                                            <label for="password">
                                                Password <span class="text-danger">*</span>
                                            </label>

                                        </div>

                                        <button
                                            type="button"
                                            class="passwordBtn"
                                            tabindex="-1"></button>

                                        @error('password')
                                            <span class="invalid-feedback d-block">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                    </div>

                                </div>

                                {{-- Confirm Password --}}
                                <div class="col-12">

                                    <div class="position-relative">

                                        <div class="form-floating">

                                            <input
                                                type="password"
                                                class="form-control rounded-3 @error('password_confirmation') is-invalid @enderror"
                                                id="password_confirmation"
                                                name="password_confirmation"
                                                placeholder="Confirm Password"
                                                required>

                                            <label for="password_confirmation">
                                                Confirm Password <span class="text-danger">*</span>
                                            </label>

                                        </div>

                                        <button
                                            type="button"
                                            class="passwordBtn"
                                            tabindex="-1"></button>

                                        @error('password_confirmation')
                                            <span class="invalid-feedback d-block">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                    </div>

                                </div>

                                {{-- Terms --}}
                                <div class="col-12">

                                    <small class="text-muted">

                                        By continuing, you agree to our

                                        <a href="#">Terms of Service</a>

                                        and

                                        <a href="#">Privacy Policy</a>.

                                    </small>

                                </div>

                                {{-- Submit --}}
                                <div class="col-12">

                                    <button
                                        type="submit"
                                        class="btn btn-gradient-primary rounded-pill py-3 w-100">

                                        Create Account

                                    </button>

                                </div>

                                <div class="col-12 text-center">

                                    Already have an account?

                                    <a href="{{ route('login') }}"
                                       class="fw-semibold">

                                        Login

                                    </a>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                {{-- Watch Animation --}}
                <div class="col-lg-5 d-none d-lg-block">

                    <div class="watch-card bg-white shadow rounded-4 p-3">

                        <iframe
                            src="{{ asset('frontend/assets/watch/index.html') }}"
                            class="watchFrame"
                            title="Watch Display">
                        </iframe>

                    </div>

                </div>

            </div>

        </div>
    </section>

</main>

@endsection


@push('styles')

<style>

.classy-register-page{
    background:linear-gradient(110deg,#f7fafd 70%,#fbeee6 100%);
    min-height:100vh;
}

.register-wrapper{
    border-radius:24px;
    box-shadow:0 8px 28px rgba(0,0,0,.08);
}

.watch-card{
    border-radius:24px;
    overflow:hidden;
    height:620px;
}

.watchFrame{
    width:100%;
    height:560px;
    border:none;
}

.sectTitle{
    color:#fa5862;
    letter-spacing:1px;
}

.form-floating label{
    color:#777;
}

.form-control{
    border-radius:12px;
}

.form-control:focus{
    border-color:#fa5862;
    box-shadow:0 0 0 .18rem rgba(250,88,98,.15);
}

.btn-gradient-primary{

    background:linear-gradient(90deg,#ff5858,#f09819);

    border:none;

    color:#fff;

    font-weight:600;

    transition:.3s;

}

.btn-gradient-primary:hover{

    background:linear-gradient(90deg,#f09819,#ff5858);

    color:#fff;

    transform:translateY(-2px);

}

.passwordBtn{

    position:absolute;

    right:18px;

    top:22px;

    width:20px;

    height:20px;

    border:none;

    background:none;

    cursor:pointer;

    z-index:5;

}

.alert{

    border-radius:18px;

}

@media(max-width:991px){

    .watch-card{

        display:none;

    }

}

</style>

@endpush


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded',function(){

    // Auto hide alerts

    setTimeout(function(){

        document.querySelectorAll('.alert').forEach(function(el){

            bootstrap.Alert.getOrCreateInstance(el).close();

        });

    },5000);


    // Password Toggle

    document.querySelectorAll('.passwordBtn').forEach(function(btn){

        btn.addEventListener('click',function(){

            let input=this.parentElement.querySelector('input');

            input.type=input.type==='password'?'text':'password';

        });

    });

});

</script>

@endpush