<style>
    .buttons {
        display: flex;
        position: fixed;
        bottom: 40px;
        right: -450;
        z-index: 100;
        padding: 15px;
        background: aliceblue;
        border-radius: 10px;
        grid-gap: 10px;
        align-items: center;
        animation: welcome 1s ease-out forwards;
        cursor: pointer;
    }

    .buttons:hover {
        border: 1px solid black;
    }

    @keyframes welcome {
        0% {
            right: -450;
        }

        100% {
            right: 0;
        }
    }

    .container {
        display: none;
    }

    .container {
        display: flex;
        flex-direction: column;
        position: fixed;
        z-index: 100000;
        background-color: white;
        left: 50%;
        top: 50%;
        bottom: 0;
        transform: translate(-50%, -50%);
        padding: 20px;
        border-radius: 10px;
        overflow-y: scroll;
        box-shadow: 0px 4px 9px 0px gray;
        width: 700px;
        height: 500px;
    }

    .container form {
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .container form .cerrar {
        position: absolute;
        right: 0;
        background: red;
        color: white;
        padding: 5px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        grid-gap: 5px;
        font-size: 12px;
        cursor: pointer;
    }

    .container form .cerrar:hover {
        box-shadow: 0px 4px 6px 0px #ddd;
    }

    .container form .cerrar ion-icon {
        font-size: 15px;
    }

    .container form .id {
        display: flex;
        align-items: center;
        grid-gap: 10px;
        margin-top: 20px;
        width: -webkit-fill-available;
    }

    .container form .id .number {
        width: 50px;
        padding: 20px;
        height: 40px;
        background-color: darkorchid;
        border-radius: 5px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container form .id .text {
        display: flex;
        flex-direction: column;
        grid-gap: 5px;
        width: -webkit-fill-available;
    }

    .container form .id .text select,
    .container form .id .text textarea {
        padding: 5px 12px;
        border-radius: 5px;
        width: -webkit-fill-available;
        cursor: pointer;
    }

    .container form button {
        padding: 10px;
        background: darkorchid;
        border: none;
        border-radius: 5px;
        color: white;
        text-transform: uppercase;
        cursor: pointer;
    }

    .container form button:hover {
        background-color: purple;
    }

    .buttons,
    .cerrar {
        cursor: pointer;
    }
</style>

<div class="buttons">
    <ion-icon name="person-outline"></ion-icon>
    <p>¿Quieres Participar en Nuestra Encuesta?</p>
    <ion-icon name="open-outline"></ion-icon>
</div>

<?php 
// Verifica si la sesión ya está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}
include 'conexion_bd.php';
try {
    $conn = new PDO("mysql:host=$servidor;dbname=$base_de_datos;charset=utf8mb4", $usuario, $contrasena);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
    $conn->exec("SET NAMES 'utf8mb4'"); // Asegura que se use UTF-8

    // Verifica si el usuario está en la sesión
    if (!isset($_SESSION['usuario'])) {
        header("Location: admin.php"); 
        exit();
    }

    // Obtiene el nombre de usuario de la sesión
    $nombre_usuario = $_SESSION['usuario'];
    // Inicializa los mensajes
    $mensaje_exitoso = '';
    $mensaje_erroneo = '';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtiene las respuestas de las preguntas del formulario
        $preguntas = [];
        for ($i = 1; $i <= 68; $i++) {
            if (isset($_POST["pregunta$i"])) {
                // Sanitiza la entrada para evitar inyecciones
                $preguntas["pregunta$i"] = htmlspecialchars(trim($_POST["pregunta$i"]));
            }
        }

        // Verifica si el usuario ya participó en la encuesta
        $checkQuery = $conn->prepare("SELECT COUNT(*) FROM encuesta WHERE nombre_usuario = :nombre_usuario");
        $checkQuery->execute([':nombre_usuario' => $nombre_usuario]);
        if ($checkQuery->fetchColumn() > 0) {
            $mensaje_erroneo = "Ya has participado en esta encuesta.";
        } else {
            // Inserta las respuestas en la base de datos
            $sql = "INSERT INTO encuesta (nombre_usuario, fecha_ingresado, " . implode(", ", array_keys($preguntas)) . ") VALUES (:nombre_usuario, NOW(), " . implode(", ", array_map(function($key) { return ":$key"; }, array_keys($preguntas))) . ")";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nombre_usuario', $nombre_usuario);

            // Cambia bindParam por bindValue para cada respuesta
            foreach ($preguntas as $key => $value) {
                $stmt->bindValue(":$key", $value); 
            }
            $stmt->execute();

            $mensaje_exitoso = '¡Gracias por participar en Nuestra Encuesta!';
        }
    }
} catch (PDOException $e) {
    $mensaje_erroneo = "Error: " . htmlspecialchars($e->getMessage());
}
$conn = null;
?>
<div class="container" style="display: none;">
    <form action="" method="post">
        <div class="cerrar">
            <ion-icon name="close-circle"></ion-icon>
            No participar
        </div>

        <h3>Plan Parto Humanizado:</h3>

        <div class="id">
            <div class="number">
                <b>1</b>
            </div>
            <div class="text">
                <label for="pregunta1">¿Estás familiarizado con el concepto de parto humanizado?</label>
                <select name="pregunta1" id="pregunta1" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>2</b>
            </div>
            <div class="text">
                <label for="pregunta2">¿Has tenido alguna experiencia previa de parto humanizado?</label>
                <select name="pregunta2" id="pregunta2" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>3</b>
            </div>
            <div class="text">
                <label for="pregunta3">¿Qué aspectos consideras más importantes en un parto humanizado?</label>
                <textarea name="pregunta3" id="pregunta3" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>4</b>
            </div>
            <div class="text">
                <label for="pregunta4">¿Qué tipo de apoyo te gustaría recibir durante el proceso de parto?</label>
                <textarea name="pregunta4" id="pregunta4" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>5</b>
            </div>
            <div class="text">
                <label for="pregunta5">¿Cómo valorarías la importancia de la participación activa de la mujer durante el trabajo de parto?</label>
                <textarea name="pregunta5" id="pregunta5" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>6</b>
            </div>
            <div class="text">
                <label for="pregunta6">¿Crees que es importante respetar las decisiones de la mujer durante el parto?</label>
                <select name="pregunta6" id="pregunta6" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>7</b>
            </div>
            <div class="text">
                <label for="pregunta7">¿Consideras que la comunicación con el personal médico es fundamental en un parto humanizado?</label>
                <select name="pregunta7" id="pregunta7" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>8</b>
            </div>
            <div class="text">
                <label for="pregunta8">¿Recomendarías el parto humanizado a otras mujeres embarazadas?</label>
                <select name="pregunta8" id="pregunta8" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <br>
        <h3>Plan de Prevención y Reducción del Embarazo a Temprana Edad y en la Adolescencia, (PRETA):</h3>

        <div class="id">
            <div class="number">
                <b>1</b>
            </div>
            <div class="text">
                <label for="pregunta9">¿Estás familiarizado con el Plan de Prevención y Reducción del Embarazo a Temprana Edad y en la Adolescencia?</label>
                <select name="pregunta9" id="pregunta9" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>2</b>
            </div>
            <div class="text"> <label for="pregunta10">¿Crees que el acceso a información sobre anticoncepción es importante para prevenir embarazos en la adolescencia?</label>
                <select name="pregunta10" id="pregunta10" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>3</b>
            </div>
            <div class="text">
                <label for="pregunta11">¿Cuál es tu disponibilidad de métodos anticonceptivos?</label>
                <textarea name="pregunta11" id="pregunta11" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>4</b>
            </div>
            <div class="text">
                <label for="pregunta12">¿Has participado en alguna actividad o programa relacionado con la prevención del embarazo en la adolescencia?</label>
                <select name="pregunta12" id="pregunta12" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>5</b>
            </div>
            <div class="text">
                <label for="pregunta13">¿Necesitas alguna ayuda? ¿qué tipo de ayuda? </label>
                <textarea name="pregunta13" id="pregunta13" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>6</b>
            </div>
            <div class="text">
                <label for="pregunta14">¿Qué sugerencias propondrías para mejorar las estrategias de prevención del embarazo en la adolescencia?</label>
                <textarea name="pregunta14" id="pregunta14" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <br>
        <h3>Unidades Móviles de Atención:</h3>

        <div class="id">
            <div class="number">
                <b>1</b>
            </div>
            <div class="text">
                <label for="pregunta15">¿Estás familiarizado con el concepto de Unidades Móviles de Atención?</label>
                <select name="pregunta15" id="pregunta15" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>2</b>
            </div>
            <div class="text">
                <label for="pregunta16">¿Has utilizado alguna vez una Unidad Móvil de Atención para acceder a servicios de salud u otros servicios sociales?</label>
                <select name="pregunta16" id="pregunta16" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>3</b>
            </div>
            <div class="text">
                <label for="pregunta17">¿Cómo evaluarías la accesibilidad de las Unidades Móviles de Atención en tu comunidad?</label>
                <textarea name="pregunta17" id="pregunta17" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>4</b>
            </div>
            <div class="text">
                <label for="pregunta18">¿Qué tipo de servicios te gustaría que ofrecieran las Unidades Móviles de Atención en tu área?</label>
                <textarea name="pregunta18" id="pregunta18" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>5</b>
            </div>
            <div class="text">
                <label for="pregunta19">¿Crees que las Unidades Móviles de Atención son una alternativa efectiva a los servicios tradicionales de atención?</label>
                <select name="pregunta19" id="pregunta19" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>6</b>
            </div>
            <div class="text">
                <label for="pregunta20">¿Consideras que las Unidades Móviles de Atención son un recurso importante para comunidades rurales o de difícil acceso?</label>
                <select name="pregunta20" id="pregunta20" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>7</b>
            </div>
            <div class="text">
                <label for="pregunta21">En una escala del 1 al 10, ¿qué tan satisfecho estás con la calidad de atención recibida en las Unidades Móviles de Atención?</label>
                <textarea name="pregunta21" id="pregunta21" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <br>
        <h3>Farmamujer:</h3>

        <div class="id">
            <div class="number">
                <b>1</b>
            </div>
            <div class="text">
                <label for="pregunta22">¿Con qué frecuencia visitas el sitio web de Farmamujer?</label>
                <textarea name="pregunta22" id="pregunta22" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>2</b>
            </div>
            <div class="text">
                <label for="pregunta23">¿Qué tipo de contenido te resulta más útil en Farmamujer? (artículos de salud, consejos de bienestar, recomendaciones de productos, etc.)</label>
                <textarea name="pregunta23" id="pregunta23" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>3</b>
            </div>
            <div class="text">
                <label for="pregunta24">¿Consideras que la información proporcionada en Farmamujer es confiable y precisa?</label>
                <select name="pregunta24" id="pregunta24" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>4</b>
            </div>
            <div class="text">
                <label for="pregunta25">¿Te gustaría ver más contenido sobre algún tema específico en Farmamujer? Si es así, ¿cuál?</label>
                <textarea name="pregunta25" id="pregunta25" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>5</b>
            </div>
            <div class="text">
                <label for="pregunta26">¿Has realizado alguna compra de productos recomendados en Farmamujer? ¿Cómo ha sido tu experiencia?</label>
                <textarea name="pregunta26" id="pregunta26" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>6</b>
            </div>
            <div class="text">
                <label for="pregunta27">¿Cómo calificarías la experiencia general de navegación en el sitio web de Farmamujer?</label>
                <textarea name="pregunta27" id="pregunta27" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>7</b>
            </div>
            <div class="text">
                <label for="pregunta28">¿Recomendarías Farmamujer a tus amigos y familiares en busca de información sobre salud y bienestar?</label>
                <select name="pregunta28" id="pregunta28" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <br>
        <h3>Instituto Nacional de la Mujer (INAMUJER):</h3>

        <div class="id">
            <div class="number">
                <b>1</b>
            </div>
            <div class="text">
                <label for="pregunta29">¿Estás familiarizado con el Instituto Nacional de la Mujer (INAMUJER)?</label>
                <select name="pregunta29" id="pregunta29" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>2</b>
            </div>
            <div class="text">
                <label for="pregunta30">¿Crees que el INAMUJER ha sido efectivo en la promoción de los derechos de las mujeres en tu comunidad?</label>
                <select name="pregunta30" id="pregunta30" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>3</b>
            </div>
            <div class="text">
                <label for="pregunta31">¿Has utilizado los servicios o recursos que ofrece el INAMUJER?</label>
                <select name="pregunta31" id="pregunta31" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>4</b>
            </div>
            <div class="text">
                <label for="pregunta32">¿Consideras que el INAMUJER ha tenido un impacto positivo en la lucha contra la violencia de género?</label>
                <select name="pregunta32" id="pregunta32" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>5</b>
            </div>
            <div class="text">
                <label for="pregunta33">¿Cómo calificarías la accesibilidad y disponibilidad de los servicios del INAMUJER en tu área?</label>
                <textarea name="pregunta33" id="pregunta33" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>6</b>
            </div>
            <div class="text">
                <label for="pregunta34">¿Qué mejoras crees que podrían implementarse en el INAMUJER para beneficiar a las mujeres de la comunidad?</label>
                <textarea name="pregunta34" id="pregunta34" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>7</b>
            </div>
            <div class="text">
                <label for="pregunta35">¿Sientes que el INAMUJER es una institución importante para apoyar y empoderar a las mujeres en el país?</label>
                <select name="pregunta35" id="pregunta35" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>8</b>
            </div>
            <div class="text">
                <label for="pregunta36">¿Has participado en alguna campaña o evento organizado por el INAMUJER?</label>
                <select name="pregunta36" id="pregunta36" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>9</b>
            </div>
            <div class="text">
                <label for="pregunta37">¿Cuál es tu opinión sobre la labor que realiza el INAMUJER en la sensibilización sobre temas de género?</label>
                <textarea name="pregunta37" id="pregunta37" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>10</b>
            </div>
            <div class="text">
                <label for="pregunta38">¿Recomendarías los servicios del INAMUJER a otras mujeres que necesiten apoyo en temas de igualdad y empoderamiento?</label>
                <select name="pregunta38" id="pregunta38" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <br>
        <h3>Defensoría Nacional de los Derechos de las Mujeres:</h3>

        <div class="id">
            <div class="number">
                <b>1</b>
            </div>
            <div class="text">
                <label for="pregunta39">¿Sabes cuál es el objetivo principal de la Defensoría Nacional de los Derechos de la Mujer?</label>
                <select name="pregunta39" id="pregunta39" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>2</b>
            </div>
            <div class="text">
                <label for="pregunta40">¿Sabes qué tipo de servicios ofrece la Defensoría Nacional de los Derechos de la Mujer?</label>
                <select name="pregunta40" id="pregunta40" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>3</b>
            </div>
            <div class="text">
                <label for="pregunta41">¿Sabes cómo puedes contactar a la Defensoría Nacional de los Derechos de la Mujer en caso de necesitar ayuda o asesoramiento?</label>
                <select name="pregunta41" id="pregunta41" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <br>
        <h3>Oficina de Atención a la Víctima:</h3>

        <div class="id">
            <div class="number">
                <b>1</b>
            </div>
            <div class="text">
                <label for="pregunta42">¿Sabes dónde queda la ubicación física de la Oficina de Atención a la Víctima más cercana a tu domicilio?</label>
                <select name="pregunta42" id="pregunta42" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>2</b>
            </div>
            <div class="text">
                <label for="pregunta43">¿Conoces el horario de atención de la Oficina de Atención a la Víctima y cuál es el procedimiento para solicitar una cita?</label>
                <select name="pregunta43" id="pregunta43" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>3</b>
            </div>
            <div class="text">
                <label for="pregunta44">¿Sabes qué tipo de servicios ofrece la Oficina de Atención a la Víctima a las personas que han sufrido algún tipo de delito o violencia?</label>
                <select name="pregunta44" id="pregunta44" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>4</b>
            </div>
            <div class="text">
                <label for="pregunta45">¿Conoces el procedimiento a seguir para presentar una denuncia en la Oficina de Atención a la Víctima y cuáles son los plazos y requisitos necesarios?</label>
                <select name="pregunta45" id="pregunta45" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>5</b>
            </div>
            <div class="text">
                <label for="pregunta46">¿Sabías que la atención en la Oficina de Atención a la Víctima es confidencial y gratuita?</label>
                <select name="pregunta46" id="pregunta46" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>6</b>
            </div>
            <div class="text">
                <label for="pregunta47">¿Te gustaría saber las medidas que se toman para proteger la privacidad de las víctimas?</label>
                <textarea name="pregunta47" id="pregunta47" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <br>
        <h3>Plan de Financiamiento:</h3>

        <div class="id">
            <div class="number">
                <b>1</b>
            </div>
            <div class="text">
                <label for="pregunta48">¿Conoces el plan de financiamiento del Ministerio de la Mujer?</label>
                <select name="pregunta48" id="pregunta48" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>2</b>
            </div>
            <div class="text">
                <label for="pregunta49">¿Te gustaría recibir apoyo financiero a través de este plan?</label>
                <select name="pregunta49" id="pregunta49" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>3</b>
            </div>
            <div class="text">
                <label for="pregunta50">¿Cuál es el principal motivo por el que necesitas ayuda financiera? (Puedes seleccionar más de una opción)</label>
                <textarea name="pregunta50" id="pregunta50" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>4</b>
            </div>
            <div class="text">
                <label for="pregunta51">¿Qué tipo de apoyo consideras más importante?</label>
                <textarea name="pregunta51" id="pregunta51" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>5</b>
            </div>
            <div class="text">
                <label for="pregunta52">¿Has intentado acceder a este plan anteriormente?</label>
                <select name="pregunta52" id="pregunta52" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>6</b>
            </div>
            <div class="text">
                <label for="pregunta53">Si respondiste "Sí", ¿cómo fue tu experiencia al intentar acceder al financiamiento?</label>
                <textarea name="pregunta53" id="pregunta53" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>7</b>
            </div>
            <div class="text">
                <label for="pregunta54">¿Qué te gustaría que se mejorara en el proceso de acceso al financiamiento?</label>
                <textarea name="pregunta54" id="pregunta54" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <br>
        <h3>Plan de Emprendimiento:</h3>

        <div class="id">
            <div class="number">
                <b>1</b>
            </div>
            <div class="text">
                <label for="pregunta55">¿Conoces el Plan de Emprendimiento del Ministerio de la Mujer?</label>
                <select name="pregunta55" id="pregunta55" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>2</b>
            </div>
            <div class="text">
                <label for="pregunta56">¿Te gustaría recibir apoyo para iniciar o mejorar tu emprendimiento?</label>
                <select name="pregunta56" id="pregunta56" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>3</b>
            </div>
            <div class="text">
                <label for="pregunta57">¿Cuál es tu principal motivación para emprender?</label>
                <textarea name="pregunta57" id="pregunta57" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>4</b>
            </div>
            <div class="text">
                <label for="pregunta58">¿Qué tipo de apoyo consideras más importante para tu emprendimiento?</label>
                <textarea name="pregunta58" id="pregunta58" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>5</b>
            </div>
            <div class="text">
                <label for="pregunta59">¿Has intentado acceder a este plan anteriormente?</label>
                <select name="pregunta59" id="pregunta59" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>6</b>
            </div>
            <div class="text">
                <label for="pregunta60">Si respondiste "Sí", ¿cómo calificarías tu experiencia al intentar acceder al apoyo?</label>
                <textarea name="pregunta60" id="pregunta60" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>7</b>
            </div>
            <div class="text">
                <label for="pregunta61">¿Qué aspectos del plan de emprendimiento crees que deberían mejorarse?</label>
                <textarea name="pregunta61" id="pregunta61" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>8</b>
            </div>
            <div class="text">
                <label for="pregunta62">¿Tienes alguna otra sugerencia o comentario sobre el plan de emprendimiento?</label>
                <textarea name="pregunta62" id="pregunta62" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <br>
        <h3>Plan de Adjudicación de Tierras:</h3>

        <div class="id">
            <div class="number">
                <b>1</b>
            </div>
            <div class="text">
                <label for="pregunta63">¿Conoces el Plan de Adjudicación de tierras del Ministerio de la Mujer?</label>
                <select name="pregunta63" id="pregunta63" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>2</b>
            </div>
            <div class="text">
                <label for="pregunta64">¿Eres mujer y trabajas en el sector agrícola?</label>
                <select name="pregunta64" id="pregunta64" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>3</b>
            </div>
            <div class="text">
                <label for="pregunta65">¿Consideras que el acceso a tierras es un problema para las mujeres del sector agrícola?</label>
                <select name="pregunta65" id="pregunta65" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>4</b>
            </div>
            <div class="text">
                <label for="pregunta66">¿Qué tipo de apoyo consideras más necesario para las mujeres agrícolas?</label>
                <textarea name="pregunta66" id="pregunta66" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>5</b>
            </div>
            <div class="text">
                <label for="pregunta67">¿Te gustaría participar en programas de capacitación relacionados con la agricultura?</label>
                <select name="pregunta67" id="pregunta67" required>
                    <option value="">Seleccionar</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="id">
            <div class="number">
                <b>6</b>
            </div>
            <div class="text">
                <label for="pregunta68">¿Qué tan importante crees que es el empoderamiento de las mujeres en la agricultura para el desarrollo del país?</label>
                <textarea name="pregunta68" id="pregunta68" placeholder="Responde Adecuadamente." required></textarea>
            </div>
        </div>

        <br>

        <button type="submit">¡Completar Encuesta!</button>

    </form>
</div>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<script>
    document.querySelector('.buttons').addEventListener('click', function() {
        document.querySelector('.container').style.display = 'block';
        document.querySelector('.buttons').style.display = 'none';
    });

    document.querySelector('.cerrar').addEventListener('click', function() {
        document.querySelector('.container').style.display = 'none';
    });
</script>