
<?php

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Pepe25";
$database = "votadb"; // Asegúrate de usar el nombre correcto de tu base de datos

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT country_name,tel_prefix,country_id FROM Countries";
    $sql2 = "SELECT user_mail FROM Users";
    $sql3 = "SELECT user_tel FROM Users";
    $stmt = $conn->prepare($sql);
    $stmt2 = $conn->prepare($sql2);
    $stmt3 = $conn->prepare($sql3);

    $stmt->execute();
    $stmt2->execute();
    $stmt3->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    $country_names = $result;
    $correxistente = $result2;
    $telsexistente = $result3;

    $conn = null;
    if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $conn_insert = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn_insert->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nombre = $_POST['register_name'];
    $email = $_POST['register_email'];
    $pass = $_POST['register_pass'];
    $passcheck = $_POST['register_repeat_pass'];
    $pais = $_POST['register_pais'];
    $tel = $_POST['register_tel'];
    $prefix = $_POST['register_prefix'];
    $ciudad = $_POST['register_ciudad'];
    $cp = $_POST['register_cp'];
    $telefono_sin_guiones = str_replace("-", "", $tel);
    $telefonoprp = preg_replace("/[^0-9]/", "", $telefono_sin_guiones);
    
    $telefonodef = $prefix . "" . $telefonoprp;

    echo $telefonodef;
    if(!validarNombre($nombre)){
        echo "nonombre";
    }else if(!validarEmail($email)){
        echo "noemail";
    }elseif(validarPass($pass)){
        echo "nopassvalid";
    }else if($pass != $passcheck){
        echo "nopassigual";
    }else if (!validarPais($pais,$country_names)) {
        echo "El país no está en la lista de nombres de país.";
    }else if(strlen($telefonoprp)!== 9){
        echo "notel";
    }else if(!validarPrefix($prefix)){
        echo "noprefix";
    }else if(!validarNombre($ciudad)){
        echo "nociudad";
    }else if(strlen($cp)!== 5){
        echo "nocp";
    }else if(!emailRepetido($email,$correxistente)){
        echo "repe";
    }elseif(!telRepetido($telefonoprp,$telsexistente)){
        echo "repetel";
    }else{
    $passhash = hash('sha512',$pass);
    $sql_insert = "INSERT INTO Users (customer_name, user_city, user_country, user_country_id, user_cp, user_mail, user_pass, user_tel) VALUES (:nombre, :ciudad, :pais, 248, :cp, :email, :pass, :tel )";
    $stmt_insert = $conn_insert->prepare($sql_insert);
    $stmt_insert->bindParam(':nombre', $nombre);
    $stmt_insert->bindParam(':email', $email);
    $stmt_insert->bindParam(':pass', $passhash);
    $stmt_insert->bindParam(':pais', $pais);
    $stmt_insert->bindParam(':tel', $telefonoprp);
    $stmt_insert->bindParam(':ciudad', $ciudad);
    $stmt_insert->bindParam(':cp', $cp);

    $stmt_insert->execute();
    $conn_insert = null;
    }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

function validarNombre($nombre) {
    $regex = '/^[A-Za-z\s]+$/';

    if (preg_match($regex, $nombre)) {
        return true;
    } else {
        return false;
    }
}

function validarEmail($email) {
    $regex = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';

    if (preg_match($regex, $email)) {
        return true;
    } else {
        return false;
    }
}

function validarPass($pass){
    if(strlen($contrasena) >= 8 && preg_match('/[A-Z]/', $contrasena) && preg_match('/[a-z]/', $contrasena) && preg_match('/[0-9]/', $contrasena)){
        return true;
    }else{
        return false;
    }
}

function validarPais($pais,$listapaises){
    $nombrepaises = array();
    foreach ($listapaises as $nombrepais) {
        $nombrepaises[] = $nombrepais['country_name'];
    }

    if (!in_array($pais, $nombrepaises)) {
        return false;
    } else {
        return true;
    }
}

function emailRepetido($email,$listamails){
    $mails = array();
    foreach ($listamails as $mail) {
        $mails[] = $mail['user_mail'];
    }
    if (in_array($email, $mails)) {
        return false;
    } else {
        return true;
    }
}

function telRepetido($telefono,$listatelefonos){
    $tels = array();
    foreach ($listatelefonos as $tel) {
        $tels[] = $tel['user_tel'];
    }
    if (in_array($telefono, $tels)) {
        return false;
    } else {
        return true;
    }
}

function validarPrefix($prefix){
    $regex = '/^\+\d{1,3}$/';
    if (!preg_match($regex, $prefix)) {
        return false;
    } else {
        return true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear una nueva cuenta | Vota!</title>
    <meta name="description" content="Página para registrarse en nuestra web. ¡Crea una cuenta y podrás participar y generar encuestas para todo el mundo!">
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="script.js"></script>
</head>
<body id="crear-cuenta">
    <main>
        <?php
    echo "<input type='hidden' name='countries' id='jsoncountry' value='" . json_encode($country_names, JSON_UNESCAPED_UNICODE) . "'>";
    ?>
    <a href="index.php" class="backhome">Volver a Inicio</a>

    <h1>Crear una cuenta</h1>
        <form method="POST">
            
            <label for="register_name">Nombre<span class="required">*</span></label>
            <input type="text" name="register_name" id="register_name" placeholder="María" required>

        </form>

    </main>

    <ul id="notification__list">
        <!-- todas las notificaciones -->
    </ul>

    <script src="componentes/notificationHandler.js"></script>
</body>
</html>
