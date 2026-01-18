<!DOCTYPE html>
<html lang="es">

<head>
    @include('staff.inc.head')
    @yield('styles')
</head>

<body class="">
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <!-- Navbar Simple para Auth -->
                <nav class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4">
                    <div class="container-fluid px-0">
                        <a class="navbar-brand font-weight-bolder ms-lg-3" href="">
                            <i class="ni ni-shop me-2"></i>
                            staff Panel - Mi Tienda
                        </a>
                        <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon mt-2">
                                <span class="navbar-toggler-bar bar1"></span>
                                <span class="navbar-toggler-bar bar2"></span>
                                <span class="navbar-toggler-bar bar3"></span>
                            </span>
                        </button>
                        <div class="collapse navbar-collapse" id="navigation">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center me-2" href="">
                                        <i class="fas fa-key opacity-6 text-dark me-1"></i>
                                        <span class="d-none d-sm-inline">Iniciar Sesión</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center me-2" href="{{ url('/') }}" target="_blank">
                                        <i class="fas fa-store opacity-6 text-dark me-1"></i>
                                        <span class="d-none d-sm-inline">Ir a la Tienda</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>
        </div>
    </div>
    
    <main class="main-content mt-0">
        @yield('content')
    </main>
    
    <!-- Footer Simple -->
    <footer class="footer py-3 position-absolute bottom-0 w-100">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-12 text-center">
                    <div class="copyright text-sm text-muted">
                        © <script>document.write(new Date().getFullYear())</script>
                        Sistema de staffistración de Tiendas | 
                        <i class="fa fa-shield-alt"></i> Área Segura
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!--   Core JS Files   -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script> --}}
    
    @yield('scripts')
    
    {{-- <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script> --}}
    
    <!-- Control Center for Soft Dashboard -->
    {{-- <script src="{{ asset('assets/js/argon-dashboard.min.js?v=2.1.0') }}"></script> --}}
</body>

</html>