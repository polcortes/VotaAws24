<?php
try {
    $logFilePath = "logs/log" . date("d-m-Y") . ".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }
    $filePathParts = explode("/", __FILE__);
} catch (Exception $e) {
    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
}

try {
    require "./data/dbAccess.php";
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
    $queryCountry = $pdo->prepare("SELECT country_name,tel_prefix,country_id FROM Country");
    $queryCountry->execute();
    $countryOptions = $queryCountry->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― DB ERROR]: Error al conectarse a la base de datos. ERROR: " . $e . "\n";
    file_put_contents($logFilePath, $logTxt, FILE_APPEND);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse | Vota!</title>
    <meta name="description"
        content="Página para registrarse en nuestra web. ¡Crea una cuenta y podrás participar y generar encuestas para todo el mundo!">
    <link rel="stylesheet" href="styles.css?<?php echo date('Y-m-d_H:i:s'); ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="componentes/notificationHandler.js"></script>
</head>

<body id="register">
    <?php include_once("common/header.php"); ?>
    <ul id="notification__list"></ul>
    <main>
        <?php echo "<input type='hidden' name='countries' id='jsoncountry' value='" . json_encode($countryOptions, JSON_UNESCAPED_UNICODE) . "'>"; ?>
        <h1>Crear una cuenta</h1>
    </main>
    <div class="footer">
        <?php include_once("common/footer.php") ?>
    </div>
    <script src="register.js"></script>
</body>

</html>
<?php
try {

    $queryUserMail = $pdo->prepare("SELECT user_mail, customer_name FROM User");
    $quaryUserTel = $pdo->prepare("SELECT user_tel FROM User");

    $queryUserMail->execute();
    $quaryUserTel->execute();

    $resultUserMail = $queryUserMail->fetchAll(PDO::FETCH_ASSOC);
    $resultUserTel = $quaryUserTel->fetchAll(PDO::FETCH_ASSOC);

    $correxistente = $resultUserMail;
    $telsexistente = $resultUserTel;

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

        $idpais = getCountryId($pais, $countryOptions);


        if (!validarNombre($nombre)) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: El nombre solo puede contener letras mayúsculas y minúsculas.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('El nombre solo puede contener letras mayúsculas y minúsculas.')</script>";

        } else if (!validarEmail($email)) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: El correo no tiene un formato válido.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('El correo no tiene un formato válido');</script>";

        } elseif (!validarPass($pass)) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: La contraseña no tiene un formato válido. Debe contener al menos 8 carácteres, al menos 1 letra mínusculas y mayuscula y al menos un número.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('La contraseña no tiene un formato válido. Debe contener al menos 8 carácteres, al menos 1 letra mínusculas y mayuscula y al menos un número.')</script>";

        } else if ($pass != $passcheck) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: Las contraseñas no coinciden.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('Las contraseñas no coinciden.')</script>";

        } else if (!validarPais($pais, $countryOptions)) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: El páis no está en la lista de paises.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('El páis no está en la lista de paises.')</script>";

        } else if (strlen($telefonOK) !== 9) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: El teléfono no es válido.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('El teléfono no es válido.')</script>";

        } else if (!validarPrefix($prefix)) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: El prefijo del teléfono no es válido.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('El prefijo del teléfono no es válido.')</script>";

        } else if (!validarNombre($ciudad)) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: La ciudad que has puesto no es válida.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('La ciudad que has puesto no es válida.')</script>";

        } else if (strlen($cp) !== 5) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: El código postal no es válido.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('El código postal no es válido.')</script>";

        } else if (!emailRepetido($email, $correxistente)) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: El email ya existe en nuestra base de datos.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('El email ya existe en nuestra base de datos.')</script>";

        } elseif (!telRepetido($telefonOK, $telsexistente)) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: El teléfono ya existe en nuestra base de datos.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('El teléfono ya existe en nuestra base de datos.')</script>";

        } elseif (is_null($idpais)) {
            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Register fail]: País no válido.\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "<script>errorNotification('País no válido.')</script>";

        } else {
            $passhash = hash('sha512', $pass);

            $query = $pdo->prepare("SELECT user_mail,customer_name FROM User");
            $query->execute();

            $new_register = true;

            while ($row = $query->fetch()) {
                if ($email == $row[0] && $row[1] == "") {
                    $new_register = false;
                    break;
                }
            }

            $token = bin2hex(random_bytes(50));

            if ($new_register) {
                $sql_insert = "INSERT INTO User (customer_name, user_mail, user_country_id, user_city, user_cp, user_tel, user_tel_prefix, user_pass, token, encryptString) VALUES (:nombre, :email, :paisid, :ciudad, :cp, :tel, :prefix, :pass, :token, :encryptString)";
                $stmt_insert = $pdo->prepare($sql_insert);
                $stmt_insert->bindParam(':nombre', $nombre);
                $stmt_insert->bindParam(':email', $email);
                $stmt_insert->bindParam(':pass', $passhash);
                $stmt_insert->bindParam(':tel', $telefonOK);
                $stmt_insert->bindParam(':prefix', $prefix);
                $stmt_insert->bindParam(':ciudad', $ciudad);
                $stmt_insert->bindParam(':paisid', $idpais);
                $stmt_insert->bindParam(':cp', $cp);
                $stmt_insert->bindParam(':encryptString', bin2hex(random_bytes(50)));
                $stmt_insert->bindParam(':token', $token);

                $stmt_insert->execute();

                $query = $pdo->prepare("SELECT user_id FROM User WHERE user_mail = :email");

                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->execute();

                $row = $query->fetch();
            } else {
                $sql_update = "UPDATE User SET customer_name = :nombre, user_country_id = :paisid, user_city = :ciudad, user_cp = :cp, user_tel = :tel, user_tel_prefix = :prefix, user_pass = :pass, encryptString = :token WHERE user_mail = :email";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->bindParam(':nombre', $nombre);
                $stmt_update->bindParam(':email', $email);
                $stmt_update->bindParam(':pass', $passhash);
                $stmt_update->bindParam(':tel', $telefonOK);
                $stmt_update->bindParam(':prefix', $prefix);
                $stmt_update->bindParam(':ciudad', $ciudad);
                $stmt_update->bindParam(':paisid', $idpais);
                $stmt_update->bindParam(':cp', $cp);
                $stmt_update->bindParam(':token', $token);

                $stmt_update->execute();
            }

            $query = $pdo->prepare("SELECT user_id FROM User WHERE user_mail = :email");

            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch();

            if ($row) {
                $_SESSION["usuario"] = $row["user_id"];
                $_SESSION['nombre'] = $nombre;
            }

            $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― REGISTER COMPLEAT]: Se ha registrado un usuario con correo '$email'\n";
            file_put_contents($logFilePath, $logTxt, FILE_APPEND);
            echo "
                <script>
                    successfulNotification('¡Registro completado! Por favor, verifica tu correo para activar tu cuenta.');
                    window.location.href = 'mail_verification.php';
                </script>
            ";
        }
    }
} catch (PDOException $e) {
    $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― Un error inesperado ha sucedido. Por favor, vuelva a intentarlo más tarde: " . $e->getMessage() . "\n";
    file_put_contents($logFilePath, $logTxt, FILE_APPEND);
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
        if ($mail['customer_name'] != "") {
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