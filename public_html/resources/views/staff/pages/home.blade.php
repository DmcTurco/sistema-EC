@extends('staff.layout.base')

@section('title', 'Dashboard - Staff')

@section('content')

    {{-- Header Bienvenida --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-background card-background-after-none align-items-start mb-4">
                <div class="full-background"
                    style="background-image: url('{{ asset('assets/img/curved-images/curved14.jpg') }}')"></div>
                <div class="card-body text-start p-4 w-100">
                    <h3 class="text-white mb-2">¡Hola, {{ Auth::guard('staff')->user()->name }}! 👋</h3>
                    <p class="mb-4 font-weight-semibold text-white">
                        Aquí está el resumen de tus publicaciones y ventas.
                    </p>
                    <a href="{{ route('staff.post.create') }}" class="btn btn-white btn-sm mb-0 me-2">
                        <i class="fas fa-plus me-1"></i> Nueva Publicación
                    </a>
                    <a href="{{ route('staff.post.index') }}" class="btn btn-outline-white btn-sm mb-0">
                        <i class="fas fa-list me-1"></i> Ver Todas
                    </a>

                    <span class="badge badge-sm bg-gradient-success mt-3 d-block w-auto">
                        <i class="fas fa-store me-1"></i>
                        {{ Auth::guard('staff')->user()->store ? Auth::guard('staff')->user()->store->name : 'Sin tienda asignada' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas Principales --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold opacity-7">Total Publicaciones</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $stats['total_posts'] }}
                                    <span class="text-success text-sm font-weight-bolder">
                                        +{{ $stats['public_posts'] }}
                                    </span>
                                </h5>
                                <p class="mb-0 text-xs">
                                    <span class="text-success font-weight-bolder">{{ $stats['public_posts'] }}</span>
                                    públicas
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center border-radius-md">
                                <i class="ni ni-album-2 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold opacity-7">Total Vistas</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($stats['total_views']) }}
                                </h5>
                                <p class="mb-0 text-xs">
                                    <span class="text-info font-weight-bolder">
                                        {{ $stats['total_posts'] > 0 ? number_format($stats['total_views'] / $stats['total_posts'], 0) : 0 }}
                                    </span> promedio/post
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center border-radius-md">
                                <i class="ni ni-active-40 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold opacity-7">Total Ventas</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($stats['total_sales']) }}
                                </h5>
                                <p class="mb-0 text-xs">
                                    <span class="text-warning font-weight-bolder">
                                        {{ $stats['total_views'] > 0 ? number_format(($stats['total_sales'] / $stats['total_views']) * 100, 1) : 0 }}%
                                    </span> conversión
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold opacity-7">Rendimiento</p>
                                <h5 class="font-weight-bolder mb-0">
                                    @php
                                        $performance =
                                            $stats['total_posts'] > 0 && $stats['total_views'] > 0
                                                ? min(100, $stats['total_sales'] / ($stats['total_views'] / 10))
                                                : 0;
                                    @endphp
                                    {{ number_format($performance, 0) }}%
                                </h5>
                                <p class="mb-0 text-xs">
                                    <span
                                        class="text-{{ $performance >= 70 ? 'success' : ($performance >= 40 ? 'warning' : 'danger') }} font-weight-bolder">
                                        {{ $performance >= 70 ? 'Excelente' : ($performance >= 40 ? 'Bueno' : 'Mejorar') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center border-radius-md">
                                <i class="ni ni-trophy text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfica y Top Posts --}}
    <div class="row mt-4">
        {{-- Gráfica de Rendimiento --}}
        <div class="col-lg-7 mb-4">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Rendimiento de tus Publicaciones</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-chart-line text-success"></i>
                        <span class="font-weight-bold ms-1">Vistas vs Ventas</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-performance" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 3 Posts --}}
        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">🏆 Tus Mejores Posts</h6>
                        <span class="badge badge-sm bg-gradient-success">Top 3</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    @forelse($popular_posts->take(3) as $index => $post)
                        <div class="timeline timeline-one-side">
                            <div class="timeline-block mb-3">
                                <span
                                    class="timeline-step bg-gradient-{{ $index === 0 ? 'warning' : ($index === 1 ? 'info' : 'success') }}">
                                    <i class="ni ni-trophy text-white"></i>
                                </span>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $post->thumbnail_url }}" alt="{{ $post->product->name }}"
                                                class="rounded me-2"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h6 class="text-dark text-sm font-weight-bold mb-0">
                                                    {{ $post->product->name }}</h6>
                                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                                    <i class="fas fa-eye me-1"></i>{{ number_format($post->views) }}
                                                    vistas
                                                    <i class="fas fa-shopping-cart ms-2 me-1"></i>{{ $post->sales }}
                                                    ventas
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-medal fa-3x text-secondary mb-3"></i>
                            <p class="text-muted mb-3">Sin datos suficientes</p>
                            <a href="{{ route('staff.post.create') }}" class="btn btn-sm btn-primary">
                                Crear Publicación
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Publicaciones Recientes --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">📱 Mis Publicaciones Recientes</h6>
                        <a href="{{ route('staff.post.index') }}" class="btn btn-sm btn-outline-primary mb-0">
                            Ver Todas <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-3">
                    @forelse($recent_posts as $post)
                        <div class="row align-items-center mb-3 pb-3 border-bottom">
                            <div class="col-lg-2 col-md-3">
                                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->product->name }}"
                                    class="rounded img-fluid" style="max-height: 80px; object-fit: cover; width: 100%;">
                            </div>
                            <div class="col-lg-3 col-md-4">
                                <h6 class="mb-1 text-sm font-weight-bold">{{ $post->product->name }}</h6>
                                <p class="text-xs text-muted mb-0">
                                    <i class="fas fa-clock me-1"></i>{{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="col-lg-2 col-md-2 text-center">
                                {!! $post->status_badge !!}
                            </div>
                            <div class="col-lg-2 col-md-2 text-center">
                                <div class="d-flex flex-column">
                                    <span class="mb-1 text-xs text-dark font-weight-bold">
                                        <i class="fas fa-eye text-info me-1"></i>{{ number_format($post->views) }}
                                    </span>
                                    <span class="text-xs text-secondary">vistas</span>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 text-center">
                                <div class="d-flex flex-column">
                                    <span class="mb-1 text-xs text-dark font-weight-bold">
                                        <i class="fas fa-shopping-cart text-warning me-1"></i>{{ $post->sales }}
                                    </span>
                                    <span class="text-xs text-secondary">ventas</span>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-1 text-end">
                                <a href="{{ route('staff.post.edit', $post->id) }}"
                                    class="btn btn-link text-dark px-2 mb-0">
                                    <i class="fas fa-pencil-alt text-dark me-1" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-video fa-4x text-secondary mb-3"></i>
                            <h5 class="text-secondary">No tienes publicaciones aún</h5>
                            <p class="text-muted mb-4">Crea tu primera publicación y comienza a generar ventas</p>
                            <a href="{{ route('staff.post.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Crear Primera Publicación
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <script>
        // Gráfica de rendimiento
        var ctx = document.getElementById("chart-performance").getContext("2d");

        var gradientStroke1 = ctx.createLinearGradient(0, 230, 0, 50);
        gradientStroke1.addColorStop(1, 'rgba(33, 150, 243, 0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(33, 150, 243, 0.0)');
        gradientStroke1.addColorStop(0, 'rgba(33, 150, 243, 0)');

        var gradientStroke2 = ctx.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColor Stop(1, 'rgba(251, 140, 0, 0.2)');
        gradientStroke2.addColorStop(0.2, 'rgba(251, 140, 0, 0.0)');
        gradientStroke2.addColorStop(0, 'rgba(251, 140, 0, 0)');

        var popularPosts = @json($popular_posts->take(5));

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: popularPosts.map(p => p.product.name.substring(0, 15) + '...'),
                datasets: [{
                    label: "Vistas",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "#2196f3",
                    data: popularPosts.map(p => p.views),
                    maxBarThickness: 30
                }, {
                    label: "Ventas",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "#fb8c00",
                    data: popularPosts.map(p => p.sales),
                    maxBarThickness: 30
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#b2b9bf',
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: true,
                            color: '#b2b9bf',
                            padding: 20,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });
    </script>
@endsection
