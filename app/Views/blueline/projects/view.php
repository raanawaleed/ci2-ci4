<?php //var_dump($subject); ?>


<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="row tile-row tile-view">
			<div class="col-md-1 col-xs-3">
				<div class="percentage easyPieChart" id="tile-pie" data-percent="<?=$project->progress;?>"><span><?=$project->progress;?>%</span>
				</div>
			</div>
			<div class="col-md-7 col-xs-9 smallscreen">
				<h1><span class="nobold">#<?=$project->project_num;?></span> - <?=$project->name;?></h1>
				<p class="truncate description"><?=$project->description;?></p>

			</div>
			<div class="col-md-4">
				<a style="margin-top: 22px;" href="<?=base_url()?>projects" class="btn btn-warning right">Liste des <?=$this->lang->line('application_projects'); 
				
				?></a>
			</div>
			<ul class="nav nav-tabs" role="tablist" style="clear:both;">
			<?php
?>
				<?php foreach($current_user as $row): ?>

						<li role="presentation" class="hidden-xs"><a href="#projectdetails-tab" aria-controls="projectdetails-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_project_details');?></a></li>

				<?php endforeach; ?>
				<li role="presentation" class="hidden-xs"><a href="#sub-projetcs-tab" aria-controls="sub-projetcs-tab" role="tab" data-toggle="tab" ><?=$this->lang->line('application_sous_projets');?></a></li>
				<li role="presentation" class="hidden-xs"><a href="#milestones-tab" aria-controls="tasks-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_milestones');?></a></li>
				<li role="presentation" class="hidden-xs"><a href="#gantt-tab" class="resize-gantt" aria-controls="gantt-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_gantt');?></a></li>
				<!--<li role="presentation" class="hidden-xs"><a href="#media-tab" class="media-tab-trigger" aria-controls="media-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_media');?></a></li>
				<li role="presentation" class="hidden-xs"><a href="#notes-tab" aria-controls="notes-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_notes');?></a></li>-->
				<?php foreach($current_user as $row): ?>
					<?php if($row->admin == 1 && $invoice_access == true ){ ?>
						<li role="presentation" class="hidden-xs">
							<a href="#invoices-tab" aria-controls="invoices-tab" role="tab" data-toggle="tab">
								<?=$this->lang->line('application_invoices');?>
								</a>
							</li>
					<?php } ?>
				<?php endforeach; ?>

					<li role="presentation" class="hidden-xs"><a href="#stat-tab" aria-controls="stat-tab" role="tab" data-toggle="tab">Statistique</a></li>

				<li role="presentation" class="hidden-xs"><a href="#activities-tab" aria-controls="activities-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_activities');?></a>
				</li>
				<!-- copier un projet -->
				<li class="pull-right">
					<a href="<?=base_url()?>projects/copy/<?=$project->id;?>" class="btn-option tt" title="<?=	$this->lang->line('application_copy_project');?>" data-toggle="mainmodal"><i class="fa fa-copy"></i></a>
				</li>
				<?php if($user->admin ==1) { ?>
				<!--
				<li class="pull-right">
					<a href="<?=base_url()?>projects/update/<?=$project->id;?>" data-toggle="mainmodal" data-target="#mainModal"><i class="fa fa-edit" title="Modifier"></i></a>
				</li> -->
				<?php } ?>
				<!--<li class="pull-right">
					<?php if(!empty($project->tracking)){ ?>
						<a href="<?=base_url()?>projects/tracking/<?=$project->id;?>" class="tt red" title="<?=$this->lang->line('application_stop_timer');?>" ><span id="timerGlobal" class="badge"></span></a>
						<script>$( document ).ready(function() { startTimer("","<?=$timertime;?>", "#timerGlobal"); });</script>
					<?php }else{ ?>
						<a href="<?=base_url()?>projects/tracking/<?=$project->id;?>" class="tt green" title="<?=$this->lang->line('application_start_timer');?>"><i class="fa fa-clock-o"></i> </a>
					<?php } ?>
				</li>-->
			</ul>
		</div>
	</div>
</div>

<!-- le contenu des onglets -->
<div class="tab-content">
	<!-- détail du projet -->
	<div class="row tab-pane active" role="tabpanel" id="projectdetails-tab">
		<!-- détails du projet -->
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-3">
			<div class="table-head"><?=$this->lang->line('application_project_details');?>
				<?php if($user->admin ==1) { ?>
				<span class=" pull-right option-icon">
					<a href="<?=base_url()?>projects/update/<?=$project->id;?>" data-toggle="mainmodal" data-target="#mainModal"><i class="fa fa-edit" title="Modifier le projet"></i>
					</a>
				</span>
				<?php } ?>
			</div>
			<div class="subcont">
				<ul class="details col-xs-12 col-sm-12">
					<li><span><?=$this->lang->line('application_project_id');?></span><?=$project->project_num;?></li>
					<li><span>Catégorie projet</span><?=$type_projet[0]->name;?></li>
					<li><span>Nature projet</span><?=$nature_projet[0]->name;?></li>
					<li><span><?=$this->lang->line('application_estimate_id');?></span><?=$project->ref_projet;?></li>
					<li><span><?=$this->lang->line('application_client');?></span>
						<?php if(!isset($project->company_id->name)){ ?> <a href="#" class="label label-default">
							<?php echo $this->lang->line('application_no_client_assigned'); }else{ ?>
							<a class="label label-info" href="<?=base_url()?>clients/view/
				<?=$project->company_id->id;?>">
								<?php $max = 28;
								if (strlen($project->company_id->name) >= $max) {
									$chaine = substr($project->company_id->name, 0, $max).'...';
								}else{
									$chaine = $project->company_id->name;
								}
								echo $chaine;}?></a></li>
					<li><span>Chef Projet</span><p class="label label-warning"> 
						<?php foreach ($chef_projet as $value):	
								$nom = $value->firstname.' '.$value->lastname; 
						endforeach;
							 if (!$nom) echo ('Veuilez choisir un chef projet'); else echo $nom;?></p></li> 
					
					<li><span>Chef Projet Client</span><p class="label label-warning">
						<?php foreach ($chef_client as $value):
									
								$nomclient = $value->firstname.' '.$value->lastname; 
						endforeach;
								






							 if (!$nomclient) echo ('Veuilez choisir un chef projet'); else echo $nomclient;?></p></li>			
		<!--	<li><span><?=$this->lang->line('application_start_date');?></span> <?php  $unix = human_to_unix($project->start.' 00:00'); echo date($core_settings->date_format, $unix);?></li>
				-->	<li><span>Etat du projet</span><p class="label label-info"> <?=(isset($etats_projet[$project->etat_projet])? $etats_projet[$project->etat_projet]->name: '');?></p></li>
					<li><span><?=$this->lang->line('application_start_date');?></span> <?php  $unix = human_to_unix($project->start.' 00:00'); echo date($core_settings->date_format, $unix);?></li>
						<li><span><?=$this->lang->line('application_end_date');?></span> <?php  $unix = human_to_unix($project->end.' 00:00'); echo date($core_settings->date_format, $unix);?></li>
				
					<li><span>Date de Livraison</span><p > <?php  $unix = human_to_unix($project->delivery.' 00:00'); 
					if ($unix != false) echo date($core_settings->date_format, $unix); else echo ("Date non définie")?></li>
					<li><span><?=$this->lang->line('application_tasks_time_spent');?></span>

							
							<?php		if($type_projet[0]->name == 'MMS'){
								foreach ($subject as $value) :

									$rendement =($value['longueur']/(int)($projet_heures_pointees->nb_heures));
									$total_rendement+=$rendement; 
									$total_surface+=$value['longueur']; 
								endforeach;
							}else{
								foreach ($subject as $value) :

									$rendement =($value['surface']/(int)($projet_heures_pointees->nb_heures));
									$total_rendement+=$rendement; 
									$total_surface+=$value['surface']; 
								endforeach;
							}


						 /*if($unite_temps->name === $this->config->item("type_occ_code_unite_temps_jours")) : ?>
							<span><p class="label label-info"><?=format_temps_jours($projet_heures_pointees);?></span>
			            <?php else: ?>*/?>
							<span><p class="label label-info"><?
							$nombre_arrondi = round($totheures->periode, 1);

$partie_decimale = $nombre_arrondi - floor($nombre_arrondi);

if ($partie_decimale >= 0.5 && $partie_decimale < 0.6) {
    $nombre_arrondi = floor($nombre_arrondi) + 0.5;
}

							 $periode_text = str_replace(".5", ":30 ", $nombre_arrondi);
							 echo $periode_text;?></span>
					</li>
<li><span>Quantité :</span>
  <p class="label label-info">
    <?=$total_surface; ?>
    <?php if ($type_projet[0]->name == 'MMS') { ?>
      ml
    <?php } else { ?>
      m²
    <?php } ?>
  </p>
</li>
					<li><span>Total Rendement :</span><p class="label label-info"><?=round($total_rendement,3)?> m²/H  </p></span></li>
					
					<!--<li><span>CRÉÉ PAR <br><p class="label label-info"><?=($chef_projet[0]->firstname.' '.$chef_projet[0]->firstname);?></p> </span></li>-->
					<li><span>CRÉÉ LE <br><p class="label label-info"> <?php  echo date($core_settings->date_format.' '.$core_settings->date_time_format, $project->datetime); ?></p></span></li>

				</ul>
				<br clear="both">
			</div>
		</div>
		<!-- statistiques du projet + tâches + Devis+ factures -->
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
			<div class="row">
				<div class="col-sm-12">
					<div class="table-head"><?=$this->lang->line('application_project_statistic');?> </div>
					<div class="tile-base no-padding">
						<div class="tile-extended-header">
							<div class="grid tile-extended-header">
								<div class="grid__col-6">
									<h5>Statistiques pour</h5>
									<h1>Cette semaine</h1>
								</div>
								<div class="grid__col-6">
									<div class="grid grid--bleed grid--justify-end">
									</div>
								</div>
								<div class="grid__col-12 grid__col--bleed grid--align-self-end">
									<div class="tile-body">
										<canvas id="projectChart" width="auto" height="80" style="margin-bottom:-5px"></canvas>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- les tâches -->
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="stdpad" >
							<div class="table-head"><?=$this->lang->line('application_tasks');?></div>
							<table id="tasks" class="data-no-search table" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
								<thead>
								<?php //var_dump($surface[0]);?>
									<th class="">ID</th>
									<th class="">Sujet</th>
									<th class="">Propriétaire</th>
									<th class="">Quantité</th>
									<th class="">Temps</th>
									<th class="">Rendement</th>



								</thead>
								
								<?php foreach ($subject as $value) :?>
								
								
								
									<tr id="<?=$value['id'];?>">
										<td class="id"><?=$value['id']; ?></td>
										<td><a href="<?=base_url()?>ctickets/view/<?=$value['id']?>"><?=$value['subject']; ?></a></td>
										<td><span class="label label-info"><?=$value['firstname'].' '.$value['lastname']?></span></td>
										<td>
										<?php if($type_projet[0]->name == 'MMS'){ ?>
											<span class="label label-info">
													<?=$value['longueur'];?>
											</span>
										<?php }else { ?>
											<span class="label label-info">
													<?=$value['surface'];?>
											</span>
										<?php } ?>
										</td>
									
										<td>
<span class="label label-info">
    <?php
    $periodee = (float)$this->ticket_model->getPeriodPerTicket($value['id'])->periode;
   
  
							$nombre_arrondi = round($periodee, 1);

$partie_decimale = $nombre_arrondi - floor($nombre_arrondi);

if ($partie_decimale >= 0.5 && $partie_decimale < 0.6) {
    $nombre_arrondi = floor($nombre_arrondi) + 0.5;
}

							 $periode_text = str_replace(".5", ":30 ", $nombre_arrondi);
							 echo $periode_text;?>


  
	
	
	
</span>
										</td>
										
								<?php 
										//var_dump((int)$projet_heures_pointees->nb_heures);
									if ($type_projet[0]->name == 'MMS') {
    $periode = $this->ticket_model->getPeriodPerTicket($value['id'])->periode;
    $rendement = $value['longueur'] / (int)$periode;
    $total_rendement += $rendement;
    $total_surface += $value['longueur'];
} else {
    $periode = $this->ticket_model->getPeriodPerTicket($value['id'])->periode;
    $rendement = $value['surface'] / (int)$periode;
    $total_rendement += $rendement;
    $total_surface += $value['surface'];
}
										?>
										<td><span  class="label label-info">
										<?=round($rendement,3);?>
										</span></td>
									

									</tr>
									<?php endforeach;
									//var_dump($value);?>


									
									
							</table>
						</div>
				</div>
			</div>



	
	
	
			<!-- les Devis -->
			<?php  if($invoice_access == true ){ ?>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="stdpad" >
							<div class="table-head">Devis</div>
							<table id="invoices" class="data-no-search table" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
								<thead>
								<th width="hidden-xs"><?=$this->lang->line('application_estimate_id');?></th>
								<th class="hidden-xs"><?=$this->lang->line('application_issue_date');?></th>
								<th class="hidden-xs"><?=$this->lang->line('application_total_ttc');?></th>
								</thead>
								<?php foreach ($devis as $value):?> 
								<tr id="<?=$value->invoice_id->id;?>" >
									<!-- Référence -->
									<td class="hidden-xs"><?=$value->estimate_num;?></td>
									<!-- Date création -->
									<td class="hidden-xs"><?php  $unix = human_to_unix($value->creation_date.' 00:00'); echo date($core_settings->date_format, $unix);?></td>
									<!-- Total ttc --> 
									<td class="hidden-xs">
										<?php if ($value->currency=='TND') $chiffre='3'; 
											  elseif ($value->currency=='Euro') $chiffre='2'; 
										echo display_money($value->sum,"",$chiffre); 
										?>	
									</td>
									<?php endforeach;?>
							</table>
						</div>
					</div>
				</div>

			<?php } ?>

			<!-- les factures -->
			<?php  if($invoice_access == true ){ ?>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="stdpad" >
							<div class="table-head">Factures</div>
							<table id="invoices" class="data-no-search table" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
								<thead>
								<th width="hidden-xs"><?=$this->lang->line('application_estimate_id');?></th>
								<th class="hidden-xs"><?=$this->lang->line('application_issue_date');?></th>
								<th class="hidden-xs"><?=$this->lang->line('application_total_ttc');?></th>
								</thead>
								<?php foreach ($project_has_invoices as $value):?>
								<tr id="<?=$value->invoice_id->id;?>" >
									<!-- Référence -->
									<td class="hidden-xs"><?=$value->estimate_num;?></td>
									<!-- Date création -->
									<td class="hidden-xs"><?php  $unix = human_to_unix($value->creation_date.' 00:00'); echo date($core_settings->date_format, $unix);?></td>
									<!-- Total ttc -->
									<td class="hidden-xs">
										<?php  if ($value->currency=='TND') $chiffre='3'; 
											   elseif ($value->currency=='Euro') $chiffre='2'; 
										echo display_money($value->sum,"",$chiffre);
									    ?>
									</td> 
									<?php endforeach;?>
							</table>
						</div>
					</div>
				</div>

			<?php } ?>
			<div class="row">
				<div class="col-sm-12 col-md-4">
					<div class="tile-base tile-with-icon">
						<div class="tile-icon hidden-md hidden-sm" style="margin: -11px 36px 2px 0px;"><i class="ion-ios-people-outline"></i>
						</div>
						<div class="tile-small-header">
							<?=$this->lang->line('application_staff_assigned');?>
						</div>
						<div class="tile-body">
							<div class="number" id="number1">
								<?=$assigneduserspercent?> %
							</div>
						</div>
						<div class="tile-bottom">
							<div class="progress tile-progress tile-progress--red" >
								<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: <?=$assigneduserspercent?>%">
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-md-4">
					<div class="tile-base tile-with-icon">
						<div class="tile-icon hidden-md hidden-sm"><i class="ion-ios-list-outline"></i></div>
						<div class="tile-small-header">
							<?=$this->lang->line('application_open_tasks');?>
						</div>
						<div class="tile-body">
							<div class="number" id="number1">
								<?=$opentasks?><small> / <?=$alltasks?></small>
							</div>
						</div>
						<div class="tile-bottom">
							<div class="progress tile-progress tile-progress--purple" >
								<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: <?=$opentaskspercent?>%"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Activités -->
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-3">
			<div class="stdpad" >
				<div class="table-head"><?=$this->lang->line('application_activities');?></div>
				<div id="main-nano-wrapper" class="nano">
					<div class="nano-content">
						<ul class="activity__list">
							<?php
							foreach ($project->project_has_activities as $value) { ?>
								<li>
									<h3 class="activity__list--header">
										<?php echo time_ago($value->datetime); ?>
									</h3>
									<p class="activity__list--sub truncate">
										<?php if(isset($value->user->id))
										{
										//	echo $value->user->name." ".$value->user->surname.' <a href="'.base_url().'projects'.$value->project->id.'">'.$value->project->name."</a>";
										} ?>
									</p>
									<div class="activity__list--body">
										<?=character_limiter(str_replace(array("\r\n", "\r", "\n",), "",strip_tags($value->message)), 260); ?>
									</div>
								</li>
								<?php $activities = true; } ?>
							<?php if(!isset($activities)) { ?>
								<div class="empty">
									<i class="ion-ios-people"></i><br>
									<?=$this->lang->line('application_no_recent_activities');?>
								</div>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		</div>

	</div>

	<!-- JALONS -->
		<div class="row tab-pane fade" role="tabpanel" id="milestones-tab">
			<div class="col-xs-12 col-sm-12 col-lg-6">
				<div class="table-head"><?=$this->lang->line('application_milestones');?>
					<span class=" pull-right">
				  <a href="<?=base_url()?>projects/milestones/<?=$project->id;?>/add" class="btn btn-primary" data-toggle="mainmodal">
					  <?=$this->lang->line('application_add_milestone');?>
				  </a>
			 </span>
				</div>
				<div class="subcont no-padding min-height-410">
					<ul id="milestones-list" class="todo sortlist sortable-list2">
						<?php  $count = 0;
						foreach ($project->project_has_milestones as $milestone):
							$count2 = 0; $count = $count+1; ?>
							<li id="milestoneLI_<?=$milestone->id;?>" class="hasItems">
								<h1 class="milestones__header ui-state-disabled">
									<i class="ion-android-list milestone__header__icon"></i>
									<?=$milestone->name?>
									<span class="pull-right">
						  <a href="<?=base_url()?>projects/milestones/<?=$milestone->project_id;?>/update/<?=$milestone->id;?>" data-toggle="mainmodal"><i class="ion-ios-gear milestone__header__right__icon"></i></a>
						</span>
								</h1>
								<ul id="milestonelist_<?=$milestone->id;?>" class="sortable-list">
									<?php  foreach ($milestone->project_has_tasks as $value):   $count2 =  $count2+1;  ?>
										<li id="milestonetask_<?=$value->id;?>" class="<?=$value->status;?> priority<?=$value->priority;?> list-item">
											<a href="<?=base_url()?>projects/tasks/<?=$project->id;?>/check/<?=$value->id;?>" class="ajax-silent task-check"></a>
											<input name="form-field-checkbox" class="checkbox-nolabel task-check dynamic-reload" data-reload="tile-pie" type="checkbox" data-link="<?=base_url()?>projects/tasks/<?=$project->id;?>/check/<?=$value->id;?>" <?php if($value->status == "done"){echo "checked";}?>/>
							<span class="lbl">
								<p class="truncate name"><?=$value->name;?></p>
							</span>
							<span class="pull-right">
							<?php if ($value->user_id != 0) {  ?><img class="img-circle list-profile-img tt"  title="<?=$value->intervenant->name;?> <?=$value->intervenant->surname;?>"  src="<?=get_user_pic($value->intervenant->userpic, $value->intervenant->email);?>"><?php } ?>
								<?php if ($value->public != 0) {  ?><span class="list-button"><i class="fa fa-eye tt" title="" data-original-title="<?=$this->lang->line('application_task_public');?>"></i></span><?php } ?>
								<a href="<?=base_url()?>projects/tasks/<?=$project->id;?>/update/<?=$value->id;?>" class="edit-button" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
							</span>

										</li>
									<?php endforeach;?>
									<?php if($count2 == 0){?>
										<li class="notask list-item ui-state-disabled"><?=$this->lang->line('application_no_tasks_yet');?></li>
									<?php }?>
								</ul>
							</li>
						<?php endforeach;?>
						<?php if($count == 0) { ?>
							<li class="notask list-item ui-state-disabled"><?=$this->lang->line('application_no_milestones_yet');?></li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-lg-6">
				<div class="table-head">
					<?=$this->lang->line('application_tasks_without_milestone');?>
				</div>
				<div class="subcont no-padding min-height-410">
					<ul id="task-list2" class="todo sortable-list">
						<?php $count3 = 0;
						foreach ($tasksWithoutMilestone as $value):
							$count3 =  $count3+1;  ?>
							<li id="milestonetask_<?=$value->id;?>" class="<?=$value->status;?> priority<?=$value->priority;?> list-item">
								<a href="<?=base_url()?>projects/tasks/<?=$project->id;?>/check/<?=$value->id;?>" class="ajax-silent task-check"></a>
								<input name="form-field-checkbox" class="checkbox-nolabel task-check dynamic-reload" data-reload="tile-pie" type="checkbox" data-link="<?=base_url()?>projects/tasks/<?=$project->id;?>/check/<?=$value->id;?>" <?php if($value->status == "done"){echo "checked";}?>/>
					<span class="lbl">
						<p class="truncate name"><?=$value->name;?></p>
					</span>
								<!-- to check-->
							</li>
						<?php endforeach ?>
						<?php if($count3 == 0){ ?>
							<li class="notask list-item ui-state-disabled"><?=$this->lang->line('application_no_tasks_without_milestone');?></li>
						<?php }?>
					</ul>
				</div>
			</div>
		</div>
	<!-- Gant -->
		<div class="row tab-pane fade" role="tabpanel" id="gantt-tab">
			<div class="col-xs-12 col-sm-12">
				<div class="table-head">
					<?=$this->lang->line('application_gantt');?>
					<span class="pull-right">
            <div class="btn-group pull-right-responsive margin-right-3">
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<?=$this->lang->line('application_show_gantt_by');?> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right" role="menu">
					<li><a href="#" class="resize-gantt"><?=$this->lang->line('application_gantt_by_milestones');?></a></li>
					<li><a href="#" class="users-gantt"><?=$this->lang->line('application_gantt_by_agents');?></a></li>
				</ul>
			</div>
      </span>
				</div>
				<div class="table-div min-height-410 gantt-width">
					<?php
					//get gantt data for Milestones
					$gantt_data = '
                                {
                                  name: "'.$project->name.'", desc: "", values: [{
                                label: "", from: "'.$project->start.'", to: "'.$project->end.'", customClass: "gantt-headerline"
                                }]},  ';
					foreach ($project->project_has_milestones as $milestone):
						$counter = 0;
						foreach ($milestone->project_has_tasks as $value):
							$milestone_Name = "";
							if($counter == 0){
								$milestone_Name = $milestone->name;
								$gantt_data .= '
                                {
                                  name: "'.$milestone_Name.'", desc: "", values: [';

								$gantt_data .= '{
                                label: "", from: "'.$milestone->start_date.'", to: "'.$milestone->due_date.'", customClass: "gantt-timeline"
                                }';
								$gantt_data .= ']
                                },  ';
							}

							$counter++;
							$start = ($value->start_date) ? $value->start_date : $milestone->start_date;
							$end = ($value->due_date) ? $value->due_date : $milestone->due_date;
							$class = ($value->status == "done") ? "ganttGrey" : "";
							$gantt_data .= '
                          {
                            name: "", desc: "'.$value->name.'", values: [';

							$gantt_data .= '{
                          label: "'.$value->name.'", from: "'.$start.'", to: "'.$end.'", customClass: "'.$class.'"
                          }';
							$gantt_data .= ']
                          },  ';
						endforeach;
					endforeach;

					//get gantt data for Users
					$gantt_data2 = '
                                { name: "'.$project->name.'", desc: "", values: [{
                                label: "", from: "'.$project->start.'", to: "'.$project->end.'", customClass: "gantt-headerline"
                                }]}, ';
					foreach ($project->project_has_workers as $worker):
						$counter = 0;
						foreach ($worker->getAllTasksInProject($project->id, $worker->intervenant->id) as $value):
							$user_name = "";
							if($counter == 0){
								$user_name = $worker->intervenant->name." ".$worker->intervenant->surname;
								$gantt_data2 .= '
                                {
                                  name: "'.$user_name.'", desc: "", values: [';

								$gantt_data2 .= '{
                                label: "", from: "'.$project->start.'", to: "'.$project->end.'", customClass: "gantt-timeline"
                                }';
								$gantt_data2 .= ']
                                },  ';
							}
							$counter++;
							$start = ($value->start_date) ? $value->start_date : $project->start;
							$end = ($value->due_date) ? $value->due_date : $project->end;
							$class = ($value->status == "done") ? "ganttGrey" : "";
							$gantt_data2 .= '
                          {
                            name: "", desc: "'.$value->name.'", values: [';

							$gantt_data2 .= '{
                          label: "'.$value->name.'", from: "'.$start.'", to: "'.$end.'", customClass: "'.$class.'", dataObj: {"id": '.$value->id.'}
                          }';
							$gantt_data2 .= ']
                          },  ';
						endforeach;
					endforeach;

					?>

					<div class="gantt"></div>
					<div id="gantData">
						<script type="text/javascript">
							$(document).on("click", '.resize-gantt', function (e) {
								ganttData = [<?=$gantt_data;?>];
								ganttChart(ganttData);
							});
							$(document).on("click", '.users-gantt', function (e) {
								ganttData2 = [<?=$gantt_data2;?>];
								ganttChart(ganttData2);
							});
						</script>
					</div>
				</div>
			</div>
		</div>
	<!-- Média -->
		<div class="row tab-pane fade" role="tabpanel" id="media-tab">
			<div class="col-xs-12 col-sm-3">
				<div class="table-head"><?=$this->lang->line('application_media');?>
					<span class=" pull-right">
				<a class="btn btn-default toggle-media-view tt" data-original-title="<?=$this->lang->line('application_media_view');?>"><i class="ion-image"></i></a>
				<a class="btn btn-default toggle-media-view hidden tt" data-original-title="<?=$this->lang->line('application_list_view');?>"><i class="ion-android-list"></i></a>
				<a href="<?=base_url()?>projects/media/<?=$project->id;?>/add" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_add_media');?></a>
			</span>
				</div>
				<div class="media-uploader">
					<?php $attributes = array('class' => 'dropzone', 'id' => 'dropzoneForm');
					echo form_open_multipart(base_url()."projects/dropzone/".$project->id, $attributes); ?>
					<?php echo form_close();?>
				</div>
			</div>
			<div class="col-xs-12 col-sm-9">
				<div class=" min-height-410 media-view-container">
					<div class="mediaPreviews dropzone"></div>
					<?php
					foreach ($project->project_has_files as $value):
						$type = explode("/", $value->type);
						$thumb = "./files/media/thumb_".$value->savename;

						if (file_exists($thumb)) {
							$filename = base_url()."files/media/thumb_".$value->savename;
						}else{
							$filename = base_url()."files/media/".$value->savename;
						}
						?>
						<div class="media-galery">
							<a href="<?=base_url()?>projects/media/<?=$project->id;?>/view/<?=$value->id;?>">
								<div class="overlay">
									<?=$value->name;?><br><br>
									<i class="ion-android-download"></i> <?=$value->download_counter;?>
								</div>
							</a>
							<div class="file-container">

								<?php switch($type[0]){
									case "image": ?>
										<img class="b-lazy"
											 src=data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==
											 data-src="<?=$filename?>"
											 alt="<?=$value->name;?>"
										/>
										<?php break; ?>

									<?php default: ?>
										<div class="icon-box">
											<i class="ion-ios-copy-outline"></i><br>
											<?=$type[1]?>
										</div>
										<?php break; ?>

									<?php } ?>
							</div>
							<div class="media-galery--footer"><?=$value->name;?></div>
						</div>

					<?php endforeach; ?>
				</div>

				<div class="media-list-view-container hidden">
					<div class="table-head"><?=$this->lang->line('application_media');?> <span class=" pull-right"><a href="<?=base_url()?>projects/media/<?=$project->id;?>/add" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_add_media');?></a></span></div>
					<div class="table-div min-height-410">
						<table id="media" class="table data-media" rel="<?=base_url()?>projects/media/<?=$project->id;?>" cellspacing="0" cellpadding="0">
							<thead>
							<tr>
								<th class="hidden"></th>
								<th><?=$this->lang->line('application_name');?></th>
								<th class="hidden-xs"><?=$this->lang->line('application_filename');?></th>
								<!--<th class="hidden-xs"><?//=$this->lang->line('application_phase');?></th>-->
								<th class="hidden-xs"><i class="fa fa-download"></i></th>
								<th><?=$this->lang->line('application_action');?></th>
							</tr>
							</thead>
							<tbody>
							<?php
							 foreach ($project->project_has_files as $value):?>

								<tr id="<?=$value->id;?>">
									<td class="hidden"><?=human_to_unix($value->date);?></td>
									<td onclick=""><?=$value->name;?></td>
									<td class="hidden-xs"><?=$value->filename;?></td>
									<!--<td class="hidden-xs"><?=$value->phase;?></td>-->
									<td class="hidden-xs"><span class="label label-info tt" title="<?=$this->lang->line('application_download_counter');?>" ><?=$value->download_counter;?></span></td>
									<td class="option " width="10%">
										<button type="button" class="btn-option btn-xs po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>projects/media/<?=$project->id;?>/delete/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-times" tilte="Supprimer"></i></button>
										<a href="<?=base_url()?>projects/media/<?=$project->id;?>/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
									</td>
								</tr>
							<?php endforeach;?>
							</tbody>
						</table>
						<?php if(!$project->project_has_files) { ?>
							<div class="no-files">
								<i class="fa fa-cloud-upload"></i><br>
								No files have been uploaded yet!
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	<!-- Notes -->
		<div class="row tab-pane fade" role="tabpanel" id="notes-tab">
			<div class="col-xs-12 col-sm-12">
				<?php $attributes = array('class' => 'note-form', 'id' => '_notes');
				echo form_open(base_url()."projects/notes/".$project->id, $attributes); ?>
				<div class="table-head"><?=$this->lang->line('application_notes');?> <span class=" pull-right"><a id="send" name="send" class="btn btn-primary button-loader"><?=$this->lang->line('application_save');?></a></span><span id="changed" class="pull-right label label-warning"><?=$this->lang->line('application_unsaved');?></span></div>
				<textarea class="input-block-level summernote-note" name="note" id="textfield" ><?=$project->note;?></textarea>
				<?php echo form_close();?>
			</div>
		</div>
		<!-- all invoices-->
		<?php  if($invoice_access == true ){ ?>
				<div class="row tab-pane fade" role="tabpanel" id="invoices-tab">
				<div class="col-xs-12 col-sm-12">
					<div class="table-head"><?=$this->lang->line('application_invoices');?> <span class=" pull-right"></span></div>
					<div class="table-div">
						<table class="data table" id="invoices"  cellspacing="0" cellpadding="0">
							<thead>
								<th width="hidden-xs"><?=$this->lang->line('application_estimate_id');?></th>
								<th class="hidden-xs"><?=$this->lang->line('application_client');?></th>
								<th class="hidden-xs"><?=$this->lang->line('application_issue_date');?></th>
								<th class="hidden-xs"><?=$this->lang->line('application_total_ttc');?></th>
								<th class="hidden-xs"><?=$this->lang->line('application_status');?></th>
								<th class="hidden-xs">Date du Paiement </th>
							</thead>
								<?php foreach ($project_has_invoices as $value):?>
									
								<tr>
									<td class="hidden-xs"><a href="<?=base_url()?>invoices/view/<?=$value->id;?>"><?=$value->estimate_num?></a>
									</td>
									<td><a href="<?=base_url()?>clients/view/<?=$value->company_id;?>">
										<?php 
										$company = getcompany($value->company_id);?>
										<span class="label label-info">
										<?php 
											echo $company->name; 
										?>	
										</span></a>			
									</td>
									<td class="hidden-xs"><?php  $unix = human_to_unix($value->creation_date.' 00:00'); echo date($core_settings->date_format, $unix);?></td>
									<td class="hidden-xs">
										<?php  if ($value->currency=='TND') $chiffre='3'; 
											   elseif ($value->currency=='Euro') $chiffre='2'; 
											   echo display_money($value->sum,"",$chiffre); 
										?>
									</td>
									<td class="hidden-xs"><?php get_etat_color($value->status) ?></td> 
									<td class="hidden-xs"><?php  
										$unix = human_to_unix($value->paid_date.' 00:00');
										if ($unix != false) echo date($core_settings->date_format, $unix);
										 else echo ("facture impayée");
										?>
									</td>
								</tr>
							<?php endforeach;?>
						</table>
					</div>
				</div>
					<div class="col-xs-12 col-sm-12">
					<div class="table-head">Date relance client <span class=" pull-right"></span></div>
						<div class="table-div">
							<?php   
								$attributes = array('class' => '', 'id' => 'view');
								echo form_open($form_action, $attributes); 
							?>
							<table class="data table" id="invoices"  cellspacing="0" cellpadding="0">
							<thead>
								<th width="hidden-xs">Facture</th>
								<th class="hidden-xs">N°1 prévisionnelle</th>
								<th class="hidden-xs">Date relance N°1</th>
								<th class="hidden-xs">N°2 prévisionnelle</th>
								<th class="hidden-xs">Date relance N°2</th>
								<th class="hidden-xs">N°3 prévisionnelle</th>
								<th class="hidden-xs">Date relance N°3</th>
							</thead>
							<?php foreach ($project_has_invoices as $value):?>	
								<tr>
									<td class="hidden-xs"><a href="<?=base_url()?>invoices/view/<?=$value->id;?>"><?=$value->estimate_num?></a>
									</td>
									<td class="hidden-xs"><?php
										if ($value->paid_date == NULL){
										$date = $value->creation_date;   
										$date= date('Y-m-d', strtotime($date.' +15 days')); 	
										$unix = human_to_unix($date.' 00:00');
										echo date($core_settings->date_format, $unix);}
										else echo ("facture payée");
										?>
									<td>
										<?php
										if ($value->paid_date == NULL)
										echo ("<input class='datepicker' name='date_relance_1' id='date_relance_1' type='text' />");
										?>
									</td>
									<td class="hidden-xs"><?php
										if ($value->paid_date == NULL){
										$date = $value->creation_date;   
										$date= date('Y-m-d', strtotime($date.' +30 days')); 	
										$unix = human_to_unix($date.' 00:00');
										echo date($core_settings->date_format, $unix);}
										else echo ("facture payée");
										?>
									<td><?php
										if ($value->paid_date == NULL)
										echo ("<input class='datepicker' name='date_relance_2' id='date_relance_2' type='text' />");
										?>
									</td>
										<td class="hidden-xs"><?php
										if ($value->paid_date == NULL){
										$date = $value->creation_date;   
										$date= date('Y-m-d', strtotime($date.' +45 days')); 	
										$unix = human_to_unix($date.' 00:00');
										echo date($core_settings->date_format, $unix);}
										else echo ("facture payée");
										?>
									<td><?php
										if ($value->paid_date == NULL)
										echo ("<input class='datepicker' name='date_relance_3' id='date_relance_3' type='text' />");
										?>
									</td>
								</tr>
						
								<?php endforeach;?>
							</table> 
							<div class="modal-footer">
							<input type="submit" name="send" id="btnSubmit" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
							</div>

							<?php if(!$project_has_invoices) { ?>
							<div class="no-files">
								<i class="fa fa-file-text"></i><br>

								<?=$this->lang->line('application_no_invoices_yet');?>
							</div>
						<?php } ?>
					</div>
					<?php echo form_close();?>
				</div>
			</div>
		<?php } ?>
								
								
	<!-- Activités -->
		<div class="row tab-pane fade" role="tabpanel" id="activities-tab">
			<div class="col-xs-12 col-sm-12">
				<div class="table-head"><?=$this->lang->line('application_activities');?>
					<span class=" pull-right"><a class="btn btn-primary open-comment-box"><?=$this->lang->line('application_new_comment');?></a></span>
				</div>
				<div class="subcont" >
					<ul id="comments-ul" class="comments">
						<li class="comment-item add-comment">
							<?php
							$attributes = array('class' => 'ajaxform', 'id' => 'replyform', 'data-reload' => 'comments-ul');
							echo form_open('projects/activity/'.$project->id.'/add', $attributes);
							?>
							<div class="comment-pic">
								<img class="img-circle tt" title="<?=$this->intervenant->name?> <?=$this->intervenant->surname?>"  src="<?=get_user_pic($this->intervenant->userpic, $this->intervenant->email);?>">
							</div>
							<div class="comment-content">
								<h5><input type="text" name="subject" class="form-control" id="subject" placeholder="<?=$this->lang->line('application_subject');?>..." required/></h5>
								<p><small class="text-muted"><span class="comment-writer"><?=$this->intervenant->name?> <?=$this->intervenant->surname?></span> <span class="datetime"><?php  echo date($core_settings->date_format.' '.$core_settings->date_time_format, time()); ?></span></small></p>
								<p><textarea class="input-block-level summernote" id="reply" name="message" placeholder="<?=$this->lang->line('application_write_message');?>..." required/></textarea></p>
								<button id="send" name="send" class="btn btn-primary button-loader"><?=$this->lang->line('application_send');?></button>
								<button id="cancel" name="cancel" class="btn btn-danger open-comment-box"><?=$this->lang->line('application_close');?></button>
							</div>
							</form>
						</li>
						<?php foreach ($project->project_has_activities as $value):?>
							<?php
							$writer = FALSE;
							if ($value->user_id->admin != 0) {
								$writer = $value->intervenant->name." ".$value->intervenant->surname;
								$image = get_user_pic($value->intervenant->userpic, $value->intervenant->email);
							}else{
								$writer = $value->client->firstname." ".$value->client->lastname;
								$image = get_user_pic($value->client->userpic, $value->client->email);
							}?>
							<li class="comment-item">
								<div class="comment-pic">
									<?php if ($writer != FALSE) {  ?>
										<img class="img-circle tt" title="<?=$writer?>"  src="<?=$image?>">
									<?php }else{?> <i class="fa fa-rocket"></i> <?php } ?>
								</div>
								<div class="comment-content">
									<h5><?=$value->subject;?></h5>
									<p><small class="text-muted"><span class="comment-writer"><?=$writer?></span> <span class="datetime"><?php  echo date($core_settings->date_format.' '.$core_settings->date_time_format, $value->datetime); ?></span></small></p>
									<p><?=$value->message;?></p>
								</div>
							</li>
						<?php endforeach;?>
						<li class="comment-item">
							<div class="comment-pic"><i class="fa fa-bolt"></i></div>
							<div class="comment-content">
								<h5><?=$this->lang->line('application_project_created');?></h5>
								<p><small class="text-muted"><?php  echo date($core_settings->date_format.' '.$core_settings->date_time_format, $project->datetime); ?></small></p>
								<p><?=$this->lang->line('application_project_has_been_created');?></p>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="row tab-pane fade" role="tabpanel" id="sub-projetcs-tab">
				<div class="col-xs-12 col-sm-12">
					<div class="table-head">
						<?=$this->lang->line('application_sous_projets');?>
						<span class="pull-right">
							<a href="<?=base_url().(isset($url_add_ref)? $url_add_ref:'#') ?>" data-toggle="mainmodal" class="to-modal btn btn-success"><?=$this->lang->line('application-add');?>
							</a>
						</span>
						</div>
						<div class="table-div">
							<br class="clear"><br class="clear">
							<table class="dataSorting table" id="projects" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
								<thead>
									<tr>

										<th><?=$this->lang->line('application_project_id');?></th>
										<th><?=$this->lang->line('application_name');?></th>
										<th class=""><?=$this->lang->line('application_description');?></th>
										<th class=""><?=$this->lang->line('application_tasks_time_spent');?></th>
										<th><?=$this->lang->line('application_action');?></th>
									</tr>
								</thead>
				                <tbody>
				                <?php foreach ($sub_projects as $item): ?>
				                <tr id="<?=$item->project->id.'/0/'.$item->id;?>">
									<!-- ID Projet -->

									<!-- CODE Projet -->
									<td class="option action" style="text-align: left" ><?=$item->code;?></td>

									<!-- Name Projet -->
									<td class="option action" style="text-align: left"><?=$item->name;?></td>
									<!-- Description -->
									<td class="option action" style="text-align: left"><?=$item->description;?></td>

									<td class="option action" style="text-align: center">
											<?php if($unite_temps->name === $this->config->item("type_occ_code_unite_temps_jours")) : ?>
								                <?=format_temps_jours($tab_sub_projects_heures_pointees[$item->id]);?>
							              	<?php else: ?>
								                <?=format_temps_heures($tab_sub_projects_heures_pointees[$item->id]);?>
							              	<?php endif ?>
						            </td>
									<td class="option action" style="text-align: left;">
								        <a 	href="<?=site_url($url_update_ref).'/'.$item->project_id.'/'.$item->id;?>" class="btn-option"
								        	data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i>
								        </a>
										<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent'
										href='<?=site_url($url_delete_ref).'/'.$item->project_id.'/'.$item->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_delete_project');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
									</td>
				                </tr>
						        <?php endforeach;?>
				            </tbody>
						</table>
						<?php if(!$project_has_invoices) { ?>
							<div class="no-files">
								<i class="fa fa-file-text"></i><br>

								<?=$this->lang->line('application_no_sub_projects_yet');?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			





			<script type="text/javascript">






				$(document).on("click", '.toggle-closed-tasks', function (e) {

					$("li.done").toggleClass("hidden");
					if(localStorage.hide_tasks == "1"){
						localStorage.removeItem("hide_tasks");
						$(".toggle-closed-tasks").css("opacity", "1");
					}else{
						localStorage.setItem("hide_tasks", "1");
						$(".toggle-closed-tasks").css("opacity", "0.6");
					}
				});
				hideClosedTasks();
				blazyloader();
				dropzoneloader("<?php echo base_url()."projects/dropzone/".$project->id; ?>", "<?=addslashes($this->lang->line('application_drop_files_here_to_upload'));?>");

				//chartjs
				var ctx = document.getElementById("projectChart");
				var myChart = new Chart(ctx, {
					type: 'line',
					data: {
						labels: [<?=$labels?>],
						datasets: [{
							label: "<?=$this->lang->line("application_task_due");?>",
							backgroundColor: "rgba(215,112,173,0.3)",
							borderColor: "rgba(215,112,173,1)",
							pointBorderColor: "rgba(0,0,0,0)",
							pointBackgroundColor: "#ffffff",
							pointHoverBackgroundColor: "rgba(237, 85, 101, 0.5)",
							pointHitRadius: 25,
							pointRadius: 1,
							borderWidth:2,
							data: [<?=$line1?>],
						},{
							label: "<?=$this->lang->line("application_task_start");?>",
							backgroundColor: "rgba(79,193,233,0.6)",
							borderColor: "rgba(79, 193, 233, 1)",
							pointBorderColor: "rgba(79, 193, 233, 0)",
							pointBackgroundColor: "#ffffff",
							pointHoverBackgroundColor: "rgba(79, 193, 233, 1)",
							pointHitRadius: 25,
							pointRadius: 1,
							borderWidth:2,
							data: [<?=$line2?>],
						}
						]
					},
					options: {
						title: {
							display: true,
							text: ' '
						},
						legend:{
							display: false
						},
						scales: {

							yAxes: [{
								display: false,
								ticks: {
									beginAtZero:true,
									maxTicksLimit:6,
									padding:20
								}
							}],
							xAxes: [{
								display: false,
								ticks: {
									beginAtZero:true,
								}
							}]
						}
				}
				}
				});
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
						"thickness":.13,
						'dynamicDraw': true,
						"displayInput":false,
					});
				
					$({value: 0}).animate({ value: perc }, {
						duration: 1000,
						easing: 'swing',
						progress: function () {
							elm.val(Math.ceil(this.value)).trigger('change')
						}
					});
					//circular progress bar color
					$(this).append(function() {
						elm.parent().parent().find('.circular-bar-content').css('color',color);
						elm.parent().parent().find('.circular-bar-content label').text(perc+'%');
					});
				});
				$(".toggle-media-view").on("click", function(){
					$(".media-view-container").toggleClass('hidden');
					$(".toggle-media-view").toggleClass('hidden');
					$(".media-list-view-container").toggleClass('hidden');

				});
				<?php if($go_to_taskID){ ?>
				$("#task_menu_link").click();
				$("#task_<?=$go_to_taskID;?> p.name").click();
				<?php  } ?>
			});
		
		</script>

		<script>
			function Delete(idProject, idSpeaker){
				var url ='<?php echo base_url(); ?>' + "projects/speakers/" + idProject +"/delete/"+ idSpeaker;
				$.ajax(
					{
						type: 'POST',
						dataType: "text",
						url: url,
						success: function (response) 
						{
								if(response == true)
								{
									$('#'+idSpeaker+'').fadeOut('slow', function()
									{
										$(this).remove();
									});
								}
						}
					})
			}

						

		</script>
		<div id="tkKey" class="hidden"><?=$this->security->get_csrf_hash();?></div>
		<div id="baseURL" class="hidden"><?=base_url();?>projects/</div>
		<div id="projectId" class="hidden"><?=$project->id;?></div>
