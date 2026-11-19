@extends('layouts.front')
@section('content')
<main class="pageWrapper">         

    <div class="cloudWrap position-relative d-none d-lg-block">
       <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-5 overflow-hidden">
       <div class="container">

          <div class="text-center animateThis slideTop">
             <nav class="breadcrumb justify-content-center mb-0 fw-semibold">
                <a class="breadcrumb-item" href="index.html">Home</a>
                <span class="breadcrumb-item active" aria-current="page">Blogs</span>
             </nav>
             <h1 class="sectTitle fw-bold txtred animateThis slideTop">Explore Our Blogs</h1> 

             <small class="m-auto d-block" style="max-width:400px;">Stay updated on the latest developments at Cheriks and explore the world of word games with our insightful blogs.</small>
          </div>

       </div>
    </section>

    <section class="overflow-hidden">
       <div class="container py-5 mb-5">

         <div class="row row-cols-lg-3 row-cols-sm-2 row-cols-1 g-md-5 g-4 mb-5 blogList">

            @forelse($blogs as $blog)
        
                @php
                    $image = $blog->image
                        ? asset('backend/assets/images/blog/' . $blog->image)
                        : asset('frontend/assets/images/no-image.png');
                @endphp
        
                <div class="col animateThis slideTop">
        
                    <div class="blogItem p-4 rounded-4 d-flex flex-column gap-3 h-100">
        
                        <div>
                            <div class="blogDate mb-2">
                                {{ \Carbon\Carbon::parse($blog->blog_date)->format('d-m-Y') }}
                            </div>
        
                            <h3 class="blogHead">
                                {{ $blog->title }}
                            </h3>
                        </div>
        
                        <div class="blogThumb rounded-3 overflow-hidden">
                            <img src="{{ $image }}" class="blogImg w-100" alt="{{ $blog->title }}">
                        </div>
        
                        <a href="{{ route('blogs.details', $blog->slug) }}" class="stretched-link"></a>
        
                    </div>
        
                </div>
        
            @empty
                <div class="col-12 text-center">
                    <h5>No Blogs Found</h5>
                </div>
            @endforelse
        
        </div>
        
        <!-- PAGINATION -->
        <div class="d-flex justify-content-center">
            {{ $blogs->links() }}
        </div>

          <nav class="d-flex justify-content-center align-items-center gap-4 mb-5">
             <button class="pgBtn rounded-pill btn"> 
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" stroke-width="1" stroke="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/></svg>
                Prev</button>
             <div class="">
                Page <strong>1</strong> of 1
             </div>
             <button class="pgBtn rounded-pill btn">Next 
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" stroke-width="1" stroke="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/></svg>
             </button>
          </nav>

       </div>
    </section>

 </main>
@endsection