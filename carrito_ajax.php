<?php
session_start();
header('Content-Type: application/json');

include 'includes/db.php';

// Validar que se reciba POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$idProducto = isset($_POST['add']) ? (int)$_POST['add'] : 0;
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;

if ($idProducto <= 0 || $cantidad <= 0) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

// Validar que el producto exista en la DB y obtener stock
$sql = "SELECT *, COALESCE(stock, 0) as stock_disponible FROM productos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $idProducto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    exit;
}

$producto = $result->fetch_assoc();
$stock_disponible = (int)$producto['stock_disponible'];

// Validar stock disponible
if ($stock_disponible <= 0) {
    echo json_encode(['success' => false, 'message' => 'Producto agotado']);
    exit;
}

// Aquí asumo que el carrito se guarda en sesión
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Calcular cantidad total que tendría en el carrito
$cantidad_actual_carrito = isset($_SESSION['carrito'][$idProducto]) ? $_SESSION['carrito'][$idProducto] : 0;
$cantidad_total = $cantidad_actual_carrito + $cantidad;

// Validar que no exceda el stock
if ($cantidad_total > $stock_disponible) {
    $cantidad_disponible = $stock_disponible - $cantidad_actual_carrito;
    if ($cantidad_disponible <= 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Ya tienes el máximo disponible de este producto en tu carrito'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => "Solo puedes agregar {$cantidad_disponible} unidad(es) más de este producto"
        ]);
    }
    exit;
}

// Añadir o actualizar producto en carrito
if (isset($_SESSION['carrito'][$idProducto])) {
    $_SESSION['carrito'][$idProducto] += $cantidad;
} else {
    $_SESSION['carrito'][$idProducto] = $cantidad;
}

// Calcular totales del carrito
$total_items = array_sum($_SESSION['carrito']);
$total_precio = 0;

foreach ($_SESSION['carrito'] as $prod_id => $cant) {
    $sql_precio = "SELECT precio FROM productos WHERE id = ?";
    $stmt_precio = $conexion->prepare($sql_precio);
    $stmt_precio->bind_param('i', $prod_id);
    $stmt_precio->execute();
    $result_precio = $stmt_precio->get_result();
    if ($row_precio = $result_precio->fetch_assoc()) {
        $total_precio += $row_precio['precio'] * $cant;
    }
}

echo json_encode([
    'success' => true, 
    'message' => 'Producto agregado al carrito',
    'cart_count' => $total_items,
    'cart_total' => number_format($total_precio, 2),
    'product_name' => $producto['nombre'],
    'quantity_added' => $cantidad
]);
