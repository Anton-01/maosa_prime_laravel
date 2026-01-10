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
                        <h4>Términos y Condiciones</h4>
                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}"> Inicio </a></li>
                                <li class="breadcrumb-item active" aria-current="page">Términos y Condiciones</li>
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
        TERMS CONTENT START
    ===========================-->
    <section id="listing_details" class="terms-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-8">
                    <div class="listing_details_text terms-content">
                        <div class="listing_det_header">
                            <div class="listing_det_header_text">
                                <h6>Términos y Condiciones</h6>
                                <p class="host_name">
                                    <i class="far fa-calendar-alt mr-2"></i>
                                    Actualizado: {{ now()->format('d/m/Y') }}
                                </p>
                                <ul>
                                    <li><a href="#"><i class="far fa-file-contract"></i>Documento Legal</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="listing_det_text terms-body">
                            {!! $termsAndCondition?->description !!}
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4">
                    <div class="listing_details_sidebar">
                        <div class="row">
                            <!-- Información Importante -->
                            <div class="col-12">
                                <div class="listing_det_side_address terms-sidebar-card">
                                    <h5 class="sidebar-title"><i class="fal fa-exclamation-circle"></i> Importante</h5>
                                    <div class="terms-info-item">
                                        <i class="fal fa-check-circle"></i>
                                        <div>
                                            <strong>Aceptación</strong>
                                            <p>Al usar nuestros servicios, aceptas estos términos.</p>
                                        </div>
                                    </div>
                                    <div class="terms-info-item">
                                        <i class="fal fa-balance-scale"></i>
                                        <div>
                                            <strong>Derechos y Obligaciones</strong>
                                            <p>Conoce tus derechos y obligaciones como usuario.</p>
                                        </div>
                                    </div>
                                    <div class="terms-info-item">
                                        <i class="fal fa-gavel"></i>
                                        <div>
                                            <strong>Marco Legal</strong>
                                            <p>Estos términos se rigen por las leyes aplicables.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen Rápido -->
                            <div class="col-12">
                                <div class="listing_det_side_open_hour terms-sidebar-card">
                                    <h5 class="sidebar-title"><i class="fal fa-list-alt"></i> Resumen</h5>
                                    <ul class="terms-summary-list">
                                        <li>
                                            <i class="fal fa-chevron-right"></i>
                                            Uso permitido del sitio
                                        </li>
                                        <li>
                                            <i class="fal fa-chevron-right"></i>
                                            Protección de propiedad intelectual
                                        </li>
                                        <li>
                                            <i class="fal fa-chevron-right"></i>
                                            Limitación de responsabilidad
                                        </li>
                                        <li>
                                            <i class="fal fa-chevron-right"></i>
                                            Políticas de cancelación
                                        </li>
                                        <li>
                                            <i class="fal fa-chevron-right"></i>
                                            Resolución de disputas
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Contacto -->
                            <div class="col-12">
                                <div class="listing_det_side_open_hour terms-sidebar-card">
                                    <h5 class="sidebar-title"><i class="fal fa-question-circle"></i> Dudas</h5>
                                    <p>Si tienes preguntas sobre estos términos, estamos aquí para ayudarte.</p>
                                    <a href="{{ url('/contact') }}" class="btn terms-contact-btn">
                                        <i class="fas fa-comments"></i> Contáctanos
                                    </a>
                                </div>
                            </div>

                            <!-- Enlaces Relacionados -->
                            <div class="col-12">
                                <div class="listing_det_side_list terms-sidebar-card">
                                    <h5 class="sidebar-title"><i class="fal fa-link"></i> Enlaces Relacionados</h5>
                                    <ul class="terms-links">
                                        <li>
                                            <a href="{{ url('/privacy-policy') }}">
                                                <i class="fal fa-shield-alt"></i>
                                                Aviso de Privacidad
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('/contact') }}">
                                                <i class="fal fa-headset"></i>
                                                Soporte
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('/') }}">
                                                <i class="fal fa-home"></i>
                                                Página Principal
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========================
        TERMS CONTENT END
    ===========================-->

    <style>
        .terms-section {
            background: #f8f9fa;
        }

        .terms-content {
            background: #fff;
            border-radius: 10px;
            box-shadow: rgba(149, 157, 165, 0.1) 0px 8px 24px;
            padding: 30px;
        }

        .terms-content .listing_det_header {
            border-bottom: 2px solid #f66542;
            margin-bottom: 25px;
            padding-bottom: 20px;
        }

        .terms-content .listing_det_header_text h6 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .terms-content .host_name {
            color: #666;
            font-size: 14px;
        }

        .terms-content .host_name i {
            color: #f66542;
        }

        .terms-body {
            line-height: 1.8;
            color: #444;
        }

        .terms-body h1, .terms-body h2, .terms-body h3,
        .terms-body h4, .terms-body h5, .terms-body h6 {
            color: #333;
            margin-top: 25px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .terms-body p {
            margin-bottom: 15px;
            text-align: justify;
        }

        .terms-body ul, .terms-body ol {
            padding-left: 25px;
            margin-bottom: 15px;
        }

        .terms-body li {
            margin-bottom: 8px;
        }

        .terms-sidebar-card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: rgba(149, 157, 165, 0.1) 0px 8px 24px;
        }

        .terms-sidebar-card .sidebar-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f66542;
        }

        .terms-sidebar-card .sidebar-title i {
            color: #f66542;
            margin-right: 10px;
        }

        .terms-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .terms-info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .terms-info-item i {
            font-size: 24px;
            color: #f66542;
            margin-right: 15px;
            margin-top: 3px;
        }

        .terms-info-item strong {
            display: block;
            font-size: 15px;
            color: #333;
            margin-bottom: 5px;
        }

        .terms-info-item p {
            font-size: 13px;
            color: #666;
            margin: 0;
            line-height: 1.5;
        }

        .terms-summary-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .terms-summary-list li {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #555;
        }

        .terms-summary-list li:last-child {
            border-bottom: none;
        }

        .terms-summary-list li i {
            color: #f66542;
            margin-right: 10px;
            font-size: 12px;
        }

        .terms-contact-btn {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #f66542 0%, #ff8a65 100%);
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        .terms-contact-btn:hover {
            background: linear-gradient(135deg, #e55a38 0%, #f66542 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(246, 101, 66, 0.3);
        }

        .terms-contact-btn i {
            margin-right: 8px;
        }

        .terms-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .terms-links li {
            margin-bottom: 12px;
        }

        .terms-links li:last-child {
            margin-bottom: 0;
        }

        .terms-links li a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .terms-links li a:hover {
            background: #f66542;
            color: #fff;
            transform: translateX(5px);
        }

        .terms-links li a i {
            margin-right: 12px;
            font-size: 18px;
            color: #f66542;
            transition: color 0.3s ease;
        }

        .terms-links li a:hover i {
            color: #fff;
        }

        @media (max-width: 991px) {
            .terms-content {
                margin-bottom: 30px;
            }

            .terms-content .listing_det_header_text h6 {
                font-size: 24px;
            }
        }
    </style>
@endsection
