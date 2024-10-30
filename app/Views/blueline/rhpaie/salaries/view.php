<?php 
$this->load->helper('my_functions_helper');
$this->load->helper('mydbhelper_helper');
?>
<!-- boutons d'action -->
<div class="row">
	<div class="col-xs-12 col-sm-12">
		
		<div class="btn-group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle=	"dropdown" aria-haspopup="true" aria-expanded="false">
			Attestation <i class="ion-android-arrow-dropdown"></i>
			</button>
			<ul class="dropdown-menu">			
			<li><a href="<?=base_url()?>gestionsalarie/attestation_travail/<?=$salarie->id;?>/show" >de travail</a></li>
			<li><a href="<?=base_url()?>invoices/attestation_salaire/<?=$salarie->id;?>/show" target="_blank">de salaire</a></li>
			<li><a href="<?=base_url()?>invoices/attestation_retenu/<?=$salarie->id;?>/show" target="_blank">de retenu d'impôt</a></li>
			</ul>
		</div>
		
		<!-- liste des salariés -->
		<a href="<?=base_url()?>gestionsalarie" class="btn btn-warning right">Liste des salariés</a>	
	</div>
</div>

<div class="row">
	<!-- détail du salarié -->
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-3">
		<!-- stylet de modification -->	
		<div class="table-head">Détail du salarié
			<span class=" pull-right option-icon"> 
				<a href="<?=base_url()?>gestionsalarie/updatedetail/<?=$salarie->id;?>" data-toggle="mainmodal" data-target="#mainModal">
					<i class="fa fa-edit" title="Modifier"></i>
				</a>
			</span>
		</div>
		<div class="subcont">
			<ul class="details col-xs-12 col-sm-12">
					<li><span><?=$this->lang->line('application_matricule');?></span><?=$salarie->code;?></li>
					<li><span>Prénom & Nom</span><?=$salarie->prenom.' '.$salarie->nom;?></li>
					<li><span>Genre</span><?=GetType_txt($salarie->genre);?></li>
					<li><span>Situation familiale</span><?=GetType_txt($salarie->situationfamiliale);?></li>
					<li><span>Date de naissance</span><?=dateFR($salarie->datedenaissance);?></li>
					<li><span>Lieu de naissance</span><?=$salarie->lieudenaissance;?></li>
					<li><span>Numéro CIN</span><?=$salarie->numerocin;?></li>
					<li><span>Date de délivrance</span><?=dateFR($salarie->datedelivrance);?></li>
					<li><span>Numéro CNSS</span><?=$salarie->numerocnss;?></li>
					<li><span>Service d'affectation</span><?=$salarie->seraffectation;?></li>
			</ul>
			<br clear="both">
		</div>
	</div>
	
	<!-- détail 2 contact -->
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-3">
		<div class="table-head">Contact
			<span class=" pull-right option-icon"> 
				<a href="<?=base_url()?>gestionsalarie/updatecontact/<?=$salarie->id;?>" data-toggle="mainmodal" data-target="#mainModal">
					<i class="fa fa-edit" title="Modifier"></i>
				</a>
			</span>
		</div>
		<div class="subcont">
			<ul class="details col-xs-12 col-sm-12">
					<li><span>Adresse</span><?=$salarie->adresse1;?></li>
					<li><span>Adresse 2</span><?=$salarie->adresse2;?></li>
					<li><span>Code Postal</span><?=$salarie->codepostal;?></li>
					<li><span>Ville</span><?=$salarie->ville;?></li>
					<li><span>Pays</span><?=$salarie->pays;?></li>
					<li><span>Téléphone 1</span><?=$salarie->tel1;?></li>
					<li><span>Téléphone 2</span><?=$salarie->tel2;?></li>
					<li><span>Skype</span><?=$salarie->skype;?></li>
					<li><span>Email</span><?=$salarie->mail;?></li>
			</ul>
			<br clear="both">
		</div>
	</div>
	
	<!-- détail 3 paie-->
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-3">
		<div class="table-head">Paie
			<span class=" pull-right option-icon"> 
				<a href="<?=base_url()?>gestionsalarie/updatepaie/<?=$salarie->id;?>" data-toggle="mainmodal" data-target="#mainModal">
					<i class="fa fa-edit" title="Modifier"></i>
				</a>
			</span>
		</div>
		<div class="subcont">
			<ul class="details col-xs-12 col-sm-12">
					<li><span>Chef de famille</span><?=$salarie->chef_famille;?></li>
					<li><span>Salaire brut</span><?=$salarie->salaire_brut;?></li>
					<li><span>Nb. d'enfants</span><?=$salarie->nb_enfants;?></li>
					<li><span>Nb. d'enfants boursiers</span><?=$salarie->nb_enfants_boursiers;?></li>
					<li><span>Nb. d'enfants handicapé(e)s</span><?=$salarie->nb_enfants_handicape;?></li>
					<li><span>Parent à charges</span><?=$salarie->parents_charges;?></li>
					<li><span>Droit congés</span><?=$salarie->droit_conge;?></li>
					<li><span>Solde de congés initial</span><?=$salarie->solde_conge_initiale;?></li>
					<li><span>Mode de paiement</span><?=GetType_txt($salarie->mode_paiement);?></li>
					<li><span>Date embauche</span><?=dateFR($salarie->date_debut_embauche);?></li>
					<li><span>Catégorie</span><?=$salarie->categorie;?></li>
					<li><span>Echelon</span><?=$salarie->echelon;?></li>
					<li><span>Taux horaire</span><?=$salarie->tauxhoraire;?></li>
					<li><span>Type de paiement</span><?=GetType_txt($salarie->type_paiement);?></li>
					<li><span>Type de contrat</span><?=GetType_txt($salarie->type_contrat);?></li>
			</ul>
			<br clear="both">
		</div>
	</div>

	<!-- détail 4 règlement-->
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-3">
		<div class="table-head">Règlement
			<span class=" pull-right option-icon"> 
				<a href="<?=base_url()?>gestionsalarie/updatereglement/<?=$salarie->id;?>" data-toggle="mainmodal" data-target="#mainModal">
					<i class="fa fa-edit" title="Modifier"></i>
				</a>
			</span>
		</div>
		<div class="subcont">
			<ul class="details col-xs-12 col-sm-12">
					<li><span>Nom de la banque</span><?=$salarie->nombanque;?></li>
					<li><span>Rib</span><?=$salarie->rib;?></li>
					<li><span>Iban</span><?=$salarie->iban;?></li>
					<li><span>Bic</span><?=$salarie->bic;?></li>
			</ul>
			<br clear="both">
		</div>
	</div>
	
</div>