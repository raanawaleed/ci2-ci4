<div id="row"> 
	
		<div class="col-md-3">
			<div class="list-group">
				<?php foreach ($submenu as $name=>$value):
				$badge = "";
				$active = "";
				if($value == "settings/updates"){ $active = 'active';}?>
	               <a class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
	            <?php endforeach;?>
			</div>
		</div>


<div class="col-md-9">
		<div class='alert alert-warning'><?=$this->lang->line('application_always_make_backup');?></div>
		<div class='alert alert-info'>Vous utilisez la version <?=$core_settings->version;?></div>
		<?php if($writable == "FALSE"){ ?>
		<div class='alert alert-danger'>Aucune autorisation d'écriture sur les dossiers suivants <b>/application/</b> et <b>/assets/</b> <br> Modifiez les autorisations de ces dossiers temporairement à 777 afin d'installer les mises à jour. Modifiez les autorisations à 755 après avoir installé toutes les mises à jour.</div>
		<?php } ?>
       <?php if($version_mismatch != "FALSE"){ ?>
		<div class='alert alert-danger'>La version de votre base de données ne correspond pas à la version du fichier!</div>
		<?php } ?>


		<?php if($curl_error){ ?>
		<div class='alert alert-danger'>Could not connect to update server. Please check if php_curl extension is enabled!</div>
		<?php } ?>

		<div class="table-head"><?=$this->lang->line('application_system_updates');?> <span class="pull-right"> <a href="<?=base_url()?>settings/updates" class="btn btn-primary"><i class="fa fa-refresh"></i> <?=$this->lang->line('application_check_for_updates');?></a></span></div>
		<div class="table-div"><table id="updates" class="table" cellspacing="0" cellpadding="0">
		<thead>
			<th><?=$this->lang->line('application_update');?></th>
			<th><?=$this->lang->line('application_release_date');?></th>
			<th><?=$this->lang->line('application_info');?></th>
			<th><?=$this->lang->line('application_action');?></th>
		</thead>
		<?php  $first = FALSE; $supported = TRUE;
		foreach ($lists as $key => $file):
		 if($file->version > $core_settings->version){
		 	$updatenews = ""; 
		 	if(isset($file->updatenews)){
		 		$updatenews = $file->updatenews; 
		 	}
		 	if(isset($file->supported)){
		 		$supported = $file->supported; 
		 	}
		?>

		<tr>
			<td><?php echo "Core ".$file->version;?></td>
			<td><?=$file->date;?></td>
			<td><a href="#" class="po" rel="popover" data-placement="top" data-content="<?=$file->changelog;?>" data-original-title="Update <?=$file->version;?>"><?=$this->lang->line('application_view_changelog');?></a></td>
			
			<td class="option">
				<?php if($first){echo $this->lang->line('application_previous_update_required');}else{ ?>
		    <a <?php if(in_array($file->file, $downloaded_updates)){echo 'class="btn btn-xs disabled" disabled="disabled"';}else{ echo 'href="update_download/'.str_replace(".zip", "", $file->file).'" class="btn btn-xs btn-success button-loader"';} ?>><?=$this->lang->line('application_download');?>
				<a <?php if(in_array($file->file, $downloaded_updates) && $writable == "TRUE"){echo 'href="update_install/'.str_replace(".zip", "", $file->file).'/'.$file->version.'/'.$updatenews.'" class="btn btn-xs btn-success button-loader"';}else{ echo 'class="btn btn-xs btn-option disabled" disabled="disabled"';} ?>><?=$this->lang->line('application_install');?></a>
				<?php } ?>
			</td>
		</tr>

		<?php $first = TRUE; } endforeach; 
		if(!$first){ ?>
		<tr>
			<td colspan="4"><?=$this->lang->line('application_system_up_to_date');?></td>
		</tr> 
		<?php } ?>
	 	</table>
	 	
	 	<?php if($supported == NULL){ ?>
		<div class='alert alert-warning'>Your support has been expired. <a href="http://codecanyon.net/item/freelance-cockpit-2-project-management/4203727" target="_blank">Please renew your support.</a></div>
		<?php } ?>

	 	</div>
	</div>	</div>