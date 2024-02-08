<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    require 'PHPMailer-master/src/Exception.php';
    require 'PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/src/SMTP.php';
    require 'data/dbAccess.php';
    require 'data/mailCredentials.php';

    $logFilePath = "logs/log" . date("d-m-Y") . ".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }
    $filePathParts = explode("/", __FILE__);

    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verificación de email</title>
        <meta name="description" content="Verifica tu dirección de email para acceder a 'Vota!'">
        <link rel="stylesheet" type="text/css" href="styles.css">
        <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
        <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
        <script src="https://code.jquery.com/jquery-3.7.1.js"
            integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script src="/componentes/notificationHandler.js"></script>
    </head>

    <body id="mailVerification">
        <?php
        include_once("common/header.php");
        if (!isset($_SESSION["usuario"])) {
            header("HTTP/1.1 403 Forbidden");
            exit();
        }

        if (isset($_GET['token'])) {
            $query = $pdo->prepare("SELECT * FROM User WHERE token = :token");
            $query->execute([':token' => $_GET['token']]);
            $row = $query->fetch();

            if ($row && $row['user_id'] && $row['user_id'] === $_SESSION["usuario"]) {
                $query = $pdo->prepare("UPDATE User SET is_mail_valid = true WHERE user_id = :id");
                $query->execute([':id' => $_SESSION["usuario"]]);
                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― MAIL VAERIFICATED]: Se ha verificado el email.\n";
                file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                if ($row['conditions_accepted']) {
                    header("Location: dashboard.php?succ=1");
                    exit();
                } else {
                    header("Location: terms_conditions.php");
                    echo "successfulNotification('¡Email verificado!')";
                    exit();
                }
            }
        }

        $query = $pdo->prepare("SELECT * FROM User WHERE user_id = :id");
        $query->execute([':id' => $_SESSION["usuario"]]);
        $row = $query->fetch();

        if ($row) {
            if ($row['is_mail_valid']) {
                if ($row['conditions_accepted']) {
                    header("Location: dashboard.php");
                    exit();
                } else {
                    header("Location: terms_conditions.php");
                    exit();
                }
            } else {
                if ($row['token'] != null) {
                    $token = $row['token'];
                } else {
                    $token = bin2hex(random_bytes(50));
                    $query = $pdo->prepare("UPDATE User SET token = :token WHERE user_id = :user_id");
                    $query->execute([':token' => $token, ':user_id' => $_SESSION["usuario"]]);
                }

                $link = "https://aws24.ieti.site/mail_verification.php?token=$token";
                try {
                    $mail = new PHPMailer();
                    $mail->IsSMTP();
                    $mail->Mailer = "smtp";
                    $mail->IsSMTP();
                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';


                    // $mail->SMTPDebug = 1;
                    $mail->SMTPAuth = TRUE;
                    $mail->SMTPSecure = "tls";
                    $mail->Port = 587;
                    $mail->Host = "smtp.gmail.com";
                    $mail->Username = $emailUsername;
                    $mail->Password = $emailPassword;

                    $mail->IsHTML(true);
                    $mail->AddAddress($row['user_mail']);
                    $mail->SetFrom($emailUsername);
                    $mail->Subject = 'Verificacion de correo electronico';
                    $content = "Haz clic en este enlace para verificar tu correo electrónico: $link";

                    $mail->MsgHTML($content);
                    $mail->CharSet = 'UTF-8';
                    $mail->Send();
                    if (!$mail->Send()) {
                        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― MAIL ERROR]: Ha habido un error al enviarle un email al correo " . $row['user_mail'] . ".\n";
                        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                        echo "<script>errorNotification('Ha surgido un error al enviar el email de verificacion al email " . $row['user_mail'] . "')</script>";
                    } else {
                        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― MAIL SEND]: Se ha enviado un email de verificacion al correo " . $row['user_mail'] . ".\n";
                        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                        echo "<script>successfulNotification('Se ha enviado un email de verificacion al correo " . $row['user_mail'] . "')</script>";
                    }
                } catch (Exception $e) {
                    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― MAIL ERROR]: Ha habido un error al enviarle un email al correo " . $row['user_mail'] . ".\n";
                    file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                    echo "<script>errorNotification('Ha surgido un error al enviar el email de verificacion al email " . $row['user_mail'] . "')</script>";
                }
                ?>
                <main>
                    <h1>Verifica tu dirección de email</h1>
                    <p>Te hemos enviado un email a <b>
                            <?php echo $row['user_mail'] ?>
                        </b> con un enlace para verificar tu dirección de email.</p>
                    <p>Si no lo encuentras, revisa la carpeta de spam.</p>
                    <a href="https://mail.google.com/mail" target="_blank">Ir al correo</a>
                </main>

                <ul id="notification__list"></ul>
                <div class="footer">
                    <?php include_once("common/footer.php") ?>
                </div>
                <script> successfulNotification('¡Registro completado!'); </script>
            </body>

            </html>
            <?php
            }
        } else {
            echo "<script>errorNotification('ERROR al consultar la base de datos.')</script>";
        }
} catch (PDOException $e) {
    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― DB ERROR]: Ha habido un error al conectarse a la base de datos: " . $e->getMessage() . "\n";
    file_put_contents($logFilePath, $logTxt, FILE_APPEND);
    echo "<script>errorNotification('Ha habido un error al conectarse a la base de datos.')</script>";
}