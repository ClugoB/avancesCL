<?php
include 'conexion_bd.php'; 
$sql = "SELECT * FROM form_mujeres"; 
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
?>