<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    include_once("errors/error403.php");
    header("HTTP/1.1 403 Forbidden");
    exit();
}

if (isset($_POST["actual-pass"]) && isset($_POST["new-pass"]) && isset($_POST["repeat-new-pass"])) {
    require 'data/dbAccess.php';
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

    $query = $pdo->prepare("SELECT user_pass FROM User WHERE user_id = :user_id;");
    $query->execute([":user_id" => $_SESSION["usuario"]]);
    
    $pass = $query->fetch()["user_pass"];

    $isPassUpdated = false;

    if (hash("sha512", $_POST["actual-pass"]) === $pass) {
        // sigue el codigo...
        $decryptStringQuery = $pdo->prepare("SELECT encryptString FROM User WHERE user_id = :user_id;");
        $decryptStringQuery->execute([":user_id" => $_SESSION["usuario"]]);
        $encryptedString = $decryptStringQuery->fetch();
        
        $decryptString = openssl_decrypt($encryptedString["encryptString"], "AES-256-CTR", $_POST["actual-pass"]);

        $query = $pdo->prepare(
            "SELECT `i`.`mail_to`, `i`.`survey_id`, `v`.`option_id`, `i`.`invitation_id`
            FROM UserVote v
            INNER JOIN Invitation i ON `v`.`invitation_id_enc` = aes_encrypt(concat(convert(`i`.`invitation_id`, char), :decryptString), :pass)
            WHERE `i`.`mail_to` = (SELECT user_mail FROM User WHERE user_id = :user_id);"
        );
        $query->execute([":user_id" => $_SESSION["usuario"], ":decryptString" => $decryptString, ":pass" => $_POST["actual-pass"]]);

        $rows = $query->fetchAll();

        foreach ($rows as $row) {
            $insert = $pdo->prepare("INSERT INTO UserVote (invitation_id_enc, option_id) VALUES (:enc_id, :option_id);");
            $insert->execute([
                ":enc_id"    => openssl_encrypt($row["invitation_id"].$decryptString, "AES-256-CTR", $_POST["new-pass"]),
                ":option_id" => $row["option_id"]
            ]);
        }

        $update = $pdo->prepare("UPDATE User SET user_pass = sha2(:pass, 512);");
        $update->execute([":pass" => $_POST["new-pass"]]);

        $isPassUpdated = true;
        // echo "<h1>".print_r($rows, true)."</h1>";
        
    } else {

    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña | Vota!</title>
    <link rel="stylesheet" href="styles.css?<?php echo date('Y-m-d_H:i:s'); ?>">
    <script src="https://code.jquery.com/jquery-3.7.1.js"
            integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="change_pass.js"></script>
    <script src="/componentes/notificationHandler.js"></script>
</head>
<body id="change-pass">
    <?php include("common/header.php") ?>
    <ul id="notification__list"></ul>
    <main>
        <h1>Cambiar la contraseña:</h1>

        <form method="POST" id="change-pass-form">
            <label for="actual-pass">Contraseña actual:</label>
            <input type="password" name="actual-pass" id="actual-pass" placeholder="Contraseña..." required>

            <label for="new-pass">Contraseña nueva:</label>
            <input type="password" name="new-pass" id="new-pass" placeholder="Contraseña..." required>

            <label for="repeat-new-pass">Repite la contraseña nueva:</label>
            <input type="password" name="repeat-new-pass" id="repeat-new-pass" placeholder="Contraseña..." required>

            <input type="submit" value="Cambiar" disabled>
        </form>
    </main>
    <?php if (isset($isPassUpdated) and $isPassUpdated) {
        echo "<script>successfulNotification('La contraseña ha sido actualizada exitosamente.')</script>";
    } else if (isset($isPassUpdated) and !$isPassUpdated) {
        echo "<script>errorNotification('La contraseña actual que has escrito no coincide con la actual en la base de datos.')</script>";
    } ?>
    <?php include("common/footer.php") ?>
</body>
</html>