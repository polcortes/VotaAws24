<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear una nueva cuenta | Vota!</title>
    <meta name="description" content="Página para registrarse en nuestra web. ¡Crea una cuenta y podrás participar y generar encuestas para todo el mundo!">
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
</head>
<body id="crear-cuenta">
    <main>
        <h1>Crear una cuenta</h1>
        <form method="POST">
            <label for="register_email">Email<span class="required">*</span></label>
            <input type="email" name="register_email" id="register_email" placeholder="tucorreo@gmail.com" required>
        </form>

        <button id="createError">Crear error</button>
    </main>

    <ul id="notification__list">
        <!-- todas las notificaciones -->
    </ul>

    <script src="componentes/notificationHandler.js"></script>
</body>
</html>