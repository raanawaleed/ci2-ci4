<?php
	function afficher($data, $level = 0) {
		$levelContent = "";
		for ($i = 0; $i < $level; $i++) { $levelContent .= "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp;"; }
		
		foreach ($data as $item) {
			$output .= "<tr>";
			$output .= "<td>$levelContent" . $item['libelle'] . "</td>";
			$output .='<td class=option action">
				        <a href="'.base_url().'items/update_family_items/'.$item['id'].'" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
						</td>';		
			$output .= "</tr>";
			$output .= afficher($item['children'], $level + 1);
		}
		
		return $output;
	}
?>

<div class="col-sm-12  col-md-12 main"> 
	<div class="row">
		<a href="<?=base_url()?>items/create_family_items" class="btn btn-success" data-toggle="mainmodal"><?=$this->lang->line('application_create_family_item');?></a>
		<a href="<?=base_url()?>items/article" class="btn btn-primary"><?=$this->lang->line('application_items_gestion');?></a>
	</div>
	<div class="row">
		<div class="table-head"> <?=$this->lang->line('application_family_items');?></div>
		<div class="table-div">
			<table class="table" id="items" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
				<thead>
					<th><?=$this->lang->line('application_name');?></th>
					<th><?=$this->lang->line('application_action');?></th>
				</thead>
				<tbody>
					<?php echo afficher($items); ?>
				</tbody>
			</table>
		</div>
	</div>
	<br clear="all">
</div>
<style>
	ul{
		list-style:none;
		margin-left:5px;
	}
</style>
