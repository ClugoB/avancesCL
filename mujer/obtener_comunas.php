<?php
include 'conexion_bd.php';
$parroquia = $_POST['parroquia'];
$comunas = obtenerDatos($conn, 'comunas', 'comuna', "parroquia = '$parroquia'");
echo '<select id="comuna" name="comuna" required>';
echo '<option value="">Seleccione la Comuna a la que pertenece</option>';
foreach ($comunas as $comuna) {
    echo "<option value='$comuna'>$comuna</option>";
}
echo '</select>';
?>