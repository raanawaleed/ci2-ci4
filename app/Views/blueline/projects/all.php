<div class="col-sm-13  col-md-12 main">  
    <div class="row tile-row">
		<div class="col-md-3 col-xs-6 tile">
			<div class="icon-frame "><i class="ion-ios-lightbulb"></i> 
			</div>
			<h1><?php if(isset($projects_assigned_to_me[0])){echo $projects_assigned_to_me[0]->amount;} ?> <span><?=$this->lang->line('application_projects');?></span></h1><h2 ><?=$this->lang->line('application_assigned_to_me');?></h2>
		</div>
    <div class="col-md-3 col-xs-6 tile">
		<div class="icon-frame secondary ">
			<i class="ion-ios-list-outline"></i> </div><h1> <?php if(isset($tasks_assigned_to_me)){echo $tasks_assigned_to_me;} ?> <span><?=$this->lang->line('application_tasks');?></span></h1><h2><?=$this->lang->line('application_assigned_to_me');?></h2>
		</div>
    <div class="col-md-6 col-xs-12 tile ">
        <div style="width:97%; margin-top: -4px; margin-bottom: 17px; height: 80px;">
            <canvas id="tileChart" width="auto" height="80"></canvas>
        </div>
    </div>
</div>   
<div class="row">
	<a href="<?=base_url()?>projects/create" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_create_new_project');?></a>
	<div class="btn-group pull-right margin-right-3">
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
			<?php $last_uri = $this->uri->segment($this->uri->total_segments()); if($last_uri != "projects"){echo $this->lang->line('application_'.$last_uri);}else{echo $this->lang->line('application_open');} ?> <span class="caret"></span>
		</button>
		<ul class="dropdown-menu pull-right" role="menu">
		<?php foreach ($submenu as $name=>$value):?>
				<li><a id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$name?></a></li>
			<?php endforeach;?>
		</ul>
	</div>
	
	
</div>
<div class="row">
	<div class="table-head"><?=$this->lang->line('application_projects');?></div>
		<div class="table-div">
			<table class="dataSorting table" id="projects" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th width="20px" class=""><?=$this->lang->line('application_project_id');?></th>
						<th class="" width="19px" class="no-sort sorting"></th>
						<th><?=$this->lang->line('application_name');?></th>
						<th class=""><?=$this->lang->line('application_client');?></th>
						<th class=""><?=$this->lang->line('application_deadline');?></th>       
						<th class="">Catégorie</th>
						<th class="">Nombre des heures</th>
						<th><?=$this->lang->line('application_action');?></th>
					</tr>
				</thead>
                <tbody>
                <?php foreach ($project as $value): ?>	
            <tr id="<?=$value->id;?>">
					<!-- ID Projet -->
					<td class=""><?=$value->project_num;?></td>
					<!-- sorting -->
					<td class="">
                    <div class="circular-bar tt" title="<?=$value->progress;?>%">
						<input type="hidden" class="dial" data-fgColor="<?php if($value->progress== "100"){ ?>#43AC6E<?php }else{ ?>#11A7DB<?php } ?>" data-width="19" data-height="19" data-bgColor="#e6eaed"  value="<?=$value->progress;?>" >
					</div>
					</td>
					<!-- Nom projet -->
					<td onclick=""><?=$value->name;?></td>
					<!-- Nom client --> 
					<td class="">
						<?php if (!isset($value->txt_client_name)) 
							{echo '<a class="label label-warning">'.$this->lang->line('application_no_client_assigned');}
						else
						{echo '<a class="label label-info">'.$value->txt_client_name;}
						?></a></td>
						<!-- date limite -->
					<td class=""><span class=" label label-success <?php
					if($value->end <= date('Y-m-d') && $value->progress != 100){
						echo 'label-important tt" title="'.$this->lang->line('application_overdue'); 
						} ?>"><?php $unix = human_to_unix($value->end.' 00:00');
						echo '<span class="hidden">'.$unix.'</span> ';
						echo date($core_settings->date_format, $unix);?></span></td>
					<!-- type projet -->
					<td class="">
                    	<?=$value->txt_type_projet; ?>
					</td>
					
					<td class="number-hours">
              <?php if($unite_temps->name === $this->config->item("type_occ_code_unite_temps_jours")) : ?>
                <?=format_temps_jours($tab_heures_pointees, $value);?> 
              <?php else: ?>
                <?=format_temps_heures($tab_heures_pointees, $value);?> 
              <?php endif ?>
          </td>
					<td class="option action">
						<?php if($user->admin ==1) { ?>
				        <a href="<?=base_url()?>projects/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
						<?php } ?>
						<a href="<?=base_url()?>projects/view/<?=$value->id;?>" class="btn-option" ><i class="fa fa-eye"></i></a>
						<?php if($user->admin ==1) { ?>
						<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>projects/delete/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_delete_project');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
						<?php } ?>
					</td>
                </tr>
		        <?php endforeach;?>
            </tbody>
			</table>
		</div>
	</div>
<script>
$(document).ready(function(){ 
$('.dial').each(function () { 
	var elm = $(this);
	var color = elm.attr("data-fgColor");  
	var perc = elm.attr("value");  
          elm.knob({ 
               'value': 0, 
                'min':0,
                'max':100,
                "skin":"tron",
                "readOnly":true,
                "thickness":.25,                 
                'dynamicDraw': true,                
                "displayInput":false
          });

          $({value: 0}).animate({ value: perc }, {
              duration: 1000,
              easing: 'swing',
              progress: function () {                  elm.val(Math.ceil(this.value)).trigger('change')
              }
          });

          //circular progress bar color
          $(this).append(function() {
              elm.parent().parent().find('.circular-bar-content').css('color',color);
              elm.parent().parent().find('.circular-bar-content label').text(perc+'%');
          });

          });
   

//chartjs

var ctx = $("#tileChart").get(0).getContext("2d");

<?php
$days = array(); 
$data = "";
$this_week_days = array(
  date("Y-m-d",strtotime('monday this week')),
  date("Y-m-d",strtotime('tuesday this week')),
	date("Y-m-d",strtotime('wednesday this week')),
	  date("Y-m-d",strtotime('thursday this week')),
		date("Y-m-d",strtotime('friday this week')),
		  date("Y-m-d",strtotime('saturday this week')),
			date("Y-m-d",strtotime('sunday this week')));

$labels = "";
foreach ($projects_opened_this_week as $value) {
  $days[$value->date_formatted] = $value->amount;
}
$counter = 0;
foreach ($this_week_days as $selected_day) { $counter++;
  $labels .= '"'.$selected_day.'"';
  if($counter != 7){$labels .= ",";}
  $y = 0;
	if(isset($days[$selected_day])){ $y = $days[$selected_day];}
	  $data .= $y;
	   if($counter != 7){$data .= ",";}
	  $selday = $selected_day;
	 } ?>
var ctx = document.getElementById("tileChart").getContext("2d");
    var myBarChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [<?=$labels?>],
        datasets: [{
          label: "<?=$this->lang->line('application_new_projects');?>",
          backgroundColor: "rgba(51, 195, 218, 0.3)",
          borderColor: "rgba(51, 195, 218, 1)",
          pointBorderColor: "rgba(51, 195, 218, 0)",
          pointBackgroundColor: "rgba(51, 195, 218, 1)",
          pointHoverBackgroundColor: "rgba(51, 195, 218, 1)",
          pointHitRadius: 25,
          pointRadius: 2,
          borderWidth: 2,
          data: [<?=$data;?>]
        }]
      },
       options: {
        
         title: {
            display: true,
            text: ' '
        },
        maintainAspectRatio: false,
        tooltips:{
          enabled: true,
        },
        legend:{
          display: false
        },
        scales: {
          yAxes: [{
            gridLines: { 
                        display: false, 
                        lineWidth: 2,
                        color: "rgba(51, 195, 218, 0)"
                      },
            ticks: {
                        beginAtZero:true,
                        display: false,
                       
                    }
          }],
          xAxes: [{
             gridLines: { 
                        display: false, 
                        lineWidth: 2,
                        color: "rgba(51, 195, 218, 0)"
                      },
            ticks: {
                        beginAtZero:true,
                        display: false,
                      
                    }
          }]
        }
      }
    });
});
</script>
	