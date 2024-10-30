
<?php if(count($natures_projetcs) == 0) :?>
	<option value="#">Aucune nature pour cette catégorie</option>
<?php else : ?>
	<option value="#">Choisir la nature </option>
	<?php foreach($natures_projetcs as $key=>$nat) :  ?>

		<option value="<?=$nat->id; ?>"><?=$nat->name;?></option>
	<?php endforeach; ?>
<?php endif ; ?>