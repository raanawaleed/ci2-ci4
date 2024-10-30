<div id="row">
	<div class="col-md-3">
		<div class="list-group">
			<?php foreach ($submenu as $name=>$value):
			$badge = "";
			$active = "";
			if($value == "settings/societe"){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
			if($name == $breadcrumb){ $active = 'active';}?>
				<a class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
			<?php endforeach;?>
		</div>
	</div>

	   <?php   
		$attributes = array('class' => '', 'id' => 'user_form');
		echo form_open_multipart($form_action, $attributes); 
		?>
    <div class="col-md-3"></div>
	<div class="col-md-9">
		<div class="row">
			<div class="span12 marginbottom20">
				<div class="table-head"><?=$this->lang->line('application_choice_templates')?><span class="pull-right"></span></div>
				<div class="subcont">
			<div class="row">
				<?php $test = ""; foreach ($files as $file ) { $test= $test.','.$file;   ?>
					<div class="col-md-4 " style="margin-bottom: 35px;">
						<!-- bouton pdf -->
						<span><?php echo ($file); ?> / </span>
						<a target="_blank" href="<?=base_url()?>settings/preview/<?=$file;?>/show" class="btn-option">
							<i class="" title="PDF"><img src="<?=base_url()?>assets/blueline/images/pdf.png" alt=""></i>
						</a><br><br>
						<label class="switch" >
							<?php if($defaultTemplate == $file){ ?>
						<input type="checkbox" id =<?=$file;?> onclick="tester('<?=$file;?>');" name="default_template" value="<?=$file;?>" checked>
						<?php	}else{ ?>  
						<input type="checkbox" id =<?=$file;?> onclick="tester('<?=$file;?>');" name="default_template" value="<?=$file;?>">
						<?php  } ?>
						<div class="slider round"></div>
						</label>
					</div>
				<?php } ?>	
			</div>	
			<div class="form-group no-border">
				<input type="submit" name="send" style="float: right;" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
			</div>
			<?php echo form_close(); ?>
			</div>
		</div>

	</div>
</div>
<script>

function tester(id)
{ 	
	var files = "<?php echo ($test);?>"; 
	files = files.split (",");
	element = document.getElementById(id);
	if(element.checked) {
		for (var prop in files) {
			if(id != files[prop] && files[prop]!=""){ 
				otherelement = document.getElementById(files[prop]);
				otherelement.checked = false; 
			}
		} 
	}
}
</script>
