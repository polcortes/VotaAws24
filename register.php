<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear una nueva cuenta | Vota!</title>
    <meta name="description" content="Página para registrarse en nuestra web. ¡Crea una cuenta y podrás participar y generar encuestas para todo el mundo!">
    <link rel="stylesheet" href="styles.css">
</head>
<body id="crear-cuenta">
    <main>
        <h1>Crear una cuenta</h1>
        <form method="POST">
            <label for="register_email">Email<span class="required">*</span></label>
            <input type="email" name="register_email" id="register_email" placeholder="tucorreo@gmail.com" required>
            
            <label for="register_name">Nombre<span class="required">*</span></label>
            <input type="text" name="register_name" id="register_name" placeholder="María" required>
            
            <div class="divisor">
                <span>
                    <label for="register_pass">Contraseña<span class="required">*</span></label>
                    <input type="password" name="register_pass" id="register_pass" placeholder="Min. 8 carácteres" min="8" required>
                </span>
                
                <span>
                    <label for="register_repeat_pass">Repite la contraseña<span class="required">*</span></label>
                    <input type="password" name="register_repeat_pass" id="register_repeat_pass" placeholder="Repite la contraseña" required>
                </span>
            </div>
            
            <label for="register_tel">Teléfono<span class="required">*</span></label>
            <input type="tel" name="register_tel" id="register_tel" placeholder="639122561" required>
            
            <label for="register_pais">País<span class="required">*</span></label>
            <select name="register_pais" id="register_pais" required>
                <option disabled selected>Selecciona tu país</option>
                <option value="hola">Hola</option>
                <?php /* Recoger todos los paises de la base de datos y ponerlo en options */ ?>
            </select>
            
            <div class="divisor">
                <span>
                    <label for="register_ciudad">Ciudad<span class="required">*</span></label>
                    <input type="text" name="register_ciudad" id="register_ciudad" placeholder="Sídney" required>
                </span>
            
                <span>
                    <label for="register_cp">Código postal<span class="required">*</span></label>
                    <input type="number" name="register_cp" id="register_cp" placeholder="20852" required>
                </span>
            </div>

            <input type="submit" value="Crear cuenta">
        </form>
    </main>
</body>
</html>