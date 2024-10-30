<!doctype html>
<html>
<head>
	<style>
		
		@font-face {
  font-family: 'book-antiqua';
  src: url('book-antiqua.ttf');
}
				@font-face {
  font-family: 'Monotype-Corsiva';
  src: url('Monotype-Corsiva.ttf');
}	
		body{
  font-family: 'book-antiqua', Georgia, serif; 
			font-size: 18px;
      line-height: 1.8em;
			margin: 100px 25px 0 25px;
}
	
		
	.attestation-de-travail span { font-style: italic; }
		.attestation-de-travail h2 { text-align: center; font-family: 'Monotype-Corsiva', Georgia, serif;}
		.attestation-de-travail p { margin: 60px 0; text-align: justify;}
		.attestation-de-travail p>i { font-weight: bold; }
		.footer-doc{ font-weight: bold; line-height: 2.2em; }
	</style>
<meta charset="utf-8">
<title>Attestation de travail</title>
</head>
<?php
	$this->load->helper('mydbhelper_helper');
	$this->load->helper('my_functions_helper');
	//déterminer monsieur ou madame
	if($salarie->genre ==81){$titre='M.';} else {$titre='Mme.';}
	if(!is_null($salarie->datedelivrance)){$datecin='délivrée le '.dateFR($salarie->datedelivrance);} else {$datecin='';}
?>	
<body>
	 <div class="attestation-de-travail">
		<h2>Attestation de travail</h2>
		<span><b><u>Réf</u></b> : HAD 11 / 16</span>
		<p>
			Je soussigné <?=$setting->signataire; ?>, agissant en qualité de directeur de la société <?=$vcompanies->name; ?>, 
			certifie que <?=$titre.' '.$salarie->prenom.' '.$salarie->nom; ?> demeurant au <?=$salarie->adresse1.' '.$salarie->adresse2; ?> titulaire de la carte d’identité nationale n° <?=$salarie->numerocin.' '.$datecin; ?>  est salarié de notre société, en qualité de <?=GetType_txt($salarie->idfonction);?> depuis le <?=dateFR($salarie->date_debut_embauche);?>.
			<br /><br />
			Cette attestation est délivrée à l'intéressée pour servir et valoir ce que de droit.
		</p>
		<p class="footer-doc">
			Fait à Tunis, Le <?=date("d/m/Y") ?>.<br />
			<i><?=$vcompanies->name; ?></i><br />
			<?=$setting->signataire; ?>
		</p>
	 </div>
</body>
</html>
