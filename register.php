<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear una nueva cuenta | Vota!</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <h1>Crear una cuenta</h1>
        <form method="POST">
            <label for="register_email">Email</label>
            <input type="email" name="register_email" id="register_email">
            
            <label for="register_name">Nombre</label>
            <input type="text" name="register_name" id="register_name">
            
            <label for="register_pass">Contraseña</label>
            <input type="password" name="register_pass" id="register_pass">
            
            <label for="register_repeat_pass">Repite la Contraseña</label>
            <input type="password" name="register_repeat_pass" id="register_repeat_pass">
            
            <label for="register_tel">Teléfono</label>
            <input type="tel" name="register_tel" id="register_tel">
            
            <!--<label for="register_pais"></label>-->
            <select name="register_pais" id="register_pais">
                <?php /* Recoger todos los paises de la base de datos y ponerlo en options */ ?>
            </select>
            
            <label for="register_ciudad">Ciudad</label>
            <input type="text" name="register_ciudad" id="register_ciudad">
            
            <label for=""></label>
            <input type="text">
        </form>
    </main>
</body>
</html>