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
    <script src="invitar.js"></script>
</head>
<body id="list_polls">
    <?php include_once('common/header.php'); ?>
    <main>
        <h1>Tus encuestas</h1>
        <span>Haz click para ver los detalles de las encuestas</span>
        <section class="grid-polls">
            <?php
            session_start();

            if (isset($_SESSION["usuario"])) {
                $dbname = "votadb";
                $user = "root";
                $password = "p@raMor3";
            
                try {
                    $dsn = "mysql:host=localhost;dbname=$dbname";
                    $pdo = new PDO($dsn, $user, $password);
                } catch (PDOException $e){
                    echo $e->getMessage("");
                }
            
                $query = $pdo -> prepare("SELECT survey_id, survey_title, start_date, end_date, public_title, public_results FROM Survey WHERE user_id = ". $_SESSION['usuario'] .";");
                // $query = $pdo -> prepare("SELECT question_text, start_time, end_time, public_title,  FROM Surveys WHERE owner_id = 2;");
                $query -> execute();
            
                // Error:
                $e= $query->errorInfo();
                if ($e[0]!='00000') {
                    echo "\nPDO::errorInfo():\n";
                    die("Error accedint a dades: " . $e[2]);
                }
            
                if (!$query->rowCount() == 0) {
                    foreach ($query as $row) {
                        $end_time = new DateTime($row["end_date"]);
                        $formated_end_time = $end_time -> format('d-m-Y H:i:s');
                        
                        $start_time = new DateTime($row["start_date"]);
                        $formated_start_time = $start_time -> format('d-m-Y H:i:s');

                        $current_date = new DateTime();

                        $isOnline;
                        $isOnlineClass;

                        if ($current_date >= $end_time && $current_date > $start_time) {
                            $isOnline = "Finalizada";
                            $isOnlineClass = "finalizada";
                        } else if ($current_date < $end_time && $current_date < $start_time) {
                            $isOnline = "Programada";
                            $isOnlineClass = "programada";
                        } else {
                            $isOnline = "En proceso";
                            $isOnlineClass = "en-proceso";
                        }

                        $isPublic = [
                            "public"  => '<svg width="10" height="10" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M11.102 17.957c-3.204 -.307 -5.904 -2.294 -8.102 -5.957c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6a19.5 19.5 0 0 1 -.663 1.032" /><path d="M15 19l2 2l4 -4" /></svg>',
                            "hidden"  => '<svg width="10" height="10" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M12 18c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /><path d="M19 19m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M17 21l4 -4" /></svg>',
                            "private" => '<svg width="10" height="10" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 9c-2.4 2.667 -5.4 4 -9 4c-3.6 0 -6.6 -1.333 -9 -4" /><path d="M3 15l2.5 -3.8" /><path d="M21 14.976l-2.492 -3.776" /><path d="M9 17l.5 -4" /><path d="M15 17l-.5 -4" /></svg>'
                        ];

                        echo '
                        <article class="grid-poll-item">
                            <a href="/survey_details.php?id='. $row["survey_id"] .'">
                                <div>
                                    <h2>'. $row["survey_title"] .'</h2>
                                    <div>De: '. $_SESSION["nombre"] .'</div>
                                </div>

                                <footer>
                                    <div class="poll-is-published '. ($row["public_title"] ? " publicada" : "") .'"> Encuesta '. ($row["public_title"] ? "publicada" : "no publicada") .'</div>
                                    <div class="poll-is-online '. $isOnlineClass .'">'. $isOnline .'</div>
                                    <div class="poll-results-public">'. ($row["public_results"] === "public" ? $isPublic["public"]."Resultados públicos" : ($row["public_results"] === $isPublic["hidden"]."Resultados ocultos" ? "hidden-results" : $isPublic["private"]."Resultados privados")) .'"</div>

                                    <button type="button" id="invitar">Invitar</button>
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
            } else {

            }
            ?>

            <dialog id="modal-invitar">
                <h1>Invita a gente a tu encuesta:</h1>
                <span>Separa los correos por comas</span>
                <form method="POST">
                    <textarea name="invite-area" id="invite-area" cols="30" rows="10"></textarea>
                    <input type="submit" value="Invitar">
                </form>
            </dialog>
        </section>
    </main>
    <?php include_once("common/footer.php") ?>
</body>
</html>