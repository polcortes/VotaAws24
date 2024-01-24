<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio | Vota!</title>
    <meta name="description" content="Página de inicio de nuestra aplicación web 'Vota!', en la que podrás crear encuestas disponibles en todo el mundo en tan solo un par de clics!">
    <meta name="keywords" content="encuestas, votos, votar, votas, votad, ieti, worldwide">
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
            <a href="#maintext" class="ancoreto"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-badge-down-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.375 6.22l-4.375 3.498l-4.375 -3.5a1 1 0 0 0 -1.625 .782v6a1 1 0 0 0 .375 .78l5 4a1 1 0 0 0 1.25 0l5 -4a1 1 0 0 0 .375 -.78v-6a1 1 0 0 0 -1.625 -.78z" stroke-width="0" fill="currentColor" /></svg></a>
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