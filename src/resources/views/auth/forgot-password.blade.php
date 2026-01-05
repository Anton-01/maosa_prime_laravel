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
                            <h4>¿Ha olvidado su contraseña?</h4>
                            <nav style="--bs-breadcrumb-divider: '';" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#"> Inicio </a></li>
                                    <li class="breadcrumb-item active" aria-current="page"> ¿Ha olvidado su contraseña? </li>
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
                        @if(session()->has('status'))
                            <x-alert-message />
                        @endif
                        <div class="wsus__login_area">
                            <p>¿Ha olvidado su contraseña? No se preocupe. <br> Indíquenos su dirección de correo electrónico y le enviaremos un enlace para restablecer la contraseña que le permitirá elegir una nueva.</p>
                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="wsus__login_imput">
                                            <label>Correo electrónico</label>
                                            <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" required>
                                        </div>
                                    </div>
                                    <div style="display:none;"><input type="text" name="honeypot" id="honeypot" value=""></div>

                                    <div class="col-xl-12">
                                        <div class="wsus__login_imput">
                                            <button type="submit">Enlace de restablecimiento de contraseña</button>
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
