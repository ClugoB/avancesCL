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
                    <h1>FORMULARIO DE MOVIMIENTOS</h1>
                </div>
            </div>
            <!-- FORMULARIO -->
<section class="formulario_mujer">
  <h2>DATOS DEL MOVIMIENTO</h2>
  <form action="conexion_form_mujeres.php" method="post">
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
        $cargos_ayudantes = obtenerDatos($conn, 'cargos_ayudantes', 'cargo');
?>  
<!-- CAMPOS EXISTENTES EN EL FORMULARIO -->
 <!-- NOMBRE DEL MOVIMIENTO-->
 <div class="caja_form">
      <div class="input_form">
        <label for="nombre_movimiento">Nombre del movimiento:</label>
        <input type="text" id="nombre_movimiento" name="nombre_movimiento" required placeholder="Ingrese su nombre">
      </div>
      <!-- DESCRIPCIÓN -->
      <div class="input_form">
        <label for="descripcion_movimiento">Descripción del movimiento:</label>
        <input type="text" id="descripcion_movimiento" name="descripcion_movimiento" required placeholder="Ingrese la descripción">
      </div>
      </div>
<!-- CIUDAD DONDE SE UBICA -->
<div class="caja_form">
  <div class="input_form">
    <label for="ciudad_ubicado" class="input_form">Ciudad ubicado:</label>
    <br>
    <select id="ciudad_ubicado" name="ciudad_ubicado" required>
      <option value="">Seleccione una Ciudad</option>
      <?php foreach ($ciudades as $ciudad): ?>
        <option value="<?php echo htmlspecialchars($ciudad); ?>"><?php echo htmlspecialchars($ciudad); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
<!-- ESTADO DONDE SE UBICA -->
  <div class="input_form">
    <label for="estado_ubicado" class="input_form">Estado ubicado:</label>
    <br>
    <select id="estado_ubicado" name="estado_ubicado" required>
      <option value="">Seleccione un Estado</option>
      <?php foreach ($estados as $estado): ?>
        <option value="<?php echo htmlspecialchars($estado); ?>"><?php echo htmlspecialchars($estado); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <!-- MUNICIPIO DONDE SE UBICA -->
  <div class="caja_form">
  <div class="input_form">
    <label for="municipio_ubicado" class="input_form">Municipio ubicado:</label>
    <br>
    <select id="municipio_ubicado" name="municipio_ubicado" required>
      <option value="">Seleccione un Municipio</option>
      <?php foreach ($municipios as $municipio): ?>
        <option value="<?php echo htmlspecialchars($municipio); ?>"><?php echo htmlspecialchars($municipio); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <!-- PARROQUIA DONDE SE UBICA -->
  <div class="input_form">
    <label for="parroquia_ubicado" class="input_form">Parroquia ubicado:</label>
    <br>
    <select id="parroquia_ubicado" name="parroquia_ubicado" required>
      <option value="">Seleccione una Parroquia</option>
      <?php foreach ($parroquias as $parroquia): ?>
        <option value="<?php echo htmlspecialchars($parroquia); ?>"><?php echo htmlspecialchars($parroquia); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <!-- TIPO DE MOVIMIENTO -->
<div class="caja_form">
    <div class="input_form">
        <label for="tipos_movimientos">Tipo de movimiento:</label><br>
        <select id="tipos_movimientos" name="tipos_movimientos">
            <option value="Seleccione el tipo de movimiento">Seleccione el tipo de movimiento</option>
            <?php foreach ($tipo_movimiento as $tipos_movimientos): ?>
                <option value="<?php echo htmlspecialchars($tipos_movimientos); ?>"><?php echo htmlspecialchars($tipos_movimientos); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <!-- RAZÓN DEL MOVIMIENTO -->
    <div class="input_form">
    <label for="razon_movimiento">Razón del movimiento:</label>
    <input type="text" id="razon_movimiento" name="razon_movimiento" required placeholder="Ingresela razón del proyecto">
    </div>
</div>
<!-- ESTADOS DONDE ESTA -->
<div class="caja_form">
  <div class="input_form">
    <label class="input_form">Estados presentes:</label>
    <br>
    <div id="estado_lista" name="estados_presentes">
      <?php foreach ($estados as $estado): ?>
        <label>
          <input type="checkbox" name="estados_presentes[]" value="<?php echo htmlspecialchars($estado); ?>">
          <?php echo htmlspecialchars($estado); ?>
        </label>
      <?php endforeach; ?>
    </div>
  </div>
  <?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $estado_ubicado = $_POST['estado_ubicado'];
  $estados_presentes = $_POST['estados_presentes'];
  $movementType = (count($estados_presentes) < 5) ? 'Micro movimiento' : 'Movimiento nacional';

  $query = "INSERT INTO form_mujeres (estado_ubicado, estados_presentes, movimiento_tipo) 
             VALUES ('$estado_ubicado', '" . implode(',', $estados_presentes) . "', '$movementType')";
  mysqli_query($conn, $query);

  foreach ($estados_presentes as $estado) {
    echo "Estado seleccionado: $estado<br>";
  }
}
?>
<h2>CONTRASEÑA DEL MOVIMIENTO</h2>
<!-- CONTRASEÑA -->
<div class="caja_form">
    <div class="input_form">
        <label for="contrasena_movimiento">Contraseña:</label>
        <input type="password" id="contrasena_movimiento" name="contrasena_movimiento" placeholder="Ingrese una contraseña" class="form-elementos" required minlength="8" maxlength="16" oninput="this.setCustomValidity(this.validity.patternMismatch ? 'La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un símbolo.' : '');" pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$">
    </div>
    <!-- CONFIRMAR CONTRASEÑA -->
    <div class="input_form">
        <label for="confirmar_contrasena">Confirmar Contraseña:</label>
        <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="Confirme su contraseña" class="form-elementos" required minlength="8" maxlength="16">
    </div>
</div>
    <h2>DATOS DEL LÍDER</h2>
<!-- NOMBRE -->
    <div class="caja_form">
      <div class="input_form">
        <label for="nombre_lider">Nombre:</label>
        <input type="text" id="nombre_lider" name="nombre_lider" required placeholder="Ingrese nombre del lider">
      </div>
<!-- APELLIDO -->
    <div class="input_form">
        <label for="apellido_lider">Apellido:</label>
        <input type="text" id="apellido_lider" name="apellido_lider" required placeholder="Ingrese apellido del lider">
    	</div>
    </div>
<!-- NUMERO DE IDENTIDAD -->
<div class="caja_form">
    <div class="input_form">
        <label for="num_id_lider">Número de identidad:</label><br>
        <select id="num_id_lider" name="num_id_lider" required onchange="updateMaxLength('num_id_lider', 'cedula_lider')">
            <option value="Seleccione el número telefónico">Seleccione el número telefónico</option>
            <?php foreach ($num_identidad as $identidad): ?>
                <option value="<?php echo htmlspecialchars($identidad); ?>"><?php echo htmlspecialchars($identidad); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <!-- CEDULA DE IDENTIDAD -->
    <div class="input_form">
        <label for="cedula_lider">Número de identidad:</label>
        <input type="number" id="cedula_lider" name="cedula_lider" placeholder="Ingrese una cédula de identidad" class="form-elementos" required maxlength="10" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
    </div>
</div>
<!-- CORREO -->
	<div class="caja_form">
    <div class="input_form">
        <label for="correo_lider">Correo electrónico:</label>
        <input type="correo_lider" id="correo_lider" name="correo_lider" required placeholder="Ingrese su correo electrónico">
	   </div>
<!-- FECHA DE NACIMIENTO -->
      <div class="input_form">
        <label for="fecha_nacimiento_lider">Fecha de nacimiento:</label>
        <input type="date" id="fecha_nacimiento_lider" name="fecha_nacimiento_lider" required>
      </div>
    </div>
<!-- NUMERO DE TELEFONO INICIAL -->
	<div class="caja_form">
		<div class="input_form">
			<label for="codigo_lider">Código celular:</label><br>
    		<select id="codigo_lider" name="codigo_lider" required>
        <option value="">Seleccione el número telefónico</option>
        <?php foreach ($codigos_operadoras as $codigo): ?>
            <option value="<?php echo htmlspecialchars($codigo); ?>"><?php echo htmlspecialchars($codigo); ?></option>
        <?php endforeach; ?>
    </select>
	</div>
<!-- NUMERO DE TELEFONO -->
	<div class="input_form">
		<label for="numero_telefono_lider">Número:</label><br>
        <input type="number" id="numero_telefono_lider" name="numero_telefono_lider" placeholder="Ingrese un número de teléfono"  required maxlength="7" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
        </div>
	</div> 
  <h2>DATOS DE LOS AYUDANTES</h2>
<div class="caja_form" id="form_container">
    <div class="input_form">
        <label for="nombre_apellido_ayudantes_${ayudantesCount}" class="input_form label">Nombre y Apellido:</label>
        <input type="text" id="nombre_apellido_ayudantes_${ayudantesCount}" name="nombre_apellido_ayudantes[]" required placeholder="Ingrese nombre" class="input_form input">
    </div>
    <div class="input_form">
        <label for="descripcion_cargo_ayudantes_${ayudantesCount}" class="input_form label">Cargo del ayudante:</label>
        <select id="descripcion_cargo_ayudantes_${ayudantesCount}" name="descripcion_cargo_ayudantes[]" required class="input_form input">
            <option value="">Seleccione el cargo</option>
            <?php foreach ($cargos_ayudantes as $cargo): ?>
                <option value="<?php echo htmlspecialchars($cargo); ?>"><?php echo htmlspecialchars($cargo); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="input_form">
        <label for="num_id_ayudantes_${ayudantesCount}" class="input_form label">Número de identidad:</label>
        <select id="num_id_ayudantes_${ayudantesCount}" name="num_id_ayudantes[]" required class="input_form input">
            <option value="">Seleccione el número telefónico</option>
            <?php foreach ($num_identidad as $identidad): ?>
                <option value="<?php echo htmlspecialchars($identidad); ?>"><?php echo htmlspecialchars($identidad); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="input_form">
        <label for="cedula_ayudantes_${ayudantesCount}" class="input_form label">Cédula de identidad:</label>
        <input type="number" id="cedula_ayudantes_${ayudantesCount}" name="cedula_ayudantes[]" placeholder="Ingrese una cédula de identidad" class="input_form input" required maxlength="10" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
    </div>
    <div class="input_form">
        <button type="button" class="boton_agregar_ayudantes" onclick="agregarAyudante()">
            <i class='bx bx-plus'></i>
        </button>
        <button type="button" class="boton_quitar_ayudantes" onclick="quitarAyudante()">
            <i class='bx bx-minus'></i>
        </button>
    </div>
    <div class="adicional_input"></div>
</div>
<!-- CANTIDAD DE PERSONAS EN EL MOVIMIENTO-->
  <!-- HOMBRES -->
<div class="caja_form">
  <div class="input_form">
    <label for="cantidad_hombres">Cantidad de hombres:</label>
    <input type="number" id="cantidad_hombres" name="cantidad_hombres" required placeholder="Ingrese la cantidad">
  </div>
    <!-- MUJERES -->
  <div class="input_form">
    <label for="cantidad_mujeres">Cantidad de mujeres:</label>
    <input type="number" id="cantidad_mujeres" name="cantidad_mujeres" required placeholder="Ingrese la cantidad">
  </div>
    <!-- CANTIDADES -->
  <div id="resultado">
    <p class="p-cantidad">Cantidad de hombres: <span id="hombres">0</span></p>
    <p class="p-cantidad">Cantidad de mujeres: <span id="mujeres">0</span></p>
  </div>
    <!-- BOTONES -->
  <div class="botones">
    <button type="button" id="agregar">Agregar</button>
    <button type="button" id="eliminar">Eliminar</button>
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
let ayudantesCount = 0;

function agregarAyudante() {
    ayudantesCount++;
    const formContainer = document.getElementById('form_container');

    // Crear un contenedor para el nuevo ayudante
    const newDiv = document.createElement('div');
    newDiv.classList.add('input_form');
    newDiv.id = `ayudante_${ayudantesCount}`; 

    newDiv.innerHTML = `
    <label for="nombre_apellido_ayudantes_${ayudantesCount}" class="input_form label">Nombre y Apellido:</label>
    <input type="text" id="nombre_apellido_ayudantes_${ayudantesCount}" name="nombre_apellido_ayudantes[]" required placeholder="Ingrese nombre" class="input_form input">
    <label for="descripcion_cargo_ayudantes_${ayudantesCount}" class="input_form label">Cargo del ayudante:</label>
    <select id="descripcion_cargo_ayudantes_${ayudantesCount}" name="descripcion_cargo_ayudantes[]" required class="input_form input">
        <option value="">Seleccione el cargo</option>
        <?php foreach ($cargos_ayudantes as $cargo): ?>
            <option value="<?php echo htmlspecialchars($cargo); ?>"><?php echo htmlspecialchars($cargo); ?></option>
        <?php endforeach; ?>
    </select>
    <label for="num_id_ayudantes_${ayudantesCount}" class="input_form label">Número de identidad:</label>
    <select id="num_id_ayudantes_${ayudantesCount}" name="num_id_ayudantes[]" required class="input_form input">
        <option value="">Seleccione el número telefónico</option>
        <?php foreach ($num_identidad as $identidad): ?>
            <option value="<?php echo htmlspecialchars($identidad); ?>"><?php echo htmlspecialchars($identidad); ?></option>
        <?php endforeach; ?>
    </select>
    <label for="cedula_ayudantes_${ayudantesCount}" class="input_form label">Cédula de identidad:</label>
    <input type="number" id="cedula_ayudantes_${ayudantesCount}" name="cedula_ayudantes[]" placeholder="Ingrese una cédula de identidad" class="input_form input" required maxlength="10" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
`;

    formContainer.appendChild(newDiv);
}
function quitarAyudante() {
    const formContainer = document.getElementById('form_container');
    // Verificar si hay ayudantes para eliminar
    if (ayudantesCount > 0) {
        const lastAyudante = document.getElementById(`ayudante_${ayudantesCount}`);
        formContainer.removeChild(lastAyudante); 
        ayudantesCount--; 
    }
}
</script>
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

                // Agregar opciones al select (esto debería ser generado por PHP)
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
        updateMaxLength('num_id_lider', 'cedula_lider');
        updateMaxLength('num_id_ayudantes', 'cedula_ayudantes');
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