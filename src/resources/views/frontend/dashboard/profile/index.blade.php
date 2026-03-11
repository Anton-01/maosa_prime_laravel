@extends('frontend.layouts.master')

@section('contents')
<section id="dashboard" class="dashboard-section">
    <div class="container">
        <div class="row">
            {{-- Sidebar --}}
            <div class="col-lg-3 mb-4">
                @include('frontend.dashboard.sidebar')
            </div>

            {{-- Main Content --}}
            <div class="col-lg-9">

                {{-- Alert success --}}
                @if(session()->has('statusUpdUsr'))
                    <div class="dashboard-alert dashboard-alert-success mb-4" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <span>¡Perfil actualizado correctamente!</span>
                        <button type="button" class="dashboard-alert-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                {{-- Welcome Card --}}
                <div class="dashboard-welcome mb-4">
                    <div class="dashboard-banner"
                         style="background-image: url('{{ $user->banner ? asset($user->banner) : '' }}');">
                        <div class="dashboard-banner-overlay"></div>
                    </div>
                    <div class="dashboard-user-info">
                        <div class="dashboard-avatar-wrap">
                            <img src="{{ asset($user->avatar) }}" alt="avatar" class="dashboard-avatar">
                        </div>
                        <div class="dashboard-user-meta">
                            <h5 class="dashboard-user-name">{{ $user->name }} </h5>
                            <p class="dashboard-user-email">
                                <i class="fas fa-envelope"></i> {{ $user->email }}
                            </p>
                            <div class="dashboard-badges">
                                <span class="dash-badge dash-badge-type">
                                    <i class="fas fa-user-shield"></i> {{ ucfirst($user->user_type) }}
                                </span>
                                @if($user->canViewPriceTable())
                                    <span class="dash-badge dash-badge-price">
                                        <i class="fas fa-gas-pump"></i> Tabla de Precios
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <a href="{{ route('user.profile.index') }}" class="dash-quick-card dash-quick-primary">
                            <i class="fas fa-user-edit"></i>
                            <span style="color: white;">Mi Perfil</span>
                        </a>
                    </div>
                    @if($user->canViewPriceTable())
                    <div class="col-sm-4">
                        <a href="{{ route('user.price-table.index') }}" class="dash-quick-card dash-quick-warning">
                            <i class="fas fa-gas-pump"></i>
                            <span style="color: white;">Tabla de Precios</span>
                        </a>
                    </div>
                    @endif
                    <div class="col-sm-4">
                        <a href="{{ route('listings') }}" class="dash-quick-card dash-quick-info">
                            <i class="fas fa-building"></i>
                            <span style="color: white;">Ver Proveedores</span>
                        </a>
                    </div>
                </div>

                {{-- Profile Form Card --}}
                <div class="dashboard-card mb-4">
                    <div class="dashboard-card-header">
                        <div class="dashboard-card-title">
                            <i class="fas fa-id-card"></i>
                            <span>Información básica</span>
                        </div>
                    </div>
                    <div class="dashboard-card-body">
                        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-xl-8">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="dash-form-group">
                                                <label>Nombre <span class="required">*</span></label>
                                                <input type="text" name="name" value="{{ $user->name }}" placeholder="Tu nombre completo" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="dash-form-group">
                                                <label>Teléfono <span class="required">*</span></label>
                                                <input type="text" name="phone" value="{{ $user->phone }}"
                                                       placeholder="Tu número de teléfono" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="dash-form-group">
                                                <label>Correo electrónico <span class="required">*</span></label>
                                                <input type="email" name="email" value="{{ $user->email }}"
                                                       placeholder="tu@correo.com" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="dash-form-group">
                                                <label>Dirección</label>
                                                <input type="text" name="address" value="{{ $user->address }}"
                                                       placeholder="Tu dirección">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="dash-form-group">
                                                <label>Sobre mí</label>
                                                <textarea name="about" rows="3"
                                                          placeholder="Cuéntanos un poco sobre ti...">{!! $user->about !!}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="dash-form-group mb-3">
                                        <label>Foto de perfil</label>
                                        <div class="dash-img-upload">
                                            <img src="{{ asset($user->avatar) }}" alt="avatar">
                                            <div class="dash-img-overlay">
                                                <i class="fas fa-camera"></i>
                                                <span>Cambiar foto</span>
                                            </div>
                                            <input type="file" name="avatar" accept="image/*">
                                            <input type="hidden" name="old_avatar" value="{{ $user->avatar }}">
                                        </div>
                                    </div>
                                    <div class="dash-form-group">
                                        <label>Imagen de fondo</label>
                                        <div class="dash-img-upload">
                                            <img src="{{ asset($user->banner) }}" alt="banner">
                                            <div class="dash-img-overlay">
                                                <i class="fas fa-image"></i>
                                                <span>Cambiar imagen</span>
                                            </div>
                                            <input type="file" name="banner" accept="image/*">
                                            <input type="hidden" name="old_banner" value="{{ $user->banner }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 pt-2">
                                    <button type="submit" class="dash-btn dash-btn-primary">
                                        <i class="fas fa-save"></i> Guardar cambios
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Password Card --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div class="dashboard-card-title">
                            <i class="fas fa-lock"></i>
                            <span>Cambiar contraseña</span>
                        </div>
                    </div>
                    <div class="dashboard-card-body">
                        <form action="{{ route('user.profile-password.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="dash-form-group">
                                        <label>Contraseña actual</label>
                                        <input type="password" name="current_password" placeholder="••••••••">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="dash-form-group">
                                        <label>Nueva contraseña</label>
                                        <input type="password" name="password" placeholder="••••••••">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="dash-form-group">
                                        <label>Confirmar contraseña</label>
                                        <input type="password" name="password_confirmation" placeholder="••••••••">
                                    </div>
                                </div>
                                <div class="col-12 pt-2">
                                    <button type="submit" class="dash-btn dash-btn-outline">
                                        <i class="fas fa-key"></i> Actualizar contraseña
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>{{-- /.col-lg-9 --}}
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
/* =============================================
   DASHBOARD – Estilos modernos
   ============================================= */
.dashboard-section {
    padding: 50px 0 70px;
    background: #f4f6f9;
    min-height: 80vh;
}

/* --- Alert --- */
.dashboard-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
}
.dashboard-alert-success {
    background: #eafaf1;
    border-left: 4px solid #27ae60;
    color: #1e7e4e;
}
.dashboard-alert-close {
    margin-left: auto;
    background: none;
    border: none;
    cursor: pointer;
    color: inherit;
    opacity: 0.6;
    transition: opacity 0.2s;
}
.dashboard-alert-close:hover { opacity: 1; }

/* --- Welcome Card --- */
.dashboard-welcome {
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    background: #fff;
}
.dashboard-banner {
    height: 110px;
    background: linear-gradient(135deg, var(--colorPrimary, #f66542) 0%, #c8402a 100%);
    background-size: cover;
    background-position: center;
    position: relative;
}
.dashboard-banner-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(246,101,66,0.7) 0%, rgba(200,64,42,0.5) 100%);
}
.dashboard-user-info {
    padding: 0 24px 24px;
    display: flex;
    align-items: flex-end;
    gap: 18px;
}
.dashboard-avatar-wrap {
    margin-top: -36px;
    flex-shrink: 0;
}
.dashboard-avatar {
    width: 76px !important;
    height: 76px !important;
    border-radius: 50%;
    border: 4px solid #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.18);
    object-fit: cover;
    background: #eee;
}
.dashboard-user-meta {
    padding-top: 12px;
    flex: 1;
}
.dashboard-user-name {
    font-size: 18px;
    font-weight: 700;
    color: #1c212b;
    margin-bottom: 2px;
}
.dashboard-user-email {
    font-size: 13px;
    color: #888;
    margin-bottom: 8px;
}
.dashboard-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.dash-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.dash-badge-type {
    background: #eef2ff;
    color: #4f46e5;
}
.dash-badge-price {
    background: #fff4ed;
    color: var(--colorPrimary, #f66542);
}

/* --- Quick Action Cards --- */
.dash-quick-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 20px 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.dash-quick-card i {
    font-size: 22px;
}
.dash-quick-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    text-decoration: none;
}
.dash-quick-primary {
    background: linear-gradient(135deg, #f66542 0%, #e04e2c 100%);
    color: #fff;
}
.dash-quick-warning {
    background: linear-gradient(135deg, #f5a623 0%, #e0900d 100%);
    color: #fff;
}
.dash-quick-info {
    background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
    color: #fff;
}

/* --- Dashboard Cards --- */
.dashboard-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    overflow: hidden;
}
.dashboard-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
}
.dashboard-card-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    font-weight: 700;
    color: #1c212b;
}
.dashboard-card-title i {
    color: var(--colorPrimary, #f66542);
    font-size: 16px;
}
.dashboard-card-body {
    padding: 24px;
}

/* --- Form Groups --- */
.dash-form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.dash-form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #555;
    margin-bottom: 0;
}
.dash-form-group input,
.dash-form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e8e8e8;
    border-radius: 8px;
    font-size: 14px;
    color: #333;
    background: #fafafa;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: inherit;
    resize: vertical;
}
.dash-form-group input:focus,
.dash-form-group textarea:focus {
    border-color: var(--colorPrimary, #f66542);
    box-shadow: 0 0 0 3px rgba(246,101,66,0.12);
    background: #fff;
}
.dash-form-group input::placeholder,
.dash-form-group textarea::placeholder {
    color: #bbb;
}
.required { color: #e53e3e; }

/* --- Image Upload --- */
.dash-img-upload {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    border: 2px dashed #e0e0e0;
    background: #f9f9f9;
    transition: border-color 0.2s;
}
.dash-img-upload:hover {
    border-color: var(--colorPrimary, #f66542);
}
.dash-img-upload img {
    width: 100%;
    height: 110px;
    object-fit: cover;
    display: block;
}
.dash-img-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    opacity: 0;
    transition: opacity 0.2s;
}
.dash-img-overlay i { font-size: 20px; }
.dash-img-upload:hover .dash-img-overlay { opacity: 1; }
.dash-img-upload input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
    z-index: 2;
}

/* --- Buttons --- */
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 26px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
}
.dash-btn:hover { transform: translateY(-1px); }
.dash-btn-primary {
    background: linear-gradient(135deg, var(--colorPrimary, #f66542) 0%, #d44e2c 100%);
    color: #fff;
    box-shadow: 0 3px 12px rgba(246,101,66,0.35);
}
.dash-btn-primary:hover {
    box-shadow: 0 5px 18px rgba(246,101,66,0.45);
}
.dash-btn-outline {
    background: #fff;
    color: var(--colorPrimary, #f66542);
    border: 2px solid var(--colorPrimary, #f66542);
}
.dash-btn-outline:hover {
    background: var(--colorPrimary, #f66542);
    color: #fff;
}

/* --- Responsive --- */
@media (max-width: 575px) {
    .dashboard-user-info {
        flex-direction: column;
        align-items: flex-start;
    }
    .dashboard-avatar-wrap {
        margin-top: -36px;
    }
}
</style>
@endpush
