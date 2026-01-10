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

    <!--==========================
        ABOUT SECTION START
    ===========================-->
    <section id="about_page" class="about-redesigned">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-xl-6 col-lg-6">
                    <div class="about_text about-content-redesigned">
                        <div class="about-badge">
                            <span><i class="fas fa-building"></i> Nuestra Historia</span>
                        </div>
                        <div class="about-description">
                            {!! $about?->description !!}
                        </div>
                        <a href="{{ $about?->button_url }}" class="about-cta-btn">
                            <span>Conoce más</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>

                        <div class="about-stats">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <h4>500+</h4>
                                    <p>Clientes Satisfechos</p>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div class="stat-content">
                                    <h4>100+</h4>
                                    <p>Proveedores</p>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div class="stat-content">
                                    <h4>10+</h4>
                                    <p>Años de Experiencia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6">
                    <div class="about_img about-image-redesigned">
                        <div class="about-main-image">
                            <img src="{{ getYtThumbnail($about?->video_url) }}" alt="about" class="img-fluid">
                            <a class="venobox play-button" data-autoplay="true" data-vbtype="video" href="{{ $about?->video_url }}">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                        <div class="about-secondary-image">
                            <img src="{{ asset($about?->image) }}" alt="about" class="img-fluid">
                        </div>
                        <div class="about-floating-card">
                            <i class="fas fa-shield-alt"></i>
                            <span>Empresa Verificada</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========================
        ABOUT SECTION END
    ===========================-->

    <!--==========================
        BLOG/SEMINARS SECTION START
    ===========================-->
    <section class="seminars-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 m-auto">
                    <div class="section-header-redesigned">
                        <span class="section-badge"><i class="fas fa-graduation-cap"></i> Formación</span>
                        <h2>Nuestros Seminarios</h2>
                        <p>Mantente actualizado con nuestros eventos y capacitaciones diseñados para impulsar tu crecimiento profesional.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach ($blogs as $blog)
                    <div class="col-xl-6 col-md-6 col-lg-6">
                        <div class="blog-card-redesigned">
                            <div class="blog-card-image">
                                <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}" class="img-fluid">
                                <div class="blog-card-overlay">
                                    <a href="{{ route('blog.show', $blog->slug) }}" class="blog-view-btn">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="blog-card-content">
                                <div class="blog-card-meta">
                                    <span class="meta-date">
                                        <i class="fal fa-calendar-alt"></i>
                                        {{ date('d M Y', strtotime($blog->created_at)) }}
                                    </span>
                                    <span class="meta-author">
                                        <i class="fas fa-user"></i>
                                        {{ $blog->author->name }}
                                    </span>
                                </div>
                                <h3 class="blog-card-title">
                                    <a href="{{ route('blog.show', $blog->slug) }}">{{ truncate($blog->title) }}</a>
                                </h3>
                                <p class="blog-card-excerpt">{{ truncate(strip_tags($blog->description), 150) }}</p>
                                <a class="blog-read-more" href="{{ route('blog.show', $blog->slug) }}">
                                    Leer más
                                    <i class="far fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!--==========================
        BLOG/SEMINARS SECTION END
    ===========================-->

    @include('frontend.home.sections.featured-category-section')

    <style>
        /* About Section Redesigned */
        .about-redesigned {
            padding: 80px 0;
            background: #fff;
        }

        .about-content-redesigned {
            padding-right: 30px;
        }

        .about-badge {
            margin-bottom: 20px;
        }

        .about-badge span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #fff5f2 0%, #ffe8e2 100%);
            color: #f66542;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
        }

        .about-description {
            margin-bottom: 30px;
            line-height: 1.8;
            color: #555;
        }

        .about-description h1, .about-description h2, .about-description h3 {
            color: #1a1a2e;
            margin-bottom: 15px;
        }

        .about-description p {
            margin-bottom: 15px;
        }

        .about-cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #f66542 0%, #ff8a65 100%);
            color: #fff;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 40px;
        }

        .about-cta-btn:hover {
            background: linear-gradient(135deg, #e55a38 0%, #f66542 100%);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(246, 101, 66, 0.3);
        }

        .about-cta-btn i {
            transition: transform 0.3s ease;
        }

        .about-cta-btn:hover i {
            transform: translateX(5px);
        }

        .about-stats {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #f66542 0%, #ff8a65 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon i {
            color: #fff;
            font-size: 20px;
        }

        .stat-content h4 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 2px;
        }

        .stat-content p {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        /* About Image Section */
        .about-image-redesigned {
            position: relative;
            padding: 20px;
        }

        .about-main-image {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .about-main-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .play-button i {
            color: #f66542;
            font-size: 24px;
            margin-left: 5px;
        }

        .play-button:hover {
            transform: translate(-50%, -50%) scale(1.1);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
        }

        .about-secondary-image {
            position: absolute;
            bottom: -20px;
            left: -40px;
            width: 180px;
            height: 180px;
            border-radius: 20px;
            overflow: hidden;
            border: 5px solid #fff;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .about-secondary-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .about-floating-card {
            position: absolute;
            top: 0;
            right: -20px;
            background: #fff;
            padding: 15px 25px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: floatCard 3s ease-in-out infinite;
        }

        .about-floating-card i {
            color: #28a745;
            font-size: 18px;
        }

        .about-floating-card span {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Seminars/Blog Section */
        .seminars-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
        }

        .section-header-redesigned {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #fff5f2 0%, #ffe8e2 100%);
            color: #f66542;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .section-header-redesigned h2 {
            font-size: 36px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 15px;
        }

        .section-header-redesigned p {
            color: #666;
            font-size: 16px;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Blog Cards Redesigned */
        .blog-card-redesigned {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            transition: all 0.4s ease;
        }

        .blog-card-redesigned:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .blog-card-image {
            position: relative;
            overflow: hidden;
            height: 250px;
        }

        .blog-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .blog-card-redesigned:hover .blog-card-image img {
            transform: scale(1.1);
        }

        .blog-card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(246, 101, 66, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .blog-card-redesigned:hover .blog-card-overlay {
            opacity: 1;
        }

        .blog-view-btn {
            width: 60px;
            height: 60px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: scale(0);
            transition: transform 0.3s ease;
        }

        .blog-card-redesigned:hover .blog-view-btn {
            transform: scale(1);
        }

        .blog-view-btn i {
            color: #f66542;
            font-size: 20px;
        }

        .blog-card-content {
            padding: 25px;
        }

        .blog-card-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .blog-card-meta span {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #888;
        }

        .blog-card-meta i {
            color: #f66542;
        }

        .blog-card-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .blog-card-title a {
            color: #1a1a2e;
            transition: color 0.3s ease;
        }

        .blog-card-title a:hover {
            color: #f66542;
        }

        .blog-card-excerpt {
            color: #666;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .blog-read-more {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #f66542;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .blog-read-more:hover {
            color: #e55a38;
        }

        .blog-read-more i {
            transition: transform 0.3s ease;
        }

        .blog-read-more:hover i {
            transform: translateX(5px);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .about-content-redesigned {
                padding-right: 0;
                margin-bottom: 50px;
            }

            .about-stats {
                gap: 15px;
            }

            .stat-item {
                padding: 15px;
            }

            .about-secondary-image {
                left: 0;
                bottom: -10px;
                width: 140px;
                height: 140px;
            }

            .about-floating-card {
                right: 0;
            }
        }

        @media (max-width: 575px) {
            .about-redesigned,
            .seminars-section {
                padding: 50px 0;
            }

            .about-stats {
                flex-direction: column;
            }

            .section-header-redesigned h2 {
                font-size: 28px;
            }

            .blog-card-image {
                height: 200px;
            }
        }
    </style>
@endsection
