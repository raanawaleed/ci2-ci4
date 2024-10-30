<?php   
$attributes = array('class' => '', 'id' => '_clients', 'autocomplete' => 'off');
echo form_open_multipart($form_action, $attributes); 
?>
<?php if(isset($client)){ ?>
<input id="id" type="hidden" name="id" value="<?=$client->id;?>" />
<?php } 
if(isset($view)){ ?>
<input id="view" type="hidden" name="view" value="true" />
<?php } ?>
<!-- Nom & Prénom -->
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
		        <label for="firstname"><?=$this->lang->line('application_firstname');?> *</label>
		        <input id="firstname" type="text" name="firstname" class=" form-control" value="<?php if(isset($client)){echo $client->firstname;} ?>" required/>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		        <label for="lastname"><?=$this->lang->line('application_lastname');?> *</label>
		        <input id="lastname" type="text" name="lastname" class="required form-control" value="<?php if(isset($client)){echo $client->lastname;} ?>" required/>
		</div>
	</div>
</div>
<!-- @email -->
<div class="form-group">
        <label for="email"><?=$this->lang->line('application_email');?> *</label>
        <input id="email" type="email" name="email" class="required email form-control" value="<?php if(isset($client)){echo $client->email;} ?>" required/>
</div>
<!-- téléphone + portable -->
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
		        <label for="phone"><?=$this->lang->line('application_phone');?></label>
		        <input id="phone" type="text" name="phone" class="form-control" value="<?php if(isset($client)){echo $client->phone;}?>" />
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
	        <label for="mobile"><?=$this->lang->line('application_mobile');?></label>
	        <input id="mobile" type="text" name="mobile" class="form-control" value="<?php if(isset($client)){echo $client->mobile;}?>" />
		</div>
	</div>
</div>
<!-- adresse -->
<div class="form-group">
        <label for="address"><?=$this->lang->line('application_address');?></label>
        <input id="address" type="text" name="address" class="form-control" value="<?php if(isset($client)){echo $client->address;}?>" />
</div>
<!-- code postale + ville -->
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
		        <label for="zipcode"><?=$this->lang->line('application_zip_code');?></label>
		        <input id="zipcode" type="text" name="zipcode" class="form-control" value="<?php if(isset($client)){echo $client->zipcode;}?>" />
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		        <label for="city"><?=$this->lang->line('application_city');?></label>
		        <input id="city" type="text" name="city" class="form-control" value="<?php if(isset($client)){echo $client->city;}?>" />
		</div>
	</div>
</div>
<!-- sauvegarder -->
<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>