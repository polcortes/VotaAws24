<?php
    include 'data/dbAccess.php';

try {
    $conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT country_name,tel_prefix,country_id FROM Country";
    $sql2 = "SELECT user_mail, customer_name FROM User";
    $sql3 = "SELECT user_tel FROM User";
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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $nombre = $_POST['register_name'];
        $email = $_POST['register_email'];
        $pass = $_POST['register_pass'];
        $passcheck = $_POST['register_repeat_pass'];
        $pais = $_POST['register_pais'];
        $tel = $_POST['register_tel'];
        $prefix = $_POST['register_prefix'];
        $ciudad = $_POST['register_ciudad'];
        $cp = intval($_POST['register_cp']);
        $telefonOK = intval(preg_replace("/[^0-9]/", "", $tel));

        $idpais = getCountryId($pais, $country_names);


        if (!validarNombre($nombre)) {
            echo "<script>errorNotification('El nombre solo puede contener letras mayúsculas y minúsculas.')</script>";
        } else if (!validarEmail($email)) {
            echo "<script>errorNotification('El correo no tiene un formato válido');</script>";
        } elseif (!validarPass($pass)) {
            echo "<script>errorNotification('La contraseña no tiene un formato válido. Debe contener al menos 8 carácteres, al menos 1 letra mínusculas y mayuscula y al menos un número.')</script>";
        } else if ($pass != $passcheck) {
            echo "<script>errorNotification('Las contraseñas no coinciden.')</script>";
        } else if (!validarPais($pais, $country_names)) {
            echo "<script>errorNotification('El páis no está en la lista de paises.')</script>";
        } else if (strlen($telefonOK) !== 9) {
            echo "<script>errorNotification('El teléfono no es válido.')</script>";
        } else if (!validarPrefix($prefix)) {
            echo "<script>errorNotification('El prefijo del teléfono no es válido.')</script>";
        } else if (!validarNombre($ciudad)) {
            echo "<script>errorNotification('La ciudad que has puesto no es válida.')</script>";
        } else if (strlen($cp) !== 5) {
            echo "<script>errorNotification('El código postal no es válido.')</script>";
        } else if (!emailRepetido($email, $correxistente)) {
            echo "<script>errorNotification('El email ya existe en nuestra base de datos.')</script>";
        } elseif (!telRepetido($telefonOK, $telsexistente)) {
            echo "<script>errorNotification('El teléfono ya existe en nuestra base de datos.')</script>";
        } elseif (is_null($idpais)) {
            echo "<script>errorNotification('País no válido.')</script>";
        } else {
            $conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
            echo "conetado";
        
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $passhash = hash('sha512', $pass);
            
            $query = $conn->prepare("SELECT user_mail,customer_name FROM User");
            $query->execute();

            $new_register = 0;
            
            while ($row = $query->fetch()) {
                if($email == $row[0] && $row[1]==""){
                    $new_register = false;
                    break;
                }
            }
            if($new_register){
                $sql_insert = "INSERT INTO User (customer_name, user_mail, user_country_id, user_city, user_cp, user_tel, user_tel_prefix, user_pass) VALUES (:nombre, :email, :paisid, :ciudad, :cp, :tel, :prefix, :pass )";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bindParam(':nombre', $nombre);
                $stmt_insert->bindParam(':email', $email);
                $stmt_insert->bindParam(':pass', $passhash);
                $stmt_insert->bindParam(':tel', $telefonOK);
                $stmt_insert->bindParam(':prefix', $prefix);
                $stmt_insert->bindParam(':ciudad', $ciudad);
                $stmt_insert->bindParam(':paisid', $idpais);
                $stmt_insert->bindParam(':cp', $cp);
                // $stmt_insert->bindParam(':token', $token);
    
                $stmt_insert->execute();
                
                $query = $conn->prepare("SELECT user_id FROM User WHERE user_mail = :email");
                
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->execute();
                
                $row = $query->fetch();
                $conn = null;
            }else{
                $sql_update = "UPDATE User SET customer_name = :nombre, user_country_id = :paisid, user_city = :ciudad, user_cp = :cp, user_tel = :tel, user_tel_prefix = :prefix, user_pass = :pass WHERE user_mail = :email";
                $stmt_insert = $conn->prepare($sql_update);
            $stmt_insert->bindParam(':nombre', $nombre);
            $stmt_insert->bindParam(':email', $email);
            $stmt_insert->bindParam(':pass', $passhash);
            $stmt_insert->bindParam(':tel', $telefonOK);
            $stmt_insert->bindParam(':prefix', $prefix);
            $stmt_insert->bindParam(':ciudad', $ciudad);
            $stmt_insert->bindParam(':paisid', $idpais);
            $stmt_insert->bindParam(':cp', $cp);
            // $stmt_insert->bindParam(':token', $token);

            $stmt_insert->execute();
            
            $query = $conn->prepare("SELECT user_id FROM User WHERE user_mail = :email");
            
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();
            
            $row = $query->fetch();
            $conn = null;
            }
            
            if ($row) {
                // session_start();
                // $_SESSION["usuario"] = $row["user_id"];
                // $_SESSION['nombre'] = $nombre;
                // header("Location: dashboard.php?succ=1");
                // exit();
            } else {
                echo "ha habido un error";
                echo "<script>errorNotification('Un error inesperado ha sucedido. Por favor, vuelva a intentarlo más tarde.')</script>";
            }
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    echo "<script>errorNotification('Un error inesperado ha sucedido. Por favor, vuelva a intentarlo más tarde.')</script>";
}

function validarNombre($nombre)
{
    $regex = '/^[A-Za-z\s]+$/';

    if (preg_match($regex, $nombre)) {
        return true;
    } else {
        return false;
    }
}

function validarEmail($email)
{
    $regex = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';

    if (preg_match($regex, $email)) {
        return true;
    } else {
        return false;
    }
}

function validarPass($pass)
{
    if (strlen($pass) >= 8 && preg_match('/[A-Z]/', $pass) && preg_match('/[a-z]/', $pass) && preg_match('/[0-9]/', $pass)) {
        return true;
    } else {
        return false;
    }
}

function validarPais($pais, $listapaises)
{
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

function emailRepetido($email, $listamails)
{
    $mails = array();
    foreach ($listamails as $mail) {
        if($mail['customer_name']!=""){
            $mails[] = $mail['user_mail'];
        }
    }
    if (in_array($email, $mails)) {
        return false;
    } else {
        return true;
    }
}

function telRepetido($telefono, $listatelefonos)
{
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

function validarPrefix($prefix)
{
    $regex = '/^\+\d{1,3}$/';
    if (!preg_match($regex, $prefix)) {
        return false;
    } else {
        return true;
    }
}

function getCountryId($pais, $listapaises)
{
    foreach ($listapaises as $datapais) {
        if ($datapais['country_name'] == $pais) {
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
    <meta name="description"
        content="Página para registrarse en nuestra web. ¡Crea una cuenta y podrás participar y generar encuestas para todo el mundo!">
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