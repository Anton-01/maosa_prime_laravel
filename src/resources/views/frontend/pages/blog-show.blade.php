@extends('frontend.layouts.master')

@section('contents')
    <!--==========================
        BREADCRUMB PART START
    ===========================-->
    <div id="breadcrumb_part">
        <div class="bread_overlay">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 text-center text-white">
                        <h4>Blog</h4>
                        <nav style="--bs-breadcrumb-divider: '';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}"> Inicio </a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Blog - detalles</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--==========================
        BREADCRUMB PART END
    ===========================-->


    <!--==========================
        BLOG DETAILS START
    ===========================-->
    <section id="blog_details">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="main_blog">
                        <div class="main_blog_img">
                            <img src="{{ asset($blog->image) }}" alt="blog" class="img-fluid w-100">
                        </div>
                        <ul class="main_blog_header">
                            <li><a href="javascipts:;"><i class="fal fa-calendar-alt"></i> {{ date('d M Y', strtotime($blog->created_at)) }}</a></li>
                            <li><a href="javascipts:;"><i class="fal fa-tags"></i> {{ $blog->category->name }} </a></li>
                        </ul>
                        <h4>{{ $blog->title }}</h4>
                        {!! $blog->description !!}
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <div class="blog_sidebar">
                        <div class="sidebar_blog">
                            <h4>Seminarios similares</h4>
                            @foreach ($popularBlogs as $popularBlog)
                            <a href="{{ route('blog.show', $popularBlog->slug) }}" class="sidebar_blog_single">
                                <div class="sidebar_blog_img">
                                    <img src="{{ asset($popularBlog->image) }}" alt="blog" class="imgofluid w-100">
                                </div>
                                <div class="sidebar_blog_text">
                                    <h5>{{ truncate($popularBlog->title, 120) }}</h5>
                                    <p> <span>{{ date('d M Y', strtotime($popularBlog->created_at)) }} </span> {{ count($popularBlog->comments) }} Comment </p>
                                </div>
                            </a>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========================
        BLOG DETAILS END
    ===========================-->
@endsection
