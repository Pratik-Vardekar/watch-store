{{-- resources/views/frontend/pages/return_policy.blade.php --}}

@extends('layouts.front')

@section('title', $returnRefund?->meta_title ?? $returnRefund?->title ?? 'Return & Refund Policy')
@section('meta_description', $returnRefund?->meta_description ?? '')
@section('meta_keywords', $returnRefund?->meta_keywords ?? '')

@section('content')
<main class="pageWrapper">
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="page-content">
                        <h1 class="page-title mb-4">{{ $returnRefund?->title ?? 'Return & Refund Policy' }}</h1>
                        <div class="content-wrapper">
                            {!! $returnRefund?->content ?? '<p>Sorry, our Return & Refund Policy is currently unavailable. Please contact support for more information.</p>' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection