<?php
// INICIAR SESIÓN
session_start();
// CÓDIGO 403 HTTP
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acceso denegado</title>
<link rel="stylesheet" href="style_denegado.css"> 
</head>
<body>
<div class="contenedor">
<h1>Acceso denegado</h1>
<p>Lo sentimos, no tienes permiso para acceder a esta página.</p>
<p>Si cree que se trata de un error, póngase en contacto con el super administrador.</p>
<a href="principal.php" class="boton">Volver</a> 
</div>
</body>
</html>