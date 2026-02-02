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
                    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert"
                        id="successAlert">
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
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Públicos</option>
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
                                        <a href="{{ route('staff.posts') }}"
                                            class="btn btn-secondary btn-sm flex-fill">
                                            <i class="fas fa-times"></i> Limpiar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card-body">
                    @forelse($posts as $post)
                        <div class="row border-bottom pb-3 mb-3">
                            <div class="col-md-2 col-4">
                                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->product->name }}"
                                    class="img-fluid rounded" style="width: 100%; height: 100px; object-fit: cover;">
                            </div>
                            <div class="col-md-7 col-8">
                                <h6 class="mb-1">{{ $post->product->name }}</h6>
                                <p class="text-sm text-muted mb-1">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $post->created_at->format('d/m/Y H:i') }}
                                </p>
                                <div class="d-flex gap-3 text-xs">
                                    <span><i class="fas fa-eye me-1"></i>{{ number_format($post->views) }} vistas</span>
                                    <span><i class="fas fa-shopping-cart me-1"></i>{{ number_format($post->sales) }}
                                        ventas</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 text-md-end mt-md-0 mt-2">
                                <div class="mb-2">
                                    {!! $post->status_badge !!}
                                </div>
                                <div class="d-flex gap-2 justify-content-md-end">
                                    <a href="{{ route('staff.post.edit', $post) }}"
                                        class="btn btn-warning btn-sm px-2" data-bs-toggle="tooltip" title="Editar">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    <form action="{{ route('staff.post.destroy', $post) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm px-2 btn-delete"
                                            data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
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
                    @endforelse

                    <!-- Pagination -->
                    @if ($posts->hasPages())
                        <div class="mt-4">
                            {{ $posts->links() }}
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