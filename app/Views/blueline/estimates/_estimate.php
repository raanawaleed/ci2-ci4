<?php   
$attributes = array('class' => '', 'id' => '_invoices');
echo form_open($form_action, $attributes); 
?>
<?php if(isset($estimate)){ ?>
<input id="id" type="hidden" name="id" value="<?=$estimate->id;?>" />
<?php } ?>
<?php if(isset($view)){ ?>
<input id="view" type="hidden" name="view" value="true" />
<?php } ?>
<input id="status" name="status" type="hidden" value="Open"> 

<!-- nouveau devis -->
<!-- numéro devis + devise -->
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="reference"><?=$this->lang->line('application_reference_id');?></label>
			<?php if(!empty($core_settings->estimate_prefix)){ ?>
		    <div class="input-group"><?php if(empty($estimate)){?><div class="input-group-addon"><?php 
			$reverse = strrev($core_settings->estimate_prefix); 
			$splitReverse = explode('-', $reverse);
			$splitDate= explode('-', date("d-m-y")); 
			if($splitReverse[0] == 'YY') {
				$output = strrev($splitReverse[1]).$splitDate[2];
			}else if ($splitReverse[0] =='MM') {
				$output = strrev($splitReverse[2]).$splitDate[2].$splitDate[1]; 
			}			
			echo $output; 
			?></div> <?php }} ?>
			<input id="reference" type="text" name="reference" class="form-control"  
			value="<?php if(isset($estimate)){echo $estimate->estimate_num; 
						} else{
							if($core_settings->estimate_reference<10){
							echo '0'.$core_settings->estimate_reference;
							}else{echo $core_settings->estimate_reference;}}?>" readonly />
			<?php if(!empty($core_settings->estimate_prefix)){ ?> </div><?php } ?>	
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="currency"><?=$this->lang->line('application_currency');?></label>
			
			<?php if (!isset($estimate)){ ?>
				<select name="currency"  id="currency" id="" class="chosen-select">
				 <?php foreach($currencys as $currency){
				   if($core_settings->currency==$currency->name){ ?>
						<option value="<?=$currency->name?>" selected><?=$currency->name?></option>
				   <?php  } else{?>
						<option value="<?=$currency->name?>"><?=$currency->name?></option>
				   <?php }}?>
				</select>
			<?php } else { ?>
				<select name="currency"  id="currency" id="" class="chosen-select">
				 <?php foreach($currencys as $currency){
				   if($estimate->currency==$currency->name){ ?>
						<option value="<?=$currency->name?>" selected><?=$currency->name?></option>
				   <?php  } else{?>
						<option value="<?=$currency->name?>"><?=$currency->name?></option>
				   <?php }}?>
				</select>
			<?php } ?>	
		</div>
	</div>
</div>

<!-- client -->
<div class="row">
	<div class="col-md-12">
		<div class="form-group clientPersistant" <?php if ($company->passager == 1 ) {?> style="display:none" <?php } else { ?> style="display"  <?php } ?> >
			<label for="client"><?=$this->lang->line('application_client');?> *</label>
				<?php $options = array();
					
					foreach ($companies as $value): 
						if (!isset($estimate)){
							if($value->passager != 1){
								$options[$value->id] = $value->name;
							}
						}
						else {
							if (($value->passager == 1) && ($estimate->company_id != $value->id)){
								unset($options[$value->id]);
							} else {
								$options[$value->id] = $value->name;
							}
						}
					endforeach;
					if(isset($estimate)){$client = $estimate->company_id; $project = $estimate->project_id; } else { $client = ""; $project = ""; }
					echo form_dropdown('company_id', $options, $client, 'style="width:100%" data-destination="getProjects" class="chosen-select getProjects clientsetter"');?>
			</div>
		</div>
</div>

<!-- client passager -->
<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			<?php if ($company->passager == 1 ) {?>
				<input id="company" type="checkbox" class="checkbox" name="company" value="1" onclick="validate()" checked data-labelauty="<?=$this->lang->line('application_client_passager');?>">
			<?php } else { ?>
				<input id="company" type="checkbox" class="checkbox" name="company" value="1" onclick="validate()" data-labelauty="<?=$this->lang->line('application_client_passager');?>">
			<?php } ?>
		</div>
	</div>
</div>

 <!--Nom client passager -->
<div class="form-group nomClient" <?php if ($company->passager == 1 ) {?> style="display" <?php } else { ?> style="display:none"  <?php } ?> >
	<label for="textfield"><?=$this->lang->line('application_name_client');?> </label>
	<input id="nomClient" type="text" name="nomClient" <?php if ($company->	passager == 1 ) {?> value="<?=$company->name ?>" <?php } else { ?> 
	value="" <?php } ?>	class=" form-control number money-format" />
</div>

<!--Client exonoree de timbre fiscale  -->
<div class="form-group timbre_fiscal" <?php if ($company->passager == 1 ) {?> style="display" <?php } else { ?> style="display:none"  <?php } ?> >
    <label for="city"><?=$this->lang->line('application_timbre');?></label>
	<?php if ($company->timbre_fiscal == 1 ) {?> 
		<input type="checkbox" name="timbre_fiscal" data-labelauty="<?=$this->lang->line('application_exoneration_timbre');?>"  class="checkbox" value="1" checked>
	<?php } else { ?>
		<input type="checkbox" name="timbre_fiscal" data-labelauty="<?=$this->lang->line('application_exoneration_timbre');?>"  class="checkbox" value="1" >
	<?php } ?>
</div>

<!--Client exonoree de TVA  -->
<div class="form-group tva" <?php if ($company->passager == 1 ) {?> style="display" <?php } else { ?> style="display:none"  <?php } ?> >
	<label for="city"><?=$this->lang->line('application_TVA');?></label>
	<?php if ($company->tva == 1 ) {?> 
		<input type="checkbox" name="tva" class="checkbox" value="1" checked data-labelauty="<?=$this->lang->line('application_exoneration_tva');?>">
	<?php } else {  ?>
		<input type="checkbox" name="tva" data-labelauty="<?=$this->lang->line('application_exoneration_tva');?>"  class="checkbox" value="1">
	<?php  } ?>
</div>

<!--retenue guarantee -->
<div class="form-group guarantee" <?php if ($company->passager == 1 ) {?> style="display" <?php } else { ?> style="display:none"  <?php } ?> >
	<label for="city"><?=$this->lang->line('application_guarantee');?></label>
	<?php if ($company->guarantee == 1 ) {?>   
		<input type="checkbox" name="guarantee" data-labelauty="<?=$this->lang->line('application_retenue');?>"  class="checkbox" value="1" checked>
	<?php } else {  ?>
		<input type="checkbox" name="guarantee" data-labelauty="<?=$this->lang->line('application_retenue');?>"  class="checkbox" value="1" >
	<?php } ?>
</div>

<!-- choix d'un projet -->
<div id="item-selector" style="position:relative;">
	<div class="form-group">
		<label for="project"><?=$this->lang->line('application_projects');?></label>
		<select name="project_id" id="project_id" style="width:100%" class="chosen-select">
			<option value="0">-</option>
			<?php foreach ($companies as $comp): ?>
				<optgroup label="<?=$comp->name?>" id="optID_<?=$comp->id?>" 
				<?php  if(($company->id != $comp->id) &&($company->passager == 1)){ ?>disabled="disabled"<?php } ?>>
				  <?php foreach ($comp->projects as $pro): ?>
					<option value="<?=$pro->id?>" 
					<?php if($pro->id == $projectId ) {?> selected <?php } ?>><?=$pro->project_num.'_'.$pro->name?></option>
					<?php endforeach; ?>
				</optgroup>
				<?php  endforeach; ?>
		</select>
		
		<?php if ($company->passager == 1 ) {?>
			<a class="btn btn-primary tt addprojectClient" id="addproject" titel="<?=$this->lang->line('application_custom_item');?>" style="position: absolute;top: 0;right: 0;margin:0 !important;"><i class="fa fa-plus"></i></a>
		<?php } else {?>
			<a class="btn btn-primary tt addproject" id="addproject" titel="<?=$this->lang->line('application_custom_item');?>" style="position: absolute;top: 0;right: 0; margin:0 !important;"><i class="fa fa-plus"></i></a>
		<?php } ?>
	</div>
</div>

<!-- creation of new project(name + start date)-->
<div id="item-editor">
	<div class="form-group">
		<label for="name"><?=$this->lang->line('application_name');?>*</label>
		<input id="name" name="name" type="text" class="form-control"  value="" />
	</div>
	<div class="form-group">
	  <label for="start"><?=$this->lang->line('application_start_date');?> *</label>
	  <input class="form-control datepicker not-required" name="start" id="start" type="text" value="" />
	</div>
	<div class="form-group">
	  <label for="end"><?=$this->lang->line('application_deadline');?> *</label>
	  <input class="form-control datepicker not-required" name="end" id="end" type="text" value="" />
	</div>
</div>

<!-- objet du devis -->
<div class="form-group">
	<label><?=$this->lang->line('application_asset');?></label>
	<input type="text" name="subject" class="form-control" value="<?php if(isset($estimate)){ echo $estimate->subject;} ?> ">
</div>


<!-- remise -->
<div class="form-group">
	<label for="currency"><?=$this->lang->line('application_discount_percent');?></label>
	<input class="form-control" name="discount" id="appendedInput" type="number" min="0" max="100" value="<?php if(isset($estimate)){ echo $estimate->discount;} else { echo "0"; } ?>"/>
</div>

<!-- statut devis accepté, refusé, ... -->

<div class="row">
	<div class="col-md-6">
<div class="form-group">
	<label>unité de quantité</label>
	<input type="text" name="unite" class="form-control" value="<?php if(isset($estimate)){ echo $estimate->unite;} else { echo "m²"; } ?>">
</div></div>
<div class="col-md-6">
<div class="form-group">
	<label for="status">Etat</label>
		<?php echo form_dropdown_ref('status', $state, $estimate->status, 'style="width:100%" class="chosen-select"'); ?>
</div>
</div>

		</div>


<!-- date émission + échéance -->
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="issue_date"><?=$this->lang->line('application_issue_date');?></label>
			<input id="issue_date" type="text" name="issue_date" class="required datepicker-linked  form-control" value="<?php if(isset($estimate)){echo $estimate->issue_date;}else{echo $current_date;} ?>"  required/>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="due_date"><?=$this->lang->line('application_due_date');?></label>
			<input id="due_date" type="text" name="due_date" class="required datepicker-linked form-control" value="<?php if(isset($estimate)){echo $estimate->due_date;}else{echo $current_echeance;} ?>"  required/>
		</div>
	</div>
</div>

<div class="form-group">
	<label for="notes"><?=$this->lang->line('application_notes');?></label>
	<textarea id="notes" name="notes" class="textarea summernote-modal form-control" style="height:100px"><?php if(isset($estimate)) {echo $estimate->notes;} else { echo $core_settings->notes; }?></textarea>
</div>

<div class="modal-footer">
    <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
</div>
<?php echo form_close(); ?>
<script>
$(document).ready(function(){
	if($('.clientsetter').val()<=0){
		$('input[type=submit]').prop('disabled',true);
	}
	$('.clientsetter').on('change',function(){
		if($(this).val()>0){
			$('input[type=submit]').prop('disabled',false);
		}else{
			$('input[type=submit]').prop('disabled',true);
		}
	});
})
</script>
<script>
//$(document).ready(function() {
//	document.getElementById("notes").setAttribute('style', 'display:inline !important');
//});
</script>