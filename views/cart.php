<h2 class="mb-4"><i class="fa-solid fa-cart-shopping"></i> Tu Carrito de Compra</h2>

<?php if (empty($productsInCart)): ?>
    <div class="alert alert-info p-5 text-center">
        <h4>Tu carrito está vacío.</h4>
        <p>¿A qué esperas? Pásate por nuestro catálogo.</p>
        <a href="index.php?c=product_list" class="btn btn-primary">Ir al Catálogo</a>
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productsInCart as $p): ?>
                        <tr>
                            <td>
                                <strong><?php echo $p['name']; ?></strong><br>
                                <small class="text-muted"><?php echo $p['category_name']; ?></small>
                            </td>
                            <td><?php echo number_format($p['price'], 2); ?>€</td>
                            <td><?php echo $p['quantity']; ?></td>
                            <td><strong><?php echo number_format($p['subtotal'], 2); ?>€</strong></td>
                            <td>
                                <a href="index.php?c=cart&a=remove&id=<?php echo $p['id']; ?>" class="btn btn-outline-danger btn-sm">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end h4">Total:</td>
                        <td colspan="2" class="h4 text-success"><?php echo number_format($total, 2); ?>€</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="index.php?c=cart&a=clear" class="btn btn-outline-secondary">Vaciar Carrito</a>
        <div>
            <a href="index.php?c=product_list" class="btn btn-outline-primary">Seguir Comprando</a>
            <a href="index.php?c=order&a=checkout" class="btn btn-success px-5">Finalizar Pedido ➔</a>
        </div>
    </div>
<?php endif; ?>