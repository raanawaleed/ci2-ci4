<?php   
$attributes = array('class' => '', 'id' => '_clients', 'autocomplete' => 'off');
echo form_open_multipart($form_action, $attributes); 
?>

 
<?php if(isset($view)){ ?>
<input id="view" type="hidden" name="view" value="true" />
<?php } ?>

	<div class="col-sm-12 col-md-12 main"> 

<div class="row">
<?php foreach ($item as $value):?>


                        <div class="form-group">
                                <label for="chefdefamille"><?=$this->lang->line('application_chefdefamille');?> 1</label>
								<input type="checkbox" name="chefdefamille" 
									data-labelauty="<?=$this->lang->line('application_yes');?>"
									 class="checkbox"
									  value="<?php if(isset($value)){echo $value->chefdefamille;}?>" >
                        </div>

                        <div class="form-group col-md-6">
                                <label for="salaire_base"><?=$this->lang->line('application_salaire_brut');?> </label>
                                <input type="text" name="salaire_base" id="salaire_base" class="form-control" 
                                 value="<?php if(isset($value)){echo $value->salaire_base;}?>"
                                required>
                        </div>

                        <div class="form-group col-md-6">
                                <label for="nbr_enfant_handicapes"><?=$this->lang->line('application_nbr_enfant_handicapes');?></label>
                                <input type="text" name="nbr_enfant_handicapes" id="nbr_enfant_handicapes" class="form-control" 
                                 value="<?php if(isset($value)){echo $value->nbr_enfant_handicapes;}?>"
                                required>
                        </div>

                        <div class="form-group col-md-6">
                                <label for="parnets_a_charges"><?=$this->lang->line('application_parents_a_charges');?></label>
                                <input type="text" name="parnets_a_charges" id="parnets_a_charges" class="form-control" 
                                 value="<?php if(isset($value)){echo $value->parnets_a_charges;}?>"
                                required>
                        </div>

                        <div class="form-group col-md-6">
                                <label for="droit_conge"><?=$this->lang->line('application_droit_congés');?></label>
                                <input type="text" name="droit_conge" id="droit_conge" class="form-control" 
                                 value="<?php if(isset($value)){echo $value->droit_conge;}?>"
                                required>
                        </div>

<!--                          <div class="form-group">
                                <label for="paienonconge"><?=$this->lang->line('application_ne_pas_affiche_les_congés_dans_la_fiche_de_paie');?>
                                	
                                </label>
                                <input type="text" name="paienonconge" id="paienonconge" class="form-control" 
                                 value="<?php if(isset($value)){echo $value->paienonconge;}?>"
                                required >
                        </div> -->

                         <div class="form-group col-md-6">
                                <label for="mode_paiement"><?=$this->lang->line('application_mode_paiement');?>
                                	
                                </label>
                                <input type="text" name="mode_paiement" id="mode_paiement" class="form-control" 
                                 value="<?php if(isset($value)){echo $value->mode_paiement;}?>"
                                required >
                        </div>

 						<div class="form-group col-md-6">
			  				<label for="dateembauche"><?=$this->lang->line('application_date_embauche');?>*</label>

							<input type="text" class="datepicker form-control" name="dateembauche" id="dateembauche" class="form-control" value="<?php if(isset($value)){echo $value->dateembauche;}?>" required>

						</div>


						
							<div class="form-group">
								<label for="echelon"><?=$this->lang->line('application_echelon');?>
									
								</label>
								<input type="text" name="echelon" id="echelon" class="form-control" 
								value="<?php if(isset($value)){echo $value->echelon;}?>" required >
							</div>

						<div class="form-group col-md-6">
							<label for="nbr_enfant"><?=$this->lang->line('application_nbr_enfant');?>*</label>
							<input type="number" name="nbr_enfant" id="nbr_enfant" class="form-control" 
							value="<?php if(isset($value)){echo $value->nbr_enfant;}?>" required>
						</div>

						<div class="form-group col-md-6">
							<label for="nbr_enfant_boursier"><?=$this->lang->line('application_nbr_enfant_boursiers');?>*</label>
							<input type="number" name="nbr_enfant_boursier" id="nbr_enfant_boursier" class="form-control" 
								value="<?php if(isset($value)){echo $value->nbr_enfant_boursier;}?>"
							required>
						</div>

						<div class="form-group col-md-6">
							<label for="typepaiment"><?=$this->lang->line('application_type_paiement');?>*</label>
							<input type="number" name="typepaiment" id="nbr_enfant_boursier" class="form-control" 
								value="<?php if(isset($value)){echo $value->typepaiment;}?>"
							required>
						</div>

						<div class="form-group col-md-6">
				 			<label for="soldecongeinitial"><?=$this->lang->line('application_solde_de_conge_initial');?>*</label>
							<input type="text" name="soldecongeinitial" id="soldecongeinitial" class="form-control" 
							value="<?php if(isset($value)){echo $value->soldecongeinitial;}?>"	required>
						</div>

						<div class="form-group col-md-6">
							<label for="type_contart"><?=$this->lang->line('application_type_contrat');?></label>
						
						<select name="type_contart" id="type_contart" class="chosen-select">

                               <?php if((isset($value->type_contart))&& ($value->type_contart != 0))
                                    {

                                               foreach($typecontarts as $ref)
                                               {

                                                    if($value->type_contart == $ref->id )
                                                    {
                                                        echo "<option value='$ref->id' selected>$ref->name</option>";
                                                        break;
                                                    }

                                                

                                                }
                                     }
                                    else
                                    {
                                ?>
                                   <option value="0" selected></option>

                                   <?php } ?> 


                    <?php foreach($typecontarts as $g){?>

                        <option value="<?=$g->id?>"><?=$g->name?></option>

                    <?php }?>
                </select>
						</div>
			               


							

						<div class="form-group col-md-6">
							<label for="datedepart"><?=$this->lang->line('application_date_depart');?>*</label>

							<input type="text" class="datepicker form-control" name="datedepart" id="datedepart" class="form-control" 
								value="<?php if(isset($value)){echo $value->datedepart;}?>"
							required>
						</div>
			                   			                                           				

			                                            				
						<div class="form-group col-md-6">
							<label for="categorie"><?=$this->lang->line('application_category');?></label>
							<input type="text" name="categorie" id="categorie" class="form-control" 
								value="<?php if(isset($value)){echo $value->categorie;}?>"
							required>
						</div>
			                                            

 <?php endforeach;?>

</div>
</div>

<?php
$access = array();
if(isset($client)){ $access = explode(",", $client->access); }
?>

        <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
        <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
        </div>
<?php echo form_close(); ?>