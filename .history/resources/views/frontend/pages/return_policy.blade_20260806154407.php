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

                    <h1 class="mb-4">
                        {{ $returnRefund->title ?? 'Return & Refund Policy' }}
                    </h1>

                    @if($returnRefund)

                        {!! $returnRefund->content !!}

                    @else

                        <div class="alert alert-warning">
                            Return & Refund Policy not available.
                        </div>

                    @endif

                </div>
            </div>

        </div>
    </section>

</main>

@endsection