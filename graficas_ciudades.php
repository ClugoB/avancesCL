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
// PERMISOS
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

// OBTENER DATOS PARA LAS GRÁFICAS
$stmt = $conn->prepare("SELECT ciudad_ubicado, estado_ubicado, municipio_ubicado, parroquia_ubicado, nombre_movimiento, SUM(cantidad_hombres) as total_hombres, SUM(cantidad_mujeres) as total_mujeres FROM form_mujeres GROUP BY ciudad_ubicado, estado_ubicado, municipio_ubicado, parroquia_ubicado, nombre_movimiento");
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DATOS PARA CHARTS
$ciudades_movimientos = []; // Cambiado para almacenar ciudad y movimiento
$total_hombres = [];
$total_mujeres = [];
foreach ($datos as $dato) {
    // Combina ciudad y nombre del movimiento
    $ciudades_movimientos[] = htmlspecialchars($dato['ciudad_ubicado'] . ' - ' . $dato['nombre_movimiento']);
    $total_hombres[] = $dato['total_hombres'];
    $total_mujeres[] = $dato['total_mujeres'];
}
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
                    <h1>Datos por Ciudad, Estado, Municipio y Parroquia</h1>
                </div>
            </div>
            <div>
            <div>
                <!-- SELECCIONAR-->
    <label for="dataGroup">Selecciona el nivel de datos a mostrar:</label>
    <select id="dataGroup">
        <option value="ciudad">Ciudades</option>
        <option value="estado">Estados</option>
        <option value="municipio">Municipios</option>
        <option value="parroquia">Parroquias</option>
    </select>
</div>
<!-- SELECCIONAR-->
    <label for="dataType">Selecciona el tipo de datos a mostrar:</label>
    <select id="dataType">
        <option value="gender">Cantidad de Hombres y Mujeres</option>
        <option value="movements">Movimientos</option>
    </select>
</div>
            <!-- GRÁFICA DE MUJERES Y HOMBRES EN CIUDADES-->
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
            <div>
                <canvas id="myChart"></canvas>
            </div>
            <h2>Datos por Ciudad, Estado, Municipio y Parroquia</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th>Municipio</th>
                        <th>Parroquia</th>
                        <th>Nombre del Movimiento</th>
                        <th>Cantidad de Hombres</th>
                        <th>Cantidad de Mujeres</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos as $dato): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dato['ciudad_ubicado']); ?></td>
                            <td><?php echo htmlspecialchars($dato['estado_ubicado']); ?></td>
                            <td><?php echo htmlspecialchars($dato['municipio_ubicado']); ?></td>
                            <td><?php echo htmlspecialchars($dato['parroquia_ubicado']); ?></td>
                            <td><?php echo htmlspecialchars($dato['nombre_movimiento']); ?></td>
                            <td><?php echo htmlspecialchars($dato['total_hombres']); ?></td>
                            <td><?php echo htmlspecialchars($dato['total_mujeres']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
    <!-- SCRIPTS -->
    <script>
// Obtener el contexto del canvas
const ctx = document.getElementById('myChart').getContext('2d');

// Inicializar el gráfico
let myChart = new Chart(ctx, {
    type: 'bar', 
    data: {
        labels: <?php echo json_encode($ciudades_movimientos); ?>, 
        datasets: [{
            label: 'Cantidad de Hombres',
            data: <?php echo json_encode($total_hombres); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.8)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }, {
            label: 'Cantidad de Mujeres',
            data: <?php echo json_encode($total_mujeres); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderColor: 'rgba(255, 99, 132, 1)',
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
                    text: 'Cantidad',
                    font: {
                        size: 18,
                        family: 'Arial',
                        weight: 'bold',
                        color: 'rgba(255, 255, 255, 1)' 
                    }
                },
                ticks: {
                    font: {
                        size: 14,
                        family: 'Arial',
                        color: 'rgba(255, 255, 255, 1)' 
                    }
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.5)'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Ciudades y Movimientos',
                    font: {
                        size: 18,
                        family: 'Arial',
                        weight: 'bold',
                        color: 'rgba(255, 255, 255, 1)' 
                    }
                },
                ticks: {
                    font: {
                        size: 14,
                        family: 'Arial',
                        color: 'rgba(255, 255, 255, 1)' 
                    }
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.5)'
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    font: {
                        size: 16,
                        family: 'Arial',
                        color: 'rgba(255, 255, 255, 1)' 
                    }
                }
            }
        }
    }
});

// Función para actualizar el gráfico
function updateChart() {
    const selectedType = document.getElementById('chartType').value;
    const selectedDataType = document.getElementById('dataType').value;
    const selectedDataGroup = document.getElementById('dataGroup').value; 

    myChart.destroy(); 

    let datasets;

    if (selectedDataType === 'gender') {
        datasets = [{
            label: 'Cantidad de Hombres',
            data: <?php echo json_encode($total_hombres); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.8)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }, {
            label: 'Cantidad de Mujeres',
            data: <?php echo json_encode($total_mujeres); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }];
    } else if (selectedDataType === 'movements') {
        datasets = [{
            label: 'Movimientos',
            data: <?php echo json_encode($total_hombres); ?>, 
            backgroundColor: 'rgba(153, 102, 255, 0.8)',
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1
        }];
    }

    // Ajustar las etiquetas según el grupo seleccionado
    let labels;
    if (selectedDataGroup === 'ciudad') {
        labels = <?php echo json_encode($ciudades_movimientos); ?>;
    } else if (selectedDataGroup === 'estado') {
        labels = <?php echo json_encode(array_unique(array_column($datos, 'estado_ubicado'))); ?>; 
    } else if (selectedDataGroup === 'municipio') {
        labels = <?php echo json_encode(array_unique(array_column($datos, 'municipio_ubicado'))); ?>; 
    } else if (selectedDataGroup === 'parroquia') {
        labels = <?php echo json_encode(array_unique(array_column($datos, 'parroquia_ubicado'))); ?>; 
    }

    myChart = new Chart(ctx, {
        type: selectedType,
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: selectedDataType === 'gender' ? 'Cantidad' : 'Movimientos',
                        font: {
                            size: 18,
                            family: 'Arial',
                            weight: 'bold',
                            color: 'rgba(255, 255, 255, 1)' 
                        }
                    },
                    ticks: {
                        font: {
                            size: 14,
                            family: 'Arial',
                            color: 'rgba(255, 255, 255, 1)' 
                        }
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.5)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: selectedDataGroup === 'ciudad' ?
                        'Ciudades' : 
                        selectedDataGroup === 'estado' ? 'Estados' : 
                        selectedDataGroup === 'municipio' ? 'Municipios' : 
                        'Parroquias',
                        font: {
                            size: 18,
                            family: 'Arial',
                            weight: 'bold',
                            color: 'rgba(255, 255, 255, 1)' 
                        }
                    },
                    ticks: {
                        font: {
                            size: 14,
                            family: 'Arial',
                            color: 'rgba(255, 255, 255, 1)' 
                        }
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.5)'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 16,
                            family: 'Arial',
                            color: 'rgba(255, 255, 255, 1)' 
                        }
                    }
                }
            }
        }
    });
}

// Escuchar cambios en los selects
document.getElementById('chartType').addEventListener('change', updateChart);
document.getElementById('dataType').addEventListener('change', updateChart);
document.getElementById('dataGroup').addEventListener('change', updateChart); 
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