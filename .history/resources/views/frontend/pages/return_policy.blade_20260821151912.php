@extends('layouts.front')

@section('title', $returnRefund->meta_title ?? $returnRefund->title ?? 'Return & Refund Policy')
@section('meta_description', $returnRefund->meta_description ?? '')
@section('meta_keywords', $returnRefund->meta_keywords ?? '')

@section('content')


<main class="pageWrapper">
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="policy-content-card policy-wrapper">

                        <h1 class="text-center">{{ $returnRefund->title ?? 'Return & Refund Policy' }}</h1>

                        @if($returnRefund)
                            {!! $returnRefund->content !!}
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Return & Refund Policy not available.
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection