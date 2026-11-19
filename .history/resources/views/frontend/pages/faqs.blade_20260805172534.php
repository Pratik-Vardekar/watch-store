@extends('layouts.front')
@section('content')
<main class="pageWrapper">

    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>

    <section class="py-5 overflow-hidden">
        <div class="container mb-5">

            <div class="text-center mb-4">
                <nav class="breadcrumb justify-content-center mb-0 fw-semibold">
                    <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                    <span class="breadcrumb-item active" aria-current="page">Help Center</span>
                </nav>
                <h1 class="sectTitle fw-bold txtred">Help Center</h1>
            </div>

            <div class="mx-auto generalContent" style="max-width:1000px;">

                <div class="faqList" id="faqAccd">
                    @forelse($helpCategories as $category)
                        <div class="faqItem p-3 mb-4 rounded-3 {{ $category->bg_class }}">
                            <div class="faqItemHead" role="button" data-bs-toggle="collapse" data-bs-target="#faqItem_{{ $category->id }}" aria-expanded="false">
                                {{ $category->title }}
                            </div>
                            <div id="faqItem_{{ $category->id }}" class="collapse {{ $category->bg_sub_class }} rounded-2" data-bs-parent="#faqAccd">
                                <div class="px-4">
                                    @forelse($category->activeFaqs as $faq)
                                        <div class="faqBox py-3">
                                            <div class="faqQ" role="button" data-bs-toggle="collapse" data-bs-target="#faq_{{ $category->id }}_{{ $faq->id }}" aria-expanded="false">
                                                {{ $faq->question }}
                                            </div>
                                            <div id="faq_{{ $category->id }}_{{ $faq->id }}" class="collapse" data-bs-parent="#faqItem_{{ $category->id }}">
                                                <div class="pt-3">
                                                    <p>{!! nl2br(e($faq->answer)) !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="py-3 text-muted">No questions in this section yet.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Help content will appear here soon.</p>
                    @endforelse
                </div>

            </div>

        </div>
    </section>

</main>
@endsection

@push('scripts')
<script>
    $('.faqList .collapse').on('shown.bs.collapse', function (e) {
        var $opentarget = $(this).parent();
        e.stopPropagation();
        $('html,body').animate({scrollTop: $opentarget.offset().top - 120}, 300, 'swing');
    });
</script>
@endpush
