@extends('staff.layout.auth')

@section('title', 'Panel Empleado - Login')

@section('content')
    <section>
        <div class="page-header min-vh-100">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                        <div class="card card-plain">
                            <div class="card-header pb-0 text-center">
                                <div class="mb-3">
                                    <i class="ni ni-shop text-primary" style="font-size: 3rem;"></i>
                                </div>
                                <h4 class="font-weight-bolder">Panel de Administración</h4>
                                <p class="mb-0">Ingresa tus credenciales para acceder al sistema</p>
                            </div>
                            <div class="card-body">
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <span class="alert-icon"><i class="ni ni-fat-remove"></i></span>
                                        <span class="alert-text">{{ session('error') }}</span>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <span class="alert-icon"><i class="ni ni-check-bold"></i></span>
                                        <span class="alert-text">{{ session('success') }}</span>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <form role="form" method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <label class="form-label">Correo Electrónico</label>
                                    <div class="mb-3">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            placeholder="staff@tienda.com" aria-label="Email" name="email"
                                            value="{{ old('email') }}" required autofocus>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <label class="form-label">Contraseña</label>
                                    <div class="mb-3">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            placeholder="••••••••" aria-label="Password" name="password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-check form-switch d-flex align-items-center mb-3">
                                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                        <label class="form-check-label mb-0 ms-2" for="rememberMe">Mantener sesión
                                            iniciada</label>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary w-100 mt-4 mb-0">
                                            <i class="ni ni-key-25 me-2"></i>Iniciar Sesión
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                @if (Route::has('admin.password.request'))
                                    <p class="mb-2 text-sm mx-auto">
                                        <a href=""
                                            class="text-primary font-weight-bold">
                                            <i class="ni ni-lock-circle-open me-1"></i>¿Olvidaste tu contraseña?
                                        </a>
                                    </p>
                                @endif

                                <hr class="horizontal dark my-3">

                                <p class="mb-0 text-xs text-secondary">
                                    <i class="ni ni-shield-check-10 me-1"></i>
                                    Acceso restringido solo para personal autorizado
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                        <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                            style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80'); background-size: cover;">
                            <span class="mask bg-gradient-primary opacity-7"></span>
                            <div class="position-relative text-center">
                                <i class="ni ni-cart text-white mb-3" style="font-size: 4rem;"></i>
                                <h4 class="text-white font-weight-bolder">"Sistema de Gestión Comercial"</h4>
                                <p class="text-white">Administra tu tienda de manera eficiente y profesional. Control total
                                    de inventario, ventas, clientes y reportes en tiempo real.</p>

                                <div class="mt-5">
                                    <div class="row text-white">
                                        <div class="col-4">
                                            <i class="ni ni-box-2 mb-2" style="font-size: 2rem;"></i>
                                            <p class="text-xs mb-0">Inventario</p>
                                        </div>
                                        <div class="col-4">
                                            <i class="ni ni-money-coins mb-2" style="font-size: 2rem;"></i>
                                            <p class="text-xs mb-0">Ventas</p>
                                        </div>
                                        <div class="col-4">
                                            <i class="ni ni-chart-bar-32 mb-2" style="font-size: 2rem;"></i>
                                            <p class="text-xs mb-0">Reportes</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('styles')
    <style>
        /* Animación del logo */
        .ni-shop {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* Mejora del form */
        .form-control:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
        }
    </style>
@endsection
