<?php 
	function afficher($data,$type, $level = 0) { 
		settype($type, 'string'); 
		for ($i = 0; $i < $level; $i++) { $levelContent .= "----"; }
		foreach ($data as $item) {
			if($type == $item['libelle']){
				$output .= "<option value=". $item['id'] ." selected >$levelContent". $item['libelle'] ."</option>";	
			}
			else {
				$output .= "<option value=". $item['id'] ." >$levelContent". $item['libelle'] ."</option>";
			}
			$output .= "<option value=". $item['children'] .">". afficher($item['children'],$type, $level + 1) ."</option>";	
				
		}
		return $output;
	}
?>

<?php   
	$attributes = array('class' => '', 'id' => '_item');
	echo form_open($form_action, $attributes); 
?>
<?php if(isset($items)){ ?>
	<input id="id" type="hidden" name="id" value="<?=$items->id;?>" />
<?php } ?>
 <div class="form-group">
	<label for="name"><?=$this->lang->line('application-Libelle');?></label>
	<input id="name" name="libelle" type="text" class="required form-control"  value="<?php if(isset($items)){ echo $items->libelle; } ?>"  required/>
</div>

<div class="form-group">
	<label for="type"><?=$this->lang->line('application_parent_family');?></label>
	<select name="parent" id="type" class="chosen-select description-setter">
	<option value="0">_</option>
	<?php echo afficher($families,$type); ?>
	</select> 
</div>

<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>