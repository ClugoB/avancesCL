<?php
include 'conexion_bd.php'; 

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $id = $data['id'];
    $nombre_movimiento = $data['nombre_movimiento'] ?? null;
    $descripcion_movimiento = $data['descripcion_movimiento'] ?? null;

    $sql = "UPDATE form_mujeres SET 
                nombre_movimiento = :nombre_movimiento, 
                descripcion_movimiento = :descripcion_movimiento 
            WHERE id = :id";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre_movimiento', $nombre_movimiento);
        $stmt->bindParam(':descripcion_movimiento', $descripcion_movimiento);
        $stmt->bindParam(':id', $id);

        
        $stmt->execute();

        
        echo json_encode(["status" => "success", "message" => "Datos actualizados correctamente."]);
    } catch (PDOException $e) {
        
        echo json_encode(["status" => "error", "message" => "Error al actualizar los datos: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID no proporcionado."]);
}
?>