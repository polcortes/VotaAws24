<?php
try {
    session_start();
    
    require 'data/dbAccess.php';
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);

    $query = $pdo->prepare("SELECT * FROM User WHERE user_id = :id");
    $query->execute([':id' => $_SESSION["usuario"]]);
    $row = $query->fetch();
    // ['is_mail_valid'] && $row['conditions_accepted']

    if ($row) {
        $validUser = ($row['is_mail_valid'] && $row['conditions_accepted']);
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>
<nav class="navbar">
    <ul>
        <a href="/index.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 19h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v5.5" />
                <path d="M19 22v-6" />
                <path d="M22 19l-3 -3l-3 3" />
                <path d="M3 7l9 6l9 -6" />
            </svg></a>
        <li><a href="index.php">Inicio</a></li>
        <?php
        session_start();
        if (isset($_SESSION['usuario'])) {
            if ($validUser) {
                echo "<li><a href='dashboard.php'>DashBoard</a></li>";
                echo '<li><a href="create_poll.php">Crear Encuesta</a></li>';
                echo '<li><a href="logout.php">Cerrar Sesión</a></li>';
            } else {
                echo '<li><a href="mail_verification.php">Acaba de registrarte</a></li>';
                echo '<li><a href="logout.php">Cerrar Sesión</a></li>';
            }
        } else {
            echo '<li><a href="register.php">Registrarse</a></li>';
        }
        ?>
    </ul>
    <ul>
        <?php
        if (isset($_SESSION['usuario'])) {
            echo '<li>Bienvenido ' . $_SESSION['nombre'] . '</li>';
        }
        ?>
    </ul>
</nav>