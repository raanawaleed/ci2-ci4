	
	<div class="col-sm-12  col-md-12 main"> 
		<div class="row">

			<a href="<?=base_url()?>gestionpret/create" class="btn btn-success" data-toggle="mainmodal"><?=$this->lang->line('application-add');?></a>

			<a type="button" class="btn btn-success" href="<?=base_url()?>exporter/prets_as_excel"><?=$this->lang->line('application_export')?></a>

			</div>
			</div>


	<div class="col-sm-12  col-md-12 main"> 

		<div class="row"> 

		<div class="table-head" style="background-color: #DE2821 ; color: white" > <img src="https://image.flaticon.com/icons/svg/180/180012.svg" width="25px"> <?=$this->lang->line('application_liste_des_prets_salarie');?></div>


	<div class="table-div">
		<table class="dataSorting table" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
		<!--<table class="data table"  rel="<?=base_url()?>" cellspacing="0" cellpadding="0">-->
		<thead>
			<th><?=$this->lang->line('application_salarie');?></th>
			<th><?=$this->lang->line('application_typepret');?></th>
			<th><?=$this->lang->line('application_remboursement');?></th>
			<th><?=$this->lang->line('application_date_pret');?></th>
			<th><?=$this->lang->line('application_duree');?></th>
			<th><?=$this->lang->line('application_montant');?></th>
			<th><?=$this->lang->line('application_montant_remb');?></th>
			<th><?=$this->lang->line('application_date_debut_remb');?></th>
			<th><?=$this->lang->line('application_action');?></th>
		</thead>

		<?php foreach ($prets as $value):?>

		<tr  id="<?=$value->id;?>" >


		<td class="hidden-xs">

					<?php foreach($salaries as $salarie){

							if($salarie->id == $value->id_salarie)
							{
								echo $salarie->nom. ' '.$salarie->prenom ;
							}
						?>
							
					<?php }?>
			
			</td>
 

			<td class="hidden-xs">
				<?php if(isset($value->type_pret)){ echo $value->type_pret;}else{ echo "-";} ?>
				
				</td>

							<td class="hidden-xs">
				<?php if(isset($value->remboursement)){ echo $value->remboursement;}else{ echo "-";} ?>
				
				</td>

							<td class="hidden-xs">
				<?php if(isset($value->date_pret)){ echo $value->date_pret;}else{ echo "-";} ?>
				
				</td>


							<td class="hidden-xs">
				<?php if(isset($value->duree)){ echo $value->duree;}else{ echo "-";} ?>
				
				</td>


							<td class="hidden-xs">
				<?php if(isset($value->montant)){ echo $value->montant;}else{ echo "-";} ?>
				
				</td>

											<td class="hidden-xs">
				<?php if(isset($value->montant_remb)){ echo $value->montant_remb;}else{ echo "-";} ?>
				
				</td>

											<td class="hidden-xs">
				<?php if(isset($value->date_debut_remboursement)){ echo $value->date_debut_remboursement;}else{ echo "-";} ?>
				
				</td>



			<td class="option action">
			            <a href="<?=base_url()?>gestionpret/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>


				        <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>gestionpret/delete/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button> 
				        
			</td>

		</tr>
		<?php endforeach;?>



	 	</table>
	 	<br clear="all">		
	</div>
	</div>
	</div>
	



