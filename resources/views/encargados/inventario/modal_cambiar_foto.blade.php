<!-- Modal Universal: Cambiar Fotografia del Articulo -->
<div class="modal fade" id="modalCambiarFotoArticulo" tabindex="-1" aria-labelledby="modalCambiarFotoArticuloLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: #1B4B82;">
                <h5 class="modal-title fs-5" id="modalCambiarFotoArticuloLabel">
                    <i class="bi bi-camera me-2"></i> Cambiar Fotografia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCambiarFotoArticulo" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-danger d-none mb-3" id="modalFotoErrorBox" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <span id="modalFotoErrorMsg"></span>
                    </div>

                    <!-- Ficha identificatoria del articulo -->
                    <div class="p-3 mb-3 rounded-3" style="background-color: #F1F5F9; border-left: 4px solid #1B4B82;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge" style="background-color: #1B4B82; font-size: 0.85rem;" id="modalFotoCodigoArticulo">-</span>
                            <small class="text-muted text-uppercase fw-semibold" id="modalFotoTipoArticulo"></small>
                        </div>
                        <div class="fw-semibold text-dark text-truncate" id="modalFotoNombreArticulo">-</div>
                    </div>

                    <!-- Comparativa visual: Foto Actual vs Nueva Foto -->
                    <div class="row g-3 mb-3">
                        <div class="col-6 text-center">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Foto Actual</label>
                            <div class="border rounded-3 p-1 bg-white shadow-sm d-flex align-items-center justify-content-center" style="height: 140px; overflow: hidden;">
                                <img id="modalFotoActualPreview" src="{{ asset('img/placeholder-articulo.svg') }}" alt="Foto actual" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Nueva Foto</label>
                            <div class="border rounded-3 p-1 bg-white shadow-sm d-flex align-items-center justify-content-center position-relative" style="height: 140px; overflow: hidden;" id="modalFotoNuevaContainer">
                                <img id="modalFotoNuevaPreview" src="{{ asset('img/placeholder-articulo.svg') }}" alt="Nueva foto" style="max-height: 100%; max-width: 100%; object-fit: contain; opacity: 0.4;">
                                <span class="badge bg-secondary position-absolute bottom-0 mb-2" id="modalFotoNuevaBadge">Sin seleccionar</span>
                            </div>
                        </div>
                    </div>

                    <!-- Input para nueva foto -->
                    <div class="mb-2">
                        <label for="inputCambiarFoto" class="form-label fw-semibold">
                            Seleccionar nueva imagen <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" id="inputCambiarFoto" name="foto" accept="image/jpeg,image/png,image/webp" required>
                        <div class="form-text small text-muted">
                            <i class="bi bi-info-circle me-1"></i> Formatos admitidos: JPG, PNG, WEBP. Tamano maximo: 5 MB.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSubmitCambiarFoto" style="background-color: #1B4B82; border-color: #1B4B82;">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Guardar Fotografia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('modalCambiarFotoArticulo');
    if (!modalElement) return;

    const form = document.getElementById('formCambiarFotoArticulo');
    const inputFoto = document.getElementById('inputCambiarFoto');
    const codigoSpan = document.getElementById('modalFotoCodigoArticulo');
    const nombreSpan = document.getElementById('modalFotoNombreArticulo');
    const tipoSpan = document.getElementById('modalFotoTipoArticulo');
    const actualPreview = document.getElementById('modalFotoActualPreview');
    const nuevaPreview = document.getElementById('modalFotoNuevaPreview');
    const nuevaBadge = document.getElementById('modalFotoNuevaBadge');
    const errorBox = document.getElementById('modalFotoErrorBox');
    const errorMsg = document.getElementById('modalFotoErrorMsg');
    const placeholderUrl = "{{ asset('img/placeholder-articulo.svg') }}";

    function showError(msg) {
        if (!errorBox || !errorMsg) return;
        errorMsg.textContent = msg;
        errorBox.classList.remove('d-none');
    }

    function clearError() {
        if (!errorBox || !errorMsg) return;
        errorBox.classList.add('d-none');
        errorMsg.textContent = '';
    }

    function resetModal() {
        clearError();
        if (inputFoto) inputFoto.value = '';
        if (nuevaPreview) {
            nuevaPreview.src = placeholderUrl;
            nuevaPreview.style.opacity = '0.4';
        }
        if (nuevaBadge) {
            nuevaBadge.textContent = 'Sin seleccionar';
            nuevaBadge.className = 'badge bg-secondary position-absolute bottom-0 mb-2';
        }
    }

    modalElement.addEventListener('show.bs.modal', function (event) {
        resetModal();
        const triggerBtn = event.relatedTarget;
        if (!triggerBtn) return;

        const action = triggerBtn.getAttribute('data-action') || '';
        const codigo = triggerBtn.getAttribute('data-codigo') || '';
        const nombre = triggerBtn.getAttribute('data-nombre') || '';
        const tipo = triggerBtn.getAttribute('data-tipo') || '';
        const fotoActual = triggerBtn.getAttribute('data-foto-actual') || placeholderUrl;

        if (form) form.action = action;
        if (codigoSpan) codigoSpan.textContent = codigo;
        if (nombreSpan) nombreSpan.textContent = nombre;
        if (tipoSpan) tipoSpan.textContent = tipo;
        if (actualPreview) actualPreview.src = fotoActual;
    });

    if (inputFoto) {
        inputFoto.addEventListener('change', function () {
            clearError();
            const file = this.files && this.files[0];
            if (!file) {
                resetModal();
                return;
            }

            // Validar tamano (maximo 5MB = 5 * 1024 * 1024)
            const maxBytes = 5 * 1024 * 1024;
            if (file.size > maxBytes) {
                showError('El archivo seleccionado supera el limite maximo permitido de 5 MB.');
                this.value = '';
                nuevaPreview.src = placeholderUrl;
                nuevaPreview.style.opacity = '0.4';
                nuevaBadge.textContent = 'Archivo demasiado grande';
                nuevaBadge.className = 'badge bg-danger position-absolute bottom-0 mb-2';
                return;
            }

            // Validar tipo de imagen
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                showError('Formato no valido. Seleccione una imagen en formato JPG, PNG o WEBP.');
                this.value = '';
                nuevaPreview.src = placeholderUrl;
                nuevaPreview.style.opacity = '0.4';
                nuevaBadge.textContent = 'Formato no valido';
                nuevaBadge.className = 'badge bg-danger position-absolute bottom-0 mb-2';
                return;
            }

            // Previsualizar la nueva imagen
            const reader = new FileReader();
            reader.onload = function (e) {
                nuevaPreview.src = e.target.result;
                nuevaPreview.style.opacity = '1';
                nuevaBadge.textContent = 'Lista para subir';
                nuevaBadge.className = 'badge bg-success position-absolute bottom-0 mb-2';
            };
            reader.readAsDataURL(file);
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            if (!inputFoto || !inputFoto.files || inputFoto.files.length === 0) {
                e.preventDefault();
                showError('Debe seleccionar un archivo de imagen antes de guardar.');
                return false;
            }
        });
    }
});
</script>
