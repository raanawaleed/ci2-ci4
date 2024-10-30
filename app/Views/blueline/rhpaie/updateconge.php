<?php
    $attributes = array('class' => '', 'id' => '_company');
    echo form_open_multipart($form_action, $attributes);
    $core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company']));
?>

<?php if(isset($company)){ ?>
    <input id="id" type="hidden" name="id_companie" value="<?=$company->id;?>" />
<?php } ?>

<div class="row">
    <div class="col-sm-12 col-md-12 main">
        <div class="subcont">
<input type="hidden" value="<?php echo (isset($conge)? $conge->id_salarie:"") ?>" name="id_salarie" id="id_salarie">
                <!-- date début & date fin -->
            <div class="row">
                <!-- date début -->
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="start">Date début *</label>
                        <input class="form-control datepicker" name="date_debut" id="date_debut" type="text" value="<?php echo (isset($conge)? $conge->date_debut:"") ?>"  required/>
                    </div>
                </div>
                <!-- date fin -->
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="end"> Date fin*</label>
                        <input class="form-control datepicker-linked" name="date_fin" id="date_fin" type="text"  value="<?php echo (isset($conge)? $conge->date_fin:"") ?>"  required/>
                    </div>
                </div>
            </div>
             <!-- motif -->
            <div class="form-group">
                <label for="motif"><?=$this->lang->line('application_motif');?>*</label>
          
                <?php echo form_dropdown_ref('motif', $motif, $conge->motif, 'style="width:100%" class="chosen-select"'); ?>
               
            </div>

          

        </div>

        <div class="modal-footer">
            <input type="submit" name="send" class="btn btn-success" value="<?=$this->lang->line('application_save');?>"/>
            <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
