$(document).ready(function(){

    $("button.share-button").click(function(e){
        e.preventDefault();
        var surveyId = $(this).data('survey-id');
        $('#survey_id').val(surveyId);

        // Centrar el modal en la pantalla
        var modal = $('#modal-share');
        var overlay = $("#overlay");

        var topPosition = Math.max(0, (($(window).height() - modal.outerHeight()) / 2) + $(window).scrollTop());
        // el valor -420 se ha obtenido a base de prueba y error para centrar el modal en la pantalla
        // no sabemos por qué es necesario restar 420, pero se ve centrado en cualquier pantalla
        var leftPosition = Math.max(0, (($(window).width() - modal.outerWidth()) / 2) - 420);

        modal.css({
            'top': topPosition + 'px',
            'left': leftPosition + 'px',
            'display': 'block'
        });

        overlay.show();
    });

    $(".close, #overlay").click(function(){
        $("#modal-share").hide();
        $("#overlay").hide();
    });
});

