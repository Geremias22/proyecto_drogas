<div class="row">
    <div class="col-12 mb-4">
        <h2 class="border-bottom pb-2">🌿 Nuestro Catálogo</h2>
        <p class="text-muted">Selección exclusiva para socios de El Punto Ciego.</p>
    </div>
</div>

<div class="row">
    <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-warning">No hay productos disponibles en este momento.</div>
        </div>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <?php include 'views/partials/product_card.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>