{{-- resources/views/frontend/pages/warranties_guarantees.blade.php --}}

@extends('layouts.front')

@section('title', $warranty->meta_title ?? $warranty->title)
@section('meta_description', $warranty->meta_description ?? '')
@section('meta_keywords', $warranty->meta_keywords ?? '')

@section('content')
<main class="pageWrapper">
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="page-content">
                        <h1 class="page-title mb-4">{{ $warranty->title }}</h1>
                        <div class="content-wrapper">
                            {!! $warranty->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection