<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion_bd.php';

// Verificar si se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula']; 
    $ciudad = $_POST['ciudad'];
    $estado = $_POST['estado'];
    $municipio = $_POST['municipio'];
    $parroquia = $_POST['parroquia'];
    $embarazada = isset($_POST['embarazada']) ? $_POST['embarazada'] : null;
    $tiempo_embarazada = $_POST['tiempo_embarazada'];
    $discapacidad = isset($_POST['discapacidad']) ? $_POST['discapacidad'] : null;
    $tipo_discapacidad = $_POST['tipo_discapacidad'];
    $genero = $_POST['genero'];
    $tipo_genero = $_POST['tipo_genero'];
    $correo = $_POST['correo'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $sql = "UPDATE datos_ayudantes 
            SET ciudad = :ciudad, 
                estado = :estado, 
                municipio = :municipio, 
                parroquia = :parroquia, 
                embarazada = :embarazada, 
                tiempo_embarazada = :tiempo_embarazada, 
                discapacidad = :discapacidad, 
                tipo_discapacidad = :tipo_discapacidad, 
                genero = :genero, 
                tipo_genero = :tipo_genero, 
                correo = :correo, 
                fecha_nacimiento = :fecha_nacimiento 
            WHERE cedula = :cedula";


    $stmt = $conn->prepare($sql);

    // Vincular los parámetros
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
    $stmt->bindParam(':cedula', $cedula); 

    // Ejecutar la consulta
    if ($stmt->execute()) {

        header("Location: consultas_movimientos.php?mensaje_exitoso=" . urlencode("Los datos se han actualizado correctamente."));
        exit();
    } else {

        header("Location: consultas_movimientos.php?mensaje_erroneo=" . urlencode("Error al actualizar los datos."));
        exit();
    }
} else {

    header("Location: consultas_movimientos.php?mensaje_erroneo=" . urlencode("No se han enviado datos."));
    exit();
}
?>