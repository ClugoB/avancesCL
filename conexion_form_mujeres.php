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

// VERIFICAR SI EL FORMULARIO SE HA ENVIADO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // OBTENER LOS VALORES DEL FORMULARIO Y ESPACAR DE LAS CADENAS PARA PREVENIR INYECCIONES EN SQL
    $nombre_movimiento = $_POST["nombre_movimiento"];
    $descripcion_movimiento = $_POST["descripcion_movimiento"];
    $razon_movimiento = $_POST["razon_movimiento"]; 
    $tipos_movimientos = $_POST["tipos_movimientos"]; 
    $ciudad_ubicado = $_POST["ciudad_ubicado"]; 
    $estado_ubicado = $_POST["estado_ubicado"];
    $municipio_ubicado = $_POST["municipio_ubicado"]; 
    $parroquia_ubicado = $_POST["parroquia_ubicado"]; 
    $estados_presentes = $_POST["estados_presentes"]; 
    $nombre_apellido_ayudantes = $_POST["nombre_apellido_ayudantes"];
    $descripcion_cargo_ayudantes = $_POST["descripcion_cargo_ayudantes"];
    $num_id_ayudantes = $_POST["num_id_ayudantes"];
    $cedula_ayudantes = $_POST["cedula_ayudantes"];
    $cantidad_hombres = $_POST["cantidad_hombres"];
    $cantidad_mujeres = $_POST["cantidad_mujeres"];
    $nombre_lider = $_POST["nombre_lider"];
    $apellido_lider = $_POST["apellido_lider"];
    $num_id_lider = $_POST["num_id_lider"];
    $cedula_lider = $_POST["cedula_lider"];
    $codigo_lider = $_POST["codigo_lider"];
    $numero_telefono_lider = $_POST["numero_telefono_lider"];
    $fecha_nacimiento_lider = $_POST["fecha_nacimiento_lider"];
    $correo_lider = $_POST["correo_lider"];
    $contrasena_movimiento = $_POST["contrasena_movimiento"]; 
    $confirmar_contrasena = $_POST["confirmar_contrasena"]; 

    // Inicializar un array para mensajes de error
    $error_messages = [];

    // VALIDACIÓN DE LA CONTRASEÑA
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $contrasena_movimiento)) {
        $error_messages[] = "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un símbolo.";
    } elseif ($contrasena_movimiento != $confirmar_contrasena) {
        $error_messages[] = "Las contraseñas no coinciden.";
    }

    // VERIFICAR SI EXISTE UN MOVIMIENTO CON EL MISMO NOMBRE
    $sql_check_movimiento = "SELECT COUNT(*) FROM form_mujeres WHERE nombre_movimiento = :nombre_movimiento";
    $stmt_check_movimiento = $conn->prepare($sql_check_movimiento);
    $stmt_check_movimiento->bindParam(':nombre_movimiento', $nombre_movimiento);
    $stmt_check_movimiento->execute();
    $count_movimiento = $stmt_check_movimiento->fetchColumn();

    // Si el movimiento ya existe, agregar un mensaje de error
    if ($count_movimiento > 0) {
        $error_messages[] = "Ya existe un movimiento con ese nombre.";
    }

    // VERIFICAR SI EXISTEN CEDULAS REPETIDAS
    $mensaje_error = [];
    
    // VERIFICA SI EXISTE LA CÉDULA DEL LÍDER
    $sql_check_lider = "SELECT COUNT(*) FROM form_mujeres WHERE cedula_lider = :cedula_lider";
    $stmt_check_lider = $conn->prepare($sql_check_lider);
    $stmt_check_lider->bindParam(':cedula_lider', $cedula_lider);
    $stmt_check_lider->execute();
    $count_lider = $stmt_check_lider->fetchColumn();
    if ($count_lider > 0) {
        $mensaje_error[] = "La cédula del líder ya existe.";
    }

    // VERIFICA SI EXISTE LA CÉDULA DEL AYUDANTE
    foreach ($cedula_ayudantes as $cedula_ayudante) {
        $sql_check_ayudantes = "SELECT COUNT(*) FROM datos_ayudantes WHERE cedula = :cedula_ayudantes"; // Cambiar a datos_ayudantes
        $stmt_check_ayudantes = $conn->prepare($sql_check_ayudantes);
        $stmt_check_ayudantes->bindParam(':cedula_ayudantes', $cedula_ayudante);
        $stmt_check_ayudantes->execute();
        $count_ayudantes = $stmt_check_ayudantes->fetchColumn();

        if ($count_ayudantes > 0) {
            $mensaje_error[] = "La cédula de uno de los ayudantes ya existe.";
        }
    }

    // Si hay errores, construir el mensaje de error
    if (!empty($error_messages) || !empty($mensaje_error)) {
       
        $mensaje_error_str = implode(" ", array_merge($error_messages, $mensaje_error));
        header("Location: form_movimientos.php?mensaje_erroneo=" . urlencode($mensaje_error_str));
        exit;
    }

    // DETERMINAR EL TIPO DE MOVIMIENTO
    $movementType = (count($estados_presentes) < 5) ? 'Micro movimiento' : 'Movimiento nacional';

    // ENCRIPTAR LA CONTRASEÑA
    $hashed_contrasena = password_hash($contrasena_movimiento, PASSWORD_ARGON2I, ['cost' => 15]);

    // INSERTA LOS DATOS EN LA TABLA DE FORMULARIO DE MUJERES 
    $sql_rep = "INSERT INTO form_mujeres (nombre_movimiento, descripcion_movimiento, razon_movimiento, tipos_movimientos, ciudad_ubicado, estado_ubicado, municipio_ubicado, parroquia_ubicado, estados_presentes, movimiento_tipo, cantidad_hombres, cantidad_mujeres, nombre_lider, apellido_lider, num_id_lider, cedula_lider, codigo_lider, numero_telefono_lider, fecha_nacimiento_lider, correo_lider, contrasena_movimiento, fecha_registro) 
    VALUES (:nombre_movimiento, :descripcion_movimiento, :razon_movimiento, :tipos_movimientos, :ciudad_ubicado, :estado_ubicado, :municipio_ubicado, :parroquia_ubicado, :estados_presentes, :movementType, :cantidad_hombres, :cantidad_mujeres, :nombre_lider, :apellido_lider, :num_id_lider, :cedula_lider, :codigo_lider, :numero_telefono_lider, :fecha_nacimiento_lider, :correo_lider, :contrasena_movimiento, NOW())";

    $stmt_rep = $conn->prepare($sql_rep);
    $stmt_rep->bindParam(':nombre_movimiento', $nombre_movimiento);
    $stmt_rep->bindParam(':descripcion_movimiento', $descripcion_movimiento);
    $stmt_rep->bindParam(':razon_movimiento', $razon_movimiento); 
    $stmt_rep->bindParam(':tipos_movimientos', $tipos_movimientos);
    $stmt_rep->bindParam(':ciudad_ubicado', $ciudad_ubicado);
    $stmt_rep->bindParam(':estado_ubicado', $estado_ubicado);
    $stmt_rep->bindParam(':municipio_ubicado', $municipio_ubicado);
    $stmt_rep->bindParam(':parroquia_ubicado', $parroquia_ubicado);
    
    // Convertir el array de estados presentes a una cadena
    $estados_presentes_implode = implode(',', $estados_presentes);
    $stmt_rep->bindParam(':estados_presentes', $estados_presentes_implode);
    
    $stmt_rep->bindParam(':movementType', $movementType);
    $stmt_rep->bindParam(':cantidad_hombres', $cantidad_hombres);
    $stmt_rep->bindParam(':cantidad_mujeres', $cantidad_mujeres);
    $stmt_rep->bindParam(':nombre_lider', $nombre_lider);
    $stmt_rep->bindParam(':apellido_lider', $apellido_lider);
    $stmt_rep->bindParam(':num_id_lider', $num_id_lider);
    $stmt_rep->bindParam(':cedula_lider', $cedula_lider);
    $stmt_rep->bindParam(':codigo_lider', $codigo_lider);
    $stmt_rep->bindParam(':numero_telefono_lider', $numero_telefono_lider);
    $stmt_rep->bindParam(':fecha_nacimiento_lider', $fecha_nacimiento_lider);
    $stmt_rep->bindParam(':correo_lider', $correo_lider);
    
    // Aquí es donde se utiliza la contraseña encriptada
    $stmt_rep->bindParam(':contrasena_movimiento', $hashed_contrasena); 

    // Ejecutar la consulta
    if (!$stmt_rep->execute()) {
        error_log("Error al insertar en el formulario: " . implode(", ", $stmt_rep->errorInfo()));
        die("Error al insertar los datos. Por favor, intenta de nuevo.");
    } else {
        // Obtener el ID del movimiento recién insertado
        $id_movimiento = $conn->lastInsertId();

// Preparar la inserción en la tabla de ayudantes
$sql_ayudantes = "INSERT INTO datos_ayudantes (id_movimiento, nombre_apellido, descripcion_cargo, num_id, cedula) 
                  VALUES (:id_movimiento, :nombre_apellido, :descripcion_cargo, :num_id, :cedula)";
$stmt_ayudantes = $conn->prepare($sql_ayudantes);

// Iterar sobre los ayudantes y realizar la inserción
for ($i = 0; $i < count($nombre_apellido_ayudantes); $i++) {
    // Obtener los datos de cada ayudante
    $nombre_apellido = $nombre_apellido_ayudantes[$i];
    $descripcion_cargo = $descripcion_cargo_ayudantes[$i];
    $num_id = $num_id_ayudantes[$i];
    $cedula = $cedula_ayudantes[$i];

    // Vincular los parámetros
    $stmt_ayudantes->bindParam(':id_movimiento', $id_movimiento);
    $stmt_ayudantes->bindParam(':nombre_apellido', $nombre_apellido);
    $stmt_ayudantes->bindParam(':descripcion_cargo', $descripcion_cargo);
    $stmt_ayudantes->bindParam(':num_id', $num_id);
    $stmt_ayudantes->bindParam(':cedula', $cedula);

    // Ejecutar la inserción para cada ayudante
    if (!$stmt_ayudantes->execute()) {
        error_log("Error al insertar ayudante: " . implode(", ", $stmt_ayudantes->errorInfo()));
    }
}

// Redirigir al usuario con un mensaje de éxito
header("Location: form_movimientos.php?mensaje_exitoso=Movimiento registrado correctamente");
exit;
    }
}

// CIERRA LA CONEXIÓN CON LA BASE DE DATOS
$conn = null;
?>