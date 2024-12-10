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
$mensaje_exitoso = '';
$mensaje_erroneo = '';
// Mensajes de éxito y error
if (isset($_GET['mensaje_exitoso'])) {
    echo "<div class='mensaje_exitoso'>" . $_GET['mensaje_exitoso'] . "</div>";
}
if (isset($_GET['mensaje_erroneo'])) {
    echo "<div class='mensaje_erroneo'>" . $_GET['mensaje_erroneo'] . "</div>";
}
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
// Manejo de la búsqueda de usuario
$usuarioData = null;
$mostrarLista = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['usuario'])) {
        // Cambia 'usuario' por 'nombre_usuario' en la búsqueda
        $usuario_busqueda = $_POST['usuario'];
        $stmt = $conn->prepare('SELECT * FROM usuarios WHERE nombre_usuario = :usuario');
        $stmt->bindParam(':usuario', $usuario_busqueda);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Verifica si se encontró el usuario
        if (!empty($result)) {
            $usuarioData = $result[0];
            $mostrarLista = true;
            $_SESSION['usuarioData'] = $usuarioData;  
        } else {
            $mensaje_erroneo = "<span class='mensaje_erroneo'>Usuario no encontrado</span>";
        }
    }
}
?>
<script>
</script>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="imagenes/mujer3.jpg" type="image/x-icon">
    <title>SIIMM</title>
<!-- ESTILOS -->
    <link rel="stylesheet" href="style_interfaz.css">
    <!-- ICONOS -->
  <link rel="stylesheet" type="text/css" href="boxicons-2.1.4/css/boxicons.min.css">
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
<main>
    <div class="header">
        <div class="left">
            <h1>BUSCAR USUARIO</h1>
        </div>
    </div>  
    <div class="container_tickets">
        <div>
        </div>
        <div class="desarrollo">
            <span class="restriction-message" style="display: none;"><img src='../assets/icons/alert-circle-outline.svg'> Solo se permiten letras y números.</span>
            <?php echo $mensaje_erroneo?>
            <form method="POST" id="form">
                <label for="">Ingresa el usuario</label>
                <input type="text" name="usuario" id="usuario" placeholder="usuario123" required>
                <br>
                <button type="submit" class="boton">Buscar Usuario</button>
            </form>
        </div>
    </div>
<br><br>
    <?php if ($mostrarLista && $usuarioData) {?>
    <div class="lista">
        <div class="titulo">
            <h1>Cambiar Datos</h1>
        </div>
        <div class="desarrollo">
<!-- FORMULARIO -->
            <form method="POST" action="actualizar.php">
                <label for="">Nombre:</label>
                <input type="text" name="nombre" value="<?php echo $usuarioData['nombre'];?>" required>
                <br>
                <label for="">Apellido:</label>
                <input type="text" name="apellido" value="<?php echo $usuarioData['apellido'];?>" required>
                <br>
                <label for="">Correo:</label>
                <input type="email" name="correo" value="<?php echo $usuarioData['correo'];?>" required>
                <br>
                <label for="">Cédula:</label>
                <input type="text" name="cedula" value="<?php echo $usuarioData['cedula'];?>" required>
                <br>
                <label for="">Oficina:</label>
                <input type="text" name="oficina" value="<?php echo $usuarioData['oficina'];?>" required>
                <br>
                <label for="">Departamento:</label>
                <input type="text" name="departamento" value="<?php echo $usuarioData['departamento'];?>" required>
                <br>
                <label for="">Cargo:</label>
                <input type="text" name="cargo" value="<?php echo $usuarioData['cargo'];?>" required>
                <br>
                <br>
                <label for="">Contraseña actual:</label>
                <input type="password" name="contrasena_actual" required>
                <br>
                <input type="hidden" name="usuario" value="<?php echo $usuarioData['nombre_usuario']; ?>">
                <button type="submit" class="boton">Actualizar</button>
                <br>
                <br>
            </form>
<!-- FORMULARIO DE CAMBIAR CONTRASEÑA -->
            <button id="cambiar-contrasena">Cambiar contraseña</button>
            <div id="contrasena-container" style="display: none;">
                <form method="POST" action="actualizar_contrasena.php">
                    <label for="">Contraseña actual:</label>
                    <input type="password" name="contrasena_actual" required>
                    <br>
                    <label for="">Nueva contraseña:</label>
                    <input type="password" name="contrasena_nueva" >
                    <br>
                    <label for="">Confirmar contraseña:</label>
                    <input type="password" name="contrasena_confirmar" >
                    <input type="hidden" name="usuario" value="<?php echo $usuarioData['nombre_usuario']; ?>">
                    <button type="submit" class="boton">Cambiar contraseña</button>
                </form>
            </div>
    <?php }?>   
    </div>
</main>
<!-- ALERTAS -->
    <script>
        setTimeout(function() {
    document.querySelectorAll('.mensaje_exitoso,.mensaje_erroneo').forEach(function(element) {
        element.style.display = 'none';
    });
}, 5000);
    </script>
<!-- CONTRASEÑA -->
<script>
    document.getElementById('cambiar-contrasena').addEventListener('click', function() {
        var contrasenaContainer = document.getElementById('contrasena-container');
        if (contrasenaContainer.style.display === 'none') {
            contrasenaContainer.style.display = 'block';
        } else {
            contrasenaContainer.style.display = 'none';
        }
    });
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