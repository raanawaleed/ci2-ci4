<?php if(count($sub_projects) == 0) :?>
	<option value="#">Aucun sous projet n'est rattaché à ce projet</option>
<?php else : ?>
	<option value="#">Vous pouvez choisir un sous projet</option>
	<?php foreach($sub_projects as $key=>$proj) :?>
		<option value="<?=$proj->id; ?>"><?=$proj->name;?></option>
	<?php endforeach; ?>
<?php endif ; ?>
