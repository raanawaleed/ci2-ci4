<div class="col-sm-12  col-md-12 main"> 
	 <!-- entête + quelques statistiques -->
	 <div class="row tile-row">
		<div class="col-md-2 col-xs-12 tile blue">
			<h1><span><?=$this->lang->line('application_items');?></span></h1>
		</div>
	</div>
	<!-- boutons d'actions -->
	<div class="row">
		<a href="<?=base_url()?>items/create_items" class="btn btn-success" data-toggle="mainmodal"><?=$this->lang->line('application_create_item');?>
		</a>
		<a type="button" class="btn btn-success" href="<?=base_url()?>exporter/itemsxlsx">
			<i class="fa fa-file-excel-o"></i>
			<?=$this->lang->line('application_export')?>
		</a>
		<a href="<?=base_url()?>items/famille/" class="btn btn-primary"><?=$this->lang->line('application_family_items');?>
		</a>
	</div>
	
	<!-- tableau de la liste des articles -->
	<div class="row">
		<div class="table-head"> <?=$this->lang->line('application_items');?></div>
		<div class="table-div">
			<table class="dataSorting table" id="items" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
				<thead>
					<th><?=$this->lang->line('application_name');?></th>
					<th><?php echo 'Description'; ?></th>
					<th class="hidden-xs"><?=$this->lang->line('application_family');?></th>
					<th class="hidden-xs text-right"><?=$this->lang->line('application_prix_ht');?></th>
					<th class="hidden-xs text-right"><?=$this->lang->line('application_prixttc');?></th>
					<th class="hidden-xs"><?=$this->lang->line('application_taxe_tva');?></th>
					<th class="hidden-xs"><?=$this->lang->line('application_unit');?></th>
					<th><?=$this->lang->line('application_action');?></th>
				</thead>
				
				<?php foreach ($items as $value):?>
				<tr id="<?=$value->id;?>" >
					<td><?=$value->name;?></td>
					<td class="hidden-xs"><?=$value->description;?></td>
					<td class="hidden-xs"><?=$value->type;?></td>
					<td class="hidden-xs text-right"><?=display_money($value->value, '', $core_settings->chiffre);?></td>
					<td class="hidden-xs text-right"><?=display_money(($value->value * $value->tva) / 100 + $value->value, '', $core_settings->chiffre);?></td>
					<td class="hidden-xs"><?php if($value->tva!=null && $value->tva!=0){ echo($value->tva.'%'); }?></td>
					<td class="hidden-xs"><?=$value->unit;?></td>
					<td class="option action">
						<a href="<?=base_url()?>items/update_items/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i>
						</a>
						<a href="<?=base_url()?>items/copy/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-copy" title="Dupliquer"></i>
						</a>
						<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>items/delete_items/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i>
						</button>
					</td>
				</tr>
				<?php endforeach;?>
			</table>
		</div>
	</div>
	<br clear="all">
</div>