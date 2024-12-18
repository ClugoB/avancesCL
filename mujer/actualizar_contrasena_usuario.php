<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header('Location: admin.php');
exit();
}
$userNombre = $_SESSION['usuario'];
$mensaje_exitoso = '';
$mensaje_erroneo = '';
// MENSAJES DE EXITO Y ERROR
if (isset($_GET['mensaje_exitoso'])) {
echo "<div class='mensaje_exitoso'>" . $_GET['mensaje_exitoso'] . "</div>";
}
if (isset($_GET['mensaje_erroneo'])) {
echo "<div class='mensaje_erroneo'>" . $_GET['mensaje_erroneo'] . "</div>";
}
include 'conexion_bd.php';
// VERIFICAR LA SOLICITUD POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$usuarioData = $_POST;
// VERIFICAR LA CONTRASEÑA ACTUAL
$stmt = $conn->prepare('SELECT contrasena FROM usuarios WHERE nombre_usuario = :nombre_usuario'); 
$stmt->bindParam(':nombre_usuario', $userNombre); 
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// VERIFICA SI ENCONTRO AL USUARIO
if (empty($result)) {
error_log("Error: Usuario no encontrado");
$mensaje_erroneo = "Usuario no encontrado";
header('Location: editar_usuario_usuarios.php?mensaje_erroneo=' . $mensaje_erroneo);
die();
}
$contrasena_actual_bd = $result[0]['contrasena'];
// VERIFICAR SI LA CONTRASEÑA ACTUAL ES IGUAL
if (!password_verify($usuarioData['contrasena_actual'], $contrasena_actual_bd)) {
error_log("Error: La contraseña actual es errónea");
$mensaje_erroneo = "La contraseña actual es errónea";
header('Location: editar_usuario_usuarios.php?mensaje_erroneo=' . $mensaje_erroneo);
die();
}
// VERIFICA LA NUEVA CONTRASEÑA
if (empty($usuarioData['contrasena_nueva'])) {
$mensaje_erroneo = "Debe ingresar una nueva contraseña";
header('Location: editar_usuario_usuarios.php?mensaje_erroneo=' . $mensaje_erroneo);
die();
}
if ($usuarioData['contrasena_nueva'] != $usuarioData['contrasena_confirmar']) {
$mensaje_erroneo = "Las contraseñas no coinciden";
header('Location: editar_usuario_usuarios.php?mensaje_erroneo=' . $mensaje_erroneo);
die();
}
// HASHEAR LA CONTRASEÑA
$contrasena_hash = password_hash($usuarioData['contrasena_nueva'], PASSWORD_BCRYPT); 
// ACTUALIZA EL USUARIO
$stmt = $conn->prepare('UPDATE usuarios SET contrasena = :contrasena WHERE nombre_usuario = :nombre_usuario'); 
$stmt->bindParam(':contrasena', $contrasena_hash);
$stmt->bindParam(':nombre_usuario', $userNombre); 
if ($stmt->execute()) {
$mensaje_exitoso = "Contraseña actualizada correctamente";
header('Location: editar_usuario_usuarios.php?mensaje_exitoso=' . $mensaje_exitoso);
die();
} else {
error_log("Error al actualizar datos: " . $stmt->errorInfo()[2]);
$mensaje_erroneo = "Error al actualizar datos";
header('Location: editar_usuario_usuarios.php?mensaje_erroneo=' . $mensaje_erroneo);
die();
}
}
?>