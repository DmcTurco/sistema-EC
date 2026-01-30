@extends('admin.layout.base')

@section('title', isset($product) ? 'Editar Producto' : 'Crear Producto')

@section('page-title', isset($product) ? 'Editar Producto' : 'Crear Producto')

@section('content')

<div class="row h-100">
    <div class="col-12">
        <div class="card h-100 d-flex flex-column">
            <div class="card-header flex-shrink-0">
                <div class="row align-items-center">
                    <div class="col-sm-8 col-12">
                        <h3 class="mb-0">
                            {{ isset($product) ? 'Editar Producto' : 'Nuevo Producto' }}
                        </h3>
                        <p class="text-sm mb-0">
                            {{ isset($product) ? 'Modifica la información del producto' : 'Completa la información del
                            producto' }}
                        </p>
                    </div>
                    <div class="col-sm-4 col-12 text-sm-end text-start mt-sm-0 mt-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body flex-grow-1 overflow-auto">
                <form
                    action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
                    method="POST" enctype="multipart/form-data" id="productForm">
                    @csrf
                    @if (isset($product))
                    @method('PUT')
                    @endif

                    <div class="row">
                        <!-- Columna Izquierda - Información Básica -->
                        <div class="col-lg-8 col-12">
                            <div class="card mb-4">
                                <div class="card-header pb-0">
                                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                        Información Básica
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Nombre -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="name" class="form-label">
                                                Nombre del Producto <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $product->name ?? '') }}"
                                                placeholder="Ej: Laptop HP Pavilion" required>
                                            @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Descripción -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="description" class="form-label">
                                                Descripción
                                            </label>
                                            <textarea class="form-control @error('description') is-invalid @enderror"
                                                id="description" name="description" rows="4"
                                                placeholder="Describe las características del producto...">{{ old('description', $product->description ?? '') }}</textarea>
                                            @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Precio y Stock -->
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-3">
                                            <label for="price" class="form-label">
                                                Precio <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number"
                                                    class="form-control @error('price') is-invalid @enderror" id="price"
                                                    name="price" value="{{ old('price', $product->price ?? '') }}"
                                                    step="0.01" min="0" placeholder="0.00" required>
                                                @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12 mb-3">
                                            <label for="stock" class="form-label">
                                                Stock <span class="text-danger">*</span>
                                            </label>
                                            <input type="number"
                                                class="form-control @error('stock') is-invalid @enderror" id="stock"
                                                name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0"
                                                placeholder="0" required>
                                            @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- SKU -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="sku" class="form-label">
                                                SKU / Código
                                            </label>
                                            <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                                id="sku" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                                                placeholder="Ej: PROD-001">
                                            @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Video del Producto -->
                            <div class="card mb-4">
                                <div class="card-header pb-0">
                                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                        <i class="fas fa-video me-1"></i> Video del Producto
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Vista previa del video -->
                                    <div id="videoPreviewContainer" class="mb-3">
                                        @if (isset($product) && $product->main_video_path)
                                        <div class="position-relative">
                                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm">
                                                <video id="videoPlayer" controls class="bg-dark">
                                                    <source src="{{ asset('storage/' . $product->main_video_path) }}"
                                                        type="video/mp4">
                                                    Tu navegador no soporta videos.
                                                </video>
                                            </div>
                                            <div
                                                class="d-flex justify-content-between align-items-center mt-2 p-2 bg-light rounded">
                                                <small class="text-muted">
                                                    <i class="fas fa-file-video me-1"></i>
                                                    {{ basename($product->main_video_path) }}
                                                </small>
                                                <button type="button" class="btn btn-sm btn-danger" id="removeVideo">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                            <input type="hidden" name="remove_video" id="remove_video" value="0">
                                        </div>
                                        @else
                                        <!-- Sin video - Mostrar placeholder -->
                                        <div class="text-center p-5 border rounded-3 bg-light" id="noVideoPlaceholder">
                                            <i class="fas fa-video fa-3x text-secondary mb-3"></i>
                                            <p class="text-muted mb-0">No hay video cargado</p>
                                            <small class="text-muted">Sube un video para mostrar tu
                                                producto</small>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Input para subir video -->
                                    <div id="videoUploadSection">
                                        <label for="main_video_path" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            {{ isset($product) && $product->main_video_path ? 'Cambiar Video' : 'Subir
                                            Video' }}
                                        </label>
                                        <input type="file" class="d-none @error('main_video_path') is-invalid @enderror"
                                            id="main_video_path" name="main_video_path"
                                            accept="video/mp4,video/webm,video/avi,video/mov">

                                        <!-- Info del archivo seleccionado -->
                                        <div id="videoFileInfo" class="mt-2 p-2 bg-light rounded"
                                            style="display: none;">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-file-video me-1"></i>
                                                <span id="videoFileName"></span>
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-weight me-1"></i>
                                                Tamaño: <span id="videoFileSize"></span>
                                            </small>
                                            <div class="progress mt-2" style="height: 4px; display: none;"
                                                id="videoProgress">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Formatos soportados: MP4, WebM, AVI, MOV<br>
                                        Tamaño máximo: 50MB
                                    </small>

                                    @error('main_video_path')
                                    <div class="text-danger text-sm mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha - Imagen y Estado -->
                        <div class="col-lg-4 col-12">
                            <!-- Imagen del Producto -->
                            <div class="card mb-4">
                                <div class="card-header pb-0">
                                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">
                                        <i class="fas fa-image me-1"></i> Imagen del Producto
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <div class="position-relative d-inline-block">
                                            <img id="imagePreview"
                                                src="{{ isset($product) && $product->image ? asset('storage/' . $product->image) : asset('assets/img/marie.jpg') }}"
                                                class="img-fluid rounded-3 shadow-sm"
                                                style="max-height: 200px; width: auto;" alt="Preview">
                                            @if (isset($product) && $product->image)
                                            <button type="button"
                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                                id="removeImage" data-bs-toggle="tooltip" title="Eliminar imagen">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <input type="hidden" name="remove_image" id="remove_image" value="0">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label for="image" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-upload"></i>
                                            {{ isset($product) && $product->image ? 'Cambiar Imagen' : 'Subir Imagen' }}
                                        </label>
                                        <input type="file" class="d-none @error('image') is-invalid @enderror"
                                            id="image" name="image" accept="image/*">
                                    </div>

                                    <small class="text-muted d-block">
                                        Formatos: JPG, PNG, GIF<br>
                                        Tamaño máx: 2MB
                                    </small>

                                    @error('image')
                                    <div class="text-danger text-sm mt-2">{{ $message }}</div>
                                    @enderror
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
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status"
                                            value="1" {{ old('status', $product->status ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Producto Activo
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Los productos inactivos no se mostrarán en el catálogo
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex flex-sm-row flex-column justify-content-end gap-2">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    {{ isset($product) ? 'Actualizar Producto' : 'Guardar Producto' }}
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
    let currentVideoURL = null;

    // ===== PREVIEW DE IMAGEN =====
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgPreview = document.getElementById('imagePreview');
                imgPreview.src = e.target.result;
                
                // ✅ Agregar botón de cancelar si no existe
                if (!document.getElementById('cancelImageBtn')) {
                    const imgContainer = imgPreview.parentElement;
                    const cancelBtn = document.createElement('button');
                    cancelBtn.type = 'button';
                    cancelBtn.id = 'cancelImageBtn';
                    cancelBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 m-2';
                    cancelBtn.innerHTML = '<i class="fas fa-times"></i>';
                    cancelBtn.title = 'Cancelar imagen';
                    cancelBtn.onclick = function() {
                        document.getElementById('image').value = '';
                        imgPreview.src = '{{ asset("assets/img/marie.jpg") }}';
                        this.remove();
                    };
                    imgContainer.appendChild(cancelBtn);
                }
            }
            reader.readAsDataURL(file);
        }
    });

    // ===== PREVIEW DE VIDEO =====
    document.getElementById('main_video_path').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamaño (100MB)
            const maxSize = 100 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('El archivo es demasiado grande. Máximo 100MB');
                this.value = '';
                return;
            }

            // Mostrar info del archivo
            const fileInfo = document.getElementById('videoFileInfo');
            const fileName = document.getElementById('videoFileName');
            const fileSize = document.getElementById('videoFileSize');

            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.style.display = 'block';
            fileInfo.style.backgroundColor = '#d4edda';
            fileInfo.style.borderLeft = '4px solid #28a745';
            fileInfo.style.padding = '10px';

            // Liberar URL anterior
            if (currentVideoURL) {
                URL.revokeObjectURL(currentVideoURL);
            }

            // Crear preview del video
            const videoContainer = document.getElementById('videoPreviewContainer');
            const noVideoPlaceholder = document.getElementById('noVideoPlaceholder');

            if (noVideoPlaceholder) {
                noVideoPlaceholder.style.display = 'none';
            }

            currentVideoURL = URL.createObjectURL(file);

            const newVideoHTML = `
                <div class="position-relative">
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm" style="border: 3px solid #28a745;">
                        <video id="videoPlayer" controls class="bg-dark">
                            <source src="${currentVideoURL}" type="${file.type}">
                            Tu navegador no soporta videos.
                        </video>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 p-2 rounded" style="background-color: #28a745; color: white;">
                        <div>
                            <strong><i class="fas fa-check-circle me-1"></i> Video cargado</strong><br>
                            <small><i class="fas fa-file-video me-1"></i>${file.name} (${formatFileSize(file.size)})</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" id="cancelVideoBtn" title="Cancelar video">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            `;

            videoContainer.innerHTML = newVideoHTML;

            // ✅ Event listener para botón cancelar
            document.getElementById('cancelVideoBtn').addEventListener('click', function() {
                // Limpiar input
                document.getElementById('main_video_path').value = '';
                
                // Liberar memoria
                if (currentVideoURL) {
                    URL.revokeObjectURL(currentVideoURL);
                    currentVideoURL = null;
                }
                
                // Ocultar info
                fileInfo.style.display = 'none';
                
                // Restaurar placeholder
                videoContainer.innerHTML = `
                    <div class="text-center p-5 border rounded-3 bg-light" id="noVideoPlaceholder">
                        <i class="fas fa-video fa-3x text-secondary mb-3"></i>
                        <p class="text-muted mb-0">No hay video cargado</p>
                        <small class="text-muted">Sube un video para mostrar tu producto</small>
                    </div>
                `;
            });
        }
    });

    // ===== FUNCIONES AUXILIARES =====
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // ===== REMOVER IMAGEN EXISTENTE =====
    const removeImageBtn = document.getElementById('removeImage');
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            document.getElementById('imagePreview').src = '{{ asset("assets/img/marie.jpg") }}';
            document.getElementById('remove_image').value = '1';
            this.remove();
        });
    }

    // ===== REMOVER VIDEO EXISTENTE =====
    const removeVideoBtn = document.getElementById('removeVideo');
    if (removeVideoBtn) {
        removeVideoBtn.addEventListener('click', function() {
            if (confirm('¿Estás seguro de eliminar el video?')) {
                const videoContainer = document.getElementById('videoPreviewContainer');
                document.getElementById('remove_video').value = '1';
                videoContainer.innerHTML = `
                    <div class="text-center p-5 border rounded-3 bg-light" id="noVideoPlaceholder">
                        <i class="fas fa-video fa-3x text-secondary mb-3"></i>
                        <p class="text-muted mb-0">No hay video cargado</p>
                        <small class="text-muted">Sube un video para mostrar tu producto</small>
                    </div>
                `;
            }
        });
    }

    // ===== VALIDACIÓN AL ENVIAR =====
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const price = parseFloat(document.getElementById('price').value);
        const stock = parseInt(document.getElementById('stock').value);

        if (price < 0) {
            e.preventDefault();
            alert('El precio no puede ser negativo');
            return false;
        }

        if (stock < 0) {
            e.preventDefault();
            alert('El stock no puede ser negativo');
            return false;
        }
    });

    // ===== LIBERAR MEMORIA AL SALIR =====
    window.addEventListener('beforeunload', function() {
        if (currentVideoURL) {
            URL.revokeObjectURL(currentVideoURL);
        }
    });
</script>
@endpush

@push('styles')
<style>
    video {
        object-fit: contain;
    }

    /* ✅ LIMITAR TAMAÑO DEL CONTAINER */
    #videoPreviewContainer {
        max-height: 600px;
    }

    #videoPreviewContainer .ratio {
        max-height: 450px;
    }

    #videoPreviewContainer video {
        max-height: 450px;
        object-fit: contain;
    }

    .btn-danger:hover {
        transform: scale(1.05);
        transition: transform 0.2s;
    }

    #noVideoPlaceholder {
        transition: all 0.3s ease;
    }

    #noVideoPlaceholder:hover {
        background-color: #f8f9fa !important;
        border-color: #5e72e4 !important;
    }
</style>
@endpush