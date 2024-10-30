<?php $attributes = array('role'=> 'form', 'id' => 'resetpwd'); ?>
<?=form_open('forgotpass/reset_password/'.$token, $attributes)?>



<!---  ------------------------------------ Start  reset password --------------------------------------------- -->
<div onloadstart="setBackgroundResetPasswordLayout();"  class="card card-outline-secondary  animated fadeInLeft col-md-6 " style="position:fixed;
    top: 25%;
    left: 25%; background: white ; padding: 20px;">
    <div class="card-header">
        <h3 class="mb-0" style="color: black">Réinitialiser le mot de passe</h3>
    </div>
    <div class="card-body ">
        <form class="form" role="form" autocomplete="off">
            <div class="form-group">
                <label for="password"><?= $this->lang->line('application_password'); ?></label>
                <input type="password" class="form-control" id="password" name="password"  oninput="disableResetButton()"
                       minlength="8" <?php echo 'required'; ?>/>
                <span class="form-text small text-muted" >
                                            <?php echo " (8 caractères minimun)"; ?>
                                        </span>

            </div>

            <div class="form-group">
                <label for="password2"><?=$this->lang->line('application_password_confirmed');?></label>
                <input type="password" oninput="disableResetButton()" class="form-control" id="password2" name="password2"   required="" style="height: min-content; " >
                <span class="form-text small text-muted" >
                                            <?= $this->lang->line('application_password_confirmed'); ?>
                                        </span>

            </div>

            <div class="reference" style="color:red;"></div>
            <input type="submit" onclick="myFunction2()" style="width: 100%; margin-bottom: 30px;" id="btnSubmit" class="btn btn-primary fadeoutOnClick" value="<?=$this->lang->line('application_reset_password');?>" />

        </form>
    </div>
</div>

<!---  ------------------------------------End reset password ----------------------------------------------------- -->


<?=form_close()?>

<script>
//test if pwd idantique or not
function setBackgroundResetPasswordLayout(){
    $(".sidebar-bg").hide();
    $(".main-footer").hide();
    $(".mainnavbar").hide();
    $("body").css("background-color", "#086A87");

}



function myFunction2() {


    let pwd1 = document.getElementById("password").value;
    let pwd2 = document.getElementById("password2").value;

    if (pwd1 !== pwd2 && pwd2 !== '') {
        document.getElementById("btnSubmit").disabled = true;
        $(".reference").html('<p>Mots de passe non identiques</p>');
        $(".reference").show();
        $("#password").val("");

        $("#password2").val("");
    } else {
        $(".reference").hide();



    }
}

	function disableResetButton(){

        let pwd1 = document.getElementById("password").value;
        let pwd2 = document.getElementById("password2").value;
        if ( pwd1 === pwd2 && document.getElementById("btnSubmit").disabled === true && pwd2 !== ''  ){
            document.getElementById("btnSubmit").disabled = false;
            $(".reference").html('');

        } else if (pwd1 === pwd2 && pwd2 !== ''){
            document.getElementById("btnSubmit").disabled = false;
            $(".reference").html('');
        }
    }


</script>
