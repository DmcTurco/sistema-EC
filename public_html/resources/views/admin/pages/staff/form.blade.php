@extends('admin.layout.base')

@section('title', isset($staff) ? 'Editar Personal' : 'Crear Personal')

@section('page-title', isset($staff) ? 'Editar Personal' : 'Crear Personal')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-sm-8 col-12">
                            <h3 class="mb-0">
                                {{ isset($staff) ? 'Editar Personal' : 'Nuevo Personal' }}
                            </h3>
                            <p class="text-sm mb-0">
                                {{ isset($staff) ? 'Modifica la información del personal' : 'Completa la información del personal' }}
                            </p>
                        </div>
                        <div class="col-sm-4 col-12 text-sm-end text-start mt-sm-0 mt-2">
                            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ isset($staff) ? route('admin.staff.update', $staff) : route('admin.staff.store') }}"
                        method="POST" id="staffForm">
                        @csrf
                        @if (isset($staff))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <!-- Columna Izquierda - Información Personal -->
                            <div class="col-lg-8 col-12">
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            Información Personal
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Nombre -->
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="name" class="form-label">
                                                    Nombre Completo <span class="text-danger">*</span>
                                                </label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                                    name="name" value="{{ old('name', $staff->name ?? '') }}"
                                                    placeholder="Ej: Juan Pérez López" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="email" class="form-label">
                                                    Email <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-envelope"></i>
                                                    </span>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        id="email" name="email"
                                                        value="{{ old('email', $staff->email ?? '') }}"
                                                        placeholder="ejemplo@email.com" required>
                                                </div>
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Este email se usará para iniciar sesión
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Contraseña -->
                                        <div class="row">
                                            <div class="col-md-6 col-12 mb-3">
                                                <label for="password" class="form-label">
                                                    Contraseña
                                                    @if (!isset($staff))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <div class="position-relative">
                                                    <input type="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        id="password" name="password" placeholder="••••••••"
                                                        style="padding-right: 40px;" {{ !isset($staff) ? 'required' : '' }}>
                                                    <i class="fas fa-eye position-absolute" id="togglePassword"
                                                        style="right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #8392ab;"></i>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                @if (isset($staff))
                                                    <small class="text-muted">
                                                        Dejar en blanco para mantener la contraseña actual
                                                    </small>
                                                @else
                                                    <small class="text-muted">
                                                        Mínimo 6 caracteres
                                                    </small>
                                                @endif
                                            </div>

                                            <div class="col-md-6 col-12 mb-3">
                                                <label for="password_confirmation" class="form-label">
                                                    Confirmar Contraseña
                                                    @if (!isset($staff))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control" id="password_confirmation"
                                                        name="password_confirmation" placeholder="••••••••"
                                                        style="padding-right: 40px;"
                                                        {{ !isset($staff) ? 'required' : '' }}>
                                                    <i class="fas fa-eye position-absolute" id="togglePasswordConfirm"
                                                        style="right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #8392ab;"></i>
                                                </div>
                                            </div>
                                        </div>

                                        @if (isset($staff))
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
                                                                    {{ $staff->created_at->format('d/m/Y H:i') }}
                                                                </small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <small class="d-block mb-1">
                                                                    <i class="fas fa-calendar-check text-success me-1"></i>
                                                                    <strong>Última Actualización:</strong>
                                                                </small>
                                                                <small class="text-muted">
                                                                    {{ $staff->updated_at->format('d/m/Y H:i') }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Asignación de Tienda -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            <i class="fas fa-store me-1"></i> Asignación de Tienda
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="store_id" class="form-label">
                                                    Tienda Asignada
                                                </label>
                                                <select class="form-select @error('store_id') is-invalid @enderror"
                                                    id="store_id" name="store_id">
                                                    <option value="">Sin asignar a tienda</option>
                                                    @foreach ($stores as $store)
                                                        <option value="{{ $store->id }}"
                                                            {{ old('store_id', $staff->store_id ?? '') == $store->id ? 'selected' : '' }}>
                                                            {{ $store->name }}
                                                            @if ($store->address)
                                                                - {{ $store->address }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('store_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    El personal podrá crear publicaciones solo para la tienda asignada
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna Derecha - Avatar y Estado -->
                            <div class="col-lg-4 col-12">
                                <!-- Avatar -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            <i class="fas fa-user me-1"></i> Avatar
                                        </h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <div class="position-relative d-inline-block">
                                                <div
                                                    class="bg-gradient-info rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto">
                                                    <i class="fas fa-user fa-5x text-white p-4"></i>
                                                </div>
                                            </div>
                                        </div>

                                        @if (isset($staff))
                                            <div class="mt-3 p-3 bg-light rounded">
                                                <h6 class="mb-2">ID de Personal</h6>
                                                <h4 class="mb-0 text-primary font-weight-bold">
                                                    #{{ str_pad($staff->id, 4, '0', STR_PAD_LEFT) }}
                                                </h4>
                                            </div>
                                        @else
                                            <small class="text-muted d-block">
                                                El ID se asignará automáticamente al crear
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Estado -->
                                <div class="card">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            <i class="fas fa-toggle-on me-1"></i> Estado
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch ps-0">
                                            <input class="form-check-input ms-0" type="checkbox" id="status"
                                                name="status" value="1"
                                                {{ old('status', $staff->status ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-3" for="status">
                                                <span class="d-block font-weight-bold">Personal Activo</span>
                                                <small class="text-muted">
                                                    El personal inactivo no podrá iniciar sesión
                                                </small>
                                            </label>
                                        </div>

                                        <div class="mt-3 p-2 rounded"
                                            style="background-color: {{ old('status', $staff->status ?? true) ? '#d4edda' : '#f8d7da' }}">
                                            <small class="d-block text-center"
                                                style="color: {{ old('status', $staff->status ?? true) ? '#155724' : '#721c24' }}">
                                                <i
                                                    class="fas {{ old('status', $staff->status ?? true) ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                                <span id="statusText">
                                                    {{ old('status', $staff->status ?? true) ? 'Acceso Habilitado' : 'Acceso Bloqueado' }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex flex-sm-row flex-column justify-content-end gap-2">
                                    <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        {{ isset($staff) ? 'Actualizar Personal' : 'Guardar Personal' }}
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
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');

            if (password.type === 'password') {
                password.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });

        // Toggle password confirmation visibility
        document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
            const passwordConfirm = document.getElementById('password_confirmation');

            if (passwordConfirm.type === 'password') {
                passwordConfirm.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                passwordConfirm.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });

        // Cambiar el texto del estado al hacer toggle
        const statusCheckbox = document.getElementById('status');
        const statusText = document.getElementById('statusText');

        if (statusCheckbox && statusText) {
            statusCheckbox.addEventListener('change', function() {
                const parent = statusText.parentElement;

                if (this.checked) {
                    statusText.textContent = 'Acceso Habilitado';
                    parent.style.backgroundColor = '#d4edda';
                    parent.style.color = '#155724';
                } else {
                    statusText.textContent = 'Acceso Bloqueado';
                    parent.style.backgroundColor = '#f8d7da';
                    parent.style.color = '#721c24';
                }
            });
        }

        // Validación antes de enviar
        document.getElementById('staffForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;
            const isEdit = {{ isset($staff) ? 'true' : 'false' }};

            if (!name) {
                e.preventDefault();
                alert('El nombre es obligatorio');
                document.getElementById('name').focus();
                return false;
            }

            if (!email) {
                e.preventDefault();
                alert('El email es obligatorio');
                document.getElementById('email').focus();
                return false;
            }

            // Validar password solo si es creación o si se ingresó una nueva
            if (!isEdit || password) {
                if (password.length < 6) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 6 caracteres');
                    document.getElementById('password').focus();
                    return false;
                }

                if (password !== passwordConfirm) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    document.getElementById('password_confirmation').focus();
                    return false;
                }
            }
        });

        // Auto-focus
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('name').focus();
        });
    </script>
@endpush

@push('styles')
    <style>
        .form-check-input {
            transition: all 0.3s ease;
        }

        .form-switch .form-check-input {
            width: 3rem;
            height: 1.5rem;
        }

        .fa-user {
            transition: transform 0.3s ease;
        }

        .fa-user:hover {
            transform: scale(1.1);
        }
    </style>
@endpush
