<?php   
    $attributes = array('class' => '', 'id' => '_company', 'autocomplete' => 'off');
    echo form_open_multipart($form_action, $attributes); 
?>

<div class="form-group">
    <label for="name"><?=$this->lang->line('application_unit_value');?> *</label>
    <input id="name" type="text" name="value" class="required form-control" value="<?php if(isset($unit)){echo $unit->value;} ?>" required/>
</div>
<div class="form-group">
    <label for="name"><?=$this->lang->line('application_unit_description');?></label>
    <input id="name" type="text" name="description" class="form-control" value="<?php if(isset($unit)){echo $unit->description;} ?>" />
</div>
<div class="modal-footer">
    <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
    <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>

<?php echo form_close(); ?>
