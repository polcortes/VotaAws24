<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"])) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Vota!</title>
    <meta name="description" content='Accede a diversas funciones disponibles tras crearte una cuenta en "Vota!"'>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="componentes/notificationHandler.js"></script>
    <script src="dashboard.js"></script>
</head>
<body>
    <?php
    if ((!isset($_SESSION["conditions_accepted"]) || $_SESSION["conditions_accepted"] == false) && !isset($_POST['conditions'])) {
        echo "
        <dialog id='conditions_dialog'>
            <h1>Tienes que aceptar nuestras condiciones de uso</h1>
            <p>Para poder usar nuestra aplicación web, tendrás que aceptar los siguientes términos y condiciones de uso. Pulse la checkbox de abajo si está de acuerdo y podrá disfrutar de Vota!</p>
            <form method='POST'>
                <label>
                    <input type='checkbox' name='conditions' id='conditions'>
                    Acepto los terminos y condiciones de uso de esta web<span class='required'>*</span>
                </label>
                <button type='button' disabled title='Activa el checkbox para poder avanzar.'>Enviar</button>
            </form>
            
            <ul id='notification__list'>
    
            </ul>
        </dialog>
        
        ";
    } else {
        include_once("common/header.php");
        
        if (isset($_POST['conditions'])) {
            $dsn = "mysql:host=localhost;dbname=votadb";
            $pdo = new PDO($dsn, 'root', 'p@raMor3');

            // Cambiar query
            $query = $pdo->prepare("UPDATE user SET conditions_accepted = true WHERE user_id = :user_id");
            $query -> bindParam(":user_id", $_SESSION["usuario"]);
        }

        echo '<main id="dashboard">
            <h1>Bienvenido, '.$_SESSION["nombre"].'</h1>
            
            <ul>
                <li><a href="list_polls.php">Ver mis encuestas</a></li>
                <li><a href="create_poll.php">Crear encuestas</a></li>
                <li><a href="#">Ver encuestas pendientes</a></li>
            </ul>
            
            <!--<a id="logout" href="logout.php">Cerrar sesión</a>-->
        </main>
        
        <ul id="notification__list">
            <!-- todas las notificaciones -->
        </ul>';
        
        if (isset($_GET["succ"])) {
            echo "<script>successfulNotification('Has iniciado sesión correctamente.');</script>";

            
        }
        include_once("common/footer.php");
    }
    ?>
</body>
</html>
