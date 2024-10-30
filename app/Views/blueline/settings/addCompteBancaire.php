<?php   
    $attributes = array('class' => '', 'id' => '_company', 'autocomplete' => 'off');
    echo form_open_multipart($form_action, $attributes); 
?>
<div class="form-group">
    <label for="nom"><?=$this->lang->line('application_Name');?> *</label>
    <input id="nom" type="text" name="nom" class="required form-control" value="<?php if(isset($data)){echo $data->nom;} ?>" required/>
</div>
<div class="form-group">
    <label for="RIB"><?=$this->lang->line('application_RIB');?> *</label>
    <input id="RIB" type="number" name="RIB" class="required form-control" onblur="javascript:MaxLengthTextarea(this, 20);" onkeyup="Verify(this, 20)" value="<?php if(isset($data)){echo $data->RIB;} ?>" required/>
</div>

 <!--Number RIB -->
<div class="numberRIB"   style="display:none">
	<label style="color: red ;">RIB contient uniquement 20 chiffres, Veuillez vérifier</label> <br>
</div>

<div class="form-group">
    <label for="BIC">BIC</label>
    <input id="BIC" type="text" name="BIC" class="form-control" value="<?php if(isset($data)){echo $data->BIC;} ?>"/>
</div>
<div class="form-group">
    <label for="IBAN">IBAN</label>
    <input id="IBAN" type="text" name="IBAN" class="form-control" value="<?php if(isset($data)){echo $data->IBAN;} ?>"/>
</div>
<div class="form-group">
    <label for="adr_banque"><?=$this->lang->line('application_adress_banque');?> </label>
    <input id="adr_banque" type="text" name="adr_banque" class=" form-control" value="<?php if(isset($data)){echo $data->adr_banque;} ?>"/>
</div>
<div class="form-group" style="padding: 20px 9px;">
	<span><?=$this->lang->line('application_default_compte_bancaire');?></span><br><br>
	<label class="switch" >
		<?php if(($settings->comptebancaire == $data->id) && isset($data)){?>
		<input type="checkbox" name="default_compteBancaire" id="default_compteBancaire" checked>
		<?php	}else{ ?>  
		<input type="checkbox" name="default_compteBancaire" id="default_compteBancaire">
		<?php  } ?>
	<div class="slider round"></div>
	</label>
</div>
<div class="modal-footer">
    <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
    <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
 function MaxLengthTextarea(objettextarea,maxlength){	
  if (objettextarea.value.length != maxlength ) {
	$( "div.numberRIB" ).show();
	$('input[type=submit]').prop('disabled',true);
    objettextarea.value = objettextarea.value.substring(0, maxlength);
   } else {
	   $( "div.numberRIB" ).hide();
		$('input[type=submit]').prop('disabled',false);
   }
}
 function Verify(objettextarea,maxlength){	
  if (objettextarea.value.length > maxlength ) {
    objettextarea.value = objettextarea.value.substring(0, maxlength);
   }
}
</script>