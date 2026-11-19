@extends('layouts.front')
@section('content')
<main class="pageWrapper">         

    <div class="cloudWrap position-relative d-none d-lg-block">
       <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-5 overflow-hidden">
       <div class="container d-flex justify-content-center">

          <div class="col-xxl-8 col-md-10 text-center">
             <nav class="breadcrumb justify-content-center mb-4 fw-semibold">
                <a class="breadcrumb-item" href="index.html">Home</a>
                <a class="breadcrumb-item" href="blogs.html">Blogs</a>
                <span class="breadcrumb-item active" aria-current="page">How Word Hunt Challenge Enhances Vocabulary</span>
             </nav>

             @php
             $image = $blog->image
                 ? asset('backend/assets/images/blog/' . $blog->image)
                 : asset('frontend/assets/images/no-image.png');
         @endphp
         
         <div class="blogContent text-start">
         
             <div class="p-4 rounded-4 shadow-lg blogPgTitle bgPurple">
                 <div class="blogDate bgRed mb-2">
                     {{ \Carbon\Carbon::parse($blog->blog_date)->format('d-m-Y') }}
                 </div>
         
                 <h1 class="sectTitle mb-0 fw-bold text-white">
                     {{ $blog->title }}
                 </h1>
             </div>
         
             <div class="blogBanner rounded-4 border mb-5 overflow-hidden animateThis fadeIn">
                 <img src="{{ $image }}" class="w-100" alt="{{ $blog->title }}">
             </div>
         
             {!! $blog->description !!}
         </div>

             <nav class="d-flex justify-content-center align-items-center gap-3 my-5 pt-4 border-top">
                <a href="" class="pgBtn rounded-pill btn"> 
                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" stroke-width="1" stroke="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/></svg>
                   Prev
                </a>
                <a href="blogs.html" class="pgBtn rounded-pill btn px-4 fw-medium" style="width:auto;"> View All Blogs</a>
                <a href="" class="pgBtn rounded-pill btn">Next 
                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" stroke-width="1" stroke="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/></svg>
                </a>
             </nav>

          </div>

       </div>
    </section>

 </main>

@endsection