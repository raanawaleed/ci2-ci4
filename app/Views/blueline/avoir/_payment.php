 <?php  
$attributes = array('class' => '', 'id' => '_partial');
echo form_open($form_action, $attributes); 
?>

<input id="avoir_id" type="hidden" name="avoir_id" value="<?=$avoir->id;?>" />
<?php if(isset($payment)){?>
<input id="id" type="hidden" name="id" value="<?=$payment->id;?>" />
<?php } ?>

<div class="form-group">
	<label for="value"><?=$this->lang->line('application_value');?> *</label>
	<input id="value"  onblur="Checkamount()" type="text" name="amount" class="required form-control number money-format"  value="<?php if(isset($payment)){ 
	echo display_money($payment->amount,"",$core_settings->chiffre); }else{ echo display_money($sumRest,"",$core_settings->chiffre); }?>" required/>
</div>

<!-- valeur -->
<div class="valueExceedOutsanding" style="display:none">
	<label for="textfield" style="color:red;"><?php echo $this->lang->line('application_value_Exceed_Outsanding'); ?> </label>
	 <br/>
</div>

<!-- date -->
<div class="form-group">
   <label for="date"><?=$this->lang->line('application_date');?> *</label>
   <input class="form-control datepicker" name="date" id="date" type="text" value="<?php if(isset($payment)){ echo $payment->date; }else{  echo date('Y-m-d', time()); }?>" data-date-format="yyyy-mm-dd" required/>
</div>

<!-- Moyen de paiement -->
<div class="form-group">
	<label for="typepayment">Moyen de paiement</label>

	<?php echo form_dropdown_ref('type', $typepaiement, $payment->type, 'style="width:100%" class="chosen-select" onchange="myFunction(this.value)"'); ?>   
</div>
<!-- Type == cheque-->
<div class="form-group chequeType" style="display:none">
	<label for="textfield">Numero chèque</label>
	<input id="num_cheque" type="text" name="num_cheque" value="" class=" form-control number money-format" />
</div>

<div class="form-group banqueCheque" style="display:none">
	<label for="textfield">Banque</label>
	<input id="banque_cheque" type="text" name="banque_cheque" value=""  class=" form-control number money-format" />
</div>

<!-- Type == virement-->
<div class="form-group virementBancaire" id ="virementBancaire" style="display:none">
	<label for="Compte Bancaire"><?=$this->lang->line('application_compte_bancaire');?></label><br>
	<select name="nomCompteBancaire" type="text" id="nomCompteBancaire" class="chosen-select" style="width:100%">
		<?php foreach($compteBancaire as $compteBancaire){ 
				if ($compteBancaire->visible == 1) { 
					if($core_settings->comptebancaire == $compteBancaire->nom){ ?>
					<option value="<?=$compteBancaire->nom?>" selected><?=$compteBancaire->nom?></option> 
					<?php } else { ?>
					<option value="<?=$compteBancaire->nom?>"><?=$compteBancaire->nom?></option>   
					<?php } ?>
		<?php } } ?>
	</select>      
</div>

<div class="form-group">
	<label for="textfield"><?=$this->lang->line('application_description');?></label>
	<textarea class="input-block-level form-control"  id="textfield" name="notes"><?php if(isset($payment)){echo $payment->notes;} ?></textarea>
</div>

<div class="modal-footer">
	<input type="submit" name="send" id="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>
<script>
function myFunction(a) {
	//cheque
	//$this->config->item("occ_paiement_cheque")
	if (a == "27" ){
		$( "div.chequeType" ).show();
		$( "div.banqueCheque" ).show();
		$( "div.virementBancaire" ).hide();
		document.getElementById("num_cheque").required = true;
		document.getElementById("banque_cheque").required = true;
		document.getElementById("virementBancaire").required = false;
		document.getElementById("send").disabled = false; 
	}
	// virement
	// $this->config->item("occ_paiement_vire")
	else if (a == "26" ) {
		$( "div.virementBancaire" ).show();
		$( "div.chequeType" ).hide();
		$( "div.banqueCheque" ).hide();
		document.getElementById("num_cheque").required = false;
		document.getElementById("banque_cheque").required = false;
		document.getElementById("send").disabled = false; 
	}else {
		document.getElementById("num_cheque").required = false;
		document.getElementById("banque_cheque").required = false;
		$( "div.virementBancaire" ).hide();
		$( "div.chequeType" ).hide();
		$( "div.banqueCheque" ).hide();
	}
}
</script>