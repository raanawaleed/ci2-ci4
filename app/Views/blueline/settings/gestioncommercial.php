<div id="row">
	<div class="col-md-3">
		<div class="list-group">
			<?php foreach ($submenu as $name=>$value):
			$badge = "";
			$active = "";
			if($value == "settings/societe"){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
			if($name == $breadcrumb){ $active = 'active';}?>
				<a class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
			<?php endforeach;?>
		</div>
	</div>
    <div class="col-md-3"></div>
	<!-- Echeance -->
	<div class="col-md-9">
		<div class="row">
			<div class="span12 marginbottom20">
				<div class="table-head"><?=$this->lang->line('application_due_date')?><span class="pull-right"><a href="<?=base_url()?>settings/ajoutecheance" data-toggle="mainmodal" class="btn btn-success"><?=$this->lang->line('application-add');?></a> </span></div>
				<div class="subcont">
					<table class="data-no-search table dataTable no-footer" cellspacing="0" cellpadding="0" role="grid" id="sample_1">
						<thead> 
							<tr> 
								<th>Libelle de date d'echéance</th>
								<th class="hidden-480">Description</th>
								<th>Actions </th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($echeance as $key ) { ?>
							<tr class="odd gradeX">
							<?php                                               
								echo("<td>".$key->name."</td>");
								echo("<td width='50%'>".$key->description."</td>");
								echo('<td width="8%"><a href="editecheance/'.$key->id.'" data-toggle="mainmodal" class="btn-option"><i class="fa fa-edit" title="Modifier"></i></a>');
							?>
							<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>settings/deleteecheance/<?=$key->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<br clear="all">
				</div>
		    </div>
	    </div>	
    </div>
    <!-- Unité -->
    <div class="col-md-3"></div>
	<div class="col-md-9">
		<div class="row">
			<div class="span12 marginbottom20">
				<div class="table-head"><?=$this->lang->line('application_unit_types')?><span class="pull-right"><a href="<?=base_url()?>settings/addUnit" data-toggle="mainmodal" class="btn btn-success"><?=$this->lang->line('application-add');?></a> </span></div>
				<div class="subcont">
					<table class="data-no-search table dataTable no-footer" cellspacing="0" cellpadding="0" role="grid" id="sample_1">
						<thead> 
							<tr> 
								<th class="hidden-480"><?=$this->lang->line('application_unit_value')?></th>
								<th><?=$this->lang->line('application_description')?></th>
								<th><?=$this->lang->line('application_action')?></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($item_units as $item) { ?>
							<tr class="odd gradeX">
							<?php
								echo("<td width='50%'>".$item->value."</td>");
								echo("<td>".$item->description."</td>");
								echo('<td width="8%"><a href="updateUnit/'.$item->id.'" data-toggle="mainmodal" class="btn-option"><i class="fa fa-edit"></i></a>');
							?>
							<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>settings/deleteUnit/<?=$item->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash"></i></button>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<br clear="all">
				</div>
			</div>
		</div>
	</div>
<div class="col-md-3"></div>
	<div class="col-md-9">
		<div class="row">
			<div class="span12 marginbottom20">
			<div class="table-head"><?=$this->lang->line('application_timbre')?></div>
			<div class="subcont">
				<?php echo form_open_multipart($form_action); ?>
				<div class="form-group">
					<label for="Timbre"><?=$this->lang->line('application_timbre')?></label>
					<div class="input-group">  
						<input type="text" id="Timbre" name="timbre_fiscal"  value="<?php if(isset($timbre)){echo display_money($timbre,$settings->currency,3);;}?>" class="form-control">
					</div>
				</div>
				
				<?php $this->load->helper("mydbhelper_helper.php") ?>
				<div class="form-header"><?=$this->lang->line('application_reference_prefix');?></div>

				<!-- estimate -->
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="devis"><?=$this->lang->line('application_estimate');?></label>
							<?php
								$prefix = substr($settings->estimate_prefix, 0, (strpos($settings->estimate_prefix, '-')));
							?>
							<input type="text" id="devis" value="<?=$prefix?>" class="form-control" onchange="ChangePrefix('devis')" >
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="nomdevis"><?=$this->lang->line('application_estimate');?></label>
							<div class="input-group col-md-12">  
								<select onchange="myFunction('nomdevis')" name="nomdevis" id="nomdevis" class="chosen-select prefixselect">
									<?php if (substr($settings->estimate_prefix, strlen($prefix)) == "-YY") { ?>
									<option value="_">_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee" selected>Annee</option>
									<?php } elseif (substr($settings->estimate_prefix, strlen($prefix)) == "-YY-MM") { ?>
									<option value="_">_</option>
									<option value="Mois" selected>Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } else { ?>
									<option value="_" selected>_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<!-- not visible -->
					<div class="col-md-4" style="display: none">
						<div class="form-group">
							<label><?=$this->lang->line('application_estimate');?></label>
							<div class="input-group">
								<input type="text" id="estimateNotvisible" class="form-control devis" value="<?php echo $settings->estimate_prefix;?>" name="estimate_prefix" readonly>
							</div>
						</div>
					</div>
					<!-- visible -->
					<div class="col-md-4" >
						<div class="form-group">
							<label><?=$this->lang->line('application_estimate');?></label>
							<div class="input-group">
								<input type="text"  id="estimate" class="form-control devis" value="<?php $dateToday =date("d-m-y");
								$piecesDateToday = explode("-",$dateToday); 
								$year = explode(" ",$piecesDateToday[2]); 
								$project_pieces = explode("-",strrev($settings->estimate_prefix));	
								// année 
								if ($project_pieces[0]=='YY')
								{
									echo(strrev($project_pieces[1]).$year[0]);
								}
								//année + mois 
								else
								{	
									echo(strrev($project_pieces[2]).$year[0].$piecesDateToday[1]);
								}?> " name="estimate" readonly>
							</div>
						</div>
					</div>
					<br clear="all">
				</div>

				<!-- invoice -->
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="facture"><?=$this->lang->line('application_invoice');?></label>
							<?php $prefix = substr($settings->invoice_prefix, 0, (strpos($settings->invoice_prefix, '-')));
							?>
							<input type="text" id="facture"   value="<?=$prefix?>" class="form-control" onchange="ChangePrefix('facture')" >
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="nomfacture"><?=$this->lang->line('application_invoice');?></label>
							<div class="input-group col-md-12">  
								<select onchange="myFunction('nomfacture')" name="nomfacture" id="nomfacture" class="chosen-select prefixselect">
									<?php if (substr($settings->invoice_prefix, strlen($prefix))  == "-YY") { ?>
									<option value="_">_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee" selected>Annee</option>
									<?php } elseif (substr($settings->invoice_prefix, strlen($prefix))  == "-YY-MM") { ?>
									<option value="_">_</option>
									<option value="Mois" selected>Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } else { ?>
									<option value="_" selected>_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<!-- not visible -->
					<div class="col-md-4" style="display: none">
						<div class="form-group">
							<label><?=$this->lang->line('application_invoice');?></label>
							<div class="input-group">
								<input type="text"  id="invoiceNotvisible" class="form-control facture" value="<?=$settings->invoice_prefix?>"  name="invoice_prefix" readonly>
							</div>
						</div>
					</div>
					<!-- visible -->
					<div class="col-md-4" >
						<div class="form-group">
							<label><?=$this->lang->line('application_invoice');?></label>
							<div class="input-group">
								<input type="text"  id="invoice" class="form-control facture" value="<?php $dateToday =date("d-m-y");
								$piecesDateToday = explode("-",$dateToday); 
								$year = explode(" ",$piecesDateToday[2]); 
								$project_pieces = explode("-",strrev($settings->invoice_prefix));	
								// année 
								if ($project_pieces[0]=='YY')
								{
									echo(strrev($project_pieces[1]).$year[0]);
								}
								//année + mois 
								else
								{	
									echo(strrev($project_pieces[2]).$year[0].$piecesDateToday[1]);
								}?> " name="invoice" readonly>
							</div>
						</div>
					</div>
					<br clear="all">
				</div>
				
				<!-- avoir -->
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="avoir"><?=$this->lang->line('application_avoir');?></label>
							<?php
								$prefix = substr($settings->avoir_prefix, 0, (strpos($settings->avoir_prefix, '-')));
							?>
							<input type="text" id="avoir" value="<?=$prefix?>" class="form-control" onchange="ChangePrefix('avoir')" >
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="nomavoir"><?=$this->lang->line('application_avoir');?></label>
							<div class="input-group col-md-12">  
								<select onchange="myFunction('nomavoir')" name="nomavoir" id="nomavoir" class="chosen-select prefixselect">
									<?php if (substr($settings->avoir_prefix, strlen($prefix)) == "-YY") { ?>
									<option value="_">_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee" selected>Annee</option>
									<?php } elseif (substr($settings->avoir_prefix, strlen($prefix)) == "-YY-MM") { ?>
									<option value="_">_</option>
									<option value="Mois" selected>Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } else { ?>
									<option value="_" selected>_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<!-- not visible -->
					<div class="col-md-4" style="display: none">
						<div class="form-group">
							<label><?=$this->lang->line('application_avoir');?></label>
							<div class="input-group">
								<input type="text" id="avoirNotvisible" class="form-control avoir" value="<?php echo $settings->avoir_prefix;?>" name="avoir_prefix" readonly>
							</div>
						</div>
					</div>
					<!-- visible -->
					<div class="col-md-4" >
						<div class="form-group">
							<label><?=$this->lang->line('application_avoir');?></label>
							<div class="input-group">
								<input type="text"  id="avoirPrefix" class="form-control avoir" value="<?php $dateToday =date("d-m-y");
								$piecesDateToday = explode("-",$dateToday); 
								$year = explode(" ",$piecesDateToday[2]); 
								$project_pieces = explode("-",strrev($settings->avoir_prefix));	
								// année 
								if ($project_pieces[0]=='YY')
								{
									echo(strrev($project_pieces[1]).$year[0]);
								}
								//année + mois 
								else
								{	
									echo(strrev($project_pieces[2]).$year[0].$piecesDateToday[1]);
								}?> " name="avoir" readonly>
							</div>
						</div>
					</div>
					<br clear="all">
				</div>

				<!-- subscription -->
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="abonnement"><?=$this->lang->line('application_subscription');?></label>
							<?php $prefix = substr($settings->subscription_prefix, 0, (strpos($settings->subscription_prefix, '-')));
							?>
							<input type="text" id="abonnement"  value="<?=$prefix?>" class="form-control" onchange="ChangePrefix('abonnement')" >
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="nomabonnement"><?=$this->lang->line('application_subscription');?></label>
							<div class="input-group col-md-12">  
								<select onchange="myFunction('nomabonnement')" name="nomabonnement" id="nomabonnement" class="chosen-select prefixselect">
									<?php if (substr($settings->subscription_prefix, strlen($prefix)) == "-YY") { ?>
									<option value="_">_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee" selected>Annee</option>
									<?php } elseif (substr($settings->subscription_prefix, strlen($prefix)) == "-YY-MM") { ?>
									<option value="_">_</option>
									<option value="Mois" selected>Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } else { ?>
									<option value="_" selected>_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<!-- not visible -->
					<div class="col-md-4" style="display: none">
						<div class="form-group">
							<label><?=$this->lang->line('application_subscription');?></label>
							<div class="input-group">
								<input type="text"  id="subscriptionNotvisible" class="form-control abonnement" value="<?=$settings->subscription_prefix?>"  name="subscription_prefix" readonly>
							</div>
						</div>
					</div>
					<!-- visible -->
					<div class="col-md-4" >
						<div class="form-group">
							<label><?=$this->lang->line('application_subscription');?></label>
							<div class="input-group">
								<input type="text"  id="subscription" class="form-control subscription" value="<?php $dateToday =date("d-m-y");
								$piecesDateToday = explode("-",$dateToday); 
								$year = explode(" ",$piecesDateToday[2]); 
								$project_pieces = explode("-",strrev($settings->subscription_prefix));	
								// année 
								if ($project_pieces[0]=='YY')
								{
									echo(strrev($project_pieces[1]).$year[0]);
								}
								//année + mois 
								else
								{	
									echo(strrev($project_pieces[2]).$year[0].$piecesDateToday[1]);
								}?> " name="subscription" readonly>
							</div>
						</div>
					</div>
					<br clear="all">
				</div>
				
				<!-- bc -->
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="commande"><?=$this->lang->line('application_bc');?></label>
							<?php $prefix = substr($settings->commande_prefix, 0, (strpos($settings->commande_prefix, '-')));
							?>
							<input type="text" id="commande" value="<?=$prefix?>" class="form-control" onchange="ChangePrefix('commande')" >
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="nomcommande"><?=$this->lang->line('application_bc');?></label>
							<div class="input-group col-md-12">  
								<select onchange="myFunction('nomcommande')" name="nomcommande" id="nomcommande" class="chosen-select prefixselect">
									<?php if (substr($settings->commande_prefix, strlen($prefix)) == "-YY") { ?>
									<option value="_">_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee" selected>Annee</option>
									<?php } elseif (substr($settings->commande_prefix, strlen($prefix)) == "-YY-MM") { ?>
									<option value="_">_</option>
									<option value="Mois" selected>Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } else { ?>
									<option value="_" selected>_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<!-- not visible -->
					<div class="col-md-4" style="display: none">
						<div class="form-group">
							<label><?=$this->lang->line('application_bc');?></label>
							<div class="input-group">
								<input type="text"  id="commandeNotvisible" class="form-control commande" value="<?=$settings->commande_prefix?>"  name="commande_prefix" readonly>
							</div>
						</div>
					</div>
					<!-- visible -->
					<div class="col-md-4" >
						<div class="form-group">
							<label><?=$this->lang->line('application_bc');?></label>
							<div class="input-group">
								<input type="text"  id="commandePrefix" class="form-control subscription" value="<?php $dateToday =date("d-m-y");
								$piecesDateToday = explode("-",$dateToday); 
								$year = explode(" ",$piecesDateToday[2]); 
								$project_pieces = explode("-",strrev($settings->commande_prefix));	
								// année 
								if ($project_pieces[0]=='YY')
								{
									echo(strrev($project_pieces[1]).$year[0]);
								}
								//année + mois 
								else
								{	
									echo(strrev($project_pieces[2]).$year[0].$piecesDateToday[1]);
								}?> " name="commandePrefix" readonly>
							</div>
						</div>
					</div>
					<br clear="all">
				</div>
				
				<!-- bl -->
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="livraison"><?=$this->lang->line('application_bl');?></label>
							<?php $prefix = substr($settings->livraison_prefix, 0, (strpos($settings->commande_prefix, '-')));
							?>
							<input type="text" id="livraison" value="<?=$prefix?>" class="form-control" onchange="ChangePrefix('livraison')" >
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="nomlivraison"><?=$this->lang->line('application_bl');?></label>
							<div class="input-group col-md-12">  
								<select onchange="myFunction('nomlivraison')" name="nomlivraison" id="nomlivraison" class="chosen-select prefixselect">
									<?php if (substr($settings->livraison_prefix, strlen($prefix)) == "-YY") { ?>
									<option value="_">_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee" selected>Annee</option>
									<?php } elseif (substr($settings->livraison_prefix, strlen($prefix)) == "-YY-MM") { ?>
									<option value="_">_</option>
									<option value="Mois" selected>Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } else { ?>
									<option value="_" selected>_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<!-- not visible -->
					<div class="col-md-4" style="display: none">
						<div class="form-group">
							<label><?=$this->lang->line('application_bl');?></label>
							<div class="input-group">
								<input type="text"  id="livraisonNotvisible"  class="form-control livraison" value="<?=$settings->livraison_prefix?>"  name="livraison_prefix" readonly>
							</div>
						</div>
					</div>
					<!-- visible -->
					<div class="col-md-4" >
						<div class="form-group">
							<label><?=$this->lang->line('application_bl');?></label>
							<div class="input-group">
								<input type="text"  id="livraisonPrefix" class="form-control livraison" value="<?php $dateToday =date("d-m-y");
								$piecesDateToday = explode("-",$dateToday); 
								$year = explode(" ",$piecesDateToday[2]); 
								$project_pieces = explode("-",strrev($settings->livraison_prefix));	
								// année 
								if ($project_pieces[0]=='YY')
								{
									echo(strrev($project_pieces[1]).$year[0]);
								}
								//année + mois 
								else
								{	
									echo(strrev($project_pieces[2]).$year[0].$piecesDateToday[1]);
								}?> " name="livraisonPrefix" readonly>
							</div>
						</div>
					</div>
					<br clear="all">
				</div>
			
				<!-- project -->
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="livraison"><?=$this->lang->line('application_project');?></label>
							<?php $prefix = substr($settings->project_prefix, 0, (strpos($settings->project_prefix, '-')));
							?>
							<input type="text" id="project" value="<?=$prefix?>" class="form-control" onchange="ChangePrefix('project')" >
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="nomlivraison"><?=$this->lang->line('application_project');?></label>
							<div class="input-group col-md-12">  
								<select onchange="myFunction('nomproject')" name="nomproject" id="nomproject" class="chosen-select">
									<?php if (substr($settings->project_prefix, strlen($prefix)) == "-YY") { ?>
									<option value="_">_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee" selected>Annee</option>
									<?php } elseif (substr($settings->project_prefix, strlen($prefix)) == "-YY-MM") { ?>
									<option value="_">_</option>
									<option value="Mois" selected>Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } else { ?>
									<option value="_" selected>_</option>
									<option value="Mois">Annee+Mois</option>
									<option value="Annee">Annee</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<!-- not visible -->
					<div class="col-md-4" style="display: none">
						<div class="form-group">
							<label><?=$this->lang->line('application_project');?></label>
							<div class="input-group">
								<input type="text"  id="projectNotvisible"  class="form-control project" value="<?=$settings->project_prefix?>"  name="project_prefix" readonly>
							</div>
						</div>
					</div>
					<!-- visible -->
					<div class="col-md-4" >
						<div class="form-group">
							<label><?=$this->lang->line('application_project');?></label>
							<div class="input-group">
								<input type="text"  id="projectPrefix" class="form-control project" value="<?php $dateToday =date("d-m-y");
								$piecesDateToday = explode("-",$dateToday); 
								$year = explode(" ",$piecesDateToday[2]); 
								$project_pieces = explode("-",strrev($settings->project_prefix));	
								// année 
								if ($project_pieces[0]=='YY')
								{
									echo(strrev($project_pieces[1]).$year[0]);
								}
								//année + mois 
								else
								{	
									echo(strrev($project_pieces[2]).$year[0].$piecesDateToday[1]);
								}?> " name="projectPrefix" readonly>
							</div>
						</div>
					</div>
					<br clear="all">
				</div>
			
				<div class="form-group no-border">
					<input type="submit" name="send" class="btn btn-primary pull-right" value="<?=$this->lang->line('application_save');?>"/>
				</div>

				<?php echo form_close(); ?>
				<br clear="all">
			</div>
		</div>
		<br clear="all">
		<div class="col-md-12">
			<div class="row">
				<?php echo form_open('settings/saveNotes'); ?>
					<div class="table-head">
						<?=$this->lang->line('application_notes_devis')?>
						<span class="pull-right">
							<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
						</span>
					</div>

					<div class="form-group settings-gc-note">
						<textarea id="notes" name="notes" class="textarea summernote required form-control" style="height:100px"><?=$settings->notes?></textarea>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
		<div class="col-md-12">
			<div class="row">
				<?php echo form_open('settings/saveFactureNotes'); ?>
					<div class="table-head">
						<?=$this->lang->line('application_notes_facture')?>
						<span class="pull-right">
							<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
						</span>
					</div>

					<div class="form-group settings-gc-note">
						<textarea id="notes_facture" name="notes_facture" class="textarea summernote required form-control" style="height:100px"><?=$settings->notes_facture?></textarea>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
		
		<!--Note avoir-->
		<br clear="all">
		<div class="col-md-12">
			<div class="row">
				<?php echo form_open('settings/saveAvoirNotes'); ?>
					<div class="table-head">
						<?=$this->lang->line('application_notes_avoir')?>
						<span class="pull-right">
							<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
						</span>
					</div>
					<div class="form-group settings-gc-note">
						<textarea id="notes_avoir" name="notes_avoir" class="textarea summernote required form-control" style="height:100px"><?=$settings->notes_avoir?></textarea>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
	</div>
</div>
<script>
function myFunction(id) { 
	if (id == 'nomdevis') {
		var status =  document.getElementById("nomdevis").value; 
		var prefix = document.getElementById("devis").value;
	} else if (id == 'nomfacture'){
		var status =  document.getElementById("nomfacture").value; 
		var prefix = document.getElementById("facture").value;
	}else if (id == 'nomabonnement'){
		var status =  document.getElementById("nomabonnement").value; 
		var prefix = document.getElementById("abonnement").value;
	}else if (id == 'nomcommande'){
		var status =  document.getElementById("nomcommande").value; 
		var prefix = document.getElementById("commande").value;
	}else if (id == 'nomlivraison'){
		var status =  document.getElementById("nomlivraison").value; 
		var prefix = document.getElementById("livraison").value;
	}else if (id == 'nomproject'){
		var status =  document.getElementById("nomproject").value; 
		var prefix = document.getElementById("project").value;
	}else if (id == 'nomavoir') {
		var status =  document.getElementById("nomavoir").value; 
		var prefix = document.getElementById("avoir").value;
	} 
	var val;
	var dateToDay = moment().format("DD-MM-YY");
	var dateSplit = dateToDay.split('-');  
	if (status == "Mois"){
		val = prefix.concat(dateSplit[2]).concat(dateSplit[1]);  
	} else {
		val = prefix.concat(dateSplit[2]); 
	}

	if (id == 'nomdevis') {
		document.getElementById("estimate").value = val;
	}else if (id == 'nomfacture'){
		document.getElementById("invoice").value = val;
	}else if (id == 'nomabonnement'){
		document.getElementById("subscription").value = val;
	}else if (id == 'nomcommande'){
		document.getElementById("commandePrefix").value = val;
	}else if (id == 'nomlivraison'){
		document.getElementById("livraisonPrefix").value = val;
	}else if (id == 'nomproject'){
		document.getElementById("projectPrefix").value = val;
	}else if (id == 'nomavoir'){
		document.getElementById("avoirPrefix").value = val;
	}
}
function ChangePrefix(id,event){
	var idOuput;
	var refId; 
	var idStatus; 
	if(id == 'devis'){
		idOuput = 'estimate';
		refId = 'estimateNotvisible'; 
		idStatus = 'nomdevis'; 
	} else if (id == 'facture'){
		idOuput = 'invoice'; 
		refId = 'invoiceNotvisible'; 
		idStatus = 'nomfacture'; 
	} else if (id == 'abonnement'){
		idOuput = 'subscription';
		refId = 'subscriptionNotvisible';
		idStatus = 'nomabonnement'; 
	} else if (id == 'commande'){
		idOuput = 'commandePrefix'; 
		refId = 'commandeNotvisible';
		idStatus = 'nomcommande'; 
	} else if (id == 'livraison'){
		idOuput = 'livraisonPrefix'; 
		refId = 'livraisonNotvisible';
		idStatus = 'nomlivraison'; 
	} else if(id == 'project'){
		idOuput = 'projectPrefix'; 
		refId = 'projectNotvisible';
		idStatus = 'nomproject'; 
	}else if(id == 'avoir'){
		idOuput = 'avoirPrefix'; 
		refId = 'avoirNotvisible';
		idStatus = 'nomavoir'; 
	}
	var status =  document.getElementById(idStatus).value;  
	var output = document.getElementById(id).value;
	var reference = document.getElementById(refId).value;
	//teste le chmp : année , année + mois 
	if (status == "Mois"){
		var NotVisible = output.concat("-YY-MM"); 
	} else {
		var NotVisible = output.concat("-YY");  
	}
	document.getElementById(refId).value = NotVisible;
	if (reference.indexOf("-YY-MM") > -1) {
		output += moment().format("YYMM");
	} else if (reference.indexOf("-YY") > -1) {
		output += moment().format("YY");
	}
	document.getElementById(idOuput).value = output;
}
</script>

