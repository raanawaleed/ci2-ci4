<?php

$language = $this->input->cookie('language');
if (!isset($language))
{
  $language = $core_settings->language;
}
if($avoir->due_date <= date('Y-m-d') && $avoir->status != "Paid"){ 
  $status = "Overdue"; }else{
   $status = $invoice->status;
  }
require('nuts.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xml:lang="en" lang="en">
<head>
  <meta name="Author" content="<?= $core_settings->company?>"/> 
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <style type="text/css">

    @font-face {
    font-family: "<?=$core_settings->pdf_font?>";
    src: url(<?php if( $core_settings->pdf_path == 1 ) {echo base_url(); } ?>assets/blueline/fonts/<?= $core_settings->pdf_font?>-Regular.ttf);
    font-weight: normal;
    }
    @font-face {
    font-family: "<?=$core_settings->pdf_font?>";
    src: url(<?php if( $core_settings->pdf_path == 1 ) {echo base_url(); } ?>assets/blueline/fonts/<?= $core_settings->pdf_font?>-Bold.ttf);
    font-weight: bold;
    }
body{
  color: #61686d;
  font: 12px Helvetica, Arial, Verdana, sans-serif;
  line-height:17px;
  font-weight: normal;
  padding-bottom: 60px;
}
p{
  margin:0px;
  padding:0px;
}
.center{
  text-align: center !important;
}
.right{
  text-align: right !important;
}
.left{
  text-align: left !important;
}
.top-background{
  color:#000000;
  border-bottom:2px solid #11A7DB;
  width:100%; 
  margin:-44px -44px 0px;
  padding:40px 40px 5px;
}
.top-background tr{
	font-size: 10px;
	 padding:40px 40px 5px;

}

.status {
  font-weight: normal;
  text-transform: uppercase;
  color: black;
  font-size: 12px;
  margin:0;
  margin-top: -5px;
  text-align: right;

}

.Accepted {color: #43AC6E; }
.Sent {color: #EAAA10; }
.Invoiced {color: #B361FF; }
.Declined {color: #FC704C; }

.company-logo {
  margin-bottom: 10px;
}

.company-address {
  line-height:11px;
}
.recipient-address {
  line-height:13px;
}
.invoicereference{
  font-size: 22px;
  font-weight: normal;
  margin:10px 0;
}

#table{
  width:100%;
  margin:20px 0px;
}

#table tr.header th{
  font-weight: bold;
  color:#777777;
  font-size: 10px;
  text-transform: uppercase;
  border-bottom:2px solid #DDDDDD;
  padding:0 5px 10px;
}
#table tr td{
  font-weight: lighter;
  color:#444444;
  font-size: 11px;
  border-bottom:1px solid #DDDDDD;
  padding:2px 5px;

}
#table tr td .item-name{
  font-weight: bold;
  color:#444444;
}
#table tr td .description{
  font-weight: normal;
  color:#888888;
  font-size: 10px;
}

.padding{
  padding: 5px 0px;
}
.total-amount {
  padding: 8px 20px 8px 0;
  color: #FFFFFF;
  font-size: 17px;
  font-weight: normal;
  margin: 0;
  text-align: right;
}

.custom-terms {
  padding:20px 2px;
  border-bottom:1px solid #DDDDDD;
  font-size: 12px;
}
.over{
  text-transform: uppercase;
  font-size: 10px;
  font-weight: bold;

}
.under{
  font-size: 16px;
}

.total-heading {
  background: #11A7DB;
  color: #FFFFFF;
  text-align: right;
  padding:10px;

}
.side{
  padding:10px;
  background: #EDF2F4;
}

.footer{
  padding:5px 1px;
  font-size: 9px;
  text-align:center;
}
<?php if(isset($htmlPreview)){ ?>
html{
   background: #3E4042;
}
body{
  padding:40px; width:750px;
  background:#FFFFFF;
  margin:50px auto;
  min-height:800px;
  box-shadow: 0px 0px 5px 0px #000;
}
.top-background {
    margin: -44px -40px 0px;
}
.notification-div{
  position:absolute;
  background:##ED5564;
  margin:0 auto;
  top:10px;
  color:#FFFFFF;
  font-size: 14px;
  font-weight: bold;
  padding:10px;
}

<?php  } ?>
<?php if( $core_settings->pdf_path == 1 && $core_settings->display_logo_facture==1 ) { ?>
	@page { margin-top: 15px;margin-bottom:0 !important; }
<?php }else {  ?>
	@page { margin-top: 80px;margin-bottom:0 !important; }
<?php } ?>
.bold{
	font-size: 12px !important;
	font-weight:bold !important;
}
</style>
</head>
   <?php
	foreach ($vcompanies as $vcompanies){
		   
	}	
	?>
	<body>
	<div class="top-background">
	   <table width="100%" cellspacing="0" >
		<?php if( $core_settings->pdf_path == 1 && $core_settings->display_logo_avoir==1 ) { ?>
	    <tr>
			<td><img src="<?php echo 'files/media/'.$logo; ?>" class="company-logo" width="100px" /></td>
			<td style="width:400px;vertical-align: top;padding-top:25px;">
			 <div style="text-align: right;"><?=$vcompanies->name;?></div>
			 <div style="text-align: right;"><?=$vcompanies->address;?></div>
			 <div style="text-align: right;"><?=$vcompanies->city;?></div>
			 <div style="text-align: right;"><?=$vcompanies->vat;?></div>
			</td>
	  </tr>
	 
	  <tr><td style="border-bottom:1px solid #ccc;"></td><td style="border-bottom:1px solid #ccc;padding-bottom:5px;"></td></tr>

		<?php } ?>
		
		<tr>
            <td colspan="2" class="right"  style="vertical-align:top;padding-top:30px">
			<span id="nameFont"  class="bold"><?=$company->name;?></span>
			</td>
        </tr> 
			<tr>
				<td class="right" colspan="2" style="vertical-align:top"><?=$company->address;?></td>
			</tr>
			<tr>
				<td class="right" colspan="2" style="vertical-align:top"><?=$company->city;?></td>
			</tr>
			<tr>
				<td style="vertical-align:top"></td>
				<td class="right" style="vertical-align:top"></td>
			</tr>
			<tr>
				<td style="vertical-align:top"></td>
				<td class="right" style="vertical-align:top"><?php if($company->vat != ""){?><?=$this->lang->line('application_vat');?>: <?php echo $company->vat; ?><?php } ?></td>
			</tr>
			<tr>
				<td class="padding" style="vertical-align:top">
				<span class="invoicereference"><?=$this->lang->line('application_avoir');?> <?=$avoir->avoir_num;?></span><br/>
				<br>			
				<span class="over"><?php echo $vcompanies->city." Le , ";?><?php $unix = human_to_unix($avoir->issue_date.' 00:00');
				 echo date($core_settings->date_format, $unix);?>
				 </span><br>
				<span class="over"><?php if(isset($avoir->subject)){?>
				<?php echo($this->lang->line('application_Object:')) ?><?=$avoir->subject;?><?php } ?></span> 
				</td>
				<td class="padding" align="right" style="vertical-align:bottom"><strong><?=$this->lang->line('application_currency');?> : <?=$avoir->currency;?></strong></td>
			</tr>
	  </table>
 
	</div>

<?php
	if($countDiscount < 1 ){ 
		$colspan="colspan=1";
		$colspan1="colspan=1";
	}
	else{
		$colspan="colspan=1";
        $colspan1="colspan=2";	 
	}
?>	
		<table id="table" cellspacing="0" style="margin-top:2px;margin-bottom:0px;"> 
		<thead> 
		<tr class="header"> 
		<th width="2%">#</th>
		<th class="left" width="50%"><?=$this->lang->line('application_item');?></th>
		<th class="left" width="10%"><?=$this->lang->line('application_unit');?></th>
		<th class="right" width="4%"><?=$this->lang->line('application_hrs_qty');?></th>
		<th class="center" width="12%"><?=$this->lang->line('application_pu_ht');?></th>
		<?php if($countDiscount > 0 ){ ?>
		<th class="right" width="4%"><?=$this->lang->line('application_discount');?></th>
		<?php } ?>
		<?php if ($company->tva == 0){ ?> 
			<th class="right" width="4%"><?=$this->lang->line('application_tva');?></th>
		<?php } else { ?>
			<th> </th>
		<?php } ?>
		<th  <?php echo $colspan; ?> class="right"  width="25%"><?=$this->lang->line('application_sub_total_HT');?></th>
	    </tr> 
	    </thead> 
	 <tbody> 
		<?php $i = 0; $sum = 0; $row=false; ?>
		<?php foreach ($items as $value):
		$description = preg_replace( "/\r|\n/", "<br>", $value->description);
		$description = str_replace("&lt;br&gt;", "<br>", $description);
		?>
		<tr <?php if($row){?>class="even"<?php } ?>>
			<td width="2%" style="vertical-align: top;">
			<?=$i+1?>
			</td>
			<td width="30%">
			<span class="item-name"><?php if(!empty($value->name)){echo $value->name;}else{ echo $value->name; }?></span><br/>
			<span class="description" style="color:#111"><?=$description;?><span class="item-name">
			</td>
			<td class="left" width="4%" style="vertical-align: top;"><?=$value->unit;?></td>  
			<td width="4%" style="text-align:center;vertical-align: top;"><?php echo(str_replace('.',',',$value->amount));?></td>
			<td class="center" style="vertical-align: top;"><?php echo (number_format($value->value, $chiffre, ',', ' '));?></td>	
			<?php if($countDiscount > 0 ){ ?>
			<td width="4%" class="right" style="vertical-align: top;"><?=$value->discount?>%</td>
				<?php } ?>
			<?php if ($company->tva == 0){ ?> 
				<td width="4%" class="right" style="vertical-align: top;"><?=$value->tva?>%</td>
			<?php } else { ?>
				<td> </td>
			<?php } ?>
			<td <?php echo $colspan; ?> class="right" style="vertical-align: top;">
			<?php
				$SousTotal = ($value->amount * $value->value ) - ( $value->amount * $value->value * $value->discount) / 100;
				$SousTotalTVA = $SousTotal + ($SousTotal * $value->tva) / 100;
				$totalTVA += $SousTotalTVA;
				$total += $SousTotal;
				echo number_format($SousTotal,$chiffre,',',' ');
			?>
			</td>
		</tr>
		<?php $sum = $sum+$value->amount*$value->value; $i++; if($row){$row=false;}else{$row=true;}?>
		
		<?php endforeach;
		if(empty($items)){ echo "<tr><td colspan='6'>".$this->lang->line('application_no_items_yet')."</td></tr>";}
		$discount = ($sum/100)*$avoir->discount;
		$sum = $sum-$discount;
		$presum = $sum;
		$taxes = array();
		foreach ($items as $item) {
			if ($item->tva != 0) {
				$discount = ($item->amount * $item->value ) - ( $item->amount * $item->value * $item->discount) / 100;
				$value = ($discount) * $item->tva / 100;
				
				if (array_key_exists ($item->tva, $taxes)) {
					$taxes[$item->tva] += $value;
				} else {
					$taxes[$item->tva] = $value;
				}
				$sum = $sum + $value;
			}
		}
		$presum = $total;
		?>
		<?php if ($company->tva == 0){ ?> 
		<?php foreach ($taxes as $tax => $value): ?>
			<tr>
			<td class=""></td><td></td><td></td><td></td><td></td><td <?php echo $colspan1; ?> class="right"><?=$this->lang->line('application_tax');?> (+<?=$tax?>%)</td><td class="right"><?=number_format($value,$chiffre,',',' ');?></td>
		</tr>
		<?php endforeach; ?>
		<?php } ?>
		
		<?php if($company->guarantee == 1) { 
					if ($company->tva == 1) {?>
						<tr><td class=""></td><td></td><td></td><td></td><td></td><td <?php echo $colspan1; ?> width="30%" class="right"><?=$this->lang->line('application_guarantee');?>(-10%)</td><td class="right"><?=number_format((($total *10)/100),$chiffre,',',' ');?></td></td>
						</tr> 
					<?php } else { ?>
						<tr><td class=""></td><td></td><td></td><td></td><td></td><td <?php echo $colspan1; ?> width="30%" class="right"><?=$this->lang->line('application_guarantee');?>(-10%)</td><td class="right"><?=number_format((($totalTVA *10)/100),$chiffre,',',' ');?></td>
						</tr> 
					<?php } ?>
		<?php }  ?>
    </tbody> 
    </table> 
        <table width="100%">
			<tr>
				<?php if ($avoir->discount != 0): ?><td class="side right"><span class="over"><?=$this->lang->line('application_discount');?>(<?=$avoir->discount?>%)</span><br/><span class="under">- <?=display_money($discount, $avoir->currency,$chiffre);?></span></td><?php endif ?>
				
				 <?php if($company->tva == 1){ ?>
				  <td class="side right"><span class="over">
				  
				 <?php if($company->tva == 0){ 
					echo ($this->lang->line('application_total_ttc'));
				}else {
					echo ($this->lang->line('application_total_ht'));	
				}?>
				  </span><br/><span class="under"><?=number_format($presum,$chiffre,',',' ')." ".$avoir->currency; ?></span>
				  </td>
				 <?php } ?>  
				  
				 <td class="side right"><span class="over"><?=$this->lang->line('application_total_ttc');?></span><br/><span class="under">
			
				 <?php if($company->guarantee == 1){ ?>
					<?php if ($company->tva == 0) { ?>
						<?=number_format($totalTVA-($totalTVA *10)/100 ,$chiffre,',',' ')." ".$avoir->currency;?></span>
						<?php $pr= array('nb' =>$totalTVA-($totalTVA *10)/100 , 'unit' => $avoir->currency); ?>
						<?php } else { ?>
						<?=number_format($total-($total *10)/100 ,$chiffre,',',' ')." ".$avoir->currency;?></span>
						<?php $pr= array('nb' =>$total-($total*10)/100 , 'unit' => $avoir->currency); ?>
						<?php } ?>
				<?php } else { ?>
					<?php if ($company->tva == 0) { ?>
						<?=number_format($totalTVA,$chiffre,',',' ')." ".$avoir->currency;?></span>
						<?php $pr= array('nb' =>$totalTVA, 'unit' => $avoir->currency); ?>
					<?php }  else { ?>
						<?=number_format($total,$chiffre,',',' ')." ".$avoir->currency;?></span>
						<?php $pr= array('nb' =>$total, 'unit' => $avoir->currency); ?>
					<?php } ?>
				<?php } ?>
						
				  </td>
			</tr> 
        </table>
		<br>
    	 <div>
			<?php
				$obj = new nuts($pr); 
				$text = $obj->convert("fr-FR");
				echo $this->lang->line('application_avoir_letter')."<b>".$text."</b><br>";
			?>
        </div>
		<br>
		<div><?php echo "".$avoir->notes."<br>";?></div><br>
			<script type='text/php'>
				if (isset($pdf)) { 
				  $font = Font_Metrics::get_font('helvetica', 'bold');
				  $size = 7;
				  $color = array(0.6,0.6,0.6); 
				  $y = $pdf->get_height() - 29;
				  $x = $pdf->get_width() - 35 - Font_Metrics::get_text_width('1/1', $font, $size);
				  $pdf->page_text($x, $y, '{PAGE_NUM}/{PAGE_COUNT}', $font, $size, $color);
				} 
			</script>
		</div>
</body>
</html>
