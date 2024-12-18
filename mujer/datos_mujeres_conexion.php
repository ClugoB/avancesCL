<?php
// CONEXIÓN A LA BASE DE DATOS
include 'conexion_bd.php';
try {
// CONSULTA A LA BASE DE DATOS
$stmt = $conn->prepare('SELECT nombre_movimiento, descripcion_movimiento, movimiento_tipo, ciudad_ubicado, estado_ubicado, municipio_ubicado, parroquia_ubicado, estados_presentes, cantidad_mujeres, cantidad_hombres, nombre_lider, apellido_lider, num_id_lider, cedula_lider, codigo_lider, numero_telefono_lider, fecha_nacimiento_lider, correo_lider, fecha_registro FROM form_mujeres');
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