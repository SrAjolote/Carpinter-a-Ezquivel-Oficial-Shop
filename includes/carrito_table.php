<?php
// $productos y $total deben estar definidos antes de incluir este archivo.
if (empty($productos)): ?>
    <div class="alert alert-warning text-center mt-4 mb-5 animate-fadein">
        <i class="fa fa-exclamation-triangle"></i> ¡Tu carrito está vacío!
    </div>
    <div class="text-center mb-5 animate-fadein animate-delay-1">
        <a href="productos.php" class="btn btn-carpinteria btn-lg"><i class="fa fa-cube"></i> Ver productos</a>
    </div>
<?php else: ?>
<form method="post" id="formCarrito">
    <div class="table-responsive animate-slideup">
        <table class="table align-middle table-bordered carrito-table shadow-sm">
            <thead class="table-madera">
                <tr>
                    <th></th>
                    <th>Producto</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Quitar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                <tr id="row-<?php echo $producto['id']; ?>">
                    <td class="text-center">
                        <img src="assets/img/<?php echo htmlspecialchars($producto['imagen']); ?>"
                             alt=""
                             style="width:44px; height:44px; object-fit:cover; border-radius:10px; box-shadow:0 1px 6px #a9743630;">
                    </td>
                    <td>
                        <strong class="fw-bolder"><?php echo htmlspecialchars($producto['nombre']); ?></strong>
                    </td>
                    <td class="text-muted" style="font-size:.99em;">
                        <?php echo htmlspecialchars($producto['descripcion']) ?: '<span class="text-secondary">Sin descripción</span>'; ?>
                    </td>
                    <td class="fw-semibold">$<?php echo number_format($producto['precio'],2); ?></td>
                    <td style="width:90px;">
                        <input type="number" min="1" class="form-control text-center cantidad-input" name="cantidades[<?php echo $producto['id']; ?>]" value="<?php echo $producto['cantidad']; ?>">
                    </td>
                    <td class="fw-bold text-success">$<?php echo number_format($producto['subtotal'],2); ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-remove shadow-sm" data-id="<?php echo $producto['id']; ?>" title="Quitar"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5" class="text-end fw-bold">Total:</td>
                    <td colspan="2" class="fw-bold text-success fs-5">$<?php echo number_format($total,2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex flex-wrap justify-content-between mt-3 gap-2">
        <a href="productos.php" class="btn btn-secondary btn-lg shadow-sm"><i class="fa fa-arrow-left"></i> Seguir comprando</a>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-warning btn-lg shadow-sm" id="btnActualizar">
                <i class="fa fa-sync-alt"></i> Actualizar
            </button>
            <a href="checkout.php" class="btn btn-carpinteria btn-lg shadow-sm"><i class="fa fa-credit-card"></i> Ir a Pagar</a>
        </div>
    </div>
</form>
<?php endif; ?>