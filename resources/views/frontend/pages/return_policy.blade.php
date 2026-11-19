@extends('layouts.front')

@section('title', $returnRefund->meta_title ?? ($returnRefund->title ?? 'Return & Refund Policy'))
@section('meta_description', $returnRefund->meta_description ?? '')
@section('meta_keywords', $returnRefund->meta_keywords ?? '')

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
                        <span class="breadcrumb-item active" aria-current="page">Return and Refund Policy</span>
                    </nav>
                    <h1 class="sectTitle fw-bold txtred">{{ $returnRefund->title ?? 'Return and Refund Policy' }}</h1>
                </div>
                <div class="mx-auto generalContent" style="max-width:1000px;">
                    @if ($returnRefund && filled($returnRefund->content))
                        {!! $returnRefund->content !!}
                    @else
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle me-2"></i> Return &amp; Refund Policy not available.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection
