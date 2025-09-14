<?php
session_start();
require_once "vendor/autoload.php"; // Ajusta la ruta si usas composer

// Configura tu access_token de MercadoPago
MercadoPago\SDK::setAccessToken(''); // PON AQUÍ TU ACCESS TOKEN

// Calcula el total del carrito
$total = 0;
$items = [];
if (!empty($_SESSION['carrito'])) {
    include 'includes/db.php';
    $ids = implode(",", array_map('intval', array_keys($_SESSION['carrito'])));
    if (!empty($ids)) {
        $sql = "SELECT * FROM productos WHERE id IN ($ids)";
        $result = $conexion->query($sql);
        while ($row = $result->fetch_assoc()) {
            $cantidad = $_SESSION['carrito'][$row['id']];
            $items[] = [
                "title" => $row['nombre'],
                "quantity" => $cantidad,
                "currency_id" => "MXN",
                "unit_price" => floatval($row['precio'])
            ];
            $total += $cantidad * $row['precio'];
        }
    }
}

if ($total <= 0) {
    // Si no hay productos, regresa al checkout
    header("Location: checkout.php");
    exit;
}

// Crea la preferencia
$preference = new MercadoPago\Preference();
$preference->items = [];
foreach ($items as $item) {
    $producto = new MercadoPago\Item();
    $producto->title = $item['title'];
    $producto->quantity = $item['quantity'];
    $producto->unit_price = $item['unit_price'];
    $producto->currency_id = $item['currency_id'];
    $preference->items[] = $producto;
}

// Puedes personalizar return_url según tu flujo
$preference->back_urls = [
    "success" => "https://tusitio.com/gracias.php", // Cambia por tu URL
    "failure" => "https://tusitio.com/checkout.php",
    "pending" => "https://tusitio.com/checkout.php"
];
$preference->auto_return = "approved";
$preference->save();

// Redirige al usuario al checkout de Mercado Pago
header("Location: " . $preference->init_point);
exit;
?>