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
                        <h4>Iniciar Sesión</h4>
                        <nav style="--bs-breadcrumb-divider: '';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#"> Inicio </a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Iniciar Sesión </li>
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


    <!--=========================
            SIGN IN START
    ==========================-->
    <section class="wsus__login_page">
        <div class="container">
            <div class="row">
                <div class="col-xxl-5 col-xl-6 col-md-9 col-lg-7 m-auto">
                    @if(session()->has('status') || $errors->any())
                        <x-alert-message />
                    @endif
                    <div class="wsus__login_area">
                        <h2>¡Bienvenido!</h2>
                        <p>Inicia sesión para continuar</p>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="wsus__login_imput">
                                        <label>Correo Electrónico</label>
                                        <input type="email" placeholder="Correo Electrónico" name="email" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div style="display:none;"><input type="text" name="honeypot" id="honeypot" value=""></div>
                                <div class="col-xl-12">
                                    <div class="wsus__login_imput">
                                        <label>Contraseña</label>
                                        <input type="password" placeholder="Contraseña" name="password" required >
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="wsus__login_imput wsus__login_check_area">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="flexCheckDefault" name="remember">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Recordarme
                                            </label>
                                        </div>
                                        <a href="{{ route('password.request') }}">¿Ha olvidado la contraseña?</a>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="wsus__login_imput">
                                        <button type="submit">Iniciar Sesión</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--=========================
            SIGN IN END
    ==========================-->
@endsection
