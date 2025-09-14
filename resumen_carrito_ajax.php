<?php
session_start();
include 'includes/db.php';

$productos = [];
$total = 0;
if (!empty($_SESSION['carrito'])) {
    $ids = implode(",", array_map('intval', array_keys($_SESSION['carrito'])));
    if (!empty($ids)) {
        $sql = "SELECT * FROM productos WHERE id IN ($ids)";
        $result = $conexion->query($sql);
        while ($row = $result->fetch_assoc()) {
            $row['cantidad'] = $_SESSION['carrito'][$row['id']];
            $row['subtotal'] = $row['cantidad'] * $row['precio'];
            $productos[] = $row;
            $total += $row['subtotal'];
        }
    }
}
?>
<h5><i class="fa fa-list"></i> Resumen de tu compra</h5>
<ul class="checkout-list">
    <?php foreach ($productos as $p): ?>
    <li class="checkout-list-item">
        <div class="checkout-product-info">
            <img src="assets/img/<?php echo htmlspecialchars($p['imagen']); ?>" alt="" class="checkout-thumb">
            <span class="checkout-prodname"><?php echo htmlspecialchars($p['nombre']); ?></span>
            <span class="checkout-prodqty">x<?php echo $p['cantidad']; ?></span>
        </div>
        <div class="checkout-prodprice">$<?php echo number_format($p['subtotal'],2); ?></div>
    </li>
    <?php endforeach; ?>
    <li class="checkout-list-item checkout-total">
        <span>Total:</span>
        <span class="checkout-total-price">$<?php echo number_format($total,2); ?></span>
    </li>
</ul>