<?php
$isEdit = ($mode ?? '') === 'edit';
$action = $isEdit ? 'update' : 'store';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"><?php echo $isEdit ? '✏️ Editar pack' : '➕ Nuevo pack'; ?></h2>
    <a class="btn btn-outline-secondary" href="index.php?c=admin_pack&a=index"><i class="fa-solid fa-arrow-left me-2"></i>Volver</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="index.php?c=admin_pack&a=<?php echo $action; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(SecurityHelper::generateCsrfToken()); ?>">

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-control" required
                    value="<?php echo htmlspecialchars($pack['name'] ?? ''); ?>">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pack padre (opcional)</label>
                    <select name="parent_id" class="form-select">
                        <option value="">-- Ninguno --</option>
                        <?php foreach ($parents as $p): ?>
                            <option value="<?php echo (int)$p['id']; ?>"
                                <?php echo ((int)($pack['parent_id'] ?? 0) === (int)$p['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Imagen (opcional)</label>
                    <input type="text" name="image" class="form-control"
                        value="<?php echo htmlspecialchars($pack['image'] ?? ''); ?>">
                </div>
            </div>

            <?php $packId = (int)($pack['id'] ?? 0); ?>

            <?php if ($isEdit): ?>
                <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="fw-bold">📦 Precio del pack</div>
                        <div class="text-muted">
                            Suma: <strong id="packSum">--</strong>
                            · Final: <strong id="packFinal">--</strong>
                            · Regla: <strong id="packMode">--</strong>
                        </div>
                    </div>
                    <a class="btn btn-sm btn-outline-success"
                        href="index.php?c=admin_pack&a=items&id=<?php echo $packId; ?>">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Editar contenido
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-light border mb-3">
                    <div class="fw-bold">📦 Precio del pack (preview)</div>
                    <div class="text-muted">
                        Suma: <strong id="packSum">0,00€</strong>
                        · Final: <strong id="packFinal">0,00€</strong>
                        · Regla: <strong id="packMode">suma</strong>
                    </div>
                    <div class="small text-muted mt-1">En “Nuevo pack” la suma será 0€ hasta que añadas productos.</div>
                </div>
            <?php endif; ?>


            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Precio fijo del pack (opcional)</label>
                    <input id="finalPriceInput" type="number" step="0.01" min="0" name="final_price" class="form-control"
                        value="<?php echo htmlspecialchars($pack['final_price'] ?? ''); ?>">
                    <div class="form-text">Si lo rellenas, se ignora el descuento %.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Descuento % (opcional)</label>
                    <input id="discountInput" type="number" step="0.01" min="0" max="100" name="discount_percent" class="form-control"
                        value="<?php echo htmlspecialchars($pack['discount_percent'] ?? ''); ?>">
                    <div class="form-text">Se aplica sobre la suma de productos si no hay precio fijo.</div>
                </div>
            </div>

            <?php if ($isEdit): ?>
                <button class="btn btn-success"><i class="fa-solid fa-floppy-disk me-2"></i>Guardar</button>
            <?php else: ?>
                <button class="btn btn-success"><i class="fa-solid fa-plus me-2"></i>Crear</button>
            <?php endif; ?>

            <?php if ($isEdit): ?>
                <a class="btn btn-outline-success ms-2"
                    href="index.php?c=admin_pack&a=items&id=<?php echo (int)$pack['id']; ?>">
                    <i class="fa-solid fa-pen-to-square me-2"></i>Editar contenido
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>
<script>
(function () {
  const isEdit = <?php echo $isEdit ? 'true' : 'false'; ?>;
  const packId = <?php echo (int)($pack['id'] ?? 0); ?>;

  const elSum = document.getElementById('packSum');
  const elFinal = document.getElementById('packFinal');
  const elMode = document.getElementById('packMode');

  const inputFinal = document.getElementById('finalPriceInput');
  const inputDisc = document.getElementById('discountInput');

  let baseSum = 0;

  function fmtEUR(n) {
    const x = Number(n || 0);
    return x.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '€';
  }

  function readNumber(el) {
    if (!el) return null;
    const v = (el.value || '').trim();
    if (v === '') return null;
    const n = Number(v);
    return Number.isFinite(n) ? n : null;
  }

  function recalc() {
    const fp = readNumber(inputFinal);
    const dp = readNumber(inputDisc);

    let mode = 'suma';
    let final = baseSum;

    if (fp !== null) {
        if (fp !== null && inputDisc) inputDisc.value = '';
      mode = 'precio_fijo';
      final = fp;
      // UI: si hay precio fijo, visualmente “anula” descuento
      if (inputDisc) inputDisc.classList.add('opacity-50');
    } else {
      if (inputDisc) inputDisc.classList.remove('opacity-50');
      if (dp !== null) {
        const d = Math.min(100, Math.max(0, dp));
        mode = 'descuento';
        final = baseSum * (1 - (d / 100));
      }
    }

    if (elSum) elSum.textContent = fmtEUR(baseSum);
    if (elFinal) elFinal.textContent = fmtEUR(final);
    if (elMode) elMode.textContent = mode;
  }

  async function loadSumFromServer() {
    if (!isEdit || packId <= 0) {
      baseSum = 0;
      recalc();
      return;
    }

    try {
      const url = `index.php?c=admin_pack&a=priceInfo&id=${encodeURIComponent(packId)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const data = await res.json();

      if (data && data.ok) {
        baseSum = Number(data.sum || 0);
      } else {
        baseSum = 0;
      }
    } catch (e) {
      baseSum = 0;
    }
    recalc();
  }

  // Eventos
  if (inputFinal) inputFinal.addEventListener('input', recalc);
  if (inputDisc) inputDisc.addEventListener('input', recalc);

  // Inicial
  loadSumFromServer();
})();
</script>
