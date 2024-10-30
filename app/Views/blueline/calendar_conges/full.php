<!DOCTYPE html>
<html>
<head>
<head>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/fullcalendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.2.0/main.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


 <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 14px;
        }

        #calendar {
            max-width: 1100px;
            margin: 40px auto;
        }

        @media (max-width: 767px) {
            /* Adjust calendar styles for screens smaller than 768px width */
            #calendar {
                max-width: 100%!important;
                margin: 10px auto;
            }
        }
    </style>


</head>


<div class="col-sm-12  col-md-12 main">  

	<div class="row">
	<a href="<?=base_url()?>gestionconge" class="btn btn-primary right" >Liste des congés et absences</a>

		<div class="col-sm-3 left">
            <!-- salariés -->
            <div class="form-group">
                <label for="filter"><?=$this->lang->line('application_salarie');?>(e)</label>
                <select id="filter"  class="chosen-select">
                        <option value="all" selected>Tous les salariés</option>
                        <?php foreach($salaries as $salarie){
							?>
                            <option value="<?=$salarie->nom?> <?=$salarie->prenom?>" <?php echo (isset($item)? (($item->id_salarie == $salarie->id)? " selected":""):"") ?>><?=$salarie->nom?> <?=$salarie->prenom?></option>
                        <?php }?>
                </select>
		</div>
    </div>
     </div> </div> 
	
<div class="row">
    <div class="col-md-12">
        <div class="table-head"><?=$this->lang->line('application_calendar');?></div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-div table-responsive!important">
            <div id='fullcalendar'></div>
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
        lang: '<?=$lang;?>',
        nextDayThreshold: '08:00:00',

        header:{
          left: 'month,agendaWeek,agendaDay,listMonth , listWeek',
            center: 'title',
            right:  ' prev, today ,next ',
			//users_events : true ,
			//allDay: true
        },

        <?php if($core_settings->calendar_google_api_key != "" && $core_settings->calendar_google_event_address != ""){ ?>
         googleCalendarApiKey: '<?=$core_settings->calendar_google_api_key;?>',
      <?php } ?>
          weekends: false ,
          minTime: "08:00:00",
           maxTime: "19:00:00",

			events: [

				<?php if(isset($events_list)) echo $events_list;?> 
       

                  ],
			
                  resources: [
                    <?php if(isset($events_list)) echo $events_list;?> 
  ],
			eventRender: function(event, element ) {
           
				var status = false;
        showFilters = event.title.indexOf() >=0 ;

                element.attr('title', event.description);
                    if(event.source.className[0] == "nice-event"){
                element.attr('target', "_blank");
                }         
        //filter
         var filter = $('#filter').val();
         console.log(filter);
         if(filter.lenght !=0)
         {
          if (filter.trim().toLowerCase() == "all") 
              {
                  return showFilters = true ; 


                } 
                else
                {
                  return showFilters = event.title.indexOf(filter) >=0 ;

                } 
        
           
         }

		    if(event.modal == 'true'){
			   element.attr('data-toggle', "mainmodal");
		    } 
		    if(event.description != ''){
				element.attr('title', event.description);
				var tooltip = event.description;
				$(element).attr("data-original-title", tooltip)
				$(element).tooltip({ container: "body", trigger: 'hover', delay: { "show": 300, "hide": 50 }})
		    }
		           
          
            },
                eventClick: function(event) {
                if (event.url && event.modal == 'true') {
					NProgress.start();
					var url = event.url;
					if (url.indexOf('#') === 0) {
						$('#mainModal').modal('open');
					} else {
						$.get(url, function(data) { 
                        $('#mainModal').modal();
                        $('#mainModal').html(data);
                    }).done(function() { NProgress.done();  });
                }


                }
return false ;
            }


    });
	

$('#filter').on('change',function(){
	$('#fullcalendar').fullCalendar('rerenderEvents');     
});

});


</script>
</body>
</html>


