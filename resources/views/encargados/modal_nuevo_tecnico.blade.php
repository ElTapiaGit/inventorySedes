<!-- Modal: Registrar Nuevo Tecnico -->
<div class="modal fade" id="modalNuevoTecnico" tabindex="-1" aria-labelledby="modalNuevoTecnicoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow rounded-4 border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalNuevoTecnicoLabel">
                    <i class="bi bi-person-plus me-2"></i>Registrar Nuevo Técnico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formNuevoTecnico" class="no-disable" action="{{ route('encargado.tecnicos.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body p-4">
                    <!-- Contenedor dinamico de alertas de error -->
                    <div class="alert alert-danger d-none mb-3" id="alertaErroresTecnico" role="alert">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Por favor corrija los siguientes errores:</strong>
                        </div>
                        <ul class="mb-0 ps-3 small" id="listaErroresTecnico"></ul>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="tecnico_nombre" class="form-label fw-semibold">
                                Nombres <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tecnico_nombre" name="nombre" maxlength="60" placeholder="Ej. Juan Carlos" required>
                            <div class="invalid-feedback" id="feedback_tecnico_nombre"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="tecnico_ap_paterno" class="form-label fw-semibold">
                                Apellido Paterno <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tecnico_ap_paterno" name="ap_paterno" maxlength="60" placeholder="Ej. Pérez" required>
                            <div class="invalid-feedback" id="feedback_tecnico_ap_paterno"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="tecnico_ap_materno" class="form-label fw-semibold">
                                Apellido Materno <small class="text-muted fw-normal">(Opcional)</small>
                            </label>
                            <input type="text" class="form-control" id="tecnico_ap_materno" name="ap_materno" maxlength="60" placeholder="Ej. Gómez">
                            <div class="invalid-feedback" id="feedback_tecnico_ap_materno"></div>
                        </div>

                        <div class="col-12">
                            <label for="tecnico_celular" class="form-label fw-semibold">
                                Celular / Teléfono <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tecnico_celular" name="celular" maxlength="20" placeholder="Ej. 70012345" required>
                            <div class="invalid-feedback" id="feedback_tecnico_celular"></div>
                        </div>

                        <div class="col-12">
                            <label for="tecnico_direccion" class="form-label fw-semibold">
                                Dirección / Taller <small class="text-muted fw-normal">(Opcional)</small>
                            </label>
                            <textarea class="form-control" id="tecnico_direccion" name="direccion" rows="2" maxlength="150" placeholder="Ej. Av. 6 de Agosto #1234, Taller de Soporte"></textarea>
                            <div class="invalid-feedback" id="feedback_tecnico_direccion"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-3" id="btnGuardarTecnico">
                        <i class="bi bi-save me-1"></i> Guardar Técnico
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
