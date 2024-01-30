$(function(){
    createQuestion();
    
    $("body").on('focusout', '#question', function(){
        var input = $(this).val();
        checkQuest(input);
    });

    $('body').on('focusout', '#quest', function(){
        checkAns();
    });

    $('body').on('focusout', '#start_date', function(){
        checkStartDate();
    });

    $("body").on('click', '#addButton', function(){
        addOption();
        deleteDate();
    });
    
    $("body").on('click', '#deleteButton', function(){
        deleteOption();
        checkAns();
    });

});

function createQuestion(){
    $('h1').after(`
    <form method="post">
    <input type="text" id="question" name="question" placeholder="Pregunta de la encuesta" required>
    <br>`);
}

function createOriginalAns(){
    if ($("#optionsContainer").length === 0) {
    $('#question').after(`
    <div id="optionsContainer">
         <input type="text" name="options[]" id = 'quest' class="quest" placeholder="Respuesta" required>
         <input type="text" name="options[]" id = 'quest' class="quest" placeholder="Respuesta" required>
     </div>
     <button type="button" id="addButton"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
     <path stroke="none" d="M0 0h24v24H0z" fill="none" />
     <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
     <path d="M13.5 6.5l4 4" />
     <path d="M16 19h6" />
     <path d="M19 16v6" />
     </svg>Añadir respuesta</button>
     <button type="button" id="deleteButton" ><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eraser" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3" /><path d="M18 13.3l-6.3 -6.3" /></svg>Eliminar última respuesta</button>
     <br>
    `);}
}

function createStartDate(){
    if ($("#datepoll").length === 0) {
    $('#deleteButton').after(`
    <div id="datepoll">
    <label for="start_date">Fecha de Inicio:</label>
    <input type="datetime-local" id="start_date" name="start_date" required>
    

    `);
    }
}

function createEndDate(){
    if ($("#datepoll").length === 0) {
        $('#deleteButton').after(`
    <label for="end_date">Fecha de Finalización:</label>
    <input type="datetime-local" id="end_date" name="end_date" required>
    <br>
    </div>
    `)};

}

function deleteDate(){
    if ($("#datepoll").length != 0) {
        $('#datepoll').remove();
    }

}

function createPoll(){
    if ($("#create").length === 0) {
    $('#end_date').after(`
    <button type="submit" id="create">Crear Encuesta</button>
    </form>
    `)}
}

function deletePoll(){
    if ($("#create").length !== 0) {
        $('#end_date').remove(`
        <button type="submit" id="create">Crear Encuesta</button>
        </form>
        `)}
}


function checkQuest(question){
    if (question !== null && question.trim() !== '') {
        createOriginalAns();
    }else{
        console.log('no');
    }
}

function checkStartDate(){
    var fechaEntrada = new Date($('#start_date').val());
    var fechaActual = new Date();
    $('#start_date').attr('min', minDate);
}

function checkAns(){
    var arrayquest = [];
    var complete = true;
    $('.quest').each(function() {
        var valorInput = $(this).val();
        console.log()
        if (valorInput !== null && valorInput.trim() !== '') {
            arrayquest.push(11)
        }else{
            arrayquest.push(0)
        }
    });
    arrayquest.forEach(function(element){
        if(element === 0){
            complete = false;
        }
    })
    console.log(complete)
    if(complete){
        createStartDate();
    }else{
        deleteDate();
        deletePoll();
    }
}

function checkinput(){

}

function addOption(){
    var optionsContainer = $('#optionsContainer');
    var optionInputs = optionsContainer.find('input[name="options[]"]');
    if(optionInputs.length < 100){
        optionsContainer.append('<input type="text" name="options[]" id = "quest" class="quest" placeholder="Respuesta" required>');
    } else {
        alertNotification('El numero maximo de preguntas es de cien')
    }
}

function deleteOption(){
    var optionsContainer = $('#optionsContainer');
    var optionInputs = optionsContainer.find('input[name="options[]"]');
    if(optionInputs.length > 2){
        optionInputs.last().remove();
    } else {
        alertNotification('El numero minimo de preguntas es de dos')
    }
}