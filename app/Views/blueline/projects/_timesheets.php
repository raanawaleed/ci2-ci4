<style>
@media (min-width: 768px){
        .modal-dialog {
            width: 800px;
        }
}
</style>
<div id="printtimesheet">
<style>
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
    .print{
        display: block;
    }
}
table thead tr th {
    text-align:left;
}
</style>

<div class="hidden print" style="text-align:center; border-bottom:2px solid #000; padding:5px; margin-bottom:20px;">[<?=$task->project->name?>] <?=$task->name?></div>
	<?php   
		$attributes = array('class' => '', 'id' => '_time');
		echo form_open($form_action, $attributes); 
	 ?>
     <table class="table data-table table-striped" id="tasktable" width="100%">
		 <thead>
			<tr>
				<th><?=$this->lang->line('application_name');?></th>
				<th class="hidden-xs"><?=$this->lang->line('application_time_spent');?></th>
				<th class="hidden-xs"><?=$this->lang->line('application_start');?></th>
				<th class="hidden-xs" id="Time"><?=$this->lang->line('application_end');?></th>
				<th class="hidden-xs" width="20px"></th>

			</tr>
		  </thead>
        <tbody id="newRows">
                <?php 
				foreach ($timesheets as $value):?>
                <?php
				$tracking           = floor($value->time/60);
				$tracking_hours     = floor($tracking/60);
				$tracking_minutes   = $tracking-($tracking_hours*60);
				$time_spent         = $tracking_hours." ".$this->lang->line('application_hours')." ".$tracking_minutes." ".$this->lang->line('application_minutes'); ?>
				<tr>
					<td>
						<!--<?php $pic = get_user_pic($value->user->userpic, $value->user->email); 
						echo "<img src=\"$pic\" class=\"img-circle list-profile-img no-print \" height=\"21px\"> "; ?>-->
						<label><?=$value->user_id->name;?> <?=$value->user_id->surname;?></label>
					</td>
					<td>
						<?=$time_spent?>
					</td>
					<td>
						<?php echo $value->start; ?>
					</td>
					<td id="endTime">
						<?php  echo $value->end; ?>     
					</td>
				    <td>
					   <?php if($this->user->id == $value->user_id || $this->user->admin == 1){ ?>
							<a href="<?=base_url()?>projects/timesheet_delete/<?=$value->id;?>" class="deleteThisRow ajax-silent" title="<?=$this->lang->line('application_delete');?>"><i class="ion-close-circled red"></i></a>    
						<?php } ?>
					</td>
                </tr>
				<?php endforeach; ?>
				<tr id="dummyTR" class=" hidden no-print">
					<td class="user_id">
						<?php $pic = get_user_pic($value->user->userpic, $value->user->email); 
						echo "<img src=\"$pic\" class=\"img-circle list-profile-img no-print \" height=\"21px\"> "; ?>
						<label><?=$value->user->firstname;?> <?=$value->user->lastname;?></label>
					</td>
					<td class="time_spent">
						<span class="hours"></span> <?=$this->lang->line('application_hours');?> <span class="minutes"></span> <?=$this->lang->line('application_minutes');?>
					</td>
					
					<td class="start_time" id="starttime">
					</td>
					
					<td class="end_time" id ="testtime">
					</td>
					<td class="option_button">
						<a href="" 
							class="deleteThisRow ajax-silent" title="<?=$this->lang->line('application_delete');?>">
							<i class="ion-close-circled red"></i>
						</a>    
					 </td>
				</tr>
				<tr class="no-print input-fields">
					<input type="hidden" name="task_id" value="<?=$task->id;?>">
					<input type="hidden" name="project_id" value="<?=$task->project_id;?>">
					<td>        
					<?php   
						echo form_dropdown('user_id', $users, $user, '" class="inline-textfield user_id"');
					 ?>
					</td>
					<td>
						<input id="hours" class="inline-textfield hours" type="number" min="0" max="1000" size="3" name="hours" value="00"> <?=$this->lang->line('application_hours');?> 
						<input id ="minutes" class="inline-textfield minutes" type="number" min="0" max="60" size="2" name="minutes" value="00"> <?=$this->lang->line('application_minutes');?>          
					</td>
					<td>
						<label for="start"><?=$this->lang->line('application_start_date');?> *</label>
						<input id ="start_time" class="start_time" type="date" name="start" value="<?php echo date('d/m/Y'); ?>" size="64" required="required" />	
					</td>
				
					<td>
						<input  class="end_time hidden" type="text" name="end" value="<?php echo date('d/m/Y'); ?>" size="64" required="required" />	
					</td>
				
				<td>
					<a onclick="myFunction()" href="<?=base_url()?>projects/timesheet_add/" onclick='maFonction()' class="add-row-ajax" title="<?=$this->lang->line('application_save');?>"><i class="ion-plus-circled"></i></a> <span class="delete_link hidden"></span>
				</td>
				</tr>
			</tbody>
	</table><?php echo form_close(); ?>
</div>
<div class="modal-footer">
	<a class="btn btn-success" href="javascript:printDiv('printtimesheet')"><?=$this->lang->line('application_print');?></a>
	<a class="btn btn-default" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<script>
	printDivCSS =  new String ('<link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/bootstrap.min.css" />')
	function printDiv(divId) {
		window.frames["print_frame"].document.body.innerHTML=printDivCSS + document.getElementById(divId).innerHTML;
		window.frames["print_frame"].window.focus();
		window.frames["print_frame"].window.print();
	}
	function maFonction(){
		$('#tasktable').load(); 
	}
	function myFunction(){	
		alert("ok");
		var hours = document.getElementById('hours').value ; 
		var minutes = document.getElementById('minutes').value ; 
		var startDate = document.getElementById('start_time').value ;
		document.getElementById('starttime').innerHTML = startDate;
		var startdate = moment(startDate);
		var newTime = moment(startdate).add('h',hours );
		newTime = moment(newTime).add('m',minutes );
		newTime = moment(newTime).format('YYYY-MM-DD HH:mm');
		document.getElementById('testtime').innerHTML = newTime;  
		var id = "<?php echo($task->id); ?>";
		console.log(id); 		
		//$('#timer55').load(document.URL + ' #timer55');
	}
	
 </script>

<iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>
