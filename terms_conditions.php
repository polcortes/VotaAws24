<?php
session_start();
echo "hola";
// if (!isset($_SESSION["usuario"])) {
//     header("Location: login.php");
//     exit();
// }

try {
    $dsn = "mysql:host=localhost;dbname=votadb";
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

    $query = $pdo->prepare("SELECT * FROM User WHERE user_id = :id");
    // $query = $pdo->prepare("SELECT * FROM Users WHERE user_pass = :pwd AND user_mail = :email");

    $query->bindParam(':id', $_SESSION["usuario"], PDO::PARAM_STR);

    $query->execute();

    $row = $query->fetch();

    if ($row) {
        session_start();
        $_SESSION["usuario"] = $row['user_id'];
        $_SESSION['nombre'] = $row['customer_name'];

        if ($row['is_mail_valid']) {
            if ($row['conditions_accepted']) {
                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: terms_conditions.php");
                exit();
            }
        } else {
            ?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Terminos y condiciones</title>
                <meta name="description" content="Verifica tu dirección de email para acceder a 'Vota!'">
                <link rel="stylesheet" type="text/css" href="styles.css">
                <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
                <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
                <script src="https://code.jquery.com/jquery-3.7.1.js"
                    integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
                <script src="/componentes/notificationHandler.js"></script>
            </head>

            <body>
                <main id="login">
                    <h1>Verifica tu dirección de email</h1>
                    <p>Te hemos enviado un email a <b>
                            <?php echo $row['user_mail'] ?>
                        </b> con un enlace para verificar tu dirección de email.</p>
                    <p>Si no lo encuentras, revisa la carpeta de spam.</p>
                    <a href="index.php" class="backhome">Volver a Inicio</a>
                </main>

                <ul id="notification__list">
                    <!-- todas las notificaciones -->
                </ul>
                <?php
                include_once("common/footer.php")
                    ?>
            </body>

            </html>
            <?php
        }
    }
} catch (PDOException $e) {
    echo "<script>errorNotification('ERROR al conectarse con la base de datos -> " . $e->getMessage() . "')</script>";
}