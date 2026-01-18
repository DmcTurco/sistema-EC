@extends('staff.layout.base')

@section('title', isset($post) ? 'Editar Publicación' : 'Nueva Publicación')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-sm-8 col-12">
                            <h3 class="mb-0">
                                {{ isset($post) ? 'Editar Publicación' : 'Nueva Publicación' }}
                            </h3>
                            <p class="text-sm mb-0">
                                {{ isset($post) ? 'Actualiza tu publicación' : 'Crea una nueva publicación con tu video de saludo' }}
                            </p>
                        </div>
                        <div class="col-sm-4 col-12 text-sm-end text-start mt-sm-0 mt-2">
                            <a href="{{ route('staff.post.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form
                        action="{{ isset($post) ? route('staff.post.update', $post) : route('staff.post.store') }}"
                        method="POST" enctype="multipart/form-data" id="postForm">
                        @csrf
                        @if (isset($post))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <!-- Columna Izquierda -->
                            <div class="col-lg-8 col-12">
                                <!-- Selección de Producto -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            Información del Producto
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="product_id" class="form-label">
                                                Producto <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('product_id') is-invalid @enderror"
                                                id="product_id" name="product_id" required>
                                                <option value="">Selecciona un producto</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ old('product_id', $post->product_id ?? '') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }} - S/ {{ $product->price }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('product_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Selecciona el producto que quieres promocionar
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Video de Saludo -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            Video de Saludo (5 segundos)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="intro_video" class="form-label">
                                                Subir Video
                                                @if (!isset($post))
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="file"
                                                class="form-control @error('intro_video') is-invalid @enderror"
                                                id="intro_video" name="intro_video" accept="video/*"
                                                {{ !isset($post) ? 'required' : '' }}>
                                            @error('intro_video')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Máximo 10MB. Formatos: MP4, MOV, AVI, WEBM
                                            </small>
                                        </div>

                                        @if (isset($post) && $post->intro_video_path)
                                            <div class="mb-3">
                                                <label class="form-label">Video Actual</label>
                                                <div class="ratio ratio-16x9">
                                                    <video controls>
                                                        <source src="{{ $post->intro_video_url }}" type="video/mp4">
                                                        Tu navegador no soporta video HTML5.
                                                    </video>
                                                </div>
                                                <small class="text-muted">Sube un nuevo video para reemplazar</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Miniatura (Opcional) -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                            Miniatura (Opcional)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="thumbnail" class="form-label">Subir Miniatura</label>
                                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                                                id="thumbnail" name="thumbnail" accept="image/*">
                                            @error('thumbnail')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">
                                                Imagen para vista previa. Máximo 2MB
                                            </small>
                                        </div>

                                        @if (isset($post) && $post->thumbnail_path)
                                            <div class="mb-3">
                                                <label class="form-label">Miniatura Actual</label>
                                                <div>
                                                    <img src="{{ $post->thumbnail_url }}" alt="Miniatura"
                                                        class="img-thumbnail" style="max-width: 200px;">
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="remove_thumbnail"
                                                        name="remove_thumbnail" value="1">
                                                    <label class="form-check-label" for="remove_thumbnail">
                                                        Eliminar miniatura
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Columna Derecha - Estado -->
                            <div class="col-lg-4 col-12">
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
                                                {{ old('status', $post->status ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-3" for="status">
                                                <span class="d-block font-weight-bold">Publicación Pública</span>
                                                <small class="text-muted">
                                                    Las publicaciones privadas no serán visibles para los clientes
                                                </small>
                                            </label>
                                        </div>

                                        <div class="mt-3 p-2 rounded"
                                            style="background-color: {{ old('status', $post->status ?? true) ? '#d4edda' : '#f8d7da' }}">
                                            <small class="d-block text-center"
                                                style="color: {{ old('status', $post->status ?? true) ? '#155724' : '#721c24' }}">
                                                <i
                                                    class="fas {{ old('status', $post->status ?? true) ? 'fa-eye' : 'fa-eye-slash' }} me-1"></i>
                                                <span id="statusText">
                                                    {{ old('status', $post->status ?? true) ? 'Visible para Clientes' : 'Oculto para Clientes' }}
                                                </span>
                                            </small>
                                        </div>

                                        @if (isset($post))
                                            <div class="mt-4 pt-3 border-top">
                                                <h6 class="text-xs font-weight-bold mb-2">Estadísticas</h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-xs">Vistas:</span>
                                                    <span
                                                        class="text-xs font-weight-bold">{{ number_format($post->views) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-xs">Ventas:</span>
                                                    <span
                                                        class="text-xs font-weight-bold">{{ number_format($post->sales) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex flex-sm-row flex-column justify-content-end gap-2">
                                    <a href="{{ route('staff.post.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        {{ isset($post) ? 'Actualizar' : 'Crear' }} Publicación
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
        // Cambiar estado visual
        const statusCheckbox = document.getElementById('status');
        const statusText = document.getElementById('statusText');

        if (statusCheckbox && statusText) {
            statusCheckbox.addEventListener('change', function() {
                const parent = statusText.parentElement;
                const icon = statusText.previousElementSibling;

                if (this.checked) {
                    statusText.textContent = 'Visible para Clientes';
                    parent.style.backgroundColor = '#d4edda';
                    parent.style.color = '#155724';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    statusText.textContent = 'Oculto para Clientes';
                    parent.style.backgroundColor = '#f8d7da';
                    parent.style.color = '#721c24';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        }

        // Validación del formulario
        document.getElementById('postForm').addEventListener('submit', function(e) {
            const productId = document.getElementById('product_id').value;
            const videoInput = document.getElementById('intro_video');
            const isEdit = {{ isset($post) ? 'true' : 'false' }};

            if (!productId) {
                e.preventDefault();
                alert('Debes seleccionar un producto');
                document.getElementById('product_id').focus();
                return false;
            }

            // Validar video solo si es creación o si se subió uno nuevo
            if (!isEdit && (!videoInput.files || videoInput.files.length === 0)) {
                e.preventDefault();
                alert('Debes subir un video de saludo');
                videoInput.focus();
                return false;
            }
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
    </style>
@endpush