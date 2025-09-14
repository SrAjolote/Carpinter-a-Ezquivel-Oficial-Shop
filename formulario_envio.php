<?php
session_start();
// --- CONEXIÓN A LA BASE DE DATOS ---
$host = "localhost";
$user = "u182426195_carpinteria";
$pass = "2415691611+David";
$db   = "u182426195_carpinteria";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Error DB: " . $conn->connect_error);

// Verificar si hay productos en el carrito
if (empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit;
}

// Obtener productos del carrito
$productos = [];
$total = 0;
$ids = implode(",", array_map('intval', array_keys($_SESSION['carrito'])));
if (!empty($ids)) {
    $sql = "SELECT * FROM productos WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $row['cantidad'] = $_SESSION['carrito'][$row['id']];
        $row['subtotal'] = $row['cantidad'] * $row['precio'];
        $productos[] = $row;
        $total += $row['subtotal'];
    }
}

// --- PROCESAR ENVÍO DIRECTO A WHATSAPP ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre        = trim($_POST['nombre']);
    $direccion     = trim($_POST['direccion']);
    $cp            = trim($_POST['cp']);
    $telefono      = trim($_POST['telefono']);
    $correo        = trim($_POST['correo']);
    $referencias   = trim($_POST['referencias']);

    // Guardar en base de datos
    $stmt = $conn->prepare("INSERT INTO envios (nombre, direccion, cp, telefono, correo, referencias, fecha) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssss", $nombre, $direccion, $cp, $telefono, $correo, $referencias);
    $stmt->execute();
    $id_pedido = $conn->insert_id;
    
    // Crear mensaje para WhatsApp
    $mensaje = "*NUEVO PEDIDO - CARPINTERIA ESQUIVEL*\n\n";
    $mensaje .= "*DATOS DEL CLIENTE:*\n";
    $mensaje .= "Nombre: " . $nombre . "\n";
    $mensaje .= "Telefono: " . $telefono . "\n";
    $mensaje .= "Correo: " . ($correo ?: 'No proporcionado') . "\n\n";
    
    $mensaje .= "*DATOS DE ENVIO:*\n";
    $mensaje .= "Direccion: " . $direccion . "\n";
    $mensaje .= "C.P.: " . $cp . "\n";
    $mensaje .= "Referencias: " . ($referencias ?: 'No proporcionadas') . "\n\n";
    
    $mensaje .= "*PRODUCTOS SOLICITADOS:*\n";
    foreach ($productos as $producto) {
        $mensaje .= "- " . $producto['nombre'] . "\n";
        $mensaje .= "  Cantidad: " . $producto['cantidad'] . " unidad(es)\n";
        $mensaje .= "  Precio unitario: $" . number_format($producto['precio'], 2) . " MXN\n";
        $mensaje .= "  Subtotal: $" . number_format($producto['subtotal'], 2) . " MXN\n\n";
    }
    
    $mensaje .= "*RESUMEN DEL PEDIDO:*\n";
    $mensaje .= "Total de productos: " . count($productos) . "\n";
    $mensaje .= "*TOTAL A PAGAR: $" . number_format($total, 2) . " MXN*\n";
    $mensaje .= "Envio incluido\n\n";
    
    $mensaje .= "*FORMA DE PAGO:* Transferencia bancaria\n";
    $mensaje .= "*Solicito informacion para realizar el pago*\n\n";
    
    $mensaje .= "Fecha del pedido: " . date('d/m/Y H:i') . "\n";
    $mensaje .= "ID Pedido: #" . str_pad($id_pedido, 4, '0', STR_PAD_LEFT);
    
    // URL de WhatsApp
    $numero_whatsapp = "7861009990";
    $mensaje_encoded = urlencode($mensaje);
    $url_whatsapp = "https://wa.me/" . $numero_whatsapp . "?text=" . $mensaje_encoded;
    
    // Limpiar carrito después de enviar
    $_SESSION['carrito'] = [];
    
    // Redirigir a WhatsApp
    header("Location: " . $url_whatsapp);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Pedido | Carpintería Esquivel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #a97436;
            --primary-light: #bf9158;
            --primary-dark: #8d5f2d;
            --secondary: #4e8a4a;
            --secondary-light: #66a362;
            --secondary-dark: #3d6e39;
            --bg-light: #fffbe9;
            --bg-medium: #f5eeda;
            --accent: #e7cba0;
            --whatsapp: #25d366;
        }
        
        body {
            background: linear-gradient(120deg, var(--bg-light) 0%, var(--accent) 100%);
            font-family: 'Poppins', Arial, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .main-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .content-section {
            background: rgba(255, 251, 233, 0.92);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(169, 116, 54, 0.15);
            padding: 2.5rem 2.2rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            animation: fadeIn 1s 0.2s both;
            border: 1px solid rgba(231, 203, 160, 0.3);
        }
        
        .section-title {
            text-align: center;
            font-family: 'Merriweather', serif;
            color: var(--primary);
            font-size: 2rem;
            margin-bottom: 1.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            position: relative;
            padding-bottom: 12px;
        }
        
        .section-title:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 2px;
        }
        
        .section-title i {
            margin-right: 12px;
            opacity: 0.9;
        }
        
        .form-label {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 0.95rem;
        }
        
        input.form-control, textarea.form-control, select.form-control {
            border-radius: 14px;
            border: 2px solid var(--accent);
            background: rgba(255, 251, 233, 0.8);
            font-size: 1.05rem;
            margin-bottom: 18px;
            padding: 12px 16px;
            transition: all 0.2s ease;
            color: #5a4728;
        }
        
        input.form-control:focus, textarea.form-control:focus, select.form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(169, 116, 54, 0.15);
            background: #fff;
        }
        
        .btn-whatsapp {
            background: linear-gradient(90deg, var(--whatsapp) 0%, #20ba5a 100%);
            color: white;
            border: none;
            font-size: 1.2rem;
            padding: 16px 32px;
            border-radius: 50px;
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.3);
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-whatsapp:hover {
            background: linear-gradient(90deg, #20ba5a 0%, var(--whatsapp) 100%);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(37, 211, 102, 0.4);
            color: white;
        }
        
        .btn-secondary {
            background: linear-gradient(90deg, #6c757d 0%, #5a6268 100%);
            color: white;
            border: none;
            font-size: 1rem;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(90deg, #5a6268 0%, #6c757d 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .producto-card {
            background: white;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid var(--accent);
            display: flex;
            align-items: center;
        }
        
        .producto-img {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            margin-right: 16px;
            object-fit: cover;
            background: var(--bg-medium);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .producto-detalle {
            flex: 1;
        }
        
        .producto-nombre {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 1rem;
            margin-bottom: 4px;
        }
        
        .producto-descripcion {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 6px;
        }
        
        .producto-cantidad {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .producto-precio {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            text-align: right;
        }
        
        .info-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .badge-envio {
            background: linear-gradient(90deg, var(--secondary) 0%, var(--secondary-light) 100%);
            color: white;
            font-weight: 600;
            border-radius: 25px;
            padding: 8px 18px;
            font-size: 0.9rem;
            box-shadow: 0 3px 10px rgba(78, 138, 74, 0.2);
        }
        
        .badge-pago {
            background: linear-gradient(90deg, #ffc107 0%, #ff8f00 100%);
            color: #5a4728;
            font-weight: 600;
            border-radius: 25px;
            padding: 8px 18px;
            font-size: 0.9rem;
            box-shadow: 0 3px 10px rgba(255, 193, 7, 0.2);
        }
        
        .resumen-total {
            background: linear-gradient(90deg, var(--bg-medium) 0%, #f0e6d0 100%);
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            border: 2px solid var(--accent);
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed rgba(169, 116, 54, 0.3);
        }
        
        .total-row:last-child {
            border-bottom: none;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
            margin-top: 10px;
            padding-top: 15px;
        }
        
        .whatsapp-info {
            background: rgba(37, 211, 102, 0.1);
            border: 2px solid var(--whatsapp);
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .whatsapp-info .icon {
            font-size: 3rem;
            color: var(--whatsapp);
            margin-bottom: 15px;
        }
        
        .whatsapp-info h4 {
            color: var(--primary-dark);
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .whatsapp-info p {
            color: #5a4728;
            margin-bottom: 0;
            font-size: 1rem;
            line-height: 1.5;
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .content-section { 
                padding: 1.5rem 1.2rem;
                border-radius: 20px; 
            }
            .section-title { 
                font-size: 1.6rem;
                margin-bottom: 1.4rem;
            }
            .producto-card {
                flex-direction: column;
                text-align: center;
            }
            .producto-img {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
        
        @media (max-width: 576px) {
            body { padding: 10px; }
            .content-section { 
                padding: 1.2rem 0.8rem;
                border-radius: 16px; 
            }
            .section-title { 
                font-size: 1.4rem;
                margin-bottom: 1.2rem;
            }
            .btn-whatsapp {
                font-size: 1rem;
                padding: 14px 24px;
            }
            .info-badges {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- RESUMEN DEL CARRITO -->
        <div class="content-section">
            <h1 class="section-title"><i class="fa fa-shopping-cart"></i> Resumen de tu Pedido</h1>
            
            <div class="info-badges">
                <span class="badge-envio">
                    <i class="fa fa-truck me-1"></i> Envío Gratis
                </span>
                <span class="badge-pago">
                    <i class="fa fa-university me-1"></i> Pago por Transferencia
                </span>
            </div>
            
            <!-- Lista de productos -->
            <?php foreach ($productos as $producto): ?>
            <div class="producto-card">
                <div class="producto-img">
                    <?php if (!empty($producto['imagen']) && file_exists("assets/img/" . $producto['imagen'])): ?>
                        <img src="assets/img/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                    <?php else: ?>
                        <i class="fa fa-chair fa-2x" style="color: var(--primary);"></i>
                    <?php endif; ?>
                </div>
                <div class="producto-detalle">
                    <div class="producto-nombre"><?php echo htmlspecialchars($producto['nombre']); ?></div>
                    <?php if (!empty($producto['descripcion'])): ?>
                        <div class="producto-descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></div>
                    <?php endif; ?>
                    <div class="producto-cantidad">Cantidad: <?php echo $producto['cantidad']; ?></div>
                </div>
                <div class="producto-precio">
                    <div>$<?php echo number_format($producto['precio'], 2); ?> c/u</div>
                    <div style="font-size: 1.2rem; color: var(--secondary);">$<?php echo number_format($producto['subtotal'], 2); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Resumen total -->
            <div class="resumen-total">
                <div class="total-row">
                    <div>Subtotal (<?php echo count($productos); ?> producto<?php echo count($productos) > 1 ? 's' : ''; ?>):</div>
                    <div>$<?php echo number_format($total, 2); ?> MXN</div>
                </div>
                <div class="total-row">
                    <div>Envío:</div>
                    <div><span style="color: var(--secondary); font-weight: 600;">Gratis</span></div>
                </div>
                <div class="total-row">
                    <div>Total a pagar:</div>
                    <div>$<?php echo number_format($total, 2); ?> MXN</div>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE DATOS -->
        <div class="content-section">
            <h2 class="section-title"><i class="fa fa-user"></i> Datos de Contacto y Envío</h2>
            
            <form method="post" autocomplete="off">
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label" for="nombre"><i class="fa fa-user me-1"></i> Nombre Completo *</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="120" placeholder="Tu nombre y apellidos">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label" for="telefono"><i class="fa fa-phone me-1"></i> Teléfono *</label>
                        <input type="text" id="telefono" name="telefono" class="form-control" required maxlength="20" placeholder="Ej: 5512345678">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="correo"><i class="fa fa-envelope me-1"></i> Correo (opcional)</label>
                        <input type="email" id="correo" name="correo" class="form-control" maxlength="120" placeholder="ejemplo@correo.com">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label" for="direccion"><i class="fa fa-map-marker-alt me-1"></i> Dirección Completa de Envío *</label>
                        <textarea id="direccion" name="direccion" class="form-control" required maxlength="255" placeholder="Calle, número, colonia, municipio y estado" rows="3"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label" for="cp"><i class="fa fa-mailbox me-1"></i> Código Postal *</label>
                        <input type="text" id="cp" name="cp" class="form-control" required maxlength="10" placeholder="Ej: 54730">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="referencias"><i class="fa fa-home me-1"></i> Referencias de tu casa</label>
                        <input type="text" id="referencias" name="referencias" class="form-control" maxlength="255" placeholder="Color, portón, referencias cercanas">
                    </div>
                </div>

                <div class="whatsapp-info">
                    <div class="icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h4>¿Cómo funciona?</h4>
                    <p>Al enviar el formulario serás redirigido a WhatsApp con todos tus datos y la información de los productos. Ahí recibirás los datos bancarios para realizar tu transferencia y coordinaremos la entrega.</p>
                </div>

                <div class="d-flex gap-3 flex-wrap justify-content-between">
                    <a href="carrito.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left me-2"></i> Volver al carrito
                    </a>
                    <button type="submit" class="btn btn-whatsapp">
                        <i class="fab fa-whatsapp me-2"></i> Enviar Pedido por WhatsApp
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

    // Validación básica del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        const telefono = document.getElementById('telefono').value.trim();
        const direccion = document.getElementById('direccion').value.trim();
        const cp = document.getElementById('cp').value.trim();
        
        if (!nombre || !telefono || !direccion || !cp) {
            e.preventDefault();
            alert('Por favor, completa todos los campos obligatorios marcados con *');
            return false;
        }
        
        // Mostrar mensaje de carga
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Redirigiendo a WhatsApp...';
        btn.disabled = true;
    });
    </script>
</body>
</html>