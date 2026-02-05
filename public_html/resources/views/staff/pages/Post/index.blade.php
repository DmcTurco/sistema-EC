@extends('staff.layout.base')

@section('title', 'Mis Publicaciones')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-sm-8 col-12">
                            <h3 class="mb-0">Mis Publicaciones</h3>
                            <p class="text-sm mb-0">Gestiona tus videos y publicaciones</p>
                        </div>
                        <div class="col-sm-4 col-12 text-sm-end text-start mt-sm-0 mt-2">
                            <a href="{{ route('staff.post.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Nueva Publicación
                            </a>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert" id="successAlert">
                        <span class="alert-icon"><i class="fas fa-check"></i></span>
                        <span class="alert-text">{{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Filtros -->
                <form method="GET" action="">
                    <div class="card-header border-0 pb-0">
                        <div class="row g-2">
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Buscar por producto..."
                                        name="search" value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-6">
                                <select class="form-select form-select-sm" name="status">
                                    <option value="">Todos los estados</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Públicos
                                    </option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Privados
                                    </option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                        <i class="fas fa-filter"></i> Filtrar
                                    </button>
                                    @if (request()->anyFilled(['search', 'status']))
                                        <a href="{{ route('staff.posts') }}" class="btn btn-secondary btn-sm flex-fill">
                                            <i class="fas fa-times"></i> Limpiar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="card-body">
                    @if ($posts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;"></th>
                                        <th>Producto</th>
                                        <th style="width: 150px;">Fecha</th>
                                        <th style="width: 100px;" class="text-center">Vistas</th>
                                        <th style="width: 100px;" class="text-center">Ventas</th>
                                        <th style="width: 100px;" class="text-center">Estado</th>
                                        <th style="width: 120px;" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($posts as $post)
                                        <tr>
                                            <!-- Imagen circular -->
                                            <td>
                                                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->product->name }}"
                                                    class="rounded-circle"
                                                    style="width: 45px; height: 45px; object-fit: cover;">
                                            </td>

                                            <!-- Nombre del producto -->
                                            <td>
                                                <span class="font-weight-bold">{{ $post->product->name }}</span>
                                            </td>

                                            <!-- Fecha -->
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $post->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>

                                            <!-- Vistas -->
                                            <td class="text-center">
                                                <span class="badge badge-sm bg-gradient-info">
                                                    <i class="fas fa-eye"></i> {{ number_format($post->views) }}
                                                </span>
                                            </td>

                                            <!-- Ventas -->
                                            <td class="text-center">
                                                <span class="badge badge-sm bg-gradient-success">
                                                    <i class="fas fa-shopping-cart"></i> {{ number_format($post->sales) }}
                                                </span>
                                            </td>

                                            <!-- Estado -->
                                            <td class="text-center">
                                                {!! $post->status_badge !!}
                                            </td>

                                            <!-- Acciones -->
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('staff.post.edit', $post) }}"
                                                        class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                                        title="Editar">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>

                                                    <form action="{{ route('staff.post.destroy', $post) }}" method="POST"
                                                        class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm btn-delete"
                                                            data-bs-toggle="tooltip" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($posts->hasPages())
                            <div class="mt-4">
                                {{ $posts->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty state -->
                        <div class="text-center py-5">
                            <i class="fas fa-video fa-3x text-secondary mb-3"></i>
                            <h5 class="text-secondary">No hay publicaciones</h5>
                            <p class="text-sm text-muted">
                                @if (request()->anyFilled(['search', 'status']))
                                    No se encontraron publicaciones con los filtros aplicados
                                @else
                                    Comienza creando tu primera publicación
                                @endif
                            </p>
                            <a href="{{ route('staff.post.create') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> Crear Publicación
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Auto-cerrar alertas
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(successAlert);
                bsAlert.close();
            }, 5000);
        }

        // Confirmación de eliminación
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "La publicación será eliminada",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#5e72e4',
                        cancelButtonColor: '#f5365c',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('¿Eliminar esta publicación?')) {
                        form.submit();
                    }
                }
            });
        });

        // Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
@endpush
