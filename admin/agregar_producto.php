 <?php
include '../includes/auth.php';
include '../includes/db.php';
$mensaje = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = floatval($_POST['precio']);
    // Guardar imagen
    $imagen = '';
    if ($FILES['imagen']['name']) {
        $imagen = uniqid() . "" . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], "../assets/img/" . $imagen);
    }
    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)");
    $stmt->bindparam("ssds", $nombre, $descripcion, $precio, $imagen);
    $stmt->execute();
    $mensaje = "Producto agregado correctamente.";
}
include '../includes/header.php';
?>
<div class="container mt-5">
    <h2 class="mb-4"><i class="fa fa-plus"></i> Agregar Producto</h2>
    <?php if($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Nombre:</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Descripción:</label>
            <textarea name="descripcion" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Precio:</label>
            <input type="number" step="0.01" name="precio" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Imagen:</label>
            <input type="file" name="imagen" class="form-control">
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Guardar</button>
        <a href="productos.php" class="btn btn-secondary">Volver</a>
    </form>
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
    if (e.ctrlKey && ['+', '-', '=', ''].includes(e.key)) e.preventDefault();
});
// Bloquear zoom con scroll del mouse (Ctrl + rueda)
window.addEventListener('wheel', function(e) {
    if (e.ctrlKey) e.preventDefault();
}, { passive: false });
// Prevenir arrastrar elementos (por ejemplo imágenes)
document.addEventListener('dragstart', e => e.preventDefault());
</script>
<?php include '../includes/footer.php'; ?>