<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <meta name="description" content="Inicia sesión en tu cuenta para acceder a 'Vota!'">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="/componentes/notificationHandler.js"></script>
</head>

<body id="login">
    <main>
        <h1>Iniciar sesión</h1>
        <form method="post">
            <input type="email" name="email" placeholder="email" required>
            <br>
            <input type="password" name="password" placeholder="contraseña" required>
            <br>
            <button type="submit">Iniciar sesión</button>
        </form>
        <a href="index.php" class="backhome">Volver a Inicio</a>
    </main>

    <ul id="notification__list"></ul>
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
    $query = $pdo->prepare("SELECT * FROM User WHERE user_pass = SHA2(:pwd, 512) AND user_mail = :email");
    // $query = $pdo->prepare("SELECT * FROM Users WHERE user_pass = :pwd AND user_mail = :email");

        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':pwd', $passhash, PDO::PARAM_STR);

        $query->execute();

        $row = $query->fetch();
        if ($row) {
            session_start();
            $_SESSION["usuario"] = $row['user_id'];

            if (!$row['is_mail_valid']) {
                header("Location: mail_verification.php");
                exit();

            }
            if (!$row['conditions_accepted']) {
                header("Location: terms_conditions.php");
                exit();
            }
            
        $logFilePath = "logs/log" . date("d-m-Y") .".txt";
        if (!file_exists(dirname($logFilePath))) {
            mkdir(dirname($logFilePath), 0755, true);
        }

        $log = fopen($logFilePath, "a");
        $logTxt = "\n[" . end(explode("/", __FILE__)) . " ― " . date('H:i:s') . " ― Successful login]: El usuario ".$email." con la ID ".$row['user_id']." ha iniciado sesión correctamente.\n";
        
        fwrite($log, $logTxt);
        fclose($log);
        
        header("Location: dashboard.php?succ=1");
            exit();
        } else {
            // Añadir las notificaciones
            echo "<script> errorNotification('Los datos no coinciden en nuestra base de datos o no existen.'); </script>";
            $logFilePath = "logs/log" . date("d-m-Y") .".txt";
        if (!file_exists(dirname($logFilePath))) {
            mkdir(dirname($logFilePath), 0755, true);
        }
    
        $log = fopen($logFilePath, "a");
        $logTxt = "\n[" . end(explode("/", __FILE__)) . " ― " . date('H:i:s') . " ― Login error]: El usuario con ID ".$_SESSION['usuario']." y nombre ".$_SESSION["nombre"]." ha iniciado sesión correctamente.\n";
        
        fwrite($log, $logTxt);
        fclose($log);
    }
} else {
    // Aquí va hacer cosas del sistema de logs.
    }



} catch (PDOException $e) {
    echo "<script>errorNotification('ERROR al conectarse con la base de datos -> " . $e->getMessage() . "')</script>";
}
?>

