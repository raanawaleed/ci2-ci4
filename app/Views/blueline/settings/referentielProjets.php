<div class="row">
	<div class="col-md-3">
		<div class="list-group">
			<?php foreach ($submenu as $name=>$value): 
				$badge = "";
				$active = "";
				
				if($value == $breadcrumb){ $active = 'active';}?>
				   <a class="list-group-item <?=$active;?>"
				   id="<?php $val_id = explode("/", $value);
					    if(!is_numeric(end($val_id))):
						   echo end($val_id);
						else: 
							$num = count($val_id)-2; echo $val_id[$num];
						endif ?>"
					 href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
			<?php endforeach;?>

		</div>
	</div>
	<div class="col-md-9">
		
		<?php $this->load->view('blueline/settings/referentielTemps' , $refTab['uniteTemps'] )?>
	
	</div>
</div>
