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
    
    $idpais = getCountryId($pais, $country_names);


    if(!validarNombre($nombre)){
        echo "<script>errorNotification('El nombre solo puede contener letras mayúsculas y minúsculas.')</script>";
    }else if(!validarEmail($email)){
        echo "<script>errorNotification('El correo no tiene un formato válido');</script>";
    }elseif(!validarPass($pass)){
        echo "<script>errorNotification('La contraseña no tiene un formato válido. Debe contener al menos 8 carácteres, al menos 1 letra mínusculas y mayuscula y al menos un número.')</script>";
    }else if($pass != $passcheck){
        echo "<script>errorNotification('Las contraseñas no coinciden.')</script>";
    }else if (!validarPais($pais,$country_names)) {
        echo "<script>errorNotification('El páis no está en la lista de paises.')</script>";
    }else if(strlen($telefonoprp)!== 9){
        echo "<script>errorNotification('El teléfono no es válido.')</script>";
    }else if(!validarPrefix($prefix)){
        echo "<script>errorNotification('El prefijo del teléfono no es válido.')</script>";
    }else if(!validarNombre($ciudad)){
        echo "<script>errorNotification('La ciudad que has puesto no es válida.')</script>";
    }else if(strlen($cp)!== 5){
        echo "<script>errorNotification('El código postal no es válido.')</script>";
    }else if(!emailRepetido($email,$correxistente)){
        echo "<script>errorNotification('El email ya existe en nuestra base de datos.')</script>";
    }elseif(!telRepetido($telefonoprp,$telsexistente)){
        echo "<script>errorNotification('El teléfono ya existe en nuestra base de datos.')</script>";
    }elseif (is_null($idpais)) {
        echo "<script>errorNotification('País no válido.')</script>";
    }else{
        $passhash = hash('sha512',$pass);
        $sql_insert = "INSERT INTO Users (customer_name, user_city, user_country, user_country_id, user_cp, user_mail, user_pass, user_tel) VALUES (:nombre, :ciudad, :pais, :paisid, :cp, :email, :pass, :tel )";
        $stmt_insert = $conn_insert->prepare($sql_insert);
        $stmt_insert->bindParam(':nombre', $nombre);
        $stmt_insert->bindParam(':email', $email);
        $stmt_insert->bindParam(':pass', $passhash);
        $stmt_insert->bindParam(':pais', $pais);
        $stmt_insert->bindParam(':tel', $telefonodef);
        $stmt_insert->bindParam(':ciudad', $ciudad);
        $stmt_insert->bindParam(':paisid', $idpais);
        $stmt_insert->bindParam(':cp', $cp);

        $stmt_insert->execute();
        $conn_insert = null;

        $dsn = "mysql:host=localhost;dbname=votadb";
        $pdo = new PDO($dsn, 'root', 'p@raMor3'); // AWS24VotaPRRojo_

        $query = $pdo->prepare("SELECT user_id FROM Users WHERE user_mail = :email");

        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        
        $row = $query->fetch();

        if ($row) {
            session_start();
            $_SESSION["usuario"] = $row["user_id"];
            $_SESSION['nombre'] = $nombre;
            header("Location: dashboard.php?succ=1");
            exit();
        } else {
            echo "<script>errorNotification('Un error inesperado ha sucedido. Por favor, vuelva a intentarlo más tarde.')</script>";
        }
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
    if(strlen($pass) >= 8 && preg_match('/[A-Z]/', $pass) && preg_match('/[a-z]/', $pass) && preg_match('/[0-9]/', $pass)){
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

function getCountryId($pais ,$listapaises){
    foreach ($listapaises as $datapais) {
        if ($datapais['country_name'] == $pais){
            return $datapais['country_id'];
        }
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
    <script src="register.js"></script>
    <script src="/componentes/notificationHandler.js"></script>
</head>
<body id="crear-cuenta">
    <main>
        <?php
    echo "<input type='hidden' name='countries' id='jsoncountry' value='" . json_encode($country_names, JSON_UNESCAPED_UNICODE) . "'>";
    ?>
    <a href="index.php" class="backhome">Volver a Inicio</a>

    <h1>Crear una cuenta</h1>



    </main>

    <ul id="notification__list">
        <!-- todas las notificaciones -->
    </ul>

    <script src="componentes/notificationHandler.js"></script>
</body>
</html>
