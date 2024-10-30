<div id="row">
	<div class="col-md-3">
		<div class="list-group">
			<?php foreach ($submenu as $name=>$value):
			$badge = "";
			$active = "";
			if($value == "settings/updates"){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
			if($name == $breadcrumb){ $active = 'active';}?>
			   <a class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
			<?php endforeach;?>
		</div>
	</div>

	<input id="usersCount" value="<?php echo count($users); ?>" hidden>
	<div class="col-md-9">
		<div class="table-head"> <?php echo ($statut == 1)? "Utilisateurs actifs": "Utilisateurs non actifs"; ?>
			<span class="pull-right">
				<?php if($statut == 1): ?>
					<a href="<?=site_url('settings/listUser/0'); ?>" class="btn btn-warning" >Utilisateurs non actifs</a>	
				<?php else :?>
					<a href="<?=site_url('settings/listUser'); ?>" class="btn btn-success" >Utilisateurs actifs</a>
				<?php endif;?>		

				<?php if((count($users) == $_SESSION['users']) && ($this->user->admin == 1)) { ?>
					<a href="#" id="UserAdd" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_create_user');?></a>	
				<?php }else{ ?>
					<a href="<?=base_url()?>settings/user_create" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_create_user');?></a>
				<?php } ?>			
			   </span>
		</div>

		<div class="table-div table-responsive">
							<div class="table-head">Utilisateurs</div>

		<table id="users" class="dataSorting table" cellspacing="0" cellpadding="0">
		<thead>
			<th style="width:10px"></th>
			<th class="hidden-xs"><?=$this->lang->line('application_username');?></th>
			<th class="hidden-sm hidden-xs hidden-md"><?=$this->lang->line('application_email');?></th>
			<th class="hidden-xs"><?=$this->lang->line('application_status');?></th>
			<th class="hidden-xs"><?=$this->lang->line('application_admin_s');?></th>
			<th class="hidden-sm hidden-xs hidden-md"><?=$this->lang->line('application_last_login');?></th>
			<th><?=$this->lang->line('application_action');?></th>
		</thead>
		<?php foreach ($users as $user):?>

		<tr id="<?=$user->id;?>">
			<td  style="width:10px">
			<img class="minipic" src="
               <?php 
                if($user->userpic != 'no-pic.png'){
                  echo base_url()."files/media/".$user->userpic;
                }else{
                  echo get_gravatar($user->email, '20');
                }
                 ?>
                "/>
            </td>
			<td class="hidden-xs"><?=ucwords(strtolower($user->firstname .' ' .$user->lastname));?></td>
			<td class="hidden-sm hidden-xs hidden-md"><p class="truncate"><?=$user->email;?></p></td>
			<td class="hidden-xs"><span class="label label-<?php if($user->status == "active"){ echo "success"; }else{echo "important";} ?>"><?=$this->lang->line('application_'.$user->status);?></span></td>
			<td class="hidden-xs"><span class="label label-<?php if($user->admin == "1"){ echo "success"; }else{echo "";} ?>"><?php if($user->admin){echo $this->lang->line('application_yes');}else{echo $this->lang->line('application_no');}?></span></td>
			<td class="hidden-xs hidden-md hidden-sm"><span><?php if(!empty($user->last_login)){ echo date($core_settings->date_format.' '.$core_settings->date_time_format, $user->last_login); } else{echo "-";}?></span></td>
			<td class="option" width="5%">
			    <a href="<?=base_url()?>settings/user_access_update/<?=$user->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
			
			</td>
		</tr>

		<?php endforeach;?>
	 	</table>
	 	</div>
		<div class="alert alert-danger notification" hidden> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
</div>
</div>

<script>
$( document ).ready(function() {
var users = document.getElementById('usersCount').value;
});
$('#UserAdd').on('click', function(){
         $(".notification").fadeIn(3000).html("<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Votre licence actuelle ne vous permet pas d'ajouter un autre utilisateur. Contacter le support pour vos autoriser à le faire");
 });
 </script>
 
