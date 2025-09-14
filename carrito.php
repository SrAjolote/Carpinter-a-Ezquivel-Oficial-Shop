<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

// Añadir producto al carrito (desde productos.php)
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    $cantidad = isset($_GET['cantidad']) ? max(1, intval($_GET['cantidad'])) : 1;
    if (!isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] = $cantidad;
    } else {
        $_SESSION['carrito'][$id] += $cantidad;
    }
    header("Location: carrito.php");
    exit;
}

// Eliminar producto del carrito
if (isset($_POST['remove'])) {
    $id = intval($_POST['remove']);
    unset($_SESSION['carrito'][$id]);
}

// Actualizar cantidades
if (isset($_POST['update'])) {
    foreach ($_POST['cantidades'] as $id => $cantidad) {
        $cantidad = max(1, intval($cantidad));
        $_SESSION['carrito'][$id] = $cantidad;
    }
}

// Obtener productos del carrito
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Carpintería Artesanal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>.text-madera{color:#a97436!important;font-family:'Merriweather',serif;letter-spacing:1px}.bg-madera{background:linear-gradient(90deg,#a97436 70%,#7a5e3a 100%)!important;color:#fffbe9!important}.btn-madera,.btn-carpinteria{background:linear-gradient(90deg,#a97436 60%,#7a5e3a 100%)!important;color:#fffbe9!important;border:none!important;font-size:1.04rem;border-radius:30px!important;box-shadow:0 4px 16px rgba(169,116,54,0.3);transition:all .3s ease;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:12px 32px}.btn-carpinteria:hover,.btn-madera:hover{background:linear-gradient(90deg,#b89b64 60%,#a97436 100%)!important;color:#fff!important;transform:translateY(-3px);box-shadow:0 6px 20px rgba(169,116,54,0.4)}.btn-warning{background:linear-gradient(70deg,#f6c23e 60%,#f8d298 100%)!important;color:#7a5e3a!important;font-weight:700;border:none;border-radius:30px!important}.btn-danger{background:linear-gradient(90deg,#b74a36 60%,#a97436 100%)!important;color:#fffbe9!important;border-radius:50%!important;width:38px;height:38px;display:flex;align-items:center;justify-content:center;transition:all .2s ease}.btn-danger:hover{transform:scale(1.1);background:#d64c35!important}.btn-secondary{background:linear-gradient(90deg,#c2b49a 60%,#a99b83 100%);color:#fff!important;border:none;border-radius:30px!important}.btn-lg{padding:14px 36px!important;font-size:1.1rem!important}.carrito-table{background:#fff;border-radius:20px;overflow:hidden;border:none;box-shadow:0 8px 30px rgba(169,116,54,0.15)}.table-madera th{background:linear-gradient(90deg,#a97436 70%,#7a5e3a 100%);color:#fffbe9;font-size:1.1rem;border:none;padding:15px 12px;font-family:'Merriweather',serif}.carrito-table td{padding:16px 12px;vertical-align:middle;border-bottom:1px solid rgba(169,116,54,0.1)}.carrito-table tr:last-child td{border-bottom:none}.product-img{width:60px;height:60px;object-fit:cover;border-radius:12px;box-shadow:0 4px 12px rgba(169,116,54,0.2);transition:all .3s ease}.product-img:hover{transform:scale(1.1)}.cantidad-input{border-radius:15px;border:2px solid #e7cba0;background:#fffbe9;font-size:1.1rem;box-shadow:none;width:70px;margin:auto;text-align:center;transition:all .2s ease;padding:8px 5px}.cantidad-input:focus{border-color:#a97436;box-shadow:0 0 0 3px rgba(169,116,54,0.2)}.alert-warning{border-radius:16px;font-size:1.2rem;margin-bottom:30px;background:linear-gradient(90deg,#ffeeba 60%,#fff5d5 100%);color:#7a5e3a;border:none;box-shadow:0 6px 20px rgba(169,116,54,0.2);text-align:center;padding:24px;animation:fadeIn 0.6s ease}.main-content{min-height:60vh}.cart-title{position:relative;display:inline-block;margin-bottom:40px}.cart-title:after{content:'';position:absolute;bottom:-12px;left:50%;transform:translateX(-50%);width:80px;height:3px;background:#a97436;border-radius:3px}.cart-empty-icon{font-size:4rem;color:#a97436;margin-bottom:20px;animation:pulse 2s infinite}.product-name{font-family:'Merriweather',serif;color:#7a5e3a;transition:all .2s ease}.product-name:hover{color:#a97436}.product-desc{font-size:0.95rem;color:#6c757d;line-height:1.5}.total-row{background:#f9f4e8}.total-label{font-size:1.2rem;color:#7a5e3a;font-family:'Merriweather',serif}.total-value{font-size:1.5rem;color:#a97436;font-family:'Merriweather',serif}.actions-container{margin-top:30px}.cart-footer{margin-top:40px;padding-top:20px;border-top:1px dashed rgba(169,116,54,0.3)}.animate-fadeup{animation:fadeUp 0.6s ease}.animate-fadein{animation:fadeIn 0.6s ease}.animate-delay-1{animation-delay:0.3s}@keyframes fadeUp{0%{opacity:0;transform:translateY(20px)}100%{opacity:1;transform:translateY(0)}}@keyframes fadeIn{0%{opacity:0}100%{opacity:1}}@keyframes pulse{0%{transform:scale(1)}50%{transform:scale(1.1)}100%{transform:scale(1)}}@media (max-width:992px){.carrito-table th,.carrito-table td{padding:12px 8px}.product-img{width:50px;height:50px}}@media (max-width:768px){.carrito-table{font-size:0.9rem}.btn-lg{padding:10px 20px!important;font-size:1rem!important}.cart-title{font-size:1.8rem}}@media (max-width:576px){.carrito-table .product-desc{display:none}.carrito-table th:nth-child(3),.carrito-table td:nth-child(3){display:none}.product-img{width:40px;height:40px}.carrito-table{font-size:0.85rem}.btn{padding:8px 16px}.btn-lg{padding:10px 16px!important}.cart-actions{flex-direction:column}.cart-actions .btn{margin-bottom:10px;width:100%}.cantidad-input{width:60px;font-size:0.9rem}}body{font-family:'Poppins',sans-serif;background-color:#fffcf5}</style>
</head>
<body>
<main class="container py-5 main-content">
    <h2 class="mb-5 text-center text-madera cart-title"><i class="fas fa-shopping-cart me-2"></i>Tu Carrito</h2>
    
    <?php if (empty($productos)): ?>
        <div class="text-center animate-fadein">
            <div class="cart-empty-icon">
                <i class="fas fa-shopping-basket"></i>
            </div>
            <div class="alert alert-warning py-4 px-5 d-inline-block">
                <i class="fas fa-exclamation-triangle me-2"></i> ¡Tu carrito está vacío!
            </div>
            <div class="mt-4 mb-5 animate-fadein animate-delay-1">
                <a href="productos.php" class="btn btn-carpinteria btn-lg">
                    <i class="fas fa-cubes me-2"></i> Explorar Productos
                </a>
            </div>
        </div>
    <?php else: ?>
        <form method="post" id="formCarrito">
            <div class="animate-fadeup">
                <div class="table-responsive">
                    <table class="table align-middle carrito-table">
                        <thead class="table-madera">
                            <tr>
                                <th width="80"></th>
                                <th>Producto</th>
                                <th>Descripción</th>
                                <th class="text-center">Precio</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Subtotal</th>
                                <th class="text-center" width="60"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td class="text-center">
                                    <img src="assets/img/<?php echo htmlspecialchars($producto['imagen']); ?>"
                                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                         class="product-img">
                                </td>
                                <td>
                                    <h5 class="product-name fw-bold mb-0"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                </td>
                                <td class="product-desc">
                                    <?php echo htmlspecialchars($producto['descripcion']) ?: '<span class="text-secondary">Sin descripción</span>'; ?>
                                </td>
                                <td class="fw-semibold text-center">$<?php echo number_format($producto['precio'],2); ?></td>
                                <td class="text-center">
                                    <input type="number" min="1" class="form-control cantidad-input" 
                                           name="cantidades[<?php echo $producto['id']; ?>]" 
                                           value="<?php echo $producto['cantidad']; ?>">
                                </td>
                                <td class="fw-bold text-success text-center">$<?php echo number_format($producto['subtotal'],2); ?></td>
                                <td class="text-center">
                                    <button type="submit" name="remove" value="<?php echo $producto['id']; ?>" 
                                            class="btn btn-danger" title="Quitar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="total-row">
                                <td colspan="4" class="text-end total-label fw-bold">Total:</td>
                                <td colspan="3" class="total-value fw-bold">$<?php echo number_format($total,2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="cart-footer">
                <div class="d-flex flex-wrap justify-content-between mt-4 gap-3 actions-container">
                    <a href="productos.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i> Seguir comprando
                    </a>
                    <div class="d-flex gap-3 cart-actions">
                        <button type="submit" name="update" class="btn btn-warning btn-lg" id="btnActualizar">
                            <i class="fas fa-sync-alt me-2"></i> Actualizar
                        </button>
                        <a href="formulario_envio" class="btn btn-carpinteria btn-lg">
                            <i class="fas fa-credit-card me-2"></i> Finalizar Compra
                        </a>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</main>
<script>
// Bloquear clic derecho y selección de texto
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('selectstart', e => e.preventDefault());

// Bloquear copiar, cortar y pegar
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('cut', e => e.preventDefault());
document.addEventListener('paste', e => e.preventDefault());

// Bloquear teclas para devtools, código fuente, guardar, imprimir, terminal y zoom
document.addEventListener('keydown', function(e) {
    // F12 (DevTools)
    if (e.keyCode === 123) e.preventDefault();

    // Ctrl+Shift+I/J/C/K/L (DevTools/terminal/inspeccionar)
    if (e.ctrlKey && e.shiftKey && ['I','J','C','K','L','i','j','c','k','l'].includes(e.key)) e.preventDefault();

    // Ctrl+U (Ver código fuente)
    if (e.ctrlKey && (e.key === 'u' || e.key === 'U')) e.preventDefault();

    // Ctrl+S (Guardar)
    if (e.ctrlKey && (e.key === 's' || e.key === 'S')) e.preventDefault();

    // Ctrl+P (Imprimir)
    if (e.ctrlKey && (e.key === 'p' || e.key === 'P')) e.preventDefault();

    // Ctrl+(+/-/=) (Zoom in/out/reset)
    if (e.ctrlKey && ['+', '-', '=', '_'].includes(e.key)) e.preventDefault();
});

// Bloquear zoom con scroll del mouse (Ctrl + rueda)
window.addEventListener('wheel', function(e) {
    if (e.ctrlKey) e.preventDefault();
}, { passive: false });

// Prevenir arrastrar elementos (por ejemplo imágenes)
document.addEventListener('dragstart', e => e.preventDefault());
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Actualizar carrito automáticamente cuando se cambia una cantidad
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('btnActualizar').click();
        });
    });
</script>
</body>
</html>