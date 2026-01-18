@extends('admin.layout.base')

@section('title', 'Tiendas - Mi Sistema')

@section('page-title', 'Tiendas')

@section('content')

    <div class="row h-100">
        <div class="col-12">
            <div class="card h-100 d-flex flex-column">
                <!-- Card header -->
                <div class="card-header flex-shrink-0">
                    <div class="row align-items-center">
                        <div class="col-sm-8 col-12 mb-sm-0 mb-2">
                            <h3 class="mb-0">Gestión de Tiendas</h3>
                            <p class="text-sm mb-0">
                                Administra las tiendas del sistema
                            </p>
                        </div>
                        <div class="col-sm-4 col-12 text-sm-end text-start">
                            <a href="{{ route('admin.stores.create') }}" class="btn btn-primary btn-sm w-sm-auto w-100">
                                <i class="fas fa-plus"></i> Nueva Tienda
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

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 flex-shrink-0" role="alert"
                        id="errorAlert">
                        <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <span class="alert-text">{{ session('error') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Search and filters -->
                <form method="GET" action="{{ route('admin.stores.index') }}" class="flex-shrink-0">
                    <div class="card-header border-0 pb-0">
                        <div class="row g-2">
                            <div class="col-lg-7 col-md-6 col-12">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Buscar tienda..."
                                        name="search" value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-6">
                                <select class="form-select form-select-sm" name="status">
                                    <option value="">Todos los estados</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activos</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactivos
                                    </option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                        <i class="fas fa-filter"></i> Filtrar
                                    </button>
                                    @if (request()->anyFilled(['search', 'status']))
                                        <a href="{{ route('admin.stores.index') }}"
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
                                        Nombre
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                                        style="min-width: 250px;">
                                        Dirección
                                    </th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Estado
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Fecha Registro
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="min-width: 120px;">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stores as $store)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0 px-3">{{ $store->id }}</p>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="avatar avatar-sm bg-gradient-primary me-3 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-store text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $store->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $store->address ?? '-' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if ($store->status)
                                                <span class="badge badge-sm bg-gradient-success">
                                                    <i class="fas fa-check"></i> Activo
                                                </span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">
                                                    <i class="fas fa-times"></i> Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $store->created_at->format('d/m/Y') }}
                                            </p>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $store->created_at->format('H:i') }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('admin.stores.show', $store) }}"
                                                    class="btn btn-link text-info px-2 mb-0" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('admin.stores.edit', $store) }}"
                                                    class="btn btn-link text-warning px-2 mb-0" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Editar tienda">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>

                                                <form action="{{ route('admin.stores.destroy', $store) }}" method="POST"
                                                    class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-link text-danger px-2 mb-0 btn-delete"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Eliminar tienda">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-center">
                                                <i class="fas fa-store-slash fa-3x text-secondary mb-3"></i>
                                                <h5 class="text-secondary">No hay tiendas registradas</h5>
                                                <p class="text-sm text-muted">
                                                    @if (request()->anyFilled(['search', 'status']))
                                                        No se encontraron tiendas con los filtros aplicados
                                                    @else
                                                        Comienza agregando tu primera tienda
                                                    @endif
                                                </p>
                                                <a href="{{ route('admin.stores.create') }}"
                                                    class="btn btn-primary btn-sm mt-2">
                                                    <i class="fas fa-plus"></i> Crear Tienda
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
                @if ($stores->hasPages())
                    <div class="card-footer py-3 flex-shrink-0">
                        <div class="row align-items-center">
                            <div class="col-sm-6 col-12 mb-sm-0 mb-2">
                                <p class="text-sm text-muted mb-0">
                                    Mostrando {{ $stores->firstItem() }} a {{ $stores->lastItem() }}
                                    de {{ $stores->total() }} tiendas
                                </p>
                            </div>
                            <div class="col-sm-6 col-12">
                                <nav aria-label="Page navigation">
                                    {{ $stores->links() }}
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
            /* Ajusta según tu navbar height */
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
            // Auto-cerrar alertas después de 5 segundos
            const successAlert = document.getElementById('successAlert');
            if (successAlert) {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(successAlert);
                    bsAlert.close();
                }, 5000);
            }

            const errorAlert = document.getElementById('errorAlert');
            if (errorAlert) {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(errorAlert);
                    bsAlert.close();
                }, 7000); // 7 segundos para errores
            }
        });

        // Confirmación de eliminación con SweetAlert2 (si está disponible)
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                // Si tienes SweetAlert2
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "La tienda será desactivada",
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
                    // Confirmación nativa
                    if (confirm('¿Desactivar esta tienda?')) {
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