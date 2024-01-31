<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de encuestas | Vota!</title>
    <meta name="description" content="Accede a todas las encuestas que hayas creado.">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
</head>

<body id="list_polls">
    <?php include_once('common/header.php'); ?>
    <main>
        <h1>Tus encuestas</h1>
        <span>Haz click para ver los detalles de las encuestas</span>
        <section class="grid-polls">
            <?php
            session_start();

            try {
                if (isset($_SESSION["usuario"])) {

                    require 'data/dbAccess.php';

                    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

                    $query = $pdo->prepare("SELECT * FROM Survey WHERE user_id = :user_id");
                    $query->execute([':user_id' => $_SESSION["usuario"]]);

                    if ($query->rowCount() > 0) {
                        foreach ($query as $row) {
                            $end_date = new DateTime($row["end_date"]);
                            $formatted_end_date = $end_date->format('d-m-Y H:i:s');

                            $start_date = new DateTime($row["start_date"]);
                            $formatted_start_date = $start_date->format('d-m-Y H:i:s');

                            $current_date = new DateTime();

                            $isOnline;
                            $isOnlineClass;

                            if ($current_date >= $end_date && $current_date > $start_date) {
                                $isOnline = "Finalizada";
                                $isOnlineClass = "finalizada";
                            } else if ($current_date < $end_date && $current_date < $start_date) {
                                $isOnline = "Programada";
                                $isOnlineClass = "programada";
                            } else {
                                $isOnline = "En proceso";
                                $isOnlineClass = "en-proceso";
                            }

                            echo '
                        <article class="grid-poll-item">
                            <a href="/survey_details.php?id=' . $row["survey_id"] . '">
                                <div>
                                    <h2>' . $row["question_text"] . '</h2>
                                    <div>De: ' . $_SESSION["nombre"] . '</div>
                                </div>

                                

                                <footer>
                                    <div class="poll-is-published ' . ($row["is_published"] ? " publicada" : "") . '"> Encuesta ' . ($row["is_published"] ? "publicada" : "no publicada") . '</div>
                                    <div class="poll-is-online ' . $isOnlineClass . '">' . $isOnline . '</div>
                                </footer>
                            </a>
                        </article>
                        ';
                        }
                    } else {
                        echo "
                    <article>
                        <div>
                            <h2>No tienes encuestas creadas.</h2>
                        </div>
                    </article>
                    ";
                    }
                }
            } catch (PDOException $e) {
                echo "<script>errorNotification('ERROR al conectarse con la base de datos -> " . $e->getMessage() . "')</script>";
            }
            ?>
        </section>
    </main>
    <?php include_once("common/footer.php") ?>
</body>

</html>