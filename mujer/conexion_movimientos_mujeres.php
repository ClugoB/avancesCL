<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion_bd.php';

try {
    // Obtener los tipos de movimiento
    if ($_SERVER["REQUEST_METHOD"] == "GET" && !isset($_GET["tipos_movimientos"])) {
        $sql = "SELECT DISTINCT tipos_movimientos FROM form_mujeres"; 
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $tiposMovimiento = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tiposMovimiento);
    }

    // Obtener los movimientos en función del tipo de movimiento seleccionado
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["tipos_movimientos"])) {
        $tipoMovimiento = $_GET["tipos_movimientos"];
        $sql = "SELECT nombre_movimiento FROM form_mujeres WHERE tipos_movimientos = :tipos_movimientos"; 
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':tipos_movimientos', $tipoMovimiento);
        $stmt->execute();
        $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($movimientos);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
$conn = null;
?>