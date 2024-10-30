	
	<div class="col-sm-12  col-md-12 main"> 
		<div class="row">
<!-- 
			<a href="<?=base_url()?>gestionprime/create" class="btn btn-success" data-toggle="mainmodal"><?=$this->lang->line('application-add');?></a> -->
<!-- 
			<a type="button" class="btn btn-success" href="<?=base_url()?>exporter/conges_as_excel"><?=$this->lang->line('application_export')?></a> -->

			</div>
			</div>


	<div class="col-sm-12  col-md-12 main"> 

		<div class="row"> 

		<div class="table-head" style="background-color: #DE2821 ; color: white" > <img src="https://image.flaticon.com/icons/svg/180/180012.svg" width="25px"> <?=$this->lang->line('application_liste_gestionprime');?></div>


	<div class="table-div">
		<table class="data table"  rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
		<thead>
			<th><?=$this->lang->line('application_id');?></th>
			<th><?=$this->lang->line('application_prénom_nom');?></th>
			<th><?=$this->lang->line('application_titre_prime');?></th>
			<th><?=$this->lang->line('application_valeur_prime');?></th>
			<th><?=$this->lang->line('application_annee');?></th>
			<th><?=$this->lang->line('application_moins');?></th>
			<th><?=$this->lang->line('application_action');?></th>


			

		</thead>

		<?php foreach ($primes as $value):?>

		<tr  id="<?=$value->id;?>" >

 			<td><?php echo("$value->id"); ?></td>


			<td class="hidden-xs">
				<?php if(isset($value->id)){ 

					

						foreach ($salaries as $key ) {


								if($key->id == $value->id_salarie)
								{
									echo $key->nom ; echo(" "); echo $key->prenom;
								}
							# code...
						}

					}else{ echo "-";} ?>
				
				</td>


				<?php if(isset($value->id)){ 

					

						foreach ($azerty as $key22 ) {


								if($key22->id == $value->id_prime)
								{
									echo ("<td>$key22->description</td>");
									echo ("<td>$key22->valeur</td>");
									
								}
							# code...
						}

					}else{ echo "-";} ?>




				<td><?php echo("$value->annee"); ?></td>
				<td><?php echo("$value->moins"); ?></td>

			<td class="option action">


<!-- 			            <a href="<?=base_url()?>gestionaffecterprime/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit"></i></a> -->


				        <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>gestionaffecterprime/delete/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button> 
				        
			</td>



		</tr>
		<?php endforeach;?>


	 	</table>


	 	<br clear="all">	


	 	   <div class="modal-footer">
       
        <a class="btn"  href="<?=base_url()?>gestionprime"><?=$this->lang->line('application_close');?></a>
      </div>		
	</div>
	</div>
	</div>
	/



