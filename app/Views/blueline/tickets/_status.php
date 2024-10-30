<?php   
$attributes = array('class' => '', 'id' => '_type');
echo form_open($form_action, $attributes); 
if(isset($ticket)){ ?>
<input id="id" type="hidden" name="id" value="<?php echo $ticket->id; ?>" />
<?php } ?>    
<div class="form-group">
	<label for="status"><?=$this->lang->line('application_status');?></label>
	<select name="status"  id="status" id="" class="chosen-select">
	   <?php foreach($status as $val){ 
			if($val->id == $ticket->status) { ?>
		<option value="<?=$val->id?>" selected><?=$val->name?></option>
			<?php } else { ?>
		<option value="<?=$val->id?>" ><?=$val->name;?></option>
			<?php } ?>
	   <?php } ?>
	</select>
</div>    

        <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
        <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
        </div>


<?php echo form_close(); ?>