@extends('layouts.front')

@section('title', 'Confirm Password')

@section('content')
<main class="pageWrapper loginMainWrap">
    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>

    <section class="py-5 mb-5 overflow-hidden">
        <div class="container py-5">
            <div class="rounded-4 animateThis slideTop loginWrap shadow-lg p-5 mx-auto">
                <h1 class="sectTitle mb-3 lh-1 fw-bold animateThis slideTop">Confirm Password</h1>
                <p class="text-secondary mb-4">This is a secure area. Please confirm your password before continuing.</p>

                <form method="POST" action="{{ route('password.confirm') }}" class="row g-3 formFormat animateThis fadeGrow">
                    @csrf
                    <div class="col-12">
                        <div class="position-relative">
                            <input
                                type="password"
                                class="form-control"
                                name="password"
                                id="password"
                                placeholder="Password"
                                required
                                autocomplete="current-password"
                            />
                            <button type="button" class="passwordBtn" data-bs-toggle="button" tabindex="-1" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);" aria-label="Show/Hide password"></button>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="ctaBtn">
                            <span class="btnTxt">Confirm</span>
                            <span class="arw"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
@endsection
