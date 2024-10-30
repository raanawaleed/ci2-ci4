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
            <!-- salariés -->
            <div class="form-group">
                <label for="id_salarie"><?=$this->lang->line('application_salarie');?>*</label>
                <select name="id_salarie" id="id_salarie" class="chosen-select">
                            <option value="<?=$id?>" selected><?=$user?></option>
                </select>
            </div>

                <!-- date début & date fin -->
            <div class="row">
                <!-- date début -->
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="start">Date début *</label>
                        <input class="form-control datepicker" name="date_debut" id="date_debut" type="text" value="<?php echo (isset($item)? $item->date_debut:"") ?>"  required/>
                    </div>
                </div>
                <!-- date fin -->
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="end"> Date fin*</label>
                        <input class="form-control datepicker-linked" name="date_fin" id="date_fin" type="text"  value="<?php echo (isset($item)? $item->date_fin:"") ?>"  required/>
                    </div>
                </div>
            </div>
             <!-- motif -->
            <div class="form-group">
                <label for="motif"><?=$this->lang->line('application_motif');?>*</label>
          
               
              <select name="motif" id="motif" class="chosen-select">
                   
                    <?php foreach($motif as $g){?>
                        <option value="<?=$g->id?>" <?php echo (isset($item)? (($item->motif == $g->id)? " ":""):"") ?>><?=$g->name?></option>
                    <?php }?>
                
                </select> 
            </div>

          

        </div>

        <div class="modal-footer">
            <input type="submit" name="send" class="btn btn-success" value="<?=$this->lang->line('application_save');?>"/>
            <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
