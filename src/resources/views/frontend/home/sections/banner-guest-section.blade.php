<section class="hg-section">
    <div class="hg-wrapper">

        {{-- ======= LEFT PANEL — Hero image with industrial overlay ======= --}}
        <div class="hg-left">

            {{-- Background: image from admin (configurable) with dark overlay --}}
            @if(@$hero->background)
                <div class="hg-bg-img" style="background-image: url({{ asset(@$hero->background) }});"></div>
            @endif
            <div class="hg-bg-overlay"></div>

            {{-- Subtle refinery wave decoration at bottom (matches auth layout) --}}
            <svg class="hg-wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 580 120"
                 preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,60 Q72,42 145,55 Q218,68 290,50 Q362,32 435,48 Q507,62 580,46 L580,120 L0,120 Z"
                      fill="#1a2870" opacity="0.5"/>
                <path d="M0,80 Q90,62 180,74 Q270,86 360,68 Q450,52 540,66 Q560,70 580,62 L580,120 L0,120 Z"
                      fill="#1a2870" opacity="0.38"/>
            </svg>

            {{-- Floating decorative badges --}}
            <div class="hg-badge hg-badge-1">
                <i class="fas fa-shield-alt"></i>
                <span>Proveedores Verificados</span>
            </div>
            <div class="hg-badge hg-badge-2">
                <i class="fas fa-handshake"></i>
                <span>Servicio de Calidad</span>
            </div>

            {{-- Bottom left brand label --}}
            <div class="hg-left-footer">
                <p>Plataforma integral para la gestión y monitoreo<br>de plantas de tratamiento de hidrocarburos.</p>
            </div>
        </div>
        {{-- /.hg-left --}}

        {{-- ======= RIGHT PANEL — Welcome content ======= --}}
        <div class="hg-right">

            {{-- Welcome pill badge --}}
            <div class="hg-welcome-badge">
                <i class="fas fa-star"></i>
                <span>Bienvenido a nuestra plataforma</span>
            </div>

            {{-- Title from hero model --}}
            <h1 class="hg-title">{!! @$hero->title ?? config('settings.site_name') !!}</h1>

            {{-- Subtitle from hero model --}}
            @if(@$hero->sub_title)
                <div class="hg-subtitle">{!! @$hero->sub_title !!}</div>
            @endif

            {{-- CTA --}}
            <div class="hg-cta">
                <a href="{{ route('login') }}" class="hg-btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </a>
            </div>

            {{-- Feature checklist --}}
            <div class="hg-features">
                <div class="hg-feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Acceso a proveedores exclusivos</span>
                </div>
                <div class="hg-feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Precios actualizados en tiempo real</span>
                </div>
                <div class="hg-feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Soporte personalizado</span>
                </div>
            </div>

        </div>
        {{-- /.hg-right --}}

    </div>
</section>

<style>
/* ============================================================
   HERO GUEST SECTION — same visual language as auth pages
   ============================================================ */
.hg-section {
    background: linear-gradient(145deg, #0f1c52 0%, #1a0b50 45%, #0a1035 100%);
    overflow: hidden;
}

.hg-wrapper {
    display: flex;
    min-height: calc(100vh - 120px);
    width: 100%;
}

/* ---- Left panel ---- */
.hg-left {
    flex: 1 1 55%;
    position: relative;
    background: #070e30;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

.hg-bg-img {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0.28;
}

.hg-bg-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(7, 14, 48, 0.55) 0%,
        rgba(7, 14, 48, 0.2)  40%,
        rgba(7, 14, 48, 0.7)  80%,
        rgba(7, 14, 48, 0.92) 100%
    );
    z-index: 1;
}

.hg-wave-svg {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 120px;
    z-index: 2;
}

/* Floating badges */
.hg-badge {
    position: absolute;
    z-index: 3;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(8px);
    padding: 12px 22px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
    animation: hgFloat 3.5s ease-in-out infinite;
}
.hg-badge i {
    color: var(--colorPrimary);
    font-size: 18px;
}
.hg-badge span {
    font-weight: 600;
    color: #1a1f5e;
    font-size: 13.5px;
    white-space: nowrap;
}
.hg-badge-1 {
    top: 22%;
    right: 8%;
    animation-delay: 0s;
}
.hg-badge-2 {
    bottom: 28%;
    left: 6%;
    animation-delay: 1.8s;
}
@keyframes hgFloat {
    0%, 100% { transform: translateY(0);    }
    50%       { transform: translateY(-12px); }
}

/* Bottom brand text */
.hg-left-footer {
    position: relative;
    z-index: 3;
    padding: 0 40px 36px;
}
.hg-left-footer p {
    font-size: 13px;
    color: rgba(200, 215, 255, 0.55);
    line-height: 1.75;
    margin: 0;
}

/* ---- Right panel ---- */
.hg-right {
    flex: 0 0 420px;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 52px 46px;
    overflow-y: auto;
}

/* Welcome pill */
.hg-welcome-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    color: var(--colorPrimary);
    padding: 9px 18px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 22px;
    align-self: flex-start;
}
.hg-welcome-badge i {
    font-size: 12px;
}

/* Title */
.hg-title {
    font-size: 34px;
    font-weight: 800;
    color: #1a1f5e;
    line-height: 1.2;
    margin-bottom: 16px;
}
.hg-title strong,
.hg-title b { color: var(--colorPrimary); font-weight: 800; }

/* Subtitle */
.hg-subtitle {
    font-size: 15px;
    color: #777;
    line-height: 1.72;
    margin-bottom: 32px;
}
.hg-subtitle p { margin: 0; }

/* CTA */
.hg-cta {
    margin-bottom: 36px;
}
.hg-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--colorPrimary);
    color: #fff;
    padding: 14px 32px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    text-decoration: none;
    transition: filter 0.25s, transform 0.2s, box-shadow 0.25s;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
    border: none;
}
.hg-btn-primary:hover {
    color: #fff;
    filter: brightness(1.12);
    transform: translateY(-2px);
    box-shadow: 0 10px 26px rgba(0, 0, 0, 0.22);
    text-decoration: none;
}

/* Features */
.hg-features {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.hg-feature-item {
    display: flex;
    align-items: center;
    gap: 11px;
    font-size: 14.5px;
    color: #555;
}
.hg-feature-item i {
    color: #22c55e;
    font-size: 16px;
    flex-shrink: 0;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 1024px) {
    .hg-right {
        flex: 0 0 380px;
        padding: 44px 36px;
    }
    .hg-title { font-size: 28px; }
}

@media (max-width: 860px) {
    .hg-wrapper {
        flex-direction: column;
        min-height: auto;
    }
    .hg-left {
        flex: 0 0 240px;
        height: 240px;
    }
    .hg-left-footer { display: none; }
    .hg-badge-2 { display: none; }
    .hg-badge-1 {
        top: auto;
        bottom: 16px;
        right: 16px;
        animation: none;
    }
    .hg-right {
        flex: 0 0 auto;
        width: 100%;
        padding: 36px 32px 44px;
    }
    .hg-title { font-size: 26px; }
}

@media (max-width: 580px) {
    .hg-left { height: 190px; }
    .hg-badge { display: none; }
    .hg-right { padding: 30px 22px 40px; }
    .hg-title { font-size: 24px; }
    .hg-subtitle { font-size: 14px; }
    .hg-btn-primary {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 400px) {
    .hg-left { display: none; }
    .hg-right { padding: 28px 18px; }
}
</style>
