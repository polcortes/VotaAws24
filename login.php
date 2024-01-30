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

<body>
    <main id="login">
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

    <ul id="notification__list">
        <!-- todas las notificaciones -->
    </ul>
    <?php
    include_once("common/footer.php")
        ?>


</body>

</html>

<?php
try {
    include 'data/dbAccess.php';

    if (isset($_POST['email']) && isset($_POST['password'])) {
        echo $_POST['email'];
        echo $_POST['password'];
        $email = $_POST["email"];
        $password = $_POST["password"];

        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

        // Cambiar query
        $query = $pdo->prepare("SELECT * FROM `User` WHERE `user_pass` = SHA2(:pwd, 512) AND `user_mail` = :email");
        // $query = $pdo->prepare("SELECT * FROM User WHERE user_pass = :pwd AND user_mail = :email");

        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':pwd', $password, PDO::PARAM_STR);

        $query->execute();

        $row = $query->fetch();
        echo "DESPUES DEL FETCH";
        // Cambiar parámetro dentro de $row
        if ($row) {
            session_start();
            $_SESSION["usuario"] = $row['user_id'];
            $_SESSION['nombre'] = $row['customer_name'];
            echo "aNTES DEL IF";

            if (!$row['is_mail_valid']) {
                header("Location: mail_verification.php");
                exit();

            } else if ($row['conditions_accepted']) {
                header("Location: terms_conditions.php");
                exit();
            }
            header("Location: dashboard.php?succ=1");
            exit();
        } else {
            // Añadir las notificaciones
            echo "<script> errorNotification('Los datos no coinciden en nuestra base de datos o no existen.'); </script>";
        }
    }



} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();

}
include 'data/dbAccess.php';

?>