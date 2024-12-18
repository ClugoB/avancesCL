<?php
/* CONEXION GENERAL PARA LA BASE DE DATOS */
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$base_de_datos = "ministeriomujer";
$charset = 'utf8mb4';
// VERIFICA SI USA MYSQL O POSTGRESQL
$db_type = 'mysql'; 
// CAMBIAR A PGSQL PARA USAR POSTGRESQL
if ($db_type == 'mysql') {
$dsn = "mysql:host=$servidor;dbname=$base_de_datos; charset=$charset";
} elseif ($db_type == 'pgsql') {
$dsn = "pgsql:host=$servidor;port=5432;dbname=$base_de_datos;user=$usuario;password=$contrasena";
}
try {
$conn = new PDO($dsn, $usuario, $contrasena);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
die("Error de conexión: " . $e->getMessage());
}
?>