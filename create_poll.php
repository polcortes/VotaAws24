<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Encuesta</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
<?php
include_once("common/header.php")
?>
    <main id="createPoll">
        <h1>Crear Encuesta</h1>

        <form action="process_poll.php" method="post">
            <input type="text" id="question" name="question" placeholder="Pregunta de la encuesta" required>
            <br>
            <div id="optionsContainer">
                <input type="text" name="options[]" placeholder="Respuesta" required>
                <input type="text" name="options[]" placeholder="Respuesta" required>
            </div>
            <button type="button" onclick="addOption()"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                    <path d="M13.5 6.5l4 4" />
                    <path d="M16 19h6" />
                    <path d="M19 16v6" />
                </svg>  Añadir respuesta</button>
            <br>
            <label for="start_date">Fecha de Inicio:</label>
            <input type="date" id="start_date" name="start_date" required>

            <label for="end_date">Fecha de Finalización:</label>
            <input type="date" id="end_date" name="end_date" required>
            <br>
            <button type="submit">Crear Encuesta</button>
        </form>
    </main>
    <?php
include_once("common/footer.php")
?>
    <script>
        function addOption() {
            const optionsContainer = document.getElementById('optionsContainer');
            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.name = 'options[]';
            newInput.placeholder = 'Respuesta';
            newInput.required = true;
            optionsContainer.appendChild(newInput);
        }
    </script>
</body>

</html>