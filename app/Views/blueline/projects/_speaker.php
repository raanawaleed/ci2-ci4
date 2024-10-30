<?php   
$attributes = array('class' => 'dynamic-form', 'data-reload' => 'speaker-list', 'data-reload2' => 'milestones-list', 'data-baseurl' => base_url(), 'id' => '_milestone');
echo form_open($form_action, $attributes); 
?>
<div id="item-selector">
	<div class="form-group focus" style="width: 98%;">
		<label for="name"><?=$this->lang->line('application_name_speaker');?></label>
		<?php $options = array();
		if(!isset($intervenant) && !isset($user)){
			$options['0'] = '-';
		}
		foreach ($intervenants as $value):
			$test = false; 
			foreach ($projectHasWorker as $project):
				if(($project->intervenant_id == $value->id) && ($project->intervenant_id != $intervenant->id)){
					$test = true; 
				}
			endforeach;	
			if($test == false){
				$options['inter'.$value->id] = $value->name.' '.$value->surname; 
			}
		endforeach;
		
		foreach ($users as $value):
			$test = false; 
			foreach ($projectHasWorker as $project):
				if(($project->user_id == $value->id) && ($project->user_id != $user->id)){
					$test = true; 
				}
			endforeach;	
			if($test == false){
				$options["user".$value->id] = $value->firstname.' '.$value->lastname;
			}
		endforeach; 
		if($intervenant != null){ 	
			$intervenantId = 'inter'.$intervenant->id; 
			echo form_dropdown('intervenant_id', $options, $intervenantId, 'id="intervenant_id" class="chosen-select description-setter style="width:100%"" ');
		} else if($user != null){
			$userId = 'user'.$user->id; 
			echo form_dropdown('intervenant_id', $options, $userId, 'id="intervenant_id" class="chosen-select description-setter style="width:100%""');
		}else {
			echo form_dropdown('intervenant_id', $options, '', 'id="intervenant_id" class="chosen-select description-setter style="width:100%""');
	}?>
		<a class="btn btn-primary tt addspeaker" titel="<?=$this->lang->line('application_custom_item');?>"><i class="fa fa-plus"></i></a> 
	</div>     
	<!--<div class="form-group">
		<label for="value"><?=$this->lang->line('application_value');?></label>
		<input id="value" type="number" min="0" step="0.01" name="value" class="form-control resetvalue" value="<?=$projectIntervenant->value?>"  />
	</div>-->	  
</div>
<div id="item-editor">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="name"><?=$this->lang->line('application_name');?></label>
				<input id="name" type="text" name="name" class="form-control resetvalue" value=""   />
			</div> 
		</div> 
		<div class="col-md-6">
			<div class="form-group">
				<label for="surname"><?=$this->lang->line('application_surname');?></label>
				<input id="surname" type="text" name="surname" class="form-control resetvalue" value=""  />
			</div>
		</div> 
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="adress"><?=$this->lang->line('application_adress_user');?></label>
				<input id="adress" type="text" name="adress" class="form-control resetvalue" value="" />
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="email"><?=$this->lang->line('application_email');?></label>
				<input id="email" type="email" name="email" class="form-control resetvalue" value=""  />
			</div> 
		</div> 
	</div> 
   <!-- valeur de l'intervention dans ce projet -->
   <?php foreach($projectHasWorker as $project){
	   if($project->intervenant_id == $intervenant->id){
		   $val = $project->value; 
	   }
   } ?>
	<!--<div class="form-group">
		<label for="new_value"><?=$this->lang->line('application_value');?></label>
		<input id="new_value" type="number" min="0" step="0.01" name="new_value" class="form-control resetvalue" value=""  />
	</div>	 -->  
	
</div>
<div class="modal-footer">
	<button name="send" class="btn btn-primary send button-loader"><?=$this->lang->line('application_save');?></button>
</div>

<?php echo form_close(); ?>

<script>
$('.addspeaker').click(function(e)
{
  $('#item-selector').slideUp('fast');
  $('#item-editor').delay(300).slideDown('fast');
  document.getElementById("name").required = true ;
  document.getElementById("surname").required = true;
  document.getElementById("value").required = true;
  $('form').validator();
 });

function testfuntion() {
	var hashcode = window.location;
	$('html,body').animate({scrollTop: $('milestones-tab#'+hascode).offset().top},'slow');
}
$('#intervenant_id').on('change',function (){
	var speaker = document.getElementById("intervenant_id").value;
	$.ajax({
		type: 'POST',
		dataType: "text",
		url:  decodeURIComponent('../getSpeakerValue/' + speaker), 
		dataType: 'json',
		cache: false,
		contentType: false,
		processData: false,
		success: function (response) {  
			document.getElementById("value").value  = response;
		}
	});
});

</script>