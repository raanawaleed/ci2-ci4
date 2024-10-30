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



 			<div class="form-group ">
				<label for="description"><?=$this->lang->line('application_titre_prime');?>*</label>
				<input type="text" name="description" id="description" 
				value="<?php if(isset($value)){echo $value->description;}?>"
				class="form-control" required>
			</div>


 			<div class="form-group ">
				<label for="valeur"><?=$this->lang->line('application_valeur_prime');?> - TND</label>
				<input type="number" name="valeur" id="valeur" class="form-control" 
				value="<?php if(isset($value)){echo $value->valeur;}?>"
				required>
			</div>


 			<div class="form-group ">
				<label for="cotisable"><?=$this->lang->line('application_cotisable');?></label>
				


				<?php

						if($value->cotisable == "on")
						{
							echo ("<input type='checkbox' name='cotisable' id='cotisable'  checked='$value->Imposable'>");
						}
						else
						{
							echo ("<input type='checkbox' name='cotisable' id='cotisable' >");
						}


				?>

			</div>


 			<div class="form-group ">
				<label for="Imposable"><?=$this->lang->line('application_imposable');?></label>

				<?php

						if($value->Imposable == "on")
						{
							echo ("<input type='checkbox' name='Imposable' id='Imposable'  checked='$value->Imposable'>");
						}
						else
						{
							echo ("<input type='checkbox' name='Imposable' id='Imposable' >");
						}


				?>

			</div>














            

 <?php endforeach;?>
</div>
</div>
   <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-success" value="<?=$this->lang->line('application_save');?>"/>
       
        <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
      </div>
<?php echo form_close(); ?>