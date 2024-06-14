<?php
try {
    $logFilePath = "logs/log" . date("d-m-Y") . ".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }
    $filePathParts = explode("/", __FILE__);
} catch (Exception $e) {
    echo "<script>errorNotification('Ha habido un error al crear el archivo de logs: $e')</script>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <meta name="description" content="Inicia sesión en tu cuenta para acceder a 'Vota!'">
    <link rel="stylesheet" href="styles.css?<?php echo date('Y-m-d_H:i:s'); ?>">
    <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="/componentes/notificationHandler.js"></script>
</head>

<body id="login">
    <?php include_once("common/header.php"); ?>
    <ul id="notification__list"></ul>
    <main>
        <h1>Iniciar sesión</h1>
        <form method="post">
            <input type="email" name="email" placeholder="email" required>
            <br>
            <input type="password" name="password" placeholder="contraseña" required>
            <br>
            <a href="forgot_pass.php" class="forgot-pass-link">¿Has olvidado tu contraseña?</a>
            <button id="login-btn" type="submit">Iniciar sesión</button>
        </form>
    </main>

    <div class="footer">
        <?php include_once("common/footer.php") ?>
    </div>


</body>

</html>

<?php
try {
    include 'data/dbAccess.php';

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $passhash = hash('sha512', $password);
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

        // Cambiar query
        // $query = $pdo->prepare("SELECT * FROM `User` WHERE `user_pass` = SHA2(:pwd, 512) AND `user_mail` = :email");
        $query = $pdo->prepare("SELECT * FROM User WHERE user_pass = :pwd AND user_mail = :email");

        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':pwd', $passhash, PDO::PARAM_STR);
        // $query->bindParam(':pwd', $password, PDO::PARAM_STR);

        $query->execute();

        $row = $query->fetch();
        if ($row) {
            $_SESSION["usuario"] = $row['user_id'];
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― LOGIN SUCCESFULL]: El usuario $email ha iniciado sesión.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);

            if (!$row['is_mail_valid']) {
                header("Location: mail_verification.php");
                exit();
            }
            if (!$row['conditions_accepted']) {
                header("Location: terms_conditions.php");
                exit();
            }
            header("Location: dashboard.php?succ=1");
            exit();
        } else {
            // Añadir las notificaciones
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― LOGIN ERROR]: los datos introducidos son erroneos o no existen\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script> errorNotification('Los datos no coinciden en nuestra base de datos o no existen.'); </script>";
        }
    } else {
        // echo "no va";
    }
} catch (PDOException $e) {
    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― DB ERROR]: Ha habido un error al conectarse a la base de datos: " . $e->getMessage() . "\n";
    file_put_contents($logFilePath, $logTxt, FILE_APPEND);
    echo "<script>errorNotification('Ha habido un error al conectarse a la base de datos.')</script>";
}
?>