{{-- resources/views/frontend/pages/warranties_guarantees.blade.php --}}

@extends('layouts.front')

@section('title', $page->meta_title ?? $page->title ?? 'Warranties & Guarantees')
@section('meta_description', $page->meta_description ?? '')
@section('meta_keywords', $page->meta_keywords ?? '')

@section('content')

<style>
    /* Inherit font family from site */
    .warranty-wrapper,
    .warranty-wrapper * {
        font-family: inherit !important;
    }

    .warranty-wrapper .page-title {
        font-family: inherit !important;
        font-weight: 700;
        font-size: 2.2rem;
        color: #1a1a2e;
        margin-bottom: 1.5rem;
        letter-spacing: -0.5px;
        position: relative;
        padding-bottom: 0.75rem;
    }

    .warranty-wrapper .page-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: #e94560;
        border-radius: 2px;
    }

    .warranty-wrapper .content-wrapper {
        font-family: inherit !important;
    }

    .warranty-wrapper .content-wrapper h2 {
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

    .warranty-wrapper .content-wrapper h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: #e94560;
        border-radius: 2px;
    }

    .warranty-wrapper .content-wrapper h3 {
        font-family: inherit !important;
        font-weight: 600;
        font-size: 1.2rem;
        color: #16213e;
        margin-top: 1.8rem;
        margin-bottom: 0.8rem;
    }

    .warranty-wrapper .content-wrapper h4 {
        font-family: inherit !important;
        font-weight: 600;
        font-size: 1.05rem;
        color: #16213e;
        margin-top: 1.5rem;
        margin-bottom: 0.6rem;
    }

    .warranty-wrapper .content-wrapper p {
        font-family: inherit !important;
        font-size: 1rem;
        color: #444;
        line-height: 1.8;
        margin-bottom: 1rem;
    }

    .warranty-wrapper .content-wrapper ul,
    .warranty-wrapper .content-wrapper ol {
        font-family: inherit !important;
        padding-left: 1.5rem;
        margin-bottom: 1.2rem;
    }

    .warranty-wrapper .content-wrapper ul li,
    .warranty-wrapper .content-wrapper ol li {
        font-family: inherit !important;
        font-size: 1rem;
        color: #444;
        line-height: 1.8;
        margin-bottom: 0.5rem;
    }

    .warranty-wrapper .content-wrapper ul li::marker {
        color: #e94560;
    }

    .warranty-wrapper .content-wrapper strong,
    .warranty-wrapper .content-wrapper b {
        font-weight: 600;
        color: #1a1a2e;
    }

    .warranty-wrapper .content-wrapper a {
        color: #e94560;
        text-decoration: none;
        transition: color 0.3s ease;
        font-family: inherit !important;
    }

    .warranty-wrapper .content-wrapper a:hover {
        color: #c0392b;
        text-decoration: underline;
    }

    .warranty-wrapper .content-wrapper blockquote {
        font-family: inherit !important;
        border-left: 4px solid #e94560;
        padding: 1rem 1.5rem;
        margin: 1.5rem 0;
        background: #f8f9fa;
        border-radius: 0 8px 8px 0;
        color: #444;
        font-style: italic;
    }

    .warranty-wrapper .content-wrapper table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
        font-family: inherit !important;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .warranty-wrapper .content-wrapper table thead {
        background: #1a1a2e;
    }

    .warranty-wrapper .content-wrapper table th {
        font-family: inherit !important;
        color: #ffffff;
        font-weight: 600;
        padding: 0.875rem 1.25rem;
        text-align: left;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .warranty-wrapper .content-wrapper table td {
        font-family: inherit !important;
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid #e9ecef;
        font-size: 0.95rem;
        color: #444;
        background: #ffffff;
    }

    .warranty-wrapper .content-wrapper table tbody tr:last-child td {
        border-bottom: none;
    }

    .warranty-wrapper .content-wrapper table tbody tr:hover td {
        background: #f8f9fa;
    }

    .warranty-wrapper .content-wrapper table tbody tr:nth-child(even) td {
        background: #fafafa;
    }

    .warranty-wrapper .content-wrapper table tbody tr:nth-child(even):hover td {
        background: #f0f0f0;
    }

    /* Highlight Box */
    .warranty-wrapper .content-wrapper .highlight-box {
        background: #f8f9fa;
        border-left: 4px solid #e94560;
        padding: 1.25rem 1.5rem;
        border-radius: 8px;
        margin: 1.5rem 0;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .warranty-wrapper .content-wrapper .highlight-box i {
        color: #e94560;
        font-size: 1.3rem;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .warranty-wrapper .content-wrapper .highlight-box strong {
        display: block;
        color: #1a1a2e;
        margin-bottom: 2px;
    }

    .warranty-wrapper .content-wrapper .highlight-box p {
        margin-bottom: 0;
        color: #555;
    }

    /* Contact Section */
    .warranty-wrapper .content-wrapper .contact-info {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        margin-top: 0.75rem;
    }

    .warranty-wrapper .content-wrapper .contact-info p {
        margin-bottom: 0.5rem;
        font-family: inherit !important;
    }

    .warranty-wrapper .content-wrapper .contact-info p:last-child {
        margin-bottom: 0;
    }

    .warranty-wrapper .content-wrapper .contact-info i {
        color: #e94560;
        width: 24px;
        text-align: center;
        margin-right: 10px;
    }

    .warranty-wrapper .content-wrapper .contact-info a {
        color: #1a1a2e;
        text-decoration: none;
        transition: color 0.3s ease;
        font-family: inherit !important;
    }

    .warranty-wrapper .content-wrapper .contact-info a:hover {
        color: #e94560;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .warranty-wrapper .page-title {
            font-size: 1.8rem;
        }

        .warranty-wrapper .content-wrapper h2 {
            font-size: 1.3rem;
        }

        .warranty-wrapper .content-wrapper h2::after {
            width: 35px;
        }

        .warranty-wrapper .content-wrapper h3 {
            font-size: 1.1rem;
        }

        .warranty-wrapper .content-wrapper table th,
        .warranty-wrapper .content-wrapper table td {
            padding: 0.625rem 0.875rem;
            font-size: 0.85rem;
        }

        .warranty-wrapper .content-wrapper .highlight-box {
            flex-direction: column;
            padding: 1rem 1.25rem;
        }

        .warranty-wrapper .content-wrapper .highlight-box i {
            font-size: 1.1rem;
        }

        .warranty-wrapper .content-wrapper .contact-info {
            padding: 1rem 1.25rem;
        }
    }

    @media (max-width: 480px) {
        .warranty-wrapper .page-title {
            font-size: 1.5rem;
        }

        .warranty-wrapper .content-wrapper h2 {
            font-size: 1.15rem;
        }

        .warranty-wrapper .content-wrapper ul,
        .warranty-wrapper .content-wrapper ol {
            padding-left: 1rem;
        }

        .warranty-wrapper .content-wrapper ul li,
        .warranty-wrapper .content-wrapper ol li {
            font-size: 0.95rem;
        }

        .warranty-wrapper .content-wrapper p {
            font-size: 0.95rem;
        }

        .warranty-wrapper .content-wrapper table th,
        .warranty-wrapper .content-wrapper table td {
            padding: 0.5rem 0.625rem;
            font-size: 0.8rem;
        }
    }
</style>

<main class="pageWrapper">
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="page-content warranty-wrapper">
                        <h1 class="page-title mb-4">{{ $page->title ?? 'Warranties & Guarantees' }}</h1>
                        <div class="content-wrapper">
                            @if($page && $page->content)
                                {!! $page->content !!}
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Warranties & Guarantees content is not available. Please check back later.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection