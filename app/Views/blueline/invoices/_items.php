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
<!-- Nom / Code + Famille -->
<div class="row">
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="name">Nom / Code</label>
			<input id="name" name="name" type="text" class="required form-control"  onblur="validatefname(this)" value="<?php if(isset($items)){ echo $items->name; } ?>" required/>
            <div id="name-help-block" class="help-block" ></div>
        </div>
	</div>
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="id_family"><?=$this->lang->line('application_parent_family');?></label>
			<select name="id_family" id="id_family" class="chosen-select ">
				<option value="0">_</option>
				<?php  echo afficher($families,$type);?>
			</select> 
		</div>
	</div>
</div>

<!-- descriptioon -->
<div class="form-group">
	<label for="description"><?=$this->lang->line('application_description');?></label>
    <textarea class="input-block-level summernote-modal"  id="textfield" name="description"><?php if(isset($items)){ echo $items->description; } ?></textarea>
</div>

<!-- Prix + TVA -->
<div class="row">
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="value"><?=$this->lang->line('application_prix_ht');?></label>
			<input id="value" type="text" name="value" class="form-control number"  value="<?php if(isset($items)){ echo $items->value; } ?>" />
		</div>
	</div>
	<div class="col-md-6 col-xs-12">
		<div class="form-group">
			<label for="TVA"><?=$this->lang->line('application_taxe_tva');?></label>
			<?php //var_dump($tva); exit;?> 
		    	<?php //echo form_dropdown_ref('tva', $tva, $items->tva, 'style="width:100%" class="chosen-select"'); ?>

		    	<select name="tva" id="TVA" class="chosen-select">
		        <?php if(isset($items)){
					$currenttva=$items->tva;
					foreach($tva as $key){
					   if($key->name==$currenttva){?>
					<option value="<?=$key->name?>" selected><?=$key->name?>%</option>
				<?php }else{?> 
					<option value="<?=$key->name?>"><?=$key->name?>%</option>
				<?php }  }
		        }else{ 
					{ foreach($tva as $key){
						if($key->id == $defaultTva){ ?> 
						<option value="<?=$key->name?>" selected><?=$key->name?>%</option>
						<?php } else {  ?>
							<option value="<?=$key->name?>" ><?=$key->name?>%</option>
						<?php }} ?>
			    <?php }}?>
			    </select> 
		</div>
	</div>
</div>


<!-- Unité -->
<div class="form-group">
	<label for="status"><?=$this->lang->line('application_unit');?></label>
	<?php
		$options = array();
		$options[''] = $this->lang->line('application_no_unit');
		foreach ($item_units as $unit) {
			$options[$unit->value] = "{$unit->description} ({$unit->value})";
		}
		echo form_dropdown('unit', $options, $items->unit, 'style="width:100%" class="chosen-select"');
	?>
</div>

<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>

<script>
    function validate(tag,type,message){
        alert( tag.siblings('.help-block').html());
        if(type=='error'){
            tag.closest('help-block').text(message).addClass('color-red');
            tag.closest('help-block').addClass('color-red').removeClass('color-green');
        }else if(type=="success"){
            tag.closest('help-block').text(message).removeClass('color-red');
            tag.closest('help-block').addClass('color-green').removeClass('color-red');
        }
        alert(tag.parent.html());
    }

    function validatefname(elem){
        var value = elem.value.trim();
        if(value==''){
            validate($(this), "error", "Champ obligatoire");
        }else{
            var formData = new FormData($('#_item')[0]);
            $.ajax({
                type: 'POST',
                data: formData ,
                async: false,
                url: "<?php echo site_url("/items/verifierReferenceItem") ?>" ,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (res) {
                    if (res.result == 0) {
                        $('#name-help-block').text("").removeClass('color-red');
                        $('#name-help-block').addClass('color-green').removeClass('color-red');
                    }else if(res.result == 1){
                        $('#name-help-block').text("Référence déjà existante").addClass('color-red');
                        $('#name-help-block').addClass('color-red').removeClass('color-green');
                    }
                }
            });

        }
    }
</script>
