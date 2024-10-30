<div class="col-sm-12  col-md-12 main">  
    <div class="row tile-row">
	  <div class="col-md-3 col-xs-12 tile"><div class="icon-frame hidden-xs"><i class="fa fa-file-text-o"></i> </div><h1><span><?=$this->lang->line('application_command');?></span></h1></div>
      <div class="col-md-3 col-xs-3 tile"><div class="icon-frame hidden-xs"><i class="ion-ios-bell"></i> </div><h1> <?php if(isset($invoices_due_this_month)){echo $invoices_due_this_month;} ?><span><?=$this->lang->line('application_command');?></span></h1><h2><?=$this->lang->line('application_due_this_month');?></h2></div>
      <div class="col-md-3 col-xs-3 tile"><div class="icon-frame secondary hidden-xs"><i class="ion-ios-analytics"></i> </div><h1> <?php if(isset($invoices_paid_this_month)){echo $invoices_paid_this_month;} ?> <span><?=$this->lang->line('application_command');?></span></h1><h2><?=$this->lang->line('application_paid_this_month');?></h2></div>
      <div class="col-md-3 col-xs-3 tile hidden-xs">
      <div style="width:97%; margin-top: -4px; margin-bottom: 17px; height: 80px;">
            <canvas id="tileChart" width="auto" height="80"></canvas>
        </div>
      </div>
    
    </div>  
    <div class="row">
      <a href="<?=base_url()?>boncommande/create" class="btn btn-success" data-toggle="mainmodal"><?=$this->lang->line('application_new_commande');?></a>
      <a href="<?=base_url()?>invoices" class="btn btn-primary"><?=$this->lang->line('application_invoices');?></a> 
      <a href="<?=base_url()?>boncommande" class="btn btn-primary"><?=$this->lang->line('application_commande');?></a>
      <a href="#" class="btn btn-primary"><?=$this->lang->line('application_livraison');?></a>
      <!--<div class="btn-group pull-right-responsive margin-right-3">-->
          <!--<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">-->
            <!--<?php $last_uri = $this->uri->segment($this->uri->total_segments()); if($last_uri != "commande"){echo $this->lang->line('application_'.$last_uri);}else{echo $this->lang->line('application_all');} ?> <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right" role="menu">
            <?php foreach ($submenu as $name=>$value):?>
	                <li><a id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$name?></a></li>
	            <?php endforeach;?>
          </ul>-->
      <!--</div>-->
    </div>  
    <div class="row">
         <div class="table-head"><?=$this->lang->line('application_commande');?></div>
         <div class="table-div">
		<table class="data table" id="commande" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
		<thead>
			<th width="70px" class="hidden-xs"><?=$this->lang->line('application_estimate_id');?></th>
			<th ><?=$this->lang->line('application_client');?></th>
			<th class="hidden-xs"><?=$this->lang->line('application_issue_date');?></th>
			<th class="hidden-xs"><?=$this->lang->line('application_total');?></th>
			<th><?=$this->lang->line('application_status');?></th>
			<th><?=$this->lang->line('application_action');?></th>
		</thead>
		<?php foreach ($commande as $value):
        $change_date = "";
        switch($value->estimate_status){
          case "Open": $label = "label-default"; break;
          case "Accepted": $label = "label-success"; $change_date = 'title="'.date($core_settings->date_format, human_to_unix($value->estimate_accepted_date.' 00:00')).'"'; break;
          case "Sent": $label = "label-warning"; $change_date = 'title="'.date($core_settings->date_format, human_to_unix($value->estimate_sent.' 00:00')).'"'; break; 
          case "Declined": $label = "label-important"; $change_date = 'title="'.date($core_settings->date_format, human_to_unix($value->estimate_accepted_date.' 00:00')).'"'; break;
          case "Invoiced": $label = "label-chilled"; $change_date = 'title="'.$this->lang->line('application_Accepted').' '.date($core_settings->date_format, human_to_unix($value->estimate_accepted_date.' 00:00')).'"'; break;
          case "Revised": $label = "label-warning"; $change_date = 'title="'.$this->lang->line('application_Revised').' '.date($core_settings->date_format, human_to_unix($value->estimate_accepted_date.' 00:00')).'"'; break;
          

          default: $label = "label-default"; break;
        } ?>
		<tr id="<?=$value->id;?>" >
			<td class="hidden-xs"><?=$core_settings->estimate_prefix;?><?=$value->estimate_reference;?></td>
			<td><span class="label label-info"><?php if(isset($value->company->name)){echo $value->company->name; }?></span></td>
			<td class="hidden-xs"><span><?php $unix = human_to_unix($value->issue_date.' 00:00'); echo '<span class="hidden">'.$unix.'</span> '; echo date($core_settings->date_format, $unix);?></span></td>
			<td class="hidden-xs"><?=display_money(sprintf("%01.2f", round($value->sum, 2)));?></td>
			<td><span class="label  <?=$label?> tt" <?=$change_date;?>><?=$this->lang->line('application_'.$value->estimate_status);?></span></td>
		
			<td class="option" width="8%">
				        <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>boncommande/delete/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-times"></i></button>
				        <a href="<?=base_url()?>boncommande/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
			</td>
		</tr>

		<?php endforeach;?>
	 	</table>
            </div>
    </div>