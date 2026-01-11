<div class="card shadow-sm mt-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="mb-1"><?php echo htmlspecialchars($pack['name']); ?></h2>
                <p class="text-muted mb-0">Contenido del pack y detalle de productos.</p>
            </div>

            <div class="text-end">
                <div class="h4 text-success mb-0">
                    Total pack: <?php echo number_format($final, 2); ?> €
                </div>

                <div class="text-muted small">
                    Suma productos: <strong><?php echo number_format($sum, 2); ?> €</strong>
                    · Regla:
                    <strong>
                    <?php
                        echo ($mode === 'precio_fijo') ? 'Precio fijo'
                        : (($mode === 'descuento') ? ('Descuento ' . number_format((float)$pack['discount_percent'], 2) . '%')
                        : 'Suma');
                    ?>
                    </strong>
                </div>
                </div>
        </div>

        <hr>

        <?php if (empty($items)): ?>
            <div class="alert alert-warning mb-0">Este pack no tiene productos asociados.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Categoría</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Subtotal</th>
                            <th class="text-center">Descuento</th>
                            <th class="text-center">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                            <?php
                                $qty = (int)$it['qty'];
                                $price = (float)$it['price'];
                                $subtotal = $qty * $price;
                                $stock = (int)($it['stock_qty'] ?? 0);
                                $inactive = isset($it['is_active']) && (int)$it['is_active'] !== 1;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($it['name']); ?></strong><br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars(substr($it['description'] ?? '', 0, 70)); ?>...
                                    </small>
                                </td>
                                <td class="text-center"><?php echo htmlspecialchars($it['category_name']); ?></td>
                                <td class="text-center"><?php echo $qty; ?></td>
                                <td class="text-end"><?php echo number_format($price, 2); ?> €</td>
                                <td class="text-end"><strong><?php echo number_format($subtotal, 2); ?> €</strong></td>
                                <td class="text-center">
                                    <?php
                                        if ($mode === 'descuento') {
                                            $discountPercent = (float)$pack['discount_percent'];
                                            $discountAmount = $subtotal * ($discountPercent / 100);
                                            echo number_format($discountAmount, 2) . ' €';
                                        } else {
                                            echo '-';
                                        }
                                    ?>
                                <td class="text-center">
                                    <?php if ($inactive): ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php elseif ($stock <= 0): ?>
                                        <span class="badge bg-danger">Sin stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo $stock; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end h5 mb-0">Total pack:</td>
                            <td colspan="2" class="text-end h5 text-success mb-0">
                            <?php echo number_format($final, 2); ?> €
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a class="btn btn-outline-secondary" href="index.php?c=home&a=index">Volver</a>

                <!-- Opcional: botón para añadir el pack al carrito (lo montaríamos luego) -->
                <!-- <a class="btn btn-success" href="#">Añadir pack al carrito</a> -->
            </div>
        <?php endif; ?>
    </div>
</div>
