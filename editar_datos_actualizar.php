<?php
// Incluir la conexión a la base de datos
require_once 'conexion_bd.php';

// Obtener los datos del formulario de edición
$data = json_decode(file_get_contents('php://input'), true); 

// Verificar si se recibieron los datos necesarios
if (!isset($data['nombre_lider'], $data['apellido_lider'], $data['num_id_lider'], $data['cedula_lider'])) {
    error_log("Error: Faltan datos para actualizar.");
    echo json_encode(["status" => "error", "message" => "Faltan datos para actualizar."]);
    exit();
}

// Validar que los demás campos no estén vacíos
$nombre_lider = trim($data['nombre_lider']);
$apellido_lider = trim($data['apellido_lider']);
$num_id_lider = trim($data['num_id_lider']);
$cedula_lider = trim($data['cedula_lider']);

if (empty($nombre_lider) || empty($apellido_lider) || empty($num_id_lider) || empty($cedula_lider)) {
    error_log("Error: Los campos no pueden estar vacíos. Datos recibidos: nombre_lider=$nombre_lider, apellido_lider=$apellido_lider, num_id_lider=$num_id_lider, cedula_lider=$cedula_lider");
    echo json_encode(["status" => "error", "message" => "Los campos no pueden estar vacíos."]);
    exit();
}

// Verificar si el registro existe en la tabla form_mujeres
$checkStmt = $conn->prepare("SELECT COUNT(*) FROM form_mujeres WHERE num_id_lider = ?");
$checkStmt->bindParam(1, $num_id_lider);
$checkStmt->execute();
if ($checkStmt->fetchColumn() == 0) {
    error_log("Error: No se encontró el registro con el num_id_lider proporcionado en la tabla form_mujeres. num_id_lider: $num_id_lider");
    echo json_encode(["status" => "error", "message" => "No se encontró el registro con el num_id_lider proporcionado en la tabla form_mujeres."]);
    exit();
}

// Actualizar los datos en la tabla form_mujeres
try {
    // Preparar la consulta para actualizar los datos
    $stmt = $conn->prepare("UPDATE form_mujeres SET nombre_lider = ?, apellido_lider = ?, cedula_lider = ? WHERE num_id_lider = ?");
    
    // Vincular los parámetros
    $stmt->bindParam(1, $nombre_lider);
    $stmt->bindParam(2, $apellido_lider);
    $stmt->bindParam(3, $cedula_lider);
    $stmt->bindParam(4, $num_id_lider);
    
    // Imprimir la consulta y los parámetros para diagnóstico
    error_log("Consulta: UPDATE form_mujeres SET nombre_lider = '$nombre_lider', apellido_lider = '$apellido_lider', cedula_lider = '$cedula_lider' WHERE num_id_lider = '$num_id_lider'");
    
    // Ejecutar la consulta
    if ($stmt->execute()) {
        error_log("Éxito: Datos actualizados correctamente en la tabla form_mujeres.");
        echo json_encode(["status" => "success", "message" => "Datos actualizados correctamente en la tabla form_mujeres."]);
    } else {
        // Capturar el error de ejecución
        $errorInfo = $stmt->errorInfo();
        error_log("Error: No se pudo ejecutar la consulta de actualización. Error: " . $errorInfo[2]);
        echo json_encode(["status" => "error", "message" => "No se pudo ejecutar la consulta de actualización. Error: " . $errorInfo[2]]);
    }
} catch (PDOException $e) {
    // Manejo de errores específicos
    error_log("Error al actualizar los datos: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Error al actualizar los datos: " . $e->getMessage()]);
} catch (Exception $e) {
    // Captura de errores generales
    error_log("Error inesperado: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Error inesperado: " . $e->getMessage()]);
}

// Cerrar la conexión a la base de datos
$conn = null;
?>
