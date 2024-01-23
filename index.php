<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="main">
    <?php
    include_once("common/header.php")
    ?>
    <div class="bodyimg">
        <div class="bodytext">
            <div class="title">
                <h1>VOTA</h1>
                <span class="subtitle">Tu pagina de confianza para votar</span>
            </div>
        </div>
        <div class="godown">
            <a href="#maintext" class="ancoreto">&#11167;</a>
        </div>
    </div>
    <div class="maintext" id="maintext">
        <p>En este proyecto, desarrollaremos un portal destinado a organizaciones que requieren procesos de votación, especialmente, aunque no exclusivamente, dirigido a partidos políticos, ayuntamientos, instituciones, claustros, entre otros.</p>
        <h3>Un usuario registrado tendrá la capacidad de:</h3>
        <ul>
            <li>Administrar encuestas:</li>
            <ul>
                <li>Crear nuevas encuestas.</li>
                <li>Invitar a votantes.</li>
                <li>Supervisar la publicación de enunciados y resultados.</li>
                <li>Bloquear votaciones.</li>
                <li>Crear agendas para votantes.</li>
                <li>Visualizar las encuestas en las que ha sido invitado como votante y las votaciones pendientes.</li>
            </ul>
        </ul>
        <h3>Usuario anónimo</h3>
        <p>Los usuarios anónimos podrán participar en votaciones mediante un enlace de votación que recibirán por correo electrónico. Si deciden registrarse más adelante con esa dirección de correo electrónico, podrán acceder a sus votaciones anteriores.</p>
    </div>
    </div>
    <?php
    include_once("common/footer.php")
    ?>
</body>

</html>