<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require "vendor/autoload.php";


// PHPMailer configuration:
$mail = new PHPMailer(); $mail->IsSMTP(); $mail->Mailer = "smtp";
$mail->SMTPDebug  = 1;  
$mail->SMTPAuth   = TRUE;
$mail->SMTPSecure = "tls";
$mail->Port       = 587;
$mail->Host       = "smtp.gmail.com";
$mail->Username   = "pcortesgarcia.cf@iesesteveterradas.cat";
$mail->Password   = "p0werR@nger_rojo";
$mail->SetFrom("pcortesgarcia.cf@iesesteveterradas.cat", "AWS-24");


// PDO configuration:
$pdo = new PDO("mysql:host=localhost;dbname=votadb", 'root', 'p@raMor3'); //AWS24VotaPRRojo_
$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


function generarToken($pdo) { 
    $query = $pdo -> prepare("SELECT token FROM Invitation;");
    $query -> execute();

    $query -> fetchAll();

    $token = "";
    
    if (!$query -> rowCount() == 0) {
        foreach ($query as $row) {
            do {
                $token = bin2hex(random_bytes(50));
            } while (in_array($token, $row["token"]));
        }
    } else {
        $logFilePath = "logs/log" . date("d-m-Y") .".txt";
        if (!file_exists(dirname($logFilePath))) {
            mkdir(dirname($logFilePath), 0755, true);
        }
    
        $log = fopen($logFilePath, "a");
        $logTxt = "\n[" . end(explode("/", __FILE__)) . " ― " . date('H:i:s') . " ― ALERTA al mandar invitaciones]: No se han encontrado invitaciones en la tabla, por lo tanto, no hay tokens.\n";
        
        fwrite($log, $logTxt);
        fclose($log);
    }
    
    return $token;
}


try {
    $query = $pdo -> prepare("SELECT country from Invitation WHERE is_send = false LIMIT 5;");
    $query -> execute();
    
    if (!$query -> rowCount() == 0) {
        $emails = [];
        foreach ($query as $row) {
            $anchor = "<a href='https://aws24.ieti.site/vote.php?token=". generarToken($pdo) ."' target='_blank' rel='noopener noreferrer'>Haz click aquí<a>";
    
            $mail -> IsHTML(true);
            $mail -> AddAddress($row["mail_to"]);
            $mail -> Subject = "Has sido invitadx a rellenar un formulario.";
            $mail -> MsgHTML("Enlace del formulario: $anchor");
    
            if (!$mail->Send()) {
                $logFilePath = "logs/log" . date("d-m-Y") .".txt";
                if (!file_exists(dirname($logFilePath))) {
                    mkdir(dirname($logFilePath), 0755, true);
                }
            
                $log = fopen($logFilePath, "a");
                $logTxt = "\n[" . end(explode("/", __FILE__)) . " ― " . date('H:i:s') . " ― Email sending ERROR]: Mail wasn't sent correctly to ". $row["mail_to"] ."\n";
                
                fwrite($log, $logTxt);
                fclose($log);
            }

            array_push($emails, $row['mail_to']);
        }
    }
} catch (PDOException $e) {
    $logFilePath = "logs/log" . date("d-m-Y") .".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }

    $log = fopen($logFilePath, "a");
    $logTxt = "\n[" . end(explode("/", __FILE__)) . " ― " . date('H:i:s') . " ― MySQL ERROR]: " . $e . "\n";
    
    fwrite($log, $logTxt);
    fclose($log);
}


?>