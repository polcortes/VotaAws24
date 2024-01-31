<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="componentes/notificationHandler.js"></script>
</head>

<body>
    <ul id="notification__list">
        <!-- todas las notificaciones -->
    </ul>
</body>

</html>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $logFilePath = "logs/log" . date("d-m-Y") . ".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }
    $log = fopen($logFilePath, "a");
    $filePathParts = explode("/", __FILE__);
    try {
        require 'PHPMailer-master/src/Exception.php';
        require 'PHPMailer-master/src/PHPMailer.php';
        require 'PHPMailer-master/src/SMTP.php';
        require 'data/dbAccess.php';

        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

        $query = $pdo->prepare("SELECT * FROM User WHERE user_id = :id");
        $query->execute([':id' => $_SESSION["usuario"]]);

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->IsSMTP();

        $mail->SMTPAuth = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->Host = "smtp.gmail.com";
        $mail->Username = $emailUsername;
        $mail->Password = $emailPassword;

        foreach ($mailsToSend as $mailToSend) {
            $link = "https://aws24.ieti.site/vote.php?token=" . $mailToSend['invitation_token'] . "&survey_id=" . $mailToSend['survey_id'];
            $mail->IsHTML(true);
            $mail->AddAddress($mailToSend['mail_to']);
            $mail->SetFrom($emailUsername);
            $mail->Subject = '¡PARTICIPA EN MI ENCUESTA!';
            $content = "Haz clic en este enlace para participar en mi encuesta: $link";

            $mail->MsgHTML($content);
            if (!$mail->Send()) {
                echo "<script>errorNotification('No se ha enviado el mail a " . $mailToSend['mail_to'] . "')</script>";

                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― ERROR at sending invitation email]: Envio de invitación a " . $mailToSend['mail_to'] . " erroneo\n";
                fwrite($log, $logTxt);
                fclose($log);
            } else {
                echo "<script>errorNotification('Se ha enviado el mail a " . $mailToSend['mail_to'] . "')</script>";

                $query = $pdo->prepare("UPDATE Invitation SET is_sent = 1 WHERE invitation_id = :id");
                $query->execute([':id' => $mailToSend['invitation_id']]);
                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Invitation email]: Envio de invitación a " . $mailToSend['mail_to'] . " exitoso\n";
                fwrite($log, $logTxt);
                fclose($log);
            }
        }
    } catch (Exception $e) {
        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― ERROR at sending invitation email]: Envio de invitación erroneo\n";
        fwrite($log, $logTxt);
        fclose($log);
    }
} catch (Exception $e) {
    echo "Ha surgido un error al abrir el archivo de logs.";
}
?>