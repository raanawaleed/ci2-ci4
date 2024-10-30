<?php
	function afficher($data, $level = 0) {
		$levelContent = "";
		for ($i = 0; $i < $level; $i++) { $levelContent .= "----"; }
		foreach ($data as $item) {
			$output .= "<option value=". $item['id'] ." >$levelContent". $item['libelle'] ."</option>";
			$output .= "<option value=". $item['children'] .">". afficher($item['children'], $level + 1) ."</option>";
		}
		return $output;
	}
?>
<?php
	$attributes = array('class' => '', 'id' => '_item');
	echo form_open($form_action, $attributes);
?>
<?php if(isset($avoir)){ ?>
	<input id="id" type="hidden" name="avoir_id" value="<?=$avoir->id;?>" />
<?php }
if(isset($avoir_has_items)){?>
	<input id="id" type="hidden" name="id" value="<?=$avoir_has_items->id;?>" />
	<input id="avoir_id" type="hidden" name="avoir_id" value="<?=$avoir_has_items->avoir_id;?>" />
<div class="form-group">
	<label for="name"><?=$this->lang->line('application_name');?></label>
	<input id="name" name="name" type="text" class="form-control"  value="<?=$avoir_has_items->name;?>" readonly />
</div>
<div class="form-group">
	<label for="type"><?=$this->lang->line('application_family');?></label>
	<input id="type" type="text" name="type" class="required form-control"  value="<?=$avoir_has_items->type;?>" readonly />
</div>
<div class="form-group">
	<label for="value"><?=$this->lang->line('application_value');?></label>
	<input id="value" type="text" name="value" class="form-control number"  value="<?=$avoir_has_items->value;?>" />
</div>

<div class="form-group">
	<label for="type"><?=$this->lang->line('application_discount');?> %</label>
	<input id="remise" type="number" name="discount" class="form-control number"  value="<?=$avoir_has_items->discount;?>" />
</div>

<div class="form-group">
	<label for="type"><?=$this->lang->line('application_type');?></label>
	<input id="type" type="text" name="type" class="required form-control"  value="<?=$avoir_has_items->type;?>" />
</div>
<?php } else{ ?>
<div id="item-selector">
	<div class="form-group" style="position:relative;">
		<label for="item_id"><?=$this->lang->line('application_item');?></label><br>
		<?php $options = array();
		$options['0'] = '-';
		foreach ($items as $value):
			$options[$value->id] = $value->name." - ".$value->value." ".$core_settings->currency;
			?><span class="hidden" id="item<?=$value->id;?>"><?=$value->description;?></span><?php
		endforeach;
		foreach ($rebill as $value):
			$options["rebill_".$value->id] = "[".$this->lang->line('application_rebill')."] ".$value->description." - ".$value->value." ".$core_settings->currency;
		endforeach;
		echo form_dropdown('item_id', $options, '', 'id="item_id" class="chosen-select description-setter" ');?>
		<a style="position: absolute; top: 0px; right: 0;" class="btn btn-primary tt additem" titel="<?=$this->lang->line('application_custom_item');?>"><i class="fa fa-plus"></i></a>
	</div>
</div>
<div id="item-editor">
	<div class="form-group">
		<label for="name"><?=$this->lang->line('application_name');?></label>
		<input id="name" name="name" type="text" class="form-control"  value="" />
	</div>
<div class="form-group">
	<label for="value"><?=$this->lang->line('application_prix_unitaire');?></label>
	<input id="value" type="text" name="value" class="form-control number"  value="" />
</div>
<div class="form-group">
	<label for="id_family"><?=$this->lang->line('application_parent_family');?></label>
	<select name="id_family" id="id_family" class="required chosen-select description-setter">
	<option value="0">_</option>
	<?php  echo afficher($families);?>
	</select>
</div>
</div>
<div class="form-group Prixunitaire" style="display" >
	<label for="value"><?=$this->lang->line('application_prix_unitaire');?></label>
	<input id="Prixunitaire" type="text" name="Prixunitaire" class="form-control number"  value="" />
</div>
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
			}else{ foreach($tva as $key){?>
				<option value="<?=$key->name?>"><?=$key->name?>%</option>
				<?php }}?>
				</select>
		</div>	<?php } ?>
	</div>
</div>
<div class='alert alert-danger error' hidden>
	Vérifier la saisie de QTÉ/HEURES
</div>
<div class="row">
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="amount"><?=$this->lang->line('application_quantity');?></label>
			<input id="amount" type="number" min="0" step="0.01" name="amount" class="required form-control number" value="<?php if(isset($avoir_has_items)){ echo $avoir_has_items->amount; }else{echo '1';} ?>"  />
		</div>
	</div>
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="status"><?=$this->lang->line('application_unit');?></label>
			<input id="unit" type="text" name="unit" class="requiredss form-control number"  value="" />
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
