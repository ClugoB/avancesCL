<?php
require 'conexion_bd.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener los datos del ayudante que se va a rechazar
    $sql = "SELECT * FROM ayudantes_en_espera WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $ayudante = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ayudante) {
        // Inserta el registro rechazado en la tabla de rechazados
        $sqlInsert = "INSERT INTO rechazados (tipos_movimientos, nombre_movimiento, nombre_apellido, descripcion_cargo, num_id_lider, cedula_lider, ciudad, estado, municipio, parroquia, embarazada, tiempo_embarazada, discapacidad, tipo_discapacidad, genero, tipo_genero, correo, fecha_nacimiento, fecha_creacion, fecha_rechazo) 
                      VALUES (:tipos_movimientos, :nombre_movimiento, :nombre_apellido, :descripcion_cargo, :num_id_lider, :cedula_lider, :ciudad, :estado, :municipio, :parroquia, :embarazada, :tiempo_embarazada, :discapacidad, :tipo_discapacidad, :genero, :tipo_genero, :correo, :fecha_nacimiento, :fecha_creacion, NOW())";
        
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bindParam(':tipos_movimientos', $ayudante['tipos_movimientos']);
        $stmtInsert->bindParam(':nombre_movimiento', $ayudante['nombre_movimiento']);
        $stmtInsert->bindParam(':nombre_apellido', $ayudante['nombre_apellido']);
        $stmtInsert->bindParam(':descripcion_cargo', $ayudante['descripcion_cargo']);
        $stmtInsert->bindParam(':num_id_lider', $ayudante['num_id_lider']);
        $stmtInsert->bindParam(':cedula_lider', $ayudante['cedula_lider']);
        $stmtInsert->bindParam(':ciudad', $ayudante['ciudad']);
        $stmtInsert->bindParam(':estado', $ayudante['estado']);
        $stmtInsert->bindParam(':municipio', $ayudante['municipio']);
        $stmtInsert->bindParam(':parroquia', $ayudante['parroquia']);
        $stmtInsert->bindParam(':embarazada', $ayudante['embarazada']);
        $stmtInsert->bindParam(':tiempo_embarazada', $ayudante['tiempo_embarazada']);
        $stmtInsert->bindParam(':discapacidad', $ayudante['discapacidad']);
        $stmtInsert->bindParam(':tipo_discapacidad', $ayudante['tipo_discapacidad']);
        $stmtInsert->bindParam(':genero', $ayudante['genero']);
        $stmtInsert->bindParam(':tipo_genero', $ayudante['tipo_genero']);
        $stmtInsert->bindParam(':correo', $ayudante['correo']);
        $stmtInsert->bindParam(':fecha_nacimiento', $ayudante['fecha_nacimiento']);
        $stmtInsert->bindParam(':fecha_creacion', $ayudante['fecha_creacion']);
        
        // Ejecutar la inserción
        if ($stmtInsert->execute()) {
            // Eliminar el registro de la tabla ayudantes_en_espera
            $sqlDelete = "DELETE FROM ayudantes_en_espera WHERE id = :id";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bindParam(':id', $id);
            $stmtDelete->execute();

            // Redirigir a la página principal o a donde desees
            header("Location: aceptar_ayudantes.php?success=2");
            exit();
        } else {
            echo "Error al mover los datos a la tabla de rechazados.";
        }
    } else {
        echo "Ayudante no encontrado.";
    }
} else {
    echo "ID no válido.";
}
?>