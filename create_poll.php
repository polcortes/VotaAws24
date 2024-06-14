<?php
try {
    $logFilePath = "logs/log" . date("d-m-Y") . ".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }
    $filePathParts = explode("/", __FILE__);
} catch (Exception $e) {
    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear encuesta | Vota!</title>
    <meta name="description" content="Crea una encuesta para obtener las respuestas de todo el mundo!">
    <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">

    <link rel="stylesheet" href="styles.css?<?php echo date('Y-m-d_H:i:s'); ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="componentes/notificationHandler.js"></script>
</head>

<body id="createPoll">
    <ul id="notification__list"></ul>

    <?php
    include_once("common/header.php");
    if (!isset($_SESSION["usuario"])) {
        header("HTTP/1.1 403 Forbidden");
        exit();
    }

    if (!isset($_SESSION['usuario'])) {
        header('Location: login.php');
        exit();
    }
    try {
        require 'data/dbAccess.php';
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
    } catch (PDOException $e) {
        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― DB ERROR]: Error al conectarse a la base de datos: " . $e->getMessage() . "\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
        echo "<script>errorNotification('ERROR al conectarse con la base de datos -> " . $e->getMessage() . "')</script>";
    }
    ?>
    <main>
        <h1>Crear Encuesta</h1>
    </main>
    <?php
    include_once("common/footer.php")
        ?>

    <script src="componentes/createPoll.js"></script>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― SURVEY FORM SEND]: Se ha enviado el formulario de la encuesta.\n";
    file_put_contents($logFilePath, $logTxt, FILE_APPEND);

    $user_id = $_SESSION['usuario'];
    $survey_title = $_POST["question"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $public_title = "hidden";
    $public_results = "hidden";
    $survey_image = "";
    $surveryImageName = "";

    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― SURVEY RECIVED]: Se han obtenido las variables del formulario (solo survey).\n";
    file_put_contents($logFilePath, $logTxt, FILE_APPEND);

    $max_file_size = 50 * 1024;

    try {
        if ($_FILES["surveyImage"]["name"]) {
            $upload_directory = "uploads/survey/";
            if (!file_exists($upload_directory)) {
                mkdir($upload_directory, 0777, true);

                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― DIRECTORY CREATED]: Se ha creado el directorio /uploads/survey\n";
                file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            }
            if ($_FILES["surveyImage"]["size"] > $max_file_size) {
                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― IMAGE UPLOAD ERROR]: Error al subir la imagen de la encuesta: El archivo es demasiado grande. El tamaño máximo permitido es de 50 KB\n";
                file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                echo "<script>errorNotification('El archivo es demasiado grande. El tamaño máximo permitido es de 50 KB.')</script>";
            } else {
                $surveryImageName = uniqid() . "." . pathinfo($_FILES["surveyImage"]["name"], PATHINFO_EXTENSION);
                $survey_image = $upload_directory . $surveryImageName;
                move_uploaded_file($_FILES["surveyImage"]["tmp_name"], $survey_image);
            }
        }
    } catch (Exception $e) {
        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― IMAGE UPLOAD ERROR]: Error al subir la imagen de la encuesta: " . $e->getMessage() . "\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
        echo "<script>errorNotification('ERROR al subir la imagen de la encuesta')</script>";
    }

    try {
        if ($surveryImageName == "") {
            $surveryImageName = null;
        }

        $query_survey = $pdo->prepare("INSERT INTO Survey (user_id, survey_title, start_date, end_date, public_title, public_results, imag )
                                VALUES (:user_id, :survey_title, :start_date, :end_date, :public_title, :public_results, :survey_image)");

        $query_survey->bindParam(':user_id', $user_id);
        $query_survey->bindParam(':survey_title', $survey_title);
        $query_survey->bindParam(':start_date', $start_date);
        $query_survey->bindParam(':end_date', $end_date);
        $query_survey->bindParam(':public_title', $public_title);
        $query_survey->bindParam(':public_results', $public_results);
        $query_survey->bindParam(':survey_image', $surveryImageName);

        $query_survey->execute();

        $survey_id = $pdo->lastInsertId();

        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― SURVEY INSERTED]: Se ha insertado la encuesta en la base de datos.\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);

        // Handle Survey Options
        $options = $_POST["options"];
        $images = $_FILES["images"];

    } catch (PDOException $e) {
        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― ERROR SURVEY SAVE]: Error al intentar guardar la encuesta $survey_id\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
        echo "<script> errorNotification('Error al intentar guardar la encuesta $survey_id.'); </script>";
    }

    try {
        for ($i = 1; $i <= count($options); $i++) {
            $option_text = $options[$i];
            $option_image = "";
            $optionImageName = "";

            if ($images["name"][$i]) {
                $upload_directory = "uploads/option/";
                if (!file_exists($upload_directory)) {
                    mkdir($upload_directory, 0777, true);

                    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― DIRECTORY CREATED]: Se ha creado el directorio /uploads/option\n";
                    file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                }
                if ($_FILES["imgageOpt"]["size"][$i] > $max_file_size) {
                    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― IMAGE UPLOAD ERROR]: Error al subir la imagen de la encuesta: El archivo es demasiado grande. El tamaño máximo permitido es de 50 KB\n";
                    file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                    echo "<script>errorNotification('El archivo es demasiado grande. El tamaño máximo permitido es de 50 KB.')</script>";
                } else {
                    $optionImageName = uniqid() . "." . pathinfo($images["name"][$i], PATHINFO_EXTENSION);
                    $option_image = $upload_directory . $optionImageName;
                    move_uploaded_file($images["tmp_name"][$i], $option_image);
                }
            }

            if ($optionImageName == "") {
                $optionImageName = null;
            }

            try {
                $query_options = $pdo->prepare("INSERT INTO SurveyOption (option_text, survey_id, imag) VALUES (:option_text, :survey_id, :option_image)");
                $query_options->bindParam(':option_text', $option_text);
                $query_options->bindParam(':survey_id', $survey_id);
                $query_options->bindParam(':option_image', $optionImageName);

                $query_options->execute();

                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― SURVEY OPTION INSERTED]: Se ha insertado la opcion $i de la encuesta $survey_id en la base de datos (" . ($i + 1) . " de " . (count($options) + 1) . ")\n";
                file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            } catch (PDOException $e) {
                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― ERROR SURVEY OPTION SAVE]: Error al intentar guardar la opcion $i de la encuesta $survey_id (" . ($i + 1) . " de " . (count($options) + 1) . ")\n";
                file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                echo "<script> errorNotification('Error al intentar guardar la opcion $i de la encuesta $survey_id (' . ($i + 1) . ' de ' . (count($options) + 1) . '); </script>";
            }

        }
    } catch (PDOException $e) {
        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― ERROR SURVEY OPTION SAVE]: Error al intentar guardar las opciones de la encuesta $survey_id\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
        echo "<script> errorNotification('Error al intentar guardar las opciones de la encuesta $survey_id.'); </script>";
    } finally {
        $pdo = null;

    }

    echo "
        <script>
            successfulNotification('Tu encuesta se ha creado correctamente.');
        </script>
    ";
}
?>