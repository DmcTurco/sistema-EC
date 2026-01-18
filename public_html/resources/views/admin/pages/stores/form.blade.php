@extends('admin.layout.base')

@section('title', isset($store) ? 'Editar Tienda' : 'Crear Tienda')

@section('page-title', isset($store) ? 'Editar Tienda' : 'Crear Tienda')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-sm-8 col-12">
                            <h3 class="mb-0">
                                {{ isset($store) ? 'Editar Tienda' : 'Nueva Tienda' }}
                            </h3>
                            <p class="text-sm mb-0">
                                {{ isset($store) ? 'Modifica la información de la tienda' : 'Completa la información de la tienda' }}
                            </p>
                        </div>
                        <div class="col-sm-4 col-12 text-sm-end text-start mt-sm-0 mt-2">
                            <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ isset($store) ? route('admin.stores.update', $store) : route('admin.stores.store') }}"
                        method="POST" id="storeForm">
                        @csrf
                        @if (isset($store))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <!-- Columna Izquierda - Información Básica -->
                            <div class="col-lg-8 col-12">
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            Información de la Tienda
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Nombre de la Tienda -->
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="name" class="form-label">
                                                    Nombre de la Tienda <span class="text-danger">*</span>
                                                </label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                                    name="name" value="{{ old('name', $store->name ?? '') }}"
                                                    placeholder="Ej: Supermercado Central" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Ingresa el nombre completo y oficial de la tienda
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Dirección -->
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="address" class="form-label">
                                                    Dirección Completa
                                                </label>
                                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="4"
                                                    placeholder="Av. Principal 123, Distrito, Ciudad, Región">{{ old('address', $store->address ?? '') }}</textarea>
                                                @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    Incluye calle, número, distrito, ciudad y región
                                                </small>
                                            </div>
                                        </div>

                                        @if (isset($store))
                                            <!-- Información del Registro -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="alert alert-light border" role="alert">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <small class="d-block mb-1">
                                                                    <i class="fas fa-calendar-plus text-primary me-1"></i>
                                                                    <strong>Fecha de Creación:</strong>
                                                                </small>
                                                                <small class="text-muted">
                                                                    {{ $store->created_at->format('d/m/Y H:i') }}
                                                                </small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <small class="d-block mb-1">
                                                                    <i class="fas fa-calendar-check text-success me-1"></i>
                                                                    <strong>Última Actualización:</strong>
                                                                </small>
                                                                <small class="text-muted">
                                                                    {{ $store->updated_at->format('d/m/Y H:i') }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Información Adicional (Opcional) -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            <i class="fas fa-info-circle me-1"></i> Información Adicional
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info border-0" role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-lightbulb fa-2x me-3"></i>
                                                <div>
                                                    <h6 class="mb-1">Configuración de la Tienda</h6>
                                                    <p class="text-sm mb-0">
                                                        Esta tienda estará disponible para que el personal del
                                                        supermercado pueda asociarse y crear publicaciones de productos.
                                                        Asegúrate de mantener la información actualizada.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna Derecha - Estado y Acciones -->
                            <div class="col-lg-4 col-12">
                                <!-- Icono de la Tienda -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            <i class="fas fa-store me-1"></i> Identificación
                                        </h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <div class="position-relative d-inline-block">
                                                <div
                                                    class="bg-gradient-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto">
                                                    <i class="fas fa-store fa-5x text-white p-4"></i>
                                                </div>
                                            </div>
                                        </div>

                                        @if (isset($store))
                                            <div class="mt-3 p-3 bg-light rounded">
                                                <h6 class="mb-2">ID de Tienda</h6>
                                                <h4 class="mb-0 text-primary font-weight-bold">
                                                    #{{ str_pad($store->id, 4, '0', STR_PAD_LEFT) }}
                                                </h4>
                                            </div>
                                        @else
                                            <small class="text-muted d-block">
                                                El ID se asignará automáticamente al crear la tienda
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Estado -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            <i class="fas fa-toggle-on me-1"></i> Estado
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch ps-0">
                                            <input class="form-check-input ms-0" type="checkbox" id="status"
                                                name="status" value="1"
                                                {{ old('status', $store->status ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-3" for="status">
                                                <span class="d-block font-weight-bold">Tienda Activa</span>
                                                <small class="text-muted">
                                                    Las tiendas inactivas no estarán disponibles para el personal
                                                </small>
                                            </label>
                                        </div>

                                        <div class="mt-3 p-2 rounded"
                                            style="background-color: {{ old('status', $store->status ?? true) ? '#d4edda' : '#f8d7da' }}">
                                            <small class="d-block text-center"
                                                style="color: {{ old('status', $store->status ?? true) ? '#155724' : '#721c24' }}">
                                                <i
                                                    class="fas {{ old('status', $store->status ?? true) ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                                <span id="statusText">
                                                    {{ old('status', $store->status ?? true) ? 'Tienda Visible' : 'Tienda Oculta' }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                @if (isset($store))
                                    <!-- Estadísticas Rápidas -->
                                    <div class="card">
                                        <div class="card-header pb-0">
                                            <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                                <i class="fas fa-chart-bar me-1"></i> Estadísticas
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-6 mb-3">
                                                    <div class="p-2 bg-light rounded">
                                                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                                        <h6 class="mb-0 text-sm">0</h6>
                                                        <small class="text-muted">Personal</small>
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <div class="p-2 bg-light rounded">
                                                        <i class="fas fa-video fa-2x text-success mb-2"></i>
                                                        <h6 class="mb-0 text-sm">0</h6>
                                                        <small class="text-muted">Videos</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="text-muted text-center d-block">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Información de uso de la tienda
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex flex-sm-row flex-column justify-content-end gap-2">
                                    <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        {{ isset($store) ? 'Actualizar Tienda' : 'Guardar Tienda' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Cambiar el texto del estado al hacer toggle
        const statusCheckbox = document.getElementById('status');
        const statusText = document.getElementById('statusText');

        if (statusCheckbox && statusText) {
            statusCheckbox.addEventListener('change', function() {
                const parent = statusText.parentElement;

                if (this.checked) {
                    statusText.textContent = 'Tienda Visible';
                    parent.style.backgroundColor = '#d4edda';
                    parent.style.color = '#155724';
                } else {
                    statusText.textContent = 'Tienda Oculta';
                    parent.style.backgroundColor = '#f8d7da';
                    parent.style.color = '#721c24';
                }
            });
        }

        // Validación antes de enviar
        document.getElementById('storeForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();

            if (!name) {
                e.preventDefault();
                alert('El nombre de la tienda es obligatorio');
                document.getElementById('name').focus();
                return false;
            }

            if (name.length < 3) {
                e.preventDefault();
                alert('El nombre debe tener al menos 3 caracteres');
                document.getElementById('name').focus();
                return false;
            }
        });

        // Auto-focus en el primer campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('name').focus();
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Animación suave para el toggle del estado */
        .form-check-input {
            transition: all 0.3s ease;
        }

        /* Mejorar apariencia del switch */
        .form-switch .form-check-input {
            width: 3rem;
            height: 1.5rem;
        }

        /* Efecto hover en el icono de tienda */
        .fa-store {
            transition: transform 0.3s ease;
        }

        .fa-store:hover {
            transform: scale(1.1);
        }

        /* Estilo para las estadísticas */
        .bg-light {
            transition: all 0.3s ease;
        }

        .bg-light:hover {
            background-color: #e9ecef !important;
            transform: translateY(-2px);
        }
    </style>
@endpush
