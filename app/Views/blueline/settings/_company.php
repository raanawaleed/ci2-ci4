<div id="row">
	<div class="col-md-3">
		<div class="list-group">
			<?php foreach ($submenu as $name=>$value):
			$badge = "";
			$active = "";
			if($value == "settings/achat"){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
			if($name == $breadcrumb){ $active = 'active';}?>
			   <a class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
			<?php endforeach;?>
		</div>
	</div>

<div class="col-md-9">
<div class="row">
		<div class="span12 marginbottom20">
		<div class="table-head"><?=$this->lang->line('application_edit_company')?></div>
		<div class="subcont">
           <?php   
			$attributes = array('class' => '', 'id' => 'user_form');
			echo form_open_multipart($form_action, $attributes); 
			?>

<div class="form-group">
        <label for="name"><?=$this->lang->line('application_company_name');?> *</label>
        <input id="name" type="text" name="name" class="required form-control"  value="<?php if(isset($company)){echo $company->name;} ?>"  required/>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="cnss">CNSS</label>
            <input id="cnss" type="text" name="cnss" class="form-control"  value="<?php if(isset($company)){echo $company->cnss;} ?>" />
        </div>
    </div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
				<label for="phone"><?=$this->lang->line('application_phone');?></label>
				<input id="phone" type="tel" name="phone" class="form-control"  value="<?php if(isset($company)){echo $company->phone;} ?>" />
		</div>
	</div>
    <div class="col-md-6">	
		<div class="form-group">
				<label for="mobile"><?=$this->lang->line('application_mobile');?></label>
				<input id="mobile" type="tel" name="mobile" class="form-control"  value="<?php if(isset($company)){echo $company->mobile;} ?>" />
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
				<label for="address"><?=$this->lang->line('application_address');?></label>
				<input id="address" type="text" name="address" class="form-control"  value="<?php if(isset($company)){echo $company->address;} ?>" />
		</div>
	</div>
	<div class="col-md-6">	
		<div class="form-group">
				<label for="zipcode"><?=$this->lang->line('application_zip_code');?></label>
				<input id="zipcode" type="text" name="zipcode" class="form-control"  value="<?php if(isset($company)){echo $company->zipcode;} ?>" />
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
        <div class="form-group">
			<label for="city"><?=$this->lang->line('application_city');?></label>
			<input id="city" type="text" name="city" class="form-control"  value="<?php if(isset($company)){echo $company->city;} ?>" />
	    </div>
	</div>
	<div class="col-md-6">
        <div class="form-group">
			<label for="website"><?=$this->lang->line('application_website');?></label>
			<input id="website" type="text" name="website" class="form-control"  value="<?php if(isset($company)){echo $company->website;} ?>" />
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
        <div class="form-group">
			<label for="country"><?=$this->lang->line('application_country');?></label>
			<input id="country" type="text" name="country" class="form-control"  value="<?php if(isset($company)){echo $company->country;} ?>" />
	    </div>
	</div>
	<div class="col-md-6">
        <div class="form-group">
			<label for="vat"><?=$this->lang->line('application_vat');?></label>
			<input id="vat" type="text" name="vat" class="form-control"  value="<?php if(isset($company)){echo $company->vat;} ?>" />
		</div>
	</div>
</div>
<div class="form-groupe">
<img src="<?=base_url().'/files/media/'.$company->picture?>" width="100"  class="picture" alt="">
</div>
<div class="form-group">
	<label for="userfile"><?=$this->lang->line('application_company_picture');?></label>
	<div>
		<div id="image"></div>
		<input id="uploadFile" type="text" name="dummy" class="form-control uploadFile" placeholder="Choose File" disabled="disabled"/>
		<div class="fileUpload btn btn-primary">
			<span><i class="fa fa-upload"></i><span class="hidden-xs"> <?=$this->lang->line('application_select');?></span></span>
			<input id="uploadBtn" type="file" name="userfile" class="upload" />
		</div>
	</div>
</div> 
	
<div class="modal-footer">
        <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
        <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
        </div>
<div class="alert alert-danger notification" hidden></div>
<?php echo form_close(); ?>
		<br clear="all">
		</div>
		</div>
		</div>
</div>
<!--<script>

$('#uploadBtn').on('change', function(){
         
         var _URL = window.URL || window.webkitURL;
         img = new Image();
        file=$('#uploadBtn')[0].files[0];
        img.src = _URL.createObjectURL(file);
        img.onload = function() {
       if(img.height>200 && img.width>200){
         $(".notification").fadeIn(3000).html("La taille de votre image est trop élevée");
       
       $( ":input[type=submit]" ).prop( "disabled", true );
 }else{
	    $(".notification").fadeOut(3000);
		//document.getElementById("image").innerHTML="<img src='images/be-drapeau.jpg'>";
         $( ":input[type=submit]" ).prop( "disabled", false );
 }
       }
 
 
 });
 </script>-->