<?php
$old = $_SESSION['old'] ?? [];
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error'], $_SESSION['old']);

function old($key, $default = '')
{
    global $old;
    return isset($old[$key]) ? $old[$key] : $default;
}
?>

<form id="registerForm"
    action="index.php?c=auth&a=registerPost"
    method="POST"
    class="card p-4 shadow needs-validation"
    novalidate>

     <div class="mb-3">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(SecurityHelper::generateCsrfToken()); ?>">
        <label class="form-label">Nombre</label>
        <input type="text"
            name="name"
            class="form-control"
            required minlength="3" maxlength="60"
            pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s'-]{3,60}$"
            value="<?php echo htmlspecialchars(old('name')); ?>">
        <div class="invalid-feedback">Nombre válido (3-60), sin caracteres raros.</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input id="emailInput"
            type="email"
            name="gmail"
            class="form-control"
            required maxlength="255"
            value="<?php echo htmlspecialchars(old('gmail')); ?>">
        <div id="emailFeedback" class="form-text"></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Edad (opcional)</label>
        <input type="number"
            name="edad"
            class="form-control"
            min="0" max="120"
            value="<?php echo htmlspecialchars(old('edad')); ?>">
        <div class="form-text">Opcional.</div>
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input"
                type="checkbox"
                id="adultCheck"
                name="adult_confirm"
                value="1"
                required
                <?php echo old('adult_confirm') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="adultCheck">
                Declaro que soy mayor de 18 años y acepto las normas de la asociación.
            </label>
            <div class="invalid-feedback">
                Debes confirmar que eres mayor de 18 años para registrarte.
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input id="passwordInput"
            type="password"
            name="password"
            class="form-control"
            required
            minlength="8"
            maxlength="72"
            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,72}$">
        <div class="invalid-feedback">
            8+ caracteres, con mayúscula, minúscula, número y símbolo.
        </div>
        <div id="passwordHelp" class="form-text">
            Usa mayúscula, minúscula, número y símbolo.
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Pregunta de seguridad</label>
        <select name="security_question" class="form-select" required>
            <option value="">Selecciona una pregunta…</option>

            <?php $q = old('security_question'); ?>
            <option value="¿Cómo se llamaba tu primera mascota?" <?php echo $q === '¿Cómo se llamaba tu primera mascota?' ? 'selected' : ''; ?>>
                ¿Cómo se llamaba tu primera mascota?
            </option>
            <option value="¿Cuál es tu ciudad de nacimiento?" <?php echo $q === '¿Cuál es tu ciudad de nacimiento?' ? 'selected' : ''; ?>>
                ¿Cuál es tu ciudad de nacimiento?
            </option>
            <option value="¿Cuál es tu película favorita?" <?php echo $q === '¿Cuál es tu película favorita?' ? 'selected' : ''; ?>>
                ¿Cuál es tu película favorita?
            </option>
            <option value="¿Cómo se llama tu mejor amigo/a de la infancia?" <?php echo $q === '¿Cómo se llama tu mejor amigo/a de la infancia?' ? 'selected' : ''; ?>>
                ¿Cómo se llama tu mejor amigo/a de la infancia?
            </option>
        </select>
        <div class="invalid-feedback">Selecciona una pregunta.</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Respuesta (no la olvides)</label>
        <input type="text"
            name="security_answer"
            class="form-control"
            required minlength="2" maxlength="80"
            value="<?php echo htmlspecialchars(old('security_answer')); ?>">
        <div class="invalid-feedback">Escribe una respuesta válida.</div>
    </div>

    <button id="registerBtn" type="submit" class="btn btn-success w-100">
        Registrarme
    </button>

    <?php if ($flashError): ?>
        <div class="alert alert-danger mt-3 mb-0">
            <?php echo htmlspecialchars($flashError); ?>
        </div>
    <?php endif; ?>

</form>

<script>
    (function() {
        const form = document.getElementById('registerForm');
        if (!form) return;

        const btn = document.getElementById('registerBtn');

        // Email AJAX
        const emailInput = document.getElementById('emailInput');
        const emailFeedback = document.getElementById('emailFeedback');
        let lastValue = '';
        let timer = null;

        // Password realtime
        const passwordInput = document.getElementById('passwordInput');

        function setEmailState(state, msg) {
            emailInput.classList.remove('is-valid', 'is-invalid');

            if (state === 'ok') {
                emailInput.classList.add('is-valid');
                emailFeedback.className = 'valid-feedback d-block';
                emailFeedback.textContent = msg || 'Email disponible';
                return;
            }

            if (state === 'bad') {
                emailInput.classList.add('is-invalid');
                emailFeedback.className = 'invalid-feedback d-block';
                emailFeedback.textContent = msg || 'Email no disponible';
                return;
            }

            emailFeedback.className = 'form-text';
            emailFeedback.textContent = msg || '';
        }

        async function checkEmail(email) {
            try {
                const url = `index.php?c=auth&a=checkEmail&email=${encodeURIComponent(email)}`;
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();

                if (!data || data.ok !== true) {
                    setEmailState('idle', 'No se pudo verificar el email ahora');
                    return;
                }

                if (data.valid === false) {
                    setEmailState('bad', data.message || 'Formato de email no válido');
                    return;
                }

                if (data.available) setEmailState('ok', data.message);
                else setEmailState('bad', data.message);

            } catch (e) {
                setEmailState('idle', 'Error verificando el email');
            }
        }

        // Password realtime (verde/rojo al escribir)
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                if (passwordInput.value.trim() === '') {
                    passwordInput.classList.remove('is-valid', 'is-invalid');
                    return;
                }
                if (passwordInput.checkValidity()) {
                    passwordInput.classList.add('is-valid');
                    passwordInput.classList.remove('is-invalid');
                } else {
                    passwordInput.classList.add('is-invalid');
                    passwordInput.classList.remove('is-valid');
                }
            });
        }

        // Email input: debounce
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                const value = emailInput.value.trim();
                if (timer) clearTimeout(timer);

                if (value === '') {
                    lastValue = '';
                    setEmailState('idle', '');
                    emailInput.classList.remove('is-valid', 'is-invalid');
                    return;
                }

                const looksLikeEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                if (!looksLikeEmail) {
                    setEmailState('idle', 'Escribe un email válido…');
                    emailInput.classList.remove('is-valid');
                    return;
                }

                timer = setTimeout(() => {
                    if (value === lastValue) return;
                    lastValue = value;
                    setEmailState('idle', 'Comprobando…');
                    checkEmail(value);
                }, 350);
            });
        }

        // Si venimos con email ya escrito (por old), disparamos comprobación al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const value = (emailInput?.value || '').trim();
            const looksLikeEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            if (value !== '' && looksLikeEmail) {
                lastValue = value;
                setEmailState('idle', 'Comprobando…');
                checkEmail(value);
            }
        });

        // Submit: validación bootstrap + bloquear si email no confirmado
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            const emailValue = (emailInput?.value || '').trim();
            if (emailValue !== '' && emailInput && !emailInput.classList.contains('is-valid')) {
                event.preventDefault();
                event.stopPropagation();
                setEmailState('bad', 'Confirma un email válido y disponible');
            }

            form.classList.add('was-validated');
        }, false);

    })();
</script>