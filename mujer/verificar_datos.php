<?php
// INCLUIR ARCHIVOS DE CONEXIÓN A LA BASE DE DATOS
include 'conexion_bd.php';
try {
    // OBTENER DATOS DEL FORMULARIO
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar si se está enviando la respuesta de seguridad
        if (isset($_POST['respuesta_seguridad'])) {
            $respuesta_seguridad = trim(filter_var($_POST['respuesta_seguridad'], FILTER_SANITIZE_STRING));
            // Validar la respuesta de seguridad
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE respuesta_seguridad = :respuesta_seguridad AND nombre_usuario = :nombre_usuario");
            $stmt->bindParam(':respuesta_seguridad', $respuesta_seguridad);
            $stmt->bindParam(':nombre_usuario', $_POST['nombre_usuario']); 
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Respuesta correcta.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Respuesta de seguridad incorrecta.']);
            }
            exit; 
        }
        // Si no se envió la respuesta de seguridad, procesar los datos iniciales
        $nombre_usuario = trim(filter_var($_POST['nombre_usuario'], FILTER_SANITIZE_STRING));
        $num_id = trim(filter_var($_POST['num_id'], FILTER_SANITIZE_STRING));
        $cedula = trim(filter_var($_POST['cedula'], FILTER_SANITIZE_STRING));

        // Mensajes de depuración
        error_log("Datos recibidos: nombre_usuario=$nombre_usuario, num_id=$num_id, cedula=$cedula");

        // VALIDAR EN LA BASE DE DATOS
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nombre_usuario = :nombre_usuario AND num_id = :num_id AND cedula = :cedula");
        $stmt->bindParam(':nombre_usuario', $nombre_usuario);
        $stmt->bindParam(':num_id', $num_id);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Usuario encontrado: " . print_r($usuario, true));
            echo json_encode([
                'success' => true,
                'message' => 'Los datos son correctos.',
                'pregunta_seguridad' => $usuario['pregunta_seguridad']
            ]);
        } else {
            error_log("No se encontraron coincidencias.");
            echo json_encode(['success' => false, 'message' => 'Los datos no coinciden.']);
        }
    } else {
        throw new Exception("Método de solicitud no válido. Se esperaba POST.");
    }
} catch (PDOException $e) {
    error_log("Error en la conexión: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la conexión: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error general: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>