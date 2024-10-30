<?php   
	$attributes = array('class' => '', 'id' => '_import');
	echo form_open_multipart($form_action, $attributes);
?>

<div class="form-group">
	<label for="userfile"><?=$this->lang->line('application_excel_file');?></label>
	<div>
		<input id="uploadFile" type="text" name="dummy" class="form-control uploadFile" placeholder="Choose File" disabled="disabled" />
		<div class="fileUpload btn btn-primary">
			<span><i class="fa fa-upload"></i><span class="hidden-xs"> <?=$this->lang->line('application_select');?></span></span>
			<input id="uploadBtn" type="file" name="userfile" class="upload" />
		</div>
	</div> 
</div>

<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>

<?php echo form_close(); ?>