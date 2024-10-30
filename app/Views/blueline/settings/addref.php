<?php   
    $attributes = array('class' => '', 'id' => '_company', 'autocomplete' => 'off');
    echo form_open_multipart($form_action, $attributes); 
?>

<div class="form-group">
    <label for="name"><?=$this->lang->line('application-Libelle');?> *</label>
    <input id="name" type="text" name="name" class="required form-control" value="<?php if(isset($data)){echo $data->name;} ?>" required/>
</div>

<div class="form-group">
    <label for="description"><?=$this->lang->line('application_Description');?> </label>
    <input id="description" type="text" name="description" class="form-control" value="<?php if(isset($data)){echo $data->description;} ?>"/>
</div>

<?php if (isset($view_status)) :?>
	<div class="form-group">
		<label for="statut"><?=$this->lang->line('application_statut');?> </label>
	    <input id="statut" type="checkbox" class="checkbox" name="statut"
	    	<?php if(isset($data->visible)) { 
	    		if($data->visible == 1) { echo " checked "; } 
			}elseif(isset($data->inactive)){
				if($data->inactive == 0) { echo " checked "; } 
			};?>
		 data-labelauty="<?=$this->lang->line('application_task_public');?>" />
	</div>
<?php endif; ?>
			

<div class="modal-footer">
    <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
    <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>
