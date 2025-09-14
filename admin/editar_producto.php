<?php
include '../includes/auth.php';
include '../includes/db.php';

$id = intval($_GET['id']);
$mensaje = '';
$tipo_mensaje = 'success';

// Obtener datos del producto
$stmt = $conexion->prepare("SELECT * FROM productos WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

if (!$producto) {
    header("Location: productos.php");
    exit;
}

// Obtener imágenes del producto
$stmt = $conexion->prepare("SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, orden ASC");
$stmt->bind_param("i", $id);
$stmt->execute();
$imagenes_result = $stmt->get_result();
$imagenes = $imagenes_result->fetch_all(MYSQLI_ASSOC);

// Procesar acciones
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete_image':
                $imagen_id = intval($_POST['imagen_id']);
                
                try {
                    // Obtener información de la imagen
                    $stmt = $conexion->prepare("SELECT imagen FROM producto_imagenes WHERE id = ? AND producto_id = ?");
                    $stmt->bind_param("ii", $imagen_id, $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($img_data = $result->fetch_assoc()) {
                        // Eliminar archivo físico
                        $archivo_imagen = "../assets/img/" . $img_data['imagen'];
                        if (file_exists($archivo_imagen)) {
                            unlink($archivo_imagen);
                        }
                        
                        // Eliminar registro de base de datos
                        $stmt = $conexion->prepare("DELETE FROM producto_imagenes WHERE id = ?");
                        $stmt->bind_param("i", $imagen_id);
                        $stmt->execute();
                        
                        $mensaje = "Imagen eliminada correctamente.";
                        
                        // Recargar imágenes
                        $stmt = $conexion->prepare("SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, orden ASC");
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $imagenes_result = $stmt->get_result();
                        $imagenes = $imagenes_result->fetch_all(MYSQLI_ASSOC);
                        
                    } else {
                        $mensaje = "Imagen no encontrada.";
                        $tipo_mensaje = 'warning';
                    }
                    
                } catch (Exception $e) {
                    $mensaje = "Error al eliminar imagen: " . $e->getMessage();
                    $tipo_mensaje = 'danger';
                }
                break;
                
            case 'set_principal':
                $imagen_id = intval($_POST['imagen_id']);
                
                try {
                    // Quitar principal de todas las imágenes del producto
                    $stmt = $conexion->prepare("UPDATE producto_imagenes SET es_principal = 0 WHERE producto_id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    
                    // Establecer nueva imagen principal
                    $stmt = $conexion->prepare("UPDATE producto_imagenes SET es_principal = 1 WHERE id = ? AND producto_id = ?");
                    $stmt->bind_param("ii", $imagen_id, $id);
                    $stmt->execute();
                    
                    $mensaje = "Imagen principal actualizada.";
                    
                    // Recargar imágenes
                    $stmt = $conexion->prepare("SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, orden ASC");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $imagenes_result = $stmt->get_result();
                    $imagenes = $imagenes_result->fetch_all(MYSQLI_ASSOC);
                    
                } catch (Exception $e) {
                    $mensaje = "Error al actualizar imagen principal: " . $e->getMessage();
                    $tipo_mensaje = 'danger';
                }
                break;
        }
    } else {
        // Actualizar producto
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $precio_anterior = !empty($_POST['precio_anterior']) ? floatval($_POST['precio_anterior']) : null;
        $stock = intval($_POST['stock']);

        try {
            $conexion->autocommit(false);
            
            // Actualizar datos del producto
            $stmt = $conexion->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, precio_anterior=?, stock=? WHERE id=?");
            $stmt->bind_param("ssddii", $nombre, $descripcion, $precio, $precio_anterior, $stock, $id);
            $stmt->execute();

            // Procesar nuevas imágenes si se subieron
            if (!empty($_FILES['nuevas_imagenes']['name'][0])) {
                // Obtener el último orden
                $stmt = $conexion->prepare("SELECT MAX(orden) as max_orden FROM producto_imagenes WHERE producto_id = ?");
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
                                $stmt = $conexion->prepare("INSERT INTO producto_imagenes (producto_id, imagen, orden, es_principal) VALUES (?, ?, ?, 0)");
                                $stmt->bind_param("isi", $id, $nuevo_nombre, $orden);
                                $stmt->execute();
                                
                                $orden++;
                            }
                        }
                    }
                }
            }
            
            $conexion->commit();
            $mensaje = "Producto actualizado correctamente.";
            
            // Actualizar datos en pantalla
            $producto = [
                'id' => $id,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'precio_anterior' => $precio_anterior,
                'stock' => $stock
            ];
            
            // Recargar imágenes
            $stmt = $conexion->prepare("SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, orden ASC");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $imagenes_result = $stmt->get_result();
            $imagenes = $imagenes_result->fetch_all(MYSQLI_ASSOC);
            
        } catch (Exception $e) {
            $conexion->rollback();
            $mensaje = "Error al actualizar producto: " . $e->getMessage();
            $tipo_mensaje = 'danger';
        }
        
        $conexion->autocommit(true);
    }
}
include '../includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-madera text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0"><i class="fa fa-edit me-2"></i>Editar Producto</h2>
                        <a href="productos.php" class="btn btn-light">
                            <i class="fa fa-arrow-left me-1"></i>Volver a Productos
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <?php if($mensaje): ?>
                        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                            <i class="fa fa-info-circle me-2"></i><?php echo $mensaje; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label fw-bold">
                                        <i class="fa fa-tag me-1"></i>Nombre del Producto *
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label fw-bold">
                                        <i class="fa fa-boxes me-1"></i>Stock Disponible *
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg" 
                                           id="stock" 
                                           name="stock" 
                                           min="0" 
                                           value="<?php echo $producto['stock'] ?? 0; ?>"
                                           required>
                                    <div class="form-text">
                                        <span class="stock-status">
                                            <?php 
                                            $stock_actual = $producto['stock'] ?? 0;
                                            if ($stock_actual == 0): ?>
                                                <span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Sin stock</span>
                                            <?php elseif ($stock_actual <= 5): ?>
                                                <span class="text-warning"><i class="fa fa-exclamation-circle"></i> Stock bajo</span>
                                            <?php else: ?>
                                                <span class="text-success"><i class="fa fa-check-circle"></i> Stock disponible</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold">
                                <i class="fa fa-align-left me-1"></i>Descripción *
                            </label>
                            <textarea class="form-control" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="4" 
                                      required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio" class="form-label fw-bold">
                                        <i class="fa fa-dollar-sign me-1"></i>Precio Actual *
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control form-control-lg" 
                                               id="precio" 
                                               name="precio" 
                                               step="0.01" 
                                               min="0"
                                               value="<?php echo $producto['precio']; ?>"
                                               required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio_anterior" class="form-label fw-bold">
                                        <i class="fa fa-percentage me-1"></i>Precio Anterior (Opcional)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control form-control-lg" 
                                               id="precio_anterior" 
                                               name="precio_anterior" 
                                               step="0.01" 
                                               min="0"
                                               value="<?php echo $producto['precio_anterior'] ?? ''; ?>">
                                    </div>
                                    <small class="text-muted">Para mostrar descuentos</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Imágenes actuales -->
                        <?php if (!empty($imagenes)): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fa fa-images me-1"></i>Imágenes Actuales
                            </label>
                            <div class="row g-3">
                                <?php foreach ($imagenes as $imagen): ?>
                                <div class="col-md-3 col-sm-4 col-6">
                                    <div class="image-item position-relative">
                                        <img src="../assets/img/<?php echo htmlspecialchars($imagen['imagen']); ?>" 
                                             alt="Imagen del producto" 
                                             class="img-fluid rounded">
                                        
                                        <div class="image-actions position-absolute top-0 end-0 p-2">
                                            <?php if (!$imagen['es_principal']): ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="action" value="set_principal">
                                                <input type="hidden" name="imagen_id" value="<?php echo $imagen['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm" title="Establecer como principal">
                                                    <i class="fa fa-star"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            
                                            <form method="post" style="display: inline;" onsubmit="return confirm('¿Eliminar esta imagen?')">
                                                <input type="hidden" name="action" value="delete_image">
                                                <input type="hidden" name="imagen_id" value="<?php echo $imagen['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar imagen">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <?php if ($imagen['es_principal']): ?>
                                        <div class="position-absolute bottom-0 start-0 p-2">
                                            <span class="badge bg-success">Principal</span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="position-absolute bottom-0 end-0 p-2">
                                            <span class="badge bg-info">Orden: <?php echo $imagen['orden']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Agregar nuevas imágenes -->
                        <div class="mb-4">
                            <label for="nuevas_imagenes" class="form-label fw-bold">
                                <i class="fa fa-plus-circle me-1"></i>Agregar Nuevas Imágenes
                            </label>
                            <input type="file" 
                                   class="form-control form-control-lg" 
                                   id="nuevas_imagenes" 
                                   name="nuevas_imagenes[]" 
                                   multiple 
                                   accept="image/*">
                            <div class="form-text">
                                <i class="fa fa-info-circle me-1"></i>
                                Puedes seleccionar múltiples imágenes. Formatos permitidos: JPG, PNG, GIF, WebP
                            </div>
                            
                            <!-- Preview de nuevas imágenes -->
                            <div id="newImagePreview" class="mt-3 row g-2" style="display: none;">
                                <!-- Las previews se mostrarán aquí -->
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="productos.php" class="btn btn-outline-secondary btn-lg me-md-2">
                                <i class="fa fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-madera btn-lg">
                                <i class="fa fa-save me-1"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
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
<?php include '../includes/footer.php'; ?>