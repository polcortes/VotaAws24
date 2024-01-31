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
    <script src="/componentes/notificationHandler.js"></script>
</head>

<body>
<?php
include_once("common/header.php")
?>
    <main id="createPoll">
        <h1>Crear Encuesta</h1>


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
        $dsn = "mysql:host=localhost;dbname=votadb";        
        $username = "root";
        $password = "p@raMor3"; // AWS24VotaPRRojo_
        
        // Conectar a la base de datos
        $pdo = new PDO($dsn, $username, $password);

        // Iniciar transacción
        $pdo->beginTransaction();

        $true = true;
        $private = 'private';

        // Insertar la encuesta en la tabla Surveys
        $query_survey = $pdo->prepare("INSERT INTO Survey (user_id, survey_title, start_date, end_date, public_title, public_results)
                                       VALUES (:owner_id, :question_text, :start_time, :end_time, :isPublished, :public_results)");
        $query_survey->bindParam(':owner_id', $_SESSION['usuario']);
        $query_survey->bindParam(':question_text', $question_text);
        $query_survey->bindParam(':start_time', $start_date);
        $query_survey->bindParam(':end_time', $end_date);
        $query_survey->bindParam(':isPublished', $true);
        $query_survey->bindParam(":public_results", $private);

        $query_survey->execute();

        $survey_id = $pdo->lastInsertId();

        // Insertar opciones en la tabla 
        $query_options = $pdo->prepare("INSERT INTO SurveyOption (option_text, survey_id) VALUES (:option_text, :survey_id)");

        foreach ($options as $option_text) {
            $query_options->bindParam(':option_text', $option_text);
            $query_options->bindParam(':survey_id', $survey_id);
            $query_options->execute();
        }

        $pdo->commit();

        echo "<script>successfulNotification('Tu encuesta se ha creado correctamente.');</script>";

        //header("Location: dashboard.php");
        //exit();
    } catch (PDOException $e) {
        //$pdo->rollBack();
        echo "Error al crear la encuesta: " . $e->getMessage();
        echo "<script> errorNotification('Ha habido un error al crear la encuesta, por favor, vuelva a crearla o intentalo en otro momento.'); </script>";
    } finally {
        $pdo = null;
    }
}
?>