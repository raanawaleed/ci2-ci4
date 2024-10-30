	<div class="col-sm-12  col-md-12 main"> 




		<div class="row">
		<div class="table-head"> <?=$this->lang->line('application_liste_fiche_paie');?></div>


	<div class="table-div">
		<table class="data table" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
		<thead>
			<th><?=$this->lang->line('application_nom_salairie');?></th>
			<th><?=$this->lang->line('application_type_paiement');?></th>


<!-- 			<th><?=$this->lang->line('application_date_debut');?></th>
			<th><?=$this->lang->line('application_date_fin');?></th> -->


			<th><?=$this->lang->line('application_salaire');?></th>
			<th><?=$this->lang->line('application_type_contrat');?></th>


			<th><?=$this->lang->line('application_cnss');?></th>

			<th><?=$this->lang->line('application_salaire_impossable');?></th>
			<th><?=$this->lang->line('application_salaire_annuel');?></th>
			<th><?=$this->lang->line('application_salaire_net');?></th>



			

			
			<th><?=$this->lang->line('application_action');?></th>
		</thead>

		<?php foreach ($paies as $value):?>

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
				<?php                                              

						if((isset($value->typepaiment))&& ($value->typepaiment != 0))
                                    {

                                               foreach($typepaiement007 as $ref)
                                               {

                                                    if($value->typepaiment == $ref->id )
                                                    {
                                                        
														
																		echo "$ref->name";
																	
                                                        break;
                                                    }

                                                

                                                }
                                     }
                                    else
                                    {
                                ?>
                                   

                                   <?php } ?> 
				
				</td>

<!-- 			<td class="hidden-xs"><?php 
					if(isset($value->dateembauche))
						{ echo $value->dateembauche ;}
					else
						{ echo "-";} ?>
			
			</td>


			<td class="hidden-xs">
				<?php if(isset($value->datedepart)){ echo $value->datedepart;}else{ echo "-";} ?>

				</td>  -->

							<td class="hidden-xs">
				<?php if(isset($value->salaire_base)){ echo $value->salaire_base;}else{ echo "-";} ?>

				</td> 

			<td class="hidden-xs">
				                               <?php if((isset($value->type_contart))&& ($value->type_contart != 0))
                                    {

                                               foreach($typecontarts as $ref)
                                               {

                                                    if($value->type_contart == $ref->id )
                                                    {
                                                        echo "$ref->name";
                                                        break;
                                                    }

                                                

                                                }
                                     }
                                    else
                                    {
                                ?>
                                   

                                   <?php } ?>
				
				</td>


							<td class="hidden-xs">
				<?php if(isset($value->cnss)){ echo "<p style=' color:red ;'>$value->cnss</p>";}else{ echo "-";} ?>

				</td> 


							<td class="hidden-xs">
				<?php if(isset($value->salaire_impossable)){ echo "<p style=' color:red ;'>$value->salaire_impossable</p>";}else{ echo "-";} ?>

				</td> 


							<td class="hidden-xs">
				<?php if(isset($value->salaire_annuel)){  echo "<p style=' color:red ;'>$value->salaire_annuel</p>";}else{ echo "-";} ?>

				</td> 


							<td class="hidden-xs">
				<?php if(isset($value->salaire_net)){ echo "<p style=' color:red ;'>$value->salaire_net</p>";}else{ echo "-";} ?>

				</td> 



<!--  		<td class="hidden-xs" style="width:70px"><?=$core_settings->company_prefix;?><?php if(isset($value->reference)){ echo sprintf("%04d",$value->reference);} ?></td>	 -->



			<td class="option action">

<!-- 			            <a href="<?=base_url()?>gestionpaie/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit"></i></a> -->


 						<a href="<?=base_url()?>gestionpaie/view/<?=$value->id;?>" class="btn-option" ><i class="fa fa-eye"></i></a> 

				        <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>gestionpaie/delete/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button> 
				        
			</td>

		</tr>
		<?php endforeach;?>



	 	</table>
	 	<br clear="all">		
	</div>



