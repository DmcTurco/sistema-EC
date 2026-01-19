@extends('admin.layout.base')

@section('title', 'Dashboard - Panel Administrativo')

@section('page-title', 'Dashboard')

@section('content')
<!-- Tarjetas de estadísticas principales -->
<div class="row">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Ventas de Hoy</p>
                            <h5 class="font-weight-bolder mb-0">
                                S/ {{ number_format($todaySales, 2) }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-sm">{{ $stats['pending_orders'] }} órdenes pendientes</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
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
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Ventas del Mes</p>
                            <h5 class="font-weight-bolder mb-0">
                                S/ {{ number_format($monthSales, 2) }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-sm">{{ $stats['completed_orders'] }} completadas</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                            <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
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
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Usuarios</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ number_format($stats['total_users']) }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-success text-sm font-weight-bolder">+{{ $newUsersToday }}</span>
                                nuevos hoy
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                            <i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>
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
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Órdenes</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ number_format($stats['total_orders']) }}
                            </h5>
                            <p class="mb-0">
                                @if($stats['low_stock_products'] > 0)
                                <span class="text-danger text-sm font-weight-bolder">{{ $stats['low_stock_products']
                                    }}</span>
                                productos bajo stock
                                @else
                                <span class="text-success text-sm">Stock OK</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                            <i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfica de ventas y Estados de órdenes -->
<div class="row mt-4">
    <div class="col-lg-8 mb-lg-0 mb-4">
        <div class="card z-index-2 h-100">
            <div class="card-header pb-0 pt-3 bg-transparent">
                <h6 class="text-capitalize">Ventas de los últimos 6 meses</h6>
                <p class="text-sm mb-0">
                    <i class="fa fa-check text-success"></i>
                    <span class="font-weight-bold">{{ number_format($stats['total_orders']) }} órdenes</span> procesadas
                </p>
            </div>
            <div class="card-body p-3">
                <div class="chart">
                    <canvas id="chart-sales" class="chart-canvas" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Estados de Órdenes</h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-group">
                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm me-3 bg-gradient-warning shadow text-center">
                                <i class="ni ni-time-alarm text-white opacity-10"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark text-sm">Pendientes</h6>
                                <span class="text-xs">{{ $ordersByStatus['pending'] }} órdenes</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 text-sm">{{ $ordersByStatus['pending'] }}</h6>
                        </div>
                    </li>

                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm me-3 bg-gradient-info shadow text-center">
                                <i class="ni ni-credit-card text-white opacity-10"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark text-sm">Pagadas</h6>
                                <span class="text-xs">{{ $ordersByStatus['paid'] }} órdenes</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 text-sm">{{ $ordersByStatus['paid'] }}</h6>
                        </div>
                    </li>

                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                <i class="ni ni-delivery-fast text-white opacity-10"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark text-sm">Enviadas</h6>
                                <span class="text-xs">{{ $ordersByStatus['shipped'] }} órdenes</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 text-sm">{{ $ordersByStatus['shipped'] }}</h6>
                        </div>
                    </li>

                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm me-3 bg-gradient-success shadow text-center">
                                <i class="ni ni-check-bold text-white opacity-10"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark text-sm">Completadas</h6>
                                <span class="text-xs">{{ $ordersByStatus['completed'] }} órdenes</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 text-sm">{{ $ordersByStatus['completed'] }}</h6>
                        </div>
                    </li>

                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm me-3 bg-gradient-danger shadow text-center">
                                <i class="ni ni-fat-remove text-white opacity-10"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark text-sm">Canceladas</h6>
                                <span class="text-xs">{{ $ordersByStatus['cancelled'] }} órdenes</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 text-sm">{{ $ordersByStatus['cancelled'] }}</h6>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Posts más vistos y Productos bajo stock -->
<div class="row mt-4">
    <div class="col-lg-7 mb-lg-0 mb-4">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <div class="d-flex justify-content-between">
                    <h6 class="mb-2">Posts Más Vistos</h6>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Producto
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Staff
                            </th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Vistas</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Ventas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topPosts as $post)
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div>
                                        <img src="{{ $post->thumbnail_url }}" class="avatar avatar-sm me-3"
                                            alt="thumbnail">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{ $post->product->name }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $post->staff->name }}</p>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold">{{ number_format($post->views)
                                    }}</span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold">{{ $post->sales }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-sm py-3">No hay posts registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Productos con Bajo Stock</h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-group">
                    @forelse($lowStockProducts as $product)
                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                        <div class="d-flex align-items-center">
                            <div
                                class="icon icon-shape icon-sm me-3 bg-gradient-{{ $product->stock < 5 ? 'danger' : 'warning' }} shadow text-center">
                                <i class="ni ni-box-2 text-white opacity-10"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark text-sm">{{ $product->name }}</h6>
                                <span class="text-xs">Quedan <span
                                        class="font-weight-bold text-{{ $product->stock < 5 ? 'danger' : 'warning' }}">{{
                                        $product->stock }}</span> unidades</span>
                            </div>
                        </div>
                        <div class="d-flex">
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto">
                                <i class="ni ni-bold-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item border-0 text-center text-sm py-3">
                        <i class="ni ni-check-bold text-success"></i> Todos los productos tienen stock suficiente
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Últimas órdenes -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Últimas Órdenes</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Orden #
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Cliente</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Items</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Total</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Estado</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $order->order_number }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $order->customer_email }}</p>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $order->getTotalItems()
                                        }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $order->formatted_total
                                        }}</span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    {!! $order->status_badge !!}
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{
                                        $order->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-sm py-3">No hay órdenes registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
<script>
    // Gráfica de ventas
    var ctx = document.getElementById("chart-sales").getContext("2d");
    
    var gradientStroke = ctx.createLinearGradient(0, 230, 0, 50);
    gradientStroke.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
    gradientStroke.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
    gradientStroke.addColorStop(0, 'rgba(94, 114, 228, 0)');
    
    var salesData = @json($salesByMonth);
    
    new Chart(ctx, {
        type: "line",
        data: {
            labels: salesData.map(item => item.month),
            datasets: [{
                label: "Ventas (S/)",
                tension: 0.4,
                borderWidth: 0,
                pointRadius: 2,
                pointBackgroundColor: "#5e72e4",
                borderColor: "#5e72e4",
                backgroundColor: gradientStroke,
                borderWidth: 3,
                fill: true,
                data: salesData.map(item => item.sales),
                maxBarThickness: 6
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
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
                        borderDash: [5, 5]
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