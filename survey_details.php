<?php
include 'data/dbAccess.php';
session_start();
/*if (isset($_POST["results-visibility"])) {
    $pdo = new PDO("mysql:host=localhost;dbname=votadb", 'root', 'AWS24VotaPRRojo_'); //AWS24VotaPRRojo_

    $query = $pdo->prepare("UPDATE Surveys SET public_results");
}*/

if (isset($_SESSION["usuario"]) && isset($_GET["id"])) {
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

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
    } else {
        // Añadir las notificaciones
        echo "<script> errorNotification('No tienes una encuesta con esa ID.'); </script>";
    }

    $query = $pdo->prepare("SELECT option_id, option_text FROM SurveyOption WHERE survey_id = :survey_id;");

    $queryDos = $pdo->prepare("SELECT option_id, count(*) AS countOfVotes FROM UserVote WHERE option_id = :question_id;");
    //    SELECT option_id, option_text, countVotes FROM Questions WHERE survey_id = 3 AND countVotes = (SELECT count(*) FROM Votes_User WHERE option_id = 5 or option_id = 6);

    /*
    SELECT Orders.OrderID, Customers.CustomerName, Orders.OrderDate
    FROM Orders
    INNER JOIN Customers ON Orders.CustomerID=Customers.CustomerID;

    SELECT option_id.Questions, option_text.Questions, count(option_id.Votes_User) From Questions INNER JOIN Votes_User ON option_id.Votes_User=option_id.Questions;
    */

    $query->bindParam(":survey_id", $_GET["id"], PDO::PARAM_INT);
    $query->execute();

    $rows = $query->fetchAll();

    $questions = [];
    foreach ($rows as $row) {
        // $questions[$row['option_id']] = $row['option_text'];
        $queryDos->bindParam(":question_id", $row['option_id'], PDO::PARAM_INT);
        $queryDos->execute();

        $rowDos = $queryDos->fetch();

        // $questions[(int) $row['option_id']] = [$row['option_text'], (int) $rowDos['countOfVotes']];
        array_push($questions, ['id' => $row['option_id'], 'optionText' => $row['option_text'], 'countOfVotes' => $rowDos['countOfVotes']]);
    }

    echo "<input type='hidden' id='questions-to-js' name='questions' value='" . json_encode($questions, JSON_UNESCAPED_UNICODE) . "'>";

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
    <title>Detalles de "
        <?php echo $question_text; ?>"
    </title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <meta name="description" content="Crea una encuesta para obtener las respuestas de todo el mundo!">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
    <script src="componentes/notificationHandler.js"></script>
    <script src="./survey_details.js"></script>
</head>

<body id="survey_details">
    <a href="/list_polls.php"><svg style="transform: rotate(90deg); margin-right: 5px;" width="24" height="24"
            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
            stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path
                d="M16.375 6.22l-4.375 3.498l-4.375 -3.5a1 1 0 0 0 -1.625 .782v6a1 1 0 0 0 .375 .78l5 4a1 1 0 0 0 1.25 0l5 -4a1 1 0 0 0 .375 -.78v-6a1 1 0 0 0 -1.625 -.78z"
                stroke-width="0" fill="currentColor" />
        </svg> Vuelve a tus encuestas</a>
    <main>
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

            <!--<div>-->
            <!--
                    => [color]: respuesta
                    => [color]: respuesta
                    => [color]: respuesta
                -->
            <!--</div>-->
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

    <ul id="notification__list">

    </ul>
</body>

</html>