<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"])) {
    // Redirige a la página de inicio de sesión si no ha iniciado sesión
    header("Location: index.php");
    exit();
}

// Cierra la sesión actual
session_destroy();

// Redirige a la página de inicio de sesión
header("Location: index.php");
exit();
?>
