<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
MercadoPago\SDK::setAccessToken('TU_ACCESS_TOKEN'); // Reemplaza con tu access token real

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

// Siempre calcula el monto real desde el carrito y la base de datos
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

try {
    $payment = new MercadoPago\Payment();
    $payment->transaction_amount = round($total,2);
    $payment->token = $data['token'];
    $payment->description = "Compra en Carpintería";
    $payment->installments = (int)$data['installments'];
    $payment->payment_method_id = $data['paymentMethodId'];
    $payment->payer = array(
        "email" => $data['email']
    );
    $payment->save();
    if ($payment->status == 'approved') {
        // Limpia el carrito solo si el pago fue exitoso
        unset($_SESSION['carrito']);
        echo json_encode(['status'=>'approved']);
    } else {
        echo json_encode(['status'=>'error','message'=>"Pago rechazado: " . $payment->status_detail]);
    }
} catch(Exception $e) {
    echo json_encode(['status'=>'error','message'=>'Error: '.$e->getMessage()]);
}