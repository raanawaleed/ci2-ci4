

<?php   
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);

$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company'])); 
?>


<?php if(isset($company)){ ?>
<input id="id" type="hidden" name="id_companie" value="<?=$company->id;?>" />
<?php } ?>

	<div class="col-sm-12 col-md-12 main"> 

<div class="row">




 			<div class="form-group ">
				<label for="description"><?=$this->lang->line('application_titre_prime');?>*</label>
				<input type="text" name="description" id="description" class="form-control" required>
			</div>


 			<div class="form-group ">
				<label for="valeur"><?=$this->lang->line('application_valeur_prime');?>- TND</label>
				<input type="number" name="valeur" id="valeur" class="form-control" required>
			</div>


 			<div class="form-group ">
				<label for="cotisable"><?=$this->lang->line('application_cotisable');?>*</label>
				<input type="checkbox" name="cotisable" id="cotisable" class="form-control" >
			</div>


 			<div class="form-group ">
				<label for="Imposable"><?=$this->lang->line('application_imposable');?>*</label>
				<input type="checkbox" name="Imposable" id="Imposable" class="form-control" >
			</div>








<!-- 			<div class="form-group">
<p style="color: blue"><?=$this->lang->line('application_documents');?></p>
</div> -->

<!--             <a href="<?=base_url()?>gestionsalarie/addfonction" class="btn btn-success" data-toggle="mainmodal">
            <div>
            	<img src="https://image.flaticon.com/icons/svg/148/148764.svg" width="25px" />
			        <?=$this->lang->line('application_add');?>
			</div>
            </a>



            	<div class="table-div">
		<table class="data table" id="clients" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
		<thead>
			
			<th><?=$this->lang->line('application_name');?></th>
			<th><?=$this->lang->line('application_piece_joint');?></th>
			<th><?=$this->lang->line('application_affichier');?></th>
			<th><?=$this->lang->line('application_action');?></th>

		</thead>

		<tr>

		</tr>

		</table>
		</div> -->
            

</div>
</div>
   <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-success" value="<?=$this->lang->line('application_save');?>"/>
       
        <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
      </div>
<?php echo form_close(); ?>