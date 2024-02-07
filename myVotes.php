<?php
try {
    $logFilePath = "logs/log" . date("d-m-Y") . ".txt";
    if (!file_exists(dirname($logFilePath))) {
        mkdir(dirname($logFilePath), 0755, true);
    }
    $filePathParts = explode("/", __FILE__);
} catch (Exception $e) {
    echo "<script>errorNotification('ERROR al crear el archivo de log -> " . $e->getMessage() . "')</script>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Votos | ¡Vota!</title>
    <meta name="description" content="Verifica tu dirección de email para acceder a 'Vota!'">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="shortcut icon" href="/icons/faviconDark.svg" type="image/svg">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/svg" media="(prefers-color-scheme: light)">
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="/componentes/notificationHandler.js"></script>
</head>

<body id="conditions">
    <?php
    include_once("common/header.php");
    if (!isset($_SESSION["usuario"])) {
        header("HTTP/1.1 403 Forbidden");
        exit();
    }

    try {
        require 'data/dbAccess.php';
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

        $query = $pdo->prepare("SELECT * FROM User WHERE user_id = :id");
        $query->execute([':id' => $_SESSION["usuario"]]);
        $row = $query->fetch();

        $query = $pdo->prepare("select `i`.`mail_to`, `i`.`survey_id`, `v`.`option_id`
        from `UserVote` `v`, `Invitation` `i`
        where `i`.`mail_to` = (select `user_mail` from `User` where `user_id` = 1)
        and `v`.`invitation_id_enc` = aes_encrypt(concat(convert(2, char), @decryptString), @pass);");

        if ($row && $row['is_mail_valid'] && $row['conditions_accepted']) {
            header("Location: dashboard.php");
            exit();
        }
    } catch (PDOException $e) {
        $logTxt = "\n[" . end($filePathParts) . " ― " . date('H:i:s') . " ― ERROR db connect]: ERROR al conectarse con la base de datos -> " . $e->getMessage() . "\n";
        file_put_contents($logFilePath, $logTxt, FILE_APPEND);
        echo "<script>errorNotification('ERROR al conectarse con la base de datos')</script>";
    }
    ?>
    <main>
        <h1>Mis Votos</h1>

    </main>

    <ul id="notification__list"></ul>
    <div class="footer">
        <?php include_once("common/footer.php") ?>
    </div>
</body>

</html>