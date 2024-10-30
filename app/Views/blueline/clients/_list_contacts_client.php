
<?php if(count($contacts_client) == 0) :?>
	<option value="#">Aucun contact pour ce client</option>
<?php else : ?>
	<option value="#">Choisir un contact</option>
	<?php foreach($contacts_client as $key=>$contact) :  ?>

		<option value="<?=$contact->id; ?>"><?=$contact->firstname.' '.$contact->lastname;?></option>
	<?php endforeach; ?>
<?php endif ; ?>