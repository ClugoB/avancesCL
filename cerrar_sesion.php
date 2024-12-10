<!-- CERRAR SESIÓN -->
<?php 
session_start();
session_destroy();
header("location: ministeriodelamujer.php");
?>