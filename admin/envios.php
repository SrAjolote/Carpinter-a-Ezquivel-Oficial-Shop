<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login");
    exit;
}

$host = "localhost";
$user = "u182426195_carpinteria";
$pass = "2415691611+David";
$db   = "u182426195_carpinteria";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Error DB: " . $conn->connect_error);

// Borrar solicitud
if (isset($_GET['borrar'])) {
    $delid = intval($_GET['borrar']);
    $conn->query("DELETE FROM envios WHERE id=$delid");
    header("Location: envios?msg=Borrado");
    exit;
}

// Marcar como confirmado
if (isset($_GET['confirmar'])) {
    $id = intval($_GET['confirmar']);
    $conn->query("UPDATE envios SET estado='CONFIRMADO' WHERE id=$id");
    header("Location: envios?msg=Confirmado");
    exit;
}

// Marcar como reembolsado
if (isset($_GET['reembolsar'])) {
    $id = intval($_GET['reembolsar']);
    $conn->query("UPDATE envios SET estado='REEMBOLSADO' WHERE id=$id");
    header("Location: envios?msg=Reembolsado");
    exit;
}

// Marcar como completado
if (isset($_GET['completar'])) {
    $id = intval($_GET['completar']);
    $conn->query("UPDATE envios SET estado='COMPLETADO' WHERE id=$id");
    header("Location: envios?msg=Completado");
    exit;
}

$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : "";

$envios = $conn->query("SELECT * FROM envios ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Envíos | Admin Carpintería Esquivel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #fffbe9 0%, #e7cba0 100%);
            font-family: 'Poppins', Arial, sans-serif;
        }
        .envios-header {
            background: linear-gradient(90deg, #7a5e3a 0%, #a97436 100%);
            color: #fffbe9;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            padding: 24px 0 14px 0;
            margin-bottom: 38px;
            box-shadow: 0 6px 24px #a9743621;
            text-align: center;
        }
        .envios-header h2 {
            font-family: 'Merriweather', serif;
            font-weight: bold;
            font-size: 2rem;
            letter-spacing: .5px;
            margin-bottom: 0;
        }
        .admin-section {
            background: #fffbeedb;
            border-radius: 20px;
            box-shadow: 0 4px 18px #a9743635;
            padding: 2rem 1.5rem 2rem 1.5rem;
            margin-bottom: 36px;
        }
        .table thead th {
            background: linear-gradient(90deg, #a97436 70%, #7a5e3a 100%);
            color: #fffbe9;
            font-size: 1.09rem;
            border: none;
        }
        .btn {
            border-radius: 16px;
            font-weight: 500;
        }
        .estado-pendiente { background: #fff3cd; color: #a97436; border-radius: 10px; padding: 3px 11px; font-weight: bold;}
        .estado-confirmado { background: #c8e6c9; color: #256029; border-radius: 10px; padding: 3px 11px; font-weight: bold;}
        .estado-reembolsado { background: #fdecea; color: #d32f2f; border-radius: 10px; padding: 3px 11px; font-weight: bold;}
        .estado-completado { background: #b2ebf2; color: #027a7a; border-radius: 10px; padding: 3px 11px; font-weight: bold;}
        .btn-wa {
            background: linear-gradient(90deg,#25d366 70%,#128c7e 100%) !important;
            color: #fff !important;
            border: none;
        }
        .btn-wa:hover { background: #128c7e !important; color: #fff !important;}
        .fa-check-circle { color: #4e8a4a; }
        @media (max-width: 600px) {
            .envios-header { border-radius: 12px; font-size: 0.98rem;}
            .admin-section { padding: 1rem 0.3rem 1rem 0.3rem; }
        }
    </style>
</head>
<body>
    <div class="envios-header">
        <h2><i class="fa fa-truck"></i> Envios Recibidos</h2>
        <a href="dashboard" class="btn btn-secondary btn-sm mt-2"><i class="fa fa-arrow-left"></i> Panel admin</a>
    </div>
    <div class="container">
        <?php if($msg): ?>
            <div class="alert alert-success text-center"><?php echo $msg; ?></div>
        <?php endif; ?>
        <section class="admin-section mb-5">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>CP</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Referencias</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($envios->num_rows > 0): ?>
                        <?php while ($row = $envios->fetch_assoc()): 
                            // Mensaje para WhatsApp
                            $mensajeWA = rawurlencode(
                                "Hola {$row['nombre']}, soy de Carpintería Esquivel. ¿Podrías confirmar que tus datos de envío son correctos?\n\n".
                                "Nombre: {$row['nombre']}\n".
                                "Dirección: {$row['direccion']}\n".
                                "CP: {$row['cp']}\n".
                                "Teléfono: {$row['telefono']}\n".
                                ($row['correo'] ? "Correo: {$row['correo']}\n" : "").
                                "Referencias: {$row['referencias']}\n\n".
                                "Por favor responde *SI* si todo está correcto."
                            );
                            $waLink = "https://wa.me/52".preg_replace('/\D/', '', $row['telefono'])."?text={$mensajeWA}";
                        ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                                <td><?php echo htmlspecialchars($row['cp']); ?></td>
                                <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                <td><?php echo htmlspecialchars($row['referencias']); ?></td>
                                <td>
                                    <?php
                                    $estado = $row['estado'];
                                    if ($estado == "PENDIENTE") echo '<span class="estado-pendiente">PENDIENTE</span>';
                                    elseif ($estado == "CONFIRMADO") echo '<span class="estado-confirmado"><i class="fa fa-check-circle"></i> CONFIRMADO</span>';
                                    elseif ($estado == "REEMBOLSADO") echo '<span class="estado-reembolsado">REEMBOLSADO</span>';
                                    elseif ($estado == "COMPLETADO") echo '<span class="estado-completado">COMPLETADO</span>';
                                    ?>
                                </td>
                                <td style="min-width:160px;">
                                    <!-- WhatsApp -->
                                    <a target="_blank" title="Confirmar por WhatsApp" class="btn btn-wa btn-sm mb-1"
                                       href="<?php echo $waLink; ?>">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <!-- Confirmar -->
                                    <?php if ($estado == "PENDIENTE"): ?>
                                        <a href="envios.php?confirmar=<?php echo $row['id']; ?>"
                                           class="btn btn-success btn-sm mb-1" title="Marcar como confirmado">
                                            <i class="fa fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <!-- Reembolsar / Completar -->
                                    <?php if ($estado == "CONFIRMADO"): ?>
                                        <a href="envios.php?reembolsar=<?php echo $row['id']; ?>"
                                           class="btn btn-danger btn-sm mb-1" title="Marcar como reembolsado">
                                            <i class="fa fa-undo"></i> Rembolso
                                        </a>
                                        <a href="envios.php?completar=<?php echo $row['id']; ?>"
                                           class="btn btn-primary btn-sm mb-1" title="Marcar como completado">
                                            <i class="fa fa-check-double"></i> Completado
                                        </a>
                                    <?php endif; ?>
                                    <!-- Borrar -->
                                    <a href="envios.php?borrar=<?php echo $row['id']; ?>"
                                       class="btn btn-secondary btn-sm mb-1"
                                       onclick="return confirm('¿Borrar esta solicitud de envío?');"
                                       title="Borrar solicitud">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4"><i class="fa fa-inbox"></i> No hay registros de envío.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
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
</html>