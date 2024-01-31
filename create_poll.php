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
        $dsn = "mysql:host=localhost;dbname=votadb";        
        $username = "root";
        $password = "Pepe25"; // AWS24VotaPRRojo_
        
        $pdo = new PDO($dsn, $username, $password);

        $pdo->beginTransaction();

        $true = 2;


        // Insertar la encuesta en la tabla Surveys
        $query_survey = $pdo->prepare("INSERT INTO Survey (user_id, survey_title, start_date, end_date, public_title )
                                       VALUES (:owner_id, :question_text, :start_time, :end_time, :isPublished)");
        $query_survey->bindParam(':owner_id', $_SESSION['usuario']);
        $query_survey->bindParam(':question_text', $question_text);
        $query_survey->bindParam(':start_time', $start_date);
        $query_survey->bindParam(':end_time', $end_date);
        $query_survey->bindParam(':isPublished', $true);

        $query_survey->execute();

        $survey_id = $pdo->lastInsertId();

        // Insertar opciones en la tabla 

        foreach ($options as $index => $ans) {
            $query_options = $pdo->prepare("INSERT INTO SurveyOption (option_text, survey_id) VALUES (:option_text, :survey_id)");
            $query_options->bindParam(':option_text', $ans);
            $query_options->bindParam(':survey_id', $survey_id);
            $query_options->execute();
            $answerId = $pdo->lastInsertId();
            echo $answerId;
          
                if (!empty($_FILES['img_ans']['name'][$index][$answerIndex])) {
                    $directorioDestino = 'uploads/';
                    $fileType = $_FILES['img_ans']['type'][$index];
                    $fileName = $_FILES['img_ans']['name'][$index];
                    $nombreImagenUnico = generarNombreUnico($fileName);

                    $newFileName = $nombreImagenUnico;
                    $targetPath = $directorioDestino . $newFileName;

                    if (move_uploaded_file($_FILES['img_ans']['tmp_name'][$index], $targetPath)) {
                        $queryInsertImagen = $pdo->prepare("UPDATE SurveyOption SET imag = :imgurl WHERE option_id = :ansid");
                        $queryInsertImagen->bindParam(':imgurl', $targetPath);
                        $queryInsertImagen->bindParam(':ansid', $answerId );
                        $queryInsertImagen->execute();
                    } else {
                        //aqui van logs
                    }
                }else{
                    //logs
                }                
        
    }

        $pdo->commit();

        echo "<script>successfulNotification('Tu encuesta se ha creado correctamente.');</script>";

        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        //$pdo->rollBack();
        echo "Error al crear la encuesta: " . $e->getMessage();
        echo "<script> errorNotification('Ha habido un error al crear la encuesta, por favor, vuelva a crearla o intentalo en otro momento.'); </script>";
    } finally {
        $pdo = null;
    }
}

function generarNombreUnico($nombre_original) {
    // Obtiene la extensión del archivo
    $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
    
    // Genera un nombre único basado en la fecha y hora actual
    $nombre_unico = uniqid() . '_' . time() . '.' . $extension;

    return $nombre_unico;
}
?>