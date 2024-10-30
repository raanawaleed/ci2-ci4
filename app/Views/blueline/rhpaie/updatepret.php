<?php   
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);

$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company'])); 
?>


<?php if(isset($company)){ ?>
<input id="id" type="hidden" name="id_companie" value="<?=$company->id;?>" />
<?php } ?>

	<div class="col-sm-12 col-md-12 main"> 

<div class="row">
<?php foreach ($item as $value):?>

			<div class="form-group">

				<label for="id_salarie"><?=$this->lang->line('application_salarie');?>*</label>

				<select name="id_salarie" id="id_salarie" class="chosen-select">
						<option value="0" selected></option>
						<?php foreach($salaries as $salarie){?>


                                <?php if($value->id_salarie == $salarie->id)
                                {
                                    echo "<option value='$salarie->id' selected>$salarie->nom</option>";
                                }
                                else
                                {
                                    ?>
                                   <option value="0" ></option>

                                   <?php } ?>



							<option value="<?=$salarie->id?>"><?=$salarie->nom?></option>
						<?php }?>
				</select>
			</div>

			<div class="form-group">
				<label for="type_pret"><?=$this->lang->line('application_typepret');?>*</label>
				<select name="type_pret" id="type_pret" class="chosen-select">
					
					<?php foreach($pret as $g){?>


                                <?php if($value->type_pret == $g)
                                {
                                    echo "<option value='$g' selected>$g</option>";
                                }
                                else
                                {
                                    ?>
                                   <option value="0" ></option>

                                   <?php } ?>



						<option value="<?=$g?>"><?=$g?></option>
					<?php }?>
				</select>
			</div>

 			<div class="form-group ">
				<label for="remboursement"><?=$this->lang->line('application_remboursement');?>*</label>
				 <select name="remboursement" id="remboursement" class="chosen-select">
				
					<?php foreach($remboursement as $g){?>

                                <?php if($value->remboursement == $g)
                                {
                                    echo "<option value='$g' selected>$g</option>";
                                }
                                else
                                {
                                    ?>
                                   <option value="0" ></option>

                                   <?php } ?>


						<option value="<?=$g?>"><?=$g?></option>
					<?php }?>
				</select>
			</div>


			<div class="form-group">
				<label for="date_pret"><?=$this->lang->line('application_date_pret');?>*</label>
				<input type="text" class="datepicker form-control" name="date_pret" id="date_pret" class="form-control" 
				value="<?php if(isset($value)){echo $value->date_pret;}?>"
				required>
			</div>

			<div class="form-group">
			<label for="duree"><?=$this->lang->line('application_duree');?>*</label>
			<input type="number" name="duree" id="duree" class="form-control"
			value="<?php if(isset($value)){echo $value->duree;}?>"
			 required>
			</div>


			<div class="form-group">
				<label for="statut"><?=$this->lang->line('application_montant');?>*</label>
				<input type="number" name="montant" id="montant" class="form-control" 
				value="<?php if(isset($value)){echo $value->montant;}?>"
				required>
			</div>

			<div class="form-group">
				<label for="interet"><?=$this->lang->line('application_interet');?>*</label>
				<input type="number" step="0.01" name="interet" id="interet" class="form-control" 
				value="<?php if(isset($value)){echo $value->interet;}?>"
				required>
			</div>

			<div class="form-group">
				<label for="montant_remb"><?=$this->lang->line('application_montant_remb');?>*</label>
				<input type="number" name="montant_remb" id="montant_remb" class="form-control" 
				value="<?php if(isset($value)){echo $value->montant_remb;}?>"
				required>
			</div>

			<div class="form-group">
				<label for="montant_remboursement_moins"><?=$this->lang->line('application_montant_remb_moins');?>*</label>
				<input type="number" name="montant_remboursement_moins" id="montant_remboursement_moins" class="form-control" 
				value="<?php if(isset($value)){echo $value->montant_remboursement_moins;}?>"
				required>
			</div>

			<div class="form-group">
				<label for="date_debut_remboursement"><?=$this->lang->line('application_date_debut_remb');?>*</label>
				<input type="text" class="datepicker form-control" name="date_debut_remboursement" id="date_debut_remboursement" class="form-control" 
				value="<?php if(isset($value)){echo $value->date_debut_remboursement;}?>"
				required>
			</div>
            

 <?php endforeach;?>
</div>
</div>
   <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-success" value="<?=$this->lang->line('application_save');?>"/>
       
        <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
      </div>
<?php echo form_close(); ?>