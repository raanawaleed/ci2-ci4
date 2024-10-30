<?php   
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);
$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company'])); 
$core_settings->company_reference = $core_settings->company_reference +1; 
?>

<?php if(isset($company)){ ?>
<input id="id" type="hidden" name="id" value="<?=$company->id;?>" />
<?php } 
if(isset($view)){ ?>
<input id="view" type="hidden" name="view" value="true" />
<?php } ?>

<!-- id client + contact principale -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
			<label for="reference"><?=$this->lang->line('application_reference_id');?> *</label>
			<?php if(!empty($core_settings->company_prefix)){  ?>
		   <div class="input-group"> <div class="input-group-addon"><?=$core_settings->company_prefix;?></div> <?php } ?>
			<input id="reference" type="text" name="reference" class="required form-control" 
			 onblur="myFunction(this.value)"
			value="<?php if(isset($company)){echo sprintf("%04d",$company->reference);} else{ echo sprintf("%04d",$core_settings->company_reference); } ?>" />
			<?php if(!empty($core_settings->company_prefix)){ ?></div> <?php } ?>
        </div>
    </div>


    <div class="col-md-6">
        <div class="form-group">
                <label for="contact"><?=$this->lang->line('application_primary_contact');?></label>
                <?php $options = array();
                        $options['0'] = '-';
                        foreach ($company->clients as $value):  
                        $options[$value->id] = $value->firstname.' '.$value->lastname;
                        endforeach;
                if(isset($company->client_id)){ 
                    $client = $company->client_id; }else{$client = "0";} 
                echo form_dropdown('client_id', $options, $client, 'style="width:100%" class="chosen-select"');?>
        </div>  
    </div>    

</div>

<!-- Nom société client + matricule fiscale -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name"><?=$this->lang->line('application_name');?> <?=$this->lang->line('application_company');?> *</label>
            <input id="name" type="text" name="name" class="required form-control" value="<?php if(isset($company)){echo $company->name;} ?>"  required/>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="vat"><?=$this->lang->line('application_vat');?></label>
            <input id="vat" type="text" name="vat" class="form-control" value="<?php if(isset($company)){echo $company->vat;}?>" />
        </div>
    </div>
</div>

<!-- site web entreprise + email -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
                <label for="website"><?=$this->lang->line('application_website');?></label>
                 <div class="input-group"> <div class="input-group-addon">http://</div>
                <input id="website" type="text" name="website" class="form-control" value="<?php if(isset($company)){echo $company->website;} ?>" />
                </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
                <label for="email"><?=$this->lang->line('application_email');?></label>
                <input id="email" type="email" name="email" class="form-control" value="<?php if(isset($company)){echo $company->email;}?>" />
        </div>
    </div>
</div>

<!-- téléphone + portable -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
                <label for="phone"><?=$this->lang->line('application_phone');?></label>
                <input id="phone" type="tel" name="phone" pattern="^((\+\d{1,3}?\(?\d\)?\d{1,5})|(\(?\d{2,6}\)?))?(\d{3,4})?(\d{4})(( x| ext)\d{1,5}){0,1}$"  class="form-control" value="<?php if(isset($company)){echo $company->phone;}?>" />
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
                <label for="mobile"><?=$this->lang->line('application_mobile');?></label>
                <input id="mobile" type="tel" name="mobile" pattern="^((\+\d{1,3}?\(?\d\)?\d{1,5})|(\(?\d{2,6}\)?))?(\d{3,4})?(\d{4})(( x| ext)\d{1,5}){0,1}$" class="form-control" value="<?php if(isset($company)){echo $company->mobile;}?>" />
        </div>
    </div>
</div>

<!-- adresse -->
<div class="form-group">
        <label for="address"><?=$this->lang->line('application_address');?></label>
        <input id="address" type="text" name="address" class="form-control" value="<?php if(isset($company)){echo $company->address;}?>" />
</div>

<!-- code postale + ville + pays -->
<div class="row">
    <div class="col-md-4">    
        <div class="form-group">
                <label for="zipcode"><?=$this->lang->line('application_zip_code');?></label>
                <input id="zipcode" type="text" name="zipcode" class="form-control" value="<?php if(isset($company)){echo $company->zipcode;}?>" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
                <label for="city"><?=$this->lang->line('application_city');?></label>
                <input id="city" type="text" name="city" class="form-control" value="<?php if(isset($company)){echo $company->city;}?>" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
                <label for="country"><?=$this->lang->line('application_country');?></label>
                <input id="country" type="text" name="country" class="form-control" value="<?php if(isset($company)){echo $company->country;}?>" />
        </div>
    </div>
</div>



<!--Client exonoree de =timbre fiscale  -->
<div class="form-group">
      <label for="city"><?=$this->lang->line('application_timbre');?></label>
	  <?php if($company->timbre_fiscal==0){ ?>
      <input type="checkbox" name="timbre_fiscal" data-labelauty="<?=$this->lang->line('application_exoneration_timbre');?>"  class="checkbox" value="1" >
      <?php }else{?>
	  <input type="checkbox" name="timbre_fiscal" data-labelauty="<?=$this->lang->line('application_exoneration_timbre');?>"  class="checkbox" value="0" checked="checked">
	  <?php } ?>
</div>

<!--Client exonoree de TVA  -->
<div class="form-group">
	  <label for="city"><?=$this->lang->line('application_TVA');?></label>
		<?php if($company->tva==0){ ?>
	  <input type="checkbox" name="tva" data-labelauty="<?=$this->lang->line('application_exoneration_tva');?>"  class="checkbox" value="0" >
	  <?php }else{?>
	  <input type="checkbox" name="tva" data-labelauty="<?=$this->lang->line('application_exoneration_tva');?>"  class="checkbox" value="1" checked="checked">
	  <?php } ?>
</div>
<!--retenue guarantee -->
<div class="form-group">
	  <label for="city"><?=$this->lang->line('application_guarantee');?></label>
		  <?php if($company->guarantee==0){ ?>
	  <input type="checkbox" name="guarantee" data-labelauty="<?=$this->lang->line('application_retenue');?>" class="checkbox" value="0" >
	  <?php }else{?>
	  <input type="checkbox" name="guarantee" data-labelauty="<?=$this->lang->line('application_retenue');?>"  class="checkbox" value="1" checked="checked">
	  <?php } ?>
</div>
			
<div class="modal-footer">
	<input type="submit" name="send" id="btnSubmit" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>
<script>
function myFunction(a) {
	var  exist = false;
	if(a.charAt(2)== 0 && a.charAt(1)== 0 && a.charAt(0)== 0){
		a = a.replace(0, '');
		a = a.replace(0, '');
		a = a.replace(0, '');
	}else if (a.charAt(2)!= 0 && a.charAt(1)== 0 && a.charAt(0)== 0){
		a = a.replace(0, '');
		a = a.replace(0, '');
	}else if (a.charAt(1)!= 0 && a.charAt(0)== 0){
		a = a.replace(0, '');
	} 
	$.ajax({
		type: 'POST',
		dataType: "text",
		url: 'clients/AllReference',
		success: function (response) {
			var res = response.split(",");
			for(var i=0 in res) {
				res[i] = res[i].replace("reference", '');
				res[i] = res[i].replace("{", '');
				res[i] = res[i].replace("}", '');
				res[i] = res[i].replace("[", '');
				res[i] = res[i].replace("]", '');
				res[i] = res[i].replace(":", '');
				res[i] = res[i].replace('""', '');
				res[i] = res[i].replace('"', '');
				res[i] = res[i].replace('"', '');
			}
			for(var i=0 in res) {
				if (res[i] == a) {
					exist = true; 	
				}
			} 			
			if ( exist == true) {
				document.getElementById("btnSubmit").disabled = true;
				$( "div.reference" ).html('<p>Veuillez changer la référence</p>');
				$( "div.reference" ).show();
			}else {
				document.getElementById("btnSubmit").disabled = false;
				$( "div.reference" ).hide();
			}
		}
	})
}
</script>