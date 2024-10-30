<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="<?php echo base_url()."assets/3cs/faq-support/css/reset.css"?>"> <!-- CSS reset -->
	<link rel="stylesheet" href="<?php echo base_url()."assets/3cs/faq-support/css/style.css"?>"> <!-- Resource style -->
	<script src="<?php echo base_url()."assets/3cs/faq-support/js/modernizr.js"?>"></script> <!-- Modernizr -->
	
</head>
<body>
<header>
	<h1>Questions fréquemment posées (FAQs)</h1>
</header>
<section class="cd-faq">
	<!-- les catégories -->
	<ul class="cd-faq-categories">
		<li><a class="selected" href="#Line_1">Généralités</a></li>
		<li><a href="#Line_2">Tableau de bord</a></li>
		<li><a href="#Line_3">Messages</a></li>
		<li><a href="#Line_4">Projets</a></li>
		<li><a href="#Line_5">Clients</a></li>
	</ul>

	<div class="cd-faq-items">
		<ul id="Line_1" class="cd-faq-group">
			<li class="cd-faq-title"><h2>Généralités</h2></li>
			<li>
				<a class="cd-faq-trigger" href="#0">Mes données sont-ils en sécurité ?</a>
				<div class="cd-faq-content">
					<p>Oui, dès votre connexion à votre espace personnalisé, votre connexion est sécurisée en SSL 256 bits. Pendant toute la navigation sur votre espace personnalisé, le certificat SSL 256 bits reste actif. Vous seul, ou vos collaborateurs à qui vous donnez accès peuvent voir vos données.</p>
				</div> <!-- cd-faq-content -->
			</li>
			
			<li>
				<a class="cd-faq-trigger" href="#0">Comment modifier mon mot de passe ?</a>
				<div class="cd-faq-content">
					<p>Allez dans votre profil en cliquant à droite sur votre nom d’utilisateur et sur “Profil”. Dans la fenêtre qui s’ouvre mettez votre ancien mot de passe et le mot de passe qui vous souhaitez avoir.</p>
				</div>
			</li>

			<li>
				<a class="cd-faq-trigger" href="#0">Comment ajouter un nouvel utilisateur ?</a>
				<div class="cd-faq-content">
					<p>Si vous êtes administrateur et que votre licence vous permette d'ajouter d'autres utilisateurs à VISION, il suffit d'aller dans "PARAMÈTRES" >> "GESTION DES UTILISATEURS" et vous allez pouvoir ajouter un nouvel utiliseur en cliquant sur le bouton adéquat.</p>
				</div> <!-- cd-faq-content -->
			</li>	
		</ul> <!-- cd-faq-group -->

		<ul id="Line_2" class="cd-faq-group">
			<li class="cd-faq-title"><h2>Tableau de bord</h2></li>
			 <li>
				<a class="cd-faq-trigger" href="#0">La date d'expiration de ma licence arrive. Comment renouveller ?</a>
				<div class="cd-faq-content">
					<p>Si votre licence est expirée ou si la date d'échéance d'expiration arrive bientôt, vous pouvez contacter le support (Menu "SUPPORT" sous menu "CONTACT") et demander la procédure de renouvellement de votre licence.</p>
				</div> 
			</li> 				
		</ul> <!-- cd-faq-group -->
		
				
		<ul id="Line_3" class="cd-faq-group">
			<li class="cd-faq-title"><h2>Messages</h2></li>
			
			<li>
				<a class="cd-faq-trigger" href="#0">Puis-je envoyer un message en utilisant une adresse email ?</a>
				<div class="cd-faq-content">
					<p>Non, le module message est destiné à communiquer entre les utilisateurs de l’ERP. Lors de l’envoi de votre message vous pouvez choisir un ou plusieurs utilisateurs parmi la liste des utilisateurs de l’ERP.</p>
				</div> 
			</li> 
			
		</ul> <!-- cd-faq-group -->

		<ul id="Line_4" class="cd-faq-group">
			<li class="cd-faq-title"><h2>Projets</h2></li>
			<li>
				<a class="cd-faq-trigger" href="#0">Comment créer un nouveau projet ?</a>
				<div class="cd-faq-content">
					<p>Vous pouvez ajouter un nouveau projet en cliquant sur le bouton "Nouveau Projet". Vous devez alors saisir les informations nécessaire à la création du projet. Une fois la création du projet est validée, votre nouveau projet apparait dans la liste des projets visible en arrivant sur le sous module "LISTE".</p>
				</div> <!-- cd-faq-content -->
			</li>

			<li>
				<a class="cd-faq-trigger" href="#0">Comment voir le détail d'un projet ?</a>
				<div class="cd-faq-content">
					<p>En arrivant sur le module "PROJETS", sous module "LISTE", vous allez trouver une liste de vos projets déjà crées. En cliquant sur la ligne du projet, vous pouvez accéder au détail du projet sélectionné. Vous allez donc voir un ensemble d'onglets qui vous donne toute les informations nécessaires au bon suivi de votre projet.</p>
				</div> <!-- cd-faq-content -->
			</li>

			
		</ul> <!-- cd-faq-group -->

		<ul id="Line_5" class="cd-faq-group">
			<li class="cd-faq-title"><h2>Clients</h2></li>
			<li>
				<a class="cd-faq-trigger" href="#0">Comment ajouter un nouveau client ?</a>
				<div class="cd-faq-content">
					<p>En arrivant sur le module "CLIENTS" vous avez la liste de vos clients. Pour créer un nouveau client, cliquer sur le bouton "Nouveau Client". Une fois les informations obligatoires saisis et validées, le nouveau client apparaît dans la liste des clients.</p>
				</div> <!-- cd-faq-content -->
			</li>

			<li>
				<a class="cd-faq-trigger" href="#0">Comment modifier un client ?</a>
				<div class="cd-faq-content">
					<p>Pour modifier un client, il suffit de cliquer sur l'icône stylet <i class="fa fa-edit"></i> pour éditer les informations de votre client.</p>
				</div> <!-- cd-faq-content -->
			</li>

			
		</ul> <!-- cd-faq-group -->

		
	</div> <!-- cd-faq-items -->
	<a href="#0" class="cd-close-panel">Close</a>
</section> <!-- cd-faq -->
<script src="<?php echo base_url()."assets/3cs/faq-support/js/jquery-2.1.1.js"?>"></script>
<script src="<?php echo base_url()."assets/3cs/faq-support/js/jquery.mobile.custom.min.js"?>"></script>
<script src="<?php echo base_url()."assets/3cs/faq-support/js/main.js"?>"></script> <!-- Resource jQuery -->
</body>
</html>