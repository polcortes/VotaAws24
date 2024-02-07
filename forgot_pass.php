<?php
session_start();

if (!isset($_SESSION[""]))
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Has olvidado tu contraseña?</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body id="forgot-pass">
    <?php include("common/header.php"); ?>
    <?php if (!isset($_POST["email-forgot"])): ?>
        <main>
            <h1>¿Has olvidado tu contraseña?</h1>
            <div>
                <p>¡No te preocupes! Puedes recuperarla introduciendo tu mail en el siguiente campo de texto.</p>
                <p>Te enviaremos un correo con indicaciones para poder cambiar tu contraseña. Aún así, te tenemos que advertir de lo siguiente:</p>
                <p>Al restablecer tu contraseña, perderás por completo el acceso a la lista de encuestas en las que has participado.</p>
            </div>
            <form method="POST">
                <label for="email-forgot">Email:</label>
                <input type="email" name="email-forgot" id="email-forgot" placeholder="ejemplo@dominio.com">

                <input type="submit" value="Enviar ">
            </form>
        </main>
    <?php else: ?>

    <?php endif; ?>

    <?php include("common/footer.php"); ?>
</body>
</html>