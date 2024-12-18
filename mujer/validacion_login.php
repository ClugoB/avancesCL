<?php
session_start();
// REDIRIGE SI YA HAY UNA SESIÓN INICIADA
if (isset($_SESSION['usuario'])) {
    header("Location: principal.php");
    return; 
}
// CONEXIÓN A LA BASE DE DATOS
include 'conexion_bd.php';
if (!$conn) {
$error = 'Error al conectar a la base de datos.';
header("Location: admin.php?mensaje_erroneo=" . urlencode($error));
return; 
}
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];
// VALIDACIÓN DE ENTRADA
if (empty($usuario) || empty($contrasena)) {
$error = 'Por favor, rellene todos los campos.';
header("Location: admin.php?mensaje_erroneo=" . urlencode($error));
return; 
}
$stmt = $conn->prepare('SELECT contrasena FROM usuarios WHERE nombre_usuario = :usuario LIMIT 1');
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($result)) {
$contrasena_encriptada = $result[0]['contrasena'];
if (password_verify($contrasena, $contrasena_encriptada)) {
$_SESSION['usuario'] = $usuario;
header("Location: principal.php?mensaje_exitoso=Inicio de sesión exitoso.");
return; 
} else {
$error = 'Credenciales incorrectas.';
header("Location: admin.php?mensaje_erroneo=" . urlencode($error));
return; 
}
} else {
$error = 'Credenciales incorrectas.';
header("Location: admin.php?mensaje_erroneo=" . urlencode($error));
return; 
}
?>