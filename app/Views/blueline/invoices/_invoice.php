<?php   
$attributes = array('class' => '', 'id' => '_invoices');
echo form_open($form_action, $attributes);
?>
<?php if(isset($invoice)){ ?>
<input id="id" type="hidden" name="id" value="<?=$invoice->id;?>" />
<?php } ?>
<?php if(isset($view)){ ?>
<input id="view" type="hidden" name="view" value="true" />
<?php } ?>
<input id="status" name="status" type="hidden" value="Open">

<!-- numéro devis + devise -->
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="reference"><?=$this->lang->line('application_reference_id');?></label>
			<?php if(!empty($core_settings->invoice_prefix)){ ?>
			<div class="input-group"><?php if(empty($invoice)){?>
			   <div class="input-group-addon"><?php
				$reverse = strrev($core_settings->invoice_prefix);
				
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
				value="<?php if(isset($invoice)){echo $invoice->estimate_num;
				
							} else
							{

								if($core_settings->invoice_reference<10)
								{
								echo '0'.$core_settings->invoice_reference;
								
								}else{echo $core_settings->invoice_reference;
									
								}
							}?>" readonly />
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="currency"><?=$this->lang->line('application_currency');?></label>
			<?php if (!isset($invoice)){ ?>
			<select name="currency"  id="currency" id="" class="chosen-select">
			   <?php foreach($currencys as $currency){
			   if($core_settings->currency==$currency->name){
				?>
				<option value="<?=$currency->name?>" selected><?=$currency->name?></option>
				<?php }else{?>
				<option value="<?=$currency->name?>"><?=$currency->name?></option>
				<?php }}?>
			</select>
			<?php } else {?>
			<select name="currency"  id="currency" id="" class="chosen-select">
			   <?php foreach($currencys as $currency){
			   if($invoice->currency==$currency->name){
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
		<div class="form-group clientPersistant focus" <?php if ($company->passager == 1 ) {?> style="display:none" <?php } else { ?> style="display"  <?php } ?> >
			<label for="client"><?=$this->lang->line('application_client');?> *</label>
			<?php
			$options = array();
			$options['0'] = '-';
			foreach ($companies as $value):
				if (!isset($invoice)){
					if($value->passager != 1){
						$options[$value->id] = $value->name;
					}
				}
				else {
					if (($value->passager == 1) && ($invoice->company_id != $value->id)){
						unset($options[$value->id]);
					} else {
						$options[$value->id] = $value->name;
					}
				}
			$projects[$value->id] = $value->projects;
		    endforeach;
			if(isset($invoice)){
				$client = $invoice->company_id;
				$project = $invoice->project_id;
				
			}
			else{
			  $client = ""; $project = "";}
			echo form_dropdown('company_id', $options, $client, 'id ="company_id" onChange="selectClient(this)" style="width:100%" data-destination="getProjects" class="chosen-select getProjects clientsetter"');?>
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

<!--Client exonoree de =timbre fiscale  -->
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
		<input type="checkbox" name="tva" data-labelauty="<?=$this->lang->line('application_exoneration_tva');?>"  class="checkbox" value="1" checked>
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
<div id="item-selector" style="position:relative;" >
	<div class="form-group">
		<label for="project"><?=$this->lang->line('application_projects');?></label>
		<select name="project_id" id="project_id" style="width:100%" class="chosen-select">
			<option value="0">-</option>
			<?php foreach ($companies as $comp): ?>
				<optgroup label="<?=$comp->name?>" id="optID_<?=$comp->id?>"
				<?php  if(($company->id != $comp->id) &&($company->passager == 1)){ ?>disabled="disabled"<?php } ?>>
				  <?php foreach ($comp->projects as $pro): ?>
					<option <?=($pro->id == $project) ? 'selected' : '' ?> value="<?=$pro->id ?>"><?=$pro->name?></option>
					<?php endforeach; ?>
				</optgroup>
				<?php  endforeach; ?>
		</select>
		<?php if ($company->passager == 1 ) {?>
			<a class="btn btn-primary tt addprojectClient" id="addproject" title="<?=$this->lang->line('application_custom_item');?>" style="position:absolute;top:0;    right: 0;margin:0 !important;"><i class="fa fa-plus"></i></a>
		<?php } else {?>
			<a class="btn btn-primary tt addproject" id="addproject" title="<?=$this->lang->line('application_custom_item');?>" style="position:absolute;top:0;    right: 0;margin:0 !important;"><i class="fa fa-plus"></i></a>
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

<!-- objet -->
<div class="form-group">
	<label><?=$this->lang->line('application_asset');?></label>
	<input type="text" name="subject" class="form-control" value="<?php if(isset($invoice)){ echo $invoice->subject;} ?>">
</div>

<!-- statut -->
<?php if(isset($invoice)){ ?>
<div class="form-group">
	<label for="status">Etat</label>
	<?php echo form_dropdown_ref('status', $state, $invoice->status, 'style="width:100%" class="chosen-select"'); ?>
</div>
<?php } ?>


<?php if(isset($invoice)){ if($invoice->status == "Paid"){ ?>
<div class="form-group">
	<label for="paid_date"><?=$this->lang->line('application_payment_date');?></label>
	<input id="paid_date" type="text" name="paid_date" class="datepicker form-control" value="<?php if(isset($invoice)){echo $invoice->paid_date;}?>"  required/>
</div>
 <?php }} ?>

<!-- date émission + remise + retenue-->
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label for="issue_date"><?=$this->lang->line('application_issue_date');?></label>
			<input id="issue_date" type="text" name="issue_date" class="datepicker form-control" value="<?php if(isset($invoice)){echo $invoice->creation_date;}else{echo $current_date;} ?>"  required/>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="currency"><?=$this->lang->line('application_discount_percent');?></label>
			<input class="form-control" name="discount" id="appendedInput" type="number" min="0" max="100" value="<?php if(isset($invoice)){ echo $invoice->discount;} else { echo "0"; } ?>"/>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="currency"><?=$this->lang->line('application_deduction__percent');?></label>
			<input class="form-control" name="deduction" id="appendedInput" type="number" min="0" max="100" value="<?php if(isset($invoice)){ echo $invoice->deduction;} else { echo "0"; } ?>"/>
		</div>
	</div>
</div>

<div class="form-group">
	<label for="notes"><?=$this->lang->line('application_notes');?></label>
	<textarea id="notes" name="notes" class="textarea summernote-modal form-control" style="height:100px"><?php if(isset($invoice)) {echo $invoice->notes;} else { echo $core_settings->notes_facture; }?></textarea>
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
			$( "div.timbre_fiscal" ).show();
			$( "div.tva" ).show();
			$( "div.guarantee" ).show();
			document.getElementById("addproject").className = "btn btn-primary tt addprojectClient";
			$('input[type=submit]').prop('disabled',false);

        } else {
			$( "div.clientPersistant" ).show();
			$( "div.nomClient" ).hide();
			$( "div.timbre_fiscal" ).hide();
			$( "div.tva" ).hide();
			$( "div.guarantee" ).hide();
			document.getElementById("addproject").className = "btn btn-primary tt addproject";
			$('input[type=submit]').prop('disabled',false);

        }
    }

</script>
<script type='text/javascript'>
    function selectClient(a)
    {
        $('input[type=submit]').prop('disabled',false);
    }
</script>
