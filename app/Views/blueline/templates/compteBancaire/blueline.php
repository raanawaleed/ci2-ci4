<?php


$language = $this->input->cookie('language');
if (!isset($language))
{
  $language = $core_settings->language;
}

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
	font-size: 25px !important;
	font-weight:bold !important;
}
</style>

</head>

<body>
	<div style="text-align:center;color:blue;"><b><?php echo $this->lang->line('application_Bank_details'); ?> <br><br><b></div>
	
	<table id="table" cellspacing="0" style="margin-top:2px;margin-bottom:0px;">
		<tr>
			<td><b><?php echo $this->lang->line('application_Code_Bank'); ?><b>
			<td><b><?php echo $this->lang->line('application_Code_agency'); ?> <b>
			<td><b><?php echo $this->lang->line('application_N°_compte'); ?> <b>
			<td><b><?php echo $this->lang->line('application_key_RIB'); ?> <b>
		</tr>
		<tr>
			<td><?php echo substr($compteBancaire->RIB, 0, -18); ?></td>
			<td><?php echo substr($compteBancaire->RIB, 2, -12); ?></td>
			<td><?php echo substr($compteBancaire->RIB, 8, -2); ?></td>
			<td><?php echo substr($compteBancaire->RIB, -2); ?></td>
		</tr>
	</table>
	
	<br><br>
	
	<b><div style="color:blue;"><?php echo $this->lang->line('application_IBAN'); ?> _ Identifiant international de compte bancaire<br><b></div>
	<?php echo $compteBancaire->IBAN  ?> <br><br>
	
	<b><div style="color:blue;"><?php echo $this->lang->line('application_BIC'); ?>_ identifiant international de l'établissement<br><b></div>
	<?php echo $compteBancaire->BIC  ?><br><br>
	
	
	<b><div style="color:blue;"><?php echo $this->lang->line('application_bank_adress'); ?> <br><b></div>
	<?php echo $compteBancaire->nom ?>,
	<?php echo $compteBancaire->adr_banque ?><br><br>
	
	<b><div style="color:blue;"><?php echo $this->lang->line('application_client_account'); ?> <br><b></div>
	<?php echo $company->name?> <br>
	
</body>
</html>
