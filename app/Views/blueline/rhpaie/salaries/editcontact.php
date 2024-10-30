<?php
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);
$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company']));
?>
<?php if(isset($company)){ ?>
<input id="id" type="hidden" name="id_companie" value="<?=$company->id;?>" />
<?php } //var_dump($situations);exit; ?>

<!-- Adresse 1 -->
<div class="row">
	<div class="col-sm-12 col-md-12">
		<div class="form-group">
			<label for="nom">Adresse</label>
			<div class="input-group">
				<input type="text" name="adresse1" id="adresse1" class="form-control" value="<?=$salarie->adresse1; ?>">
			</div>
		</div>
	</div>
</div>

<!-- Adresse 2 -->
<div class="row">
	<div class="col-sm-12 col-md-12">
		<div class="form-group">
			<label for="nom">Adresse 2</label>
			<div class="input-group">
				<input type="text" name="adresse2" id="adresse2" class="form-control" value="<?=$salarie->adresse2; ?>">
			</div>
		</div>
	</div>
</div>

<!-- code postale & Ville & Pays -->
<div class="row">
	<!-- code postale -->
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="nom">Code Postal</label>
			<div class="input-group">
				<input type="text" name="codepostal" id="codepostal" class="form-control" value="<?=$salarie->codepostal; ?>">
			</div>
		</div>
	</div>
	<!-- Ville -->
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="nom">Ville</label>
			<div class="input-group">
				<input type="text" name="ville" id="ville" class="form-control" value="<?=$salarie->ville; ?>">
			</div>
		</div>
	</div>
	<!-- Pays -->
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="nom">Pays</label>
			<div class="input-group">
				<input type="text" name="pays" id="pays" class="form-control" value="<?=$salarie->pays; ?>">
			</div>
		</div>
	</div>
</div>

<!-- tél 1 & tél 2 -->
<div class="row">
	<!-- Tél 1 -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Téléphone 1</label>
			<div class="input-group">
				<input type="text" name="tel1" id="tel1" class="form-control" value="<?=$salarie->tel1; ?>" >
			</div>
		</div>
	</div>
	<!-- Tél 2 -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Téléphone 2</label>
			<div class="input-group">
				<input type="text" name="tel2" id="tel2" class="form-control" value="<?=$salarie->tel2; ?>">
			</div>
		</div>
	</div>
</div>

<!-- skype & mail -->
<div class="row">
	<!-- skype -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Skype</label>
			<div class="input-group">
				<input type="text" name="skype" id="skype" class="form-control" value="<?=$salarie->skype; ?>">
			</div>
		</div>
	</div>
	<!-- mail -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Mail</label>
			<div class="input-group">
				<input type="text" name="mail" id="mail" class="form-control" value="<?=$salarie->mail; ?>">
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