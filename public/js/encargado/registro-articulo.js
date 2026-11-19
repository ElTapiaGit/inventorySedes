/**
 * registro-articulo.js
 * Logica modular para formularios de registro de articulos en el panel de Encargado.
 * Gestiona fotografia dual (subir nueva vs reutilizar existente), contadores de caracteres en tiempo real
 * y limpieza reactiva de validaciones is-invalid.
 * Compatible al 100% con form-protection.js.
 */

(function () {
    'use strict';

    /**
     * Inicializa los contadores de caracteres en tiempo real para inputs y textareas.
     */
    function initCharacterCounters() {
        const countableElements = document.querySelectorAll('input[maxlength], textarea[maxlength], input[data-maxlength], textarea[data-maxlength]');

        countableElements.forEach(el => {
            const max = parseInt(el.getAttribute('data-maxlength') || el.getAttribute('maxlength'), 10);
            if (!max || isNaN(max)) return;

            // Buscar elemento visual del contador asociado
            let counterEl = null;
            if (el.id) {
                counterEl = document.querySelector(`.char-counter[data-for="${el.id}"]`);
            }
            if (!counterEl && el.parentElement) {
                counterEl = el.parentElement.querySelector('.char-counter');
            }

            function updateCounter() {
                const currentLength = el.value.length;
                if (!counterEl) return;

                counterEl.textContent = `${currentLength} / ${max}`;

                // Umbrales visuales: 85% advertencia, 100% peligro
                counterEl.classList.remove('text-muted', 'text-warning', 'text-danger', 'fw-bold', 'fw-semibold');

                if (currentLength >= max) {
                    counterEl.classList.add('text-danger', 'fw-bold');
                } else if (currentLength >= Math.floor(max * 0.85)) {
                    counterEl.classList.add('text-warning', 'fw-semibold');
                } else {
                    counterEl.classList.add('text-muted');
                }
            }

            el.addEventListener('input', updateCounter);
            // Actualizar inmediatamente por si trae datos precargados de old()
            updateCounter();
        });
    }

    /**
     * Limpieza dinámica de clases is-invalid al interactuar con campos con error previo.
     */
    function initDynamicValidationReset() {
        document.addEventListener('input', function (event) {
            const target = event.target;
            if (target && target.classList && target.classList.contains('is-invalid')) {
                target.classList.remove('is-invalid');
            }
        });

        document.addEventListener('change', function (event) {
            const target = event.target;
            if (target && target.classList && target.classList.contains('is-invalid')) {
                target.classList.remove('is-invalid');
            }
        });
    }

    /**
     * Inicializa la gestion dual de fotos (subida de nuevo archivo vs seleccion de foto existente).
     */
    function initPhotoManager() {
        const fotoInput = document.getElementById('foto');
        const existingFotoInput = document.getElementById('existing_foto_id');
        const photoPreview = document.getElementById('photoPreview');
        const previewBox = document.getElementById('previewBox');
        const previewEmpty = document.getElementById('previewEmpty');
        const btnLimpiar = document.getElementById('btnLimpiarFoto');
        const thumbItems = document.querySelectorAll('.reusable-thumb-item');
        const btnUltimaFoto = document.getElementById('btnUsarUltimaFoto');

        if (!photoPreview && !fotoInput && !existingFotoInput) {
            return;
        }

        const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        const ALLOWED_EXTS = ['.jpeg', '.jpg', '.png', '.webp'];
        const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5 MB

        /**
         * Muestra una imagen en el contenedor de previsualizacion.
         * @param {string} src
         */
        function setPreviewImage(src) {
            if (photoPreview) {
                photoPreview.src = src;
                photoPreview.classList.remove('d-none');
            }
            if (previewEmpty) {
                previewEmpty.classList.add('d-none');
            }
            if (previewBox) {
                previewBox.classList.add('has-image');
            }
            if (btnLimpiar) {
                btnLimpiar.classList.remove('d-none');
            }
        }

        /**
         * Restaura el contenedor de previsualizacion al estado vacio.
         */
        function resetPreview() {
            if (photoPreview) {
                photoPreview.src = '';
                photoPreview.classList.add('d-none');
            }
            if (previewEmpty) {
                previewEmpty.classList.remove('d-none');
            }
            if (previewBox) {
                previewBox.classList.remove('has-image');
            }
            if (btnLimpiar) {
                btnLimpiar.classList.add('d-none');
            }
        }

        /**
         * Desmarca todas las miniaturas de la galeria de reutilizables.
         */
        function clearGallerySelection() {
            thumbItems.forEach(item => item.classList.remove('selected'));
        }

        /**
         * Selecciona una foto reutilizable por ID y URL.
         * @param {string|number} id
         * @param {string} url
         */
        function selectExistingPhoto(id, url) {
            if (!id || !url) return;

            // Limpiar input de archivo para no enviar archivos contradictorios
            if (fotoInput) {
                fotoInput.value = '';
                fotoInput.classList.remove('is-invalid');
            }

            // Asignar al campo oculto
            if (existingFotoInput) {
                existingFotoInput.value = id;
                existingFotoInput.classList.remove('is-invalid');
            }

            // Marcar visualmente la miniatura correspondiente
            clearGallerySelection();
            const matchingThumb = document.querySelector(`.reusable-thumb-item[data-foto-id="${id}"]`);
            if (matchingThumb) {
                matchingThumb.classList.add('selected');
            }

            // Actualizar vista previa
            setPreviewImage(url);
        }

        // 1. Manejo del input de archivo (subir nueva foto)
        if (fotoInput) {
            fotoInput.addEventListener('change', function (event) {
                const file = event.target.files && event.target.files[0];
                if (!file) {
                    return;
                }

                // Validacion de extension y tipo MIME
                const fileName = (file.name || '').toLowerCase();
                const hasValidExt = ALLOWED_EXTS.some(ext => fileName.endsWith(ext));
                const hasValidType = ALLOWED_TYPES.includes(file.type.toLowerCase()) || hasValidExt;

                if (!hasValidType) {
                    fotoInput.value = '';
                    const errorMsg = 'Formato de imagen no permitido. Utilice solo archivos JPG, PNG o WEBP.';
                    if (window.Swal) {
                        Swal.fire({ icon: 'warning', title: 'Archivo no válido', text: errorMsg });
                    } else {
                        alert(errorMsg);
                    }
                    resetPreview();
                    return;
                }

                // Validacion de tamano maximo (5MB)
                if (file.size > MAX_SIZE_BYTES) {
                    fotoInput.value = '';
                    const errorMsg = 'La imagen excede el límite de 5MB. Por favor optimice o elija otra imagen.';
                    if (window.Swal) {
                        Swal.fire({ icon: 'warning', title: 'Tamaño excedido', text: errorMsg });
                    } else {
                        alert(errorMsg);
                    }
                    resetPreview();
                    return;
                }

                // Si la imagen es valida, anular seleccion reutilizada previa
                if (existingFotoInput) {
                    existingFotoInput.value = '';
                }
                clearGallerySelection();

                // Cargar preview con FileReader nativo
                const reader = new FileReader();
                reader.onload = function (e) {
                    setPreviewImage(e.target.result);
                };
                reader.readAsDataURL(file);
            });
        }

        // 2. Manejo de clics en miniaturas de fotos reutilizables
        thumbItems.forEach(thumb => {
            thumb.addEventListener('click', function () {
                const id = this.getAttribute('data-foto-id');
                const url = this.getAttribute('data-foto-url');
                selectExistingPhoto(id, url);
            });
        });

        // 3. Manejo de boton de acceso rapido a la ultima foto registrada
        if (btnUltimaFoto) {
            btnUltimaFoto.addEventListener('click', function () {
                const id = this.getAttribute('data-foto-id');
                const url = this.getAttribute('data-foto-url');
                selectExistingPhoto(id, url);
            });
        }

        // 4. Manejo del boton Limpiar / Quitar seleccion
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', function () {
                if (fotoInput) {
                    fotoInput.value = '';
                }
                if (existingFotoInput) {
                    existingFotoInput.value = '';
                }
                clearGallerySelection();
                resetPreview();
            });
        }

        // 5. Restauracion en caso de old('existing_foto_id') precargado
        if (existingFotoInput && existingFotoInput.value) {
            const preselectedId = existingFotoInput.value;
            const preselectedThumb = document.querySelector(`.reusable-thumb-item[data-foto-id="${preselectedId}"]`);
            if (preselectedThumb) {
                const url = preselectedThumb.getAttribute('data-foto-url');
                selectExistingPhoto(preselectedId, url);
            }
        }
    }

    // Inicializar al cargar el DOM
    document.addEventListener('DOMContentLoaded', function () {
        initCharacterCounters();
        initDynamicValidationReset();
        initPhotoManager();
    });

    // Exponer API para inicializaciones dinamicas si fueran necesarias
    window.RegistroArticulo = {
        initCounters: initCharacterCounters,
        initPhotoManager: initPhotoManager
    };
})();
