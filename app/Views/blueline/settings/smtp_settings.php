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
	<div class="col-md-9">
		<div class="table-head"><?=$this->lang->line('application_smtp_settings');?></div>
			<div class="table-div settings">
			<?php   
			$attributes = array('class' => '', 'id' => 'smtpsettings');
			echo form_open($form_action, $attributes); 
			?>
				<div class="form-header"><?=$this->lang->line('application_SMTP_settings_for_outgoing_emails');?></div>
				<span class="highlight-text"><?=$this->lang->line('application_SMTP_settings_not_changed');?></span>
				<br>
				<br>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_protocol');?></label>
						<select name="protocol" class="formcontrol chosen-select ">
							<?php if($this->config->item("protocol") != ""){ ?>
								<option value="<?=$this->config->item("protocol");?>" selected=""><?=$this->config->item("protocol");?></option>
							<?php } ?>
								<option value="smtp" >SMTP</option>
								<option value="sendmail" >sendmail</option>
								<option value="mail" >mail</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_hostname');?></label>
						<input type="text" name="smtp_host" class="form-control" value="<?=$this->config->item("smtp_host");?>">
					</div>
				</div>
			</div>
		
			<div class="row">	
				<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_username');?></label>
						<input type="text" name="smtp_user" autocomplete="off" class="form-control" value="<?=$this->config->item("smtp_user");?>">
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_password');?></label>
						<input type="password" autocomplete="off" name="smtp_pass" class="form-control" value="<?=$this->config->item("smtp_pass");?>">
					</div>
				</div>
			</div>
			<div class="row">	
				<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_port');?> (25, 465, 587)</label>
						<input type="text" name="smtp_port" class="form-control" value="<?=$this->config->item("smtp_port");?>">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_security');?></label>
						<select name="smtp_crypto" class="formcontrol chosen-select ">
								<option value="" <?php if($this->config->item("smtp_crypto") == ""){ echo 'selected="selected"';}?> >None</option>
								<option value="tls" <?php if($this->config->item("smtp_crypto") == "tls"){ echo 'selected="selected"';}?>>TLS</option>
								<option value="ssl" <?php if($this->config->item("smtp_crypto") == "ssl"){ echo 'selected="selected"';}?>>SSL</option>
						</select>
					</div>
				</div>
			</div>

			<div class="row">	
				<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_timeout');?></label>
						<input type="text" name="smtp_timeout" class="form-control" value="5">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label><?=$this->lang->line('application_debug');?> (Enable only for Testing!)</label>
						<select name="smtp_debug" class="formcontrol chosen-select ">
								<option value="0" <?php if($this->config->item("smtp_debug") == "0"){ echo 'selected="selected"';}?> >Off</option>
								<option value="1" <?php if($this->config->item("smtp_debug") == "1"){ echo 'selected="selected"';}?> >Commands</option>
								<option value="2" <?php if($this->config->item("smtp_debug") == "2"){ echo 'selected="selected"';}?> >Commands and data</option>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group no-border">
				<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
			</div>
			<?php echo form_close(); ?>
			<?php   
			$attributes = array('class' => '', 'id' => 'smtpsettings');
			echo form_open($form_action2, $attributes); 
			?>
			<div class="form-header"><?=$this->lang->line('application_send_test_email');?></div>
				<div class="form-group">
					<label><?=$this->lang->line('application_email');?></label>
					<input type="email" name="dist" class="form-control" value="" required>
				</div>
				<div class="form-group no-border">
					<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_send_test_email');?>"/>
				</div>

			<?php echo form_close(); ?>
			</div>
		</div>
	</div>