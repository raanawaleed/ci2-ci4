<?php
	$attributes = array('class' => '', 'id' => '_item');
	echo form_open($form_action, $attributes);
?>
<?php if(isset($invoice)){ ?>
	<input id="invoice_id" type="hidden" name="facture_id" value="<?=$invoice->id;?>" />
<?php }
if(isset($invoice_has_items)){?>
	<input id="id" type="hidden" name="id" value="<?=$invoice_has_items->id;?>" />
	<input id="invoice_id" type="hidden" name="facture_id" value="<?=$invoice_has_items->facture_id;?>" />
<div class="form-group">
	<label for="name"><?=$this->lang->line('application_name');?></label>
	<input id="name" name="name" type="text" class="form-control"  value="<?=$invoice_has_items->name;?>" readonly />
</div>
<div class="form-group">
	<label for="type"><?=$this->lang->line('application_family');?></label>
	<input id="type" type="text" name="type" class="required form-control"  value="<?=$invoice_has_items->type;?>" readonly />
</div>
<div class="form-group">
	<label for="value"><?=$this->lang->line('application_value');?></label>
	<input id="value" type="text" name="value" class="form-control number"  value="<?php echo display_money($invoice_has_items->value,"",$chiffre);?>" />
</div>

<div class="form-group">
	<label for="type"><?=$this->lang->line('application_discount');?> %</label>
	<input id="remise" type="number" name="discount" class="form-control number"  value="<?=$invoice_has_items->discount;?>" />
</div>

<div class="form-group">
	<label for="type"><?=$this->lang->line('application_type');?></label>
	<input id="type" type="text" name="type" class="required form-control"  value="<?=$invoice_has_items->type;?>" />
</div>
<?php } else{ ?>

<!-- liste des articles -->
<div class="form-group">
	<label for="id_family">Article</label>
	<?php echo form_dropdown('item_id', $list_items, '', 'id="item_id" class="chosen-select description-setter" '); ?>
</div>


<?php $step = 0.001; ?>
<!-- Prix unitaire -->
<div class="form-group Prixunitaire" style="display" >
    <label for="value"><?=$this->lang->line('application_prix_unitaire');?></label>
    <input id="Prixunitaire" type="number" step ="<?=$step;?>"  name="Prixunitaire" class="form-control number"  value="" />
</div>
<!-- Remise + TVA -->
<div class="row">
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="type"><?=$this->lang->line('application_discount');?> %</label>
			<input id="remise" type="number" name="discount" min =0 max=100 class="form-control number"  value="0" />
		</div>
	</div>
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="TVA"><?=$this->lang->line('application_taxe_tva');?></label>
			<select name="tva" id="TVA" class="chosen-select">
			<?php if(isset($invoice_has_items)){
			$currenttva=$invoice_has_items->tva;
			foreach($tva as $key){
				if($key->name==$currenttva){?>
					<option value="<?=$key->name?>" selected><?=$key->name?>%</option>
					<?php }else{?>
					<option value="<?=$key->name?>"><?=$key->name?>%</option>
					<?php
				}
			}
			}else{ foreach($tva as $key){?>
				<option value="<?=$key->name?>"><?=$key->name?>%</option>
				<?php }}?>
				</select>
		</div>	<?php } ?>
	</div>
</div>

<div class="row">
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="amount"><?=$this->lang->line('application_quantity');?></label>
			<input id="amount" type="number" min="0" step="0.001" name="amount" class="required form-control number" value="<?php if(isset($invoice_has_items)){ echo $invoice_has_items->amount; }else{echo '1';} ?>"  />
		</div>
	</div>
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="status"><?=$this->lang->line('application_unit');?></label>
			<?php
				$options = array();
				$options[''] = $this->lang->line('application_no_unit');
				foreach ($item_units as $unit) {
					$options[$unit->value] = "{$unit->description} ({$unit->value})";
				}
				echo form_dropdown('unit', $options, $invoice_has_items->unit, 'id="unit" style="width:100%" class="chosen-select"');
			?>
		</div>
	</div>
</div>
<div class="form-group">
	<label for="description"><?=$this->lang->line('application_description');?></label>
	<textarea id="description" class="form-control" name="description"><?php if(isset($invoice_has_items)){ echo $invoice_has_items->description; } ?></textarea>
</div>
<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_add');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>
<script>
$('#item_id').on('change',function (){
    
	var xid =  $("#item_id option:selected").val();
    var url = decodeURIComponent('../renderItem/' + xid);
    $.ajax({
		type: 'POST',
		dataType: "text",
		url: url,
		success: function (response) {
		    if (response.indexOf('{') > -1) {
				response = response.substr(response.indexOf('{'))
			} else if (response.indexOf('[') > -1) {
				response = response.substr(response.indexOf('['))
			} else {
				response = response.substr(response.indexOf('"'))
			}
			var res = response.split(","); 
			res[0] = res[0].substr(9, ); 
			res[1] = res[1].substr(6, );
			res[2] = res[2].substr(7, ).replace('}', '');
			//edit result 
			res[0] = res[0].replace('}', '').substring(1,((res[0].length)-1));
			res[1] = res[1].substring(1,((res[1].length)-1));
			res[2] = res[2].substring(1,((res[2].length)-1));
			if (res[0] == 'null'){
				document.getElementById("Prixunitaire").value = ''; 
			} else {
				document.getElementById("Prixunitaire").value = res[0];  
			}
			if (res[1] == 'null'){
				$("#TVA").val('').trigger('chosen:updated');
			}else {
				$("#TVA").val(res[1]).trigger('chosen:updated');	
			} 
			if (res[2] == 'null'){
				$("#unit").val('').trigger('chosen:updated');
			}else {
				$("#unit").val(res[2]).trigger('chosen:updated');	
			} 
		}
	});	
})
</script>