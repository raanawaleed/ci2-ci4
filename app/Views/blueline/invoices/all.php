<div class="col-sm-12  col-md-12 main">  
    <!-- statistiques sur les factures -->
    <div class="row tile-row">
		<div class="col-md-2 col-xs-12 tile blue"><h1><span><?=$this->lang->line('application_invoices');?></span></h1>
		</div>
		<div class="col-md-3 col-xs-3 tile"><div class="icon-frame hidden-xs"><i class="ion-ios-bell"></i> </div><h1> <?php if(isset($invoices_due_this_month)){echo $invoices_due_this_month;} ?> <span><?=$this->lang->line('application_invoices');?></span></h1><h2><?=$this->lang->line('application_due_this_month');?></h2>
		</div>
		<div class="col-md-3 col-xs-3 tile"><div class="icon-frame secondary hidden-xs"><i class="ion-ios-analytics"></i> </div><h1> <?php if(isset($invoices_paid_this_month)){echo $invoices_paid_this_month;} ?> <span><?=$this->lang->line('application_invoices');?></span></h1><h2><?=$this->lang->line('application_paid_this_month');?></h2>
		</div>
		<div class="col-md-3 col-xs-3 tile hidden-xs">
			<div style="width:97%; margin-top: -4px; margin-bottom: 17px; height: 80px;">
				<canvas id="tileChart" width="auto" height="80"></canvas>
			</div>
		</div>
	</div>
	<!-- boutons d'actions -->   
	<div class="row">
    <a href="<?=base_url()?>invoices/create" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_create_invoice');?></a>
    <a type="button" class="btn btn-success" href="<?=base_url()?>exporter/factures_as_excel"><?=$this->lang->line('application_export')?></a>
				<a href="https://vision.bimmapping.com/exportFacture/indexx.php"  class="btn btn-primary" data-toggle="mainmodal">Exporter 2</a>

    <div class="btn-group pull-right-responsive margin-right-3">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
            <?php $last_uri = $this->uri->segment($this->uri->total_segments());
				
				if($last_uri != "invoices" && is_numeric($last_uri) == false){
					if($this->lang->line('application_'.$last_uri) == false) { 
						echo $last_uri; 
					} else {
						echo $this->lang->line('application_'.$last_uri);
					}
					}else{echo $this->lang->line('application_all');} ?> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right" role="menu">
            <?php foreach ($submenu as $name=>$value):?>
	            <li><a id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>"
				<?php $filter = substr($val_id[1], 0, 6);
					$val_id[1] = str_replace($filter , '', $val_id[1]);
						$value = $val_id[0].'/'.$filter.'/'.$val_id[1];  ?> href="<?=site_url($value);?>"><?=$name?></a></li>
				<?php endforeach;?>
        </ul>
    </div>
	<?php $years = array_combine(range(date("Y"), 2013), range(date("Y"),2013)); ?>
	<div class="btn-group pull-right-responsive margin-right-3">
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">	
			<?php $last_uri = $this->uri->segment($this->uri->total_segments());			
				if($last_uri != "invoices" && is_numeric($last_uri) == true){
					echo  $last_uri; 
				}else
				{
					echo  date("Y");
				} ?> <span class="caret"></span>
		</button>
		<ul class="dropdown-menu pull-right" role="menu">
			<?php foreach($years as $year){ ?>
						<li><a href="<?=base_url()?>invoices/filter/False/<?=$year?>"><?=$year;?></a></li>
			<?php  }?>
          </ul>
    </div>	
</div>
	
	<!-- tableau de la liste des factures -->
	<div class="row">
	<div class="table-head"><?=$this->lang->line('application_invoices');?></div>
		<div class="table-div">
			<table class="dataSorting table" id="invoices" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
				<thead>
					<th><?=$this->lang->line('application_pdf')?></th>
					<th hidden></th>
					<th width="70px" class="hidden-xs"><?=$this->lang->line('application_invoice_id');?></th>
					<th><?=$this->lang->line('application_client');?></th>
					<th><?=$this->lang->line('application_asset');?></th>
					<th class="hidden-xs"><?=$this->lang->line('application_issue_date');?></th>
					<th class="hidden-xs"><?=$this->lang->line('application_currency');?></th>
					<th class="hidden-xs"><?=$this->lang->line('application_total_ht');?></th>
					<th class="hidden-xs"><?=$this->lang->line('application_total_ttc');?></th>
					<th>Etat</th>
					<th><?=$this->lang->line('application_action');?></th>
				</thead>
				<?php foreach ($invoices as $value):?>

				<tr id="<?=$value->id;?>" >
				<td class="option" width="6%">
					<a target="_blank" href="<?=base_url()?>invoices/preview/<?=$value->id;?>/show" class="btn-option"><i class="" title="PDF"><img src="<?=base_url()?>assets/blueline/images/pdf.png" alt=""></i></a>
				</td>
				<td class="hidden-xs" hidden><?=$value->id;?></td>
				<td class="hidden-xs"><?=$value->estimate_num;?></td>
				<td class=" 
					<?php 
					$this->load->helper('mydbhelper_helper');
					$company = getcompany($value->company_id);
					echo ' " title="'.$company->name; ?>">
					<span class="label label-info">
					<?php 
						echo $company->name; 
					?>
					</span>
				</td>
				<td class=" <?php echo '" title="'.$value->subject; ?>"
								><?php $max = 15;
						 if (strlen($value->subject) >= $max) {
						$subject = substr($value->subject, 0, $max).'...';
						 }else{
							$subject = $value->subject; 
						 }
						 echo $subject;
				?>  </td>
				<td class="hidden-xs"><span><?php $unix = human_to_unix($value->issue_date.' 00:00'); 
				echo '<span class="hidden">'.$unix.'</span> '; echo date($core_settings->date_format, $unix);?></span></td>
				<td class="hidden-xs"><span class="label"><?=$value->currency;?></span></td>
				<!-- TOTAL TTC -->
				<td class="hidden-xs nowrap"><?php echo number_format($value->sumht,$value->chiffre,'.',' ');?></td>
				<?php $this->load->helper('mydbhelper_helper');
				$company = getcompany($value->company_id);
				$TTC = $value->sum ; 
				$TTH = $value->sumht; 
				if($value->deduction > 0){
					$TTC = $TTC - ($TTC-$value->timbre_fiscal)*$value->deduction/100; 
					$TTH = $TTH - ($TTH*$value->deduction)/100;
				}
				if($company->timbre_fiscal == 0){ 
					$TTH = $TTH + $value->timbre_fiscal; 
				}
				 if ($company->tva == 1)  { ?>
					<td class="hidden-xs nowrap">	
						<?php if(isset($TTH)){echo  number_format($TTH,$value->chiffre,'.',' ');} ?> 
					</td>
				<?php } else { ?>
					<td class="hidden-xs nowrap"><?php if(isset($TTC)){echo  number_format($TTC,$value->chiffre,'.',' ');} ?> </td>
				<?php } ?>
				
				<!-- Etats de la facture -->
				<td class="<?=$value->status?>"><?php get_etat_color($value->status) ?></td>
				
				<td class="option" width="12%">
					<div class="dropdown">
						<button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
						 <i class="fa fa-cogs"></i> Outils
						<span class="caret"></span></button>
						<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
						<?php if ($value->status == $this->config->item("occ_facture_paye") 
							|| $value->status == $this->config->item("occ_facture_p_paye")  
							|| $value->status == $this->config->item("occ_facture_avoir") ) { ?>
						  <li><a style= "background-Color:grey; cursor:none;" data-toggle="mainmodal"><i class="fa fa-edit" title="Facture payée" readonly> Modifier</i></a></li>
						<?php } else {?>  
						  <li role="presentation"><a href="<?=base_url()?>invoices/update/<?=$value->id;?>" class="btn-option"  data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"> Modifier</i></a></li>
						<?php } ?>
						  <li role="presentation"><a href="<?=base_url()?>invoices/view/<?=$value->id;?>" class="btn-option"><i class="fa fa-eye" title="visualisez"> Visualisez</i></a></li>
						  <li role="presentation"><a  href="<?=base_url()?>invoices/duplicate/<?=$value->id?>" class="btn-option"><i class="fa fa-clone" aria-hidden="true" title="Dupliquer"> Dupliquer</i></a></li>
						  <!-- trasform to avoir -->
						  <li role="presentation"><a  href="<?=base_url()?>invoices/PassToAvoir/<?=$value->id?>" class="btn-option"><i class="fa fa-cog" aria-hidden="true" title="Dupliquer"> <?=$this->lang->line('application_avoir');?></i></a></li>
						  <!--sent mail-->
						  <li role="presentation"><a  href="<?=base_url()?>invoices/sendfiles/<?=$value->id?>" class="btn-option"><i class="fa fa-envelope" aria-hidden="true" title="Sent"> <?=$this->lang->line('application_sent_to');?></i></a></li>
						</ul>
					</div>
				</td>
			</tr>
			<?php endforeach;?>
		</table>
    </div>
</div>

</div>
<script>
$(document).ready(function(){ 
//chartjs
<?php
	$days = array(); 
	$data = "";
	$data2 = "";
	$this_week_days = array(
	  date("Y-m-d",strtotime('monday this week')),
	  date("Y-m-d",strtotime('tuesday this week')),
		date("Y-m-d",strtotime('wednesday this week')),
		  date("Y-m-d",strtotime('thursday this week')),
			date("Y-m-d",strtotime('friday this week')),
			  date("Y-m-d",strtotime('saturday this week')),
				date("Y-m-d",strtotime('sunday this week')));

	$labels = '';

	//First Dataset            
	foreach ($invoices_paid_this_month_graph as $value) {
	  $days[$value->date_formatted] = $value->amount;
	}
	foreach ($this_week_days as $selected_day) {
	  $y = 0;
	  $labels .= '"'.$selected_day.'",';
		if(isset($days[$selected_day])){ $y = $days[$selected_day];}
		  $data .= $y.",";
		  $selday = $selected_day;
		 } 

	//Second Dataset
	foreach ($invoices_due_this_month_graph as $value) {
	  $days[$value->date_formatted] = $value->amount;
	}
	foreach ($this_week_days as $selected_day2) {
	  $y = 0;
		if(isset($days[$selected_day2])){ $y = $days[$selected_day2];}
		  $data2 .= $y.",";
		  $selday2 = $selected_day2;
		 }
?>
var ctx = document.getElementById("tileChart").getContext("2d");
    var myBarChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [<?=$labels?>],
        datasets: [
        {
          label: "<?=$this->lang->line('application_paid');?>",
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
