<?php   
$attributes = array('class' => '', 'id' => 'sendFile');
echo form_open($form_action, $attributes); 
?>

<div class="col-sm-12  col-md-12 main">
	<!-- statistiques sur les devis -->
	<div class="row tile-row">
		<div class="col-md-2 col-xs-12 tile blue">
			<h1><span>
			<?php if ($type == "facture") {
				echo $this->lang->line('application_invoices');
			} else if ($type == "devis"){ 
				echo $this->lang->line('application_estimate');
			}	else {
				echo $this->lang->line('application_avoir');
			}
			?></span></h1>
		</div>
	</div>
</div>

<div class="col-md-12">
	<div class="table-head"><?=$this->lang->line('application_Sent_mail');?></div>
		<div class="table-div settings">
		<br>
		<br>
		<div class="row">
			<div class="col-md-2">
				</div>
				<div class="col-md-6">
				<span style="margin-right: 36px;display: inline;float: left;"><label><?=$this->lang->line('application_Object');?>*</label></span>
					<div class="form-group" style="display: inline;float:left;width:80%;">
						<input type="text" name="smtp_user" autocomplete="off" class="form-control" value="<?php
						if ($type == "facture") {
							echo $this->lang->line("application_your_facture");
						} else if  ($type == "devis") {
							echo $this->lang->line("application_your_devis");
						} else {
							echo $this->lang->line("application_your_avoir");
						}
						?><?php if($type == "avoir") {
							echo $data->avoir_num; 
						} else {
							echo $data->estimate_num; 
						}?>" required>
					</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
			</div>
			<div class="col-md-6">
			<span style="margin-right: 22px;display: inline;float: left;"><label><?=$this->lang->line('application_Contact');?></label></span>
				<div class="form-group" style="display: inline;float:left;width:80%;">
					<input type="text" name="dist" autocomplete="off" class="form-control" value="">
				</div>
			</div>
		</div> 
		<div class="row">
			<div class="col-md-2">
			</div>
			<div class="col-md-6">
			<span style="margin-right: 53px;display: inline;float: left;"><label><?=$this->lang->line('application_BCC');?></label></span>
				<div class="form-group" style="display: inline;float:left;width:80%;">
					<input type="text" name="cc" autocomplete="off" class="form-control" value="" >
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
			</div>
			<div class="col-md-6">
				<span style="margin-right: 23px;display: inline;float: left;"><label><?=$this->lang->line('application_message');?></label></span>
				<textarea id="notes" name="notes" class="textarea summernote-modal form-control" style="height:100px;display: inline;float:left;width:80%;" required></textarea>
			</div>
		</div> 
		<br>
		<!--<div class="row">
			<div class="col-md-2" style="margin-left: -35px;">
			</div>
			<div class="col-md-6"> 
				<span style="margin-right: 36px;display: inline;float: left;"><label><?=$this->lang->line('application_other_files');?></label></span>
				<input id="uploadFile" type="text" name="dummy" class="form-control uploadFile"  placeholder="<?=$this->lang->line('application_file');?>" disabled="disabled"/>
				<div class="fileUpload btn btn-primary">
					<span><i class="fa fa-upload"></i><span class="hidden-xs"> <?=$this->lang->line('application_select');?></span></span>
					<input id="userfile" type="file" name="userfile" class="upload" onchange="telech()"/>
				</div>
			</div>
		</div> 
		<!-- link to file -->
		<div class="row">
			<div class="col-md-2">
			</div>
			<div class="col-md-6">
				<span style="margin-right: 36px;display: inline;float: left;"><label><?=$this->lang->line('application_attachment');?></label></span>
					<span  class="telechd"> <a href="<?=base_url()?>
					<?php if($type == "devis"){
							echo ("estimates/preview/"); 
						} else if ($type == "facture"){
							echo ("invoices/preview/"); 
				}?><?=$data->id;?>/show" target="_blank" ><?php echo  $data->estimate_num; ?>.pdf</a></span>
			</div> 
		</div>
	<div class="form-group" style="display: none;">
		<input type="text" name="id"  class="form-control" value="<?=$data->id;?>" >
	</div>
	<!-- sent -->
	<div class="modal-footer">
            <input type="submit" name="send" class="btn btn-success" value="<?=$this->lang->line('application_save');?>"/>
            <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
        </div>
        <?php echo form_close(); ?>
	
</div>

<script>
  function telech(){
	var nom= document.getElementById('userfile').value;
    var doc =nom.split('\\');
	$('.telechd').html(doc[2]);
	}
</script>
