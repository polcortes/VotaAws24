<?php
try {
    $logFilePath = "logs/log" . date("d-m-Y") . ".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }
    $filePathParts = explode("/", __FILE__);

    require 'data/dbAccess.php';
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
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

    <body id="conditions">
        <?php
        include_once("common/header.php");
        if (!isset($_SESSION["usuario"])) {
            header("HTTP/1.1 403 Forbidden");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["acceptCheckbox"])) {
                $query = $pdo->prepare("UPDATE User SET `conditions_accepted` = true WHERE user_id = :id");
                $query->execute([':id' => $_SESSION["usuario"]]);
            } else {
                header("Location: index.php");
                exit();
            }
        }

        $query = $pdo->prepare("SELECT * FROM User WHERE user_id = :id");
        $query->execute([':id' => $_SESSION["usuario"]]);
        $row = $query->fetch();

        if ($row && $row['is_mail_valid'] && $row['conditions_accepted']) {
            header("Location: dashboard.php");
            exit();
        }

        ?>
        <main>
            <h1>Términos y Condiciones - Vota!</h1>
            <div id="terms">
                <p>
                    Bienvenido al portal de votaciones. Antes de utilizar nuestros servicios, lea detenidamente los
                    siguientes
                    términos y condiciones.
                </p>
                <p>
                    <span>1. Uso del Portal:</span> Usted acepta utilizar este portal de votaciones de manera ética y
                    conforme a la legislación aplicable. No está permitido el uso indebido del portal con fines ilegales o
                    fraudulentos.
                </p>
                <p>
                    <span>2. Privacidad:</span> Respetamos su privacidad. La información recopilada durante el proceso de
                    votación se utilizará exclusivamente con fines electorales y se manejará de acuerdo con nuestra política
                    de
                    privacidad. Al utilizar nuestros servicios, usted acepta nuestra política de privacidad.
                </p>
                <p>
                    <span>3. Conducta del Usuario:</span> Usted se compromete a no realizar acciones que puedan interferir
                    con el correcto funcionamiento del portal. No está permitido realizar intentos de acceso no autorizado
                    ni
                    manipular los resultados de las votaciones.
                </p>
                <p>
                    <span>4. Responsabilidad:</span> El portal de votaciones no se hace responsable de los errores en los
                    resultados de las votaciones causados por información incorrecta proporcionada por los usuarios. La
                    responsabilidad recae en los usuarios al garantizar la veracidad de la información proporcionada.
                </p>
                <p>
                    <span>5. Modificaciones:</span> Nos reservamos el derecho de modificar estos términos y condiciones en
                    cualquier momento. Las modificaciones entrarán en vigencia inmediatamente después de su publicación en
                    el
                    portal. Le recomendamos revisar periódicamente los términos y condiciones para estar al tanto de
                    cualquier
                    cambio.
                </p>

                <form method="post">
                    <p>Al utilizar nuestro portal de votaciones, usted acepta cumplir con estos términos y
                        condiciones.</p>
                    <label for="acceptCheckbox">
                        <input type="checkbox" id="acceptCheckbox" name="acceptCheckbox">
                        Acepto las condiciones establecidas
                    </label>
                    <input type="submit" value="Enviar">
                </form>
            </div>
        </main>

        <ul id="notification__list"></ul>
        <div class="footer">
            <?php include_once("common/footer.php") ?>
        </div>
    </body>

    </html>
    <?php
} catch (PDOException $e) {
    echo "<script>errorNotification('ERROR al conectarse con la base de datos -> " . $e->getMessage() . "')</script>";
}
?>