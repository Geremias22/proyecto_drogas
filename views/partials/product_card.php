<div class="col-md-4 mb-4">
    <div class="card h-100 shadow-sm">
        <div class="card-body">
            <span class="badge bg-info text-dark mb-2"><?php echo $product['category_name']; ?></span>
            <h5 class="card-title"><?php echo $product['name']; ?></h5>
            <p class="card-text text-muted small">
                <?php echo substr($product['description'], 0, 80) . '...'; ?>
            </p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="h5 mb-0 text-success"><?php echo number_format($product['price'], 2); ?>€</span>
                <a href="index.php?c=product_show&a=index&id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm">Ver más</a>
            </div>
        </div>
        <div class="card-footer bg-transparent border-top-0">
            <a href="index.php?c=cart&a=add&id=<?php echo $product['id']; ?>" class="btn btn-success w-100">Añadir al carrito</a>
        </div>
    </div>
</div>