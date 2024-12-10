<?php
// INCLUIR ARCHIVOS DE CONEXIÓN A LA BASE DE DATOS
include 'conexion_bd.php';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $contrasena = trim(filter_var($_POST['nueva_contrasena'], FILTER_SANITIZE_STRING));
        $nombre_usuario = trim(filter_var($_POST['nombre_usuario'], FILTER_SANITIZE_STRING));
        $error_messages = [];
        // VALIDACIONES DE LA CONTRASEÑA
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $contrasena)) {
            $error_messages[] = "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un símbolo.";
        }
        if (!empty($error_messages)) {
            foreach ($error_messages as $error_message) {
                echo json_encode(['success' => false, 'message' => $error_message]);
            }
            exit;
        }
        // ENCRIPTAR LA CONTRASEÑA
        $hashed_contrasena = password_hash($contrasena, PASSWORD_ARGON2I, ['cost' => 15]);
        $stmt = $conn->prepare("UPDATE usuarios SET contrasena = :contrasena WHERE nombre_usuario = :nombre_usuario");
        $stmt->bindParam(':contrasena', $hashed_contrasena);
        $stmt->bindParam(':nombre_usuario', $nombre_usuario);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Contraseña actualizada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la contraseña.']);
        }
    } else {
        throw new Exception("Método de solicitud no válido. Se esperaba POST.");
    }
} catch (PDOException $e) {
    error_log("Error en la base de datos: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>