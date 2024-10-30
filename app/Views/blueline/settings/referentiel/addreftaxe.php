<?php   
    $attributes = array('class' => '', 'id' => '_company', 'autocomplete' => 'off');
    echo form_open_multipart($form_action, $attributes); 
?>

<div class="form-group">
    <label for="name"><?=$this->lang->line('application-Libelle');?>  (0->100) *</label>
    <input id="name" type="number"  min="0" max="100" name="name" class="required form-control" value="<?php if(isset($data)){echo $data->name;} ?>" required/>
</div>
<div class="form-group">
    <label for="name"><?=$this->lang->line('application_Description');?> </label>
    <input id="name" type="text" name="description" class="form-control" value="<?php if(isset($data)){echo $data->description;} ?>"/>
</div>
<div class="form-group" style="padding: 20px 9px;">
	<span><?=$this->lang->line('application_default_taxe');?></span><br><br>
	<label class="switch" >
		<?php if(($settings->tax == $data->id) && isset($data)){?>
		<input type="checkbox" name="tax" id="tax" checked>
		<?php	}else{ ?>  
		<input type="checkbox" name="tax" id="tax">
		<?php  } ?>
	<div class="slider round"></div>
	</label>
</div>
<div class="modal-footer">
    <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
    <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>
