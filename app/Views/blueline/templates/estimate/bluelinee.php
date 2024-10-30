<?php


$language = $this->input->cookie('language');
if (!isset($language))
{
  $language = $core_settings->language;
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
<?php if( $core_settings->pdf_path == 1 && $core_settings->display_logo_devis==1 ) { ?>
	@page { margin-top: 15px;margin-bottom:0 !important; }
<?php }else{ ?>
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
        <?php if( $core_settings->pdf_path == 1 && $core_settings->display_logo_devis==1 ) { ?>
	  <tr>
	    <td><img src="<?php echo 'files/media/'.$logo;?>" class="company-logo" width="200px" /></td>
	    <td style="vertical-align: top;padding-top:25px;">
	     <div style="text-align: right;"><?=$vcompanies->name;?></div>
	     <div style="text-align: right;"><?=$vcompanies->address;?></div>
	     <div style="text-align: right;"><?=$vcompanies->city;?>

		 
		 </div>
		 <div style="text-align: right;"><?=$vcompanies->vat;?>

		 </div>
	    </td>
	  </tr>
	  <tr><td style="border-bottom:1px solid #ccc;"></td>

	  
	  <td style="border-bottom:1px solid #ccc;padding-bottom:5px;">

	  </td></tr>

		<?php } 

		?>
        <tr>

            <td colspan="2" class="right"  style="vertical-align:top;padding-top:30px">
			<span id="nameFont"  class="bold"><?=$estimate->company->name;?></span>
			</td>

        </tr> 
        <tr>
            <td class="right" colspan="2" style="vertical-align:top"><?=$estimate->company->address;?></td>

        </tr>
        <tr>
            <td class="right" colspan="2" style="vertical-align:top"><?=$estimate->company->city;?></td>
        </tr>

        <tr>
            <td style="vertical-align:top"></td>
            <td class="right" style="vertical-align:top"></td>
        </tr>
		
        <tr>
            <td style="vertical-align:top"></td>

		    <td class="right" style="vertical-align:top"><?php if($estimate->company->vat != ""){?><?=$this->lang->line('application_vat');?>: <?php echo $estimate->company->vat; ?><?php } ?></td>
        </tr>
	
	
        <tr>
          <td class="padding" style="vertical-align:top">
          <span class="invoicereference"> Attachement (<?php echo $estimate->project_ref; ?>) </span><br/>
          <span id="nameFont"  class="bold">   ( Ref: <?=$estimate->estimate_num;?>) : <?=$refP;?></span>
			<br>
          <span class="over" ><?php echo $vcompanies->city." , Le ";?><?php $unix = human_to_unix($estimate->issue_date.' 00:00'); echo date($core_settings->date_format, $unix);?></span><br>
		  <span class="over"><?php echo($this->lang->line('application_Object:'))?> <?php echo $estimate->project_name; ?></span> 
          </td>
		  <td class="padding" align="right" style="vertical-align:bottom"><strong><?=$this->lang->line('application_currency');?> : <?=$estimate->currency;?></strong></td>
        </tr>
  </table>
</div>
<?php
 if($countDiscount < 1 ){ 
 $colspan="colspan=1";
 $colspan1="colspan=2";
 }
 else{
	$colspan="colspan=1";
    $colspan1="colspan=3";	 
 }
 ?>
<table id="table" cellspacing="0" style="margin-top:2px;margin-bottom:0px;"> 
		<thead> 
			<tr class="header"> 
				<th width="2%" >#</th>
				<th class="left" width="70%"><?=$this->lang->line('application_item');?></th>
				<th class="left" width="30%"><?=$this->lang->line('application_unit')?></th>
				<th class="right" width="10%">QUANTITE</th>
					    <!--<th class="center" width="12%">TOTAL RENDEMENT</th>-->

				
			
			
			</tr>
		</thead>
  <tbody> 
  <?php $i = 0; $sum = 0; $row=false; ?>
    <?php foreach ($items as $value):
    $description = preg_replace( "/\r|\n/", "<br>", $value->description);
    $description = str_replace("&lt;br&gt;", "<br>", $description);
    ?>
    <tr <?php if($row){?>class="even"<?php } ?>>
		  <td width="2%" style="vertical-align: top;"><?=$i + 1?></td>
		  <td width="80%">
			<span class="item-namename" ><?php if(!empty($value->name)){echo $value->name;}else{ echo $value->item->name; }?></span><br/>
			<span class="description" style="color:#111"><?=$description;?><span class="item-name">
		  </td>
		
		
		  <td class="left" width="30%" style="vertical-align: top;"><?=$value->unit?></td>
		
		  <td  width="10%" class="right" style="text-align:center;vertical-align: top;"><?php echo(str_replace('.',',',$value->amount));?> </td>
		  

		  
		
	</tr>
    <?php 
		$sum = $sum+$value->amount*$value->value; $i++; if($row){$row=false;}else{$row=true;}
		endforeach;
	 
	
	
    if(empty($items)){ echo "<tr><td colspan='6'>".$this->lang->line('application_no_items_yet')."</td></tr>";}
    $discount = ($sum/100)*$estimate->discount;
	
	$sum = $sum - $discount;
    $presum = $sum;
	$taxes = array();
	$total = $total - $discount;
	
	$totalTVA = $totalTVA - $discount;
	
	
	if($company->tva == 0){
		foreach ($items as $item) {
			if ($item->tva != 0) {
				$discount2 = ($item->amount * $item->value ) - ( $item->amount * $item->value * $item->discount) / 100 - ( $item->amount * $item->value * $estimate->discount) / 100;
				 
				$value = ($discount2) * $item->tva / 100;
				if (array_key_exists ($item->tva, $taxes)) {
					$taxes[$item->tva] += $value;
				} else {
					$taxes[$item->tva] = $value;
				}
				//$sum = $total + $value;
			}
		}
	}  
	$presum = $total;
	
	?>
	
 
		
	
	 
  </tbody> 
 </table> 

 <br>
	
	<div><?php echo "   La somme des heures passées sur ce dossier est ". $estimate->calcul_heure ." Heures" ;?></div><br>
	
  
  <div><?php echo "   La somme des quantités  sur ce dossier est ".$totalg." ". $estimate->unite; ?> </div><br>
	
  <div > 
	  <span style =" 
    margin: 70px" ;> Chargé de projet BIMMAPPING :  <?php echo $estimate->chef_projet; ?></span><br/><br>
	  <span style =" margin: 70px"> Chargé de projet <?=$estimate->company->name;?> : <?php echo $estimate->chef_projet_client; ?> </span>
      </div> <br> <br>

 	
  
		 	

  <centre> <span style =" 
    margin: 100px"  >&nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; Validation <?=$estimate->company->name;?> :  </span></centre>
	<br>
	<div>
	 <?php
		// $obj = new nuts($pr); 
		// $text = $obj->convert("fr-FR");
		// $test=$this->lang->line('application_devis_letter');
		//var_dump($test);
		// echo $test."<b>".$text."</b><br>";
	 ?>
	</div>

    
    <script type='text/php'>
        if (isset($pdf)) { 
          $font = Font_Metrics::get_font('helvetica', 'normal');
          $size = 9;
          $y = $pdf->get_height() - 24;
          $x = $pdf->get_width() - 15 - Font_Metrics::get_text_width('1/1', $font, $size);
          $pdf->page_text($x, $y, '{PAGE_NUM}/{PAGE_COUNT}', $font, $size);
        } 
      </script>
</div>
</body>
</html>
