$(document).ready(function(){

    $("button.share-button").click(function(e){
        e.preventDefault();
        var surveyId = $(this).data('survey-id');
        $('#survey_id').val(surveyId);
        $('#modal-share').show();
        $("#overlay").show();
    });

    $(".close, #overlay").click(function(){
        $("#modal-share").hide();
        $("#overlay").hide();
    });
});