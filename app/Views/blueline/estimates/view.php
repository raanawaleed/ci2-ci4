<div class="row">
	<div class="col-xs-12 col-sm-12">
		<!-- Editer -->
		<a href="<?=base_url()?>estimates/update/<?=$estimate->id;?>/view" class="btn btn-primary" data-toggle="mainmodal"><i class="fa fa-edit visible-xs" title="Modifier"></i><span class="hidden-xs"><?=$this->lang->line('application_edit_estimate');?></span>
		</a>
		<!-- PDF -->
		<a type="button" class="btn btn-primary" href="<?=base_url()?>estimates/preview/<?=$estimate->id;?>" target="_blank">
			<i class="fa fa-file-pdf-o"></i> DEVIS
		</a>
		<a type="button" class="btn btn-primary" href="<?=base_url()?>estimates/previewe/<?=$estimate->id;?>" target="_blank">
			<i class="fa fa-file-pdf-o"></i> ATT-DV
		</a>
			<a type="button" class="btn btn-primary" href="<?=base_url()?>estimates/previewb/<?=$estimate->id;?>" target="_blank">
			<i class="fa fa-file-pdf-o"></i> ATT-PRJ
		</a>
        <a href="<?=base_url()?>estimates" class="btn btn-warning right"><?=$this->lang->line('application_devis_list');?></a>
		
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="table-head"><?=$this->lang->line('application_estimate_details');?></div>
		<div class="subcont">
			<ul class="details col-xs-12 col-sm-6">
				<li><span><?=$this->lang->line('application_estimate_id');?>:</span> <?=$estimate->estimate_num;?></li>
				<li><span><?=$this->lang->line('application_subject');?>:</span> <?php echo $estimate->project_name; ?> cc<?php if (empty($estimate->subject)) {echo "-";} else echo $estimate->subject ?></li>
				<li><span>Etat :</span>
				<?php   
				$change_date = "";
				$change= "";
				
				?>
				<?php get_etat_color(intval($estimate->status)) ?>
				</li>
				<li><span><?=$this->lang->line('application_issue_date');?>:</span> <?php $unix = human_to_unix($estimate->issue_date.' 00:00'); echo date($core_settings->date_format, $unix);?></li>
				<li><span><?=$this->lang->line('application_due_date');?>:</span> <?php $unix = human_to_unix($estimate->due_date.' 00:00'); echo date($core_settings->date_format, $unix);?></li>
				<?php 
				if($estimate->company->timbre_fiscal > 0){ 
				echo "<li><span>".$this->lang->line('application_timbre')." : <span><br>";
				echo "<span style='color:red !important;'>".$this->lang->line('application_exoneration_timbre')."<span></li>";} ?>
				<?php if(isset($estimate->company->vat)){?> 
				<?php if($company->tva == 0){ ?>
				<li><span><?=$this->lang->line('application_vat');?>:</span> <?php if (empty($estimate->company->vat)) {echo "-";} else echo $estimate->company->vat ?></li>

				<?php } ?>
				<?php } ?>
				<?php if(isset($project)){?>
				<li><span><?=$this->lang->line('application_projects');?>:</span> <?php echo $project->project_num.' : '.$project->name; ?></li>
				<?php } ?>
				<span class="visible-xs"></span>
			</ul>
			<ul class="details col-xs-12 col-sm-6">
				<?php  if(isset($company->name)){ ?>	
				<li><span><?=$this->lang->line('application_company');?>:</span> <a href="<?=base_url()?>clients/view/<?=$company->id;?>" class="label label-info">
				<?php echo $company->name ?>
				</a>
				
				</li>
				<li><span>CONTACT PRINCIPAL:</span> 
					<?php if(isset($contact_principale->firstname)){ 
						echo $contact_principale->firstname.' '.$contact_principale->lastname;?> <?php }else{echo "-";} ?></li>
				<li><span><?=$this->lang->line('application_street');?>:</span> <?php echo $estimate->company->address ?></li>
				<li><span><?=$this->lang->line('application_city');?>:</span> <?php echo $estimate->company->city ?></li>

				<?php }else{ ?>
				<li><?=$this->lang->line('application_no_client_assigned');?></li>
				<?php } ?>
				<!-- Guarantee client -->
				<?php if($estimate->company->guarantee == 1){ 
				echo "<li><span>".$this->lang->line('application_guarantee')." : <span><br>";
				echo "<span style='color:red !important;'>".'Client bénéficié de la retenue de garantie'."<span></li>";} ?>
				<!-- tva -->
				<?php if($company->tva == 1){ 
				echo "<li><span>".$this->lang->line('application_TVA')." : <span><br>";
				echo "<span style='color:red !important;'>".$this->lang->line('application_exoneration_tva')."<span></li>";} ?>
			</ul>
			<br clear="all">
		</div>
	</div>
</div>
<div style="float: right;margin-bottom:10px;"><strong><?=$this->lang->line('application_currency');?> : <?php echo $estimate->currency; ?></strong></div>
<div class="row">
	<div class="col-md-12">
		<div class="table-head">
			<?=$this->lang->line('application_items');?>
			<span class=" pull-right">
				<a class="status-btn text-success btn-sm"><?=$this->lang->line("application_up_to_date")?></a>
				<a href="<?=base_url()?>estimates/item/<?=$estimate->id;?>" class="btn btn-md btn-primary" data-toggle="mainmodal"><i class="fa fa fa-plus visible-xs"></i>
				<span class="hidden-xs"><?=$this->lang->line('application_add_item');?></span></a>
				<a href="<?=base_url()?>estimates/itemEmpty/<?=$estimate->id;?>" class="btn btn-md btn-danger" data-toggle="mainmodal"><i class="fa fa fa-plus visible-xs"></i>
				<span class="hidden-xs"><?=$this->lang->line('application_add_item_empty');?></span></a>
			</span>
	</div>
	<div class="table-div min-height-200 table-responsive">
		<table class="table noclick" id="items" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
			<thead>
				<th width="8%"><?=$this->lang->line('application_action');?></th>
				<th width="1%">#</th>
				<th><?=$this->lang->line('application_name');?></th>
				<th class="hidden-xs"><?=$this->lang->line('application_description');?></th>
				<th class="hidden-xs" width="5%"><?=$this->lang->line('application_unit');?></th>
				<th class="hidden-xs RightTd" width="12%"><?=$this->lang->line('application_unit_price_ht');?></th>
				<th class="hidden-xs center" width="8%"><?=$this->lang->line('application_quantity');?></th>
				<th class="hidden-xs RightTd" width="8%"><?=$this->lang->line('application_discount');?></th>
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
			<td class="option" style="text-align:left;" width="4%">
			<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a id='delete' class='btn btn-danger po-delete' href='<?=base_url()?>estimates/item_delete/<?=$value->id;?>/<?=$estimate->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
			<?php if($value->type != NULL ){?>
			<a href="<?=base_url()?>estimates/item_update/<?=$value->id;?>" title="<?=$this->lang->line('application_edit');?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit"></i></a>
			<?php }else{?>
			<a href="<?=base_url()?>estimates/item_update_empty/<?=$value->id;?>" title="<?=$this->lang->line('application_edit');?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit"></i></a>
			<?php } ?>
			<!-- duplicate -->
			<a href="<?=base_url()?>estimates/duplicateItemEmpty/<?=$value->id;?>" title="<?=$this->lang->line('application_dupliacte');?>" class="btn-option"><i class="fa fa-files-o"></i></a>
			
			</td>
            <td class="hidden-xs" width="1%"><?php echo $i+1;?></td>
			<td><?php if(!empty($value->name)){echo $value->name;}else{ echo $value->name; }?></td>
			<td class="hidden-xs"><?=nl2br($value->description);?></td>
			<td class="hidden-xs"><?=$value->unit;?></td>
			<td class="hidden-xs RightTd"><?php echo display_money($value->value,"",$chiffre);?></td>
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
					echo display_money($SousTotal,"",$chiffre);
					?>
			</td>
			</tr>
			<?php $sum = $sum+$estimate->invoice_has_items[$i]->amount*$estimate->invoice_has_items[$i]->value; $i++;?>
			<?php endforeach;?>
			</tbody>
			<tbody>
				<?php
			if(empty($items)){ echo "<tr><td colspan='7'>".$this->lang->line('application_no_items_yet')."</td></tr>";}
			$discount = ($sum/100)*$estimate->discount; 
			$sum = $sum-$discount;
			?>
			<?php if ($discount != 0 && $sum>0){ ?>
			<tr>
			<td></td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
			<td><?=$this->lang->line('application_discount');echo('('.$estimate->discount.'%)');?>  <?php if(isset($discountpercent)){ echo "(".$estimate->discount.")";}?></td>
			<td class="RightTd">-<?=display_money($discount,"",$chiffre);?></td>
			</tr>	
			<?php } ?>
			<?php
			$taxes = array();
			foreach ($items as $item) {
			if ($item->tva != 0) {
				$discount = ($item->amount * $item->value ) - ( $item->amount * $item->value * $item->discount) / 100;
				if(!isset($discountpercent))
				{
					$discount =$discount - ($discount/100)*$estimate->discount; 
				}
				$value = ($discount) * $item->tva / 100;

				if (array_key_exists ($item->tva, $taxes)) {
					$taxes[$item->tva] += $value;
				} else {
					$taxes[$item->tva] = $value;
				}
				$sum = $sum + $value;
				}
			}
			?>
			
			<!-- discount-->
			<?php if(!isset($discountpercent)){ 
				$discountHt = ($total/100)*$estimate->discount; 
				$total = $total-$discountHt; 
				$dis = ($totalTVA/100)*$estimate->discount; 
				$totalTVA = $totalTVA-$dis; 
			}
			?>
			<tr>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
			<td style="white-space:nowrap;"><?=$this->lang->line('application_total_ht');?></td>
			<?php if($sum>0){?>
			<td class="RightTd"><?=number_format($total,$chiffre,'.',' ');?></td>
			<?php } else {?>
			<td><?=display_money("0",'', $core_settings->chiffre);?></td>
			<?php } ?>
			</tr>
			<!-- TVA-->
			<?php if($company->tva == 0){?>
				<?php foreach ($taxes as $tax => $value): ?>
			<tr>
				<td colspan="8"></td><td style="white-space:nowrap;"><?=$this->lang->line('application_tax');?> (<?=$tax?>%)</td><td class="RightTd"><?=number_format($value,$chiffre,'.',' ');?></td>
			</tr>
			<?php endforeach; ?>
			<?php } ?>
		
			<!-- retenue guarantee -->
			<?php if($company->guarantee == 1){ ?>
			<tr>
				<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
				<td style="white-space:nowrap;"><?=$this->lang->line('application_guarantee');?></td>
				<?php if ($company->tva == 1) { ?>
					<td class="RightTd">
					<?php $guarantee = ($total * 10)/100; ?>
					<?=number_format($guarantee,$chiffre,'.',' ');?>
				<?php } else { ?>
					<td class="RightTd">
					<?php $guarantee = ($totalTVA * 10)/100;?>
					<?=number_format($guarantee,$chiffre,'.',' ');?>
				<?php } ?>
			</tr>
			<?php } ?>
			<!-- timbre fiscal-->
			<?php
		/*if($company->timbre_fiscal<1){
			
					/*display_money($estimate->timbre_fiscal,"",$chiffre);
					$totalTVA = $totalTVA+$estimate->timbre_fiscal;
		$total = $total +$estimate->timbre_fiscal;}*/
			?> 
			<tr class="active">
			<td colspan="8"></td><td style="white-space:nowrap;"><?=$this->lang->line('application_total_ttc');?></td>
			<?php if($company->tva == 1){?>
				<?php if($sum>0){?>
				<td class="RightTd"><?=number_format($total - $guarantee,$chiffre,'.',' ');?></td>
				<?php }else{?>
				<td><?=display_money("0",'', $core_settings->chiffre);?></td>
				<?php } }  else {?>
				<?php if($sum>0){?>
				<td class="RightTd"><?=number_format($totalTVA - $guarantee,$chiffre,'.',' ');?></td>
					<?php }else{?>
					<td><?=display_money("0",'', $core_settings->chiffre);?></td>
				<?php } }?>
			</tr>
			</table>
		</div>
	</div>
<!-- note -->
<?php if($estimate->notes){?>
<div class="col-md-12" >
	<div class="table-head"><?=$this->lang->line('application_notes');?></div>
	<div class="subcont" id="notes">
		<ul>
		<?php echo $estimate->notes; ?>
		</ul>	
	</div>
</div>
<?php } ?>
		<!-- ------->
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
				$.get("<?=base_url()?>api/sort_estimates?items=" + order, function(data) {
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

<style>
	.sortable-highlight {
		background: #ecf0f1;
	}
</style>