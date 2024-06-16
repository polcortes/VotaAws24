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
        session_start();

        require 'PHPMailer-master/src/Exception.php';
        require 'PHPMailer-master/src/PHPMailer.php';
        require 'PHPMailer-master/src/SMTP.php';
        require 'data/dbAccess.php';
        require 'data/mailCredentials.php';

        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― BD CONNECTION] Conexión con la db establecida\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);

        $query = $pdo->prepare("SELECT * FROM User WHERE user_id = :id");
        $query->execute([':id' => $_SESSION["usuario"]]);

        $mail = new PHPMailer();
        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― PHPMailer OK] PHPMailer OK\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->IsSMTP();

        $mail->SMTPAuth = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->Host = "smtp.gmail.com";
        $mail->Username = $emailUsername;
        $mail->Password = $emailPassword;

        $query = $pdo->prepare("SELECT * FROM Invitation WHERE is_send = false LIMIT 5");
        $query->execute();
        $mailsToSend = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($mailsToSend as $mailToSend) {
            $link = "https://pcortesgarcia.ieti.site/vote.php?token=" . $mailToSend['invitation_token'] . "&survey_id=" . $mailToSend['survey_id'];
            $mail->IsHTML(true);
            $mail->AddAddress($mailToSend['mail_to']);
            $mail->SetFrom($emailUsername);
            $mail->Subject = '¡PARTICIPA EN MI ENCUESTA!';
            $content = "Haz clic en este enlace para participar en mi encuesta: $link";
            $mail->MsgHTML($content);
            $mail->CharSet = 'UTF-8';
            // $mail->Send();

            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            if (!$mail->Send()) {
                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― ERROR at sending invitation email]: Envio de invitación a " . $mailToSend['mail_to'] . " erroneo: " . $mail->ErrorInfo . "\n";
                file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            } else {
                $query = $pdo->prepare("UPDATE Invitation SET is_send = true WHERE invitation_id = :id");
                $query->execute([':id' => $mailToSend['invitation_id']]);

                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Invitation email]: Envio de invitación a " . $mailToSend['mail_to'] . " exitoso\n";
                file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            }
        }
    } catch (Exception $e) {
        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― ERROR at sending invitation email]: Envio de invitación erroneo\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
    }
} catch (Exception $e) {
    echo "Ha surgido un error al abrir el archivo de logs.";
}
?>