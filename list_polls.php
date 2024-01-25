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
        <section class="grid-polls">
            <?php
            session_start();

            if (isset($_SESSION["usuario"])) {
                $dbname = "votadb";
                $user = "root";
                $password = "AWS24VotaPRRojo_";
            
                try {
                    $dsn = "mysql:host=localhost;dbname=$dbname";
                    $pdo = new PDO($dsn, $user, $password);
                } catch (PDOException $e){
                    echo $e->getMessage("");
                }
            
                $query = $pdo -> prepare("SELECT question_text, start_time, end_time, is_published FROM Surveys WHERE owner_id = ". $_SESSION['usuario'] .";");
                // $query = $pdo -> prepare("SELECT question_text, start_time, end_time, is_published FROM Surveys WHERE owner_id = 2;");
                $query -> execute();
            
                // Error:
                $e= $query->errorInfo();
                if ($e[0]!='00000') {
                    echo "\nPDO::errorInfo():\n";
                    die("Error accedint a dades: " . $e[2]);
                } 
            
                if (!$query->rowCount() == 0) {
                    foreach ($query as $row) {
                        $end_time = new DateTime($row["end_time"]);
                        $formated_end_time = $end_time -> format('d-m-Y H:i:s');
                        
                        $start_time = new DateTime($row["start_time"]);
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

                        echo '
                        <article class="grid-poll-item">
                            <div>
                                <h2>'. $row["question_text"] .'</h2>
                                <div>De: '. $_SESSION["nombre"] .'</div>
                            </div>

                            

                            <footer>
                                <div class="poll-is-published '. ($row["is_published"] ? " publicada" : "") .'"> Encuesta '. ($row["is_published"] ? "publicada" : "no publicada") .'</div>
                                <div class="poll-is-online '. $isOnlineClass .'">'. $isOnline .'</div>
                            </footer>
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
        </section>
    </main>
    <?php include_once("common/footer.php") ?>
</body>
</html>