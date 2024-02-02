$(function(){
    createQuestion();
    var numeroinput = 2;
    $("body").on('focusout', '#question', function(){
        var input = $(this).val();
        checkQuest(input);
    });

    $('body').on('focusout', '#quest', function(){
        checkAns();
    });
    
    $('body').on('input', '#question', function(){
        deleteNext($(this))
    });


    $('body').on('input', '#quest', function(){
        deleteNext($('#deleteButton'))
    });

    $('body').on('change', '#start_date', function(){
        fillDate();
        checkDate();
    });

    $('body').on('change', '#end_date', function(){
        fillDate();
        checkDate();
    });

    $('body').on('input', '#start_date', function(){
        deleteNext($('#end_date'));
    });

    $('body').on('input', '#end_date', function(){
        deleteNext($('#end_date'));
    });

    $("body").on('click', '#addButton', function(){
        numeroinput++;
        addOption(numeroinput);
        deleteNext($('#deleteButton'))
    });

    $("body").on('click', '#deleteButton', function(){
        deleteOption();
        checkAns();
    });
    
    $("body").on('click', '#create', function(){
        checkDate();
    });


    $("body").on('keydown', 'input', function(event) {
        if (event.keyCode === 13 || event.keyCode === 9 ) { 
            event.preventDefault();
            $(this).blur();
            var borderBottomColor = $(this).css('border-bottom-color');

            if (borderBottomColor != 'rgb(255, 37, 37)') {
                var inputs = $('input');
                var currentIndex = inputs.index(this);
                var nextIndex = (currentIndex + 1) % inputs.length;
                inputs[nextIndex].focus();
            }
        }
        });
});

function createQuestion(){
    $('h1').after(`
    <form method="post" enctype="multipart/form-data">
    <input name="img_quest" type="file" class="imagen" accept="image/*">
    <input type="text" id="question" name="question" placeholder="Pregunta de la encuesta" required>
    <br>`);
}

function createOriginalAns(){
    if ($("#optionsContainer").length === 0) {
    $('#question').after(`
    <div id="optionsContainer">
         <input type="text" name="options[1]" id = 'quest' class="quest" placeholder="Respuesta" required>
         <input name="img_ans[1]" type="file" class="imagen" accept="image/*">
         <input type="text" name="options[2]" id = 'quest' class="quest" placeholder="Respuesta" required>
         <input name="img_ans[2]" type="file" class="imagen" accept="image/*">
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
    <label for="end_date">Fecha de Finalización:</label>
    <input type="datetime-local" id="end_date" name="end_date" required>
    <br>
    </div>
    `);
    }
}







function createPoll(){
    if ($("#create").length === 0) {
    $('#end_date').after(`
    <button type="submit" id="create">Crear Encuesta</button>
    </form>
    `)}
}






function checkQuest(question){
    if (question !== null && question.trim() !== '') {
        createOriginalAns();
    }
}


function fillDate(){
    var start = $('#start_date').val();
    var end = $('#end_date').val();

    if(start != "" && end != ""){
        createPoll()
    }
}

function checkDate(){
    var fechaInicio = new Date($('#start_date').val());
    var fechaFin = new Date($('#end_date').val());
    var hoy = new Date();
    if(fechaFin < fechaInicio){
        errorNotification('La fecha de final no puede ser menor a la de inicio ');
        deleteNext($('#end_date'));
        return false;
    }else if(fechaInicio < hoy){
        errorNotification('La fecha de inicio no puede ser menor a la actual ');
        deleteNext($('#end_date'));
        return false;
    }else{
        return true;
    }
}
function checkAns(){
    var arrayquest = [];
    var complete = true;
    $('.quest').each(function() {
        var valorInput = $(this).val();
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
    if(complete){
        createStartDate();
    }
}



function deleteNext(input){
    input.nextAll().remove()
}



function addOption(i){
    var optionsContainer = $('#optionsContainer');
    var optionInputs = optionsContainer.find('input[name="options[]"]');
    if(optionInputs.length < 100){
        optionsContainer.append(`
        <input type="text" name="options[`+i+`] " id = "quest" class="quest" placeholder="Respuesta" required>
        <input name="img_ans[` + i +`]" type="file" class="imagen" accept="image/*">
        `);
    } else {
        alertNotification('El numero maximo de preguntas es de cien')
    }
}

function deleteOption(){
    var optionsContainer = $('#optionsContainer');
    var optionInputs = optionsContainer.find('input[name^="options"]');
    var imginputs = optionsContainer.find('input[name^="img_ans"]');
    if(optionInputs.length > 2 && imginputs.length > 2){
        optionInputs.last().remove();
        imginputs.last().remove();
    } else {
        alertNotification('El numero minimo de preguntas es de dos')
    }
}