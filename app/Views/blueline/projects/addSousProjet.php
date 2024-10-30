<?php   
    $attributes = array('class' => '', 'id' => '_company', 'autocomplete' => 'off');
    echo form_open_multipart($form_action, $attributes); 
?>

<div class="form-group">
    <label for="name"><?=$this->lang->line('application-Libelle');?> *</label>
    <input id="name" type="text" name="name" class="required form-control" value="<?php if(isset($data)){echo $data->name;} ?>" required/>
</div>

<div class="form-group">
    <label for="name"><?=$this->lang->line('application_Description');?> </label>
    <input id="name" type="text" name="description" class="form-control" value="<?php if(isset($data)){echo $data->description;} ?>"/>
</div>


	<div class="form-group" style="padding: 20px 9px;">
		<span><?=$this->lang->line('application_ajouter_tickets_par_defaut');?></span><br><br>
		<label class="switch" >
			<?php if(($data->create_tickets == 1) && isset($data)){?>
				<input type="checkbox" name="create_tickets" id="create_tickets" checked <?=(isset($create))? "":"disabled";?>>
			<?php	}else{ ?>  
				<input type="checkbox" name="create_tickets" id="create_tickets" <?=(isset($create))? "":"disabled";?>>>
			<?php  } ?>
		<div class="slider round"></div>
		</label>
	</div>


<div class="modal-footer">
    <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
    <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>
