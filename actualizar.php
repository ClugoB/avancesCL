<?php
session_start();
include 'conexion_bd.php';
// VERIFICA SI EL USUARIO INICIO SESIÓN
if (!isset($_SESSION['usuario'])) {
    header("Location: admin.php");
    exit();
}
// VERIFICA SI HA ENVIADO EL FORMULARIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarioData = $_POST;
// VERIFICA LA CONTRASEÑA ACTUAL
$stmt = $conn->prepare('SELECT contrasena FROM usuarios WHERE nombre_usuario = :usuario'); 
$stmt->bindParam(':usuario', $usuarioData['usuario']); 
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// VERIFICA SI SE ENCONTRÓ EL USUARIO
if (empty($result)) {
error_log("Error: Usuario no encontrado");
$mensaje_erroneo = "Usuario no encontrado";
header('Location: editar_usuario.php?mensaje_erroneo=' . $mensaje_erroneo);
die();
}
$contrasena_actual_bd = $result[0]['contrasena'];
// VERIFICA LA CONTRASEÑA ACTUAL INGRESADA
if (!password_verify($usuarioData['contrasena_actual'], $contrasena_actual_bd)) {
error_log("Error: La contraseña actual es errónea");
$mensaje_erroneo = "La contraseña actual es errónea";
header('Location: editar_usuario.php?mensaje_erroneo=' . $mensaje_erroneo);
die();
}
// ACTUALIZA DATOS
$stmt = $conn->prepare('UPDATE usuarios SET nombre = :nombre, apellido = :apellido, correo = :correo, cedula = :cedula, oficina = :oficina, departamento = :departamento, cargo = :cargo WHERE nombre_usuario = :usuario'); 
$stmt->bindParam(':nombre', $usuarioData['nombre']);
$stmt->bindParam(':apellido', $usuarioData['apellido']);
$stmt->bindParam(':correo', $usuarioData['correo']);
$stmt->bindParam(':cedula', $usuarioData['cedula']);
$stmt->bindParam(':oficina', $usuarioData['oficina']);
$stmt->bindParam(':departamento', $usuarioData['departamento']);
$stmt->bindParam(':cargo', $usuarioData['cargo']);
$stmt->bindParam(':usuario', $usuarioData['usuario']); 
$stmt->execute();
// VERIFICA QUE LA ACTUALIZACIÓN FUE EXITOSA
if ($stmt->rowCount() > 0) {
$mensaje_exitoso = "Datos actualizados correctamente";
header('Location: editar_usuario.php?mensaje_exitoso=' . $mensaje_exitoso);
die();
} else {
error_log("Error al actualizar datos: " . $stmt->errorInfo()[2]);
$mensaje_erroneo = "Error al actualizar datos";
header('Location: editar_usuario.php?mensaje_erroneo=' . $mensaje_erroneo);
die();
}
}
?>