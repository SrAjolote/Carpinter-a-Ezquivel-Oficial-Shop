<?php
$conexion = new mysqli("localhost", "u182426195_carpinteria", "2415691611+David", "u182426195_carpinteria");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>