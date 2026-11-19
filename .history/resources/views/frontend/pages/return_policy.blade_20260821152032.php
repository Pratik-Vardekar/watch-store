@extends('layouts.front')

@section('title', $returnRefund->meta_title ?? $returnRefund->title ?? 'Return & Refund Policy')
@section('meta_description', $returnRefund->meta_description ?? '')
@section('meta_keywords', $returnRefund->meta_keywords ?? '')

@section('content')

<style>
    /* Force font family to match site */
    .policy-wrapper,
    .policy-wrapper * {
        font-family: inherit !important;
    }

    .policy-wrapper h1 {
        font-family: inherit !important;
        font-weight: 700;
        font-size: 2.2rem;
        color: #1a1a2e;
        margin-bottom: 1.5rem;
        letter-spacing: -0.5px;
    }

    .policy-wrapper h2 {
        font-family: inherit !important;
        font-weight: 700;
        font-size: 1.6rem;
        color: #1a1a2e;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        letter-spacing: -0.3px;
        position: relative;
        padding-bottom: 0.75rem;
    }

    .policy-wrapper h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: #e94560;
        border-radius: 2px;
    }

    .policy-wrapper h3 {
        font-family: inherit !important;
        font-weight: 600;
        font-size: 1.2rem;
        color: #16213e;
        margin-top: 1.8rem;
        margin-bottom: 0.8rem;
    }

    .policy-wrapper p {
        font-family: inherit !important;
        font-size: 1rem;
        color: #444;
        line-height: 1.8;
        margin-bottom: 1rem;
    }

    .policy-wrapper ul,
    .policy-wrapper ol {
        font-family: inherit !important;
        padding-left: 1.5rem;
        margin-bottom: 1.2rem;
    }

    .policy-wrapper ul li,
    .policy-wrapper ol li {
        font-family: inherit !important;
        font-size: 1rem;
        color: #444;
        line-height: 1.8;
        margin-bottom: 0.5rem;
    }

    .policy-wrapper ul li::marker {
        color: #e94560;
    }

    .policy-wrapper strong {
        color: #1a1a2e;
        font-weight: 600;
    }

    .policy-wrapper .alert {
        font-family: inherit !important;
    }

    .policy-content-card {
        border-radius: 16px;
        padding: 3rem 3.5rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .policy-content-card {
            padding: 1.5rem 1.25rem;
        }

        .policy-wrapper h1 {
            font-size: 1.8rem;
        }

        .policy-wrapper h2 {
            font-size: 1.3rem;
        }

        .policy-wrapper h2::after {
            width: 35px;
        }
    }

    @media (max-width: 480px) {
        .policy-content-card {
            padding: 1rem 0.875rem;
        }

        .policy-wrapper h1 {
            font-size: 1.5rem;
        }

        .policy-wrapper h2 {
            font-size: 1.15rem;
        }
    }
</style>

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