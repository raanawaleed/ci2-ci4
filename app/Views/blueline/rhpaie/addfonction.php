
<?php   
	$attributes = array('class' => '', 'id' => '_item');
	echo form_open($form_action, $attributes); 
?>

	<div class="col-sm-12 col-md-12 main"> 
<div class="row">

<input id="id" type="hidden" name="fonction007" value="yup" />

			<div class="form-group">
				<label for="name"><?=$this->lang->line('application-Libelle');?>*</label>
				<input type="text" name="name" id="name" class="form-control" required>
			</div>
 
			<div class="form-group">
				<label for="description"><?=$this->lang->line('application_description');?></label>
				<input type="text" name="description" id="description" class="form-control" >
			</div>
		 	 

</div>
</div>

   <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-success" value="<?=$this->lang->line('application_add');?>"/>
        <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
   </div>
   
<?php echo form_close(); ?>