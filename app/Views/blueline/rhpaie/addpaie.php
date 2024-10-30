<?php   
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);

$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company'])); 
?>




<div class="row">
	<div class="col-md-12 marginbottom20">
		<div class="table-head">
			<?=$this->lang->line('application_paie_salarie');?>


		</div>
<!-- begin foreach -->



		<div class="subcont">
			<ul class="details col-md-6">

				<li>

			<div class="form-group">
				<label for="chef_famille"><?=$this->lang->line('application_chefdefamille');?>*</label>
				<input type="checkbox" name="chefdefamille" 
				data-labelauty=
					<?=$this->lang->line('application_yes');?>
				 class="checkbox"  >
			</div>
                 </li>


				<li>


			<div class="form-group">
				<label for="salaire_base"><?=$this->lang->line('application_salaire_base');?>*</label>
				<input type="number" name="salaire_base" id="op" class="form-control" >
			</div>

                </li>



				<li>


			<div class="form-group">
				<label for="nb_jour_presence"><?=$this->lang->line('application_nombre_jour_presence');?>*</label>
				<input type="number" name="nb_jour_presence" id="op" class="form-control" >
			</div>

                </li>

				<li>

			<div class="form-group">
				<label for="nbr_enfant_handicapes"><?=$this->lang->line('application_nbr_enfant_handicapes');?>*</label>
				<input type="number" name="nbr_enfant_handicapes" id="op" class="form-control" >
			</div></li>

				<li>

			<div class="form-group">
				<label for="parnets_a_charges"><?=$this->lang->line('application_parents_a_charges');?>*</label>
				<input type="checkbox" name="parnets_a_charges" 
				data-labelauty=
					<?=$this->lang->line('application_yes');?>
				 class="checkbox"  >
			</div>
                 </li>



				<li>
			<div class="form-group">
				<label for="droit_conge"><?=$this->lang->line('application_droit_congés');?></label>
				<input type="number"  step="0.01" value="<?php echo $droitjour ;?>" name="droit_conge" id="droit_conge" class="form-control" >
			</div></li>

<!-- 				<li>

			<div class="form-group">
				<label for="paienonconge"><?=$this->lang->line('application_ne_pas_affiche_les_congés_dans_la_fiche_de_paie');?></label>
				<input type="text" name="paienonconge" id="paienonconge" class="form-control" >
			</div>
                                            </li> -->

				<li>

			<div class="form-group">
				<label for="mode_paiement"><?=$this->lang->line('application_mode_paiement');?></label>

<select name="mode_paiement" id="type_contart" class="chosen-select">


                    <?php foreach($modepaiement as $g){?>

                        <option value="<?=$g->id?>"><?=$g->name?></option>

                    <?php }?>
                </select>
			</div>
                 </li>

				<li>

			<div class="form-group">
				<label for="dateembauche"><?=$this->lang->line('application_date_embauche');?>*</label>

				<input type="text" class="form-control" name="dateembauche" id="dateembauche" class="form-control" placeholder="yyyy-mm-dd" >
			</div>
                   </li>


				
			</ul>

			<span class="visible-xs"></span>

			<ul class="details col-md-6">
				
				<li>

			<div class="form-group">
				<label for="echelon"><?=$this->lang->line('application_echelon');?>*</label>
				<input type="text" name="echelon" id="echelon" class="form-control" >
			</div>
                                            </li>

				
				<li>

			<div class="form-group">
				<label for="nbr_enfant"><?=$this->lang->line('application_nbr_enfant');?>*</label>
				<input type="number" name="nbr_enfant" id="nbr_enfant" class="form-control" >
			</div>
                                            </li>

				<li>

			<div class="form-group">
				<label for="nbr_enfant_boursier"><?=$this->lang->line('application_nbr_enfant_boursiers');?>*</label>
				<input type="number" name="nbr_enfant_boursier" id="nbr_enfant_boursier" class="form-control" >
			</div>
                </li>

				<li>
			<div class="form-group">
				<label for="soldecongeinitial"><?=$this->lang->line('application_solde_de_conge_initial');?>*</label>
				<input type="text" name="soldecongeinitial" id="soldecongeinitial" class="form-control" >
			</div>
               </li>

                                            
				<li>
			<div class="form-group">
				<label for="typepaiment"><?=$this->lang->line('application_type_paiement');?>*</label>
				<select name="typepaiment" id="typepaiment" class="chosen-select">


                    <?php foreach($typepaiement007 as $g){?>

                        <option value="<?=$g->id?>"><?=$g->name?></option>

                    <?php }?>
                </select>
			</div>
                                            </li>
			<div class="form-group">
				<label for="type_contart"><?=$this->lang->line('application_type_contrat');?></label>
								
<select name="type_contart" id="type_contart" class="chosen-select">

                               <?php if((isset($contart))&& ($contart != 0))
                                    {

                                               foreach($typecontarts as $ref)
                                               {

                                                    if($contart == $ref->id )
                                                    {
                                                        echo "<option value='$ref->id' selected>$ref->name</option>";
                                                        break;
                                                    }

                                                

                                                }
                                     }
                                    else
                                    {
                                ?>
                                   

                                   <?php } ?> 


                    <?php foreach($typecontarts as $g){?>

                        <option value="<?=$g->id?>"><?=$g->name?></option>

                    <?php }?>
                </select>			</div>
                 </li>


				<li>

			<div class="form-group">
				<label for="datedepart"><?=$this->lang->line('application_date_depart');?>*</label>

				<input type="text" class="form-control" name="datedepart" id="datedepart" class="form-control" placeholder="yyyy-mm-dd" >
			</div>
                   </li>
                                           				

                                            				<li>
			<div class="form-group">
				<label for="categorie"><?=$this->lang->line('application_category');?></label>
				<input type="text" name="categorie" id="categorie" class="form-control" >
			</div>
                                            </li>
				






			</ul>


			<br clear="all">


   <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-success" value="<?=$this->lang->line('application_archiver');?>"/>
        
   </div>


		</div>

<!-- end of foreach  -->
	</div>
</div>

<?php echo form_close(); ?>