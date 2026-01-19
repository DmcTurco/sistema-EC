<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>

        {{-- Logo y Nombre --}}
        <a class="navbar-brand m-0 d-flex align-items-center" href="{{ route('staff.home') }}">
            <div class="icon icon-shape icon-sm bg-gradient-warning shadow text-center border-radius-md me-2">
                <i class="ni ni-badge text-white text-sm opacity-10"></i>
            </div>
            <div>
                <span class="font-weight-bold text-sm">Mi Sistema</span>
                <p class="text-xs text-secondary mb-0">Panel Staff</p>
            </div>
        </a>
    </div>

    <hr class="horizontal dark mt-0">

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.home') ? 'active' : '' }}"
                    href="{{ route('staff.home') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            {{-- Mis Publicaciones --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.post.*') ? 'active' : '' }}"
                    href="{{ route('staff.post.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-album-2 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Mis Publicaciones</span>
                </a>
            </li>

            {{-- Crear Publicación --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.post.create') ? 'active' : '' }}"
                    href="{{ route('staff.post.create') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-fat-add text-success text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Nueva Publicación</span>
                </a>
            </li>

            {{-- Divider --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Estadísticas</h6>
            </li>

            {{-- Mis Ventas --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.sales*') ? 'active' : '' }}" href="">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-chart-bar-32 text-info text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Mis Ventas</span>
                </a>
            </li>

            {{-- Estadísticas --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.stats*') ? 'active' : '' }}" href="">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-analytics text-danger text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Estadísticas</span>
                </a>
            </li>

            {{-- Divider --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Mi Cuenta</h6>
            </li>

            {{-- Mi Perfil --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.profile*') ? 'active' : '' }}" href="">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Mi Perfil</span>
                </a>
            </li>

            {{-- Cerrar Sesión --}}
            <li class="nav-item">
                <form method="POST" action="{{  route('logout') }}" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="nav-link text-start w-100 border-0 bg-transparent">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-user-run text-danger text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Cerrar Sesión</span>
                    </button>
                </form>
            </li>

        </ul>
    </div>

    {{-- Footer del Sidebar --}}
    <div class="sidenav-footer mx-3 mt-3">
        <div class="card card-plain shadow-none" id="sidenavCard">
            <div class="card-body text-center p-3 w-100">
                <div class="docs-info">
                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                    <p class="text-xs font-weight-bold mb-0">
                        {{ Auth::user()->store->name ?? 'Staff' }}
                    </p>
                    <p class="text-xs text-secondary mb-0">
                        {{ Auth::user()->email }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>