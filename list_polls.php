<?php
session_start();
if (isset($_POST['send_invitations'])) {
    $emails = explode(',', $_POST['emails']);
    $survey_id = $_POST['survey_id'];

    foreach ($emails as $email) {
        $email = trim($email);

        $token = bin2hex(random_bytes(50));

        $query = $pdo->prepare("INSERT INTO Invitation (mail_to, survey_id, invitation_token) VALUES (:mail_to, :survey_id, :invitation_token)");
        $query->execute([':mail_to' => $email, ':survey_id' => $survey_id, ':invitation_token' => $token]);

        // Aquí debes enviar el correo electrónico con PHPMailer
    }
}
?>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body id="list_polls">
    <?php include_once('common/header.php'); ?>
    <main>
        <h1>Tus encuestas</h1>
        <span>Haz click para ver los detalles de las encuestas</span>
        <section class="grid-polls">
            <?php
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
                                <button class="share-button" data-survey-id="' . $row["survey_id"] . '">Compartir encuesta</button>
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
                    } ?>
                    <div id="overlay" style="display: none;"></div>
                    <dialog id="modal-share">
                        <span class="close">&times;</span>
                        <h1>Invita a gente a tu encuesta:</h1>
                        <span>Separa los correos por comas</span>
                        <form method="POST">
                            <textarea name="invite-area" id="invite-area" cols="30" rows="10"></textarea>
                            <input type="submit" value="Invitar">
                        </form>
                    </dialog>
                    <?php
                }
            } catch (PDOException $e) {
                echo "<script>errorNotification('ERROR al conectarse con la base de datos -> " . $e->getMessage() . "')</script>";
            }
            ?>
        </section>
    </main>
    <script src="send_invitations.js"></script>
    <?php include_once("common/footer.php") ?>
</body>

</html>