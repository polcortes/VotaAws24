<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de encuestas | Vota!</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body id="list_polls">
    <?php include_once('common/header.php'); ?>
    <main>
        <h1>Tus encuestas</h1>
        <section class="grid-polls">
            <!--<article class="grid-poll-item">
                <div>
                    <h2>Pregunta del formulario.</h2>
                    <div>De: emieza</div>
                </div>

                <footer>
                    <div class="poll-is-published">No publicada</div>    
                    <div class="poll-is-online en-proceso">En proceso</div>
                </footer>
            </article>

            <article class="grid-poll-item">
                <div>
                    <h2>Pregunta del formulario.</h2>
                    <div>De: Xavi</div>
                </div>

                <footer>
                    <div class="poll-is-published publicada">Publicada</div>
                    <div class="poll-is-online programada">Programada</div>
                </footer>
            </article>

            <article class="grid-poll-item">
                <div>
                    <h2>Pregunta del formulario.</h2>
                    <div>De: lzabala</div>
                </div>

                <footer>
                    <div class="poll-is-published publicada">Publicada</div>
                    <div class="poll-is-online finalizada">Finalizada</div>
                </footer>
            </article>-->

            <?php
            //session_start();
            //if (isset($_SESSION["usuario"])) {
            if (true) {
                $dbname = "votadb";
                $user = "root";
                $password = "p@raMor3";
            
                try {
                    $dsn = "mysql:host=localhost;dbname=$dbname";
                    $pdo = new PDO($dsn, $user, $password);
                } catch (PDOException $e){
                    echo $e->getMessage("");
                }

                //$query = $pdo -> prepare("SELECT customer_name FROM Users WHERE user_id = ". $_SESSION["usuario"] . ";");
                $query = $pdo -> prepare("SELECT customer_name FROM Users WHERE user_id = 2;");
                $query -> execute();

                $username;

                foreach ($query as $row) {
                    $username = $row["customer_name"];
                }
            
                //$query = $pdo -> prepare("SELECT question_text, start_time, end_time FROM Surveys WHERE owner_id = ". $_SESSION['usuario'] .";");
                $query = $pdo -> prepare("SELECT question_text, start_time, end_time, isPublished FROM Surveys WHERE owner_id = 2;");
                $query -> execute();
            
                // Error:
                $e= $query->errorInfo();
                if ($e[0]!='00000') {
                    echo "\nPDO::errorInfo():\n";
                    die("Error accedint a dades: " . $e[2]);
                } 
            
                if (!empty($query)) {
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

                        /*(
                            ($current_date >= $formated_end_time && $current_date > $formated_start_time ) 
                                ? "finalizada" 
                                : (
                                    ($current_date < $formated_end_time && $current_date < $formated_start_time) 
                                        ? "programada" 
                                        : "en-proceso"
                                )) .'">'. ($current_date >= $formated_end_time ? "Finalizada" : ($current_date < $formated_end_time ? "Programada" : "En proceso")
                        );*/

                        echo '
                        <article class="grid-poll-item">
                            <div>
                                <h2>'. $row["question_text"] .'</h2>
                                <div>De: '. $username .'</div>
                            </div>

                            

                            <footer>
                                <div class="poll-is-published '. ($row["isPublished"] ? " publicada" : "") .'">'. ($row["isPublished"] ? " Publicada" : "No publicada") .'</div>
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
            }
            ?>
        </section>
    </main>
    <?php include_once("common/footer.php") ?>
</body>
</html>