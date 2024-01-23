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
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php
include_once("common/header.php")
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
    <?php
include_once("common/footer.php")
?>
</body>
</html>
