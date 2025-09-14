<?php
// --- AUTENTICACIÓN SIMPLE ---
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

// --- CONEXIÓN A LA BASE DE DATOS ---
$host = "localhost";
$user = "u182426195_carpinteria";
$pass = "2415691611+David";
$db   = "u182426195_carpinteria";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Error DB: " . $conn->connect_error);

// --- MANEJO DE MENSAJES ---
$mensaje = "";

// --- ELIMINAR PRODUCTO ---
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    try {
        $conn->autocommit(false);
        
        // Verificar si la tabla producto_imagenes existe
        $check_table = $conn->query("SHOW TABLES LIKE 'producto_imagenes'");
        $has_images_table = $check_table->num_rows > 0;
        
        if ($has_images_table) {
            // Obtener y eliminar todas las imágenes del producto
            $stmt = $conn->prepare("SELECT imagen FROM producto_imagenes WHERE producto_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $imagenes_result = $stmt->get_result();
            
            while ($img_row = $imagenes_result->fetch_assoc()) {
                $archivo_imagen = "../assets/img/" . $img_row['imagen'];
                if (file_exists($archivo_imagen)) {
                    unlink($archivo_imagen);
                }
            }
            
            // Eliminar registros de imágenes
            $stmt = $conn->prepare("DELETE FROM producto_imagenes WHERE producto_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        } else {
            // Método original: eliminar imagen del campo imagen
            $q = $conn->query("SELECT imagen FROM productos WHERE id=$id");
            if ($fila = $q->fetch_assoc()) {
                if ($fila['imagen'] && file_exists("../assets/img/".$fila['imagen'])) {
                    unlink("../assets/img/".$fila['imagen']);
                }
            }
        }
        
        // Eliminar producto
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $conn->commit();
        $mensaje = "Producto eliminado correctamente.";
        
    } catch (Exception $e) {
        $conn->rollback();
        $mensaje = "Error al eliminar producto: " . $e->getMessage();
    }
    
    $conn->autocommit(true);
    header("Location: dashboard.php?msg=".urlencode($mensaje));
    exit;
}

// --- AGREGAR PRODUCTO ---
if (isset($_POST['agregar'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $precio_anterior = !empty($_POST['precio_anterior']) ? floatval($_POST['precio_anterior']) : null;
    $stock = intval($_POST['stock']);
    
    try {
        // Verificar si las columnas existen antes de usarlas
        $check_columns = $conn->query("SHOW COLUMNS FROM productos LIKE 'stock'");
        $has_stock = $check_columns->num_rows > 0;
        
        $check_precio_anterior = $conn->query("SHOW COLUMNS FROM productos LIKE 'precio_anterior'");
        $has_precio_anterior = $check_precio_anterior->num_rows > 0;
        
        // Crear las columnas si no existen
        if (!$has_stock) {
            $conn->query("ALTER TABLE productos ADD COLUMN stock INT DEFAULT 0 AFTER precio");
        }
        if (!$has_precio_anterior) {
            $conn->query("ALTER TABLE productos ADD COLUMN precio_anterior DECIMAL(10,2) DEFAULT NULL AFTER precio");
        }
        
        // Verificar si la tabla producto_imagenes existe
        $check_table = $conn->query("SHOW TABLES LIKE 'producto_imagenes'");
        $has_images_table = $check_table->num_rows > 0;
        
        // Crear tabla de imágenes si no existe
        if (!$has_images_table) {
            $conn->query("
                CREATE TABLE producto_imagenes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    producto_id INT NOT NULL,
                    imagen VARCHAR(255) NOT NULL,
                    orden INT DEFAULT 1,
                    es_principal BOOLEAN DEFAULT FALSE,
                    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
                )
            ");
        }
        
        $conn->autocommit(false);
        
        // Insertar producto con campos disponibles
        if ($has_stock && $has_precio_anterior) {
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, precio_anterior, stock) VALUES (?, ?, ?, ?, ?)");
            $precio_anterior_val = $precio_anterior; // Asegurar que no sea una expresión
            $stmt->bind_param("ssddi", $nombre, $descripcion, $precio, $precio_anterior_val, $stock);
        } elseif ($has_stock) {
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $stock);
        } else {
            // Fallback al método original
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $nombre, $descripcion, $precio);
        }
        
        $stmt->execute();
        $producto_id = $conn->insert_id;
        
        // Procesar imágenes múltiples si la tabla existe
        if ($has_images_table && !empty($_FILES['imagenes']['name'][0])) {
            $orden = 1;
            $es_primera = true;
            
            foreach ($_FILES['imagenes']['name'] as $key => $filename) {
                if (!empty($filename)) {
                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($extension, $allowed_extensions)) {
                        $nuevo_nombre = uniqid() . "_" . $filename;
                        $ruta_destino = "../assets/img/" . $nuevo_nombre;
                        
                        if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$key], $ruta_destino)) {
                            // Insertar imagen en la base de datos
                            $es_principal_val = $es_primera ? 1 : 0;
                            $stmt = $conn->prepare("INSERT INTO producto_imagenes (producto_id, imagen, orden, es_principal) VALUES (?, ?, ?, ?)");
                            $stmt->bind_param("isii", $producto_id, $nuevo_nombre, $orden, $es_principal_val);
                            $stmt->execute();
                            
                            $orden++;
                            $es_primera = false;
                        }
                    }
                }
            }
            $mensaje = "Producto agregado correctamente con " . ($orden - 1) . " imagen(es).";
        } else {
            // Manejo de imagen única (método original como fallback)
            if (!empty($_FILES['imagenes']['name'][0])) {
                $filename = $_FILES['imagenes']['name'][0];
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($extension, $allowed_extensions)) {
                    $nuevo_nombre = uniqid() . "_" . $filename;
                    $ruta_destino = "../assets/img/" . $nuevo_nombre;
                    
                    if (move_uploaded_file($_FILES['imagenes']['tmp_name'][0], $ruta_destino)) {
                        // Actualizar producto con imagen si el campo existe
                        $check_imagen = $conn->query("SHOW COLUMNS FROM productos LIKE 'imagen'");
                        if ($check_imagen->num_rows > 0) {
                            $stmt = $conn->prepare("UPDATE productos SET imagen = ? WHERE id = ?");
                            $stmt->bind_param("si", $nuevo_nombre, $producto_id);
                            $stmt->execute();
                        }
                    }
                }
            }
            $mensaje = "Producto agregado correctamente.";
        }
        
        $conn->commit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $mensaje = "Error al agregar producto: " . $e->getMessage();
    }
    
    $conn->autocommit(true);
    header("Location: dashboard.php?msg=".urlencode($mensaje));
    exit;
}

// --- DEBUG TEMPORAL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST recibido: " . print_r($_POST, true));
    // Mostrar debug en pantalla también
    if (isset($_POST['editar'])) {
        error_log("Intentando editar producto ID: " . $_POST['id']);
    }
}

// --- EDITAR PRODUCTO ---
if (isset($_POST['editar'])) {
    // Debug temporal
    file_put_contents('debug.log', "EDITAR PRODUCTO - ID: " . $_POST['id'] . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $precio_anterior = !empty($_POST['precio_anterior']) ? floatval($_POST['precio_anterior']) : null;
    $stock = intval($_POST['stock']);

    try {
        $conn->autocommit(false);
        
        // Actualizar datos del producto
        $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, precio_anterior=?, stock=? WHERE id=?");
        $precio_anterior_val = $precio_anterior; // Asegurar que no sea una expresión
        $stmt->bind_param("ssddii", $nombre, $descripcion, $precio, $precio_anterior_val, $stock, $id);
        $stmt->execute();

        // Procesar nuevas imágenes si se subieron
        if (!empty($_FILES['nuevas_imagenes']['name'][0])) {
            // Obtener el último orden
            $stmt = $conn->prepare("SELECT MAX(orden) as max_orden FROM producto_imagenes WHERE producto_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $max_orden = $result->fetch_assoc()['max_orden'] ?? 0;
            
            $orden = $max_orden + 1;
            
            foreach ($_FILES['nuevas_imagenes']['name'] as $key => $filename) {
                if (!empty($filename)) {
                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($extension, $allowed_extensions)) {
                        $nuevo_nombre = uniqid() . "_" . $filename;
                        $ruta_destino = "../assets/img/" . $nuevo_nombre;
                        
                        if (move_uploaded_file($_FILES['nuevas_imagenes']['tmp_name'][$key], $ruta_destino)) {
                            // Insertar imagen en la base de datos
                            $stmt = $conn->prepare("INSERT INTO producto_imagenes (producto_id, imagen, orden, es_principal) VALUES (?, ?, ?, 0)");
                            $stmt->bind_param("isi", $id, $nuevo_nombre, $orden);
                            $stmt->execute();
                            
                            $orden++;
                        }
                    }
                }
            }
        }
        
        $conn->commit();
        $mensaje = "Producto actualizado correctamente.";
        
        // Si es una petición AJAX, devolver respuesta JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => $mensaje]);
            exit;
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        $mensaje = "Error al actualizar producto: " . $e->getMessage();
        
        // Si es una petición AJAX, devolver error JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => $mensaje]);
            exit;
        }
    }
    
    $conn->autocommit(true);
    
    // Para peticiones normales, redirect
    header("Location: dashboard.php?msg=".urlencode($mensaje));
    exit;
}

// --- ELIMINAR IMAGEN INDIVIDUAL ---
if (isset($_POST['eliminar_imagen'])) {
    $imagen_id = intval($_POST['imagen_id']);
    $producto_id = intval($_POST['producto_id']);
    
    try {
        // Verificar si la tabla existe
        $check_table = $conn->query("SHOW TABLES LIKE 'producto_imagenes'");
        if ($check_table->num_rows > 0) {
            // Obtener información de la imagen
            $stmt = $conn->prepare("SELECT imagen FROM producto_imagenes WHERE id = ? AND producto_id = ?");
            $stmt->bind_param("ii", $imagen_id, $producto_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($img_data = $result->fetch_assoc()) {
                // Eliminar archivo físico
                $archivo_imagen = "../assets/img/" . $img_data['imagen'];
                if (file_exists($archivo_imagen)) {
                    unlink($archivo_imagen);
                }
                
                // Eliminar registro de base de datos
                $stmt = $conn->prepare("DELETE FROM producto_imagenes WHERE id = ?");
                $stmt->bind_param("i", $imagen_id);
                $stmt->execute();
                
                $mensaje = "Imagen eliminada correctamente.";
            } else {
                $mensaje = "Imagen no encontrada.";
            }
        } else {
            $mensaje = "Tabla de imágenes no existe.";
        }
        
    } catch (Exception $e) {
        $mensaje = "Error al eliminar imagen: " . $e->getMessage();
    }
    
    header("Location: dashboard.php?editar=$producto_id&msg=".urlencode($mensaje));
    exit;
}

// --- ESTABLECER IMAGEN PRINCIPAL ---
if (isset($_POST['set_principal'])) {
    $imagen_id = intval($_POST['imagen_id']);
    $producto_id = intval($_POST['producto_id']);
    
    try {
        // Verificar si la tabla existe
        $check_table = $conn->query("SHOW TABLES LIKE 'producto_imagenes'");
        if ($check_table->num_rows > 0) {
            // Quitar principal de todas las imágenes del producto
            $stmt = $conn->prepare("UPDATE producto_imagenes SET es_principal = 0 WHERE producto_id = ?");
            $stmt->bind_param("i", $producto_id);
            $stmt->execute();
            
            // Establecer nueva imagen principal
            $stmt = $conn->prepare("UPDATE producto_imagenes SET es_principal = 1 WHERE id = ? AND producto_id = ?");
            $stmt->bind_param("ii", $imagen_id, $producto_id);
            $stmt->execute();
            
            $mensaje = "Imagen principal actualizada.";
        } else {
            $mensaje = "Tabla de imágenes no existe.";
        }
        
    } catch (Exception $e) {
        $mensaje = "Error al actualizar imagen principal: " . $e->getMessage();
    }
    
    header("Location: dashboard.php?editar=$producto_id&msg=".urlencode($mensaje));
    exit;
}

// --- MENSAJE DESPUÉS DE ACCIÓN (GET) ---
if (isset($_GET['msg'])) {
    $mensaje = htmlspecialchars($_GET['msg']);
}

// --- PRODUCTO A EDITAR ---
$producto_editar = null;
$imagenes_producto = [];
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM productos WHERE id=$id");
    $producto_editar = $res->fetch_assoc();
    
    // Obtener imágenes del producto si la tabla existe
    if ($producto_editar) {
        $check_images_table = $conn->query("SHOW TABLES LIKE 'producto_imagenes'");
        if ($check_images_table->num_rows > 0) {
            $stmt = $conn->prepare("SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, orden ASC");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $imagenes_result = $stmt->get_result();
            $imagenes_producto = $imagenes_result->fetch_all(MYSQLI_ASSOC);
        }
    }
}

// --- OBTENER TODOS LOS PRODUCTOS CON STOCK E IMÁGENES ---
// Verificar qué columnas y tablas existen
$check_stock = $conn->query("SHOW COLUMNS FROM productos LIKE 'stock'");
$has_stock = $check_stock->num_rows > 0;

$check_precio_anterior = $conn->query("SHOW COLUMNS FROM productos LIKE 'precio_anterior'");
$has_precio_anterior = $check_precio_anterior->num_rows > 0;

$check_images_table = $conn->query("SHOW TABLES LIKE 'producto_imagenes'");
$has_images_table = $check_images_table->num_rows > 0;

// Construir consulta dinámica
$sql = "SELECT p.*";

if ($has_stock) {
    $sql .= ", COALESCE(p.stock, 0) as stock";
} else {
    $sql .= ", 0 as stock";
}

if ($has_precio_anterior) {
    $sql .= ", p.precio_anterior";
} else {
    $sql .= ", NULL as precio_anterior";
}

if ($has_images_table) {
    $sql .= ", (SELECT COUNT(*) FROM producto_imagenes pi WHERE pi.producto_id = p.id) as total_imagenes";
    $sql .= ", (SELECT pi.imagen FROM producto_imagenes pi WHERE pi.producto_id = p.id AND pi.es_principal = 1 LIMIT 1) as imagen_principal";
} else {
    $sql .= ", 0 as total_imagenes";
    $sql .= ", p.imagen as imagen_principal";
}

$sql .= " FROM productos p ORDER BY p.id DESC";

$productos = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin | Carpintería Esquivel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #e7cba0 0%, #b4845c 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .admin-header {
            background: linear-gradient(90deg, #7a5e3a 0%, #a97436 100%);
            color: #fffbe9;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            padding: 28px 0 18px 0;
            margin-bottom: 38px;
            box-shadow: 0 6px 24px #a9743621;
        }
        .admin-header h2 {
            font-family: 'Merriweather', serif;
            font-weight: bold;
            font-size: 2.1rem;
            letter-spacing: .5px;
            margin-bottom: 0;
        }
        .admin-header .btn {
            margin-left: 8px;
            margin-right: 0;
        }
        .admin-nav {
            margin-bottom: 28px;
        }
        .admin-section {
            background: #fffbeedb;
            border-radius: 20px;
            box-shadow: 0 4px 18px #a9743635;
            padding: 2rem 1.5rem 2rem 1.5rem;
            margin-bottom: 36px;
        }
        .admin-section h4 {
            font-family: 'Merriweather', serif;
            color: #a97436;
            font-size: 1.35rem;
            margin-bottom: 18px;
        }
        .form-label {
            color: #7a5e3a;
            font-weight: 600;
        }
        input.form-control, textarea.form-control {
            border-radius: 12px;
            border: 1.5px solid #e7cba0;
            background: #fffbe9;
        }
        .table thead th {
            background: linear-gradient(90deg, #a97436 70%, #7a5e3a 100%);
            color: #fffbe9;
            font-size: 1.09rem;
            border: none;
        }
        .table img {
            border-radius: 8px;
            box-shadow: 0 2px 10px #a9743632;
        }
        .btn-success, .btn-warning, .btn-danger, .btn-secondary, .btn-primary, .btn-info {
            border-radius: 18px;
        }
        .btn-info {
            background: linear-gradient(90deg,#4e8a4a 60%,#a97436 100%) !important;
            color: #fffbe9 !important;
            border: none;
        }
        .btn-info:hover {
            background: linear-gradient(90deg,#a97436 60%, #4e8a4a 100%) !important;
            color: #fffbe9 !important;
        }
        .alert {
            border-radius: 14px;
        }
        .admin-footer {
            background: linear-gradient(90deg, #7a5e3a 0%, #a97436 100%);
            color: #fffbe9;
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
            margin-top: 60px;
            padding: 32px 0 18px 0;
            box-shadow: 0 -4px 24px #a9743621;
            font-size: 1.04rem;
        }
        .admin-footer h5 {
            font-size: 1.25rem;
            font-family: 'Merriweather', serif;
            color: #fffbe9;
            font-weight: bold;
            margin-bottom: 16px;
        }
        .admin-footer ul {
            list-style: none;
            padding: 0;
            margin: 0 0 16px 0;
        }
        .admin-footer li {
            margin-bottom: 8px;
        }
        .admin-footer .footer-divider {
            border-top: 2px solid #fffbe979;
            margin: 20px 0 16px 0;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .admin-footer small {
            color: #fffbe9;
            opacity: .85;
            letter-spacing: 0.5px;
        }
        /* Estilos para stock */
        .badge.bg-danger { background-color: #dc3545 !important; }
        .badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
        .badge.bg-success { background-color: #198754 !important; }
        .badge.bg-info { background-color: #0dcaf0 !important; color: #000 !important; }
        
        /* Estilos para preview de imágenes */
        #imagePreview .border {
            transition: all 0.3s ease;
        }
        
        #imagePreview .border:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Estilos para imágenes actuales */
        .position-relative .btn {
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .position-relative:hover .btn {
            opacity: 1;
        }
        
        /* Mejorar tabla responsive */
        .table th {
            white-space: nowrap;
            font-size: 0.9rem;
        }
        
        .btn-group-vertical .btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Indicador de descuento */
        .descuento-info {
            display: block;
            margin-top: 0.25rem;
            font-weight: 600;
        }
        
        /* Validación de formularios */
        .was-validated .form-control:valid {
            border-color: #198754;
        }
        
        .was-validated .form-control:invalid {
            border-color: #dc3545;
        }
        
        @media (max-width: 600px) {
            .admin-header, .admin-footer { border-radius: 14px; font-size: 0.98rem;}
            .admin-section { padding: 1rem 0.3rem 1rem 0.3rem; }
            
            .table th, .table td {
                font-size: 0.8rem;
                padding: 0.5rem 0.25rem;
            }
            
            .btn-group-vertical .btn {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
            }
            
            #imagePreview .col-4 {
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header text-center shadow-sm">
        <div class="container d-flex flex-wrap flex-column flex-md-row align-items-center justify-content-between">
            <h2 class="mb-2 mb-md-0"><i class="fa fa-cogs"></i> Panel de Administración</h2>
            <div class="admin-nav d-flex flex-wrap gap-2">
                <a href="envios" class="btn btn-info mb-2"><i class="fa fa-truck"></i> Ver solicitudes de envío</a>
                <a href="logout.php" class="btn btn-danger mb-2"><i class="fa fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if($mensaje): ?>
            <div class="alert alert-info text-center"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <!-- FORMULARIO AGREGAR/EDITAR -->
        <section class="admin-section mb-5" id="formulario">
            <h4><?php echo $producto_editar ? '<i class="fa fa-edit"></i> Editar Producto' : '<i class="fa fa-plus"></i> Agregar Producto'; ?></h4>
            <form method="post" enctype="multipart/form-data">
                <?php if($producto_editar): ?>
                    <input type="hidden" name="id" value="<?php echo $producto_editar['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-tag me-1"></i> Nombre del Producto:</label>
                            <input type="text" name="nombre" class="form-control" required 
                                   value="<?php echo $producto_editar ? htmlspecialchars($producto_editar['nombre']) : ''; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-boxes me-1"></i> Stock Disponible:</label>
                            <input type="number" name="stock" class="form-control" min="0" required 
                                   value="<?php echo $producto_editar ? ($producto_editar['stock'] ?? 0) : '10'; ?>">
                            <?php if($producto_editar): ?>
                                <div class="form-text">
                                    <?php 
                                    $stock_actual = $producto_editar['stock'] ?? 0;
                                    if ($stock_actual == 0): ?>
                                        <span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Sin stock</span>
                                    <?php elseif ($stock_actual <= 5): ?>
                                        <span class="text-warning"><i class="fa fa-exclamation-circle"></i> Stock bajo</span>
                                    <?php else: ?>
                                        <span class="text-success"><i class="fa fa-check-circle"></i> Stock disponible</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-align-left me-1"></i> Descripción:</label>
                    <textarea name="descripcion" class="form-control" rows="3" required><?php echo $producto_editar ? htmlspecialchars($producto_editar['descripcion']) : ''; ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-dollar-sign me-1"></i> Precio Actual:</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="precio" class="form-control" required 
                                       value="<?php echo $producto_editar ? $producto_editar['precio'] : ''; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-percentage me-1"></i> Precio Anterior (Opcional):</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="precio_anterior" class="form-control" 
                                       value="<?php echo $producto_editar ? ($producto_editar['precio_anterior'] ?? '') : ''; ?>">
                            </div>
                            <small class="text-muted">Para mostrar descuentos</small>
                        </div>
                    </div>
                </div>
                
                <?php if($producto_editar && !empty($imagenes_producto)): ?>
                <!-- Imágenes actuales -->
                <div class="mb-4">
                    <label class="form-label"><i class="fa fa-images me-1"></i> Imágenes Actuales:</label>
                    <div class="row g-3">
                        <?php foreach ($imagenes_producto as $imagen): ?>
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="position-relative border rounded p-2">
                                <img src="../assets/img/<?php echo htmlspecialchars($imagen['imagen']); ?>" 
                                     alt="Imagen del producto" 
                                     class="img-fluid rounded" style="height: 120px; width: 100%; object-fit: cover;">
                                
                                <div class="position-absolute top-0 end-0 p-1">
                                    <?php if (!$imagen['es_principal']): ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="imagen_id" value="<?php echo $imagen['id']; ?>">
                                        <input type="hidden" name="producto_id" value="<?php echo $producto_editar['id']; ?>">
                                        <button type="submit" name="set_principal" class="btn btn-success btn-sm" title="Establecer como principal">
                                            <i class="fa fa-star"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <form method="post" style="display: inline;" onsubmit="return confirm('¿Eliminar esta imagen?')">
                                        <input type="hidden" name="imagen_id" value="<?php echo $imagen['id']; ?>">
                                        <input type="hidden" name="producto_id" value="<?php echo $producto_editar['id']; ?>">
                                        <button type="submit" name="eliminar_imagen" class="btn btn-danger btn-sm" title="Eliminar imagen">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                <?php if ($imagen['es_principal']): ?>
                                <div class="position-absolute bottom-0 start-0 p-1">
                                    <span class="badge bg-success">Principal</span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="position-absolute bottom-0 end-0 p-1">
                                    <span class="badge bg-info">Orden: <?php echo $imagen['orden']; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-<?php echo $producto_editar ? 'plus-circle' : 'images'; ?> me-1"></i> 
                        <?php echo $producto_editar ? 'Agregar Nuevas Imágenes:' : 'Imágenes del Producto:'; ?>
                    </label>
                    <input type="file" name="<?php echo $producto_editar ? 'nuevas_imagenes' : 'imagenes'; ?>[]" 
                           class="form-control" multiple accept="image/*" 
                           <?php echo !$producto_editar ? 'required' : ''; ?>>
                    <div class="form-text">
                        <i class="fa fa-info-circle me-1"></i>
                        Puedes seleccionar múltiples imágenes. <?php echo !$producto_editar ? 'La primera será la imagen principal.' : ''; ?>
                        Formatos permitidos: JPG, PNG, GIF, WebP
                    </div>
                    
                    <!-- Preview de imágenes -->
                    <div id="imagePreview" class="mt-3 row g-2" style="display: none;">
                        <!-- Las previews se mostrarán aquí -->
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <?php if($producto_editar): ?>
                        <button type="button" class="btn btn-secondary btn-lg" id="editBtn" onclick="guardarCambios()">
                            <i class="fa fa-save"></i> Sin cambios
                        </button>
                    <?php else: ?>
                        <button type="submit" name="agregar" value="1" class="btn btn-success btn-lg">
                            <i class="fa fa-check"></i> Agregar Producto
                        </button>
                    <?php endif; ?>
                    <?php if($producto_editar): ?>
                        <a href="dashboard.php" class="btn btn-secondary btn-lg">
                            <i class="fa fa-times"></i> Cancelar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <!-- LISTADO DE PRODUCTOS -->
        <section class="admin-section">
            <h4><i class="fa fa-cube"></i> Productos Registrados</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Imágenes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($productos->num_rows > 0): ?>
                        <?php while ($row = $productos->fetch_assoc()): 
                            $img_src = !empty($row['imagen_principal']) && file_exists('../assets/img/'.$row['imagen_principal']) 
                                       ? '../assets/img/'.htmlspecialchars($row['imagen_principal']) 
                                       : '../assets/img/noimg.png';
                        ?>
                            <tr>
                                <td class="fw-bold"><?php echo $row['id']; ?></td>
                                <td>
                                    <img src="<?php echo $img_src; ?>" 
                                         alt="<?php echo htmlspecialchars($row['nombre']); ?>" 
                                         class="img-thumbnail"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <h6 class="mb-1 text-primary"><?php echo htmlspecialchars($row['nombre']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars(substr($row['descripcion'], 0, 50)) . '...'; ?></small>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">$<?php echo number_format($row['precio'], 2); ?></span>
                                    <?php if (!empty($row['precio_anterior']) && $row['precio_anterior'] > $row['precio']): ?>
                                        <br><small class="text-decoration-line-through text-danger">$<?php echo number_format($row['precio_anterior'], 2); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $stock = $row['stock'] ?? 0;
                                    $stock_class = $stock == 0 ? 'bg-danger' : ($stock <= 5 ? 'bg-warning' : 'bg-success');
                                    $stock_text = $stock == 0 ? 'Agotado' : ($stock <= 5 ? 'Bajo' : 'Disponible');
                                    ?>
                                    <span class="badge <?php echo $stock_class; ?>">
                                        <i class="fa fa-boxes me-1"></i><?php echo $stock; ?> - <?php echo $stock_text; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="fa fa-images me-1"></i><?php echo $row['total_imagenes']; ?> foto(s)
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group-vertical" role="group">
                                        <a href="dashboard.php?editar=<?php echo $row['id']; ?>" 
                                           class="btn btn-warning btn-sm mb-1" title="Editar">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                        <a href="dashboard.php?eliminar=<?php echo $row['id']; ?>" 
                                           class="btn btn-danger btn-sm" title="Eliminar" 
                                           onclick="return confirm('¿Eliminar este producto y todas sus imágenes?');">
                                            <i class="fa fa-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fa fa-box-open fa-3x mb-3"></i><br>
                                <h5>No hay productos registrados</h5>
                                <p>Comienza agregando tu primer producto con el formulario de arriba</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
<script>
// Control de cambios en formulario de edición
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[method="post"]');
    const submitBtn = document.getElementById('editBtn');
    
    if (form && submitBtn) {
        // Valores originales
        const originalValues = {};
        const inputs = form.querySelectorAll('input, textarea, select');
        
        // Guardar valores originales
        inputs.forEach(input => {
            if (input.type !== 'file' && input.type !== 'hidden') {
                originalValues[input.name] = input.value;
            }
        });
        
        // Deshabilitar botón inicialmente
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        submitBtn.innerHTML = '<i class="fa fa-save"></i> Sin cambios';
        
        // Detectar cambios
        function checkChanges() {
            let hasChanges = false;
            
            inputs.forEach(input => {
                if (input.type !== 'file' && input.type !== 'hidden') {
                    if (input.value !== originalValues[input.name]) {
                        hasChanges = true;
                    }
                }
            });
            
            // Verificar si hay archivos seleccionados
            const fileInputs = form.querySelectorAll('input[type="file"]');
            fileInputs.forEach(fileInput => {
                if (fileInput.files.length > 0) {
                    hasChanges = true;
                }
            });
            
            // Actualizar botón
            if (hasChanges) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.innerHTML = '<i class="fa fa-save"></i> Guardar Cambios';
                submitBtn.className = 'btn btn-warning btn-lg';
            } else {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                submitBtn.innerHTML = '<i class="fa fa-save"></i> Sin cambios';
                submitBtn.className = 'btn btn-secondary btn-lg';
            }
        }
        
        // Agregar listeners
        inputs.forEach(input => {
            input.addEventListener('input', checkChanges);
            input.addEventListener('change', checkChanges);
        });
        
        // Listener especial para archivos
        const fileInputs = form.querySelectorAll('input[type="file"]');
        fileInputs.forEach(fileInput => {
            fileInput.addEventListener('change', checkChanges);
        });
        
    }
});

// Función para guardar cambios con AJAX
function guardarCambios() {
    const form = document.querySelector('form[method="post"]');
    const submitBtn = document.getElementById('editBtn');
    
    if (submitBtn.disabled) {
        alert('No hay cambios para guardar');
        return;
    }
    
    // Mostrar loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
    
    // Crear FormData
    const formData = new FormData(form);
    formData.append('editar', '1');
    
    // Enviar con fetch
    fetch('dashboard.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        
        if (data.success) {
            // Mostrar modal de éxito
            document.getElementById('modalMessage').textContent = data.message;
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        } else {
            // Mostrar modal de error
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            document.getElementById('errorMessage').textContent = data.message || 'Error al procesar la solicitud';
            errorModal.show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Mostrar modal de error
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        document.getElementById('errorMessage').textContent = 'Error de conexión';
        errorModal.show();
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa fa-save"></i> Guardar Cambios';
    });
}

// Preview de imágenes
document.addEventListener('change', function(e) {
    if (e.target.type === 'file' && e.target.accept === 'image/*') {
        const files = e.target.files;
        const previewContainer = document.getElementById('imagePreview');
        
        if (!previewContainer) return;
        
        // Limpiar previews anteriores
        previewContainer.innerHTML = '';
        
        if (files.length > 0) {
            previewContainer.style.display = 'block';
            
            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-2 col-sm-3 col-4';
                        
                        col.innerHTML = `
                            <div class="border rounded p-2 position-relative">
                                <img src="${e.target.result}" alt="Preview ${index + 1}" 
                                     class="img-fluid rounded" style="height: 80px; width: 100%; object-fit: cover;">
                                ${index === 0 ? '<span class="badge bg-success position-absolute top-0 start-0 m-1">Principal</span>' : ''}
                                <small class="text-muted d-block text-center mt-1">Imagen ${index + 1}</small>
                            </div>
                        `;
                        
                        previewContainer.appendChild(col);
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        } else {
            previewContainer.style.display = 'none';
        }
    }
});

// Calcular descuento automáticamente
document.addEventListener('input', function(e) {
    if (e.target.name === 'precio_anterior') {
        const precioAnterior = parseFloat(e.target.value);
        const precioActualInput = document.querySelector('input[name="precio"]');
        const precioActual = parseFloat(precioActualInput.value);
        
        if (precioAnterior && precioActual && precioAnterior > precioActual) {
            const descuento = ((precioAnterior - precioActual) / precioAnterior * 100).toFixed(0);
            e.target.title = `Descuento del ${descuento}%`;
            
            // Mostrar el descuento visualmente
            let descuentoSpan = e.target.parentNode.parentNode.querySelector('.descuento-info');
            if (!descuentoSpan) {
                descuentoSpan = document.createElement('small');
                descuentoSpan.className = 'text-success descuento-info';
                e.target.parentNode.parentNode.appendChild(descuentoSpan);
            }
            descuentoSpan.innerHTML = `<i class="fa fa-percentage"></i> Descuento del ${descuento}%`;
        } else {
            const descuentoSpan = e.target.parentNode.parentNode.querySelector('.descuento-info');
            if (descuentoSpan) {
                descuentoSpan.remove();
            }
        }
    }
});

// Actualizar indicador de stock en tiempo real
document.addEventListener('input', function(e) {
    if (e.target.name === 'stock') {
        const stock = parseInt(e.target.value) || 0;
        const formText = e.target.parentNode.querySelector('.form-text');
        
        if (formText) {
            let stockClass, stockIcon, stockText;
            
            if (stock === 0) {
                stockClass = 'text-danger';
                stockIcon = 'fa-exclamation-triangle';
                stockText = 'Sin stock';
            } else if (stock <= 5) {
                stockClass = 'text-warning';
                stockIcon = 'fa-exclamation-circle';
                stockText = 'Stock bajo';
            } else {
                stockClass = 'text-success';
                stockIcon = 'fa-check-circle';
                stockText = 'Stock disponible';
            }
            
            formText.innerHTML = `<span class="${stockClass}"><i class="fa ${stockIcon}"></i> ${stockText}</span>`;
        }
    }
});

// Confirmación mejorada para eliminar
function confirmarEliminacion(productName) {
    return confirm(`¿Estás seguro de que quieres eliminar "${productName}" y todas sus imágenes?\n\nEsta acción no se puede deshacer.`);
}

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
    <!-- Footer de Administración y Políticas -->
    <footer class="admin-footer mt-5">
        <div class="container">
            <h5><i class="fa fa-shield-alt"></i> Zona de Administración</h5>
            <ul>
                <li><b>Usuarios autorizados:</b> Solo personal administrativo con credenciales puede acceder.</li>
                <li><b>Gestión de productos:</b> Los cambios realizados aquí impactan directamente en la tienda en línea.</li>
                <li><b>Privacidad:</b> Está estrictamente prohibido compartir información sensible o datos privados de clientes, productos o administración.</li>
                <li><b>Políticas y sanciones:</b> El uso indebido de este panel, así como la filtración o divulgación de datos confidenciales, será motivo de sanción, suspensión o acciones legales conforme a la política interna y leyes aplicables.</li>
            </ul>
            <div class="footer-divider"></div>
            <small>
                &copy; <?php echo date("Y"); ?> Carpintería Esquivel - Panel de Administración.<br>
                Para más información sobre políticas contacta a: realijaihm@gmail.com
            </small>
        </div>
    </footer>
    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-check-circle me-2"></i>¡Éxito!
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="success-icon mb-3">
                        <i class="fa fa-check-circle fa-3x text-success"></i>
                    </div>
                    <h5 id="modalMessage">Producto actualizado correctamente</h5>
                    <p class="text-muted">Los cambios se han guardado exitosamente</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="location.reload()">
                        <i class="fa fa-check me-1"></i>Continuar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de error -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de error -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-exclamation-triangle me-2"></i>Error
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="error-icon mb-3">
                        <i class="fa fa-exclamation-triangle fa-3x text-danger"></i>
                    </div>
                    <h5 id="errorMessage">Error al guardar</h5>
                    <p class="text-muted">Por favor, inténtalo de nuevo</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>