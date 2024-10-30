<?php   
    $attributes = array('class' => '', 'id' => '_company', 'autocomplete' => 'off');
    echo form_open_multipart($form_action, $attributes); 
?>

<div class="form-group">
    <label for="name"><?=$this->lang->line('application-Libelle');?> *</label>
    <input id="name" type="text" name="name" class="required form-control" 
		<?php if(isset($data)){ ?> disabled="disabled"  <?php }?>
		value="<?php if(isset($data)){echo $data->name; }?>" 
		required/>
</div>

<div class="form-group">
    <label for="nbrChiffre"><?=$this->lang->line('application_chiffre_apvergule');?> *</label>
    <input id="nbrChiffre" type="number" name="nbrChiffre" class="required form-control" min=0 value="<?php if(isset($chiffre)){echo $chiffre;} ?>" required/>
</div>

<div class="form-group">
    <label for="name"><?=$this->lang->line('application_Description');?> </label>
    <input id="name" type="text" name="description" class="form-control" value="<?php if(isset($data)){echo $data->description;} ?>"/>
</div>
<div class="modal-footer">
    <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
    <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>
