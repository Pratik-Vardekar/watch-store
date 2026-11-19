@extends('layouts.front')

@section('title', 'Reset Password')

@section('content')

<main class="pageWrapper d-flex">

    <section class="w-100 d-flex position-relative">
        
        <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

        <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

            <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

                <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center">
                    <h1 class="sectTitle mb-4 d-inline-flex align-items-center gap-3">Reset Password</h1>
                    <small class="mb-3 d-block lh-sm">Enter your email id below to receive a password reset link.</small>

                    <form method="POST" action="{{ route('password.email') }}" class="row g-3 formFormat">
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
                            />
                            @error('email')
                                <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="ctaBtn">
                                <span class="btnTxt">Submit</span>
                                <span class="arw"></span>
                            </button>
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
    }

    @media (max-width: 576px) {
        .loginImg {
            min-height: 200px;
        }
        
        .watchFrame {
            height: 200px;
        }
    }
</style>
@endpush