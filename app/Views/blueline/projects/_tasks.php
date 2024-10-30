<?php   
$attributes = array('class' => 'dynamic-form', 'data-reload' => 'task-list', 'data-reload2' => 'milestones-list', 'data-baseurl' => base_url(), 'id' => '_tasks');
echo form_open($form_action, $attributes); 
?>
<?php if(isset($task)){ $public = $task->public; ?>
  <input id="id" type="hidden" name="id" value="<?php echo $task->id; ?>" />
<?php } ?>
 <div class="form-group">
	<label for="name"><?=$this->lang->line('application_task_name');?> *</label>
	<input id="name" type="text" name="name" class="form-control resetvalue" value="<?php if(isset($task)){echo $task->name;} ?>"  required/>
</div>
<div class="row">
    <div class="col-md-6">
         <div class="form-group">
			<label for="priority"><?=$this->lang->line('application_priority');?></label>
			<?php $prioritys = array();
					$prioritys['0'] = '-';
					$prioritys['1'] = $this->lang->line('application_low_priority');
					$prioritys['2'] = $this->lang->line('application_med_priority');
					$prioritys['3'] = $this->lang->line('application_high_priority');
			if(isset($task)){$priority = $task->priority;}else{$priority = "2";}
			echo form_dropdown('priority', $prioritys, $priority, 'style="width:100%" class="chosen-select"');?>
        </div> 
    </div>
    <div class="col-md-6">
         <div class="form-group">
			<label for="status"><?=$this->lang->line('application_status');?></label>
			<?php $options = array(
					  'open'  => $this->lang->line('application_open'),
					  'done'    => $this->lang->line('application_done'),
					);
					$status = FALSE;
					if(isset($task)){ $status = $task->status;} 
					echo form_dropdown('status', $options, $status, 'style="width:100%" class="chosen-select"'); ?>
        </div>  
    </div>
</div>

<div class="form-group">
	<label for="user"><?=$this->lang->line('application_assign_to_agent');?></label>
	<?php $defaultIntervenant = '';
	$usr = array();
	if(!isset($task)){
		$usr['0'] = '-';
	}
	foreach ($intervenants as $val):
		if($task->intervenant_id == $val->id) {
			 $defaultIntervenant = 'inter'.$val->id;
		}
		$usr['inter'.$val->id] = $val->name.' '.$val->surname; 
	endforeach;	
	foreach ($users as $val):
		if($task->user_id == $val->id) {
			$defaultIntervenant = 'user'.$val->id;
		}
		$usr['user'.$val->id] = $val->firstname.' '.$val->lastname; 
	endforeach;	
	if(isset($defaultIntervenant)){ 
		echo form_dropdown('user_id', $usr, $defaultIntervenant, 'style="width:100%" class="chosen-select"');
	} else {
		echo form_dropdown('user_id', $usr, '', 'style="width:100%" class="chosen-select"');
	}?>
</div> 

 <!--<div class="form-group">
	<label for="value"><?=$this->lang->line('application_value');?></label>
	<input id="value" type="text" name="value" class="form-control decimal" value="<?php if(isset($task)){echo $task->value;} ?>" />
</div>-->
<div class="row">
    <div class="col-md-6">
		<div class="form-group">
		  <label for="start_date"><?=$this->lang->line('application_start_date');?></label>
		  <input class="form-control datepicker not-required" name="start_date" id="start_date" type="text" value="<?php if(isset($task)){ echo $task->start_date;} ?>" data-date-format="yyyy-mm-dd"/>
		</div>
    </div>
    <div class="col-md-6">
		<div class="form-group">
		  <label for="due_date"><?=$this->lang->line('application_due_date');?></label>
		  <input class="form-control datepicker-linked not-required" name="due_date" id="due_date" type="text" value="<?php if(isset($task)){echo $task->due_date;} ?>" data-date-format="yyyy-mm-dd"/>
		</div>
    </div>
</div>
<div class="form-group">
  <label for="sector"><?=$this->lang->line('application_Sector');?> </label>
  <input type="text" name="sector" class="form-control" id="sector" 
  value="<?php if(isset($task)){echo $task->sector;} ?>" />
</div>
<div class="form-group">
  <label for="amount"><?=$this->lang->line('application_Amount');?> </label>
  <input type="text" name="amount" class="form-control" id="amount" 
  value="<?php if(isset($task)){echo $task->amount;} ?>"/>
</div>
 <div class="form-group">
	<label for="textfield"><?=$this->lang->line('application_description');?></label>
	<textarea class="input-block-level summernote-modal" id="textfield" name="description"><?php if(isset($task)){echo $task->description;} ?></textarea>
</div>

<div class="modal-footer">
    <?php if(isset($task)){ ?>
	<a href="<?=base_url()?>projects/tasks/<?=$task->project_id;?>/delete/<?=$task->id;?>" class="btn btn-danger pull-left button-loader" ><?=$this->lang->line('application_delete');?></a>
	<?php }else{  ?>
	<a class="btn btn-default pull-left" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
	<i class="fa fa-spinner fa-spin" id="showloader" style="display:none"></i> 
	<button id="send" name="send" data-keepModal="true" class="btn btn-primary send button-loader"><?=$this->lang->line('application_save_and_add');?></button>
	<?php } ?>
	<button name="send" class="btn btn-primary send button-loader"><?=$this->lang->line('application_save');?></button>
</div>
<?php echo form_close(); ?>