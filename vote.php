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
                        if ($row['user_id']) {
                            $_SESSION["usuario"] = $row['user_id'];
                            header("Location: vote.php?survey_id=" . $surveyID);
                        } else {
                            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN ERROR]: El token no es válido 1\n";
                            file_put_contents($logFilePath, $logTxt, FILE_APPEND);

                            header("Location: login.php");
                            exit();
                        }
                    } else {
                        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN ERROR]: El token no es válido 2\n";
                        file_put_contents($logFilePath, $logTxt, FILE_APPEND);

                        header("Location: login.php");
                        exit();
                    }
                } else {
                    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN ERROR]: El token no es válido 3\n";
                    file_put_contents($logFilePath, $logTxt, FILE_APPEND);

                    header("Location: login.php");
                    exit();
                }
            } else {
                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN ERROR]: El token no es válido 4\n";
                file_put_contents($logFilePath, $logTxt, FILE_APPEND);

                header("Location: login.php");
                exit();
            }
        } else {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN ERROR]: El token no es válido 5\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);

            header("Location: login.php");
            exit();
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

            header("Location: index.php");
            exit();
        }
    }
    $query = $pdo->prepare("SELECT survey_id, survey_title, imag FROM Survey WHERE survey_id = :survey_id;");

    // $query = $pdo -> prepare(
    //   "SELECT s.survey_title, s.imag, so.option_text, so.imag 
    //   FROM survey AS s 
    //   INNER JOIN surveyoption AS so ON so.survey_id = s.survey_id 
    //   WHERE s.survey_id = :survey_id;"
    // );

    $query->bindParam(":survey_id", $_GET['survey_id']);
    $query->execute();

    $row = $query->fetch();

    if (!$query->rowCount() == 0) {
        $surveyID = $row["survey_id"];
        $surveyTitle = $row["survey_title"];
        $surveyImg = $row["survey_img"];
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
        <script src="vote.js"></script>
        <script src="componentes/notificationHandler.js"></script>
    </head>

    <body id="vote">
        <?php
        include_once("common/header.php");
        ?>
        <main>

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

                foreach ($rows as $row) {
                    ?>
                    <article>
                        <?php if (isset($row["imag"])): ?>
                            <img class="optionImag" src=<?php echo "'uploads/option/" . $row["imag"] . "'" ?>
                                alt="Imagen de la opción.">
                        <?php endif ?>
                        <label>
                            <input type="radio" name="answer" value="<?php echo $row['option_id']; ?>">
                            <?php echo $row["option_text"]; ?>
                        </label>
                    </article>
                    <?php
                }
                ?>
                <input type="submit" value="Enviar voto" id="submit-vote">
            </form>
        </main>
        <ul id="notification__list"></ul>
        <div class="footer">
            <?php include_once("common/footer.php") ?>
        </div>
        <script src="componentes/notificationHandler.js"></script>
    </body>

    </html>
    <?php
} catch (PDOException $e) {
    echo "<script>errorNotification('ERROR al conectarse con la base de datos -> " . $e->getMessage() . "')</script>";
}
?>