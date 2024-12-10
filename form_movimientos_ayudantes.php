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
    $role_id = $result['role_id']; // ID del rol del usuario
} else {
// Si no se encuentra el usuario, redirige a la página de inicio de sesión
    header("Location: admin.php");
    exit();
}

// Obtener la cédula de la URL, si existe
$cedula_ayudantes = isset($_GET['cedula_ayudantes']) ? htmlspecialchars($_GET['cedula_ayudantes']) : '';

$mensaje_exitoso = '';
$mensaje_erroneo = '';

if (isset($_GET['mensaje_exitoso'])) {
    $mensaje_exitoso = htmlspecialchars($_GET['mensaje_exitoso']);
}
if (isset($_GET['mensaje_erroneo'])) {
    $mensaje_erroneo = htmlspecialchars($_GET['mensaje_erroneo']);
}
// ROLES PERMITIDOS
$roles_permitidos = [1, 2, 3]; 
// Verificar si el usuario tiene un rol permitido
if (!in_array($role_id, $roles_permitidos)) {
// Redirige a la página de acceso denegado si no tiene permisos
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
// Verificar permisos según el rol
$permisosPermitidos = $permisosPorRol[$role_id] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="imagenes/mujer3.jpg" type="image/x-icon">
    <title>SIIMM</title>
    <!-- ICONOS -->
  <link rel="stylesheet" type="text/css" href="boxicons-2.1.4/css/boxicons.min.css">
<!-- ESTILOS -->
    <link rel="stylesheet" href="style_interfaz.css">
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
 <!-- ALERTAS -->
<?php if ($mensaje_exitoso): ?>
    <div class="mensaje_exitoso"><?php echo $mensaje_exitoso; ?></div>
<?php endif; ?>
<?php if ($mensaje_erroneo): ?>
    <div class="mensaje_erroneo"><?php echo $mensaje_erroneo; ?></div>
<?php endif; ?>
        <main>
            <div class="header">
                <div class="left">
                    <h1>FORMULARIO DE AYUDANTES EN EL MOVIMIENTO</h1>
                </div>
            </div>
            <!-- FORMULARIO -->
<section class="formulario_mujer">
  <h2>DATOS PERSONALES</h2>
  <form action="conexion_form_ayudantes.php" method="post">
<!-- CONEXIONES A LA BASE DE DATOS -->
<?php
// INCLUIR ARCHIVOS DE CONEXIÓN A LA BASE DE DATOS
    include 'conexion_bd.php';
// CREAR CONEXIÓN USANDO PDO
    $conn = new PDO("mysql:host=$servidor;dbname=$base_de_datos", $usuario, $contrasena);
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
        $tipos_discapacidades = obtenerDatos($conn, 'tipos_discapacidades', 'tipo_discapacidad');
        $generos = obtenerDatos($conn, 'generos', 'genero');
?>  
<!-- CAMPOS EXISTENTES EN EL FORMULARIO -->
<?php
// Recuperar los datos de la URL
$nombre_apellido = isset($_GET['nombre_apellido']) ? $_GET['nombre_apellido'] : '';
$descripcion_cargo = isset($_GET['descripcion_cargo']) ? $_GET['descripcion_cargo'] : '';
$num_id = isset($_GET['num_id']) ? $_GET['num_id'] : '';
$cedula_ayudantes = isset($_GET['cedula_ayudantes']) ? $_GET['cedula_ayudantes'] : '';
?>

<!-- NOMBRE DEL MOVIMIENTO -->
<div class="caja_form">
    <div class="input_form">
        <label for="nombre_apellido">Nombre y apellido:</label>
        <input type="text" id="nombre_apellido" name="nombre_apellido" value="<?php echo htmlspecialchars($nombre_apellido); ?>" required placeholder="Ingrese el cargo" readonly>
    </div>
    <!-- CARGO DEL AYUDANTE -->
    <div class="input_form">
        <label for="descripcion_cargo">Cargo:</label>
        <input type="text" id="descripcion_cargo" name="descripcion_cargo" value="<?php echo htmlspecialchars($descripcion_cargo); ?>" required placeholder="Ingrese el cargo" readonly>
    </div>
</div>
<!-- NUMID CÉDULA -->
<div class="caja_form">
    <div class="input_form">
        <label for="num_id">Número de identidad:</label>
        <input type="text" id="num_id" name="num_id" value="<?php echo htmlspecialchars($num_id); ?>" required placeholder="Ingrese el cargo" readonly>
    </div>
    <!-- CÉDULA DE IDENTIDAD -->
    <div class="input_form">
        <label for="cedula">Cédula de identidad:</label>
        <input type="text" id="cedula" name="cedula" value="<?php echo htmlspecialchars($cedula_ayudantes); ?>" required placeholder="Ingrese la cédula de identidad" readonly>
    </div>
</div>
<!-- CIUDAD DONDE SE UBICA -->
<div class="caja_form">
  <div class="input_form">
    <label for="ciudad" class="input_form">Ciudad ubicado:</label>
    <br>
    <select id="ciudad" name="ciudad" required>
      <option value="">Seleccione una Ciudad</option>
      <?php foreach ($ciudades as $ciudad): ?>
        <option value="<?php echo htmlspecialchars($ciudad); ?>"><?php echo htmlspecialchars($ciudad); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
<!-- ESTADO DONDE SE UBICA -->
  <div class="input_form">
    <label for="estado" class="input_form">Estado ubicado:</label>
    <br>
    <select id="estado" name="estado" required>
      <option value="">Seleccione un Estado</option>
      <?php foreach ($estados as $estado): ?>
        <option value="<?php echo htmlspecialchars($estado); ?>"><?php echo htmlspecialchars($estado); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <!-- MUNICIPIO DONDE SE UBICA -->
  <div class="caja_form">
  <div class="input_form">
    <label for="municipio" class="input_form">Municipio ubicado:</label>
    <br>
    <select id="municipio" name="municipio" required>
      <option value="">Seleccione un Municipio</option>
      <?php foreach ($municipios as $municipio): ?>
        <option value="<?php echo htmlspecialchars($municipio); ?>"><?php echo htmlspecialchars($municipio); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <!-- PARROQUIA DONDE SE UBICA -->
  <div class="input_form">
    <label for="parroquia" class="input_form">Parroquia ubicado:</label>
    <br>
    <select id="parroquia" name="parroquia" required>
      <option value="">Seleccione una Parroquia</option>
      <?php foreach ($parroquias as $parroquia): ?>
        <option value="<?php echo htmlspecialchars($parroquia); ?>"><?php echo htmlspecialchars($parroquia); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
<!-- ENCINTA AYUDANTES -->
<div class="caja_form">
    <div class="input_form">
        <label for="embarazada">¿Se encuentra embarazada?:</label><br>
        <select id="embarazada" name="embarazada" onchange="toggleTiempoEncinta()" required>
        <option value="" disabled selected>Seleccione una opción</option>
            <option value="Si">Sí</option>
            <option value="No">No</option>
        </select>
    </div>
    <!-- TIEMPO ENCINTA -->
    <div class="input_form">
        <label for="tiempo_embarazada">¿Cuánto tiempo lleva?:</label><br>
        <select id="tiempo_embarazada" name="tiempo_embarazada" disabled>
            <option value="Seleccione un período">Seleccione un período</option>
            <option value="1 semana">1 semana</option>
            <option value="2 semanas">2 semanas</option>
            <option value="3 semanas">3 semanas</option>
            <option value="1 mes">1 mes</option>
            <option value="2 meses">2 meses</option>
            <option value="3 meses">3 meses</option>
            <option value="4 meses">4 meses</option>
            <option value="5 meses">5 meses</option>
            <option value="6 meses">6 meses</option>
            <option value="7 meses">7 meses</option>
            <option value="8 meses">8 meses</option>
            <option value="9 meses">9 meses</option>
        </select>
    </div>
</div>
<!-- DISCAPACIDAD AYUDANTES -->
<div class="caja_form">
    <div class="input_form">
        <label for="discapacidad">¿Posee una discapacidad?:</label><br>
        <select id="discapacidad" name="discapacidad" onchange="toggleTiempoEncinta()" required>
        <option value="" disabled selected>Seleccione una opción</option>
            <option value="Si">Sí</option>
            <option value="No">No</option>
        </select>
    </div>
    <!-- TIPO DE DISCAPACIDAD -->
    <div class="input_form">
        <label for="tipo_discapacidad" class="input_form">Tipo de discapacidad:</label>
        <br>
        <select id="tipo_discapacidad" name="tipo_discapacidad" required disabled>
            <option value="">Seleccione una discapacidad</option>
            <?php foreach ($tipos_discapacidades as $tipo_discapacidad): ?>
                <option value="<?php echo htmlspecialchars($tipo_discapacidad); ?>"><?php echo htmlspecialchars($tipo_discapacidad); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<!-- GÉNERO AYUDANTES -->
<div class="caja_form">
    <div class="input_form">
        <label for="genero" class="input_form">Género:</label>
        <br>
        <select id="genero" name="genero" required onchange="toggleSpecificGender()">
            <option value="">Seleccione un género</option>
            <?php foreach ($generos as $genero): ?>
                <option value="<?php echo htmlspecialchars($genero); ?>"><?php echo htmlspecialchars($genero); ?></option>
            <?php endforeach; ?>
            <option value="Otro">Otro</option> 
        </select>
    </div>
    <!-- GÉNERO ESPECIFICO -->
    <div class="input_form">
        <label for="tipo_genero">Especifique su género</label>
        <input type="text" id="tipo_genero" name="tipo_genero" required placeholder="Especifique su género" disabled>
    </div>
</div>
<!-- CORREO -->
<div class="caja_form">
    <div class="input_form">
        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="correo" required placeholder="Ingrese su correo electrónico">
	   </div>
<!-- FECHA DE NACIMIENTO -->
      <div class="input_form">
        <label for="fecha_nacimiento">Fecha de nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
      </div>
    </div>
<!-- BOTON -->
    <div class="caja_form">
      <div class="boton_form">
        <button type="submit" class="boton">Enviar</button>
      </div>
    </div>
  </form>
</section>
                </div>
            </div>
       </main>
</div>
<!-- SCRIPTS -->
<script>
    function toggleSpecificGender() {
        var select = document.getElementById("genero");
        var input = document.getElementById("tipo_genero");
        
        // Desbloquear el campo si se selecciona "Otro"
        if (select.value === "Otro") {
            input.disabled = false; 
        } else {
            input.disabled = true; 
            input.value = ""; 
        }
    }
</script>
<script>
    function toggleTiempoEncinta() {
        var encintaSelect = document.getElementById("embarazada");
        var tiempoSelect = document.getElementById("tiempo_embarazada");
        if (encintaSelect.value === "Si") {
            tiempoSelect.disabled = false; 
        } else {
            tiempoSelect.disabled = true; 
            tiempoSelect.selectedIndex = 0; 
        }
    }
</script>
<script>
    function toggleTipoDiscapacidad() {
        var discapacidadSelect = document.getElementById("discapacidad");
        var tipodisSelect = document.getElementById("tipo_discapacidad");
        
        if (discapacidadSelect.value === "Si") {
            tipodisSelect.disabled = false; 
        } else {
            tipodisSelect.disabled = true; 
            tipodisSelect.selectedIndex = 0; 
        }
    }
    window.onload = function() {
        toggleTipoDiscapacidad();
    };
</script>
  <!-- ESTADOS DONDE ESTE PRESENTE -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var estadoList = document.getElementById('estado-list');
        estadoList.addEventListener('click', function() {

        });
    });
</script>
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
        updateMaxLength('num_id', 'cedula');
    };
</script>
  <!-- ALERTAS Y OPCIONES DE BOTON DE AGREGAR Y ELIMINAR -->
<script>
let hombres = 0;
let mujeres = 0;

document.getElementById('agregar').addEventListener('click', () => {
  const cantidadHombres = parseInt(document.getElementById('cantidad_hombres').value);
  const cantidadMujeres = parseInt(document.getElementById('cantidad_mujeres').value);

  if (cantidadHombres > 0) {
    hombres += cantidadHombres;
    document.getElementById('hombres').textContent = hombres;
  }
  if (cantidadMujeres > 0) {
    mujeres += cantidadMujeres;
    document.getElementById('mujeres').textContent = mujeres;
  }

  if (cantidadHombres > 0 || cantidadMujeres > 0) {
    mostrarAlerta('Se agregó con éxito', 'mensaje_exitoso');
  } else {
    mostrarAlerta('Ingrese una cantidad válida', 'mensaje_erroneo');
  }
});

document.getElementById('eliminar').addEventListener('click', () => {
  const cantidadHombres = parseInt(document.getElementById('cantidad_hombres').value);
  const cantidadMujeres = parseInt(document.getElementById('cantidad_mujeres').value);

  if (cantidadHombres > 0) {
    hombres -= cantidadHombres;
    if (hombres < 0) {
      hombres = 0;
    }
    document.getElementById('hombres').textContent = hombres;
  }
  if (cantidadMujeres > 0) {
    mujeres -= cantidadMujeres;
    if (mujeres < 0) {
      mujeres = 0;
    }
    document.getElementById('mujeres').textContent = mujeres;
  }

  if (cantidadHombres > 0 || cantidadMujeres > 0) {
    mostrarAlerta('Se eliminó con éxito', 'mensaje_exitoso');
  } else {
    mostrarAlerta('Ingrese una cantidad válida', 'mensaje_erroneo');
  }
});

function mostrarAlerta(mensaje, tipo) {
  const alerta = document.createElement('div');
  alerta.classList.add(tipo);
  alerta.textContent = mensaje;
  document.body.appendChild(alerta);
  setTimeout(() => {
    alerta.style.display = 'none';
  }, 5000);
}
</script>
 <!-- AGREGAR TABLAS -->
 <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addMoreBtn = document.querySelector('.boton_agregar_ayudantes');
            const removeBtn = document.querySelector('.boton_quitar_ayudantes');
            const additionalInputsContainer = document.querySelector('.adicional_input');

            function createInputGroup() {
                const newInputGroup = document.createElement('div');
                newInputGroup.className = 'input_form';

                // Campos de entrada
                const fields = [
                    { label: 'Nombre de ayudante:', name: 'nombre_apellido_ayudantes', type: 'text', placeholder: 'Ingrese su nombre' },
                    { label: 'Cargo del ayudante:', name: 'descripcion_cargo_ayudantes', type: 'text', placeholder: 'Ingrese su cargo' },
                    { label: 'Cédula de identidad:', name: 'cedula_ayudantes', type: 'number', placeholder: 'Ingrese una cédula de identidad', maxLength: 8 }
                ];

                fields.forEach(field => {
                    const label = document.createElement('label');
                    label.textContent = field.label;
                    label.htmlFor = field.name;
                    label.className = 'input_form label';
                    newInputGroup.appendChild(label);

                    const input = document.createElement('input');
                    input.type = field.type;
                    input.name = field.name;
                    input.placeholder = field.placeholder;
                    input.className = 'input_form input';
                    input.required = true;

                    if (field.maxLength) {
                        input.maxLength = field.maxLength;
                        input.oninput = function() {
                          if (this.value.length > this.maxLength) {
                                this.value = this.value.slice(0, this.maxLength);
                            }
                        };
                        input.onkeypress = function(event) {
                            return event.charCode >= 48 && event.charCode <= 57; 
                        };
                    }

                    newInputGroup.appendChild(input);
                });

                // Crear el select para el número de identidad
                const selectNumId = document.createElement('select');
                selectNumId.name = 'num_id_ayudantes';
                selectNumId.required = true;
                selectNumId.className = 'input_form input';
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Seleccione el número telefónico';
                selectNumId.appendChild(defaultOption);

                // Agregar opciones al select 
                <?php foreach ($num_identidad as $identidad): ?>
                    const option = document.createElement('option');
                    option.value = "<?php echo htmlspecialchars($identidad); ?>";
                    option.textContent = "<?php echo htmlspecialchars($identidad); ?>";
                    selectNumId.appendChild(option);
                <?php endforeach; ?>

                const labelNumId = document.createElement('label');
                labelNumId.textContent = 'Número de identidad:';
                labelNumId.htmlFor = 'num_id_ayudantes';
                labelNumId.className = 'input_form label';
                newInputGroup.appendChild(labelNumId);
                newInputGroup.appendChild(selectNumId);

                // Agregar el nuevo grupo de entradas al contenedor
                additionalInputsContainer.appendChild(newInputGroup);
            }

            // Evento para agregar un nuevo grupo de entradas
            addMoreBtn.addEventListener('click', createInputGroup);

            // Evento para eliminar el último grupo de entradas
            removeBtn.addEventListener('click', () => {
                const lastInputGroup = additionalInputsContainer.lastChild;
                if (lastInputGroup) {
                    additionalInputsContainer.removeChild(lastInputGroup);
                }
            });
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