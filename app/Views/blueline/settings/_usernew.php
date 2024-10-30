<style>
	.color-red{
		border: 1px solid red !important;
		color: red !important;
		padding-left: 10px !important;
	}
</style>
<?php
$attributes = array('class' => '', 'id' => 'user_form', 'name'=>'edit-user');
echo form_open_multipart($form_action, $attributes);
?>

<div class="form-group">
	<label for="email"><?=$this->lang->line('application_email');?> *</label>
	<input id="email" type="email" name="email" class="email form-control" value="<?php if(isset($user)){echo $user->email;} ?>"/>
	<div class="help-block"></div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="firstname"><?=$this->lang->line('application_firstname');?> *</label>
			<input id="firstname" type="text" name="firstname" class="form-control"  value="<?php if(isset($user)){echo $user->firstname;} ?>"/>
			<div class="help-block"></div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="lastname"><?=$this->lang->line('application_lastname');?> *</label>
			<input id="lastname" type="text" name="lastname" class="form-control"  value="<?php if(isset($user)){echo $user->lastname;} ?>"/>
			<div class="help-block"></div>
		</div>
	</div>
</div>

<?php if($agent == true ){ ?>
<div class="form-group">
	<label for="old_password">Ancien mot de passe</label>
	<input id="old_password" type="password" name="oldpassword" class="form-control "/>
	<div class="help-block"></div>
</div>
	<?php } ?>
<div class="form-group">
	<label for="password"><?=$this->lang->line('application_password');?><?php if(!isset($user)){echo '*';} echo " (8 caractères minimun)"; ?></label>
	<input id="password" type="password" name="password" class="form-control " <?php if(!isset($user)){echo 'required';} ?>/>
	<div class="help-block"></div>
</div>
<div class="form-group">
	<label for="confirm_password"><?=$this->lang->line('application_confirm_password');?> <?php if(!isset($user)){echo '*';} ?></label>
	<input id="confirm_password" type="password" name="confirm_password" class="form-control" data-match="#password" />
	<div class="help-block"></div>
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
<?php if($agent != true ){ ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12">
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
	</div>
</div>
<div class="form-group no-border">
	<?php $i = 1; 
		while ($i <= 14) {  ?>
		<span class="color-selector bgColor<?=$i?> <?php if($classname == "bgColor".$i){ echo "selected";}?>"><input type="radio" name="classname" value="bgColor<?=$i?>" <?php if($classname == "bgColor".$i){ echo "selected";}?>></span>
	<?php $i++; } ?> 
</div>

<?php } ?>

<!----butttonn check ---->
<div class="row">
	<div class="col-md-2">
	<input id="salaries" type="checkbox" name="salaries" class="form-control"/>
	</div>
	<div class="col-md-10">
	<span class="highlight-text">cet utilisateur est un salarié(création automatique d'un nouveau salarié)</span>
	</div>
</div>

				


	
	
	



<div class="modal-footer" style="clear:both">
	<input type="submit" id="submit-edit-user" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<div id="the-message-edit-user"></div>
<?php echo form_close(); ?>
<script type="text/javascript">
	$( document ).ready(function() {
		$(".accesslist .checkbox").prop('checked', true);
	});
</script>
<?php // if($agent == true ){ ?>
<script type="text/javascript">


	function validate(tag,type,message){
		if(type=='error'){
			tag.next().text(message).addClass('color-red');
			tag.next().addClass('color-red').removeClass('color-green');
		}else if(type=="success"){
			tag.next().text(message).removeClass('color-red');
			tag.next().addClass('color-green').removeClass('color-red');
		}
	}


	$("#firstname").on("blur", function(){
		var value = $(this).val().trim();
		if(value==''){
			validate($(this), "error", "Champ obligatoire");
		}else{
			validate($(this), "success", "");
		}
	})
	$("#lastname").on("blur", function(){
		var value = $(this).val().trim();
		if(value==''){
			validate($(this), "error", "Champ obligatoire");
		}else{
			validate($(this), "success", "");
		}
	})
	$("#email").on("blur", function(){
		var value = $(this).val().trim();
		if(value==''){
			validate($(this), "error", "Champ obligatoire");
		}else{
			var reg =  /^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]/i;
			if (!reg.test(value)){
				validate($(this), "error", "Champ invalide");
			}else{
				var formData = new FormData($('#user_form')[0]);
				$.ajax({
					type: 'POST',
					data: formData ,
					async: false,
					url: "<?php echo site_url("/agent/verifEmail") ?>" ,
					dataType: 'json',
					cache: false,
					contentType: false,
					processData: false,
					success: function (res) {
						if (res.result == 0) {
							validate($("#email"), "success", "");
						}else if(res.result == 1){
							validate($("#email"), "error", "Email déjà existant");
						}
					}
				});
			}
		}
	})
	$("#old_password").on("blur", function(){
		var value = $(this).val();
		if(value==''){
			validate($(this), "error", "Champ obligatoire");
		}else{
			var formData = new FormData($('#user_form')[0]);
			$.ajax({
				type: 'POST',
				data: formData ,
				async: false,
				url: "<?php echo site_url("/agent/verifPassword") ?>" ,
				dataType: 'json',
				cache: false,
				contentType: false,
				processData: false,
				success: function (res) {
					if (res.result == 0) {
						validate($("#old_password"), "error", "Ancien mot de passe incorrect");
					}else if(res.result == 1){
						validate($("#old_password"), "success", "");
					}
				}
			});
		}
	})
	$("#password").on("blur", function(){
		var value = $(this).val();
		if(value!=''){
			if (value.length<8){
				validate($(this), "error", "8 caractere au minimum");
			}else{
				validate($(this), "success", "");
			}
		}
	})
	$("#confirm_password").on("blur", function(){
		var value = $(this).val();
		if (value!=$("#password").val()){
			validate($(this), "error", "Confirmation mot de passe est incorrecte");
		}else{
			validate($(this), "success", "");
		}
	})
	$("#submit-edit-user").on("click", function(e){
		e.preventDefault();
		var errors = 0;
		if($("#email").val()==''){
			validate($("#email"), "error", "Champ obligatoire");
			errors++;
		}else{
			var reg =  /^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]/i;
			if (!reg.test($("#email").val())){
				validate($("#email"), "error", "Champ invalide");
			}else{
				var formData = new FormData($('#user_form')[0]);
				$.ajax({
					type: 'POST',
					data: formData ,
					async: false,
					url: "<?php echo site_url("/agent/verifEmail") ?>" ,
					dataType: 'json',
					cache: false,
					contentType: false,
					processData: false,
					success: function (res) {
						if (res.result == 0) {
							validate($("#email"), "success", "");
						}else if(res.result == 1){
							validate($("#email"), "error", "Email déjà existant");
							errors++;
						}
					}
				});
			}
		}

		if($("#firstname").val().trim()==''){
			validate($("#firstname"), "error", "Champ obligatoire");
			errors++;
		}else{
			validate($("#firstname"), "success", "");
		}
		if($("#lastname").val().trim()==''){
			validate($("#lastname"), "error", "Champ obligatoire");
			errors++;
		}else{
			validate($("#lastname"), "success", "");
		}
		
		if($("#password").val()!=''){
			if ($("#password").val().length<8){
				validate($("#password"), "error", "8 caractere au minimum");
				errors++;
			}else{
				validate($("#password"), "success", "");
			}
		}else if($("#password").val()==''){
			validate($("#password"), "success", "");
		}
		if ($("#confirm_password").val()!=$("#password").val()){
			validate( $("#confirm_password"), "error", "Confirmation mot de passe est incorrecte");
			errors++;
		}else{
			validate( $("#confirm_password"), "success", "");
		}
		if (errors!=0){
			return false;
		}else{
			$("#user_form").submit();
		}
	});
</script>
<?php //} ?>