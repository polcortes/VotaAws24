<?php
session_start();

// Verifica si el usuario ha iniciado sesión
// if (!isset($_SESSION["usuario"])) {
//     // header("HTTP/1.1 403 Forbidden");
//     exit();
// }
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
    <main id="dashboard">
        <!-- <h1>Bienvenido, <?php echo $_SESSION["usuario"]; ?></h1> -->
        <h1>Bienvenido, Usuario</h1>
        
        <ul>
            <li><a href="">Ver mis encuestas</a></li>
            <li><a href="">Crear encuestas</a></li>
            <li><a href="">Ver todas la encuestas</a></li>
        </ul>
        
        <a href="logout.php">Cerrar sesión</a>
    </main>
</body>
</html>
