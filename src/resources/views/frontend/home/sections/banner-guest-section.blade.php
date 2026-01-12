<section class="hero-guest-section">
    <div class="hero-guest-container">
        <div class="container-fluid p-0">
            <div class="row g-0 hero-guest-row">
                <!-- Imagen lado izquierdo -->
                <div class="col-xl-6 col-lg-6 hero-guest-image-col">
                    <div class="hero-guest-image" style="background-image: url({{ asset(@$hero->background) }});">
                        <div class="hero-image-overlay"></div>
                        <div class="hero-floating-elements">
                            <div class="floating-badge badge-1">
                                <i class="fas fa-shield-alt"></i>
                                <span>Proveedores Verificados</span>
                            </div>
                            <div class="floating-badge badge-2">
                                <i class="fas fa-handshake"></i>
                                <span>Servicio de Calidad</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Contenido lado derecho -->
                <div class="col-xl-6 col-lg-6 hero-guest-content-col">
                    <div class="hero-guest-content">
                        <div class="hero-content-inner">
                            <div class="hero-badge-top">
                                <span><i class="fas fa-star"></i> Bienvenido a nuestra plataforma</span>
                            </div>
                            <h1 class="hero-title">{!! @$hero->title !!}</h1>
                            <div class="hero-subtitle">
                                {!! @$hero->sub_title !!}
                            </div>
                            <div class="hero-cta-buttons">
                                <a href="{{ route('login') }}" class="btn hero-btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                </a>
                            </div>
                            <div class="hero-features-list">
                                <div class="hero-feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Acceso a proveedores exclusivos</span>
                                </div>
                                <div class="hero-feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Precios actualizados</span>
                                </div>
                                <div class="hero-feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Soporte personalizado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .hero-guest-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        overflow: hidden;
    }
    .hero-guest-container {
        min-height: 85vh;
    }
    .hero-guest-row {
        min-height: 85vh;
    }
    .hero-guest-image-col {
        position: relative;
    }
    .hero-guest-image {
        height: 100%;
        min-height: 85vh;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
    }
    .hero-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(246, 101, 66, 0.1) 0%, rgba(0, 0, 0, 0.3) 100%);
    }
    .hero-floating-elements {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
    }
    .floating-badge {
        position: absolute;
        background: #fff;
        padding: 15px 25px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        animation: float 3s ease-in-out infinite;
    }
    .floating-badge i {
        color: #f66542;
        font-size: 20px;
    }
    .floating-badge span {
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    .floating-badge.badge-1 {
        top: 20%;
        right: 10%;
        animation-delay: 0s;
    }
    .floating-badge.badge-2 {
        bottom: 25%;
        left: 5%;
        animation-delay: 1.5s;
    }
    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-15px);
        }
    }
    .hero-guest-content-col {
        display: flex;
        align-items: center;
        background: #fff;
    }
    .hero-guest-content {
        width: 100%;
        padding: 60px 80px;
    }
    .hero-content-inner {
        max-width: 550px;
        margin: 0 auto;
    }
    .hero-badge-top {
        margin-bottom: 25px;
    }
    .hero-badge-top span {
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
    .hero-badge-top i {
        font-size: 12px;
    }
    .hero-title {
        font-size: 42px;
        font-weight: 700;
        color: #1a1a2e;
        line-height: 1.2;
        margin-bottom: 20px;
    }
    .hero-subtitle {
        font-size: 16px;
        color: #666;
        line-height: 1.7;
        margin-bottom: 35px;
    }
    .hero-subtitle p {
        margin-bottom: 0;
    }
    .hero-cta-buttons {
        display: flex;
        gap: 15px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }
    .hero-btn-primary {
        background: linear-gradient(135deg, #f66542 0%, #ff8a65 100%);
        color: #fff;
        padding: 15px 35px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 5px 20px rgba(246, 101, 66, 0.3);
    }
    .hero-btn-primary:hover {
        background: linear-gradient(135deg, #e55a38 0%, #f66542 100%);
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(246, 101, 66, 0.4);
    }
    .hero-btn-secondary {
        background: #fff;
        color: #333;
        padding: 15px 35px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        border: 2px solid #e0e0e0;
    }
    .hero-btn-secondary:hover {
        background: #f8f9fa;
        color: #f66542;
        border-color: #f66542;
        transform: translateY(-3px);
    }
    .hero-features-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .hero-feature-item {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #555;
        font-size: 15px;
    }
    .hero-feature-item i {
        color: #28a745;
        font-size: 16px;
    }
    /* Responsive */
    @media (max-width: 1199px) {
        .hero-guest-content {
            padding: 50px 50px;
        }
        .hero-title {
            font-size: 36px;
        }
    }
    @media (max-width: 991px) {
        .hero-guest-image {
            min-height: 400px;
        }
        .hero-guest-container,
        .hero-guest-row {
            min-height: auto;
        }
        .hero-guest-content {
            padding: 50px 30px;
        }
        .hero-title {
            font-size: 32px;
        }
        .floating-badge {
            display: none;
        }
    }
    @media (max-width: 575px) {
        .hero-guest-content {
            padding: 40px 20px;
        }
        .hero-title {
            font-size: 28px;
        }
        .hero-cta-buttons {
            flex-direction: column;
        }
        .hero-btn-primary,
        .hero-btn-secondary {
            width: 100%;
            justify-content: center;
        }
    }
</style>
