<script>
</script>
<?php
session_start();
include 'conexion_bd.php'; 

// VERIFICA SI YA EXISTE EL USUARIO
if (!isset($_SESSION['usuario'])) {
    header("Location: admin.php");
    exit();
}

// OBTIENE USUARIO
$usuario = $_SESSION['usuario'];

// OBTIENE ID DE ROL_ID
$stmt = $conn->prepare("SELECT role_id FROM usuarios WHERE nombre_usuario = :usuario LIMIT 1");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
    $role_id = $result['role_id']; 
} else {
    header("Location: admin.php");
    exit();
}

// OBTENER PERMISOS
$stmt = $conn->prepare("SELECT permiso FROM permisos WHERE rol_id = :role_id");
$stmt->bindParam(':role_id', $role_id);
$stmt->execute();
$permisos = $stmt->fetchAll(PDO::FETCH_COLUMN);

// DEFINIR PERMISOS
$permisosPorRol = [
    1 => ['ver_panel', 'ver_usuarios', 'editar_usuarios', 'crear_usuarios', 'crear_movimientos', 'ver_graficas'], 
    2 => ['ver_panel', 'ver_usuarios', 'crear_movimientos', 'ver_graficas'], 
    3 => ['ver_panel', 'crear_movimientos'], 
    4 => ['ver_panel'],
];
// VERIFICA PERMISOS
$permisosPermitidos = $permisosPorRol[$role_id] ?? [];

// Inicializa variables para mensajes
$mensaje_exitoso = '';
$mensaje_erroneo = '';
$mensaje_error = ''; 

// MANEJO DE BUSQUEDA
if (isset($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];

    // Consulta a la base de datos usando el operador de igualdad
    $stmt = $conn->prepare("SELECT * FROM datos_ayudantes WHERE cedula = :busqueda LIMIT 1");
    $stmt->bindParam(':busqueda', $busqueda);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {

        $mensaje_exitoso = "Resultados encontrados:";
        $vista_previa = "<div class='resultado'>";
        $vista_previa .= "<h2>Coincidencias encontradas:</h2>";
        $vista_previa .= "<p>Nombre y apellido: " . htmlspecialchars($resultado['nombre_apellido']) . "</p>";
        $vista_previa .= "<p>Número de identificación: " . htmlspecialchars($resultado['num_id']) . "</p>";
        $vista_previa .= "<p>Cédula del ayudante: " . htmlspecialchars($resultado['cedula']) . "</p>";
        $vista_previa .= "<p>Cargo del ayudante: " . htmlspecialchars($resultado['descripcion_cargo']) . "</p>";
        
        $vista_previa .= "</div>";
    } else {
        // Si no hay resultados, establecer mensaje de error
        $mensaje_erroneo = "No se encontraron coincidencias.";
    }
}

// MANEJO DE CONTRASEÑA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contrasena'])) {
    $contrasena_movimiento = $_POST['contrasena'];

    if (strlen($contrasena_movimiento) < 8) {
        $mensaje_error = "La contraseña debe tener al menos 8 caracteres";
    } else {
        // Verificar si la búsqueda fue exitosa
        if (isset($resultado) && $resultado) {
            $stmt = $conn->prepare("SELECT contrasena_movimiento FROM form_mujeres LIMIT 1"); 
            $stmt->execute();
            $resultado_contrasena = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debugging
            var_dump($resultado_contrasena); 
            echo "Cédula del ayudante: " . htmlspecialchars($resultado['cedula']);

            if ($resultado_contrasena) {
                // Verifica la contraseña
                if (password_verify($contrasena_movimiento, $resultado_contrasena['contrasena_movimiento'])) {
                    // Redirigir a form_movimientos_ayudantes con la cédula
                    header("Location: form_movimientos_ayudantes.php?cedula_ayudantes=" . urlencode($resultado['cedula']) . "&nombre_apellido=" . urlencode($resultado['nombre_apellido']) . "&descripcion_cargo=" . urlencode($resultado['descripcion_cargo']) . "&num_id=" . urlencode($resultado['num_id']));
                    exit();
                } else {
                    $mensaje_error = "Contraseña incorrecta";
                }
            } else {
                $mensaje_error = "No se encontró la contraseña.";
            }
        } else {
            $mensaje_error = "No se encontró el ayudante para verificar la contraseña.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="imagenes/mujer3.jpg" type="image/x-icon">
    <title>SICMMM</title>
    <link rel="stylesheet" href="style_interfaz.css">
    <link rel="stylesheet" href="style_graf.css">
<!-- ICONOS -->
  <link rel="stylesheet" type="text/css" href="boxicons-2.1.4/css/boxicons.min.css">
</head>
<body>
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
<form action="#" method="GET" role="search">
    <div class="form-input">
        <label for="searchInput" class="visually-hidden"></label>
        <input type="search" id="searchInput" name="q" class="buscador" placeholder="Buscar" required>
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
            <h1>Consultas</h1>
        </div>
    </div>
    <div class="consulta-contenedor">
    <form method="GET" action="" onsubmit="return validarFormulario()">
        <input type="number" name="busqueda" placeholder="Cédula de identidad" id="consulta-input" required>
        <button type="submit" id="consulta-boton">Buscar</button>
    </form>
    <!-- Contenedor para mostrar mensajes de éxito o error -->
    <div id="mensaje" class="mensaje"></div>
</div>
    <div class="resultados-contenedor">
    <!-- Mostrar la vista previa de los resultados si existe -->
    <?php if (isset($vista_previa)): ?>
        <div class="vista-previa">
            <?php echo $vista_previa; ?>
        </div>
        <button id="openModal">Ingresar Datos</button>
    <?php endif; ?>
</div>
    <!-- Mostrar mensajes de éxito o error -->
    <?php if ($mensaje_exitoso): ?>
        <div class='mensaje_exitoso'><?php echo $mensaje_exitoso; ?></div>
    <?php endif; ?>
    <?php if ($mensaje_erroneo): ?>
        <div class='mensaje_erroneo'><?php echo $mensaje_erroneo; ?></div>
    <?php endif; ?>
<!-- Modal -->
<div id="modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Ingrese su contraseña</h2>
        <form id="passwordForm" method="POST" action="">
            <input type="password" name="contrasena" placeholder="Contraseña" required minlength="8">
            <button type="submit">Enviar</button>
        </form>
        <div id="mensaje_error" style="color: red;"><?php echo $mensaje_error; ?></div>
    </div>
</div>
</div>
<!-- SCRIPTS -->
<script>
    function validarFormulario() {
        const input = document.getElementById('consulta-input');
        const valor = input.value;
        const mensajeDiv = document.getElementById('mensaje');

        // Limpiar mensajes anteriores
        mensajeDiv.innerHTML = '';
        mensajeDiv.className = 'mensaje'; 

        // Verifica que el valor tenga al menos 6 dígitos
        if (valor.length < 6) {
            mensajeDiv.className = 'mensaje_erroneo'; 
            mensajeDiv.innerHTML = 'Por favor, ingrese al menos 6 dígitos.';
            return false; 
        }
        
        mensajeDiv.className = 'mensaje_exitoso'; 
        mensajeDiv.innerHTML = 'Búsqueda en curso...'; 
        return true; 
    }
</script>
<script>
// Obtener el modal
var modal = document.getElementById("modal");

// Obtener el botón que abre el modal
var btn = document.getElementById("openModal");

// Obtener el elemento <span> que cierra el modal
var span = document.getElementById("closeModal");

// Cuando el usuario hace clic en el botón, abrir el modal 
btn.onclick = function() {
    modal.style.display = "block";
}

// Cuando el usuario hace clic en <span> (x), cerrar el modal
span.onclick = function() {
    modal.style.display = "none";
}

// Cuando el usuario hace clic fuera del modal, cerrarlo
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
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
    <!-- ALERTAS -->
    <script>
        setTimeout(function() {
    document.querySelectorAll('.mensaje_exitoso,.mensaje_erroneo').forEach(function(element) {
        element.style.display = 'none';
    });
}, 5000);
    </script>
</body>
</html>