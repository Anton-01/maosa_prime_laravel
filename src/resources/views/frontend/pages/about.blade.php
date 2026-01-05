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
                        <h4>Quiénes somos</h4>
                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}"> Inicio </a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Sobre nosotros </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section id="about_page">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-xl-6 col-lg-6">
                    <div class="about_text">
                        {!! $about?->description !!}

                        <a href="{{ $about?->button_url }}">learn more</a>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6">
                    <div class="about_img">
                        <img style="width: 442px !important;
                        height: 500px !important;" src="{{ getYtThumbnail($about?->video_url) }}" alt="about" class="img-fluid w-100">
                        <a class="venobox" data-autoplay="true" data-vbtype="video" href="{{ $about?->video_url }}">
                            <i class=" fas fa-play"></i>
                        </a>
                        <div class="img_2">
                            <img src="{{ asset($about?->image) }}" alt="about" class="img-fluid w-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row">
            <div class="col-xl-5 m-auto">
                <div class="wsus__heading_area">
                    <h2>Nuestros Seminarios</h2>
                    <p>Lorem ipsum dolor sit ameto ei erant essent scaevola estut clita dolorem ei est mazim fuisset scribentur.</p>
                </div>
            </div>
        </div>
    </div>

    <section id="blog_part">
        <div class="blog_part_overlay">
            <div class="container">
                <div class="row">
                    @foreach ($blogs as $blog)
                        <div class="col-xl-6 col-md-6 col-lg-6">
                            <div class="single_blog">
                                <div class="img">
                                    <img src="{{ asset($blog->image) }}" alt="bloh images" class="img-fluid w-100">
                                </div>
                                <div class="text">
                                    <span><i class="fal fa-calendar-alt"></i> {{ date('d M Y', strtotime($blog->created_at)) }}</span>
                                    <span><i class="fas fa-user"></i> by {{ $blog->author->name }}</span>
                                    <a href="{{ route('blog.show', $blog->slug) }}" class="title">{{ truncate($blog->title) }}</a>
                                    <p>{{ truncate(strip_tags($blog->description), 200) }} </p>
                                    <a class="read_btn" href="{{ route('blog.show', $blog->slug) }}">
                                        Leer más
                                        <i class="far fa-chevron-double-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @include('frontend.home.sections.featured-category-section')
@endsection
