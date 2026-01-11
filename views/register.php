<form id="registerForm" action="index.php?c=auth&a=registerPost" method="POST" class="card p-4 shadow needs-validation" novalidate>
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="name" class="form-control"
            required minlength="3" maxlength="60"
            pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s'-]{3,60}$">
        <div class="invalid-feedback">Nombre válido (3-60), sin caracteres raros.</div>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input id="emailInput" type="email" name="gmail" class="form-control" required maxlength="255">
        <!-- <div class="invalid-feedback">Introduce un email válido.</div> -->
        <div id="emailFeedback" class="form-text"></div>
    </div>

    <div class="mb-3">
        <label>Edad (opcional)</label>
        <input type="number" name="edad" class="form-control" min="0" max="120">
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input
                class="form-check-input"
                type="checkbox"
                id="adultCheck"
                name="adult_confirm"
                value="1"
                required>
            <label class="form-check-label" for="adultCheck">
                Declaro que soy mayor de 18 años y acepto las normas de la asociación.
            </label>
            <div class="invalid-feedback">
                Debes confirmar que eres mayor de 18 años para registrarte.
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label>Contraseña</label>
        <input type="password" name="password" class="form-control"
            required minlength="8" maxlength="72"
            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,72}$">
        <div class="invalid-feedback">
            8+ caracteres, con mayúscula, minúscula, número y símbolo.
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Pregunta de seguridad</label>
        <select name="security_question" class="form-select" required>
            <option value="">Selecciona una pregunta…</option>
            <option value="¿Cómo se llamaba tu primera mascota?">¿Cómo se llamaba tu primera mascota?</option>
            <option value="¿Cuál es tu ciudad de nacimiento?">¿Cuál es tu ciudad de nacimiento?</option>
            <option value="¿Cuál es tu película favorita?">¿Cuál es tu película favorita?</option>
            <option value="¿Cómo se llama tu mejor amigo/a de la infancia?">¿Cómo se llama tu mejor amigo/a de la infancia?</option>
        </select>
        <div class="invalid-feedback">Selecciona una pregunta.</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Respuesta (no la olvides)</label>
        <input type="text" name="security_answer" class="form-control" required minlength="2" maxlength="80">
        <div class="invalid-feedback">Escribe una respuesta válida.</div>
    </div>


    <button id="registerBtn" type="submit" class="btn btn-success w-100">Registrarme</button>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger mt-3 mb-0">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
</form>

<script>
    (function() {
        const emailInput = document.getElementById('emailInput');
        const feedback = document.getElementById('emailFeedback');
        const form = document.getElementById('registerForm');
        const btn = document.getElementById('registerBtn');

        let lastValue = '';
        let emailIsAvailable = false;
        let timer = null;

        if (!form) return;

        function setState(state, msg) {
            // state: 'ok' | 'bad' | 'idle'
            emailInput.classList.remove('is-valid', 'is-invalid');

            if (state === 'ok') {
                emailInput.classList.add('is-valid');
                feedback.className = 'valid-feedback d-block';
                feedback.textContent = msg || 'Email disponible';
                emailIsAvailable = true;
                btn.disabled = false;
                return;
            }

            if (state === 'bad') {
                emailInput.classList.add('is-invalid');
                feedback.className = 'invalid-feedback d-block';
                feedback.textContent = msg || 'Email no disponible';
                emailIsAvailable = false;
                btn.disabled = true;
                return;
            }

            // idle
            feedback.className = 'form-text';
            feedback.textContent = msg || '';
            emailIsAvailable = false;
            btn.disabled = false; // no bloqueamos hasta tener confirmación
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
                    setState('idle', 'No se pudo verificar el email ahora');
                    return;
                }

                if (data.valid === false) {
                    setState('bad', data.message || 'Formato de email no válido');
                    return;
                }

                if (data.available) setState('ok', data.message);
                else setState('bad', data.message);

            } catch (e) {
                setState('idle', 'Error verificando el email');
            }
        }

        emailInput.addEventListener('input', function() {
            const value = emailInput.value.trim();

            // reset básico
            if (timer) clearTimeout(timer);

            if (value === '') {
                lastValue = '';
                setState('idle', '');
                return;
            }

            const looksLikeEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            if (!looksLikeEmail) {
                setState('idle', 'Escribe un email válido…');
                return;
            }

            // debounce (evita llamadas a cada tecla)
            timer = setTimeout(() => {
                if (value === lastValue) return;
                lastValue = value;
                setState('idle', 'Comprobando…');
                checkEmail(value);
            }, 350);
        });

        form.addEventListener('submit', function(e) {
            // Si el input tiene algo y ya sabemos que NO está disponible: bloquea submit
            const value = emailInput.value.trim();
            if (value !== '' && emailInput.classList.contains('is-invalid')) {
                e.preventDefault();
            }
        });

        form.addEventListener('submit', function(e) {
            const value = emailInput.value.trim();

            // si hay email escrito y no está confirmado como válido/disponible, bloquea
            if (value !== '' && !emailInput.classList.contains('is-valid')) {
                e.preventDefault();
                e.stopPropagation();
                setState('bad', 'Confirma un email válido y disponible');
            }
        });

    })();
</script>