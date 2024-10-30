<?php
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);
$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company']));
?>
<?php if(isset($company)){ ?>
<input id="id" type="hidden" name="id_companie" value="<?=$company->id;?>" />
<?php }  ?>

<!-- matricule -->
<div class="row">
	<div class="col-sm-12 col-md-3">
		<div class="form-group">
			<label for="nom">Matricule</label>
			<div class="input-group">
				<input type="text" name="code" id="code" class="form-control" value="<?=$salarie->code; ?>" >
			</div>
		</div>
	</div>
</div>

<!-- nom & prénom -->
<div class="row">
	<!-- Prénom -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_firstname');?> *</label>
			<div class="input-group">
				<input type="text" name="prenom" id="prenom" class="form-control" value="<?=$salarie->prenom; ?>" required>
			</div>
		</div>
	</div>
	<!-- Nom -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_name');?> *</label>
			<div class="input-group">
				<input type="text" name="nom" id="nom" class="form-control" value="<?=$salarie->nom; ?>" required>
			</div>
		</div>
	</div>
</div>

<!-- date de naissance & situation fami -->
<div class="row">
	<!-- date naiss -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_date_naissance');?></label>
			<div class="input-group">
				<input class="form-control datepicker" name="datedenaissance" id="datedenaissance" 
					   type="text" value="<?=$salarie->datedenaissance; ?>"/>
			</div>
		</div>
	</div>
	<!-- situation fami -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_Situation_familiale');?> *</label>
				<select name="situationfamiliale" id="Situation" class="chosen-select">
					<?php foreach($situations as $situation){
						if ($situation->id == $salarie->situationfamiliale) {?>
						<option value="<?=$situation->id?>" selected><?=$situation->name?></option>
						<?php } else { ?>
						<option value="<?=$situation->id?>"><?=$situation->name?></option>
						<?php }?>
					<?php } ?>
				</select>
		</div>
	</div>
</div>

<!-- date d'embauche & contrat de travail -->
<div class="row">
	<!-- date embauche -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_date_embauche');?> *</label>
			<div class="input-group">
				<input class="form-control datepicker" name="date_debut_embauche" id="date_debut_embauche" 
					   type="text" value="<?=$salarie->date_debut_embauche; ?>" required/>
			</div>
		</div>
	</div>
	<!-- contrat travail -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_contrat_de_travail');?> *</label>
				<select name="type_contrat" id="type_contrat" class="chosen-select">
					<?php foreach($typecontrats as $contrat){?>
						<?php if ($contrat->id == $salarie->type_contrat) {?>
						<option value="<?=$contrat->id?>" selected><?=$contrat->name?></option>
						<?php } else { ?>
						<option value="<?=$contrat->id?>"><?=$contrat->name?></option>
						<?php }?>
					<?php }?>
				</select>
		</div>
	</div>
</div>

<div class="row">
	<!-- salaries image -->
	
	<div class="col-md-6">
			<div style="padding: 0px 15px 15px 0px;">
				<img src="<?=base_url('/files/media/'.$salarie->file)?>" alt="" width="80%" height="50%">
			</div>
	</div>

	<div class="col-md-6">
			<div class="form-group">
				<label for="file"><?=$this->lang->line('application_file');?></label>
				<input type="file" id="profile_image" name="profile_image" size="33" />
			</div>
			<div class="form-group">
					<label for="nom"><?=$this->lang->line('application_date_fin_embauche');?></label>
					<div class="input-group">
						<input class="form-control datepicker" name="date_fin_embauche" id="date_fin_embauche" 
							type="text"  value="<?=$salarie->date_fin_embauche; ?>" min="<?=$salarie->date_debut_embauche; ?>" />
					</div>
				</div>
	</div>
</div>

<!-- fonction -->
<div class="row">
			<div class="col-md-12">
			<div id="item-selector" style="position:relative">
				<div class="form-group">
					<label for="idfonction"><?=$this->lang->line('application_fonction');?>*</label>
					<select name="idfonction" class="chosen-select">
						<?php foreach($fonctions as $fonction){?>
							<?php if ($fonction->id == $salarie->idfonction) {?>
							<option value="<?=$fonction->id?>" selected><?=$fonction->name?></option>
							<?php } else { ?>
							<option value="<?=$fonction->id?>"><?=$fonction->name?></option>
							<?php }?>
						<?php }?>
					</select>
					<a class="btn btn-primary tt addprojectClient" id="addproject" title="<?=$this->lang->line('application_neauveau_fonction');?>" style="position:absolute;top:0;    right:10;margin:0 !important;"><i class="fa fa-plus"></i></a>
				</div>
			</div>
			</div>
			</div>
	

<!-- creating new function -->
<div class="row">
			<div class="col-md-12">
			<div id="item-editor">
				<div class="form-group">
					<label for="fonctionname"><?=$this->lang->line('application-Libelle');?>*</label>
					<input type="text" name="fonctionname" id="fonctionname" class="form-control" >
				</div>
	 
				<div class="form-group">
					<label for="description"><?=$this->lang->line('application_description');?></label>
					<input type="text" name="description" id="description" class="form-control" >
				</div>

				
			</div>
		</div>
	</div>


<!-- boutons sauvegarder et fermer -->	
<div class="modal-footer">
	<input type="submit" name="send" id="btnSubmit" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>

<?php echo form_close(); ?>
<script type="text/javascript">
   function validate(tag,type,message){
        if(type=='error'){
            tag.next().text(message).addClass('color-red');
            tag.addClass('border-red').removeClass('border-green');
        }else{
            tag.next().text('').removeClass('color-red');
            tag.addClass('border-green').removeClass('border-red');
        }
    }
 $("#date_fin_embauche").on("click", function(){
        var value = $(this).val().trim();
		var datemin = $('#date_debut_embauche').val().trim();
        if(value < datemin ){
			console.log('helll');
			validate($("#date_fin_embauche"), "error", "Champ doit etre suppérieur");
			$("input[name='date_fin_embauche']").css("border", "5px solid red");
			$("#btnSubmit").attr("disabled", true);
        }else{
            validate($(this), "success", "");
			$("#btnSubmit").attr("disabled", false);
        }
    })



</script>
