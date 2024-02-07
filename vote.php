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

    if (!isset($_SESSION["usuario"])) {
        if (isset($_GET['token'])) {
            $query = $pdo->prepare("SELECT * FROM Invitation WHERE invitation_token = :token");
            $query->execute([':token' => $_GET['token']]);
            $row = $query->fetch();

            if (!$query->rowCount() == 0) {
                if ($row['mail_to'] && $row['survey_id']) {
                    $userMail = $row['mail_to'];
                    $surveyID = $row['survey_id'];

                    $query = $pdo->prepare("SELECT * FROM User WHERE user_mail = :mail");
                    $query->execute([':mail' => $userMail]);
                    $row = $query->fetch();

                    if (!$query->rowCount() == 0) {
                        /*if ($row['user_id']) {
                            //$_SESSION["usuario"] = $row['user_id'];
                            //header("Location: vote.php?survey_id=" . $surveyID);
                        } else {
                            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN ERROR]: El token no es válido 1\n";
                            file_put_contents($logFilePath, $logTxt, FILE_APPEND);

                            //exit();
                        }*/
                    } else {
                        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN ERROR]: El token no es válido 2\n";
                        file_put_contents($logFilePath, $logTxt, FILE_APPEND);

                        //header("Location: login.php");
                       // exit();
                    }
                } else {
                    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN ERROR]: El token no es válido 3\n";
                    file_put_contents($logFilePath, $logTxt, FILE_APPEND);

                    //header("Location: login.php");
                    //exit();
                }
            } else {
                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN ERROR]: El token no es válido 4\n";
                file_put_contents($logFilePath, $logTxt, FILE_APPEND);

                //header("Location: login.php");
                //exit();
            }
        } else {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN ERROR]: El token no es válido 5\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);

            //header("Location: login.php");
            //exit();
        }
    } else {
        if (isset($_POST['submit-vote']) && isset($_POST['answer'])) {
            $optionID = $_POST['answer'];

            $query = $pdo->prepare("INSERT INTO UserVote (user_id, option_id) VALUES (:user_id, :option_id)");
            $query->execute([':user_id' => $_SESSION["usuario"], ':option_id' => $optionID]);

            if (isset($_GET['token'])) {
                $token = $_GET['token'];
                $query = $pdo->prepare("UPDATE Invitation SET is_survey_done = 1 WHERE invitation_token = :token");
                $query->execute([':token' => $token]);
            }
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― VOTE OK]: Se ha realizado una votación\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);

            // header("Location: index.php");
            //exit();
        }
    }
    // $query = $pdo -> prepare(
    //   "SELECT s.survey_title, s.imag, so.option_text, so.imag 
    //   FROM survey AS s 
    //   INNER JOIN surveyoption AS so ON so.survey_id = s.survey_id 
    //   WHERE s.survey_id = :survey_id;"
    // );
    $query = $pdo->prepare("SELECT `i`.`is_survey_done`, `i`.`mail_to`, `i`.`invitation_token`, `s`.`survey_id`, `s`.`survey_title`, `s`.`imag` FROM Survey `s`, Invitation `i` WHERE `i`.`invitation_token` = :invitation_token");
    $query2 = $pdo->prepare("SELECT user_id from User where user_mail = :user_mail");

    $query->bindParam(":invitation_token", $_GET['token']);
    
    $query->execute();
    
    $row = $query->fetch();
    
    $canVote = false;

    if (!$query->rowCount() == 0) {
        $query2->bindParam(":user_mail", $row["mail_to"]);
        $query2->execute();

        $row2 = $query2->fetch();
        if (!$query2->rowCount() == 0) {
            $surveyID = $row["survey_id"];
            $surveyTitle = $row["survey_title"];
            $surveyImg = $row["imag"];
            $canVote = true;
            $isBlocked = false;
            $isSurveyDone = $row["is_survey_done"];
        }

    }
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Votar en una encuesta</title>
        <link rel="stylesheet" href="styles.css">
        <script src="https://code.jquery.com/jquery-3.7.1.js"
            integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script src="componentes/vote.js"></script>
        <script src="componentes/notificationHandler.js"></script>
    </head>

    <body id="vote">
        <?php
        include_once("common/header.php");
        ?>
        <ul id="notification__list"></ul>
        <main>
            
            <?php if (isset($_POST["answer"]) && isset($_POST["pass-check"])): ?>
                <?php
                $wasSend = false;
                $query = $pdo->prepare("SELECT mail_to FROM Invitation WHERE invitation_token = :token;");
                $query->bindParam(":token", $_GET["token"]);
                $query->execute();

                $userMail = $query->fetch()["mail_to"];

                $query = $pdo->prepare("SELECT `i`.`invitation_id`, `i`.`mail_to`, `u`.`customer_name`, `u`.`user_mail`, `u`.`user_pass` FROM User u, Invitation i WHERE `u`.`user_mail` = :mail;");
                $query->execute([":mail" => $userMail]);

                $row = $query->fetch();

                if (isset($row["customer_name"])) {
                    $userData = $row;
                    // Qué hacer si es usuario registrado:
                    if (hash("sha512", $_POST["pass-check"]) === $userData["user_pass"]) {
                        $decryptStringQuery = $pdo->prepare("SELECT encryptString FROM User WHERE user_id = :user_id;");
                        $decryptStringQuery->execute([":user_id" => $_SESSION["usuario"]]);
                        $encryptedString = $decryptStringQuery->fetch();
                        
                        $decryptString = openssl_decrypt($encryptedString["encryptString"], "AES-256-CTR", $_POST["pass-check"]);

                        // insert into uservote (invitation_id_enc, option_id) VALUES (aes_encrypt(concat(convert(2, char), @decryptString), @pass), 2 );

                        $query = $pdo->prepare("INSERT INTO UserVote (invitation_id_enc, option_id) VALUES (aes_encrypt(concat(convert(:invitation_id_uno, char), :decryptString), :pass), :invitation_id_dos);");
                        $query->execute([
                            ":invitation_id_uno" => $row["invitation_id"], 
                            ":decryptString" => $decryptString, 
                            ":pass" => $_POST["pass-check"], 
                            ":invitation_id_dos" => $userData["invitation_id"]
                        ]);


                        $query = $pdo->prepare("UPDATE Invitation SET is_survey_done = :is_done WHERE invitation_token = :token");
                        $query->execute([
                            ":is_done" => true,
                            ":token"   => $_GET['token']
                        ]);

                        $wasSend = true;
                    }

                }
                ?>
                <?php if ($wasSend): ?>
                    <section>
                        <h1>¡Tu respuesta ha sido enviada!</h1>
                        <span style="display: flex; justify-content: space-between;">
                            <a href="index.php">Ir a inicio</a>
                            <a href="dashboard.php">Ir al dashboard</a>
                        </span>
                        <?php echo "<script>successfulNotification('¡Tu respuesta ha sido enviada exitosamente!')</script>"; ?>
                    </section>

                <?php else: ?>
                    <section style="position: relative;">
                        <h1>Tu contraseña no parece ser correcta...</h1>
                        <a style="position: absolute;left: 50%;transform: translateX(-50%);" href=<?php echo "'vote.php?".$_SERVER["QUERY_STRING"]."'"; ?> >¡Vuelve a intentarlo!</a>
                    </section>

                <?php endif; ?>
            
            <?php elseif (isset($_POST["answer"]) && !isset($_POST["pass-check"])): ?>
                <?php
                $wasSend = false;
                $query = $pdo->prepare("SELECT mail_to FROM Invitation WHERE invitation_token = :token;");
                $query->bindParam(":token", $_GET["token"]);
                $query->execute();

                $userMail = $query->fetch()["mail_to"];

                $query = $pdo->prepare("SELECT `i`.`invitation_id`, `i`.`mail_to`, `u`.`customer_name`, `u`.`user_mail`, `u`.`user_pass` FROM User u, Invitation i WHERE `u`.`user_mail` = :mail;");
                $query->execute([":mail" => $userMail]);

                $row = $query->fetch();

                // Qué hacer si es anónimo:
                $decryptStringQuery = $pdo->prepare("SELECT encryptString FROM User WHERE user_mail = :user_mail;");
                $decryptStringQuery->execute([":user_mail" => $userMail]);
                $encryptedString = $decryptStringQuery->fetch();
                
                $decryptString = openssl_decrypt($encryptedString["encryptString"], "AES-256-CTR", "aaaaAaa1");

                $query = $pdo->prepare("INSERT INTO UserVote (invitation_id_enc, option_id) VALUES (aes_encrypt(concat(convert(:invitation_id_uno, char), :decryptString), :pass), :invitation_id_dos);");
                $query->execute([
                    ":invitation_id_uno" => $row["invitation_id"], 
                    ":decryptString" => $decryptString, 
                    ":pass" => "aaaaAaa1", 
                    ":invitation_id_dos" => $row["invitation_id"]
                ]);


                $query = $pdo->prepare("UPDATE Invitation SET is_survey_done = :is_done WHERE invitation_token = :token");
                $query->execute([
                    ":is_done" => true,
                    ":token"   => $_GET['token']
                ]);

                $wasSend = true;
                ?>
                <?php if ($wasSend): ?>
                    <section>
                        <h1>¡Tu respuesta ha sido enviada!</h1>
                        <span style="display: flex; justify-content: space-between;">
                            <a href="index.php">Ir a inicio</a>
                            <a href="dashboard.php">Ir al dashboard</a>
                        </span>
                        <?php echo "<script>successfulNotification('¡Tu respuesta ha sido enviada exitosamente!')</script>"; ?>
                    </section>

                <?php else: ?>
                    <section style="position: relative;">
                        <h1>Parece que algo no ha salido bien...</h1>
                        <a style="position: absolute;left: 50%;transform: translateX(-50%);" href=<?php echo "'vote.php?".$_SERVER["QUERY_STRING"]."'"; ?> >¡Vuelve a intentarlo!</a>
                    </section>

                <?php endif; ?>
            
            <?php elseif ($isSurveyDone): ?>
                <section style="position: relative;">
                    <h1>¡Ya has completado esta encuesta!</h1>
                    <?php if (isset($_SESSION["usuario"])): ?>
                        <p>Puedes consultar las encuestas en las que has participado <a href="#">haciendo click aquí</a></p>
                    <?php else: ?>
                        <p>Para consultar las encuestas en las que has participado, puedes registrarte <a href="register.php">haciendo click aquí</a></p>
                    <?php endif; ?>
                </section>

            <?php elseif ($isBlocked): ?>
                <section>
                    <h1>⚠️¡Esta encuesta está bloqueada!⚠️</h1>
                    <p>Esta encuesta ha sido bloqueada por su creador.</p>
                    <span style="display: flex; justify-content: space-between;">
                        <a href="index.php">Ir a inicio</a>
                        <a href="dashboard.php">Ir al dashboard</a>
                    </span>
                </section>
            
            <?php elseif (!$canVote): ?>
                <section id="cant-vote">
                    <h1>No has sido invitado para participar en esta encuesta.</h1>
                    <p>Parece que no has sido invitado para participar en esta encuesta... ¡Pero puedes crear la tuya!</p>
                    <a href="create_poll.php">Haz click en este enlace para crear tu encuesta</a>
                </section>
            <?php else: ?>
                <div>
                    <h1>
                        <?php echo $surveyTitle; ?>
                    </h1>
                    <?php if (isset($surveyImg)): ?>
                        <img class="surveyImag" src=<?php echo "'uploads/survey/" . $surveyImg . "'" ?>
                            alt="Imagen de la encuesta.">
                    <?php endif ?>
                </div>

                <form id="vote-form" method="POST">
                    <?php
                    $querydos = $pdo->prepare("SELECT * FROM SurveyOption WHERE survey_id = :survey_id;");
                    $querydos->bindParam(":survey_id", $surveyID);
                    $querydos->execute();

                    $rows = $querydos->fetchAll();
                    ?> <section> <?php
                    foreach ($rows as $row) {
                        ?>
                        <article>
                            
                            <label>
                                <?php if (isset($row["imag"])): ?>
                                    <img class="optionImag" src=<?php echo "'uploads/option/" . $row["imag"] . "'" ?>
                                        alt="Imagen de la opción.">
                                <?php endif ?>
                                <input type="radio" name="answer" value="<?php echo $row['option_id']; ?>">
                                <?php echo $row["option_text"]; ?>
                            </label>
                        </article>
                        <?php
                    }
                    ?> </section>

                    <?php if (isset($_SESSION["usuario"])): ?>
                        <label for="pass-check">Confirma tu contraseña:</label>
                        <input type="password" name="pass-check" id="pass-check" placeholder="Contraseña..." required>
                    <?php endif ?>

                    <input type="submit" value="Enviar voto" id="submit-vote">
                </form>
            <?php endif ?>
        </main>
        <div class="footer">
            <?php include_once("common/footer.php") ?>
        </div>
        <script src="componentes/notificationHandler.js"></script>
    </body>

    </html>
    <?php
} catch (PDOException $e) {
    //echo "<script>errorNotification('ERROR al conectarse con la base de datos -> " . $e->getMessage() . "')</script>";
    $logFilePath = "logs/log" . date("d-m-Y") . ".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }
    $filePathParts = explode("/", __FILE__);

    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― DB ERROR]: ERROR al conectarse con la base de datos -> " . $e->getMessage() . "\n";
    file_put_contents($logFilePath, $logTxt, FILE_APPEND);
}
?>