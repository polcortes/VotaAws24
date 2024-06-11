<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'data/mailCredentials.php';

$logFilePath = "logs/log" . date("d-m-Y") . ".txt";
if (!file_exists(dirname($logFilePath))) {
    mkdir(dirname($logFilePath), 0755, true);
}
$filePathParts = explode("/", __FILE__);

try {
    $mail = new PHPMailer();
    $mail->Mailer = "smtp";
    $mail->IsSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->SMTPDebug = 1;
    $mail->SMTPAuth = TRUE;
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;
    $mail->Host = "smtp.gmail.com";
    $mail->Username = $emailUsername;
    $mail->Password = $emailPassword;

    $mail->IsHTML(true);
    $mail->AddAddress($emailUsername);
    $mail->SetFrom($emailUsername);
    $mail->Subject = 'Verificacion de correo electronico';
    $content = "Haz clic en este enlace para verificar tu correo electrónico:";

    $mail->MsgHTML($content);
    $mail->CharSet = 'UTF-8';
    // $mail->Send();
    if (!$mail->Send()) {
        echo "nova";
    } else {
        echo "va :D";
    }
} catch (Exception $e) {
    echo "nova";
}