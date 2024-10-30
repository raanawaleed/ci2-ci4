<div class="col-sm-12  col-md-12 main"> 
	<!-- Titre de la page -->
	<div class="row tile-row">
		<div class="col-md-2 col-xs-12 tile blue"><h1><span>Clients</span></h1></div>
	</div>
	<!-- Boutons d'actions -->
	<div class="row">
		<a href="<?=base_url()?>clients/company/create" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_add_client');?></a>
		<a type="button" class="btn btn-success" href="<?=base_url()?>exporter/clients_as_excel"><i class="fa fa-file-excel-o"></i> <?=$this->lang->line('application_export')?></a>
		
		<a type="button" class="btn btn-info" href="<?=base_url()?>clients/ClientPassager"><?=$this->lang->line('application_clients_passagers')?></a>
	</div>
	<!-- tableau -->
	<div class="row">
		<div class="table-head"><?=$this->lang->line('application_clients');?></div>
			<div class="table-div">
				<table class="dataSorting table" id="clients" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
					<thead>
				<th class="" style="width:70px"><?=$this->lang->line('application_quotation_id');?></th>
				<th><?=$this->lang->line('application_company_name');?></th>
				<th class=""><?=$this->lang->line('application_primary_contact');?></th>
				<th class=""><?=$this->lang->line('application_email');?></th>
				<th class=""><?=$this->lang->line('application_website');?></th>
				<th><?=$this->lang->line('application_action');?></th>
			</thead>
			<?php foreach ($companies as $value):?>
				<?php if ($value->passager == 0){ ?>
					<tr  id="<?=$value->id;?>" >
						<!-- id -->
						<td class="" style="width:70px"><?=$core_settings->company_prefix;?><?php if(isset($value->reference)){ echo sprintf("%04d",$value->reference);} ?></td>			
						<!-- Nom -->
						<td>
						 <span class="label label-info">
							 <?php 
									$max = 40;
										 if (strlen($value->name) >= $max) {
										$chaine = substr($value->name, 0, $max).'...';
										 }else{
											$chaine = $value->name; 
										 }
										 echo $chaine;
							 ?>
						 </span>
						</td>
						<!-- contact -->
						<td class=""><?php if(isset($value->client_id->firstname)){ echo $value->client_id->firstname.' '.$value->client_id->lastname;}else{ echo "-";} ?></td>
						<td class=""><?php if(isset($value->email)){ echo $value->email;}else{ echo "-";}?></td>
						<td class=""><?php echo $value->website = empty($value->website) ? "-" : '<a target="_blank" href="http://'.$value->website.'">'.$value->website.'</a>' ?></td>
						<td class="option action">
									<a href="<?=base_url()?>clients/company/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
									<a href="<?=base_url()?>clients/view/<?=$value->id;?>" class="btn-option" ><i class="fa fa-eye"></i></a>
									<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>clients/company/delete/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>

						</td>
					</tr>
				<?php } ?>
			<?php endforeach;?>
			</table>
			<br clear="all">		
		</div>
	</div>
</div>