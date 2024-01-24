<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
?> 

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear encuesta | Vota!</title>
    <meta name="description" content="Crea una encuesta para obtener las respuestas de todo el mundo!">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
</head>

<body>
<?php
include_once("common/header.php")
?>
    <main id="createPoll">
        <h1>Crear Encuesta</h1>

        <form method="post">
            <input type="text" id="question" name="question" placeholder="Pregunta de la encuesta" required>
            <br>
            <div id="optionsContainer">
                <input type="text" name="options[]" placeholder="Respuesta" required>
                <input type="text" name="options[]" placeholder="Respuesta" required>
            </div>
            <button type="button" onclick="addOption()" id="addButton"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                    <path d="M13.5 6.5l4 4" />
                    <path d="M16 19h6" />
                    <path d="M19 16v6" />
                </svg>  Añadir respuesta</button>
            <button type="button" onclick="deleteOption()" id="deleteButton"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eraser" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3" /><path d="M18 13.3l-6.3 -6.3" /></svg>Eliminar última respuesta</button>
            <br>
            <label for="start_date">Fecha de Inicio:</label>
            <input type="datetime-local" id="start_date" name="start_date" required>

            <label for="end_date">Fecha de Finalización:</label>
            <input type="datetime-local" id="end_date" name="end_date" required>
            <br>
            <button type="submit">Crear Encuesta</button>
        </form>
    </main>
    <?php
include_once("common/footer.php")
?>

    <ul id="notification__list">
        <!-- todas las notificaciones -->
    </ul>
<script src="componentes/notificationHandler.js"></script>
<script src="handleCreatePoll.js"></script>
</body>

</html>

<?php

if (isset($_POST['options']) && isset($_POST['question'])) {

    $question_text = $_POST['question'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $options = $_POST['options'];

    try {
        // Cambiar parámetros de conexión a BD
        $dsn = "mysql:host=root;dbname=votadb";        
        $username = "localhost";
        $password = "AWS24VotaPRRojo_";
        
        // Conectar a la base de datos
        $pdo = new PDO($dsn, $username, $password);

        // Iniciar transacción
        $pdo->beginTransaction();

        // Insertar la encuesta en la tabla Surveys
        $query_survey = $pdo->prepare("INSERT INTO Surveys (owner_id, question_text, start_time, end_time)
                                       VALUES (:owner_id, :question_text, :start_time, :end_time)");
        $query_survey->bindParam(':owner_id', $_SESSION['usuario']);
        $query_survey->bindParam(':question_text', $question_text);
        $query_survey->bindParam(':start_time', $start_date);
        $query_survey->bindParam(':end_time', $end_date);

        $query_survey->execute();

        $survey_id = $pdo->lastInsertId();

        // Insertar opciones en la tabla 
        $query_options = $pdo->prepare("INSERT INTO Questions (option_text, survey_id) VALUES (:option_text, :survey_id)");

        foreach ($options as $option_text) {
            $query_options->bindParam(':option_text', $option_text);
            $query_options->bindParam(':survey_id', $survey_id);
            $query_options->execute();
        }

        $pdo->commit();

        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error al crear la encuesta: " . $e->getMessage();
    } finally {
        $pdo = null;
    }
}
?>