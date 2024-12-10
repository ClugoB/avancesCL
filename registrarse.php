<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIIMM</title>
    <link rel="stylesheet" href="style_login.css">
        <!-- ICONOS -->
  <link rel="stylesheet" type="text/css" href="boxicons-2.1.4/css/boxicons.min.css">
  <link rel="shortcut icon" href="imagenes/mujer3.jpg" type="image/x-icon">
</head>
<body>
<div class="cuerpo">    
    <div class="main">
        <div class="contenedor-todo">
        <div class="contenedor-imagen">
        <div class="col-md-6 imagen">  
            <img src="imagenes/mujer3.jpg" alt="Imagen de mujer">
        </div>
        <div class="col-md-6 contenedor-formulario">
<!-- FORMULARIO -->
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="input-caja">
<!-- CONEXIONES A LA BASE DE DATOS -->
<?php
// INCLUIR ARCHIVOS DE CONEXIÓN A LA BASE DE DATOS
include 'conexion_bd.php';

// CREAR CONEXIÓN USANDO PDO
$conn = new PDO("mysql:host=$servidor;dbname=$base_de_datos", $usuario, $contrasena);

// FUNCIÓN PARA OBTENER LOS DATOS DE LA BASE DE DATOS
function obtenerDatos($conn, $tabla, $columnas) {
    $sql = "SELECT $columnas FROM $tabla ORDER BY $columnas";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// OBTENER LOS DATOS PARA LAS PREGUNTAS DE SEGURIDAD
$preguntas_seguridad = obtenerDatos($conn, 'preguntas_seguridad', 'id_pregunta, pregunta');

// OBTENER LOS DATOS PARA EL CAMPO "NÚMERO DE IDENTIDAD"
$num_identidad = obtenerDatos($conn, 'num_identidad', 'identidad');

// OBTENER LOS ROLES "USUARIO" Y "USUARIO LIDER" DE LA TABLA "ROLES"
$role_names = array('Usuario', 'Usuario Lider');
$conditions = array();
foreach ($role_names as $role_name) {
    $conditions[] = "role_name = :$role_name";
}
$stmt = $conn->prepare("SELECT id, role_name FROM roles WHERE role_name IN ('Usuario', 'Usuario Lider')");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// VARIABLES PARA ALMACENAR LOS MENSAJES DE ERRORES
$error_messages = array();

// OBTENER LOS DATOS PARA EL REGISTRO DE USUARIOS
if (isset($_POST['register'])) {
    try {
        $usuario = trim(filter_var($_POST['nombre_usuario'], FILTER_SANITIZE_STRING));
        $contrasena = $_POST['contrasena'];
        $role_id = $_POST['role_id']; 
        $num_id = $_POST['num_id']; 
        $cedula = $_POST['cedula']; 
        $id_pregunta = $_POST['pregunta_seguridad']; 
        $respuesta_seguridad = $_POST['respuesta_seguridad'];

        // VALIDACIONES
        // EXISTE USUARIO
        $stmt = $conn->prepare('SELECT * FROM usuarios WHERE nombre_usuario = :usuario');
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($result)) {
            $error_messages[] = "El usuario ya existe. Por favor, elija otro nombre de usuario.";
        }

        // CONTRASEÑA
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $contrasena)) {
            $error_messages[] = "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un símbolo.";
        } elseif ($contrasena != $_POST['confirmar_contrasena']) {
            $error_messages[] = "Las contraseñas no coinciden.";
        }

        // VALIDACIÓN DE IDENTIDAD EXISTENTE
        $stmt = $conn->prepare('SELECT * FROM usuarios WHERE cedula = :cedula');
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($result)) {
            $error_messages[] = "La cédula ya está registrada.";
        }

        // VALIDACIONES DE NUEVOS CAMPOS
        if (empty($num_id)) {
            $error_messages[] = "Por favor, seleccione un número de cédula.";
        }
        if (empty($cedula)) {
            $error_messages[] = "Por favor, ingrese una cédula de identidad.";
        }

        if (empty($id_pregunta)) {
            $error_messages[] = "Por favor, seleccione una pregunta de seguridad.";
        }
        if (empty($respuesta_seguridad)) {
            $error_messages[] = "Por favor, ingrese una respuesta de seguridad.";
        }
        if (!is_numeric($role_id) || $role_id <= 0) { $error_messages[] = "El rol seleccionado no es válido.";
        } else {
            // ROL
            $stmt = $conn->prepare("SELECT 1 FROM roles WHERE id = :role_id");
            $stmt->bindParam(':role_id', $role_id);
            $stmt->execute();
            if (!$stmt->fetchColumn()) {
                $error_messages[] = "El rol seleccionado no es válido.";
            }
        }

        // Mostrar mensajes de error si existen
        if (!empty($error_messages)) {
            foreach ($error_messages as $error_message) {
                echo "<div class='mensaje_erroneo'>$error_message</div>";
            }
        } else {
            // ENCRIPTAR LA CONTRASEÑA
            $hashed_contrasena = password_hash($contrasena, PASSWORD_ARGON2I, ['cost' => 15]);

            // OBTENER LA PREGUNTA DE SEGURIDAD CORRESPONDIENTE AL ID
            $stmt = $conn->prepare('SELECT pregunta FROM preguntas_seguridad WHERE id_pregunta = :id_pregunta');
            $stmt->bindParam(':id_pregunta', $id_pregunta);
            $stmt->execute();
            $pregunta_result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si se encontró la pregunta
            if ($pregunta_result) {
                $pregunta_seguridad = $pregunta_result['pregunta'];

                // INSERTAR LOS DATOS EN LA BASE DE DATOS
                $stmt = $conn->prepare('INSERT INTO usuarios (nombre_usuario, contrasena, role_id, num_id, cedula, id_pregunta, pregunta_seguridad, respuesta_seguridad) VALUES (:nombre_usuario, :contrasena, :role_id, :num_id, :cedula, :id_pregunta, :pregunta_seguridad, :respuesta_seguridad)');
                $stmt->bindParam(':nombre_usuario', $usuario);
                $stmt->bindParam(':contrasena', $hashed_contrasena);
                $stmt->bindParam(':role_id', $role_id); 
                $stmt->bindParam(':num_id', $num_id); 
                $stmt->bindParam(':cedula', $cedula); 
                $stmt->bindParam(':id_pregunta', $id_pregunta); 
                $stmt->bindParam(':pregunta_seguridad', $pregunta_seguridad); 
                $stmt->bindParam(':respuesta_seguridad', $respuesta_seguridad);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    header('Location: admin.php?mensaje_exitoso=Registro exitoso!');
                    exit;
                } else {
                    header('Location: registrarse.php?mensaje_erroneo=Error al registrar el usuario.');
                    exit;
                }
            } else {
                $error_messages[] = "La pregunta de seguridad seleccionada no es válida.";
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        echo "Error en la línea: " . $e->getLine();
        echo "Error en el archivo: " . $e->getFile();
        echo "Error en la conexión a la base de datos";
        echo "Error en la validación de datos";
        echo "Error en la inserción de datos";
    }
}
?>
<!--  FORMULARIO  -->
<header class="titulo_fuer">Regístrate</header>
<div class="caja_form">
    <!-- NÚMERO DE IDENTIDAD -->
    <div class="input_form">
        <label for="num_id">Número de identidad:</label><br>
        <select id="num_id" name="num_id" required onchange="updateMaxLength()">
        <option value="">Seleccione el número de identidad</option>
        <?php foreach ($num_identidad as $identidad): ?>
            <option value="<?php echo htmlspecialchars($identidad['identidad']); ?>"><?php echo htmlspecialchars($identidad['identidad']); ?></option>
        <?php endforeach; ?>
    </select>
    </div>
    <!-- CÉDULA DE IDENTIDAD -->
    <div class="input_form">
    <label for="cedula">Cédula de identidad:</label>
    <input type="text" id="cedula" name="cedula" placeholder="Ingrese una cédula de identidad" class="form-elementos" required minlength="6" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.length < 6) { this.setCustomValidity('La cédula debe tener al menos 6 dígitos.'); } else { this.setCustomValidity(''); }">
</div>
</div>
<div class="caja_form">
<div class="input_form">
<div class="input-formulario">
<input type="text" name="nombre_usuario" id="usuario" class="input-caja-formulario" required maxlength="20">
<label for="usuario">Usuario</label>
</div>
</div>
<div class="input_form">
<label for="roles_id" class="input_form">ROLES:</label>
<br>
<select id="role_id" name="role_id" required>
<option value="">Seleccione un rol</option>
<?php foreach ($roles as $role): ?>
<option value="<?php echo $role['id']; ?>"><?php echo $role['role_name']; ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<div class="caja_form">
<div class="input_form">
<div class="input-formulario">
<input type="password" name="contrasena" id="contrasena_usuario" class="input-caja-formulario" required maxlength="16">
<label for="contrasena">Contraseña</label>
</div>
</div>
<div class="input_form">
<div class="input-formulario">
<input type="password" name="confirmar_contrasena" id="confirmar_contrasena" class="input-caja-formulario" required maxlength="16">
<label for="confirmar_contrasena">Confirmar</label>
</div>
</div>
</div> 
<div class="caja_form">
<div class="input_form">
<label for="pregunta_seguridad" class="input_form">Preguntas:</label>
<br>
<select id="pregunta_seguridad" name="pregunta_seguridad" required maxlength="30">
<option value="">Seleccione una pregunta</option>
<?php foreach ($preguntas_seguridad as $pregunta): ?>
<option value="<?php echo $pregunta['id_pregunta']; ?>"><?php echo $pregunta['pregunta']; ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="input_form">
<div class="input-formulario">
<input type="text" name="respuesta_seguridad" id="respuesta_seguridad" class="input-caja-formulario" required maxlength="30">
<label for="usuario">Respuesta</label>
</div>
</div>
</div>
<button class="boton" type="submit" name="register">Registrarse</button>
<br>
<span><a href="admin.php">¿Ya tienes cuenta? Inicia sesión</a></span>
</form>
</div>
</div>
</div>
</div>
</div>
<script>
    // DIGITOS EN CEDULA
    function updateMaxLength() {
        const select = document.getElementById('num_id');
        const input = document.getElementById('cedula');
        const selectedValue = select.value;
        // LONGITUD DE CIFRAS
        switch (selectedValue) {
            case 'V': 
                input.maxLength = 8;
                break;
            case 'E': 
                input.maxLength = 10;
                break;
            case 'J': 
                input.maxLength = 10;
                break;
            case 'G': 
                input.maxLength = 10;
                break;
            default: 
                input.maxLength = 10; 
                break;
        }
        // Limitar el valor actual si excede la nueva longitud máxima
        if (input.value.length > input.maxLength) {
            input.value = input.value.slice(0, input.maxLength);
        }
    }
</script>
<script>
setTimeout(function() {
document.querySelectorAll('.mensaje_exitoso,.mensaje_erroneo').forEach(function(element) {
element.style.display = 'none';
});
}, 5000);
</script>
</body>
</html>