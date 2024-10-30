<?php   
$attributes = array('class' => '', 'id' => '_expense');
echo form_open_multipart($form_action, $attributes); 
?>
<?php if(isset($expense)){ ?>
<input id="id" type="hidden" name="id" value="<?=$expense->id;?>" />
<?php } ?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
        	<label for="type"><?=$this->lang->line('application_type');?></label>
        	<?php $options = array();
        			$options['payment'] = $this->lang->line('application_payment');
        			$options['refund'] = $this->lang->line('application_refund');
        			
        	if(isset($expense)){$type = $expense->type;}else{$type = "payment";}
        	echo form_dropdown('type', $options, $type, 'style="width:100%" class="chosen-select"');?>
        </div> 
    </div>  
    <div class="col-md-6">
        <div class="form-group">
        	<label for="category"><?=$this->lang->line('application_category');?></label>
        	<?php 
        		$options = array(); 
        		foreach($categories as $val){
        				if($this->lang->line('application_'.$val->name) == false){
        					$options[$val->id] = $val->name;
        				} else {
        					$options[$val->id] = $this->lang->line('application_'.$val->name);
        				}
        		}
        	echo form_dropdown('category', $options, $expense->category, 'style="width:100%" class="chosen-select"'); ?>
        </div> 
    </div>
</div>

<div class="form-group">
	<label for="terms"><?=$this->lang->line('application_description');?></label>
	<input class="form-control" name="description" type="text" value="<?php if(isset($expense)){ echo $expense->description;}?>"  required/>
</div>

<div class="form-group">
	<label for="date"><?=$this->lang->line('application_date');?></label>
	<input id="date" type="text" name="date" class="datepicker form-control" value="<?php if(isset($expense)){echo $expense->date;}else{echo date('Y-m-d');} ?>"  required/>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="value"><?=$this->lang->line('application_value');?></label>
			<input class="form-control decimal" name="value" id="value" type="text" value="<?php if(isset($expense)){ echo $expense->value;} ?>" required/>
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<label for="currency"><?=$this->lang->line('application_currency');?></label>		
			<?php if (!isset($expense)){ ?>
				<select name="currency"  id="currency" id="" class="chosen-select">
				 <?php foreach($currencys as $currency){
				   if($core_settings->currency==$currency->name){ ?>
						<option value="<?=$currency->name?>" selected><?=$currency->name?></option>
				   <?php  } else{?>
						<option value="<?=$currency->name?>"><?=$currency->name?></option>
				   <?php }}?>
				</select>
			<?php } else { ?>
				<select name="currency"  id="currency" id="" class="chosen-select">
				 <?php foreach($currencys as $currency){
				   if($expense->currency==$currency->name){ ?>
						<option value="<?=$currency->name?>" selected><?=$currency->name?></option>
				   <?php  } else{?>
						<option value="<?=$currency->name?>"><?=$currency->name?></option>
				   <?php }}?>
				</select>
			<?php } ?>	
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group" style="padding-bottom:0;">
			<label for="vat"><?=$this->lang->line('application_tax_included');?></label>
			<div class="input-group" style="padding-top: 7px;">
					<input class="form-control" name="vat" type="text" value="<?php if(isset($expense)){ echo $expense->vat;}else{$core_settings->tax;} ?>"/>
			<div class="input-group-addon input-group-addon--right">%
		</div>
	</div>
</div>
</div>
</div>

<div class="row">
    <div class="col-md-4">
      <div class="form-group">
    	<label for="project"><?=$this->lang->line('application_linked_to_project');?></label>
    	<?php $options = array();
    			$options['0'] = $this->lang->line('application_no');
    			foreach ($projects as $value):  
    			$options[$value->id] = $value->name;
    			endforeach;
    	if(isset($expense->project->id)){$project = $expense->project->id;}else{$project = "0";}
    	echo form_dropdown('project_id', $options, $project, 'style="width:100%" class="chosen-select switcher" data-switcher="rebill" ');?>
       </div> 
    </div>
    <div class="col-md-4">
         <div class="form-group">
        	<label for="type"><?=$this->lang->line('application_rebill');?></label>
        	<?php $options = array();
        			$options['0'] = $this->lang->line('application_no');
        			$options['1'] = $this->lang->line('application_yes');
        			
        	if(isset($expense)){
        		$rebill = $expense->rebill; 
        		if($rebill == 2){
        			$options['2'] = $this->lang->line('application_rebilled_on_invoice')." #".$expense->invoice->reference;
        			$disabled = "disabled";
        		}else{$disabled = "";}
        		
        	}else{$rebill = "0"; $disabled = "disabled";}
        	echo form_dropdown('rebill', $options, $rebill, 'id="rebill" style="width:100%" class="chosen-select" '.$disabled);?>
         </div>  
        </div>
        <div class="col-md-4">
            <div class="form-group">
            	<label for="reference" style="font-size: 10.5px;"><?=$this->lang->line('application_receipt_reference');?></label>
            	<input id="reference" type="text" name="reference" class="form-control"  value="<?php if(isset($expense)){echo $expense->reference;} ?>"  />
            </div>
        </div>
</div>
<div class="form-group">
	<label for="userfile"><?=$this->lang->line('application_attachment');?></label>
	<div>
		<input id="uploadFile" type="text" name="dummy" class="form-control uploadFile" placeholder="<?php if(isset($expense->attachment)){ echo $expense->attachment; }else{ echo "Choose File";} ?>" disabled="disabled" />
		<div class="fileUpload btn btn-primary">
		  <span><i class="fa fa-upload"></i><span class="hidden-xs"> <?=$this->lang->line('application_select');?></span></span>
		  <input id="uploadBtn" type="file" data-switcher="attachment_description" name="userfile" class="upload switcher" accept="capture=camera" />
		</div>
	</div>
</div> 
<div class="form-group">
	<label for="attachment_description"><?=$this->lang->line('application_attachment_description');?></label>
	<input id="attachment_description" type="text" name="attachment_description" class="form-control"  value="<?php if(isset($expense->attachment)){echo $expense->attachment_description.'"';} else{ echo '" disabled="disabled"';}?>  />
 </div>
 <?php if(isset($expense->user->firstname)){ ?>
  <div class="form-group" style="font-size: 11px; margin: 23px 3px 3px;">
    <?=$this->lang->line('application_created_by')." ".$expense->user->firstname." ".$expense->user->lastname; ?> 
 </div>
<?php }else{ ?>
<input id="user_id" type="hidden" name="user_id" value="<?=$this->user->id;?>" />
<?php    }?>
<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>
<?php echo form_close(); ?>