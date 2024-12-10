<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIIMM</title>
    <link rel="stylesheet" href="style_login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="boxicons-2.1.4/css/boxicons.min.css">
    <link rel="shortcut icon" href="imagenes/mujer3.jpg" type="image/x-icon">
</head>
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
// OBTENER LOS DATOS PARA EL CAMPO "NÚMERO DE IDENTIDAD"
$num_identidad = obtenerDatos($conn, 'num_identidad', 'identidad');
?>
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
                    <form action="validacion_login.php" method="post" class="input-caja">
                        <header class="titulo_fuer">Iniciar Sesión</header>
                        <div class="input-formulario">
                            <input type="text" name="usuario" id="usuario" class="input-caja-formulario" required maxlength="20">
                            <label for="usuario">Nombre de usuario</label>
                        </div>
                        <div class="input-formulario">
                            <input type="password" name="contrasena" id="contrasena" class="input-caja-formulario" required maxlength="16">
                            <label for="contrasena">Contraseña</label>
                            <span class="ojo">
                                <i class='bx bx-show' id="show"></i>
                                <i class='bx bx-hide' id="hide"></i>
                            </span>
                            <span><a href="#" id="olvidar-contrasena">¿Olvidaste tu contraseña?</a></span>
                        </div>
                        <button class="boton" type="submit">Iniciar Sesión</button>
                        <br>
                        <span><a href="registrarse.php">¿No tienes cuenta? Regístrate acá</a></span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ALERTAS -->
<?php
$mensaje_exitoso = '';
$mensaje_erroneo = '';
if (isset($_GET['mensaje_exitoso'])) {
    echo "<div class='mensaje_exitoso'>" . htmlspecialchars($_GET['mensaje_exitoso']) . "</div>";
}
if (isset($_GET['mensaje_erroneo'])) {
    echo "<div class='mensaje_erroneo'>" . htmlspecialchars($_GET['mensaje_erroneo']) . "</div>";
}
?> 
<!-- Modal para recuperar contraseña -->
<div id="modalRecuperar" class="modal">
    <div class="modal-contenido">
        <span class="cerrar" id="cerrarModal">&times;</span>
        <h2>Recuperar Contraseña</h2>
        <div class="input_form_container">
            <div class="input_form">
                <label for="num_id">Número de identidad:</label><br>
                <select id="num_id" name="num_id" required onchange="updateMaxLength()">
                    <option value="">Seleccione el número de identidad</option>
                    <?php foreach ($num_identidad as $identidad): ?>
                        <option value="<?php echo htmlspecialchars($identidad['identidad']); ?>"><?php echo htmlspecialchars($identidad['identidad']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input_form">
                <label for="cedula">Cédula de identidad:</label>
                <input type="number" id="cedula" name="cedula" placeholder="Ingrese una cédula de identidad" class="form-elementos" required maxlength="8" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
            </div>
        </div>
        <div class="input_form">
            <div class="input-formulario">
                <input type="text" name="nombre_usuario" id="nombre_usuario" class="input-caja-formulario" required maxlength="20">
                <label for="nombre_usuario">Usuario</label>
            </div>
        </div>
        <button class="boton_recu" type="button" id="enviarDatos">Continuar</button>
        <div id="mensajeRespuesta"></div>
    </div>
</div>
<!-- Modal de Pregunta de Seguridad -->
<div id="modalPreguntaSeguridad" class="modal">
    <div class="modal-contenido"> 
        <span class="cerrar" id="cerrarModalPregunta">&times;</span>
        <h3>Pregunta de Seguridad</h3>
        <p id="textoPreguntaSeguridad"></p>
        <div class="input_form"> 
            <label for="respuesta_seguridad">Respuesta:</label>
            <input type="text" id="respuesta_seguridad" name="respuesta_seguridad" required><br><br>
        </div>
        <button class="boton_recu" type="button" id="enviarRespuesta">Continuar</button>
        <div id="mensajeRespuestaSeguridad"></div>
    </div>
</div>
<!-- Modal para crear nueva contraseña -->
<div id="modalCrearContrasena" class="modal" style="display:none;">
    <div class="modal-contenido">
        <span class="cerrar" id="cerrarModalCrear">&times;</span>
        <h2>Crear Nueva Contraseña</h2>
        <div class="input_form">
            <label for="nueva_contrasena">Nueva Contraseña:</label>
            <input type="password" id="nueva_contrasena" name="nueva_contrasena" placeholder="Ingrese nueva contraseña" class="form-elementos" required>
        </div>
        <div class="input_form">
            <label for="confirmar_contrasena">Repetir Contraseña:</label>
            <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="Repita la nueva contraseña" class="form-elementos" required>
        </div>
        <button class="boton_recu" type="button" id="guardarContrasena">Guardar Contraseña</button>
        <div id="mensajeGuardar"></div>
    </div>
</div>
<!-- SCRIPTS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    document.getElementById('enviarDatos').addEventListener('click', () => {
        const nombreUsuario = document.getElementById('nombre_usuario').value;
        const numId = document.getElementById('num_id').value;
        const cedula = document.getElementById('cedula').value;
        const formData = new FormData();
        formData.append('nombre_usuario', nombreUsuario);
        formData.append('num_id', numId);
        formData.append('cedula', cedula);
        console.log("Datos enviados: ", { nombre_usuario: nombreUsuario, num_id: numId, cedula: cedula });
        fetch('verificar_datos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta del servidor: ", data); 
            if (data.success) {
                
                document.getElementById('modalRecuperar').style.display = 'none'; 
                document.getElementById('modalPreguntaSeguridad').style.display = 'block'; 
                document.getElementById('textoPreguntaSeguridad').innerText = data.pregunta_seguridad; 
                document.getElementById('nombre_usuario').value = nombreUsuario;
            } else {
                document.getElementById('mensajeRespuesta').innerText = data.message; 
            }
        })
        .catch(error => {
            console.error('Error:', error); 
            document.getElementById('mensajeRespuesta').innerText = 'Error de conexión. Inténtalo de nuevo.'; 
        });
    });

document.getElementById('enviarRespuesta').addEventListener('click', () => {
    const respuestaSeguridad = document.getElementById('respuesta_seguridad').value;
    const nombreUsuario = document.getElementById('nombre_usuario').value; 
    const formData = new FormData();
    formData.append('respuesta_seguridad', respuestaSeguridad);
    formData.append('nombre_usuario', nombreUsuario); 

    fetch('verificar_datos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Respuesta del servidor: ", data);
        if (data.success) {
 
            document.getElementById('modalPreguntaSeguridad').style.display = 'none'; 
            document.getElementById('modalCrearContrasena').style.display = 'block'; 
        } else {
  
            document.getElementById('mensajeRespuestaSeguridad').innerText = data.message; 
        }
    })
    .catch(error => {
        console.error('Error:', error); 
        document.getElementById('mensajeRespuestaSeguridad').innerText = 'Error de conexión. Inténtalo de nuevo.'; 
    });
});
    // Evento para abrir el modal de recuperación de contraseña
    document.getElementById('olvidar-contrasena').addEventListener('click', function(event) {
        event.preventDefault(); 
        document.getElementById('modalRecuperar').style.display = 'block'; 
    });
    // Eventos para cerrar los modales
    document.getElementById('cerrarModal').addEventListener('click', function() {
        document.getElementById('modalRecuperar').style.display = 'none'; 
    });
    document.getElementById('cerrarModalPregunta').addEventListener('click', function() {
        document.getElementById('modalPreguntaSeguridad').style.display = 'none'; 
    });
    document.getElementById('cerrarModalCrear').addEventListener('click', function() {
        document.getElementById('modalCrearContrasena').style.display = 'none'; 
    });
    // Cerrar los modales al hacer clic fuera de ellos
    window.addEventListener('click', function(event) {
        const modalRecuperar = document.getElementById('modalRecuperar');
        const modalPreguntaSeguridad = document.getElementById('modalPreguntaSeguridad');
        const modalCrearContrasena = document.getElementById('modalCrearContrasena');
        if (event.target === modalRecuperar) {
            modalRecuperar.style.display = 'none';
        }
        if (event.target === modalPreguntaSeguridad) {
            modalPreguntaSeguridad.style.display = 'none';
        }
        if (event.target === modalCrearContrasena) {
            modalCrearContrasena.style.display = 'none';
        }
    });
// Evento para crear una nueva contraseña
document.getElementById('guardarContrasena').addEventListener('click', () => {
    const nuevaContrasena = document.getElementById('nueva_contrasena').value;
    const confirmarContrasena = document.getElementById('confirmar_contrasena').value;
    const nombreUsuario = document.getElementById('nombre_usuario').value;
    // Validación de la contraseña
    const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    let errorMessage = '';
    if (!passwordRegex.test(nuevaContrasena)) {
        errorMessage = "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un símbolo.";
    } else if (nuevaContrasena !== confirmarContrasena) {
        errorMessage = "Las contraseñas no coinciden.";
    }
    if (errorMessage) {
        document.getElementById('mensajeGuardar').innerText = errorMessage;
        return; 
    }
    const formData = new FormData();
    formData.append('nueva_contrasena', nuevaContrasena);
    formData.append('nombre_usuario', nombreUsuario);

    fetch('crear_contrasena.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Respuesta del servidor: ", data);
        if (data.success) {
        
        window.location.href = 'admin.php?mensaje_exitoso=' + encodeURIComponent('Contraseña cambiada exitosamente.');
    } else {
        document.getElementById('mensajeGuardar').innerText = data.message; 
    }
})
    .catch(error => {
        console.error('Error:', error); 
        document.getElementById('mensajeGuardar').innerText = 'Error de conexión. Inténtalo de nuevo.'; 
    });
});     
});
</script>
<script>
// Script para mostrar y ocultar la contraseña
const show = document.getElementById('show');
const hide = document.getElementById('hide');
const contrasena = document.getElementById('contrasena');

show.addEventListener('click', () => {
    contrasena.type = 'text';
    show.style.display = 'none';
    hide.style.display = 'block';
});
hide.addEventListener('click', () => {
    contrasena.type = 'password';
    show.style.display = 'block';
    hide.style.display = 'none';
});
</script>
<script>
// Script para ocultar mensajes de éxito y error después de 5 segundos
setTimeout(function() {
    document.querySelectorAll('.mensaje_exitoso,.mensaje_erroneo').forEach(function(element) {
        element.style.display = 'none';
    });
}, 5000);
</script>
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
</body>
</html>