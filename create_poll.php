<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Encuesta</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

    <h1>Crear Encuesta</h1>

    <form action="process_poll.php" method="post">
        <label for="question">Pregunta:</label>
        <input type="text" id="question" name="question" required>

        <label for="options">Opciones de Respuesta (separadas por coma):</label>
        <input type="text" id="options" name="options" required>

        <label for="start_date">Fecha de Inicio:</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="end_date">Fecha de Finalización:</label>
        <input type="date" id="end_date" name="end_date" required>

        <button type="submit">Crear Encuesta</button>
    </form>

</body>
</html>
