<?php
require 'conexion_bd.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener los datos del ayudante que se va a aceptar
    $sql = "SELECT * FROM ayudantes_en_espera WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $ayudante = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ayudante) {
        // Insertar en la tabla datos_ayudantes
        $sqlInsert = "INSERT INTO datos_ayudantes (nombre_apellido, descripcion_cargo, ciudad, estado, municipio, parroquia, embarazada, tiempo_embarazada, discapacidad, tipo_discapacidad, genero, tipo_genero, correo, fecha_nacimiento, tipos_movimientos, nombre_movimiento) 
                      VALUES (:nombre_apellido, :descripcion_cargo, :ciudad, :estado, :municipio, :parroquia, :embarazada, :tiempo_embarazada, :discapacidad, :tipo_discapacidad, :genero, :tipo_genero, :correo, :fecha_nacimiento, :tipos_movimientos, :nombre_movimiento)";

        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bindParam(':nombre_apellido', $ayudante['nombre_apellido']);
        $stmtInsert->bindParam(':descripcion_cargo', $ayudante['descripcion_cargo']);
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
        $stmtInsert->bindParam(':tipos_movimientos', $ayudante['tipos_movimientos']);
        $stmtInsert->bindParam(':nombre_movimiento', $ayudante['nombre_movimiento']);

        // Ejecutar la inserción en datos_ayudantes
        if ($stmtInsert->execute()) {
            // Si la inserción fue exitosa, eliminar el registro de ayudantes_en_espera
            $sqlDelete = "DELETE FROM ayudantes_en_espera WHERE id = :id";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bindParam(':id', $id);
            $stmtDelete->execute();

            // Redirigir a la página principal o a donde desees
            header("Location: aceptar_ayudantes.php");
            exit();
        } else {
            echo "Error al mover los datos a datos_ayudantes.";
        }
    } else {
        echo "Ayudante no encontrado.";
    }
} else {
    echo "ID no proporcionado.";
}
?>