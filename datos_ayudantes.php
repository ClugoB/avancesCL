<script>
</script>
<?php
session_start();
include 'conexion_bd.php'; 
// VERIFICA SI INICIO SESIÓN
if (!isset($_SESSION['usuario'])) {
// REDIRIGE A ADMIN.PHP SI NO HA INICIADO SESIÓN
header("Location: admin.php");
exit();
}
// OBTENER NOMBRE DEL USUARIO
$usuario = $_SESSION['usuario'];

// OBTENER EL ID DEL ROL
$stmt = $conn->prepare("SELECT role_id FROM usuarios WHERE nombre_usuario = :usuario LIMIT 1");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
$role_id = $result['role_id']; 
} else {
// SI NO SE ENCUENTRA EL USUARIO REDIRIGE
header("Location: admin.php");
exit();
}
// ROLES PERMITIDOS
$roles_permitidos = [1, 2]; 
// Verificar si el usuario tiene un rol permitido
if (!in_array($role_id, $roles_permitidos)) {
// Redirige a la página de acceso denegado si no tiene permisos
header("Location: acceso_denegado.php");
exit();
}
// OBTENER PERMISOS DE ROL
$stmt = $conn->prepare("SELECT permiso FROM permisos WHERE rol_id = :role_id");
$stmt->bindParam(':role_id', $role_id);
$stmt->execute();
$permisos = $stmt->fetchAll(PDO::FETCH_COLUMN);
// DEFINIR PERMISOS POR ROL
$permisosPorRol = [
    1 => ['ver_panel', 'ver_usuarios', 'editar_usuarios', 'crear_usuarios', 'crear_movimientos', 'ver_graficas'], 
    2 => ['ver_panel', 'ver_usuarios', 'crear_movimientos', 'ver_graficas'], 
    3 => ['ver_panel', 'crear_movimientos'], 
    4 => ['ver_panel'],
];
// VERIFICAR PERMISOS SEGÚN EL ROL
$permisosPermitidos = $permisosPorRol[$role_id] ?? [];
// FUNCIÓN PARA OBTENER LOS DATOS DE LA BASE DE DATOS
function obtenerDatos($conn, $tabla, $columna) {
    $sql = "SELECT $columna FROM $tabla ORDER BY $columna";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
// OBTENER LOS DATOS PARA LOS CAMPOS DESPLEGABLES
    $num_identidad = obtenerDatos($conn, 'num_identidad', 'identidad');
    $codigos_operadoras = obtenerDatos($conn, 'codigos_operadoras', 'codigo');
    $estados = obtenerDatos($conn, 'estados', 'estado');
    $ciudades = obtenerDatos($conn, 'ciudades', 'ciudad');
    $municipios = obtenerDatos($conn, 'municipios', 'municipio');
    $parroquias = obtenerDatos($conn, 'parroquias', 'parroquia');
    $comunas = obtenerDatos($conn, 'comunas', 'comuna');
    $tipo_movimiento = obtenerDatos($conn, 'tipo_movimiento', 'tipos_movimientos');
    $cargos_ayudantes = obtenerDatos($conn, 'cargos_ayudantes', 'cargo');
?>  
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="shortcut icon" href="imagenes/mujer3.jpg" type="image/x-icon">
<title>SIIMM</title>
<!-- DATATABLE CSS -->
  <link rel="stylesheet" type="text/css" href="DataTables-1.13.8/css/jquery.dataTables.min.css">
<!-- ICONOS -->
  <link rel="stylesheet" type="text/css" href="boxicons-2.1.4/css/boxicons.min.css">
<!-- ESTILOS -->
  <link rel="stylesheet" type="text/css" href="style_consultas.css">
  <link rel="stylesheet" href="style_interfaz.css">
  <link rel="stylesheet" href="style_graf.css">
</head>
<!-- SIDEBAR --> 
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
<h1>INFORMACIÓN DE AYUDANTES</h1>
</div>
</div>
<br>
<!-- CONSULTA DE DATOS -->
<table id="table" class="display">
<thead>
<tr>
<th>Nombre y apellido</th>
<th>Descripción del cargo</th>
<th>cedula de identidad</th>
<th>id del movimiento en el que se encuentra</th>
<th>Acciones</th>
</tr>
</thead>
<tbody id="table_users">
</tbody>
</table>  
<div id="usuario_detalles">
    <h2>Detalles del Usuario</h2>
    <div class="detalles-container">
        <div class="detalles_columna izquierda">
            <p id="nombre_apellido"></p>
            <p id="descripcion_cargo"></p>
            <p id="num_id"></p>
            <p id="cedula"></p>
            <p id="ciudad"></p>
            <p id="estado"></p>
            <p id="municipio"></p>
            <p id="parroquia"></p>
            <p id="embarazada"></p>
            <p id="tiempo_embarazada"></p>
        </div>
        <div class="muro"></div>
        <div class="detalles_columna derecha">
            <p id="discapacidad"></p>
            <p id="tipo_discapacidad"></p>
            <p id="genero"></p>
            <p id="tipo_genero"></p>
            <p id="correo"></p>
            <p id="fecha_nacimiento"></p>
            <p id="fecha_registro"></p>
        </div>
    </div>
    <button id="cerrar_detalles">Cerrar</button>
</div>
<!-- Modal para editar -->
<div id="edit-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close" id="close-edit-modal">&times;</span>
        </div>
        <h2>Editar Datos</h2>
        <form id="edit-form" class="form">
            <div class="input_form">
                <label for="nombre_lider">Nombre del Líder:</label>
                <input type="text" id="nombre_lider" name="nombre_lider" class="form-elementos input_modal" required>
            </div>
            
            <div class="input_form">
                <label for="apellido_lider">Apellido del líder:</label>
                <input type="text" id="apellido_lider" name="apellido_lider" class="form-elementos input_modal" required>
            </div>

            <!-- NUMERO DE IDENTIDAD -->
            <div class="caja_form">
                <div class="input_form">
                    <label for="num_id_lider">Número de identidad:</label><br>
                    <select id="num_id_lider" name="num_id_lider" class="form-elementos" required onchange="updateMaxLength('num_id_lider', 'cedula_lider')">
                        <option value="Seleccione el número telefónico">Seleccione el número telefónico</option>
                        <?php foreach ($num_identidad as $identidad): ?>
                            <option value="<?php echo htmlspecialchars($identidad); ?>"><?php echo htmlspecialchars($identidad); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- CEDULA DE IDENTIDAD -->
                <div class="input_form">
                    <label for="cedula_lider">Número de identidad:</label>
                    <input type="number" id="cedula_lider" name="cedula_lider" placeholder="Ingrese una cédula de identidad" class="form-elementos input_modal" required maxlength="10" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                </div>
            </div>

            <input type="hidden" id="id" name="id"> 
            <button type="submit" class="btn">Guardar Cambios</button>
        </form>
    </div>
</div>
<!-- Modal para seleccionar tipo de gráfica -->
<div id="chart-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
        </div>
        <h2>Seleccionar Tipo de Gráfica</h2>
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
        <button id="next-step">Siguiente</button>
    </div>
</div>
<!-- Modal para seleccionar tipo de información -->
<div id="info-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <span class="back" style="cursor: pointer;"><i class='bx bx-chevron-left'></i></span>
            <span class="close">&times;</span>
        </div>
        <h2>Seleccionar Tipo de Información</h2>
        <select id="info-type">
            <option value="demographics">Demografía</option>
            <option value="sales">Ubicaciones</option>
        </select>
        <button id="next-info-step">Siguiente</button>
    </div>
</div>

<!-- Modal para seleccionar estadísticas -->
<div id="stats-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <span class="back" style="cursor: pointer;"><i class='bx bx-chevron-left'></i></span>
            <span class="close">&times;</span>
        </div>
        <h2>Seleccionar Estadísticas</h2>
        <select id="stats-select">
        </select>
        <button id="generate-chart">Generar Gráfica</button>
    </div>
</div>
<canvas id="myChart" style="display: none;"></canvas>
<!-- SCRIPTS -->
<script>
    // DIGITOS EN CEDULA
    function updateMaxLength(selectId, inputId) {
        const select = document.getElementById(selectId);
        const input = document.getElementById(inputId);
        const selectedValue = select.value;

        // LONGITUD DE CIFRAS
        switch (selectedValue) {
            case 'V':
                input.maxLength = 8;
                break;
            case 'E': 
            case 'J': 
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
    // Llamar a la función para establecer la longitud inicial
    window.onload = function() {
        updateMaxLength('num_id_lider', 'cedula_lider');
    };
</script>
<script type="text/javascript" charset="utf8" src="jquery-3.7.1.min.js"></script>
<script type="text/javascript" charset="utf8" src="DataTables-1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
$(document).ready(function() {
    const table = $('#table').DataTable();
    let currentUser ; 

    const listUsers = async () => {
        try {
            const response = await fetch('datos_ayudantes_conexion.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            console.log(data);
            data.forEach((user) => {
                let properties = ['nombre_apellido', 'descripcion_cargo', 'cedula', 'id_movimiento'];
                let row = [];
                properties.forEach(property => {
                    row.push(user[property] || 'N/A');
                });
// ICONOS DE ACCIONES
let actions = `
    <i class="bx bx-search view-more" data-user='${JSON.stringify(user)}' style="cursor:pointer;" title="Ver más"></i>
    <i class="bx bx-download action-button download-pdf" data-user='${JSON.stringify(user)}' title="Descargar PDF" style="cursor:pointer;"></i>
    <i class="bx bx-printer action-button print-row" data-user='${JSON.stringify(user)}' title="Imprimir" style="cursor:pointer;"></i>
    <i class="bx bx-chart action-button generate-chart" data-user='${JSON.stringify(user)}' title="Generar Gráfica" style="cursor:pointer;"></i>
    <i class="bx bx-pencil action-button edit-row" data-user='${JSON.stringify(user)}' title="Editar" style="cursor:pointer;"></i>
`;
                row.push(actions);
                table.row.add(row).draw();
            });
// EVENTO CLICK PARA EDITAR
$('#table').on('click', '.edit-row', function() {
    $('#nombre_lider').val('');
    $('#apellido_lider').val('');
    $('#num_id_lider').val('');
    $('#cedula_lider').val('');
    // Obtener los datos del usuario
    let user = JSON.parse($(this).attr('data-user'));
    // Rellenar el formulario con los datos del usuario
    $('#nombre_lider').val(user.nombre_lider || '');
    $('#apellido_lider').val(user.apellido_lider || '');
    $('#num_id_lider').val(user.num_id_lider || '');
    $('#cedula_lider').val(user.cedula_lider || '');
    console.log('Datos del usuario para editar:', user); 
    $('#edit-modal').show(); 
});
// Cerrar el modal de edición
$('#close-edit-modal').on('click', function() {
    $('#edit-modal').hide();
});
// Manejar el envío del formulario para guardar cambios
$('#edit-form').on('submit', async function(e) {
    e.preventDefault();
    const updatedUser   = {
        nombre_lider: $('#nombre_lider').val(),
        apellido_lider: $('#apellido_lider').val(),
        num_id_lider: $('#num_id_lider').val(),
        cedula_lider: $('#cedula_lider').val(),
    };
    console.log('Datos actualizados enviados al servidor:', updatedUser ); 
    // Enviar los datos actualizados al servidor
    try {
        const response = await fetch('editar_datos_actualizar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updatedUser )
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Respuesta del servidor:', result);

        if (result.status === 'success') {
            // Actualizar la fila correspondiente en la tabla
            let row = $('#table').DataTable().row($(`.edit-row[data-user*='"nombre_lider":"${updatedUser .nombre_lider}"']`).closest('tr'));
            row.data(updatedUser ).draw();

            $('#edit-modal').hide(); 
        } else {
            alert('Error al actualizar los datos: ' + result.message);
        }
    } catch (error) {
        console.error('Error updating user data:', error);
        alert('Error al actualizar los datos. Por favor, inténtelo de nuevo.');
    }
});

            // EVENTO CLICK PARA "Ver más"
            $('#table').on('click', '.view-more', function() {
                let user = JSON.parse($(this).attr('data-user'));
                // Rellenar los detalles en el contenedor
$('#nombre_apellido').text(`Nombre y Apellido: ${user.nombre_apellido || 'N/A'}`);
$('#descripcion_cargo').text(`Descripción del Cargo: ${user.descripcion_cargo || 'N/A'}`);
$('#num_id').text(`Número de Identificación: ${user.num_id || 'N/A'}`);
$('#cedula').text(`Cédula: ${user.cedula || 'N/A'}`);
$('#ciudad').text(`Ciudad: ${user.ciudad || 'N/A'}`);
$('#estado').text(`Estado: ${user.estado || 'N/A'}`);
$('#municipio').text(`Municipio: ${user.municipio || 'N/A'}`);
$('#parroquia').text(`Parroquia: ${user.parroquia || 'N/A'}`);
$('#embarazada').text(`Embarazada: ${user.embarazada || 'N/A'}`);
$('#tiempo_embarazada').text(`Tiempo Embarazada: ${user.tiempo_embarazada || 'N/A'}`);
$('#discapacidad').text(`Discapacidad: ${user.discapacidad || 'N/A'}`);
$('#tipo_discapacidad').text(`Tipo de Discapacidad: ${user.tipo_discapacidad || 'N/A'}`);
$('#genero').text(`Género: ${user.genero || 'N/A'}`);
$('#tipo_genero').text(`Tipo de Género: ${user.tipo_genero || 'N/A'}`);
$('#correo').text(`Correo Electrónico: ${user.correo || 'N/A'}`);
$('#fecha_nacimiento').text(`Fecha de Nacimiento: ${user.fecha_nacimiento || 'N/A'}`);
$('#fecha_registro').text(`Fecha de Registro: ${user.fecha_registro || 'N/A'}`);
                $('#usuario_detalles').show();
            });

            // Cerrar el contenedor de detalles
            $('#cerrar_detalles').on('click', function() {
                $('#usuario_detalles').hide();
            });

            // EVENTO CLICK PARA DESCARGAR PDF
            $('#table').on('click', '.download-pdf', function() {
                let user = JSON.parse($(this).attr('data-user'));
                downloadPDF(user);
            });

            // EVENTO CLICK PARA IMPRIMIR
            $('#table').on('click', '.print-row', function() {
                let user = JSON.parse($(this).attr('data-user'));
                printRow(user);
            });

            // Maneja el clic en el icono de estadísticas para generar gráfica
            $('#table').on('click', '.generate-chart', function() {
                currentUser  = JSON.parse($(this).attr('data-user')); 
                $('#chart-modal').show(); 

                // Maneja el clic en el botón "Siguiente"
                $('#next-step').off('click').on('click', function() {
                    let chartType = $('#chart-type').val();
                    $('#chart-modal').hide(); 
                    $('#info-modal').show(); 

                    // Maneja el clic en el botón "Siguiente" del info-modal
                    $('#next-info-step').off('click').on('click', function() {
                        let infoType = $('#info-type').val();
                        $('#info-modal').hide(); 
                        $('#stats-modal').show(); 
                    });
                });
            });

            // Maneja el clic en el botón de cerrar del primer modal
            $('.close').on('click', function() {
                // Verifica qué modal está activo y lo cierra
                if ($('#chart-modal').is(':visible')) {
                    $('#chart-modal').hide(); 
                } else if ($('#info-modal').is(':visible')) {
                    $('#info-modal').hide(); 
                } else if ($('#stats-modal').is(':visible')) {
                    $('#stats-modal').hide(); 
                }
            });

            // Maneja el clic en la flecha de retroceso
            $('.back').on('click', function() {
                // Verifica qué modal está activo y navega entre ellos
                if ($('#stats-modal').is(':visible')) {
                    $('#stats-modal').hide(); 
                    $('#info-modal').show(); 
                } else if ($('#info-modal').is(':visible')) {
                    $('#info-modal').hide(); 
                    $('#chart-modal').show(); 
                }
            });

            // Maneja el clic en el botón "Siguiente" para llenar el select de estadísticas
            document.getElementById('next-info-step').addEventListener('click', function() {
                const infoType = document.getElementById('info-type').value;
                const statsSelect = document.getElementById('stats-select');
                // Limpiar las opciones actuales
                statsSelect.innerHTML = '';
                // Llenar las opciones según el tipo de información seleccionado
                if (infoType === 'demographics') {
                    statsSelect.innerHTML += '<option value="cantidad_hombres">Cantidad de Hombres</option>';
                    statsSelect.innerHTML += '<option value="cantidad_mujeres">Cantidad de Mujeres</option>';
                } else if (infoType === 'sales') {
                    statsSelect.innerHTML += '<option value="estado_ubicado">Cantidad de Movimientos por Estado</option>';
                    statsSelect.innerHTML += '<option value="ciudad_ubicado">Cantidad de Movimientos por Ciudad</option>';
                    statsSelect.innerHTML += '<option value="municipio_ubicado">Cantidad de Movimientos por Municipio</option>';
                    statsSelect.innerHTML += '<option value="parroquia_ubicado">Cantidad de Movimientos por Parroquia</option>';
                }

                // Mostrar el modal de estadísticas
                document.getElementById('info-modal').style.display = 'none'; 
                document.getElementById('stats-modal').style.display = 'block'; 
            });

            // Maneja el clic en el botón "Generar Gráfica"
            $('#generate-chart').on('click', async function() {
                let selectedStats = [];
                $('#stats-select option:selected').each(function() {
                    selectedStats.push($(this).val());
                });

                // Verifica que al menos una estadística haya sido seleccionada
                if (selectedStats.length === 0) {
                    alert("Por favor, selecciona al menos una estadística.");
                    return;
                }

                // Obtiene los datos de la base de datos para las estadísticas seleccionadas
                try {
                    const response = await fetch('obtener_datos.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ stats: selectedStats, user: currentUser  }) 
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log("Datos para el gráfico: ", data);

                    // Generar gráfico
                    generateChart($('#chart-type').val(), selectedStats, data);
                    $('#stats-modal').hide(); 

                } catch (error) {
                    console.error('Error fetching data for chart:', error);
                }
            });

            // Función para generar el gráfico
            function generateChart(type, selectedStats, data) {
                const ctx = document.getElementById('myChart').getContext('2d');
                const datasets = [];
                const labels = []; 

                selectedStats.forEach(stat => {
                    let dataValues = [];

                    // Maneja los diferentes tipos de datos que pueden venir
                    if (Array.isArray(data[stat])) {
                        data[stat].forEach(item => {
                            labels.push(item[Object.keys(item)[0]]); 
                            dataValues.push(item.cantidad); 
                        });
                    } else {
                        dataValues.push(data[stat]);
                        labels.push(stat); 
                    }

                    datasets.push({
                        label: stat,
                        data: dataValues,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    });
                });

                // Configuración del gráfico
                const myChart = new Chart(ctx, {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                $('#myChart').show(); 
            }
        } catch (error) {
            console.error('Error fetching user data:', error);
        }
    };

    listUsers();

// Función para descargar los datos de un usuario como PDF
window.downloadPDF = function(user) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Agregar la imagen en su propia línea y más pequeña
    const imgY = 10; 
    doc.addImage('imagenes/mujer3.jpg', 'JPEG', 10, imgY, 20, 20); 

    // Título
    const title = "Información del Usuario";
    const titleWidth = doc.getTextWidth(title); 
    const pageWidth = doc.internal.pageSize.getWidth(); 
    const titleX = (pageWidth - titleWidth) / 2; 
    doc.setFontSize(20);
    doc.setTextColor(0, 0, 0); 
    doc.text(title, titleX, imgY + 40); 

    // Agregar "SIIMM" en el mismo nivel que la imagen, centrado
    const siimmText = "SIIMM";
    const siimmWidth = doc.getTextWidth(siimmText);
    const siimmX = (pageWidth - siimmWidth) / 2; 
    doc.setFontSize(16);
    doc.setTextColor(0, 0, 0);
    doc.text(siimmText, siimmX, imgY + 20); 

    // Crear tabla
    const properties = [
        { label: 'Nombre y Apellido', key: 'nombre_apellido' }, 
        { label: 'Descripción del Cargo', key: 'descripcion_cargo' }, 
        { label: 'Número de Identificación', key: 'num_id' }, 
        { label: 'Cédula', key: 'cedula' }, 
        { label: 'Ciudad', key: 'ciudad' }, 
        { label: 'Estado', key: 'estado' }, 
        { label: 'Municipio', key: 'municipio' }, 
        { label: 'Parroquia', key: 'parroquia' }, 
        { label: 'Embarazada', key: 'embarazada' }, 
        { label: 'Tiempo Embarazada', key: 'tiempo_embarazada' }, 
        { label: 'Discapacidad', key: 'discapacidad' }, 
        { label: 'Tipo de Discapacidad', key: 'tipo_discapacidad' }, 
        { label: 'Género', key: 'genero' }, 
        { label: 'Tipo de Género', key: 'tipo_genero' }, 
        { label: 'Correo Electrónico', key: 'correo' }, 
        { label: 'Fecha de Nacimiento', key: 'fecha_nacimiento' }, 
        { label: 'Fecha de Registro', key: 'fecha_registro' }
    ];

    let yPosition = imgY + 50; 
    properties.forEach(prop => {
        doc.setFontSize(12);
        doc.setTextColor(0, 0, 0); 
        doc.text(`${prop.label}:`, 20, yPosition);
        doc.setFontSize(12);
        doc.setTextColor(0, 0, 0); 
        doc.text(`${user[prop.key] || 'N/A'}`, 120, yPosition); 
        yPosition += 10; 
    });

    // PIE DE PÁGINA
    doc.setFontSize(8); 
    doc.setTextColor(0, 0, 0); 
    doc.text("SISTEMA DE INFORMACIÓN INTEGRAL DEL MINISTERIO DE LA MUJER © SIIMM 2024", 20, yPosition + 10);

    // GUARDAR EN PDF
    doc.save(`${user.nombre_proyecto || 'proyecto'}.pdf`);
};

// IMPRIMIR
window.printRow = function(user) {
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write(`
    <html>
    <head>
    <title>Imprimir Datos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #000; } 
        h1 { text-align: center; color: #000; } 
        .container { border: 1px solid #ccc; padding: 20px; border-radius: 5px; }
        .property { margin: 10px 0; }
        .label { font-weight: bold; }
        .value { }
        .footer { font-size: 8px; text-align: center; margin-top: 20px; color: #000; } 
        img { width: 100px; height: auto; display: block; margin: 0; float: left; } 
        .siimm { text-align: center; font-size: 16px; margin-top: 10px; clear: both; } 
    </style>
    </head>
    <body>
    <div class="container">
        <img src="imagenes/mujer3.jpg" alt="Imagen"> 
        <div class="siimm">SIIMM</div> 
        <h1>Información del Usuario</h1>
        
        <p><strong>Nombre y Apellido:</strong> ${user.nombre_apellido || 'N/A'}</p>
        <p><strong>Descripción del Cargo:</strong> ${user.descripcion_cargo || 'N/A'}</p>
        <p><strong>Número de Identificación:</strong> ${user.num_id || 'N/A'}</p>
        <p><strong>Cédula:</strong> ${user.cedula || 'N/A'}</p>
        <p><strong>Ciudad:</strong> ${user.ciudad || 'N/A'}</p>
        <p><strong>Estado:</strong> ${user.estado || 'N/A'}</p>
        <p><strong>Municipio:</strong> ${user.municipio || 'N/A'}</p>
        <p><strong>Parroquia:</strong> ${user.parroquia || 'N/A'}</p>
        <p><strong>Embarazada:</strong> ${user.embarazada || 'N/A'}</p>
        <p><strong>Tiempo Embarazada:</strong> ${user.tiempo_embarazada || 'N/A'}</p>
        <p><strong>Discapacidad:</strong> ${user.discapacidad || 'N/A'}</p>
        <p><strong>Tipo de Discapacidad:</strong> ${user.tipo_discapacidad || 'N/A'}</p>
        <p><strong>Género:</strong> ${user.genero || 'N/A'}</p>
        <p><strong>Tipo de Género:</strong> ${user.tipo_genero || 'N/A'}</p>
        <p><strong>Correo Electrónico:</strong> ${user.correo || 'N/A'}</p>
        <p><strong>Fecha de Nacimiento:</strong> ${user.fecha_nacimiento || 'N/A'}</p>
        <p><strong>Fecha de Registro:</strong> ${user.fecha_registro || 'N/A'}</p>
    </div>
    <div class="footer">
        SISTEMA DE INFORMACIÓN INTEGRAL DEL MINISTERIO DE LA MUJER © SIIMM 2024
    </div>
    </body>
    </html>
    `);
    printWindow.document.close(); 
    printWindow.print(); 
};
});
</script>
<script>
let dataTable;
let dataTableIsInitialized = false;
let dataTableOptions = {
dom: 'Bfrtilp',  
/**El valor 'Bfrtip' para la opción dom en dataTableOptions 
especifica el orden de los elementos de control de DataTables en la página. 
En este caso, 'Bfrtip' significa:
B: Botones
f: Filtro de búsqueda
r: Información de procesamiento
t: Tabla
i: Información de la tabla
p: Paginación **/
    buttons: [
        {
            extend: 'excelHtml5',   
            text: '<i class="fas fa-file-excel"></i> ',
            titleAttr: 'Exportar a Excel',
            className: 'btn btn-success',
        },
        {
            extend: 'pdfHtml5',
            text: '<i class="fas fa-file-pdf"></i> ',
            titleAttr: 'Exportar a PDF',
            className: 'btn btn-danger',
        },
        {
            extend: 'print',
            text: '<i class="fa fa-print"></i> ',
            titleAttr: 'Imprimir',
            className: 'btn btn-info',
        },

        
    ],
    lengthMenu: [5, 10, 15, 20, 100, 200, 500],
    columnDefs: [
        { className: 'centered', targets: [0, 1, 2, 3, 4, 5, 6] },
        { orderable: false, targets: [1, 2, 3, 4, 5, 6] },
        { searchable: false, targets: [3] },
        { width: '10%', targets: [1] },
    ],
    pageLength: 5,
    destroy: true,          
    language: {
        search: 'Buscar',
        processing: 'Procesando...',
        lengthMenu: 'Mostrar _MENU_ registros',
        zeroRecords: 'No se encontraron resultados',
        emptyTable: 'Ningún dato disponible en esta tabla',
        infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
        infoFiltered: '(filtrado de un total de _MAX_ registros)',
        search: 'Buscar:',
        infoThousands: ',',
        loadingRecords: 'Cargando...',
        paginate: {
            first: 'Primero',
            last: 'Último',
            next: 'Siguiente',
            previous: 'Anterior',
        },
        aria: {
            sortAscending: ': Activar para ordenar la columna de manera ascendente',
            sortDescending: ': Activar para ordenar la columna de manera descendente',
        },
        buttons: {
            copy: 'Copiar',
            colvis: 'Visibilidad',
            collection: 'Colección',
            colvisRestore: 'Restaurar visibilidad',
            copyKeys:
                'Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br /> <br /> Para cancelar, haga clic en este mensaje o presione escape.',
            copySuccess: {
                1: 'Copiada 1 fila al portapapeles',
                _: 'Copiadas %ds fila al portapapeles',
            },
            copyTitle: 'Copiar al portapapeles',
            csv: 'CSV',
            excel: 'Excel',
            pageLength: {
                '-1': 'Mostrar todas las filas',
                _: 'Mostrar %d filas',
            },
            pdf: 'PDF',
            print: 'Imprimir',
            renameState: 'Cambiar nombre',
            updateState: 'Actualizar',
            createState: 'Crear Estado',
            removeAllStates: 'Remover Estados',
            removeState: 'Remover',
            savedStates: 'Estados Guardados',
            stateRestore: 'Estado %d',
        },
        autoFill: {
            cancel: 'Cancelar',
            fill: 'Rellene todas las celdas con <i>%d</i>',
            fillHorizontal: 'Rellenar celdas horizontalmente',
            fillVertical: 'Rellenar celdas verticalmentemente',
        },
        decimal: ',',
        searchBuilder: {
            add: 'Añadir condición',
            button: {
                0: 'Constructor de búsqueda',
                _: 'Constructor de búsqueda (%d)',
            },
            clearAll: 'Borrar todo',
            condition: 'Condición',
            conditions: {
                date: {
                    after: 'Despues',
                    before: 'Antes',
                    between: 'Entre',
                    empty: 'Vacío',
                    equals: 'Igual a',
                    notBetween: 'No entre',
                    notEmpty: 'No Vacio',
                    not: 'Diferente de',
                },
                number: {
                    between: 'Entre',
                    empty: 'Vacio',
                    equals: 'Igual a',
                    gt: 'Mayor a',
                    gte: 'Mayor o igual a',
                    lt: 'Menor que',
                    lte: 'Menor o igual que',
                    notBetween: 'No entre',
                    notEmpty: 'No vacío',
                    not: 'Diferente de',
                },
                string: {
                    contains: 'Contiene',
                    empty: 'Vacío',
                    endsWith: 'Termina en',
                    equals: 'Igual a',
                    notEmpty: 'No Vacio',
                    startsWith: 'Empieza con',
                    not: 'Diferente de',
                    notContains: 'No Contiene',
                    notStartsWith: 'No empieza con',
                    notEndsWith: 'No termina con',
                },
                array: {
                    not: 'Diferente de',
                    equals: 'Igual',
                    empty: 'Vacío',
                    contains: 'Contiene',
                    notEmpty: 'No Vacío',
                    without: 'Sin',
                },
            },
            data: 'Data',
            deleteTitle: 'Eliminar regla de filtrado',
            leftTitle: 'Criterios anulados',
            logicAnd: 'Y',
            logicOr: 'O',
            rightTitle: 'Criterios de sangría',
            title: {
                0: 'Constructor de búsqueda',
                _: 'Constructor de búsqueda (%d)',
            },
            value: 'Valor',
        },
        searchPanes: {
            clearMessage: 'Borrar todo',
            collapse: {
                0: 'Paneles de búsqueda',
                _: 'Paneles de búsqueda (%d)',
            },
            count: '{total}',
            countFiltered: '{shown} ({total})',
            emptyPanes: 'Sin paneles de búsqueda',
            loadMessage: 'Cargando paneles de búsqueda',
            title: 'Filtros Activos - %d',
            showMessage: 'Mostrar Todo',
            collapseMessage: 'Colapsar Todo',
        },
        select: {
            cells: {
                1: '1 celda seleccionada',
                _: '%d celdas seleccionadas',
            },
            columns: {
                1: '1 columna seleccionada',
                _: '%d columnas seleccionadas',
            },
            rows: {
                1: '1 fila seleccionada',
                _: '%d filas seleccionadas',
            },
        },
        thousands: '.',
        datetime: {
            previous: 'Anterior',
            next: 'Proximo',
            hours: 'Horas',
            minutes: 'Minutos',
            seconds: 'Segundos',
            unknown: '-',
            amPm: ['AM', 'PM'],
            months: {
                0: 'Enero',
                1: 'Febrero',
                10: 'Noviembre',
                11: 'Diciembre',
                2: 'Marzo',
                3: 'Abril',
                4: 'Mayo',
                5: 'Junio',
                6: 'Julio',
                7: 'Agosto',
                8: 'Septiembre',
                9: 'Octubre',
            },
            weekdays: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
        },
        editor: {
            close: 'Cerrar',
            create: {
                button: 'Nuevo',
                title: 'Crear Nuevo Registro',
                submit: 'Crear',
            },
            edit: {
                button: 'Editar',
                title: 'Editar Registro',
                submit: 'Actualizar',
            },
            remove: {
                button: 'Eliminar',
                title: 'Eliminar Registro',
                submit: 'Eliminar',
                confirm: {
                    _: '¿Está seguro que desea eliminar %d filas?',
                    1: '¿Está seguro que desea eliminar 1 fila?',
                },
            },
            error: {
                system:
                    'Ha ocurrido un error en el sistema (<a target="\\" rel="\\ nofollow" href="\\">Más información&lt;\\/a&gt;).</a>',
            },
            multi: {
                title: 'Múltiples Valores',
                info: 'Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.',
                restore: 'Deshacer Cambios',
                noMulti:
                    'Este registro puede ser editado individualmente, pero no como parte de un grupo.',
            },
        },
        info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
        stateRestore: {
            creationModal: {
                button: 'Crear',
                name: 'Nombre:',
                order: 'Clasificación',
                paging: 'Paginación',
                search: 'Busqueda',
                select: 'Seleccionar',
                columns: {
                    search: 'Búsqueda de Columna',
                    visible: 'Visibilidad de Columna',
                },
                title: 'Crear Nuevo Estado',
                toggleLabel: 'Incluir:',
            },
            emptyError: 'El nombre no puede estar vacio',
            removeConfirm: '¿Seguro que quiere eliminar este %s?',
            removeError: 'Error al eliminar el registro',
            removeJoiner: 'y',
            removeSubmit: 'Eliminar',
            renameButton: 'Cambiar Nombre',
            renameLabel: 'Nuevo nombre para %s',
            duplicateError: 'Ya existe un Estado con este nombre.',
            emptyStates: 'No hay Estados guardados',
            removeTitle: 'Remover Estado',
            renameTitle: 'Cambiar Nombre Estado',
        },
    },
};
</script>
            </div>
        </div>
    </main>
</div>
<script>
const profile = document.querySelector('.profile');
const dropdown = document.querySelector('.profile-dropdown');
profile.addEventListener('click', (e) => {
  if (e.target.tagName === 'A') {
    return;
  }
  e.preventDefault();
  dropdown.classList.toggle('show');
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