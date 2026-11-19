@extends('layouts.front')
@section('content')
<main class="pageWrapper">
    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>
    <section class="py-5 overflow-hidden">
        <div class="container mb-5">
            <div class="text-center">
                <h1 class="sectTitle fw-bold txtred">Terms and Conditions</h1>
            </div>
            <div class="mx-auto generalContent" style="max-width:1000px;">
                @if($terms && filled($terms->content))
                    {!! $terms->content !!}
                @endif
            </div>
        </div>
    </section>
</main>
@endsection