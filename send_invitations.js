$(document).ready(function(){
    $("button.share-button").click(function(e){
        e.preventDefault();
        $("#modal-share").show();
        $("#overlay").show();
    });

    $(".close, #overlay").click(function(){
        $("#modal-share").hide();
        $("#overlay").hide();
    });
});