@extends('staff.layout.base')

@section('title', 'Dashboard - Staff')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>Bienvenido, {{ Auth::guard('staff')->user()->name }}</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-check text-info" aria-hidden="true"></i>
                                <span class="font-weight-bold ms-1">
                                    {{ Auth::guard('staff')->user()->store ? Auth::guard('staff')->user()->store->name : 'Sin tienda asignada' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-lg-6 col-5 my-auto text-end">
                            <a href="{{ route('staff.post.create') }}" class="btn btn-primary btn-sm mb-0">
                                <i class="fas fa-plus"></i> Nueva Publicación
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Publicaciones</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $stats['total_posts'] }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-collection text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Públicas</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $stats['public_posts'] }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Vistas</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($stats['total_views']) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-active-40 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Ventas</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($stats['total_sales']) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Publicaciones Recientes y Populares -->
    <div class="row mt-4">
        <!-- Publicaciones Recientes -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Publicaciones Recientes</h6>
                </div>
                <div class="card-body p-3">
                    @forelse($recent_posts as $post)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->product->name }}" class="rounded"
                                    style="width: 60px; height: 60px; object-fit: cover;">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-sm">{{ $post->product->name }}</h6>
                                <p class="text-xs text-muted mb-0">
                                    {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="text-end">
                                {!! $post->status_badge !!}
                                <p class="text-xs text-muted mb-0 mt-1">
                                    <i class="fas fa-eye me-1"></i>{{ $post->views }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-video fa-3x text-secondary mb-3"></i>
                            <p class="text-muted">No tienes publicaciones aún</p>
                            <a href="{{ route('staff.post.create') }}" class="btn btn-primary btn-sm">
                                Crear Primera Publicación
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Más Vistas -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Publicaciones Más Vistas</h6>
                </div>
                <div class="card-body p-3">
                    @forelse($popular_posts as $post)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->product->name }}" class="rounded"
                                    style="width: 60px; height: 60px; object-fit: cover;">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-sm">{{ $post->product->name }}</h6>
                                <p class="text-xs text-muted mb-0">
                                    <i class="fas fa-shopping-cart me-1"></i>{{ $post->sales }} ventas
                                </p>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 text-info">{{ number_format($post->views) }}</h6>
                                <p class="text-xs text-muted mb-0">vistas</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-secondary mb-3"></i>
                            <p class="text-muted">Sin estadísticas aún</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
