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
                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}"> Inicio </a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Blog </li>
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
        BLOG PART START
    ===========================-->
    <section class="blog-section-redesigned">
        <div class="container">
            <!-- Header -->
            <div class="row">
                <div class="col-12">
                    <div class="blog-section-header">
                        <span class="blog-badge"><i class="fas fa-newspaper"></i> Publicaciones</span>
                        <h2>Últimas Noticias y Artículos</h2>
                        <p>Mantente informado con nuestras publicaciones más recientes sobre la industria.</p>
                    </div>
                </div>
            </div>

            <!-- Blog Grid -->
            <div class="row">
                @foreach ($blogs as $blog)
                <div class="col-xl-4 col-md-6 col-lg-4">
                    <article class="blog-card-modern">
                        <div class="blog-card-image-wrapper">
                            <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}" class="blog-card-img">
                            <div class="blog-card-image-overlay">
                                <a href="{{ route('blog.show', $blog->slug) }}" class="blog-card-view-icon">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="blog-card-date">
                                <span class="day">{{ date('d', strtotime($blog->created_at)) }}</span>
                                <span class="month">{{ date('M', strtotime($blog->created_at)) }}</span>
                            </div>
                        </div>
                        <div class="blog-card-body">
                            <div class="blog-card-author">
                                <div class="author-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span>Por {{ $blog->author->name }}</span>
                            </div>
                            <h3 class="blog-card-heading">
                                <a href="{{ route('blog.show', $blog->slug) }}">{{ truncate($blog->title) }}</a>
                            </h3>
                            <p class="blog-card-text">{{ truncate(strip_tags($blog->description), 120) }}</p>
                            <a href="{{ route('blog.show', $blog->slug) }}" class="blog-card-link">
                                <span>Leer artículo</span>
                                <i class="far fa-long-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-12">
                    <div class="blog-pagination">
                        @if ($blogs->hasPages())
                            {{ $blogs->withQueryString()->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========================
        BLOG PART END
    ===========================-->

    <style>
        .blog-section-redesigned {
            padding: 80px 0;
            background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);
        }

        /* Header */
        .blog-section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .blog-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #fff5f2 0%, #ffe8e2 100%);
            color: #f66542;
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .blog-section-header h2 {
            font-size: 38px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 15px;
        }

        .blog-section-header p {
            color: #666;
            font-size: 16px;
            max-width: 500px;
            margin: 0 auto;
        }

        /* Blog Card Modern */
        .blog-card-modern {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .blog-card-modern:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12);
        }

        .blog-card-image-wrapper {
            position: relative;
            overflow: hidden;
            height: 220px;
        }

        .blog-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .blog-card-modern:hover .blog-card-img {
            transform: scale(1.1);
        }

        .blog-card-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(246, 101, 66, 0.9) 0%, rgba(255, 138, 101, 0.9) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .blog-card-modern:hover .blog-card-image-overlay {
            opacity: 1;
        }

        .blog-card-view-icon {
            width: 55px;
            height: 55px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: scale(0) rotate(-45deg);
            transition: all 0.4s ease;
        }

        .blog-card-modern:hover .blog-card-view-icon {
            transform: scale(1) rotate(0deg);
        }

        .blog-card-view-icon i {
            color: #f66542;
            font-size: 18px;
        }

        .blog-card-date {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #fff;
            padding: 10px 15px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .blog-card-date .day {
            display: block;
            font-size: 22px;
            font-weight: 700;
            color: #f66542;
            line-height: 1;
        }

        .blog-card-date .month {
            display: block;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }

        .blog-card-body {
            padding: 25px;
        }

        .blog-card-author {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .author-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #f66542 0%, #ff8a65 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .author-avatar i {
            color: #fff;
            font-size: 14px;
        }

        .blog-card-author span {
            font-size: 13px;
            color: #888;
            font-weight: 500;
        }

        .blog-card-heading {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .blog-card-heading a {
            color: #1a1a2e;
            transition: color 0.3s ease;
        }

        .blog-card-heading a:hover {
            color: #f66542;
        }

        .blog-card-text {
            color: #666;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .blog-card-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #f66542;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .blog-card-link:hover {
            color: #e55a38;
            gap: 15px;
        }

        .blog-card-link i {
            transition: transform 0.3s ease;
        }

        .blog-card-link:hover i {
            transform: translateX(5px);
        }

        /* Pagination */
        .blog-pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .blog-pagination .pagination {
            gap: 8px;
        }

        .blog-pagination .page-item .page-link {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: none;
            color: #555;
            font-weight: 600;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
        }

        .blog-pagination .page-item .page-link:hover,
        .blog-pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #f66542 0%, #ff8a65 100%);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(246, 101, 66, 0.3);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .blog-section-header h2 {
                font-size: 32px;
            }
        }

        @media (max-width: 575px) {
            .blog-section-redesigned {
                padding: 50px 0;
            }

            .blog-section-header h2 {
                font-size: 26px;
            }

            .blog-card-image-wrapper {
                height: 180px;
            }

            .blog-card-body {
                padding: 20px;
            }

            .blog-card-heading {
                font-size: 16px;
            }
        }
    </style>
@endsection
