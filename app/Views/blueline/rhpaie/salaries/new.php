<?php
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);
$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company']));
?>
<?php if(isset($company)){ ?>
<input id="id" type="hidden" name="id_companie" value="<?=$company->id;?>" />
<?php } ?>

<!-- Matricule -->
<div class="row">
	<div class="col-sm-12 col-md-3">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_matricule');?></label>
			<div class="input-group">
				<input type="text" name="code" id="code" class="form-control">
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
				<input type="text" name="prenom" id="prenom" class="form-control" required>
			</div>
		</div>
	</div>
	<!-- Nom -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_name');?> *</label>
			<div class="input-group">
				<input type="text" name="nom" id="nom" class="form-control" required>
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
				<input class="form-control datepicker" name="datedenaissance" id="datedenaissance" type="text" />
			</div>
		</div>
	</div>
	<!-- situation fami -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_Situation_familiale');?> *</label>
				<select name="situationfamiliale" id="Situation" class="chosen-select">
					<option value="0" selected> - </option>
					<?php foreach($situations as $situation){?>
						<option value="<?=$situation->id?>"><?=$situation->name?></option>
					<?php }?>
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
				<input class="form-control datepicker" name="date_debut_embauche" id="date_debut_embauche" type="text" />
			</div>
		</div>
	</div>
	<!-- contrat travail -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_contrat_de_travail');?> *</label>
				<select name="type_contrat" id="type_contrat" class="chosen-select">
					<option value="0" selected> - </option>
					<?php foreach($typecontarts as $contrat){?>
						<option value="<?=$contrat->id?>"><?=$contrat->name?></option>
					<?php }?>
				</select>
		</div>
	</div>
</div>

<!-- fonction -->
<div class="row">
			<div class="col-md-12">
			<div id="item-selector" style="position:relative">
				<div class="form-group">
					<label for="idfonction"><?=$this->lang->line('application_fonction');?>*</label>
					<select name="idfonction" class="chosen-selecte">
						<?php
						if($fi == 0){
							echo("<option value='' selected>La liste est vide ! </option>");
						}
						?>
						<option value="">Sélectionnez une option</option>
						<?php foreach($fonctions as $fonction){?>
							<option value="<?=$fonction->id?>"><?=$fonction->name?></option>
						<?php }?>
					</select>
<a class="btn btn-primary tt addprojectClient col-md-1" id="addproject" title="<?=$this->lang->line('application_neauveau_fonction');?>" style="position:absolute;top:0;    right: 0;margin:0 !important;"><i class="fa fa-plus"></i></a>
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
					<input type="text" name="fonctionname" id="fonctionname" class="form-control">
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
