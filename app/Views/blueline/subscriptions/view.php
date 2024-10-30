<div class="row">
	<div class="col-xs-12 col-sm-8 col-md-8">
		<a href="<?=base_url()?>subscriptions/update/<?=$subscription->id;?>/view" class="btn btn-primary" data-toggle="mainmodal"><i class="fa fa-edit visible-xs" title="Modifier"></i><span class="hidden-xs"><?=$this->lang->line('application_edit');?></span>
		</a>
		
		<?php 
//		if(($subscription->end_date > $subscription->next_payment || $subscription->end_date == "") && date("Y-m-d") >= date('Y-m-d', strtotime('-3 day', strtotime ($subscription->next_payment)))){ ?>
		<a href="<?=base_url()?>subscriptions/create_invoice/<?=$subscription->id;?>" class="btn btn-primary"><i class="fa fa-file-o visible-xs"></i><span class="hidden-xs"><?=$this->lang->line('application_create_invoice');?></span></a>
		<?php //} ?>
		
		
		<?php if($subscription->status != "Paid" && isset($subscription->company_id->name)){ ?><a href="<?=base_url()?>subscriptions/sendsubscription/<?=$subscription->id;?>" class="btn btn-primary"><i class="fa fa-envelope visible-xs"></i><span class="hidden-xs"><?=$this->lang->line('application_send_subscription_to_client');?></span></a><?php } ?>
		
		<?php if($core_settings->paypal == "1" && isset($subscription->subscription_has_items[0]) && $subscription->subscribed == "0"){ ?>
		<a href="javascript:document.forms['paypal_subscribe'].submit();" class="btn btn-success pull-right"><?=$this->lang->line('application_subscribe_via_paypal');?></a>
		<?php } ?>
	</div>
	<div class="col-md-4 col-sm-4  col-xs-12">
		<a href="<?=base_url()?>subscriptions" class="btn btn-warning right">Liste des <?=$this->lang->line('application_subscriptions');?></a>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="table-head"><?=$this->lang->line('application_subscription_details');?>
		</div>
		<div class="subcont">
			<ul class="details col-xs-12 col-sm-6">
				<li><span><?=$this->lang->line('application_subscription_id');?>:</span><?=$subscription->subscription_num;?></li>
				<li class="<?=$subscription->status;?>"><span><?=$this->lang->line('application_status');?>:</span>
				<a class="label <?php if($subscription->status == 'Active'){ echo 'label-success';}else{echo 'label-important'; } ?>"><?php if(($subscription->end_date <= date('Y-m-d') && $subscription->end_date != "") && $subscription->status != "Inactive"){ echo $this->lang->line('application_ended'); }else{ echo $this->lang->line('application_'.$subscription->status);}?></a>
				<?php if($subscription->subscribed != "0"){ ?>  <a class="label label-success margin-left-5 tt" title="<?php $unix = human_to_unix($subscription->subscribed.' 00:00'); echo date($core_settings->date_format, $unix);?>" ><?=$this->lang->line('application_subscribed_via_paypal');?></a> <?php } ?>
				</li>
				<li><span><?=$this->lang->line('application_issue_date');?>:</span> <?php $unix = human_to_unix($subscription->issue_date.' 00:00'); echo date($core_settings->date_format, $unix);?></li>
				<li><span><?=$this->lang->line('application_end_date');?>:</span> <?php if($subscription->end_date != "" ){ ?><a class="label <?php if($subscription->end_date <= date('Y-m-d') && $subscription->status != "Inactive"){ echo 'label-success tt" title="'.$this->lang->line('application_subscription_has_ended'); } ?>"><?php $unix = human_to_unix($subscription->end_date.' 00:00'); echo date($core_settings->date_format, $unix);?></a> <?php }else{
					echo '<span class="label label-success" style="min-width: 10px; padding-left: 10px;"><i class="ion-ios-infinite row_icon"></i></span>';
					} ?>
				</li>
				<li><span><?=$this->lang->line('application_frequency');?>:</span> 
					<?php $freq = array('+7 day'  => $this->lang->line('application_weekly'),
					  '+14 day' => $this->lang->line('application_every_other_week'),
					  '+1 month' => $this->lang->line('application_monthly'),
					  '+3 month' => $this->lang->line('application_quarterly'),
					  '+6 month' => $this->lang->line('application_semi_annually'),
					  '+1 year' => $this->lang->line('application_annually')); 
						echo $freq[$subscription->frequency];
					  ?>
					</li>
				<li><span><?=$this->lang->line('application_next_payment');?>:</span> <a class="label <?php 
				if($subscription->status == "Active" && $subscription->next_payment > date('Y-m-d')){
					echo 'label-success ';} 
				if($subscription->next_payment <= date('Y-m-d') && ($subscription->end_date > $subscription->next_payment || $subscription->end_date == "") && $subscription->status != "Inactive"){ 
					echo 'label-important tt" title="'.$this->lang->line('application_new_invoice_needed'); 
				} ?>"><?php $unix = human_to_unix($subscription->next_payment.' 00:00'); 
				if($subscription->end_date >= $subscription->next_payment || $subscription->end_date == ""){ 
					echo date($core_settings->date_format, $unix); 
				}else{ echo "-";} ?>
				</a>
				</li>
				<span class="visible-xs"></span>
			</ul>
			<ul class="details col-xs-12 col-sm-6">
				<?php if(isset($subscription->company_id->id)){ ?>		
				<li><span><?=$this->lang->line('application_company');?>:</span> <a href="<?=base_url()?>clients/view/<?=$subscription->company_id->id;?>" class="label label-info"><?=$subscription->company_id->name;?></a></li>	
				<li><span><?=$this->lang->line('application_street');?>:</span> <?=$subscription->company_id->address;?></li>
				<li><span><?=$this->lang->line('application_city');?>:</span> <?=$subscription->company_id->zipcode;?> <?=$subscription->company_id->city;?></li>
				<li><span><?=$this->lang->line('application_website');?>:</span> <?=$subscription->company_id->website;?>
				</li>
				<?php }else{ ?>
					<li><?=$this->lang->line('application_no_client_assigned');?></li>
				<?php } ?>
			</ul>
			<br clear="all">
		</div>
	</div>
</div>
<div style="float: right;margin-bottom:10px;"><strong><?=$this->lang->line('application_currency');?> : 	<?php echo $subscription->currency; ?></strong></div>
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="table-head"><?=$this->lang->line('application_subscription_items');?> <span class="pull-right"><a href="<?=base_url()?>subscriptions/item/<?=$subscription->id;?>" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_add_item');?></a></span>
			</div>
			<div class="table-div min-height-200">
				<table id="items" class="table" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
				<thead>
					<th width="4%"><?=$this->lang->line('application_action');?></th>
					<th><?=$this->lang->line('application_name');?></th>
					<!-- TVA-->
					<?php if($subscription->company_id->tva == 0){?>
					<th class="hidden-xs" width="12%"><?=$this->lang->line('application_tva');?></th>
					<?php } else {?>
					<th></th>
					<?php } ?>
					<th class="hidden-xs"><?=$this->lang->line('application_description');?></th>
					<th class="hidden-xs" width="8%"><?=$this->lang->line('application_hrs_qty');?></th>
					<th class="hidden-xs" width="12%"><?=$this->lang->line('application_unit_price');?></th>
					<th width="12%"><?=$this->lang->line('application_sub_total');?></th>
				</thead>
				<?php $i = 0; $sum = 0; $tauxTVA =0; $sumTVA= 0; ?>
				<?php foreach ($items as $value):?>
					<tr id="<?=$value->id;?>" >
					<td class="option" width="8%" style="text-align: left;">
					
						<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="right" data-content="<a class='btn btn-danger po-delete' href='<?=base_url()?>subscriptions/item_delete/<?=$value->id;?>/<?=$subscription->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
						
						
						<a href="<?=base_url()?>subscriptions/item_update/<?=$subscription->subscription_has_items[$i]->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
					</td>
					<td><?php echo $subscription->subscription_has_items[$i]->name;?></td>
					<!-- TVA-->
					<?php if($subscription->company_id->tva == 0){ ?>
					<td class="hidden-xs">
					<?php  $tauxTVA = ($subscription->subscription_has_items[$i]->amount * $subscription->subscription_has_items[$i]->value * $subscription->subscription_has_items[$i]->tva)/100 + $tauxTVA; 
						   echo $subscription->subscription_has_items[$i]->tva."%";?></td>
					<?php } else {?>
					<td></td>
					<?php } ?>
					<td class="hidden-xs"><?=$subscription->subscription_has_items[$i]->description;?></td>
					<td class="hidden-xs" align="center">
					<?=$subscription->subscription_has_items[$i]->amount;?></td>
					<td class="hidden-xs">
					<?=display_money($subscription->subscription_has_items[$i]->value,"",$chiffre);?></td>
					<td>
					<?=display_money($subscription->subscription_has_items[$i]->amount*$subscription->subscription_has_items[$i]->value,"",$chiffre);?></td>
					</tr>
					<?php $sum = $sum+$subscription->subscription_has_items[$i]->amount*$subscription->subscription_has_items[$i]->value; $i++;?>
					<?php endforeach;
					if($items == NULL){ echo "<tr><td colspan='6'>".$this->lang->line('application_no_items_yet')."</td></tr>";}
					if(substr($subscription->discount, -1) == "%"){ $discount = sprintf("%01.2f", round(($sum/100)*substr($subscription->discount, 0, -1), 2)); }
					else{$discount = $subscription->discount;}
					$sum = $sum-$discount;

					/*if($subscription->second_tax != ""){
					  $second_tax_value = $subscription->second_tax;
					}else{
					  $second_tax_value = $core_settings->second_tax;
					}
					$tax = sprintf("%01.2f", round(($sum/100)*$tax_value, 2));
					$second_tax = sprintf("%01.2f", round(($sum/100)*$second_tax_value, 2));
					$sum = sprintf("%01.2f", round($sum+$tax+$second_tax, 2));*/
					?>
					
					<?php if ($subscription->discount != 0): ?>
					<tr>
						<td colspan="5" align="right"><?=$this->lang->line('application_discount');?></td>
						<td>- <?=display_money($subscription->discount,"",$chiffre);?></td>
					</tr>	
					<?php endif ?>
					
					<!--
					<?php if ($subscription->company_id->tva == 0){ ?>
						<?php if ($tax_value != "0"){ ?>
						<tr>
							<td colspan="5" align="right"><?=$this->lang->line('application_tax');?> (<?= $tax_value?>%)</td>
							<td><?=display_money($tax);?></td>
						</tr>
						<?php } 
					} ?>
					<?php if ($second_tax != "0"){ ?>
					<tr>
						<td colspan="5" align="right"><?=$this->lang->line('application_second_tax');?> (<?= $second_tax_value?>%)</td>
						<td><?=display_money($second_tax);?></td>
					</tr>
					<?php } ?>-->
					
					<tr class="active">
						<td colspan="6" align="right">
						<?php if($subscription->company_id->tva == 0){ 
							echo($this->lang->line('application_total_ht'));
						} else {
							echo($this->lang->line('application_total'));
						}?>
						</td>
						<td><?=display_money($sum,"",$chiffre);?></td>
					</tr>
					<?php  if($subscription->company_id->tva == 0){  ?>
						<tr class="active">
							<td colspan="6" align="right"><?=$this->lang->line('application_tax');?></td>
							<td><?php echo (display_money($tauxTVA,"",$chiffre));?></td>
						</tr>
					<?php } ?>
					<?php if($subscription->company_id->tva == 0){ 
							$sumTVA = $sum + $tauxTVA ; ?>
						<tr class="active">
							<td colspan="6" align="right"><?=$this->lang->line('application_total_ttc');?></td>
							<td><?=display_money($sumTVA,"",$chiffre);?></td>
						</tr>
					<?php } ?>
						
				</table>
			</div>
		</div>
		<?php if($core_settings->paypal == "1" && $sum != "0.00" && $subscription->subscribed == "0"){ ?><br/>	
		<form action="https://www.paypal.com/cgi-bin/webscr" id="paypal_subscribe" target="_blank" method="post">
		  <input type="hidden" name="cmd" value="_xclick-subscriptions">
		  <input type="hidden" name="business" value="<?=$core_settings->paypal_account;?>">
		  <input type="hidden" name="item_name" value="<?=$this->lang->line('application_subscription');?> #<?=$subscription->reference;?>">
		  <input type="hidden" name="item_number" value="<?=$subscription->reference;?>">
		  <input type="hidden" name="image_url" value="<?=base_url()?><?=$core_settings->invoice_logo;?>">
		  <input type="hidden" name="no_shipping" value="1">
		  <input type="hidden" name="return" value="<?=base_url()?>csubscriptions/view/<?=$subscription->id;?>">
		  <input type="hidden" name="cancel_return" value="<?=base_url()?>csubscriptions/view/<?=$subscription->id;?>"> 
		  <input type="hidden" name="currency_code" value="<?= $core_settings->paypal_currency;?>">
		  <input type="hidden" name="rm" value="2">
		  <input type="hidden" name="a3" value="<?=$sum;?>">
		  <input type="hidden" name="p3" value="<?=$p3;?>">
		  <input type="hidden" name="t3" value="<?=$t3;?>">
		  <input type="hidden" name="src" value="1">
		  <input type="hidden" name="sra" value="1">
		  <?php if($subscription->end_date != "") { ?><input type="hidden" name="srt" value="<?=$run_time;?>"><?php } ?>
		  <input type="hidden" name="no_note" value="1">
		  <input type="hidden" name="invoice" value="<?=$subscription->reference;?>">
		  <input type="hidden" name="usr_manage" value="1">
		  <input type="hidden" name="notify_url" value="<?=base_url()?>paypalipn" /> 
		  <input type="hidden" name="custom" value="subscription-<?=$sum;?>">
		</form>
		<?php } ?>	
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="table-head"><?=$this->lang->line('application_subscription');?> <?=$this->lang->line('application_invoices');?>
			</div>
			<div class="table-div">
				<table class="data table" id="invoices" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
				<thead>
					<th class="hidden-xs" width="70px"><?=$this->lang->line('application_invoice_id');?></th>
					<th><?=$this->lang->line('application_subject');?></th>
					<th class="hidden-xs"><?=$this->lang->line('application_client');?></th>
					<th><?=$this->lang->line('application_issue_date');?></th>
					<th><?=$this->lang->line('application_creation_date');?></th>
					<th><?=$this->lang->line('application_status');?></th>
					<th><?=$this->lang->line('application_action');?></th>
				</thead>
				<?php  foreach ($factures as $value):?>
				<tr id="<?=$value->id;?>" >
				<td class="hidden-xs"><span class="label label-info"><?=$value->estimate_num;?></td>
				<td class="hidden-xs"><span class="label label-info"><?=$value->subject;?></td>
				
				<td class="hidden-xs"><span class="label label-info"><?php if(!isset($value->company_id->name)){echo $this->lang->line('application_no_client_assigned'); }else{ echo $value->company_id->name; }?></span></td>
				
				<td><span><?php $unix = human_to_unix($value->issue_date.' 00:00'); echo date($core_settings->date_format, $unix);?></span></td>
				<td><span><?php $unix = human_to_unix($value->creation_date.' 00:00'); echo date($core_settings->date_format, $unix);?></span></td>
				<td> <a class="label 
				<?php 
				if($value->status == "Paid")
				{ 
					$unix = human_to_unix($value->paid_date.' 00:00');
					echo 'label-success tt" title="'.$this->lang->line('application_Paid').' le '.date($core_settings->date_format, $unix);
				}elseif($value->status == "Open")
				{ 
					$unix = human_to_unix($value->issue_date.' 00:00');
					echo 'label-warning tt" title="'.$this->lang->line('application_Open').' le '.date($core_settings->date_format, $unix);
				}elseif($value->status == "Avoir")
				{ 
					$unix = human_to_unix($value->avoir_date.' 00:00');
					echo 'label-warning tt" title="'.$this->lang->line('application_avoir').' le '.date($core_settings->date_format, $unix);
				} 
				else {
					echo 'label-warning tt" title="'.$value->status;
				}?>">
					<?php if ($this->lang->line('application_'.$value->status) == false){ 
						echo $value->status;
					} else {
						echo $this->lang->line('application_'.$value->status);
					 } ?>
					</a>
				</td>
				<td class="option " width="10%">
					<a href="<?=base_url()?>invoices/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
				 </td>
				</tr>
				<?php endforeach;?>
				</table>
			</div>
	 	</div>
	</div>