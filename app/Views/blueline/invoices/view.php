<?php
header("");
 ?>
<div class="row">
	<div class="col-xs-12 col-sm-12">

		<?php if($invoice->status!=$this->config->item("occ_facture_p_paye") 
				&& $invoice->status !=$this->config->item("occ_facture_paye")
				&& $invoice->status !=$this->config->item("occ_facture_avoir")){ ?>
            <a href="<?=base_url()?>invoices/update/<?=$invoice->id;?>/view" class="btn btn-primary" data-toggle="mainmodal"><i class="fa fa-edit visible-xs" title="Modifier"></i><span class="hidden-xs"><?=$this->lang->line('application_edit_invoice');?></span></a>
		<?php }?>
		<!-- add payement if facture not paid-->
		<?php if ($invoice->status!=$this->config->item("occ_facture_paye")
				&& $invoice->status!=$this->config->item("occ_facture_avoir")){ ?>
			<a href="<?=base_url()?>invoices/payment/<?=$invoice->id;?>" class="btn btn-primary" data-toggle="mainmodal"><i class="fa fa-credit-card visible-xs"></i><span class="hidden-xs"><?=$this->lang->line('application_add_payment');?></span></a>
		<?php } ?>
		<a type="button" class="btn btn-primary" href="<?=base_url()?>invoices/preview/<?=$invoice->id;?>" target="_blank">
			<i class="fa fa-file-pdf-o"></i> PDF
		</a>

		<a href="<?=base_url()?>invoices" class="btn btn-warning right"><?=$this->lang->line('application_facture_list');?></a>
		<?php if($invoice->status != $this->config->item("occ_facture_paye") 
			&& isset($invoice->company->name)){ ?><a href="<?=base_url()?>invoices/sendinvoice/<?=$invoice->id;?>" class="btn btn-primary"><i class="fa fa-envelope visible-xs"></i><span class="hidden-xs"><?=$this->lang->line('application_send_invoice_to_client');?></span></a><?php } ?>
	</div>
</div>
<!-- détail de la facture -->
<div class="row">
	<div class="col-md-12">
		<div class="table-head"></div>
			<div class="subcont">
				<ul class="details col-xs-12 col-sm-6">
					<li><span><?=$this->lang->line('application_invoice_id');?> :</span> <span data-toggle="tooltip" title="<?=$invoice->estimate_num;?>"><?=$invoice->estimate_num;?></span></li>
					<li><span><?=$this->lang->line('application_subject');?> :</span><?php if (empty($invoice->subject)) {echo "-";} else echo $invoice->subject ?></li>
					<li class="<?=$invoice->status;?>"><span>Etat :</span>
					<?php get_etat_color(intval($invoice->status)) ?>
					</li>
					<li><span><?=$this->lang->line('application_issue_date');?>:</span> <?php $unix = human_to_unix($invoice->creation_date.' 00:00'); echo date($core_settings->date_format, $unix);?></li>
					<?php if($company->timbre_fiscal > 0){ 
					echo "<li><span>".$this->lang->line('application_timbre')." : <span><br>";
					echo "<span style='color:red !important;'>".$this->lang->line('application_exoneration_timbre')."<span></li>";} ?>
					<!-- Guarantee client -->
					<?php if($company->guarantee == 1){ 
					echo "<li><span>".$this->lang->line('application_guarantee')." : <span><br>";
					echo "<span style='color:red !important;'>".'Client bénéficié du retenue de garantie'."<span></li>";} ?>
					<?php if(isset($invoice->company->vat)){?> 
					<li><span><?=$this->lang->line('application_vat');?>:</span> <?php echo $invoice->company->vat; ?></li>
					<?php } ?>
					<?php if(isset($project)){?>
					<li><span><?=$this->lang->line('application_projects');?>:</span><?php echo $project->project_num.' : '.$project->name ?></li>
					<?php } ?>
					<span class="visible-xs"></span>
				</ul>
				<ul class="details col-xs-12 col-sm-6">
					<?php if(isset($company->name)){ ?>
					<li><span><?=$this->lang->line('application_company');?>:</span> 
						<a href="<?=base_url()?>clients/view/<?=$company->id;?>" class="label label-info">
							<?php echo $company->name ?>
						</a>
					</li>
					<li><span><?=$this->lang->line('application_contact');?>:</span> <?php if(isset($client->firstname)){ ?><?=$client->firstname;?> <?=$client->lastname;?> <?php }else{echo "-";} ?></li>
					<li><span><?=$this->lang->line('application_street');?>:</span>  <?php echo $company->address = empty($company->address) ? "-" : $company->address; ?></li>
					<li><span><?=$this->lang->line('application_city');?>:</span><?php echo $company->zipcode = empty($company->city) ? "-" : $company->city; ?> </li>
					<?php }else{ ?>
					<li><?=$this->lang->line('application_no_client_assigned');?></li>
					<?php } ?>
					<!-- tva -->
					<?php if($company->tva == 1){ 
					echo "<li><span>".$this->lang->line('application_TVA')." : <span><br>";
					echo "<span style='color:red !important;'>".$this->lang->line('application_exoneration_tva')."<span></li>";} ?>
				</ul>
			</ul>
			<br clear="all">
		</div>
	</div>
</div>

<!-- devise de la facture -->
<div style="float: right;margin-bottom:10px;">
	<strong><?=$this->lang->line('application_currency');?> : 	<?php echo $invoice->currency; ?></strong>
</div>

	<!-- tableau des articles -->
	<div class="row">
		<div class="col-md-12">
			<div class="table-head">
				<?=$this->lang->line('application_invoice_items');?> <?php if($invoice->estimate_status != "Invoiced"){ ?>
				<span class=" pull-right">
				<?php if ($invoice->status!=$this->config->item("occ_facture_paye")
					&& $invoice->status !=$this->config->item("occ_facture_p_paye")
					&& $invoice->status !=$this->config->item("occ_facture_avoir")){  ?>
				<a class="status-btn text-success btn-sm"><?=$this->lang->line("application_up_to_date")?></a>
				<a href="<?=base_url()?>invoices/item/<?=$invoice->id;?>" class="btn btn-md btn-primary" data-toggle="mainmodal"><i class="fa fa fa-plus visible-xs"></i><span class="hidden-xs"><?=$this->lang->line('application_add_item');?></span></a>
				<a href="<?=base_url()?>invoices/itemEmpty/<?=$invoice->id;?>" class="btn btn-danger" 	data-toggle="mainmodal"><i class="fa fa fa-plus visible-xs"></i><span class="hidden-xs"><?=$this->lang->line('application_add_item_empty');?></span></a>
				</span><?php } ?>
			</div>
			<?php } ?>
			<div class="table-div min-height-200 table-responsive">
				<table class="table noclick" id="items" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
					<thead>
						<th width="4%"><?=$this->lang->line('application_action');?></th>
						<th width="1%">#</th>
						<th><?=$this->lang->line('application_name');?></th>
						<th class="hidden-xs"><?=$this->lang->line('application_description');?></th>
						<th class="hidden-xs" width="5%"><?=$this->lang->line('application_unit');?></th>
						<th class="hidden-xs RightTd" width="12%"><?=$this->lang->line('application_unit_price_ht');?></th>
						<th class="hidden-xs center" width="8%"><?=$this->lang->line('application_quantity');?></th>
						<th class="hidden-xs RightTd" width="12%"><?=$this->lang->line('application_discount');?></th>
						<!-- TVA-->
						<?php if($company->tva == 0){?>
						<th class="hidden-xs" width="12%"><?=$this->lang->line('application_tva');?></th>
						<?php } else {?>
						<th></th>
						<?php } ?>
						<th class="hidden-xs RightTd" width="12%"><?=$this->lang->line('application_sub_total_HT');?></th>
					</thead>
					<tbody class="sortable">
						<?php $i = 0; $sum = 0;?>
						<?php foreach ($items as $value):?>
						<tr id="<?=$value->id;?>" class="droppable">
							<td class="option" style="text-align:left;" width="8%">
							<?php if($invoice->status != $this->config->item("occ_facture_paye")
							 && $invoice->status!=$this->config->item("occ_facture_p_paye")){ ?>
							<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="right" data-content="<a class='btn btn-danger po-delete' href='<?=base_url()?>invoices/item_delete/<?=$value->id;?>/<?=$invoice->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
						
							<?php } else{ echo '<i class="btn-option fa fa-lock"></i>';}?>
							
							<a href="<?=base_url()?>invoices/item_update_empty/<?=$value->id;?>" title="<?=$this->lang->line('application_edit');?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
							
							<a href="<?=base_url()?>invoices/duplicateItemEmpty/<?=$value->id;?>" title="<?=	$this->lang->line('application_dupliacte');?>" class="btn-option"><i class="fa fa-files-o"></i></a>
							
							</td>
							<td class="hidden-xs" width="1%"><?php echo $i+1;?></td>
							<td><?php if(!empty($value->name)){echo $value->name;}else{ echo $value->name; }?></td>
							<td class="hidden-xs"><?=nl2br($value->description);?></td>
							<td class="hidden-xs"><?=$value->unit;?></td>
							<td class="hidden-xs RightTd"><?php echo numberFormatPrecision($value->value,$chiffre,'.',' '); ?></td> 
							<td class="hidden-xs center"><?=$value->amount;?></td>
							<td class="hidden-xs RightTd"><?php echo $value->discount."%";?></td>
							<!-- TVA-->
							<?php if($company->tva == 0){?>
							<td class="hidden-xs"><?php echo $value->tva."%";?></td>
							<?php } else {?>
							<td></td>
							<?php } ?>
							<td class="hidden-xs RightTd">
							<?php
								$SousTotal = ($value->amount * $value->value ) - ( $value->amount * $value->value * $value->discount) / 100;
								$SousTotalTVA = $SousTotal + ($SousTotal * $value->tva) / 100;
								$totalTVA += $SousTotalTVA;
								$total += $SousTotal;
								echo numberFormatPrecision($SousTotal,$chiffre,'.',' ');?>
							</td>
						</tr>
						<?php $sum = $sum+$value->amount*$value->value;$i++?>	
						<?php endforeach; 
						if(!isset($discountpercent)){ 
							$dis = ($totalTVA/100)*$invoice->discount; 
							$totalTVA = $totalTVA-$dis; 
						}
						?>
					</tbody>
					<tbody>
						<?php
						if(empty($items)){ echo "<tr><td colspan='7'>".$this->lang->line('application_no_items_yet')."</td></tr>";}
						$discount = ($sum/100)*$invoice->discount; 
						$sum = $sum-$discount;
						$sumRest = $sum-$invoice->paid;
						?>
						<?php if ($discount != 0 && $sum>0){ ?>
						<tr>
							<td></td>
							<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
							<td><?=$this->lang->line('application_discount'); echo('('.$invoice->discount.'%)');?>  <?php if(isset($discountpercent)){ echo "(".$invoice->discount.")";}?></td>
							<td class="RightTd">-<?=display_money($discount,"",$chiffre);?></td>
						</tr>	
						<?php } ?>
						<?php
							$taxes = array();
							foreach ($items as $item) {
								if ($item->tva != 0) {
									$discount = ($item->amount * $item->value ) - ( $item->amount * $item->value * $item->discount) / 100;
									if(!isset($discountpercent)){
										$discount =$discount - ($discount/100)*$invoice->discount; 
									}
									$value = ($discount) * $item->tva / 100;
									if (array_key_exists ($item->tva, $taxes)) {
									$taxes[$item->tva] += $value;
									} else {
										$taxes[$item->tva] = $value;
									}
								$sum =$sum + $value;
								}
							}
							
						?>
						<tr>
							<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
							
							<!-- discount-->
							<?php if(!isset($discountpercent)){ 
								$discountHt = ($total/100)*$invoice->discount; 
								$total = $total-$discountHt; 
							}
							?>
							<td style="white-space:nowrap;"><?=$this->lang->line('application_total_ht');?></td>
							<?php if($sum>0){ ?>
							<td class="RightTd"><?=numberFormatPrecision($total,$chiffre,'.',' ');?></td>
							<?php }else{?>
								<td class="RightTd"><?=numberFormatPrecision("0",$chiffre,'.',' ');?></td>
							<?php }?>
						</tr>
						
						<!-- TVA-->
						<?php if($company->tva == 0){ ?>
							<?php foreach ($taxes as $tax => $value): ?>
						<tr>
							<td colspan="8"></td><td style="white-space:nowrap;"><?=$this->lang->line('application_tax');?> (<?=$tax?>%)</td><td class="RightTd"><?=numberFormatPrecision($value,$chiffre,'.',' ');?></td>
						</tr>
						<?php endforeach; ?>
						<?php } ?>
						<!-- retenue guarantee -->
						<?php
						if($company->guarantee == 1){
						?>
						<tr>
							<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
							<td><?=$this->lang->line('application_guarantee');?></td>
							<?php if ($company->tva == 1) { ?>
								<td class="RightTd">
								<?php $guarantee = round(($total * 10)/100,$chiffre); ?>
								<?=numberFormatPrecision($guarantee,$chiffre,'.',' ');?>
							<?php } else { ?>
								<td class="RightTd">
								<?php $guarantee = round(($totalTVA * 10)/100,$chiffre); ?>
								<?=numberFormatPrecision($guarantee,$chiffre,'.',' ');?>
							<?php } ?>
						</tr>
						<?php } ?>
						<!-- Retenue -->
						<?php if($invoice->deduction > 0){ ?>
						<tr>
							<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
							<td style="white-space:nowrap;"><?=$this->lang->line('application_deduction');?>
							(<?=$invoice->deduction?>%) </td>
							<td class="RightTd"><?php 
								if($company->tva == 1){
									echo('-'.numberFormatPrecision($deductionht,$chiffre,'.',' ')); 
									$total = $total - $deductionht;
								}else {
									echo('-'.numberFormatPrecision($deduction,$chiffre,'.',' '));
									$totalTVA = $totalTVA - $deduction;
								}
								?>
							</td>
						</tr>
						<?php } ?>
						<!-- timbre_fiscal -->
						<?php if($company->timbre_fiscal == 0){ ?>
						<tr>
							<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
							<td style="white-space:nowrap;"><?=$this->lang->line('application_timbre');?></td>
							<td class="RightTd"><?=
								display_money($invoice->timbre_fiscal,"",$chiffre);
								$totalTVA = $totalTVA+$invoice->timbre_fiscal;
								$total = $total +$invoice->timbre_fiscal;
								
								?>
							</td>
						</tr>
						<?php } ?>
						
							<tr class="active">
							<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
							<td style="white-space:nowrap;"><?=$this->lang->line('application_total_ttc');?></td>
								<?php if($company->tva == 1){?>
								<?php if($sum>0){?>

								<td class="RightTd" id="ttc"><?=numberFormatPrecision($total - $guarantee,$chiffre,'.',' ');?>
									
								</td>
								<?php }else{?>
								<td><?=display_money("0");?></td>
								<?php } }  else {?>
								<?php if($sum>0){?>
								<td class="RightTd" id="ttc"><?=numberFormatPrecision($totalTVA - $guarantee,$chiffre,'.',' ');?>
									</td>
									<?php }else{?>
								<td class="RightTd"><?=numberFormatPrecision("0",$chiffre,'.',' ');?></td>
								<?php } }?>
							</table>
	
						</div>






						<!-- note -->
						<?php if($invoice->notes){?>
						<div class="row">
							<div class="col-md-12">
							<div class="table-head"><?=$this->lang->line('application_notes');?></div>
							<div class="subcont" id="notes">
							<ul>
								<?php echo $invoice->notes; ?>
							</ul>	
							</div>
							</div>
						</div>
						<?php } ?>
						<!-- ------->
						<?php if (!empty($payments)){
							 ?>
        <div class="row">
	    <div class="col-md-12">
		<div class="table-head"><?=$this->lang->line('application_payments');?> </div>
			<div class="table-div min-height-200">
		<table class="table noclick" id="payments" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
			<thead>
				<th><?=$this->lang->line('application_action');?></th>
				<th><?=$this->lang->line('application_description');?></th>
				<th><?=$this->lang->line('application_type');?></th>
				<th><?=$this->lang->line('application_payment_date');?></th>
				<th colspan="2" class="RightTd"><?=$this->lang->line('application_value');?></th>
			</thead>
			<?php
			$i = 0; 
			foreach ($payments as $value) {  ?>
				<tr class="sec">
					<td class="option" style="text-align:left;" width="8%">
						<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="right" data-content="<a class='btn btn-danger po-delete' href='<?=base_url()?>invoices/payment_delete/<?=$payments[$i]->id;?>/<?=$invoice->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-times"></i></button>
					</td>
					<td><?=$payments[$i]->notes;?></td>
					<td><?php if($this->lang->line('application_'.$payments[$i]->type)== false){
							echo $payments[$i]->type; 
						} else {
							echo $this->lang->line('application_'.$payments[$i]->type); 
						}?></td>
					<td><?php $unix = human_to_unix($payments[$i]->date.' 00:00'); echo date($core_settings->date_format, $unix);?></td>
					
					<td colspan="2" class="RightTd">- <?=display_money($payments[$i]->amount,"",$chiffre);?></td>
				</tr>
				<?php $i++; } ?>
				<tr class="payments">
					<td colspan="5" align="right"><?=$this->lang->line('application_payments_received');?></td>
					<td class="RightTd">- <?=display_money($invoice->paid,"",$chiffre);?></td>
				</tr>
				<tr class="active">
					<td colspan="5" align="right"><?=$this->lang->line('application_total_outstanding');?></td>
					<td class="RightTd ajax-silent"  id="outstanding"><?=display_money($invoice->outstanding,"",$chiffre);?></td>
				</tr>
			</table>
		</div>
 	  </div>
      </div>
     <?php }?>

        </div>
 </div>






 






<?php if ($invoice->status=="Open"): ?>
<script>
	$(document).ready(() => {
		var shadowUpdate = function() {
			var order = ""
			setTimeout(function() {
				$('.sortable').children().each(function() {
					order += $(this).attr('id') + ","
				})
				order = order.substring(0, order.length -1)
				
				$('.status-btn').html('<i class="fa fa-spinner fa-spin"></i> <?=$this->lang->line("application_saving")?>')
				$.get("<?=base_url()?>api/sort_factures?items=" + order, function(data) {
					$('.status-btn').html('<?=$this->lang->line("application_up_to_date")?>')
					location.reload()
				});
			}, 0)
		}

		$('.sortable').sortable({
			placeholder: "sortable-highlight"
		})

		$('.droppable').droppable({
			drop: shadowUpdate
		})
	});
</script>
<?php endif; ?>

<style>
	.sortable-highlight {
		background: #ecf0f1;
	}
</style>
<script>
window.onload = function() {
    if(!window.location.hash) {
        window.location = window.location + '#loaded';
        window.location.reload();
    }
}

</script>


