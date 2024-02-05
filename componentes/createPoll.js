$(document).ready(function() {
    let optionNumber = 1;
    
    $('body main').append('<form method="post" enctype="multipart/form-data"></form>');
    $('form').append('<input type="text" id="question" name="question" placeholder="Pregunta de la encuesta" required>');
    $('form').append('<input type="file" id="surveyImage" name="surveyImage" accept="image/*">');
    

    $('#question').on('keyup', function() {
        if ($(this).val().length > 0 && $('.option').length === 0) {
            $('form').append('<div id="optionsContainer"></div>');
            createOption(optionNumber);
            createOption(optionNumber);
        }
    });

    $('#question').on('blur', function() {
        if ($(this).val().length === 0) {
            $('#question').nextAll().remove();
            optionNumber = 1;
        }
    });

    $(document).on('keyup', 'input.option', function() {
        if ($('#optionsContainer').children().length === 4) {
            const option1Value = $('input[name="options[1]"].option').val();
            const option2Value = $('input[name="options[2]"].option').val();
            
            if (option1Value.length === 0 || option2Value.length === 0) {
                $('#datePoll').remove();
                $('#butonsContainer').remove();
            } else if (option1Value.length > 0 && option2Value.length > 0) {
                if ($('#datePoll').length === 0) {
                    $('form').append('<div id="datePoll"></div>');
                    $('#datePoll').append('<div class="dateFormDiv"></div>')
                    $('#datePoll').append('<div class="dateFormDiv"></div>')
    
                    $('#datePoll > .dateFormDiv:first-child').append('<label for="start_date">Fecha de inicio</label>');
                    $('#datePoll > .dateFormDiv:first-child').append('<input type="datetime-local" id="start_date" name="start_date" required>');
                    $('#datePoll > .dateFormDiv:last-child').append('<label for="end_date">Fecha de Finalización:</label>');
                    $('#datePoll > .dateFormDiv:last-child').append('<input type="datetime-local" id="end_date" name="end_date" required>');
    
                    $('form').append('<div id="butonsContainer"></div>');
                    $('#butonsContainer').append(`
                        <button type="button" id="addButton">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4"></path>
                                <path d="M13.5 6.5l4 4"></path>
                                <path d="M16 19h6"></path>
                                <path d="M19 16v6"></path>
                            </svg>
                            Añadir respuesta
                        </button>
                    `);
                    $('#butonsContainer').append(`
                        <button type="button" id="deleteButton">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eraser" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3"></path>
                                <path d="M18 13.3l-6.3 -6.3"></path>
                            </svg>
                            Eliminar última respuesta
                        </button>
                    `);
                }
            }
        }
        
    });

    $(document).on('click', '#addButton', function() {
        if (optionNumber <= 100) {
            createOption(optionNumber);
        } else {
            $('#addButton').prop('disabled', true);
            alertNotification("No puedes crear más de 100 respuestas");
        }
    });

    $(document).on('click', '#deleteButton', function() {
        if (optionNumber > 2) {
            $('#optionsContainer input.option:last').remove();
            $('#optionsContainer input.image:last').remove();
            optionNumber--;
        } else {
            alertNotification("Una encuesta como mínimo debe contener 2 respuestas");
        }
    });

    $(document).on('click', '#create', function() {
        checkDates();
        if ($('.option').length >= 2) {
            if ($('#question').val().length > 0) {
                var allOptionsFilled = true;
                $('.option').each(function() {
                    if ($(this).val().length === 0) {
                        allOptionsFilled = false;
                        alertNotification("Debes rellenar todas las respuestas");
                        return false; // Esto detiene el bucle .each
                    }
                });
                if (allOptionsFilled) {
                    $('form').submit();
                }
            }
        }
    });


    $(document).on('change', '#start_date', checkDates);
    $(document).on('change', '#end_date', checkDates);


    $(document).on('change', 'input[type="file"]', function() {
        var file = this.files[0];

        if(file.size > 50000) {
            alertNotification("El archivo seleccionado supera el tamaño máximo permitido (50kB).");
            this.value = '';
        }
    });

    function checkDates() {
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());
        const now = new Date();
        if (startDate < now) {
            alertNotification("La fecha de inicio no puede ser anterior a la fecha y hora actuales");
        } else if (endDate <= startDate) {
            alertNotification("La fecha final no puede ser anterior o igual que la fecha de inicio");
        } else if (optionNumber >= 2) {
            if ($('#create').length === 0) {
                $('form').append('<input type="submit" id="create" value="Crear encuesta">');
            }
        }
    }

    function createOption(optionNum) {
        $('#optionsContainer').append('<input type="text" name="options[' + optionNum + ']" class="option" placeholder="Respuesta" required>');
        $('#optionsContainer').append('<input type="file" name="images[' + optionNum + ']" class="image" accept="image/*">');
        optionNumber++;
    }
});