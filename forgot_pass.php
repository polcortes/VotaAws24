<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


session_destroy();

$logFilePath = "logs/log" . date("d-m-Y") . ".txt";
if (!file_exists(dirname($logFilePath))) {
    mkdir(dirname($logFilePath), 0755, true);
}
$filePathParts = explode("/", __FILE__);

try {
    require 'data/dbAccess.php';

    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

} catch (PDOException $e) {
    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― DB ERROR]: Ha habido un error al conectarse a la base de datos: " . $e->getMessage() . "\n";
    file_put_contents($logFilePath, $logTxt, FILE_APPEND);
    echo "<script>errorNotification('Ha habido un error al conectarse a la base de datos.')</script>";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Has olvidado tu contraseña?</title>
    <meta name="description" content="Verifica tu dirección de email para acceder a 'Vota!'">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="/componentes/notificationHandler.js"></script>
    <script src="/componentes/forgot_pass.js"></script>
</head>

<body id="forgot-pass">
    <?php
    include_once("common/header.php"); ?>
    <ul id="notification__list"></ul>
    <?php if (isset($_POST['pass']) || isset($_POST['pass-confirm']) || isset($_POST['mail'])):
        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― POST]: Hemos recibido el post.\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);

        $password = $_POST['pass'];
        $passwordHash = hash('sha512', $password);
        try {
            $query = $pdo->prepare("UPDATE User SET user_pass = :passHash WHERE user_mail = :email");
            $query->execute([':passHash' => $passwordHash, ':email' => $_POST['mail']]);
        } catch (PDOException $e) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― SQL ERROR]: " . $e->getMessage() . "\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            die();
        }
        session_destroy();
        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― PASSWORD UPDATE]: Se ha actualizado la contraseña del usuario " . $_POST["mail"] . ".\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
        echo "
            <script>
                successfulNotification('¡Has actualizado tu contraseña!');
                window.location.href = 'login.php';
            </script>
        ";
    else:
        if (!isset($_GET['token'])):
            if (!isset($_POST["email-forgot"])): ?>
                <main>
                    <h1>¿Has olvidado tu contraseña?</h1>
                    <div>
                        <p>¡No te preocupes! Puedes recuperarla introduciendo tu mail en el siguiente campo de texto.</p>
                        <p>Te enviaremos un correo con indicaciones para poder cambiar tu contraseña. Aún así, te tenemos que
                            advertir de lo siguiente:</p>
                        <p>Al restablecer tu contraseña, perderás por completo el acceso a la lista de encuestas en las que has
                            participado.</p>
                    </div>
                    <form method="POST" id="forgot-form">
                        <label for="email-forgot">Email:</label>
                        <input type="email" name="email-forgot" id="email-forgot" placeholder="ejemplo@dominio.com">

                        <input type="submit" value="Enviar">
                    </form>
                </main>
            <?php else:
                require 'PHPMailer-master/src/Exception.php';
                require 'PHPMailer-master/src/PHPMailer.php';
                require 'PHPMailer-master/src/SMTP.php';
                require 'data/dbAccess.php';
                require 'data/mailCredentials.php';

                $query = $pdo->prepare("SELECT * FROM User WHERE user_mail = :mail");
                $query->execute([':mail' => $_POST["email-forgot"]]);

                if ($query->rowCount() === 0) {
                    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― USER NOT FOUND]: No hemos encontrado ningun usuario con el correo " . $_POST["email-forgot"] . ".\n";
                    file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                    echo "<script>errorNotification('No hemos encontrado ningun usuario con el correo indicado. Por favor, inténtalo de nuevo.')</script>";
                } else {
                    $row = $query->fetch();

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

                    $link = "https://aws24.ieti.site/vote.php?token=" . $row['token'];
                    $mail->IsHTML(true);
                    $mail->AddAddress($_POST["email-forgot"]);
                    $mail->SetFrom($emailUsername);
                    $mail->Subject = '¿HAS OLVIDADO TU CONTRASEÑA?';
                    $mail->SetFrom('vota@aws24.ieti.com', 'Vota Team');
                    $content = "Haz clic en este enlace para cambiar tu contraseña: $link";
                    $mail->MsgHTML($content);
                    $mail->CharSet = 'UTF-8';
                    $mail->Send();

                    if (!$mail->Send()) {
                        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― ERROR MAIL SENDING]: Envio de correo de recuperacion de contraseña a " . $_POST["email-forgot"] . " erroneo.\n";
                        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                    } else {
                        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― MAIL SENDIT]: Envio de email de recuperación de contraseña a " . $_POST["email-forgot"] . " exitoso\n";
                        file_put_contents($logFilePath, $logTxt, FILE_APPEND); ?>
                        <main>
                            <h1>VERIFICA QUE ERES TU</h1>
                            <p>Te hemos enviado un email a <b>
                                    <?php echo $_POST["email-forgot"] ?>
                                </b> con un enlace para verificar que realmente eres tu.</p>
                            <p>Si no lo encuentras, revisa la carpeta de spam.</p>
                            <a href="https://mail.google.com/mail" target="_blank">Ir al correo</a>
                        </main>

                        <script>successfulNotification('¡Email de recuperación de contraseña enviado!');</script>;
                    <?php }
                }
            endif;
        else:
            $query = $pdo->prepare("SELECT * FROM User WHERE token = :token");
            $query->execute([':token' => $_GET['token']]);

            if ($query->rowCount() === 0) {
                $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― TOKEN NOT FOUND]: No hemos encontrado ningun usuario con el token " . $_GET['token'] . ".\n";
                file_put_contents($logFilePath, $logTxt, FILE_APPEND);
                echo "<script>errorNotification('No hemos encontrado ningún usuario con ese token. Por favor, inténtalo de nuevo.')</scrip>";
            } else {
                $row = $query->fetch();
                $_SESSION["usuario"] = $row['user_id']; ?>

                <main>
                    <form method="POST" id="change-password-form">
                        <label for="pass">Nueva contraseña:</label>
                        <input type="password" name="pass" id="pass" required>
                        <label for="pass-confirm">Confirma tu nueva contraseña:</label>
                        <input type="password" name="pass-confirm" id="pass-confirm" required>
                        <input type="hidden" name="mail" value="<?php echo $row['user_mail']; ?>">
                        <input type="submit" value="Cambiar contraseña">
                    </form>
                </main>
            <?php }
        endif;
    endif;
    include("common/footer.php"); ?>
</body>

</html>