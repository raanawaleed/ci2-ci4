<div id="row">
	<div class="col-md-3">
		<div class="list-group">
			<?php foreach ($submenu as $name=>$value):
			$badge = "";
			$active = "";
			if($value == "settings/updates" && $update_count){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
			if($value == "settings"){ $active = 'active';}?>
			   <a class="list-group-item <?=$active;?>"
			   id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){
				   echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];
				   } ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
			<?php endforeach;?>

		</div>
	</div>
	<div class="col-md-9">
		<div class="table-head"><?=$this->lang->line('application_settings');?></div>
		<?php   
		$attributes = array('class' => '', 'id' => 'settings_form');
		echo form_open_multipart($form_action, $attributes); 
		?>
		<div class="table-div">	
			<!-- infos générale -->
			<div class="form-header"><?=$this->lang->line('application_general_info');?></div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_email');?> *</label>
						<input type="email" name="email" class="required form-control" value="<?=$settings->email;?>" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_domain');?> <button type="button" class="btn-option po pull-right" data-toggle="popover" data-placement="left" data-content="URL complète de votre installation vision ERP." data-original-title="URL"> <i class="fa fa-info-circle"></i></button>
						</label>
						<input type="text" name="domain" class="required form-control" value="<?=$settings->domain;?>" disabled required>
					</div>
				</div>
			</div>
			<!-- signataire des documents -->
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Signataire des documents<button type="button" class="btn-option po pull-right" data-toggle="popover" data-placement="left" data-content="Nom de la personne responsable de signer les docuemnts administratifs." data-original-title="URL"> <i class="fa fa-info-circle"></i></button>
						</label>
						<input type="text" name="signataire" class="form-control" value="<?=$settings->signataire;?>" >
					</div>
				</div>
			</div>
		
		<!-- Formats -->
		<div class="form-header"><?=$this->lang->line('application_formats');?></div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="currency"><?=$this->lang->line('application_default_currency');?></label>
						<div class="input-group col-md-12">
						  <select name="currency" id="currency" class="chosen-select" onchange="myFunction()">
							  <option value="<?=$settings->currency;?>"><?=$settings->currency;?></option>
							  <?php foreach($currencys as $currency){
								  if($settings->currency!=$currency->name){?>
							  <option value="<?=$currency->name;?>"><?=$currency->name;?></option>
							  <?php }}?>
						  </select> 
						</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="chiffre"><?=$this->lang->line('application_chiffre_apvergule');?></label>
						<input type="number" name="chiffre" id="chiffre" min= "0" max = "5" class="form-control" value="<?=$settings->chiffre;?>"  disabled required>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="echeances"><?=$this->lang->line('application_due_date');?></label>
						<div class="input-group col-md-12">
						  <select name="echeance" id="echeances" class="chosen-select">
						  <option value="<?=$settings->echeance;?>"><?=$this->lang->line('application_issue_date').' + '.$settings->echeance.' Jours';?></option>
						  <?php foreach($echeances as $echeance){
							  if($settings->echeance!=$echeance->name){?>
						  <option value="<?=$echeance->name;?>"><?=$this->lang->line('application_issue_date').' + '.$echeance->name.' Jours';?></option>
						  <?php }}?>
						  </select> 
						</div>
				</div>
			</div>
			<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_date_format');?></label>
						 <?php $options = array(
							'Y/m/d'    => date("Y/m/d"),
							'm/d/Y' => date("m/d/Y"),
							'd/m/Y' => date("d/m/Y"));
							echo form_dropdown('date_format', $options, $settings->date_format, 'style="width:250px" class="chosen-select"'); ?>
						
					</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_date_time_format');?></label>
						 <?php $options = array(
							'g:i a'  => date("g:i a"),
							'H:i' => date("H:i")
							);
							echo form_dropdown('date_time_format', $options, $settings->date_time_format, 'style="width:250px" class="chosen-select"'); ?>
						
					</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label><?=$this->lang->line('application_currency_position');?></label>
					 <?php $options = array(
						'1'  => $settings->currency." 100",
						'2' => "100 ".$settings->currency
						);
						echo form_dropdown('money_currency_position', $options, $settings->money_currency_position, 'style="width:250px" class="chosen-select"'); ?>
					
				</div>
			</div>
		</div>
		
			<!-- logo -->
			<div class="form-header"><?=$this->lang->line('application_logo');?></div>
			<div class="row">
			<div class="col-md-3">
				<div class="form-group" style="padding: 20px 9px;">
					<span><?=$this->lang->line('application_display_logo_facture');?></span><br><br>
					<label class="switch" >
						<?php if($settings->display_logo_facture==1){ ?>
					<input type="checkbox" name="display_logo_facture" checked>
					<?php	}else{ ?>  
					<input type="checkbox" name="display_logo_facture">
					<?php  } ?>
					<div class="slider round"></div>
					</label>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group" style="padding: 20px 9px;">
					<span><?=$this->lang->line('application_display_logo_devis');?></span><br><br>
					<label class="switch" >
						<?php 
						if($settings->display_logo_devis==1){ ?>
						<input type="checkbox" name="display_logo_devis" checked>
						<?php	}else{ ?>  
						<input type="checkbox" name="display_logo_devis">
						<?php  } ?>
					<div class="slider round"></div>
					</label>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group" style="padding: 20px 9px;">
					<span><?=$this->lang->line('application_display_logo_commande');?></span><br><br>
					<label class="switch" >
						<?php if($settings->display_logo_commande==1){ ?>
						<input type="checkbox" name="display_logo_commande" checked>
						<?php	}else{ ?>  
						<input type="checkbox" name="display_logo_commande">
						<?php  } ?>
						<div class="slider round"></div>
					</label>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group" style="padding: 20px 9px;">
					<span><?=$this->lang->line('application_display_logo_livraison');?></span><br><br>
					<label class="switch" >
						<?php if($settings->display_logo_livraison==1){ ?>
						<input type="checkbox" name="display_logo_livraison" checked>
						<?php	}else{ ?>  
						<input type="checkbox" name="display_logo_livraison">
						<?php  } ?>
					<div class="slider round"></div>
					</label>
				</div>
			</div>
			<!-- logo avoir -->
			<div class="col-md-3">
				<div class="form-group" style="padding: 20px 9px;">
					<span><?=$this->lang->line('application_display_logo_avoir');?></span><br><br>
					<label class="switch" >
						<?php if($settings->display_logo_avoir==1){ ?>
					<input type="checkbox" name="display_logo_avoir" checked>
					<?php	}else{ ?>  
					<input type="checkbox" name="display_logo_avoir">
					<?php  } ?>
					<div class="slider round"></div>
					</label>
				</div>
			</div>
	</div>
		<div class="form-group no-border">
			 <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
			
		</div>
	
	<?php echo form_close(); ?>
	</div>
	</div>

	</div>
	<style>
	/* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
  margin: -13px 0;
}

/* Hide default HTML checkbox */
.switch input {display:none;}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

<script>
function myFunction() {
	var currency = document.getElementById('currency').value; 
	$.ajax({
		type: 'POST',
		dataType: "text",
		url: '/settings/chiffreDevise/' + currency,
		success: function (response) {
			if (response.indexOf('{') > -1) {
				response = response.substr(response.indexOf('{'))
			} else if (response.indexOf('[') > -1) {
				response = response.substr(response.indexOf('['))
			} else {
				response = response.substr(response.indexOf('"'))
			}
			var responsesplit = JSON.parse(response);
			document.getElementById("chiffre").value = responsesplit;
		}
	});	
}
  
</script>
