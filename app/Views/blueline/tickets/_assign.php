<?php   
$attributes = array('class' => '', 'id' => '_article');
echo form_open_multipart($form_action, $attributes); ?>
<?php if(isset($ticket)){ ?>
<input id="id" type="hidden" name="id" value="<?php echo $ticket->id; ?>" />
<?php } ?>

<div class="form-group">
	<label for="collaborater"><?=$this->lang->line('collaborateur');?></label>
	<div>
	<select name="to"  id="to"  class="chosen-select">
	   <?php foreach($collaboraters as $collaborater){ 
			if($collaborater->id == $ticket->collaborater_id) { ?>
		<option value="<?=$collaborater->id?>" selected><?=$collaborater->firstname.' '.$collaborater->lastname?></option>
			<?php } else { ?>
		<option value="<?=$collaborater->id?>" ><?=$collaborater->firstname.' '.$collaborater->lastname?></option>
			<?php } ?>
	   <?php } ?>
	</select>
	</div>
</div> 
  

<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('assigner');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>