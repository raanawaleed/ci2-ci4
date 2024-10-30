<?php
$attributes = array('class' => '', 'id' => '_item');
echo form_open($form_action, $attributes);
?>
<?php if(isset($avoir)){ ?>
<input id="avoir_id" type="hidden" name="avoir_id" value="<?=$avoir->id;?>" />
<?php }
if(isset($avoir_has_items)){?>
	<input id="id" type="hidden" name="id" value="<?=$avoir_has_items->id;?>" />
	<input id="avoir_id" type="hidden" name="avoir_id" value="<?=$avoir_has_items->avoir_id;?>" />
<div class="form-group">
	<label for="name"><?=$this->lang->line('application_name');?></label>
	<input id="name" name="name" type="text" class="form-control"  value="<?=$avoir_has_items->name;?>" required />
</div>
<div class="form-group">
        <label for="value"><?=$this->lang->line('application_prix_unitaire');?></label>
        <input id="value" type="text" name="value" class="required form-control number"  value="<?=$avoir_has_items->value;?>" required />
</div>
<div class="form-group">
        <label for="type"><?=$this->lang->line('application_discount');?> %</label>
        <input id="remise" type="number" name="discount" class="form-control number"  value="<?=$avoir_has_items->discount;?>" />
</div>
<div class="form-group">
	<label for="TVA"><?=$this->lang->line('application_taxe_tva');?></label>

	<select name="tva" id="TVA" class="chosen-select" required >
		<?php $currenttva=$avoir_has_items->tva;
		foreach($tva as $key){
			  if($key->name==$currenttva){?>
			<option value="<?=$key->name?>" selected><?=$key->name?>%</option>
			<?php }else{?>
			<option value="<?=$key->name?>"><?=$key->name?>%</option>
			<?php }
		} ?>
	</select>
</div>

<?php } else{ ?>
 <div class="form-group">
	<label for="name"><?=$this->lang->line('application_name');?></label>
	<input id="name" name="name" type="text" class="form-control"  value="" required />
 </div>
 <div class="row">
	<div class="col-md-4 col-xs-12">
		 <div class="form-group">
			<label for="value"><?=$this->lang->line('application_prix_unitaire');?></label>
			<input id="value" type="text" name="value" class="form-control number"  value="" required />
		 </div>
	</div>
	<div class="col-md-4 col-xs-12">
		<div class="form-group">
			<label for="type"><?=$this->lang->line('application_discount');?> %</label>
			<input id="remise" type="number" name="discount" class="form-control"  value="" />
		</div>
	</div>
	<div class="col-md-4 col-xs-12">
		 <div class="form-group">
			<label for="TVA"><?=$this->lang->line('application_taxe_tva');?></label>
			<select name="tva" id="TVA" class="chosen-select" required >
			<?php if(isset($avoir_has_items)){
				$currenttva=$avoir_has_items->tva;
				foreach($tva as $key){
					if($key->name==$currenttva){?>
						<option value="<?=$key->name?>" selected><?=$key->name?>%</option>
					<?php }else{?>
						<option value="<?=$key->name?>"><?=$key->name?>%</option>
					<?php
					}
				}
		    }else{ foreach($tva as $key){
				if($key->id == $defaultTva){ ?>
					<option value="<?=$key->name?>" selected><?=$key->name?>%</option>
					<?php } else {  ?>
						<option value="<?=$key->name?>" ><?=$key->name?>%</option>
					<?php }} ?>

		    <?php }?>
		    </select>
		</div>
	</div>
</div>
<?php } ?>
<div class='alert alert-danger error' hidden>
      Vérifier la saisie de QTÉ/HEURES
</div>
<div class="row">
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="amount"><?=$this->lang->line('application_quantity');?></label>
			<input id="amount"  type="number" min="0" step="0.01" name="amount" class="form-control number comma-to-point"  value="<?php if(isset($avoir_has_items)){ echo $avoir_has_items->amount; }else{echo '1';} ?>"  required />
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
				echo form_dropdown('unit', $options, $avoir_has_items->unit, 'style="width:100%" class="chosen-select"');
			?>
		</div>
	</div>
</div>
 <div class="form-group">
	<label for="description"><?=$this->lang->line('application_description');?></label>
	<textarea id="description" class="form-control" name="description"><?php if(isset($avoir_has_items)){ echo $avoir_has_items->description; } ?></textarea>
</div>
<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_add');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>
<script>
$('#value.form-control.number').keydown(function (event) {
   console.log("debug");


        });
$('#amount').on('change',function (){
    var amount=$("#amount").val();
	if(isNaN(amount)){

         $("input[type=submit]").prop("disabled",true);
         $('.error').fadeIn();
         $('.error').fadeOut(5000);
	}else{
        $("input[type=submit]").prop("disabled",false);

	}
})
$('input[name="name"]').on('change',function(){
	$('input[type="submit"]').prop('disabled',false);
})
</script>
