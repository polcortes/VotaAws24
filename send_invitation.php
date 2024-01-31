<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    foreach ($mailsToSend as $mail) {
        $link = "https://aws24.ieti.site/vote.php?token=" . $mail['invitation_token'] . "&survey_id=" . $mail['survey_id'];
        $mail->IsHTML(true);
        $mail->AddAddress($mail['mail_to']);
        $mail->SetFrom($emailUsername);
        $mail->Subject = '¡PARTICIPA EN MI ENCUESTA!';
        $content = "Haz clic en este enlace para participar en mi encuesta: $link";

        $mail->MsgHTML($content);
        if (!$mail->Send()) {
            echo "<script>errorNotification('Ha surgido un error al enviar el email.')</script>";
        } else {
            $query = $pdo->prepare("UPDATE Invitation SET is_sent = 1 WHERE invitation_id = :id");
            $query->execute([':id' => $mail['invitation_id']]);
        }
    }
} catch (Exception $e) {
    echo "<script>errorNotification('Ha surgido un error al enviar el email.')</script>";
}
