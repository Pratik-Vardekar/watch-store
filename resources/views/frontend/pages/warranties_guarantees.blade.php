@extends('layouts.front')

@section('title', $warrantyGuarantee->meta_title ?? ($warrantyGuarantee->title ?? 'Warranties & Guarantees'))
@section('meta_description', $warrantyGuarantee->meta_description ?? '')
@section('meta_keywords', $warrantyGuarantee->meta_keywords ?? '')

@section('content')
    <main class="pageWrapper">
        <div class="cloudWrap position-relative d-none d-lg-block">
            <div class="container clouds position-relative"></div>
        </div>
        <section class="py-5 overflow-hidden">
            <div class="container mb-5">
                <div class="text-center">
                    <nav class="breadcrumb justify-content-center mb-0 fw-semibold">
                        <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                        <span class="breadcrumb-item active" aria-current="page">Warranties and Guarantees</span>
                    </nav>
                    <h1 class="sectTitle fw-bold txtred">{{ $warrantyGuarantee->title ?? 'Warranties and Guarantees' }}</h1>
                </div>
                <div class="mx-auto generalContent" style="max-width:1000px;">
                    @if ($warrantyGuarantee && filled($warrantyGuarantee->content))
                        {!! $warrantyGuarantee->content !!}
                    @else
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle me-2"></i> Warranties &amp; Guarantees content is not available. Please check back later.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection
