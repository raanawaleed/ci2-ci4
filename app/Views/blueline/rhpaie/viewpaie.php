 <div class="row">
	<div class="col-md-12">
		<h2><?=$company->name;?></h2> 
	</div>
</div>

<div class="row">
	<div class="col-md-12 marginbottom20">
		<div class="table-head">
			<?=$this->lang->line('application_détail_fiche_de_paie');?>

<!-- 			<span class="pull-right"><a href="<?=base_url()?>gestionpaie/update/<?=$idfiche;?>" class="btn btn-primary" data-toggle="mainmodal"><i class="icon-edit"></i> <?=$this->lang->line('application_edit');?></a></span> -->
		</div>
<!-- begin foreach -->

<?php foreach ($paies as $value):?>

		<div class="subcont">
			<ul class="details col-md-6">

				<li><span><?=$this->lang->line('application_chefdefamille');?>:</span> 

				<?php    
					 
					if($value->chefdefamille == 0)
					{
						echo "<p style=' color:green ;'>Vrai</p>";
					}

					else{ echo "<p style=' color:green ;'>Faux</p>";} 

					?>




	                   
                </li>

				<li><span><?=$this->lang->line('application_salaire_base');?>:</span> <?php 
                                            if(isset($value))
                                                    {echo $value->salaire_base;} 
                                            else {echo "nope";} 
                                            ?></li>


                <li><span><?=$this->lang->line('application_nombre_jour_presence');?>:</span> <?php 
                                            if(isset($value))
                                                    {echo $value->nb_jour_presence;} 
                                            else {echo "nope";} 
                                            ?></li>



				<li><span><?=$this->lang->line('application_nbr_enfant_handicapes');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->nbr_enfant_handicapes;} 
                                            else {echo "nope";}  ?></li>

				<li><span><?=$this->lang->line('application_parents_a_charges');?></span>                                               

				<?php    
					 
					if($value->parnets_a_charges == 0)
					{
						echo "<p style=' color:green ;'>Vrai</p>";
					}

					else{ echo "<p style=' color:green ;'>Faux</p>";} 

					?>


                                            </li>

				<li><span><?=$this->lang->line('application_droit_congés');?>2:</span> <?php                                              if(isset($value))
                                                    {echo $value->droit_conge;} 
                                            else {echo "nope";} ?></li>



				<li><span><?=$this->lang->line('application_mode_paiement');?>:</span> <?php                                              

						if((isset($value->mode_paiement))&& ($value->mode_paiement != 0))
                                    {

                                               foreach($modepaiement as $ref)
                                               {

                                                    if($value->mode_paiement == $ref->id )
                                                    {
                                                        echo "$ref->name";
                                                        break;
                                                    }

                                                

                                                }
                                     }
                                    else
                                    {
                                ?>
                                   <option value="0" selected></option>

                                   <?php } ?> 


                                            </li>

				<li><span><?=$this->lang->line('application_date_embauche');?>:</span> <?php  if(isset($value))
                                                    {echo $value->dateembauche;} 
                                            else {echo "nope";} ?></li>

				
				<li><span><?=$this->lang->line('application_echelon');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->echelon;} 
                                            else {echo "nope";}  ?></li>

                 <li><span><?=$this->lang->line('application_nbr_enfant');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->nbr_enfant;} 
                                            else {echo "nope";}?></li>

                <li><span><?=$this->lang->line('application_nbr_enfant_boursiers');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->nbr_enfant_boursier;} 
                                            else {echo "nope";}  ?></li>



				
			</ul>

			<span class="visible-xs"></span>

			<ul class="details col-md-6">
				



				<li><span><?=$this->lang->line('application_solde_de_conge_initial');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->soldecongeinitial;} 
                                            else {echo "nope";} ?></li>

				<li><span><?=$this->lang->line('application_type_paiement');?>:</span> <?php                                              

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

                                            </li>


				<li><span><?=$this->lang->line('application_type_contrat');?>:</span> 



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
              



                                            </li>

				<li><span><?=$this->lang->line('application_date_depart');?>:</span> <?php                                            if(isset($value))
                                                    {echo $value->datedepart;} 
                                            else {echo "nope";} ?></li>
			
				<li><span><?=$this->lang->line('application_category');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->categorie;} 
                                            else {echo "nope";} ?></li>

                <li><span style=' color:red ;'><?=$this->lang->line('application_cnss');?>:</span> <?php                                              if(isset($value))
                                                    { 
                                                       echo "<p style=' color:red ;'>$value->cnss</p>";
                                                    } 
                                            else {echo "nope";} ?></li>

                <li><span style=' color:red ;'><?=$this->lang->line('application_salaire_impossable');?>:</span> <?php                                              if(isset($value))
                                                    {
                                                        echo "<p style=' color:red ;'>$value->salaire_impossable</p>";
                                                    } 
                                            else {echo "nope";} ?></li>

                <li><span style=' color:red ;' ><?=$this->lang->line('application_salaire_annuel');?>:</span> <?php                                              if(isset($value))
                                                    {
                                                        echo "<p style=' color:red ;'>$value->salaire_annuel</p>";
                                                    } 
                                            else {echo "nope";} ?></li>

                <li><span style=' color:red ;'><?=$this->lang->line('application_salaire_net');?>:</span> <?php                                              if(isset($value))
                                                    {
                                                        echo "<p style=' color:red ;'>$value->salaire_net</p>";
                                                    } 
                                            else {echo "nope";} ?></li>

			


			</ul>
			<br clear="all">
		</div>
<?php endforeach;?>
<!-- end of foreach  -->
	</div>
</div>