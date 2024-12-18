<?php
// conexion_form_ayudantes.php

include 'conexion_bd.php';

$mensaje_exitoso = null;
$mensaje_erroneo = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombre_apellido = $_POST['nombre_apellido'] ?? null;
    $descripcion_cargo = $_POST['descripcion_cargo_ayudantes'][0] ?? null; 
    $num_id_lider = $_POST['num_id_lider'] ?? null;
    $cedula_lider = $_POST['cedula_lider'] ?? null;
    $ciudad = $_POST['ciudad'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $municipio = $_POST['municipio'] ?? null;
    $parroquia = $_POST['parroquia'] ?? null;

    // Manejo de embarazada
    $embarazada = $_POST['embarazada'] ?? null;
    $tiempo_embarazada = ($embarazada === "Si") ? ($_POST['tiempo_embarazada'] ?? null) : null;

    // Manejo de discapacidad
    $discapacidad = $_POST['discapacidad'] ?? null;
    $tipo_discapacidad = ($discapacidad === "Si") ? ($_POST['tipo_discapacidad'] ?? null) : null;

    // Manejo de género
    $genero = $_POST['genero'] ?? null;
    $tipo_genero = ($genero === "Otro") ? ($_POST['tipo_genero'] ?? null) : null;

    $correo = $_POST['correo'] ?? null;
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $tipos_movimientos = $_POST['tipos_movimientos'] ?? null; 
    $nombre_movimiento = $_POST['nombre_movimiento'] ?? null; 

    // Inicializar un array para almacenar mensajes de error
    $errores = [];

    // Validar que no haya campos nulos
    if (empty($tipo_genero) && $genero === "Otro") {
        $errores[] = "El campo 'Tipo de género' es obligatorio.";
    }
    if (empty($tipos_movimientos)) {
        $errores[] = "El campo 'Tipo de movimiento' es obligatorio.";
    }
    if (empty($nombre_movimiento)) {
        $errores[] = "El campo 'Nombre de movimiento' es obligatorio.";
    }

    // Verificar si hay errores
    if (!empty($errores)) {
        $mensaje_erroneo = implode("<br>", $errores);
    } else {
        try {
            // Insertar datos en la tabla ayudantes_en_espera
            $sql = "INSERT INTO ayudantes_en_espera (nombre_apellido, descripcion_cargo, num_id_lider, cedula_lider, ciudad, estado, municipio, parroquia, embarazada, tiempo_embarazada, discapacidad, tipo_discapacidad, genero, tipo_genero, correo, fecha_nacimiento, tipos_movimientos, nombre_movimiento) 
                    VALUES (:nombre_apellido, :descripcion_cargo, :num_id_lider, :cedula_lider, :ciudad, :estado, :municipio, :parroquia, :embarazada, :tiempo_embarazada, :discapacidad, :tipo_discapacidad, :genero, :tipo_genero, :correo, :fecha_nacimiento, :tipos_movimientos, :nombre_movimiento)";

            $stmt = $conn->prepare($sql);

            // Vincular parámetros
            $stmt->bindParam(':nombre_apellido', $nombre_apellido);
            $stmt->bindParam(':descripcion_cargo', $descripcion_cargo);
            $stmt->bindParam(':num_id_lider', $num_id_lider);
            $stmt->bindParam(':cedula_lider', $cedula_lider);
            $stmt->bindParam(':ciudad', $ciudad);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':municipio', $municipio);
            $stmt->bindParam(':parroquia', $parroquia);
            $stmt->bindParam(':embarazada', $embarazada);
            $stmt->bindParam(':tiempo_embarazada', $tiempo_embarazada);
            $stmt->bindParam(':discapacidad', $discapacidad);
            $stmt->bindParam(':tipo_discapacidad', $tipo_discapacidad);
            $stmt->bindParam(':genero', $genero);
            $stmt->bindParam(':tipo_genero', $tipo_genero);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':tipos_movimientos', $tipos_movimientos);
            $stmt->bindParam(':nombre_movimiento', $nombre_movimiento);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $mensaje_exitoso = "Datos guardados en lista de espera correctamente.";
                
                header("Location: form_movimientos_mujeres.php");
                exit(); 
            } else {
                $mensaje_erroneo = "Error al guardar los datos.";
            }
        } catch (PDOException $e) {
            $mensaje_erroneo = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>
