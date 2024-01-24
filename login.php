<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php
include_once("common/header.php")
?>
    <main id="login">
        <h1>Iniciar sesión</h1>
        <form method="post">
            <input type="email" name="email" placeholder="email" required>
            <br>
            <input type="password" name="password" placeholder="contraseña" required>
            <br>
            <button type="submit">Iniciar sesión</button>
        </form>
    </main>
    <?php
include_once("common/footer.php")
?>
</body>
</html>

<?php

if (isset($_POST['email']) && isset($_POST['password'])) {

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Cambiar parámetros, conexión a BD
    $dsn = "mysql:host=localhost;dbname=votadb";
    $pdo = new PDO($dsn, 'root', 'Pepe25');

    // Cambiar query
    $query = $pdo->prepare("SELECT * FROM Users WHERE user_pass = SHA2(:pwd, 512) AND user_mail = :email");
    // $query = $pdo->prepare("SELECT * FROM Users WHERE user_pass = :pwd AND user_mail = :email");

    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':pwd', $password, PDO::PARAM_STR);
    
    $query->execute();
    
    $row = $query->fetch();

    // Cambiar parámetro dentro de $row
    if ($row) {
        $_SESSION['usuario'] = $row['user_id'];
        $_SESSION['nombre'] = $row['customer_name'];
        header("Location: dashboard.php");
        exit();
    } else {
        // Añadir las notificaciones
        echo "Login Incorrecto";
    }
}
?>