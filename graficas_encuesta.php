<script>
</script>
<?php
session_start();
include 'conexion_bd.php'; 
// VERIFICA SI YA EXISTE UNA SESION
if (!isset($_SESSION['usuario'])) {
    header("Location: admin.php");
    exit();
}
// OBTENER USUARIO
$usuario = $_SESSION['usuario'];
// OBTENER ID
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
// ROLES PERMITIDOS
$roles_permitidos = [1, 2]; 

// VERIFICA SI ESTA EL ROL PERMITIDO
if (!in_array($role_id, $roles_permitidos)) {
    header("Location: acceso_denegado.php");
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
// OBTENER DATOS PARA LAS GRÁFICAS DE ENCUESTA
try {
    // Consulta de Encuesta
    $totalQuery = $conn->query("SELECT COUNT(*) FROM encuesta");
    $totalRespuestas = $totalQuery->fetchColumn();

    // Contar "Sí" y "No" para cada pregunta
    $siNoCounts = [];
    for ($i = 1; $i <= 68; $i++) {
        $siCountQuery = $conn->prepare("SELECT COUNT(*) FROM encuesta WHERE pregunta$i = 'Sí'");
        $siCountQuery->execute();
        $noCountQuery = $conn->prepare("SELECT COUNT(*) FROM encuesta WHERE pregunta$i = 'No'");
        $noCountQuery->execute();
        $siCount = $siCountQuery->fetchColumn();
        $noCount = $noCountQuery->fetchColumn();
        // Solo agregar la pregunta si hay respuestas "Sí" o "No"
        if ($siCount > 0 || $noCount > 0) {
            $siNoCounts[$i] = ['si' => $siCount, 'no' => $noCount];
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$titulosPreguntas = [
    1 => "¿Estás familiarizado con el concepto de parto humanizado?",
    2 => "¿Has tenido alguna experiencia previa de parto humanizado?",
    6 => "¿Crees que es importante respetar las decisiones de la mujer durante el parto?",
    7 => "¿Consideras que la comunicación con el personal médico es fundamental en un parto humanizado?",
    8 => "¿Recomendarías el parto humanizado a otras mujeres embarazadas?",
    9 => "¿Estás familiarizado con el Plan de Prevención y Reducción del Embarazo a Temprana Edad y en la Adolescencia?",
    10 => "¿Crees que el acceso a información sobre anticoncepción es importante para prevenir embarazos en la adolescencia?",
    12 => "¿Has participado en alguna actividad o programa relacionado con la prevención del embarazo en la adolescencia?",
    15 => "¿Estás familiarizado con el concepto de Unidades Móviles de Atención?",
    16 => "¿Has utilizado alguna vez una Unidad Móvil de Atención para acceder a servicios de salud u otros servicios sociales?",
    19 => "¿Crees que las Unidades Móviles de Atención son una alternativa efectiva a los servicios tradicionales de atención?",
    20 => "¿Consideras que las Unidades Móviles de Atención son un recurso importante para comunidades rurales o de difícil acceso?",
    24 => "¿Consideras que la información proporcionada en Farmamujer es confiable y precisa?",
    28 => "¿Recomendarías Farmamujer a tus amigos y familiares en busca de información sobre salud y bienestar?",
    29 => "¿Estás familiarizado con el Instituto Nacional de la Mujer (INAMUJER)?",
    30 => "¿Crees que el INAMUJER ha sido efectivo en la promoción de los derechos de las mujeres en tu comunidad?",
    31 => "¿Has utilizado los servicios o recursos que ofrece el INAMUJER?",
    32 => "¿Consideras que el INAMUJER ha tenido un impacto positivo en la lucha contra la violencia de género?",
    35 => "¿Sientes que el INAMUJER es una institución importante para apoyar y empoderar a las mujeres en el país?",
    36 => "¿Has participado en alguna campaña o evento organizado por el INAMUJER?",
    38 => "¿Recomendarías los servicios del INAMUJER a otras mujeres que necesiten apoyo en temas de igualdad y empoderamiento?",
    39 => "¿Sabes cuál es el objetivo principal de la Defensoría Nacional de los Derechos de la Mujer?",
    40 => "¿Sabes qué tipo de servicios ofrece la Defensoría Nacional de los Derechos de la Mujer?",
    41 => "¿Sabes cómo puedes contactar a la Defensoría Nacional de los Derechos de la Mujer en caso de necesitar ayuda o asesoramiento?",
    42 => "¿Sabes dónde queda la ubicación física de la Oficina de Atención a la Víctima más cercana a tu domicilio?",
    43 => "¿Conoces el horario de atención de la Oficina de Atención a la Víctima y cuál es el procedimiento para solicitar una cita?",
    44 => "¿Sabes qué tipo de servicios ofrece la Oficina de Atención a la Víctima a las personas que han sufrido algún tipo de delito o violencia?",
    45 => "¿Conoces el procedimiento a seguir para presentar una denuncia en la Oficina de Atención a la Víctima y cuáles son los plazos y requisitos necesarios?",
    46 => "¿Sabías que la atención en la Oficina de Atención a la Víctima es confidencial y gratuita?",
    48 => "¿Conoces el plan de financiamiento del Ministerio de la Mujer?",
    49 => "¿Te gustaría recibir apoyo financiero a través de este plan?",
    52=> "¿Has intentado acceder a este plan anteriormente?",
    55 => "¿Conoces el Plan de Emprendimiento del Ministerio de la Mujer?",
    56 => "¿Te gustaría recibir apoyo para iniciar o mejorar tu emprendimiento?",
    59 => "¿Has intentado acceder a este plan anteriormente?",
    63=> "¿Conoces el Plan de Adjudicación de tierras del Ministerio de la Mujer?",
    64=> "¿Eres mujer y trabajas en el sector agrícola?",
    65=> "¿Consideras que el acceso a tierras es un problema para las mujeres del sector agrícola?",
    67 => "¿Te gustaría participar en programas de capacitación relacionados con la agricultura?",
];
$conn = null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="imagenes/mujer3.jpg" type="image/x-icon">
    <title>SIIMM</title>
    <link rel="stylesheet" href="style_interfaz.css">
    <link rel="stylesheet" type="text/css" href="boxicons-2.1.4/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
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
            <form action="#" method="GET" role="search">
                <div class="form-input">
                    <label for="searchInput" class="visually-hidden"></label>
                    <input type="search" id="searchInput" name="q" class="buscador" placeholder="Buscar" required>
                    <button class="search-btn" type="submit"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <button id="theme-toggle" class="theme-toggle">
                <i class='bx bx-sun' id="theme-icon"></i>
            </button>
            <a href="#" class="notif">
                <i class='bx bx-bell'></i>
                <span class="count">12</span>
            </a>
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
            <h1>Gráficas de las Encuestas</h1>
        </div>
    </div>
    <!-- GRÁFICA DE RESPUESTAS -->
    <div>
    <label for="chartType">Selecciona el tipo de gráfico:</label>
    <select id="chartType">
        <option value="bar">Barras</option>
        <option value="pie">Circular</option>
        <option value="line">Línea</option>
        <option value="doughnut">Donut</option>
        <option value="radar">Radar</option>
        <option value="polarArea">Área Polar</option>
    </select>
</div>
    <h2 id="tituloGrafica">Selecciona una pregunta para ver los resultados</h2>
    <canvas id="chartPregunta"></canvas>

    <script>
        const siNoCounts = <?php echo json_encode($siNoCounts); ?>; 
        let chart; 
        function mostrarGrafica(pregunta) {
            const siCount = siNoCounts[pregunta]['si'];
            const noCount = siNoCounts[pregunta]['no'];
            const titulo = '<?php echo "Resultados"; ?>';
            const preguntaTitulo = <?php echo json_encode($titulosPreguntas); ?>;
            const tituloPregunta = preguntaTitulo[pregunta] ? preguntaTitulo[pregunta] : "Título no disponible";
            document.getElementById('tituloGrafica').innerText = '<?php echo "Resultados de "; ?>' + tituloPregunta;
            if (chart) {
                chart.destroy();
            }
            const ctx = document.getElementById('chartPregunta').getContext('2d');
            chart = new Chart(ctx, {
                type: 'bar', 
                data: {
                    labels: ['Sí', 'No'],
                    datasets: [{
                        label: 'Respuestas',
                        data: [siCount, noCount],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 99, 132, 0.5)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad de Respuestas'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: 'rgba(0, 0, 0, 0.7)' 
                            }
                        },
                        title: {
                            display: true,
                            text: titulo,
                            color: 'rgba(0, 0, 0, 0.9)' 
                        }
                    }
                }
            });
        }
    </script>
<!-- Contenedor para la navegación y los botones -->
<div id="navegacion">
    <button id="flechaIzquierda" aria-label="Desplazar a la izquierda">
        <i class='bx bx-chevron-left'></i> 
    </button>
    <div id="contenedorBotones">
        <?php foreach ($siNoCounts as $pregunta => $counts): ?>
            <button onclick="mostrarGrafica('<?php echo $pregunta; ?>')">
                <?php 
                echo isset($titulosPreguntas[$pregunta]) ? $titulosPreguntas[$pregunta] : "Título no disponible para la pregunta $pregunta"; 
                ?>
            </button>
        <?php endforeach; ?>
    </div>
    <button id="flechaDerecha" aria-label="Desplazar a la derecha">
        <i class='bx bx-chevron-right'></i> 
    </button>
</div>
</main>
<!-- CHARTS PARA GRÁFICAS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
<script>
    // BOTON DE BOTONES EN GRAFICA DE ENCUESTA
    const contenedor = document.getElementById('contenedorBotones');
    const flechaIzquierda = document.getElementById('flechaIzquierda');
    const flechaDerecha = document.getElementById('flechaDerecha');
    // Función para actualizar el estado de las flechas
    function updateArrowState() {
        const maxScrollLeft = contenedor.scrollWidth - contenedor.clientWidth;
        flechaIzquierda.disabled = contenedor.scrollLeft === 0;
        flechaDerecha.disabled = contenedor.scrollLeft >= maxScrollLeft;
        // Cambiar el estilo de las flechas según su estado
        flechaIzquierda.classList.toggle('disabled', flechaIzquierda.disabled);
        flechaDerecha.classList.toggle('disabled', flechaDerecha.disabled);
    }
    // Desplazamiento dinámico basado en el ancho de los botones
    function scrollContainer(direction) {
        const buttonWidth = contenedor.querySelector('button').offsetWidth;
        const marginRight = parseInt(getComputedStyle(contenedor.querySelector('button')).marginRight);
        const scrollAmount = buttonWidth + marginRight;
        contenedor.scrollTo({
            top: 0,
            left: contenedor.scrollLeft + (scrollAmount * direction),
            behavior: 'smooth'
        });
        updateArrowState(); 
    }
    // Manejadores de eventos para las flechas
    flechaDerecha.addEventListener('click', () => scrollContainer(1));
    flechaIzquierda.addEventListener('click', () => scrollContainer(-1));
    // Inicializar el estado de las flechas
    updateArrowState();
    contenedor.addEventListener('scroll', updateArrowState);
</script>
</body>
</html>