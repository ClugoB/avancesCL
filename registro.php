<script>
</script>
<?php
session_start();
include 'conexion_bd.php'; 
// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
// Redirige al usuario a la página de inicio de sesión si no está autenticado
header("Location: admin.php");
exit();
}
// Obtiene el nombre de usuario y el rol del usuario autenticado
$usuario = $_SESSION['usuario'];
// Obtener el role_id del usuario desde la base de datos
$stmt = $conn->prepare("SELECT role_id FROM usuarios WHERE nombre_usuario = :usuario LIMIT 1");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
$role_id = $result['role_id']; 
} else {
// Si no se encuentra el usuario, redirige a la página de inicio de sesión
header("Location: admin.php");
exit();
}
// ROLES PERMITIDOS
$roles_permitidos = [1]; 
// Verificar si el usuario tiene un rol permitido
if (!in_array($role_id, $roles_permitidos)) {
// Redirige a la página de acceso denegado si no tiene permisos
header("Location: acceso_denegado.php");
exit();
}
// Obtener permisos del usuario
$stmt = $conn->prepare("SELECT permiso FROM permisos WHERE rol_id = :role_id");
$stmt->bindParam(':role_id', $role_id);
$stmt->execute();
$permisos = $stmt->fetchAll(PDO::FETCH_COLUMN);
// Definir los permisos por rol
$permisosPorRol = [
    1 => ['ver_panel', 'ver_usuarios', 'editar_usuarios', 'crear_usuarios', 'crear_movimientos', 'ver_graficas'], 
    2 => ['ver_panel', 'ver_usuarios', 'crear_movimientos', 'ver_graficas'], 
    3 => ['ver_panel', 'crear_movimientos'], 
    4 => ['ver_panel'],
];
// Verificar permisos según el rol
$permisosPermitidos = $permisosPorRol[$role_id] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>SIIMM</title>
    <link rel="shortcut icon" href="imagenes/mujer3.jpg" type="image/x-icon">
        <!-- ICONOS -->
  <link rel="stylesheet" type="text/css" href="boxicons-2.1.4/css/boxicons.min.css">
<!-- ESTILOS -->
    <link rel="stylesheet" href="style_interfaz.css">
    <link rel="stylesheet" href="style_registro_admins.css">
</head>
<body>
<!-- SIDEBAR -->
<div class="sidebar">
        <a href="principal.php" class="logo">
    <div class="logo-name"><span>MinMujer</span></div>
        <img src="imagenes/mujer3.jpg" style="display: none;">
        </a>
        <ul class="side-menu">
        <?php if (in_array('ver_panel', $permisosPermitidos)): ?>
                <li><a href="principal.php"><i class='bx bx-home'></i>PRINCIPAL</a></li>
            <?php endif; ?>
            <?php if (in_array('ver_graficas', $permisosPermitidos)): ?>
                <li><a href="graficas.php"><i class='bx bx-line-chart'></i>GRÁFICAS</a></li>
            <?php endif; ?>
            <?php if (in_array('crear_movimientos', $permisosPermitidos)): ?>
                <li><a href="form_movimientos.php"><i class='bx bx-file'></i>FORMULARIO</a></li>
            <?php endif; ?>
            <?php if (in_array('ver_panel', $permisosPermitidos)): ?>
                <li><a href="consultas_movimientos.php"><i class='bx bx-question-mark'></i>CONSULTAS</a></li>
            <?php endif; ?>
            <?php if (in_array('crear_usuarios', $permisosPermitidos)): ?>
                <li><a href="registro.php"><i class='bx bx-user-plus'></i>CREAR USUARIOS</a></li>
            <?php endif; ?>
            <?php if (in_array('editar_usuarios', $permisosPermitidos)): ?>
                <li><a href="editar_usuario.php"><i class='bx bx-user'></i>EDITAR USUARIOS</a></li>
            <?php endif; ?>
            <?php if (in_array('ver_usuarios', $permisosPermitidos)): ?>
                <li><a href="datos_mujeres.php"><i class='bx bx-female'></i>MUJERES</a></li>
            <?php endif; ?>
            <?php if (in_array('ver_usuarios', $permisosPermitidos)): ?>
                <li><a href="datos_ayudantes.php"><i class='bx bx-male'></i>AYUDANTES</a></li>
            <?php endif; ?>
            <li><a href="#"><i class='bx bx-cog'></i>Settings</a></li>
        </ul>
    </div>
<!-- NAVEGACIÓN -->
    <div class="content">
        <nav>
            <i class='bx bx-menu'></i>
<!-- BUSCADOR DEL NAVEGADOR -->
        <form action="#">
            <div class="form-input">
                <input type="search" class="buscador" placeholder="Buscar">
                <button class="search-btn" type="submit"><i class='bx bx-search'></i></button>
            </div>
        </form>
<!-- BOTON DE MODO CLARO Y OSCURO -->
    <button id="theme-toggle" class="theme-toggle">
        <i class='bx bx-sun' id="theme-icon"></i>
    </button>
<!-- ICONO DE NOTIFICACIONES -->
            <a href="#" class="notif">
                <i class='bx bx-bell'></i>
                <span class="count">12</span>
            </a>
<!-- OPCIONES DE PERFIL -->
    <div class="profile" aria-haspopup="true" role="button">
        <img src="images/logo.png">
        <ul class="profile-dropdown">
      <li><a href="editar_usuario_usuarios.php">Editar perfil</a></li>
      <li><a href="cerrar_sesion.php">Cerrar sesión</a></li>
    </ul>
  </div>
</nav>
<!-- MAIN -->
<main class="main-content">
    <div class="header">
        <div class="left">
            <h1>CREAR USUARIOS</h1>
        </div>
    </div>
    <div class="container container_tickets">
        <?php
        if (isset($_GET['mensaje_exitoso'])) {
            echo "<div class='mensaje_exitoso'>" . $_GET['mensaje_exitoso'] . "</div>";
        }
        if (isset($_GET['mensaje_erroneo'])) {
            echo "<div class='mensaje_erroneo'>" . $_GET['mensaje_erroneo'] . "</div>";
        }
        ?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="formulario-registro">
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
$role_names = array('Usuario', 'Usuario Lider', 'Admin', 'Super Admin');
$conditions = array();
foreach ($role_names as $role_name) {
    $conditions[] = "role_name = :$role_name";
}
$stmt = $conn->prepare("SELECT id, role_name FROM roles WHERE role_name IN ('Usuario', 'Usuario Lider', 'Admin', 'Super Admin')");
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
        if (!is_numeric($role_id) || $role_id <= 0) {$error_messages[] = "El rol seleccionado no es válido.";
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
                    header('Location: registro.php?mensaje_exitoso=Registro exitoso!');
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
    <div class="caja_form">
        
            <!-- NÚMERO DE IDENTIDAD -->
            <div class="input_form">
            <h2>Número y Cédula de identidad:</h2>
                <select id="num_id" name="num_id" required onchange="updateMaxLength()">
                    <option value="">Seleccione el número de identidad</option>
                    <?php foreach ($num_identidad as $identidad): ?>
                        <option value="<?php echo htmlspecialchars($identidad['identidad']); ?>"><?php echo htmlspecialchars($identidad['identidad']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- CÉDULA DE IDENTIDAD -->
            <div class="input_form">
                <input type="text" id="cedula" name="cedula" placeholder="Ingrese una cédula de identidad" class="form-elementos" required minlength="6" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.length < 6) { this.setCustomValidity('La cédula debe tener al menos 6 dígitos.'); } else { this.setCustomValidity(''); }">
            </div>
            <h2>Nombre de usuario:</h2>
            <!-- NOMBRE DE USUARIO -->
            <div class="input_form">
                <div class="input-formulario">
                    <input type="text" name="nombre_usuario" id="usuario" class="input-caja-formulario" required maxlength="20" placeholder="Ingrese un nombre de usuario">
                </div>
            </div>

            <!-- ROLES -->
            <div class="input_form">
                <h2>Roles</h2>
                <br>
                <select id="role_id" name="role_id" required>
                    <option value="">Seleccione un rol</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo $role['role_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- CONTRASEÑA -->
            <div class="input_form">
                <div class="input-formulario">
                    <input type="password" name="contrasena" id="contrasena_usuario" class="input-caja-formulario" required maxlength="16" placeholder="Ingrese una contraseña">
                </div>
            </div>

            <!-- CONFIRMAR CONTRASEÑA -->
            <div class="input_form">
                <div class="input-formulario">
                    <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" class="input-caja-formulario" required maxlength="16" placeholder="Confirme la contraseña">
                </div>
            </div>

            <!-- PREGUNTAS DE SEGURIDAD -->
            <div class="input_form">
            <h2>Preguntas de seguridad:</h2>
                <br>
                <select id="pregunta_seguridad" name="pregunta_seguridad" required maxlength="30">
                    <option value="">Seleccione una pregunta</option>
                    <?php foreach ($preguntas_seguridad as $pregunta): ?>
                        <option value="<?php echo $pregunta['id_pregunta']; ?>"><?php echo $pregunta['pregunta']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- RESPUESTA DE SEGURIDAD -->
            <div class="input_form">
                <div class="input-formulario">
                    <input type="text" name="respuesta_seguridad" id="respuesta_seguridad" class="input-caja-formulario" required maxlength="30" placeholder="Ingrese una respuesta para la pregunta de seguridad">
                </div>
            </div>

            <button class="boton" type="submit" name="register">Registrarse</button>
        </form>
    </div>
</main>
    </div>
<!-- SCRIPTS -->
<script>
    // DIGITOS EN CEDULA
    function updateMaxLength() {
        const select = document.getElementById('num_id');
        const input = document.getElementById('cedula');
        const selectedValue = select.value;
        // LONGITUD DE CIFRAS
        switch (selectedValue) {
            case 'V': // Cédula de identidad tipo V
                input.maxLength = 8;
                break;
            case 'E': // Cédula de identidad tipo E
                input.maxLength = 10;
                break;
            case 'J': // Cédula de identidad tipo J
                input.maxLength = 10;
                break;
            case 'G': // Cédula de identidad tipo G
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
// SELECTORES
        const sideLinks = document.querySelectorAll('.sidebar .side-menu li a:not(.logout)');
        const menuBar = document.querySelector('.content nav .bx.bx-menu');
        const sideBar = document.querySelector('.sidebar');
        const searchBtn = document.querySelector('.content nav form .form-input button');
        const searchBtnIcon = document.querySelector('.content nav form .form-input button .bx');
        const searchForm = document.querySelector('.content nav form');
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;
// FUNCIONES
        function toggleActiveLink(event) {
            sideLinks.forEach((link) => link.parentElement.classList.remove('active'));
            event.target.parentElement.classList.add('active');
            event.target.parentElement.classList.add('active');
        }
        function toggleSidebar() {
            sideBar.classList.toggle('close');
            const logoName = document.querySelector('.logo-name span');
            const logoImage = document.querySelector('.logo img');
            if (sideBar.classList.contains('close')) {
                logoName.style.display = 'none';
                logoImage.style.display = 'block';
                localStorage.setItem('sidebarClosed', true);
            } else {
                logoName.style.display = 'block';
                logoImage.style.display = 'none';
                localStorage.setItem('sidebarClosed', false);
            }
        }
        function toggleSearchForm() {
            if (window.innerWidth < 576) {
                event.preventDefault();
                searchForm.classList.toggle('show');
                searchBtnIcon.classList.toggle('bx-search');
                searchBtnIcon.classList.toggle('bx-x');
            }
        }
        function updateSidebarOnResize() {
            if (window.innerWidth < 768) {
                sideBar.classList.add('close');
            } else {
                sideBar.classList.remove('close');
            }
        }

        function updateSearchFormOnResize() {
            if (window.innerWidth > 576) {
                searchBtnIcon.classList.replace('bx-x', 'bx-search');
                searchForm.classList.remove('show');
              }
        }
        sideLinks.forEach((link) => link.addEventListener('click', toggleActiveLink));
        menuBar.addEventListener('click', toggleSidebar);
        searchBtn.addEventListener('click', toggleSearchForm);
        window.addEventListener('resize', updateSidebarOnResize);
        window.addEventListener('resize', updateSearchFormOnResize);
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark');
            const isDark = body.classList.contains('dark');
            localStorage.setItem('darkMode', isDark);
            if (isDark) {
                themeToggle.innerHTML = '<i class="bx bx-moon" style="color: #fff;"></i>';
            } else {
                themeToggle.innerHTML = '<i class="bx bx-sun" style="color: #000;"></i>';
            }
        });
        sideBar.addEventListener('transitionend', function() {
            if (sideBar.classList.contains('close')) {
                const logoImage = document.querySelector('.logo img');
                logoImage.style.display = 'block';
            }
        });
        document.addEventListener('DOMContentLoaded', () => {
            const darkMode = localStorage.getItem('darkMode');
            if (darkMode === 'true') {
                body.classList.add('dark');
                themeToggle.innerHTML = '<i class="bx bx-moon" style="color: #fff;"></i>';
            } else {
                themeToggle.innerHTML = '<i class="bx bx-sun" style="color: #000;"></i>';
            }
            const sidebarClosed = localStorage.getItem('sidebarClosed');
            if (sidebarClosed === 'true') {
                sideBar.classList.add('close');
                const logoImage = document.querySelector('.logo img');
                logoImage.style.display = 'block';
                const logoName = document.querySelector('.logo-name span');
                logoName.style.display = 'none';
            }
        });
</script>
</body>
</html>