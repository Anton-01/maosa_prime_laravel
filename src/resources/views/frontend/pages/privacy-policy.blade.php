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
                        <h4>Aviso de Privacidad</h4>
                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}"> Inicio </a></li>
                                <li class="breadcrumb-item active" aria-current="page">Aviso de Privacidad</li>
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
        POLICY CONTENT START
    ===========================-->
    <section id="wsus__custom_page">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-8">
                    <div class="listing_details_text policy-content">
                        <div class="listing_det_header">
                            <div class="listing_det_header_text">
                                <h6>Aviso de Privacidad - Maosa Prime</h6>
                                <p class="host_name">
                                    <i class="far fa-calendar-alt mr-2"></i>
                                    Actualizado: {{ now()->format('d/m/Y') }}
                                </p>
                                <ul>
                                    <li>
                                        <a href="#">
                                            <i class="far fa-shield-check"></i>
                                            Documento Legal
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="listing_det_text policy-body">
                            {!! $privacyPolicy?->description !!}
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4">
                    <div class="listing_details_sidebar">
                        <div class="row">
                            <!-- Información de Contacto -->
                            <div class="col-12">
                                <div class="listing_det_side_address policy-sidebar-card">
                                    <h5 class="sidebar-title"><i class="fal fa-info-circle"></i> Información</h5>
                                    <div class="policy-info-item">
                                        <i class="fal fa-shield-alt"></i>
                                        <div>
                                            <strong>Protección de Datos</strong>
                                            <p>Tus datos personales están protegidos bajo las leyes vigentes.</p>
                                        </div>
                                    </div>
                                    <div class="policy-info-item">
                                        <i class="fal fa-user-lock"></i>
                                        <div>
                                            <strong>Confidencialidad</strong>
                                            <p>Mantenemos la confidencialidad de tu información.</p>
                                        </div>
                                    </div>
                                    <div class="policy-info-item">
                                        <i class="fal fa-sync-alt"></i>
                                        <div>
                                            <strong>Actualizaciones</strong>
                                            <p>Este aviso puede actualizarse periódicamente.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contacto -->
                            <div class="col-12">
                                <div class="listing_det_side_open_hour policy-sidebar-card">
                                    <h5 class="sidebar-title"><i class="fal fa-envelope"></i> Contacto</h5>
                                    <p>Si tienes dudas sobre este aviso de privacidad, contáctanos:</p>
                                    <a href="{{ url('/contact') }}" class="btn policy-contact-btn">
                                        <i class="fas fa-paper-plane"></i> Enviar Mensaje
                                    </a>
                                </div>
                            </div>

                            <!-- Enlaces Relacionados -->
                            <div class="col-12">
                                <div class="listing_det_side_list policy-sidebar-card">
                                    <h5 class="sidebar-title"><i class="fal fa-link"></i> Enlaces Relacionados</h5>
                                    <ul class="policy-links">
                                        <li>
                                            <a href="{{ url('/terms-and-condition') }}">
                                                <i class="fal fa-file-contract"></i>
                                                Términos y Condiciones
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
        POLICY CONTENT END
    ===========================-->

    <style>
        .policy-section {
            background: #f8f9fa;
        }
        .policy-content {
            background: #fff;
            border-radius: 10px;
            box-shadow: rgba(149, 157, 165, 0.1) 0px 8px 24px;
            padding: 30px;
        }
        .policy-content .listing_det_header {
            border-bottom: 2px solid #f66542;
            margin-bottom: 25px;
            padding-bottom: 20px;
        }
        .policy-content .listing_det_header_text h6 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        .policy-content .host_name {
            color: #666;
            font-size: 14px;
        }
        .policy-content .host_name i {
            color: #f66542;
        }
        .policy-body {
            line-height: 1.8;
            color: #444;
        }
        .policy-body h1, .policy-body h2, .policy-body h3,
        .policy-body h4, .policy-body h5, .policy-body h6 {
            color: #333;
            margin-top: 25px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .policy-body p {
            margin-bottom: 15px;
            text-align: justify;
        }
        .policy-body ul, .policy-body ol {
            padding-left: 25px;
            margin-bottom: 15px;
        }
        .policy-body li {
            margin-bottom: 8px;
        }
        .policy-sidebar-card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: rgba(149, 157, 165, 0.1) 0px 8px 24px;
        }
        .policy-sidebar-card .sidebar-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f66542;
        }
        .policy-sidebar-card .sidebar-title i {
            color: #f66542;
            margin-right: 10px;
        }
        .policy-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .policy-info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .policy-info-item i {
            font-size: 24px;
            color: #f66542;
            margin-right: 15px;
            margin-top: 3px;
        }
        .policy-info-item strong {
            display: block;
            font-size: 15px;
            color: #333;
            margin-bottom: 5px;
        }
        .policy-info-item p {
            font-size: 13px;
            color: #666;
            margin: 0;
            line-height: 1.5;
        }
        .policy-contact-btn {
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
        .policy-contact-btn:hover {
            background: linear-gradient(135deg, #e55a38 0%, #f66542 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(246, 101, 66, 0.3);
        }
        .policy-contact-btn i {
            margin-right: 8px;
        }
        .policy-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .policy-links li {
            margin-bottom: 12px;
        }
        .policy-links li:last-child {
            margin-bottom: 0;
        }
        .policy-links li a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .policy-links li a:hover {
            background: #f66542;
            color: #fff;
            transform: translateX(5px);
        }
        .policy-links li a i {
            margin-right: 12px;
            font-size: 18px;
            color: #f66542;
            transition: color 0.3s ease;
        }
        .policy-links li a:hover i {
            color: #fff;
        }
        @media (max-width: 991px) {
            .policy-content {
                margin-bottom: 30px;
            }
            .policy-content .listing_det_header_text h6 {
                font-size: 24px;
            }
        }
    </style>
@endsection
