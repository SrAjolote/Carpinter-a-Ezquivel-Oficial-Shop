<?php
// Archivo de prueba para verificar la base de datos
$host = "localhost";
$user = "u182426195_carpinteria";
$pass = "2415691611+David";
$db   = "u182426195_carpinteria";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    echo "<h2>Prueba de Base de Datos</h2>";
    
    // Verificar tabla productos
    $result = $conn->query("DESCRIBE productos");
    echo "<h3>Estructura de tabla productos:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar si existe tabla producto_imagenes
    $check_table = $conn->query("SHOW TABLES LIKE 'producto_imagenes'");
    if ($check_table->num_rows > 0) {
        echo "<h3>Tabla producto_imagenes existe ✓</h3>";
        $result = $conn->query("DESCRIBE producto_imagenes");
        echo "<table border='1'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<h3>Tabla producto_imagenes NO existe ✗</h3>";
        echo "<p>Se creará automáticamente al agregar el primer producto.</p>";
    }
    
    // Verificar campos stock y precio_anterior
    $check_stock = $conn->query("SHOW COLUMNS FROM productos LIKE 'stock'");
    echo "<p>Campo 'stock': " . ($check_stock->num_rows > 0 ? "✓ Existe" : "✗ No existe") . "</p>";
    
    $check_precio = $conn->query("SHOW COLUMNS FROM productos LIKE 'precio_anterior'");
    echo "<p>Campo 'precio_anterior': " . ($check_precio->num_rows > 0 ? "✓ Existe" : "✗ No existe") . "</p>";
    
    // Contar productos
    $count = $conn->query("SELECT COUNT(*) as total FROM productos");
    $total = $count->fetch_assoc()['total'];
    echo "<p>Total de productos: $total</p>";
    
    echo "<h3>✅ Conexión exitosa</h3>";
    echo "<p><a href='dashboard.php'>Volver al Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>