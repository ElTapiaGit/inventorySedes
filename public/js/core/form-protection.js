/**
 * form-protection.js
 * Proteccion global contra doble clic (Race Conditions), persistencia en cache (BFCache)
 * y limpieza automatica de modales Bootstrap en formularios operativos.
 */

(function () {
    'use strict';

    /**
     * Deshabilita los botones de envio de un formulario e inyecta el spinner de carga.
     * @param {HTMLFormElement} form
     */
    function disableSubmitButtons(form) {
        if (!form) return;
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])');
        submitButtons.forEach(btn => {
            // Guardar texto o HTML original si no se ha guardado previamente
            if (btn.tagName === 'INPUT') {
                if (btn.dataset.originalValue === undefined) {
                    btn.dataset.originalValue = btn.value;
                }
                btn.value = 'Procesando...';
            } else {
                if (btn.dataset.originalHtml === undefined) {
                    btn.dataset.originalHtml = btn.innerHTML;
                }
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Procesando...';
            }
            btn.disabled = true;
        });
    }

    /**
     * Restaura los botones de envio a su estado original y los habilita.
     * @param {HTMLFormElement} form
     */
    function restoreSubmitButtons(form) {
        if (!form) return;
        delete form.dataset.submitting;
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])');
        submitButtons.forEach(btn => {
            btn.disabled = false;
            if (btn.tagName === 'INPUT') {
                if (btn.dataset.originalValue !== undefined) {
                    btn.value = btn.dataset.originalValue;
                    delete btn.dataset.originalValue;
                }
            } else {
                if (btn.dataset.originalHtml !== undefined) {
                    btn.innerHTML = btn.dataset.originalHtml;
                    delete btn.dataset.originalHtml;
                }
            }
        });
    }

    /**
     * Purga filas clonadas dinamicamente en modales, preservando solo la primera fila base.
     * @param {HTMLFormElement} form
     */
    function purgeDynamicRows(form) {
        if (!form) return;
        const dynamicRowSelectors = [
            '#articulos-container .articulo-row',
            '#articulos-prestamo-container .articulo-row',
            '#pisosContainer .piso-item',
            '.dynamic-rows-container .dynamic-row'
        ];

        dynamicRowSelectors.forEach(selector => {
            const rows = form.querySelectorAll(selector);
            rows.forEach((row, index) => {
                if (index > 0) {
                    row.remove();
                } else {
                    row.querySelectorAll('input:not([type="hidden"]), textarea').forEach(input => {
                        input.value = '';
                    });
                }
            });
        });
    }

    // 1. FASE DE CAPTURA: Interceptar y abortar envios duplicados inmediatamente
    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;

        // Si ya se encuentra en proceso de envio, cancelar cualquier nuevo evento de envio
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            event.stopImmediatePropagation();
            return false;
        }
    }, true);

    // 2. FASE DE BURBUJEO: Bloquear botones tras superar validaciones y confirmaciones
    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;

        // Formularios explicitamente excluidos
        if (form.classList.contains('no-disable')) return;

        // Formularios de busqueda/filtro GET no alteran estado en base de datos
        const method = (form.getAttribute('method') || 'GET').toUpperCase();
        if (method === 'GET') return;

        // Si alguna validacion previa o dialogo SweetAlert cancelo el submit, no bloquear
        if (event.defaultPrevented) return;

        // Marcar formulario como en proceso de envio y deshabilitar botones
        form.dataset.submitting = 'true';
        disableSubmitButtons(form);

        // Salvaguarda: si un listener posterior en la cola cancela el evento, restaurar
        setTimeout(function () {
            if (event.defaultPrevented) {
                restoreSubmitButtons(form);
            }
        }, 0);
    }, false);

    // 3. INTERCEPCION PROGRAMATICA (HTMLFormElement.prototype.submit)
    // Protege llamadas directas a form.submit() originadas por SweetAlert u otros scripts
    const originalSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function () {
        if (!this.classList.contains('no-disable')) {
            const method = (this.getAttribute('method') || 'GET').toUpperCase();
            if (method !== 'GET') {
                if (this.dataset.submitting === 'true') {
                    return;
                }
                this.dataset.submitting = 'true';
                disableSubmitButtons(this);
            }
        }
        return originalSubmit.apply(this, arguments);
    };

    // 4. MANEJO DE BFCACHE Y NAVEGACION HISTORICA (pageshow)
    window.addEventListener('pageshow', function (event) {
        document.querySelectorAll('form').forEach(form => {
            restoreSubmitButtons(form);
        });

        // Limpiar fondos oscuros y modales residuales si se navego hacia atras
        if (event.persisted) {
            document.querySelectorAll('.modal.show').forEach(openModal => {
                if (window.bootstrap && bootstrap.Modal) {
                    const modalInstance = bootstrap.Modal.getInstance(openModal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                } else {
                    openModal.classList.remove('show');
                    openModal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                }
            });
        }
    });

    // 5. LIMPIEZA AUTOMATICA DE MODALES BOOTSTRAP (hidden.bs.modal)
    document.addEventListener('hidden.bs.modal', function (event) {
        const modal = event.target;
        if (!(modal instanceof HTMLElement)) return;

        const forms = modal.querySelectorAll('form');
        forms.forEach(form => {
            // Siempre restaurar el estado de envio para permitir nuevos intentos
            restoreSubmitButtons(form);

            // Si el formulario tiene la clase 'no-reset', omitir el reseteo de campos
            if (form.classList.contains('no-reset')) {
                return;
            }

            // Resetear valores de los inputs
            form.reset();

            // Limpiar clases de validacion visual de Bootstrap
            form.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
                el.classList.remove('is-invalid', 'is-valid');
            });

            // Purgar filas dinamicas clonadas
            purgeDynamicRows(form);
        });
    });

    // Exportar utilidades de apoyo al objeto window
    window.FormProtection = {
        disable: disableSubmitButtons,
        restore: restoreSubmitButtons,
        purgeRows: purgeDynamicRows
    };
})();
