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
	<!-- notification -->
	<div class="col-md-9">
    <div class="table-head"><?=$this->lang->line('application-email-notification');?></div>
		
			<div class="span12 marginbottom20">
            <div class="subcont">
            <?php   
                $attributes = array('class' => '', 'id' => '_company', 'autocomplete' => 'off');
                echo form_open_multipart($form_action, $attributes); 
            ?>
                    <div class="form-group">
                        <label for="description"><?=$this->lang->line('application-notification');?> </label>
                        <input id="email_notification" type="text" name="email_notification" value="<?php if(isset($data)){echo $data->email_notification;} ?>" class="form-control"/>
                    </div>
                    <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
  
            <?php echo form_close(); ?> 
			</div>
		   
       </div>
	</div>
	
	
	

