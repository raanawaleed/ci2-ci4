<style>
    .primary-menu{
        padding: 7px 10px 0px;
        margin-bottom: 0px;
        color: #4A4A4A;
        font-weight: 700;
    }

    .sub-menu{
        text-indent: 10px;
    }
</style>
<?php
$attributes = array('class' => '', 'id' => 'user_form');
echo form_open_multipart($form_action, $attributes); 
?>

<div class="form-group ">
	<label for="email"><?=$this->lang->line('application_email');?> *</label>
	<input id="email" type="text" name="email" class="required form-control"  value="<?php if(isset($user)){echo $user->email;} ?>"  onchange= "checkEmail()" 
	readonly >
</div>

<div class="id" style="display: none">
	<input id="id" type="text" name="id" class="required form-control"  value="<?php if(isset($user)){echo $user->id;} ?>" readonly>
</div>


<div class="row">
    <div class="col-md-6">
		<div class="form-group">
			<label for="firstname"><?=$this->lang->line('application_firstname');?> *</label>
			<input id="firstname" type="text" name="firstname" class="required form-control"  value="<?php if(isset($user)){echo $user->firstname;} ?>"  readonly />
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="lastname"><?=$this->lang->line('application_lastname');?> *</label>
			<input id="lastname" type="text" name="lastname" class="required form-control"  value="<?php if(isset($user)){echo $user->lastname;} ?>"  readonly >
		</div>
	</div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="password"><?=$this->lang->line('application_password');?><?php if(!isset($user)){echo '*';} echo " (8 caractères minimun)"; ?></label>
            <input id="password" type="password" name="password" class="form-control " <?php if(!isset($user)){echo 'required';} ?>/>
            <div class="help-block"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="confirm_password"><?=$this->lang->line('application_confirm_password');?> <?php if(!isset($user)){echo '*';} ?></label>
            <input id="confirm_password" type="password" name="confirm_password" class="form-control" data-match="#password" />
            <div class="help-block"></div>
        </div>
    </div>
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
<!---
<div class="form-group" id="<?php echo $value->name;?>" >
	<label><?=$this->lang->line('application_ecran_par_defaut');?></label>
	<select class="chosen-select inbox-folder" name="default_screen" title="<?=$user->default_screen; ?>">
        <?php foreach ($modules as $key => $menu) :
            if($menu->name === 'dashboard') : ?>
                <option value="<?php echo $menu->id ; ?>" class="primary-menu" <?php echo (isset($user->default_screen)? (($user->default_screen == $key)? "selected":"" ):"selected"); ?>><?php echo $this->lang->line('application_dashboard'); ?></option>
            <?php break; endif;
        endforeach; ?>

        <?php foreach ($modules as $key => $value) : ?>
			<?php  if($value->default_module == 0) :  ?>
				<?php //change the name of modules
                    if($value->link == "v_companies/revoke") $Module = $value->name; else $Module = $value->link;
                ?>
                <?php  if (in_array($value->id, $tabaccess)) :?>
                        <option value="<?=$value->id;?>" class="primary-menu"  <?php echo (isset($user->default_screen)? (($user->default_screen === $value->id)? "selected":"" ):""); ?>><?php echo $this->lang->line('application_'.$value->name); ?></option>
                        <?php //Sous menus
                        foreach ($submenus as $key => $submenu) :
                                if($submenu->id_modules == $value->id ):
                                    if (in_array($submenu->id, $tabsubaccess)) : ?>
                                        <option value="<?=$submenu->id;?>" class="sub-menu" <?php echo (isset($user->default_screen)? (($user->default_screen === $submenu->id)? "selected":"" ):""); ?>>
                                            <?php echo $this->lang->line('application_'.$submenu->name); ?>
                                        </option>
                                    <?php  else : ?>
                                        <option value="<?=$submenu->id;?>" class="sub-menu" <?php echo (isset($user->default_screen)? (($user->default_screen == $submenu->id)? "selected":"" ):""); ?>>
                                            <?php echo  $this->lang->line('application_'.$submenu->name); ?>
                                        </option>
                                    <?php endif;
                                endif;
                        endforeach;?>
				<?php  else : ?>
                    <option label="<?=$value->id;?>" class="primary-menu"><?php echo $this->lang->line('application_'.$value->name); ?></option>
                         <?php //Sous menus
                            foreach ($submenus as $key => $submenu) :
                                if($submenu->id_modules == $value->id ):
                                    if (in_array($submenu->id, $tabsubaccess)): ?>
                                        <option value="<?=$submenu->id;?>" class="sub-menu" <?php echo (isset($user->default_screen)? (($user->default_screen == $submenu->id)? "selected":"" ):""); ?>>
                                            <?php echo $this->lang->line('application_'.$submenu->name); ?>
                                        </option>
                                    <?php else : ?>
                                        <option value="<?=$submenu->id;?>" class="sub-menu" <?php echo (isset($user->default_screen)? (($user->default_screen == $submenu->id)? "selected":"" ):""); ?>>
                                            <?php echo $this->lang->line('application_'.$submenu->name); ?>
                                        </option>
                                    <?php endif;
                                endif;
                        endforeach; ?>
				<?php endif;?>
            <?php endif;?>
        <?php endforeach;?>
	</select>
</div>
<!---defenir comme admin 
<div class="row">
    <div class="col-md-6">
				<div class="form-group" style="padding: 20px 9px;">
					<span><?=$this->lang->line('change_to_admin');?></span><br><br>
					<label class="switch" style="text-align: center;" >
						<?php if($user->admin == 1){ ?>
						<input  type="checkbox" name="admin" id="admin" checked>
						<?php	}else{ ?>  
						<input type="checkbox" name="admin" id="admin">
						<?php  } ?>
					<div class="slider round"></div>
					</label>
				</div>
	</div>
    
    <div class="col-md-6">
				<div class="form-group" style="padding: 20px 9px;">
					<span>definir comme salarié</span><br><br>
					<label class="switch" style="text-align: center;" >
						<?php if(($user->salaries_id)=="true"){ ?>
						<input  type="checkbox" name="salarie" id="salarie" checked>
						<?php	}else{ ?>  
						<input type="checkbox" name="salarie" id="salarie">
						<?php  } ?>
					<div class="slider round"></div>
					</label>
				</div>
	</div>
</div>
	


<div class="form-group" id="<?php echo $value->name;?>" >
    <!-- Module par défaut 
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
                <li><input style="display:inline"type="checkbox" class="checkbox" id="<?=$value->id;?>" name="defaultAccess[]" data-labelauty="<?=$this->lang->line('application_'.$Module);?>"value="<?=$value->id;?>" checked  onclick="return false;">

                </li>

            <?php }} ?>
    </ul> -->
    <!-- Module à ajouter 
    <label><?=$this->lang->line('application_module_access');?></label>
    <ul class="accesslist">
        <?php foreach ($modules as $key => $value) {
            if($value->default_module == 0){
                //change the name of modules
                if($value->link == "v_companies/revoke"){
                    $Module = $value->name;}
                else{
                    $Module = (is_null($value->link))? $value->name: $value->link;
                } ?>
                <?php  if (in_array($value->id, $tabaccess)) { ?>
                    <li> <input  style="display:inline" type="checkbox"  class="checkbox test" id="<?=$value->id;?>" name="menu[]" data-labelauty="<?=$this->lang->line('application_'.$value->name);?>" value="<?=$value->id;?>"
                                 onclick="ckeckedSubmodules(<?=$value->id;?>);" checked>
                        <?php //Sous menus
                        foreach ($submenus as $key => $submenu) {
                            if($submenu->id_modules == $value->id ){
                                if (in_array($submenu->id, $tabsubaccess)){ ?>
                                    <ul>	<li style="padding-left:20px"> <input  style="display:inline" type="checkbox" class="checkbox child" id="<?=$submenu->link;?>" name="submenu[]" data-labelauty="<?php echo $this->lang->line('application_'.$submenu->name); ?>" value="<?=$submenu->id;?>"  onclick="ckeckedModule(<?=$submenu->link;?>);"
                                                                                   checked>

                                        </li>
                                    </ul>

                                <?php } else { ?>
                                    <ul>
                                        <li style="padding-left:20px"> <input  style="display:inline" type="checkbox" class="checkbox child" id="<?=$submenu->link;?>" name="submenu[]" data-labelauty="<?php echo $this->lang->line('application_'.$submenu->name); ?>" value="<?=$submenu->id;?>"  onclick="ckeckedModule(<?=$submenu->link;?>);">
                                        </li>
                                    </ul>
                                <?php }}} ?>



                    </li>
                <?php } else { ?>
                    <li> <input  style="display:inline" type="checkbox"  class="checkbox test" id="<?=$value->id;?>" name="menu[]" data-labelauty="<?=$this->lang->line('application_'.$Module);?>" value="<?=$value->id;?>"
                                 onclick="ckeckedSubmodules(<?=$value->id;?>);">
                        <?php //Sous menus
                        foreach ($submenus as $key => $submenu) {
                            if($submenu->id_modules == $value->id ){
                                if (in_array($submenu->id, $tabsubaccess)){ ?>
                                    <ul>	<li style="padding-left:20px"> <input  style="display:inline" type="checkbox" class="checkbox child" id="<?=$submenu->link;?>" name="submenu[]" data-labelauty="<?php echo $this->lang->line('application_'.$submenu->name); ?>" value="<?=$submenu->id;?>"  ">

                                        </li>
                                    </ul>

                                <?php } else { ?>
                                    <ul>
                                        <li style="padding-left:20px"> <input  style="display:inline" type="checkbox" class="checkbox child" id="<?=$submenu->link;?>" name="submenu[]" data-labelauty="<?php echo $this->lang->line('application_'.$submenu->name); ?>" value="<?=$submenu->id;?>"  ">
                                        </li>
                                    </ul>
                                <?php }}} ?>

                    </li>
                <?php } }}?>


    </ul>



</div>



---->

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
		if(img.height>1000 && img.width>1000){
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
function checkEmail() {
	var name = email.value; 
	$.ajax({
		type: 'POST',
		dataType: "text",
		url: decodeURIComponent('../settings/checkEmail/' + name),
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

<!--

<script>
function ckeckedModule(a)
{
	var id = a.value;
	var url = decodeURIComponent('rendermodule/' + id); 
	$.ajax({
		type: 'POST',
		dataType: "text",
		url: url,
		success: function (response) { 
			response= response.replace('"', "");
			response = response.substring(1,response.length -1);
			response = response.replace(/\n|\r/g,'');
			document.getElementById(response).checked= true ;


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
				response[i] = response[i].replace('["', "");
				response[i] = response[i].substring(1);
				response[i] = response[i].substring(0,response[i].length -1); 
				response[i] = response[i].replace(/\n|\r/g,'');
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
-->
<!--
<script>

$(function() {
		$(document).on('change','.child', function(){
			$('.test').prop('checked', $('.test .child:checked') );
		});
	})
</script>
-->

<style>
ul{
	list-style:none;
}
</style>

<script>
$(function() {
  $("li:has(li) > input[type='checkbox']").change(function() {
    $(this).siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
  });


  $("input[type='checkbox'] ~ ul input[type='checkbox']").on("click",function() {
    $(this).closest("li:has(li)").children("input[type='checkbox']").prop('checked', true);
  });
   
});

</script>