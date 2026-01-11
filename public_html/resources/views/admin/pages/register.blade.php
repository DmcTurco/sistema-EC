@extends('layout.auth')

@section('title', 'Registrarse - Mi Sistema')

@section('content')
<section>
    <div class="page-header min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                    <div class="card card-plain">
                        <div class="card-header pb-0 text-start">
                            <h4 class="font-weight-bolder">Crear Cuenta</h4>
                            <p class="mb-0">Ingresa tus datos para registrarte</p>
                        </div>
                        <div class="card-body">
                            <form role="form" method="POST" action="{{ route('register') }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <input type="text" 
                                           class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                           placeholder="Nombre completo" 
                                           aria-label="Name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required
                                           autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <input type="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           placeholder="Email" 
                                           aria-label="Email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <input type="password" 
                                           class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                           placeholder="Contraseña" 
                                           aria-label="Password"
                                           name="password"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <input type="password" 
                                           class="form-control form-control-lg" 
                                           placeholder="Confirmar Contraseña" 
                                           aria-label="Password Confirmation"
                                           name="password_confirmation"
                                           required>
                                </div>
                                
                                <div class="form-check form-check-info text-start">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" 
                                           type="checkbox" 
                                           id="flexCheckDefault"
                                           name="terms"
                                           required>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Acepto los <a href="javascript:;" class="text-dark font-weight-bolder">Términos y Condiciones</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">Registrarse</button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center pt-0 px-lg-2 px-1">
                            <p class="mb-4 text-sm mx-auto">
                                ¿Ya tienes una cuenta?
                                <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">Iniciar Sesión</a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                    <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden" 
                         style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signup-ill.jpg'); background-size: cover;">
                        <span class="mask bg-gradient-primary opacity-6"></span>
                        <h4 class="mt-5 text-white font-weight-bolder position-relative">"Comienza tu viaje con nosotros"</h4>
                        <p class="text-white position-relative">Únete a miles de usuarios que confían en nuestra plataforma para gestionar su información.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>