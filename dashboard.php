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
    <script src="/componentes/notificationHandler.js"></script>
</head>
<body>
<?php
include_once("common/header.php");
?>
    <main id="dashboard">
        <h1>Bienvenido, <?php echo $_SESSION["nombre"]; ?></h1>
        
        <ul>
            <li><a href="list_polls.php">Ver mis encuestas</a></li>
            <li><a href="create_poll.php">Crear encuestas</a></li>
            <li><a href="#">Ver todas las encuestas</a></li>
        </ul>
        
        <a id="logout" href="logout.php">Cerrar sesión</a>
    </main>

    <ul id="notification__list">
        <!-- todas las notificaciones -->
    </ul>
    <?php
    if (isset($_GET["succ"])) {
        echo "<script>successfulNotification('Has iniciado sesión correctamente.');</script>";
    }
include_once("common/footer.php");
?>
</body>
</html>
