{{-- resources/views/frontend/pages/return_policy.blade.php --}}

@extends('layouts.front')

@section('title', $returnRefund->meta_title ?? $returnRefund->title)
@section('meta_description', $returnRefund->meta_description ?? '')
@section('meta_keywords', $returnRefund->meta_keywords ?? '')

@section('content')
<main class="pageWrapper">
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="page-content">
                        <h1 class="page-title mb-4">{{ $returnRefund->title }}</h1>
                        <div class="content-wrapper">
                            {!! $returnRefund->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection