<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('staff.home') }}">
            <img src="{{ asset('assets/img/logo-ct-dark.png') }}" width="26px" height="26px"
                class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">Mi Sistema</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.home') ? 'active' : '' }}"
                    href="{{ route('staff.home') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
{{-- 
            <li class="nav-item {{ request()->routeIs('staff.products.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('staff.products.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Productos</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('staff.stores.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('staff.stores.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Tienda</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('staff.staff.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('staff.staff.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Empleados</span>
                </a>
            </li> --}}

            {{-- <li class="nav-item">
                <a class="nav-link" href="">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Tablas</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link " href="">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Facturación</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link " href="">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-app text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Virtual Reality</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link " href="">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-world-2 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">RTL</span>
                </a>
            </li>
            
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Páginas de Cuenta</h6>
            </li>
            
            <li class="nav-item">
                <a class="nav-link " href="">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Perfil</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link " href="">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Iniciar Sesión</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-collection text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Registrarse</span>
                </a>
            </li> --}}
        </ul>
    </div>


</aside>
