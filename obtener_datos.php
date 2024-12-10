<?php
// Incluir la conexión a la base de datos
include 'conexion_bd.php';

// Obtener los datos JSON de la solicitud POST
$data = json_decode(file_get_contents("php://input"), true);

// Verificar que se hayan recibido las estadísticas y el usuario
if (isset($data['stats']) && isset($data['user'])) {
    $stats = $data['stats'];
    $user = $data['user'];
    $nombreMovimiento = $user['nombre_movimiento']; 

    // Preparar un array para almacenar los resultados
    $result = [];

    foreach ($stats as $stat) {
        switch ($stat) {
            case 'cantidad_hombres':
                $stmt = $conn->prepare("SELECT COUNT(*) as cantidad FROM movimientos WHERE genero = 'hombre' AND nombre_movimiento = :nombre_movimiento");
                $stmt->bindParam(':nombre_movimiento', $nombreMovimiento);
                break;
            case 'cantidad_mujeres':
                $stmt = $conn->prepare("SELECT COUNT(*) as cantidad FROM movimientos WHERE genero = 'mujer' AND nombre_movimiento = :nombre_movimiento");
                $stmt->bindParam(':nombre_movimiento', $nombreMovimiento);
                break;
            case 'estado_ubicado':
                $stmt = $conn->prepare("SELECT estado_ubicado, COUNT(*) as cantidad FROM movimientos WHERE nombre_movimiento = :nombre_movimiento GROUP BY estado_ubicado");
                $stmt->bindParam(':nombre_movimiento', $nombreMovimiento);
                break;
            case 'ciudad_ubicado':
                $stmt = $conn->prepare("SELECT ciudad_ubicado, COUNT(*) as cantidad FROM movimientos WHERE nombre_movimiento = :nombre_movimiento GROUP BY ciudad_ubicado");
                $stmt->bindParam(':nombre_movimiento', $nombreMovimiento);
                break;
            case 'municipio_ubicado':
                $stmt = $conn->prepare("SELECT municipio_ubicado, COUNT(*) as cantidad FROM movimientos WHERE nombre_movimiento = :nombre_movimiento GROUP BY municipio_ubicado");
                $stmt->bindParam(':nombre_movimiento', $nombreMovimiento);
                break;
            case 'parroquia_ubicado':
                $stmt = $conn->prepare("SELECT parroquia_ubicado, COUNT(*) as cantidad FROM movimientos WHERE nombre_movimiento = :nombre_movimiento GROUP BY parroquia_ubicado");
                $stmt->bindParam(':nombre_movimiento', $nombreMovimiento);
                break;
            default:
                continue; 
        }

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los resultados
        if ($stat === 'estado_ubicado' || $stat === 'ciudad_ubicado' || $stat === 'municipio_ubicado' || $stat === 'parroquia_ubicado') {
            $result[$stat] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $result[$stat] = $stmt->fetchColumn();
        }
    }

    // Devolver los resultados como JSON
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    // Si no se recibieron los datos necesarios
    http_response_code(400);
    echo json_encode(['error' => 'Datos no válidos']);
}
?>