<?php if (empty($products)): ?>
    <div class="col-12">
        <div class="alert alert-warning">No hay productos disponibles en este momento.</div>
    </div>
<?php else: ?>
    <?php foreach ($products as $product): ?>
        <?php include 'views/partials/product_card.php'; ?>
    <?php endforeach; ?>
<?php endif; ?>
