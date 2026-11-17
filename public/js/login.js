document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const loginButton = document.getElementById('loginButton');
    const togglePasswordBtn = document.querySelector('.toggle-password');
    const toggleIcon = document.getElementById('toggleIcon');

    const STORAGE_KEY = 'login_retry_until';
    let countdownTimer = null;

    // Formatear segundos a MM:SS
    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        const formattedMins = mins < 10 ? '0' + mins : mins;
        const formattedSecs = secs < 10 ? '0' + secs : secs;
        return `${formattedMins}:${formattedSecs}`;
    }

    // Temporizador de bloqueo por Rate Limiting
    function startRateLimitCountdown(retryAfterSeconds) {
        if (countdownTimer) {
            clearInterval(countdownTimer);
        }

        let remaining = parseInt(retryAfterSeconds, 10);
        if (isNaN(remaining) || remaining <= 0) {
            remaining = 60;
        }

        // Guardar marca de expiracion en sessionStorage para persistencia ante recargas
        const expiryTimestamp = Date.now() + (remaining * 1000);
        sessionStorage.setItem(STORAGE_KEY, expiryTimestamp.toString());

        // Deshabilitar inputs y boton
        usernameInput.disabled = true;
        passwordInput.disabled = true;
        loginButton.disabled = true;

        loginButton.innerText = `Intente en ${formatTime(remaining)}`;

        countdownTimer = setInterval(() => {
            remaining--;

            if (remaining <= 0) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                sessionStorage.removeItem(STORAGE_KEY);

                // Rehabilitar inputs y boton
                usernameInput.disabled = false;
                passwordInput.disabled = false;
                loginButton.disabled = false;
                loginButton.innerText = 'Ingresar';

                passwordInput.value = '';
                updateInputState(passwordInput);
                usernameInput.focus();
            } else {
                loginButton.innerText = `Intente en ${formatTime(remaining)}`;
            }
        }, 1000);
    }

    // Verificar si existe un bloqueo activo guardado en la sesion
    function checkStoredRateLimit() {
        const storedUntil = sessionStorage.getItem(STORAGE_KEY);
        if (storedUntil) {
            const remaining = Math.ceil((parseInt(storedUntil, 10) - Date.now()) / 1000);
            if (remaining > 0) {
                startRateLimitCountdown(remaining);
            } else {
                sessionStorage.removeItem(STORAGE_KEY);
            }
        }
    }

    // Actualizar estado de etiqueta flotante
    function updateInputState(input) {
        if (!input) return;
        const container = input.closest('.input-container');
        if (!container) return;

        if (input.value && input.value.trim() !== '') {
            container.classList.add('has-value');
        } else {
            container.classList.remove('has-value');
        }
    }

    // Gestion de inputs y etiquetas flotantes
    const inputs = document.querySelectorAll('.input-container input');
    inputs.forEach(input => {
        const container = input.closest('.input-container');

        input.addEventListener('focus', function () {
            if (container) container.classList.add('focused');
        });

        input.addEventListener('blur', function () {
            if (container) container.classList.remove('focused');
            updateInputState(input);
        });

        input.addEventListener('input', function () {
            updateInputState(input);
        });

        // Inicializar estado en carga inicial
        updateInputState(input);
    });

    // Alternar visibilidad de contrasena
    if (togglePasswordBtn && passwordInput && toggleIcon) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.classList.toggle('fa-eye', !isPassword);
            toggleIcon.classList.toggle('fa-eye-slash', isPassword);
        });
    }

    // Intercepcion del submit mediante Fetch API
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Si hay un bloqueo activo por temporizador, no proceder
            if (countdownTimer) return;

            const username = usernameInput.value.trim();
            const password = passwordInput.value;

            if (!username || !password) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Por favor ingrese su usuario y contraseña.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            // Obtener token CSRF
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfTokenInput = document.querySelector('input[name="_token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : (csrfTokenInput ? csrfTokenInput.value : '');

            // Estado visual de carga
            loginButton.disabled = true;
            loginButton.innerText = 'Ingresando...';

            try {
                const response = await fetch(loginForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });

                const data = await response.json().catch(() => ({}));

                // Capturar error 429 de bloqueo por fuerza bruta
                if (response.status === 429 || data.retry_after) {
                    const retryAfter = data.retry_after || 60;
                    startRateLimitCountdown(retryAfter);

                    passwordInput.value = '';
                    updateInputState(passwordInput);

                    Swal.fire({
                        icon: 'warning',
                        title: 'Acceso bloqueado',
                        text: data.message || 'Demasiados intentos de acceso fallidos. Por favor espere a que termine el temporizador.',
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }

                // Autenticacion exitosa
                if (response.ok && data.success && data.redirect) {
                    window.location.replace(data.redirect);
                    return;
                }

                // Extraccion de mensaje de error
                let mensajeError = 'Credenciales incorrectas.';
                if (data.message) {
                    mensajeError = data.message;
                } else if (data.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    mensajeError = data.errors[firstKey][0] || mensajeError;
                }

                // Limpieza inmediata del campo de contraseña
                passwordInput.value = '';
                updateInputState(passwordInput);
                passwordInput.focus();

                Swal.fire({
                    icon: 'error',
                    title: 'Acceso denegado',
                    text: mensajeError,
                    confirmButtonColor: '#4f46e5'
                });

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de comunicacion',
                    text: 'Ocurrio un fallo de conexion con el servidor. Intente nuevamente.',
                    confirmButtonColor: '#4f46e5'
                });
            } finally {
                // Si no hay bloqueo por temporizador, restaurar boton
                if (!countdownTimer) {
                    loginButton.disabled = false;
                    loginButton.innerText = 'Ingresar';
                }
            }
        });
    }

    // Manejo de eventos Back-Forward Cache (bfcache)
    window.addEventListener('pageshow', function (event) {
        if (passwordInput) {
            passwordInput.value = '';
            updateInputState(passwordInput);
        }
        checkStoredRateLimit();
    });

    // Validar bloqueo almacenado al cargar la pagina
    checkStoredRateLimit();
});
