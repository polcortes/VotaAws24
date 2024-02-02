<?php
try {
    $logFilePath = "logs/log" . date("d-m-Y") . ".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }
    $filePathParts = explode("/", __FILE__);

    require 'data/dbAccess.php';
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['blocked'])){
        $query = $pdo->prepare("UPDATE Survey SET survey_blocked = 1 WHERE survey_id = :survey_id");

        $query->bindParam(':survey_id', $_GET["id"], PDO::PARAM_INT);
        $query->execute();
    }else{
        $query = $pdo->prepare("UPDATE Survey SET survey_blocked = 0 WHERE survey_id = :survey_id");

        $query->bindParam(':survey_id', $_GET["id"], PDO::PARAM_INT);
        $query->execute();
    }
}
    if (isset($_SESSION["usuario"]) && isset($_GET["id"])) {

        $query = $pdo->prepare("SELECT * FROM Survey WHERE user_id = :user_id AND survey_id = :survey_id");

        $query->bindParam(':user_id', $_SESSION["usuario"], PDO::PARAM_INT);
        $query->bindParam(':survey_id', $_GET["id"], PDO::PARAM_INT);
        $query->execute();

        $row = $query->fetch();

        $question_text;
        $start_time;
        $end_time;
        $is_published;
        // Cambiar parámetro dentro de $row
        if ($row) {
            $question_text = $row["survey_title"];
            $start_time = $row["start_date"];
            $end_time = $row["end_date"];
            $is_published = $row["public_title"];
            $survey_status = $row['survey_blocked'];
        } else {
            // Añadir las notificaciones
            echo "<script> errorNotification('No tienes una encuesta con esa ID.'); </script>";
        }


    } else {
        header("HTTP/1.1 403 Forbidden");
        exit();
    }
    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detalles de encuesta | Vota!</title>
        <meta name="description" content="Crea una encuesta para obtener las respuestas de todo el mundo!">
        <link rel="stylesheet" type="text/css" href="styles.css">
        <script src="https://code.jquery.com/jquery-3.7.1.js"
            integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
        <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
        <script src="/componentes/notificationHandler.js"></script>
    </head>

    <body id="survey_details">
        <main>
            <section>
                <?php
                echo $question_text;
                ?>
                <form action="" method="post">
                    <?php
                    echo $survey_status;
                    if($survey_status == 0){
                        echo '<div class="container">';
                        echo '<label class="switch" for="checkbox">';
                        echo '<input type="checkbox" id="checkbox" name="blocked"/>';
                        echo '<div class="slider"></div>';
                        echo '</label>';
                        echo '</div>';
                    }else{
                        echo '<div class="container">';
                        echo '<label class="switch" for="checkbox">';
                        echo '<input type="checkbox" id="checkbox" name="blocked" checked/>';
                        echo '<div class="slider"></div>';
                        echo '</label>';
                        echo '</div>';
                    }
                    ?>
                    <input type="submit" value="Cambio">
                </form>
            </section>
            <aside>

            </aside>
        </main>
    </body>

    </html>
    <?php
} catch (PDOException $e) {
    echo $e->getMessage();
}