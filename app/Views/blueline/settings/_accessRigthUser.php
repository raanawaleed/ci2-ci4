<?php   
$attributes = array('class' => '', 'id' => 'user_form');
echo form_open_multipart($form_action, $attributes); 
?>

<div class="form-group ">
	<label for="username"><?=$this->lang->line('application_username');?> *</label>
	<input id="username" type="text" name="username" class="required form-control"  value="<?php if(isset($user)){echo $user->username;} ?>"  onchange= "checkUsername()" 
	required/>
</div>

<div class="usernameExistant" style="display: none">
	<label for="usernameExistant" style="color:red;"><?=$this->lang->line('application_username_existent');?> </label>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="firstname"><?=$this->lang->line('application_firstname');?> *</label>
			<input id="firstname" type="text" name="firstname" class="required form-control"  value="<?php if(isset($user)){echo $user->firstname;} ?>"  required/>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="lastname"><?=$this->lang->line('application_lastname');?> *</label>
			<input id="lastname" type="text" name="lastname" class="required form-control"  value="<?php if(isset($user)){echo $user->lastname;} ?>"  required/>
		</div>
	</div>
</div>

<div class="form-group">
	<label for="email"><?=$this->lang->line('application_email');?> *</label>
	<input id="email" type="email" name="email" class="required email form-control" value="<?php if(isset($user)){echo $user->email;} ?>"  required/>
</div>

<div class="form-group">
	<label for="password"><?=$this->lang->line('application_password');?><?php if(!isset($user)){echo '*';} echo " (8 caractères minimun)"; ?></label>
	<input id="password" type="password" name="password" class="form-control "  minlength="8" <?php if(!isset($user)){echo 'required';} ?>/>
</div>

<div class="form-group">
	<label for="password"><?=$this->lang->line('application_confirm_password');?> <?php if(!isset($user)){echo '*';} ?></label>
	<input id="confirm_password" type="password" name="confirm_password" class="form-control" data-match="#password" />
</div>

<div class="form-group">
	<label for="userfile"><?=$this->lang->line('application_profile_picture');?></label>
	<div>
		<input id="uploadFile" type="text" name="dummy" class="form-control uploadFile" placeholder="Choose File" disabled="disabled" />
	    <div class="fileUpload btn btn-primary">
		    <span><i class="fa fa-upload"></i><span class="hidden-xs"> <?=$this->lang->line('application_select');?></span></span>
		    <input id="uploadBtn" type="file" name="userfile" class="upload" />
	    </div>
	</div>
</div> 

<div class="form-group">
	<label for="status"><?=$this->lang->line('application_status');?></label>   
	<?php $options = array(
							'active'  => $this->lang->line('application_active'),
							'inactive'    => $this->lang->line('application_inactive')
						   ); ?>

	<?php 
	if(isset($user)){$status = $user->status;}else{$status = 'active';}
	echo form_dropdown('status', $options, $status, 'style="width:100%" class="chosen-select"');?>
</div>
<div class="form-group">
	<label for="admin"><?=$this->lang->line('application_super_admin');?></label>
	<?php $options = array(
							'1'  => $this->lang->line('application_yes'),
							'0'    => $this->lang->line('application_no')
						   ); ?>

	<?php 
	if(isset($user)){$admin = $user->admin;}else{$admin = '0';}
	echo form_dropdown('admin', $options, $admin, 'style="width:100%" class="chosen-select"');?>
</div> 

<?php if(!isset($agent) && $this->user->admin == "1"){ 
	$access = array();
	if(isset($user)){ $access = explode(",", $user->access); }
?>
<?php } ?> 

<div class="form-group">
	<!-- Module par défaut -->
	<ul>
		<label><?=$this->lang->line('application_module_access_default');?></label>
	</ul>
	<ul class="accesslist">
			<?php foreach ($modules as $key => $value) { 
				if($value->default_module == 1){
					//change the name of modules 
					if($value->link == "v_companies/revoke"){
						$Module = $value->name;
					}else{
						$Module = $value->link;
					} ?>
				<li> <input type="checkbox" class="checkbox" id="<?=$value->id;?>" name="access[]" data-labelauty="<?=$this->lang->line('application_'.$Module);?>"value="<?=$value->id;?>" checked  > </li>
			<?php }} ?>
	</ul>
	<!-- Module à ajouter  -->
	<label><?=$this->lang->line('application_module_access');?></label>
	<ul class="accesslist">
	<?php foreach ($modules as $key => $value) { 
			if($value->default_module == 0){
				//change the name of modules 
				if($value->link == "v_companies/revoke"){
					$Module = $value->name;}
				else{
					$Module = $value->link;
				} ?>
				<li> <input type="checkbox"  class="checkbox test" id="<?=$value->id;?>" name="access[]" data-labelauty="<?=$this->lang->line('application_'.$Module);?>"value="<?=$value->id;?>"
				onclick="ckeckedSubmodules(<?=$value->id;?>);"> </li>
				
				<?php //Sous menus 
				foreach ($submenus as $key => $submenu) { 
					if($submenu->id_modules == $value->id ){ ?>
					<li style="padding-left:20px"> <input type="checkbox" class="checkbox" id="<?=$submenu->link;?>" name="acces_submenu[]" data-labelauty="<?php echo $submenu->name; ?>"value="<?=$submenu->id;?>"  onclick="ckeckedModule(<?=$submenu->link;?>);"> </li>
				<?php	}					
				}?>
			<?php }} ?>
	</ul>
</div>

<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>

<div class="alert alert-danger notification" hidden></div>
<?php echo form_close(); ?>
<script>
$('#uploadBtn').on('change', function(){     
	var _URL = window.URL || window.webkitURL;
	img = new Image();
	file=$('#uploadBtn')[0].files[0];
	img.src = _URL.createObjectURL(file);
	img.onload = function() {
		if(img.height>180 && img.width>180){
			$(".notification").fadeIn(3000).html("The Size Of Your Image is too High");
			$(".notification").fadeOut(3000);
			$( ":input[type=submit]" ).prop( "disabled", true );
		}else{
			$( ":input[type=submit]" ).prop( "disabled", false );
		}
	}
 });
 </script>
<script>
function checkUsername() {
	var name = username.value; 
	$.ajax({
		type: 'POST',
		dataType: "text",
		url: decodeURIComponent('../settings/checkUsername/' + name),
		success: function (response) { 
			if (response == "true") {
				$( "div.usernameExistant" ).show();
				$('input[type=submit]').prop('disabled',true);
			} else {
				$( "div.usernameExistant" ).hide();
				$('input[type=submit]').prop('disabled',false);
			} 
		}
	});	
 }
</script>



<script>
function ckeckedModule(a)
{
	var id = a.value;
	//var url = decodeURIComponent('../renderSubmenu/' + id); 
	var url = decodeURIComponent('rendermodule/' + id); 
	$.ajax({
		type: 'POST',
		dataType: "text",
		url: url,
		success: function (response) { 
			response = response.substring(1);
			response = response.substring(0,response.length -1);
			document.getElementById(response).checked= true ;						
			document.getElementById(response).style.display = "block";	
		}
	}); 
}


function ckeckedSubmodules(id)
{

	var url = decodeURIComponent('renderSubmenu/' + id); 
	$.ajax({
		type: 'POST',
		dataType: "text",
		url: url,
		success: function (response) { 
			//the first and last characater
			response = response.substring(1);
			response = response.substring(0,response.length -1);
			response = response.split(",");
			//parcourinr la table 
			for(var i=0 in response) {
				response[i] = response[i].substring(1);
				response[i] = response[i].substring(0,response[i].length -1);
				if (document.getElementById(id).checked == true ){
					document.getElementById(response[i]).checked= true ;
				}else{
					document.getElementById(response[i]).checked= false ;
				}
			}
		}
	}); 

}

</script>