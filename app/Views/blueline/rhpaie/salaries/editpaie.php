<?php
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);
$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company']));
?>
<?php if(isset($company)){ ?>
<input id="id" type="hidden" name="id_companie" value="<?=$company->id;?>" />
<?php } //var_dump($situations);exit; ?>

<!-- Chef de famille + salaire brut -->
<div class="row">
	<!-- chef famille -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Chef de famille</label>
			<div class="input-group">
				<input type="checkbox" id="chef_famille" name="chef_famille" class="checkbox" data-labelauty="Oui"
							<?php if ($salarie->chef_famille == 1) {
								echo "checked";
							} ?>
                                       value="<?php echo $salarie->chef_famille; ?>">
			</div>
		</div>
	</div>
	<!-- salaire brut -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Salaire brut *</label>
			<div class="input-group">
				<input type="text" name="salaire_brut" id="salaire_brut" class="form-control" value="<?=$salarie->salaire_brut; ?>" required>
			</div>
		</div>
	</div>
</div>

<!-- nb enfant + boursiers + handicapés -->
<div class="row">
	<!-- nb enfants -->
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="nom">Nb. d'enfants</label>
			<div class="input-group">
				<input type="text" name="nb_enfants" id="nb_enfants" class="form-control" value="<?=$salarie->nb_enfants; ?>" >
			</div>
		</div>
	</div>
	<!-- boursiers -->
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="nom">Nb. d'enfants boursiers</label>
			<div class="input-group">
				<input type="text" name="nb_enfants_boursiers" id="nb_enfants_boursiers" class="form-control" value="<?=$salarie->nb_enfants_boursiers; ?>">
			</div>
		</div>
	</div>
	<!-- handicapés -->
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="nom">Nb. d'enfants handicapé(e)s</label>
			<div class="input-group">
				<input type="text" name="nb_enfants_handicape" id="nb_enfants_handicape" class="form-control" value="<?=$salarie->nb_enfants_handicape; ?>">
			</div>
		</div>
	</div>
</div>

<!-- Parents à charge & droits congés & solde congé initiale-->
<div class="row">
	<!-- Parents à charge -->
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="nom">Parents à charges</label>
			<div class="input-group">
				<input type="text" name="parents_charges" id="parents_charges" class="form-control" value="<?=$salarie->parents_charges; ?>">
			</div>
		</div>
	</div>
	<!-- droit congés -->
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="nom">Droit congés</label>
			<div class="input-group">
				<input type="text" name="droit_conge" id="droit_conge" class="form-control" value="<?=$salarie->droit_conge; ?>">
			</div>
		</div>
	</div>
	<!-- solde congé initiale -->
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="nom">solde de congés initiale</label>
			<div class="input-group">
				<input type="text" name="solde_conge_initiale" id="solde_conge_initiale" class="form-control"
					    value="<?=$salarie->solde_conge_initiale; ?>"/>
			</div>
		</div>
	</div>
</div>

<!-- Catégorie + echelon -->
<div class="row">
	<!-- Catégorie -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Catégorie</label>
			<div class="input-group">
				<input type="text" name="categorie" id="categorie" class="form-control"
					    value="<?=$salarie->categorie; ?>"/>
			</div>
		</div>
	</div>
	<!-- echelon -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Echelon</label>
			<div class="input-group">
				<input type="text" name="echelon" id="echelon" class="form-control"
					    value="<?=$salarie->echelon; ?>"/>
			</div>
		</div>
	</div>
</div>

<!-- Date embauche + Taux horaire -->
<div class="row">
<!-- date embauche -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Date embauche</label>
			<div class="input-group">
				<input class="form-control datepicker" name="date_debut_embauche" id="date_debut_embauche"
					   type="text" value="<?=$salarie->date_debut_embauche; ?>"/>
			</div>
		</div>
	</div>
	<!-- Taux horaire -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Taux horaire</label>
			<div class="input-group">
				<input class="form-control"  name="tauxhoraire" id="tauxhoraire" type="texte" 
						value="<?=$salarie->tauxhoraire;?>"/>
			</div>
		</div>
	</div>	
</div>
<!-- type paiement + type contrat -->
<div class="row">
	<!-- type paiement -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="end">Type de paiement</label>
				<select name="type_paiement" id="type_paiement" class="chosen-select">
					<?php foreach($paiement as $val){
						if ($val->id == $salarie->type_paiement) {?>
						<option value="<?=$val->id?>" selected><?=$val->name?></option>
						<?php } else { ?>
						<option value="<?=$val->id?>"><?=$val->name?></option>
						<?php }?>
					<?php } ?>
				</select>
		</div>
	</div>

	<!-- type contrat -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
			<label for="nom">Type de contrat</label>
				<select name="type_contrat" id="type_contrat" class="chosen-select">
					<?php foreach($contrat as $val){
						if ($val->id == $salarie->type_contrat) {?>
						<option value="<?=$val->id?>" selected><?=$val->name?></option>
						<?php } else { ?>
						<option value="<?=$val->id?>"><?=$val->name?></option>
						<?php }?>
					<?php } ?>
				</select>
		</div>
	</div>
</div>

<!-- boutons sauvegarder et fermer -->
<div class="modal-footer">
	<input type="submit" name="send" id="btnSubmit" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>

<?php echo form_close(); ?>
