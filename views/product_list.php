<div class="row">
    <div class="col-9 mb-3">
        <h2 class="border-bottom pb-2">
        🌿 Nuestro Catálogo
        <?php if (!empty($currentCategory)): ?>
            <span class="text-muted fs-5">— <?php echo htmlspecialchars($currentCategory['name']); ?></span>
        <?php endif; ?>
        </h2>
        <p class="text-muted">Selección exclusiva para socios de El Punto Ciego.</p>
    </div>

    <div class="col-3 mb-4">
        <input
            id="productSearch"
            type="text"
            class="form-control"
            placeholder="Buscar por nombre o descripción..."
            autocomplete="off"
        >
    </div>
    <input type="hidden" id="categoryId" value="<?php echo (int)($currentCategoryId ?? 0); ?>">

</div>

<div id="productGrid" class="row">
    <?php require 'views/partials/product_grid.php'; ?>
</div>
