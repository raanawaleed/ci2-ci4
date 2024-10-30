jQuery(function($){

    function validate(tag,type,message){
        if(type=='error'){
            tag.next().text(message).addClass('color-red');
            tag.addClass('border-red').removeClass('border-green');
        }else{
            tag.next().text('').removeClass('color-red');
            tag.addClass('border-green').removeClass('border-red');
        }
    }
    $("#op").on("blur", function(){
        var value = $(this).val().trim();
        if(value==''){
            validate($(this), "error", "Champ obligatoire");
        }else{
            validate($(this), "success", "");
        }
    })
    $("#Prenom").on("blur", function(){
        var value = $(this).val().trim();
        if(value==''){
            validate($(this), "error", "Champ obligatoire");
        }else{
            validate($(this), "success", "");
        }
    })
    $("#datenaiss").on("blur", function(){
        var value = $(this).val().trim();
        if(value==''){
            validate($(this), "error", "Champ obligatoire");
        }else{
            validate($(this), "success", "");
        }
    })
    $("#date_debut_embauche").on("blur", function(){
        var value = $(this).val().trim();
        if(value==''){
            validate($(this), "error", "Champ obligatoire");
        }else{
            validate($(this), "success", "");
        }
    })
    $(document.body).on('blur', '#type_contart_chosen', function(){
        if(!$('#verif-type-contrat #type_contart').val()){
            validate($("#help-block-contrat"), "error", "Champ obligatoire");
        }else{
            validate($("#help-block-contrat"), "success", "");
        }
    })
    $(document.body).on('blur', '#Situation_chosen', function(){
        if(!$('#type_famille #Situation').val()){
            validate($("#help-block-familiale"), "error", "Champ obligatoire");
        }else{
            validate($("#help-block-familiale"), "success", "");
        }
    })
    $("#submit-rh").on("click", function(e) {
        e.preventDefault();
        var errors = 0;
        if($("#op").val().trim()==''){
            validate($("#op"), "error", "Champ obligatoire");
            errors++;
        }else{
            validate($("#op"), "success", "");
        }
        if($("#Prenom").val().trim()==''){
            validate($("#Prenom"), "error", "Champ obligatoire");
            errors++;
        }else{
            validate($("#Prenom"), "success", "");
        }
        if($("#datenaiss").val().trim()==''){
            validate($("#datenaiss"), "error", "Champ obligatoire");
            errors++;
        }else{
            validate($("#datenaiss"), "success", "");
        }
        if($("#date_debut_embauche").val().trim()==''){
            validate($("#date_debut_embauche"), "error", "Champ obligatoire");
            errors++;
        }else{
            validate($("#date_debut_embauche"), "success", "");
        }
        if(!$('#verif-type-contrat #type_contart').val()){
            validate($("#help-block-contrat"), "error", "Champ obligatoire");
            errors++;
        }else{
            validate($("#help-block-contrat"), "success", "");
        }
        if(!$('#type_famille #Situation').val()){
            validate($("#help-block-familiale"), "error", "Champ obligatoire");
            errors++;
        }else{
            validate($("#help-block-familiale"), "success", "");
        }
        if (errors!=0){
            return false;
        }else{
            $("#_company").submit();
        }
    })
});
