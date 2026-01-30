@extends('admin.layout.base')

@section('title', 'Productos - Mi Sistema')

@section('page-title', 'Productos')

@section('content')

<div class="row h-100">
    <div class="col-12">
        <div class="card h-100 d-flex flex-column">
            <!-- Card header -->
            <div class="card-header flex-shrink-0">
                <div class="row align-items-center">
                    <div class="col-sm-8 col-12 mb-sm-0 mb-2">
                        <h3 class="mb-0">Gestión de Productos</h3>
                        <p class="text-sm mb-0">
                            Administra el catálogo de productos del sistema
                        </p>
                    </div>
                    <div class="col-sm-4 col-12 text-sm-end text-start">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm w-sm-auto w-100">
                            <i class="fas fa-plus"></i> Nuevo Producto
                        </a>
                    </div>
                </div>
            </div>

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 flex-shrink-0" role="alert"
                id="successAlert">
                <span class="alert-icon"><i class="fas fa-check"></i></span>
                <span class="alert-text">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <!-- Search and filters -->
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex-shrink-0">
                <div class="card-header border-0 pb-0">
                    <div class="row g-2">
                        <div class="col-lg-5 col-md-6 col-12">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Buscar producto..." name="search"
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 col-6">
                            <select class="form-select form-select-sm" name="status">
                                <option value="">Todos</option>
                                <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Inactivos
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3 col-6">
                            <select class="form-select form-select-sm" name="stock_filter">
                                <option value="">Stock</option>
                                <option value="low" {{ request('stock_filter')=='low' ? 'selected' : '' }}>Bajo
                                </option>
                                <option value="out" {{ request('stock_filter')=='out' ? 'selected' : '' }}>Agotado
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                                @if (request()->anyFilled(['search', 'status', 'stock_filter']))
                                <a href="{{ route('admin.products.index') }}"
                                    class="btn btn-secondary btn-sm flex-fill">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Table wrapper con scroll -->
            <div class="card-body p-0 flex-grow-1 overflow-auto">
                <div class="table-responsive" style="min-height: 400px;">
                    <table class="table align-items-center table-flush mb-0">
                        <thead class="thead-light sticky-top">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    ID
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    style="min-width: 200px;">
                                    Producto
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Precio
                                </th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Stock
                                </th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Estado
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    style="min-width: 120px;">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                            <tr>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $product->id }}</p>
                                </td>
                                <td>
                                    <div class="d-flex px-2 py-1 align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            {{-- ✅ MOSTRAR THUMBNAIL SI TIENE VIDEO --}}
                                            @if ($product->has_video)
                                            <div class="position-relative video-thumbnail-wrapper"
                                                style="cursor: pointer; width: 40px; height: 40px;"
                                                data-bs-toggle="modal" data-bs-target="#videoModal{{ $product->id }}">
                                                <img src="{{ $product->video_thumbnail_url }}" class="rounded-circle"
                                                    alt="{{ $product->name }}"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                                {{-- Icono de Play centrado --}}
                                                <div class="play-icon-overlay">
                                                    <i class="fas fa-play text-white"></i>
                                                </div>
                                                {{-- Badge "Video" en esquina --}}
                                                {{-- <span class="video-badge">
                                                    <i class="fas fa-video"></i>
                                                </span> --}}
                                            </div>
                                            @elseif ($product->image ?? false)
                                            {{-- Imagen normal --}}
                                            <img src="{{ asset('storage/' . $product->image) }}" class="rounded-circle"
                                                alt="{{ $product->name }}"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                            {{-- Sin imagen/video --}}
                                            <div class="bg-gradient-secondary rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="fas fa-box text-white"></i>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $product->name }}</h6>
                                            @if ($product->category ?? false)
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $product->category->name }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">
                                        ${{ number_format($product->price, 2) }}
                                    </p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    @if ($product->stock <= 0) <span class="badge badge-sm bg-gradient-danger">Sin
                                        stock</span>
                                        @elseif($product->stock < 10) <span class="badge badge-sm bg-gradient-warning">
                                            {{ $product->stock }}</span>
                                            @else
                                            <span class="badge badge-sm bg-gradient-success">{{ $product->stock
                                                }}</span>
                                            @endif
                                </td>
                                <td class="align-middle text-center">
                                    @if ($product->status)
                                    <span class="badge badge-sm bg-gradient-success">
                                        <i class="fas fa-check"></i> Activo
                                    </span>
                                    @else
                                    <span class="badge badge-sm bg-gradient-secondary">
                                        <i class="fas fa-times"></i> Inactivo
                                    </span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- ✅ BOTÓN PARA VER VIDEO (si tiene) --}}
                                        {{-- @if ($product->has_video)
                                        <button type="button" class="btn btn-link text-info px-2 mb-0"
                                            data-bs-toggle="modal" data-bs-target="#videoModal{{ $product->id }}"
                                            title="Ver video">
                                            <i class="fas fa-play-circle"></i>
                                        </button>
                                        @endif --}}

                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="btn btn-link text-warning px-2 mb-0" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Editar producto">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>

                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-link text-danger px-2 mb-0 btn-delete"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Desactivar producto">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- ✅ MODAL DE VIDEO (si tiene video) --}}
                            @if ($product->has_video)
                            <div class="modal fade" id="videoModal{{ $product->id }}" tabindex="-1"
                                aria-labelledby="videoModalLabel{{ $product->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="videoModalLabel{{ $product->id }}">
                                                <i class="fas fa-video text-primary me-2"></i>
                                                {{ $product->name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <video controls class="w-100" poster="{{ $product->video_thumbnail_url }}"
                                                style="max-height: 70vh;">
                                                <source src="{{ $product->main_video_url }}" type="video/mp4">
                                                Tu navegador no soporta la reproducción de videos.
                                            </video>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                data-bs-dismiss="modal">
                                                Cerrar
                                            </button>
                                            <a href="{{ $product->main_video_url }}" class="btn btn-primary btn-sm"
                                                download>
                                                <i class="fas fa-download"></i> Descargar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-center">
                                        <i class="fas fa-box-open fa-3x text-secondary mb-3"></i>
                                        <h5 class="text-secondary">No hay productos registrados</h5>
                                        <p class="text-sm text-muted">
                                            @if (request()->anyFilled(['search', 'status', 'stock_filter']))
                                            No se encontraron productos con los filtros aplicados
                                            @else
                                            Comienza agregando tu primer producto
                                            @endif
                                        </p>
                                        <a href="{{ route('admin.products.create') }}"
                                            class="btn btn-primary btn-sm mt-2">
                                            <i class="fas fa-plus"></i> Crear Producto
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if ($products->hasPages())
            <div class="card-footer py-3 flex-shrink-0">
                <div class="row align-items-center">
                    <div class="col-sm-6 col-12 mb-sm-0 mb-2">
                        <p class="text-sm text-muted mb-0">
                            Mostrando {{ $products->firstItem() }} a {{ $products->lastItem() }}
                            de {{ $products->total() }} productos
                        </p>
                    </div>
                    <div class="col-sm-6 col-12">
                        <nav aria-label="Page navigation">
                            {{ $products->links() }}
                        </nav>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Asegurar que el contenedor principal use toda la altura */
    .content {
        height: calc(100vh - 120px);
        overflow: hidden;
    }

    /* Tabla con header sticky */
    .table-responsive {
        overflow-x: auto;
        overflow-y: auto;
    }

    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f6f9fc;
    }

    /* ✅ ESTILOS PARA VIDEO THUMBNAIL */
    .video-thumbnail-wrapper {
        position: relative;
        display: block;
        overflow: hidden;
    }

    .play-icon-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.7);
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        pointer-events: none;
        z-index: 2;
    }

    .play-icon-overlay i {
        font-size: 8px;
        margin-left: 1px;
    }

    .video-thumbnail-wrapper:hover .play-icon-overlay {
        background: rgba(0, 0, 0, 0.9);
        width: 24px;
        height: 24px;
    }

    .video-thumbnail-wrapper:hover .play-icon-overlay i {
        font-size: 10px;
    }

    .video-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        background: linear-gradient(310deg, #2152ff, #21d4fd);
        color: white;
        font-size: 7px;
        padding: 2px 3px;
        border-radius: 3px;
        z-index: 3;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        line-height: 1;
    }

    .video-badge i {
        font-size: 7px;
    }

    /* Mejorar scroll en móviles */
    @media (max-width: 768px) {
        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }

        .card-body {
            padding: 0.5rem !important;
        }
    }

    /* Scroll personalizado */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('successAlert');
            if (alert) {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            }

            // ✅ PAUSAR VIDEO CUANDO SE CIERRA EL MODAL
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function () {
                    const video = this.querySelector('video');
                    if (video) {
                        video.pause();
                        video.currentTime = 0;
                    }
                });
            });
        });

        // Confirmación de eliminación
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "El producto será desactivado",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#5e72e4',
                        cancelButtonColor: '#f5365c',
                        confirmButtonText: 'Sí, desactivar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('¿Desactivar este producto?')) {
                        form.submit();
                    }
                }
            });
        });

        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
</script>
@endpush