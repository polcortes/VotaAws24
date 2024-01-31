$(document).ready(function(){
    $("button.share-button").click(function(e){
        e.preventDefault();
        $("#modal-share").show();
        $("#overlay").show();
    });

    // Cuando se hace clic en el botón de cerrar (x) o fuera del modal, se cierra el modal y la superposición
    $(".close, #overlay").click(function(){
        $("#modal-share").hide();
        $("#overlay").hide();
    });
});