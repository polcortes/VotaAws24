<?php
try {
    $logFilePath = "logs/log" . date("d-m-Y") . ".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }
    $filePathParts = explode("/", __FILE__);

    require 'data/dbAccess.php';
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
} catch (Exception $e) {
    echo "<script>errorNotification('Ha habido un error al crear el archivo de logs: $e')</script>";
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
        <?php
        include_once("common/header.php");
        if (!isset($_SESSION["usuario"])) {
            header("HTTP/1.1 403 Forbidden");
            exit();
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['blocked'])) {
                $query = $pdo->prepare("UPDATE Survey SET survey_blocked = 1 WHERE survey_id = :survey_id");

                $query->bindParam(':survey_id', $_GET["id"], PDO::PARAM_INT);
                $query->execute();
            } else {
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
        <section>
            <h1>
                <?php echo $question_text; ?>
            </h1>
            <span>Gráfico de los resultados:</span>
            <div>
                <div class="survey-details-chart active-chart" id="pie-chart-cont"></div>
                <div class="survey-details-chart" id="column-chart-cont"></div>
                <aside>
                    <!--<label><input type="radio" name="chart-opt" class="chart-opt" value="pie-chart"></label>
                    <label><input type="radio" name="chart-opt" class="chart-opt" value="column-chart"></label>-->
                    <button class="chart-opt active-chart-butt" id="pie-chart">
                        <svg width="16" height="16" viewBox="0 0 22 22" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M10 3.2a9 9 0 1 0 10.8 10.8a1 1 0 0 0 -1 -1h-6.8a2 2 0 0 1 -2 -2v-7a.9 .9 0 0 0 -1 -.8" />
                            <path d="M15 3.5a9 9 0 0 1 5.5 5.5h-4.5a1 1 0 0 1 -1 -1v-4.5" />
                        </svg>
                        Pastel
                    </button>
                    <button class="chart-opt" id="column-chart">
                        <svg width="16" height="16" viewBox="0 0 22 22" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                            <path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                            <path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                            <path d="M4 20l14 0" />
                        </svg>
                        Columnas
                    </button>
                </aside>
            </div>
        </section>
        <aside>
            <div>
                <form method="POST">
                    <div>
                        <div>
                            <label for="results-visibility">Visibilidad de los resultados:</label>
                            <select name="results-visibility" id="results-visibility">
                                <option value="oculto">Oculto</option>
                                <option value="privado">Privado</option>
                                <option value="publico">Público</option>
                            </select>
                        </div>
                        <div>
                            <label for="title-visibility">Visibilidad del título:</label>
                            <select name="title-visibility" id="title-visibility">
                                <option value="oculto">Oculto</option>
                                <option value="privado">Privado</option>
                                <option value="publico">Público</option>
                            </select>
                        </div>
                    </div>

                    <input type="submit" value="Cambiar visibilidad">
                </form>
            </div>


        </aside>
    </main>

    <ul id="notification__list"></ul>
</body>

</html>