<?php
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);
$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company']));
?>
<?php if(isset($company)){ ?>
<input id="id" type="hidden" name="id_companie" value="<?=$company->id;?>" />
<?php } //var_dump($situations);exit; ?>

<!-- Règlement -->
<div class="row">
	<!-- Nom banque-->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Nom de la banque</label>
			<div class="input-group">
				<input type="text" name="nombanque" id="nombanque" class="form-control" value="<?=$salarie->nombanque; ?>">
			</div>
		</div>
	</div>
	<!-- Rib -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Rib</label>
			<div class="input-group">
				<input type="text" name="rib" id="rib" class="form-control" value="<?=$salarie->rib; ?>">
			</div>
		</div>
	</div>
</div>


<!-- IBAN -->
<div class="row">
	<div class="col-sm-12 col-md-12">
		<div class="form-group">
			<label for="nom">IBAN</label>
			<div class="input-group">
				<input type="text" name="iban" id="iban" class="form-control" value="<?=$salarie->iban; ?>">
			</div>
		</div>
	</div>
</div>

<!-- BIC -->
<div class="row">
	<!-- Prénom -->
	<div class="col-sm-12 col-md-12">
		<div class="form-group">
			<label for="nom">BIC</label>
			<div class="input-group">
				<input type="text" name="bic" id="bic" class="form-control" value="<?=$salarie->bic; ?>" >
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