<?php
// CONEXIÓN A LA BASE DE DATOS
include 'conexion_bd.php';
try {
// CONSULTA A LA BASE DE DATOS
$stmt = $conn->prepare('SELECT nombre_apellido, descripcion_cargo, num_id, cedula, ciudad, estado, municipio, parroquia, embarazada, tiempo_embarazada, discapacidad, tipo_discapacidad, genero, tipo_genero, correo, fecha_nacimiento, id_movimiento, fecha_registro FROM datos_ayudantes');
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// VERIFICA SI LA CONSULTA DEVOLVIÓ RESULTADOS
if (!empty($result)) {
// DEVUELVE LOS DATOS EN FORMATO JSON
echo json_encode($result, JSON_UNESCAPED_SLASHES);
} else {
echo json_encode([]);
}
} catch (PDOException $e) {
echo 'Error al ejecutar la consulta: ' . $e->getMessage();
}
?>