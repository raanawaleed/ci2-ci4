<head>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.1.0/fullcalendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>
<?php 


//var_dump($salaries);exit;
?>
<div class="row">

		<div class="col-sm-3 left">
            <!-- salariés -->
            <div class="form-group">
                <label for="user_filter"><?=$this->lang->line('application_salarie');?>(e)</label>
                <select class="chosen-select" aria-label="Default select example" id="user_filter" >
                        <option value="all" selected>Tous les salariés</option>
                        <?php foreach($salaries as $salarie){
							?>
                            <option value="<?=ucwords(strtolower($salarie->lastname))?> <?=ucwords(strtolower($salarie->firstname))?>"><?=ucwords(strtolower($salarie->lastname))?> <?=ucwords(strtolower($salarie->firstname))?></option>
                        <?php }?>
                </select>
		</div>
    </div>


	
   
	
	<div class="col-sm-3">
            <div class="form-group">
                <label for="project_filter">Projet</label>
                <select class="chosen-select"  id="project_filter" >
				<option value="all" selected>Tous les projets</option>
                        <?php foreach($projects as $project){
							?>
                            <option value="<?=$project->name?>"><?php echo $project->project_num." - ".$project->name; ?></option>
                        <?php }?>
                </select>
		</div>
    </div>
	
		<?php  $idadmin =$this->user->salaries_id ;
				
				if ($idadmin==NULL) {

				?>
					
	<div class="col-sm-3">
            <!-- salariés -->
            <div class="form-group">
                <label for="service_filter">Service</label>
                <select class="chosen-select" aria-label="Default select example" id="service_filter" >
                        <option value="all" selected>Tous les services</option>
						<option value="MMS" >MMS</option>
                        <option value="BIM 2D" >BIM 2D</option>
                        <option value="BIM 3D" >BIM 3D</option>
                </select>
		</div>
    </div>
	
	
			<?php } else {?>
				
				<div class="col-sm-3">
				<div class="form-group">
					             <label for="service_filter">Service</label>
		<?php	$i=1;
  foreach($data as $row)
  { $i++;?>
						  <select class="chosen-select" aria-label="Default select example" id="service_filter" >
								
							
						
   <option selected value="<?php echo $row->seraffectation; ?>"> <?php echo $row->seraffectation; ?></option> 
								<option value="MMS" >MMS</option>
                        <option value="BIM 2D" >BIM 2D</option>
                        <option value="BIM 3D" >BIM 3D</option>
						</select>	<?php }?>
				</div>
			</div><?php }?>
	
	
	<div class="col-sm-3">
	<a href="<?=base_url()?>ctickets" class="btn btn-primary right" >Liste des tâches</a>
	</div>       

</div>       
      




<div class="col-sm-12  col-md-12 main">  

	
<div class="row">

		<div class="table-head"><?=$this->lang->line('application_calendar');?></div>
			<div class="table-div">
				<div id='fullcalendar'>
				</div>
			</div>
      </div>
<?php 

	if($this->input->cookie('fc2language') != ""){$systemlanguage = $this->input->cookie('fc2language');}else{$systemlanguage = $core_settings->language;}
	switch($systemlanguage){
		case "english": $lang = "en"; break;
		case "dutch": $lang = "nl"; break;
		case "french": $lang = "fr"; break;
		case "german": $lang = "de"; break;
		case "italian": $lang = "it"; break;
		case "norwegian": $lang = "no"; break;
		case "polish": $lang = "pl"; break;
		case "portuguese": $lang = "pt"; break;
		case "russian": $lang = "ru"; break;
		case "spanish": $lang = "es"; break;
		default: $lang = "fr"; break;
	}


?>
  


<script type="text/javascript">

//fullcalendar
$(document).ready(function() {

    // page is now ready, initialize the calendar...
    var calendar =$('#fullcalendar').fullCalendar({

		themeSystem: 'bootstrap5',
        lang: '<?=$lang;?>',
        header:{
			left: 'month,agendaWeek,agendaDay,listMonth , listWeek',
            center: 'title',
			right:  ' prev, today ,next ',
			
        },
		unselectAuto: false,
      	selectable: false,
      	selectHelper: false,
		editable: false,
		navLinks: false,
		weekends: false ,

			

        <?php if($core_settings->calendar_google_api_key != "" && $core_settings->calendar_google_event_address != ""){ ?>
         googleCalendarApiKey: '<?=$core_settings->calendar_google_api_key;?>',
      eventSources: [  
      {
        googleCalendarId: '<?=$core_settings->calendar_google_event_address;?>',
        className: 'nice-event',
      }
          ], <?php } ?>
			events: [
				<?php 
					
					if(isset($events_list)) echo $events_list; ?>
/*
				$.ajax({
						url : '<?php echo base_url()?>Calendar_tickets' ,
						type: 'POST',                   
						dataType: 'json',

   				 })
*/
                  ],
			
			
			eventRender: function(event, element ) {

					var user = ['all', event.user].indexOf($('#user_filter').val() ) >= 0;
					var project = ['all', event.description].indexOf($('#project_filter').val()) >= 0;
					var service = ['all', event.service].indexOf($('#service_filter').val()) >= 0;
				
				return user && project && service  ;
	   
            }
					
    });
	

	$('#project_filter').on('change',function(){
	$('#fullcalendar').fullCalendar('rerenderEvents');     
});

	$('#service_filter').on('change',function(){
	$('#fullcalendar').fullCalendar('rerenderEvents');     
});

	$('#user_filter').on('change',function(){
	$('#fullcalendar').fullCalendar('rerenderEvents');     
});

});

</script>



