@extends('admin.layout.auth')

@section('title', 'Iniciar Sesión - Mi Sistema')

@section('content')
<section>
    <div class="page-header min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                    <div class="card card-plain">
                        <div class="card-header pb-0 text-start">
                            <h4 class="font-weight-bolder">Iniciar Sesión</h4>
                            <p class="mb-0">Ingresa tu email y contraseña para iniciar sesión</p>
                        </div>
                        <div class="card-body">
                            <form role="form" method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <input type="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           placeholder="Email" 
                                           aria-label="Email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           autofocus>
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
                                
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="rememberMe"
                                           name="remember">
                                    <label class="form-check-label" for="rememberMe">Recordarme</label>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">Iniciar Sesión</button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center pt-0 px-lg-2 px-1">
                            <p class="mb-4 text-sm mx-auto">
                                ¿No tienes una cuenta?
                                <a href="{{ route('register') }}" class="text-primary text-gradient font-weight-bold">Regístrate</a>
                            </p>
                            
                            @if (Route::has('password.request'))
                                <p class="mb-2 text-sm mx-auto">
                                    <a href="{{ route('password.request') }}" class="text-primary text-gradient font-weight-bold">¿Olvidaste tu contraseña?</a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                    <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden" 
                         style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg'); background-size: cover;">
                        <span class="mask bg-gradient-primary opacity-6"></span>
                        <h4 class="mt-5 text-white font-weight-bolder position-relative">"La atención es la nueva moneda"</h4>
                        <p class="text-white position-relative">Mientras más sencilla se vea la escritura, más esfuerzo puso realmente el escritor en el proceso.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection