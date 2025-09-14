<?php
include '../includes/auth.php';
include '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
    exit;
}

$product_id = intval($_GET['product_id']);

try {
    $stmt = $conexion->prepare("SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, orden ASC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = [
            'id' => $row['id'],
            'imagen' => $row['imagen'],
            'orden' => $row['orden'],
            'es_principal' => (bool)$row['es_principal']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'images' => $images
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener imágenes: ' . $e->getMessage()
    ]);
}
?>