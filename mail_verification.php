<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['token'])) {
    // Busca el token en la base de datos
    $query = $db->prepare("SELECT * FROM User WHERE token = ?");
    $query->execute($_GET['token']);
    $row = $query->fetch();

    // Si el token es válido, verifica el correo electrónico
    if ($row) {
        if ($row['user_id']) {
            if ($row['user_id'] === $_SESSION["usuario"]) {
                $query = $db->prepare("UPDATE User SET is_mail_valid = true WHERE token = ?");
                $query->execute([$_GET['token']]);
                echo 'Correo electrónico verificado!';
                header("Location: dashboard.php?succ=1");
                exit();
            } else {
                echo 'El token no es válido.';
            }
        } else {
            echo 'El token no es válido.';
        }
    } else {
        echo 'El token no es válido.';
    }
} else {
    echo 'No se ha encontrado ningún token.';
}
try {
    include 'data/dbAccess.php';
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
    echo "hola";

    $query = $pdo->prepare("SELECT * FROM User WHERE user_id = :id");
    // $query = $pdo->prepare("SELECT * FROM Users WHERE user_pass = :pwd AND user_mail = :email");

    $query->bindParam(':id', $_SESSION["usuario"], PDO::PARAM_STR);

    $query->execute();

    $row = $query->fetch();
    echo "hola";

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
            echo "<br>";
            $token = bin2hex(random_bytes(50));
            echo $token;
            // Guarda el token en la base de datos
            $query = $db->prepare("UPDATE User SET token = :token WHERE user_id = :user_id");
            echo "<br>1";
            $query->bindParam('token', $token);
            echo "<br>2";
            $query->bindParam('user_id', $_SESSION["usuario"]);
            echo "<br>antes de lanzar el execute";
            echo "<br>3";
            $query->execute();
            echo "<br>despues de lanzar el execute";
            // Construye el enlace de verificación
            $link = "https://aws24.ieti.site/mail_verification.php?token=$token";

            // Envía el correo electrónico
            mail($email, 'Verificación de correo electrónico', "Haz clic en este enlace para verificar tu correo electrónico: $link");


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
                <main id="login">
                    <h1>Verifica tu dirección de email</h1>
                    <p>Te hemos enviado un email a <b>
                            <?php echo $row['user_mail'] ?>
                        </b> con un enlace para verificar tu dirección de email.</p>
                    <p>Si no lo encuentras, revisa la carpeta de spam.</p>
                    <a href="https://mail.google.com/mail">Ir al correo</a>
                    <a href="index.php" class="backhome">Volver a Inicio</a>
                </main>

                <ul id="notification__list"></ul>
                <div class="footer">
                    <?php include_once("common/footer.php") ?>
                </div>
            </body>

            </html>
            <?php
        }
    }
} catch (PDOException $e) {
    echo "<script>errorNotification('ERROR al conectarse con la base de datos -> " . $e->getMessage() . "')</script>";
}