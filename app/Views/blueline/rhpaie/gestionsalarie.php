<div class="col-sm-12  col-md-12 main">  
		
		<!-- boutons d'actions -->
		<div class="row">
			<!--<a href="<?=base_url()?>gestionsalarie/create" class="btn btn-success" data-toggle="mainmodal">Nouveau Salarié</a>		
--><a type="button" class="btn btn-success" href="<?=base_url()?>exporter/salaries_as_excel">Exporter</a>
			<div class="btn-group">
					<a type="button" class="btn btn-danger" href="<?=base_url()?>exporter/salaries_import_excel" data-toggle="mainmodal"><?=$this->lang->line('application_import')?></a>

				<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="ion-android-arrow-dropdown"></i>
				</button>

				<ul class="dropdown-menu">
					<li>
							<a href="<?=base_url()?>exporter/salaries_import_template"><?=$this->lang->line('application_download_template')?></a>
					</li>
				</ul>
			</div> 
			<a href="<?=base_url()?>gestionsalarie/archive" class="btn btn-primary" >Archive</a>
		</div>
		<!-- table des salariés -->
		<div class="row">
			<div class="table-head"> <?=$this->lang->line('application_liste_des_salariés');?></div>

			<div class="table-div">
				<table class="dataSorting table hover" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
				<thead>
				<th><?=$this->lang->line('application_action');?></th>
					<th><?=$this->lang->line('application_matricule');?></th>
					<th><?=$this->lang->line('application_prénom_nom');?></th>
					<th>Salaire Brut</th>
					<th><?=$this->lang->line('application_address');?></th>
					<th><?=$this->lang->line('application_rh_fonction');?></th>
					<th><?=$this->lang->line('application_matricule_cnss');?></th>
					<th><?=$this->lang->line('application_Situation_familiale');?></th>
					<th><?=$this->lang->line('application_numerocin');?></th>
				</thead>
				
				<?php foreach ($salaries as $value):?>
					<tr  id="<?=$value->id;?>" >
					<td class="option action">
					<!-- éditer -->
					<a href="<?=base_url()?>gestionsalarie/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
					<!-- voir -->
					<a href="<?=base_url()?>gestionsalarie/view/<?=$value->id;?>" class="btn-option" ><i class="fa fa-eye"></i></a>
					<!-- supprimer -->
					<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>gestionsalarie/delete/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button> 
				</td>
				
					<td class="hidden-xs"><?=verif_null($value->code);?></td>
					<td class="hidden-xs"><?=verif_null($value->prenom.' '.$value->nom);?></td>
					<td class="hidden-xs"><?=verif_null($value->salaire_brut);?></td></td>
					<td class="hidden-xs"><?=verif_null($value->adresse1);?></td> 
					<td class="hidden-xs"><?php echo salaries_fun($value->id)?></td>
					<td class="hidden-xs"><?=verif_null($value->numerocnss);?></td>
					<td class="hidden-xs"><?php echo situation_familiale($value->id);?></td>
					<td class="hidden-xs"><?=verif_null($value->numerocin);?></td>
					<!-- actions -->
				
				</tr>
				<?php endforeach;?>

				</table>
				<br clear="all">		
			</div>

		</div>
</div>