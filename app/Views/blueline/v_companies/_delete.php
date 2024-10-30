<?php
    $attributes = array('class' => '', 'id' => '_delete', 'autocomplete' => 'off');
    echo form_open_multipart($form_action, $attributes);
?>

<h5><?=$this->lang->line('application_company_delete_text');?> (<?=$company->name?>)</h5>

<div class="form-group">
    <label for="password"><?=$this->lang->line('application_password');?> *</label>
     <input id="password" type="password" name="x" class="form-control" style="display:none;" />
    <input id="password" type="password" name="password" class="required form-control" required/>
</div>

<div class="modal-footer">
    <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_no');?></a>
    <input type="submit" name="send" class="btn btn-danger" value="<?=$this->lang->line('application_yes');?>"/>
</div>

<?php echo form_close(); ?>