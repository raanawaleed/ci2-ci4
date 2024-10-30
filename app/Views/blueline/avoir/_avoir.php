<?php   
$attributes = array('class' => '', 'id' => '_avoir');
echo form_open($form_action, $attributes); 
?>
<?php if(isset($avoir)){ ?>
<input id="id" type="hidden" name="id" value="<?=$avoir->id;?>" />
<?php } ?>
<?php if(isset($view)){ ?>
<input id="view" type="hidden" name="view" value="true" />
<?php } ?>
<input id="status" name="status" type="hidden" value="Open"> 

<!-- numéro  -->
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="reference"><?=$this->lang->line('application_reference_id');?></label>
			<?php if(!empty($core_settings->avoir_prefix)){ ?>
			<div class="input-group"><?php if(empty($avoir)){?>
			   <div class="input-group-addon"><?php 
				$reverse = strrev($core_settings->avoir_prefix); 
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
				value="<?php if(isset($avoir)){echo $avoir->avoir_num; 
							} else
							{
								if($core_settings->avoir_reference<10)
								{
									echo '0'.$core_settings->avoir_reference;
								}else{
									echo $core_settings->avoir_reference;
								}
							}?>" readonly />
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="currency"><?=$this->lang->line('application_currency');?></label>
			<?php if (!isset($avoir)){ ?>
				<select name="currency"  id="currency" id="" class="chosen-select">
				   <?php foreach($currencys as $currency){
				   if($core_settings->currency==$currency->name){
					?>
					<option value="<?=$currency->name?>" selected><?=$currency->name?></option>
					<?php }else{?>
					<option value="<?=$currency->name?>"><?=$currency->name?></option>
					<?php }}?>
				</select>
			<?php } else { ?>
				<select name="currency"  id="currency" id="" class="chosen-select">
				   <?php foreach($currencys as $currency){
				   if($avoir->currency==$currency->name){
					?>
					<option value="<?=$currency->name?>" selected><?=$currency->name?></option>
					<?php }else{?>
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
			<?php
			$options = array();
			$options['0'] = '-';
			foreach ($companies as $value):   
				if (!isset($avoir)){
					if($value->passager != 1){
						$options[$value->id] = $value->name;
					}
				}
				else {
					if (($value->passager == 1) && ($avoir->company_id != $value->id)){
						unset($options[$value->id]);
					} else {
						$options[$value->id] = $value->name;
					}
				}
			$projects[$value->id] = $value->projects;
		    endforeach;
			if(isset($avoir)){
				$client = $avoir->company_id;
				$project = $avoir->project_id;
			}
			else{
			  $client = ""; $project = "";}
			echo form_dropdown('company_id', $options, $client, 'id ="company_id" style="width:100%" data-destination="getProjects" class="chosen-select getProjects clientsetter"');?>
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

 <!--Objet de l'avoir -->
<div class="form-group">
	<label><?=$this->lang->line('application_asset');?></label>
	<input type="text" name="subject" class="form-control" value="<?php if(isset($avoir)){ echo $avoir->subject;} ?>">
</div>

<!-- Etat -->
<div class="form-group">
	<label for="status">Etat</label>
	<?php echo form_dropdown_ref('status', $state, $avoir->status, 'style="width:100%" class="chosen-select"'); ?>
</div>


<?php if(isset($avoir)){ if($avoir->status == $this->config->item("occ_avoir_paye")){ ?>
<div class="form-group">
	<label for="paid_date"><?=$this->lang->line('application_payment_date');?></label>
	<input id="paid_date" type="text" name="paid_date" class="datepicker form-control" value="<?php if(isset($avoir)){echo $avoir->paid_date;}?>"  required/>
</div>
 <?php }} ?>

<!-- date émission + remise -->
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="issue_date"><?=$this->lang->line('application_issue_date');?></label>
			<input id="issue_date" type="text" name="issue_date" class="datepicker form-control" value="<?php if(isset($avoir)){echo $avoir->issue_date;}else{echo $current_date;} ?>"  required/>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="currency"><?=$this->lang->line('application_discount_percent');?></label>
			<input class="form-control" name="discount" id="appendedInput" type="number" min="0" max="100" value="<?php if(isset($avoir)){ echo $avoir->discount;} else { echo "0"; } ?>"/>
		</div>
	</div>
</div>

<div class="form-group">
	<label for="notes"><?=$this->lang->line('application_notes');?></label>
	<textarea id="notes" name="notes" class="textarea summernote-modal form-control" style="height:100px"><?php if(isset($avoir)) {echo $avoir->notes;} else { echo $core_settings->notes_avoir; }?></textarea>
</div>

<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>
<script>
$(document).ready(function(){
	$('input[type=submit]').prop('disabled',true);
	  var clientsetter = $('.getProjects').val();
	  if(clientsetter > 0){
	  $('input[type=submit]').prop('disabled',false);  
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
    function validate() {
        if (document.getElementById('company').checked) {
			$( "div.clientPersistant" ).hide();
			$( "div.nomClient" ).show();
			document.getElementById("nomClient").required = true;
			$('input[type=submit]').prop('disabled',false);
			
        } else {
			$( "div.clientPersistant" ).show();
			$( "div.nomClient" ).hide();

        }
    }
</script>
