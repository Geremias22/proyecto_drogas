<div class="card shadow-sm mt-4">
    <div class="row g-0">
        <!-- Columna de Imagen (Placeholder por ahora) -->
        <div class="col-md-5 bg-dark d-flex align-items-center justify-content-center text-white" style="min-height: 300px;">
            <h2 class="opacity-25">IMAGEN</h2>
        </div>

        <!-- Columna de Información -->
        <div class="col-md-7">
            <div class="card-body p-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php?c=product_list">Catálogo</a></li>
                        <li class="breadcrumb-item active"><?php echo $product['category_name']; ?></li>
                    </ol>
                </nav>

                <h1 class="display-5 fw-bold"><?php echo $product['name']; ?></h1>

                <h3 class="text-success my-3"><?php echo number_format($product['price'], 2); ?> € / unidad</h3>

                <p class="lead text-muted">
                    <?php echo nl2br($product['description']); ?>
                </p>

                <hr>

                <div class="d-flex align-items-center mb-4">
                    <form action="index.php?c=cart&a=add&id=<?php echo $product['id']; ?>" method="POST" class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <label class="form-label small fw-bold">Cantidad:</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock_qty']; ?>" style="width: 80px;">
                        </div>
                        <div>
                            <p class="mb-0 small text-muted">
                                Stock disponible: <strong><?php echo $product['stock_qty']; ?> unidades</strong>
                            </p>
                        </div>
                </div>

                <div class="d-grid gap-2 d-md-flex">
                    <?php $outOfStock = ((int)$product['stock_qty'] <= 0); ?>
                    <button class="btn btn-success btn-lg px-5" <?php echo $outOfStock ? 'disabled' : ''; ?>>
                        Añadir al Carrito
                    </button>
                    </form>
                    <a href="index.php?c=product_list" class="btn btn-outline-secondary btn-lg">Volver</a>
                </div>
            </div>
        </div>
    </div>
</div>