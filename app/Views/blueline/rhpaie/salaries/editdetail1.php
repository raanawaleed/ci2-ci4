<?php
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);
$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company']));
?>
<?php if(isset($company)){ ?>
<input id="id" type="hidden" name="id_companie" value="<?=$company->id;?>" />
<?php } //var_dump($salarie);exit; ?>

<!-- code salarié -->
<div class="row">
	<!-- code -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_matricule');?></label>
			<div class="input-group">
				<input type="text" name="code" id="code" class="form-control" value="<?=$salarie->code; ?>">
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


<div class="row">
	<!-- genre -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Genre</label>
				<select name="genre" id="genre" class="chosen-select">
					<option value="0"> - </option>
					<?php foreach($genre as $val){
						if ($val->id == $salarie->genre) {?>
						<option value="<?=$val->id?>" selected><?=$val->name?></option>
						<?php } else { ?>
						<option value="<?=$val->id?>"><?=$val->name?></option>
						<?php }?>
					<?php } ?>
				</select>
		</div>
	</div>
	<!-- situation familiale -->
	<div class="col-sm-12 col-md-6">
			<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_Situation_familiale');?> *</label>
				<select name="situationfamiliale" id="Situation" class="chosen-select">
					<option value="0"> - </option>
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

<!-- date de naissance & lieu de naissance -->
<div class="row">
	<!-- date naiss -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom"><?=$this->lang->line('application_date_naissance');?></label>
			<div class="input-group">
				<input class="form-control datepicker not-required" name="datedenaissance" id="datedenaissance" 
					   type="text" value="<?=$salarie->datedenaissance; ?>"/>
			</div>
		</div>
	</div>
	<!-- lieu -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Lieu de naissance</label>
			<div class="input-group">
				<input type="text" name="lieudenaissance" id="lieudenaissance" class="form-control" value="<?=$salarie->lieudenaissance; ?>">
			</div>
		</div>
	</div>
</div>

<!-- Num CIN & cdate de délivrance -->
<div class="row">
	<!-- num cin -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Numéro CIN</label>
			<div class="input-group">
				<input type="text" name="numerocin" id="numerocin" class="form-control" value="<?=$salarie->numerocin; ?>">
			</div>
		</div>
	</div>
	<!-- cdate de delivrance -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Date de délivrance</label>
			<div class="input-group">
				<input class="form-control datepicker not-required" name="datedelivrance" id="datedelivrance" 
					   type="text" value="<?=$salarie->datedelivrance; ?>"/>
			</div>
		</div>
	</div>
</div>

<!-- N° CNSS & Service d'affectation -->
<div class="row">
	<!-- num cnss -->	
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Numéro CNSS</label>
			<div class="input-group">
				<input type="text" name="numerocnss" id="numerocnss" class="form-control" value="<?=$salarie->numerocnss; ?>">
			</div>
		</div>
	</div>
    <!-- Service d'affectation -->

	<div class="col-sm-12 col-md-6">
    <div class="form-group">
        <label for="nom">Service d'affectation</label>
        <div class="input-group">
            <select name="seraffectation" id="seraffectation" >
                <option value="MMS" >MMS</option>
                <option value="BIM2D" >BIM2D</option>
                <option value="BIM3D" >BIM3D</option>
            </select>
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