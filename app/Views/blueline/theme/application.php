<?php 
$act_uri = $this->uri->segment(1, 0);
$lastsec = $this->uri->total_segments();
$act_uri_submenu = $this->uri->segment($lastsec);
if(!$act_uri){ $act_uri = 'dashboard'; }
if(is_numeric($act_uri_submenu)){ 
    $lastsec = $lastsec-1; 
    $act_uri_submenu = $this->uri->segment($lastsec);
}
$message_icon = false;
 ?> 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <META Http-Equiv="Cache-Control" Content="no-cache">
    <META Http-Equiv="Pragma" Content="no-cache">
    <META Http-Equiv="Expires" Content="0">
    <meta name="robots" content="none" />
    <link rel="SHORTCUT ICON" href="<?=base_url()?>assets/blueline/img/favicon.ico"/>
    <title><?=$core_settings->company;?></title> 

       <!-- Bootstrap core CSS and JS -->  
    <script src="<?=base_url()?>assets/blueline/js/plugins/jquery-1.12.4.min.js?ver=<?=$core_settings->version;?>"></script> 
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/accordion-menu.css">
    <script src="<?=base_url()?>assets/blueline/css/accordion-menu.js"></script>
    <script src="<?=base_url()?>assets/blueline/js/message.js"></script>
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/super-panel.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/jquery.dataTables.min.css">
    <script src="<?=base_url()?>assets/blueline/css/super-panel.js"></script>
    <!-- Google Font Loader -->
    <script type="text/javascript">
        WebFontConfig = {
          google: { families: [ 'Open+Sans:400italic,400,300,600,700:latin,latin-ext' ] }
        };
        (function() {
          var wf = document.createElement('script');
          wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
          wf.type = 'text/javascript';
          wf.async = 'true';
          var s = document.getElementsByTagName('script')[0];
          s.parentNode.insertBefore(wf, s);
        })(); 
   </script>
    <!-- Bootstrap core CSS and JS -->  
    <script src="<?=base_url()?>assets/blueline/js/plugins/jquery-1.12.4.min.js?ver=<?=$core_settings->version;?>"></script> 

    <!-- Google Font Loader -->
    <script type="text/javascript">
        WebFontConfig = {
          google: { families: [ 'Open+Sans:400italic,400,300,600,700:latin,latin-ext' ] }
        };
        (function() {
          var wf = document.createElement('script');
          wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
          wf.type = 'text/javascript';
          wf.async = 'true';
          var s = document.getElementsByTagName('script')[0];
          s.parentNode.insertBefore(wf, s);
        })(); 
    </script>
	<!--Handle Page Loading-->
    <style>
		#load{
			width: 100%;
			height: 100%;
			position: fixed;
			z-index: 9999;
			background: url("<?=base_url()?>assets/blueline/images/logo-vision.png") no-repeat center center rgba(44, 62, 77, 0.5);
		}
    </style>
	<script>
		document.onreadystatechange = function () {
			var state = document.readyState
			if (state == 'complete') {
				document.getElementById('interactive');
				$('#load').fadeOut('slow');
			}
		}
	</script>
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/bootstrap.min.css?ver=<?=$core_settings->version;?>" />
    <!-- Plugins -->
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/jquery-ui-1.10.3.custom.min.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/colorpicker.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/jquery-slider.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/summernote.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/chosen.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/datatables.min.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/nprogress.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/jquery-labelauty.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/easy-pie-chart-style.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/fullcalendar.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/reflex.min.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/animate.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/flatpickr.dark.min.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/font-awesome.min.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/ionicons.min.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/bootstrap-editable.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/jquery.ganttView.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/dropzone.min.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/blueline.css?ver=<?=$core_settings->version;?>"/>
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/user.css?ver=<?=$core_settings->version;?>"/>
    <?=get_theme_colors($core_settings);?>
  </head>


<body onload="myFunction()">
	<div id="load"></div>
	<div id="mainwrapper">
		<div class="side">
			<div class="sidebar-bg"></div>
			<?php if($act_uri != 'forgotpass'){	?>
				<div class="sidebar">
					<div class="companyName">		
						<img src="<?php echo base_url()."assets/blueline/images/logo-vision-blanc.png"?>"> 
					</div>
					<ul class="nav nav-sidebar" style="margin-left:0;margin-bottom:50px;">
						<?php  if($act_uri != 'dashboard'){	?>	  
							
						<?php } $existSubmenu= false; ?>
						
					<?php // if we have a submenu  
						foreach ($submenuRight as $key => $val) {
						//to check the submenus for the same modules 
							$module_id = $this->db->query('Select s.id_modules from modules_sous s where s.link= "'.$act_uri.'"')->result()[0]->id_modules;
							if($val->id_modules == $module_id){	
								$existSubmenu = true; ?>	  
								<li id="<?=($val->name);?>" id="<?=strtolower($val->name);?>" class="	<?php if ($act_uri == strtolower($val->link)) {echo "active";}?>">
									<a <?php if ($val->actif ==1) { ?> href="<?=site_url($val->link);?>" <?php } else{ ?> style="background-color: #D8DCE3;" <?php } ?>>
										<span class="menu-icon">
											<i class="fa <?=$val->icon;?>"></i>
										</span>
										<span class="nav-text" >
											<?php echo $this->lang->line('application_'.($val->name));?>
										</span>
									</a> 
								</li>	
					<?php }}  ?>
					<!--  Menus -->
					<?php  if($existSubmenu == false){

					foreach ($menu as $key => $value) {   ?>
						<li id="<?=($value->name);?>" id="<?=strtolower($value->name);?>" class="<?php if ($act_uri == strtolower($value->name) ) {if ($act_uri == "invoices") {}else{echo "active";}}?>" >
							<a <?php if ($value->actif ==1) {?> href="<?=site_url($value->link);?>" <?php } else{ ?> style="background-color: #D8DCE3;" <?php } ?>>
								<span class="menu-icon">
									<i class="fa <?=$value->icon;?>"></i>
								</span>
								<span class="nav-text" >
									<?php echo $this->lang->line('application_'.($value->name));?>
									
									<?php if($value->name == 'ctickets'){ ?>
										<span class="notification-badge" id="countTickets" style="background: #ed5564;"><?=$count_ticket;?></span>
									<?php } 
									 if($value->name == 'messages'){ ?>
										<p class="notification-badge" id="countMessages" style="background: #ed5564;"><?=$count_messages;?></p>
									<?php } ?>
								</span>
							</a> 
						</li>


					<?php	 }}  ?>	
					</ul>	
						<?php foreach ($widgets as $key => $val) {
						if($sticky && $val->link == "quickaccess"){ ?>
							<ul class="nav nav-sidebar quick-access menu-sub hidden-sm hidden-xs">
							<h4><?=$this->lang->line('application_quick_access');?></h4>
						<?php foreach ($sticky as $value): ?>
						<li>
							<a href="<?=base_url()?>projects/view/<?=$value->id;?>">
							  <p class="truncate"><i class="ion-ios-clock <?php if(!empty($value->tracking)){echo "fa-spin";}?>"></i> <?=$value->name;?> </p>
								<div class="progress">
								<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?=$value->progress;?>%;">
								</div>
								</div>
							</a>
						   <div class="submenu">
								<ul>
									<?php if(isset($value->company->name)){ ?>
									<li class="underline"><a href="<?=base_url()?>clients/view/<?=$value->company_id;?>"><b><?=$value->company->name?></b></a></li>
									<?php } ?>
									  <li><a data-toggle="mainmodal" href="<?=base_url()?>projects/update/<?=$value->id;?>"> <?=$this->lang->line('application_edit_project');?></li>
									  <li><a data-toggle="mainmodal" href="<?=base_url()?>projects/tasks/<?=$value->id;?>/add"> <?=$this->lang->line('application_add_task');?></li>
									  <li><a href="<?=base_url()?>projects/tracking/<?=$value->id;?>" id="<?=$value->id;?>"><?php if(empty($value->tracking)){ echo $this->lang->line("application_start_timer");}else{echo $this->lang->line("application_stop_timer");} ?></a></li>
								</ul>
							</div>
						</li>
						<?php endforeach; ?> 
                    </ul>
					<?php } 
					if($user_online && $val->link == "useronline"){ ?>    
					<ul class="nav nav-sidebar user-online menu-sub hidden-sm hidden-xs  enLigne">
						<ul id="menu-accordeon">
						   <li><a href="#"><h4 style="margin-top:0;"><?=$this->lang->line('application_user_online');?></h4></a>
								<ul>
								<?php foreach ($user_online as $value): 
									if($value->last_active+(15 * 60) > time()){ $status = "online";}else{ $status = "away";} ?>
									<li>
										<a href="#" >
										  <p class="truncate"><img class="img-circle" src="<?=get_user_pic($value->userpic, $value->email);?>" width="21px" /> 
											<span class="user_online__indicator user_online__indicator--<?=$status;?>"></span>
											<?php echo $value->firstname." ".$value->lastname;?> 
										  </p>
										</a>
									</li>
									<?php endforeach; ?> 
								</ul>
								</li>
							</ul>
							<style>
							#menu-accordeon {
							  padding:0;
							  margin:0;
							  list-style:none;
							  text-align: center;
								  margin: 0 15px 0 -5px;
								background-color: #2c3e4d;
							}
							#menu-accordeon ul {
							  padding:0;
							  margin:0;
							  list-style:none;
							  text-align: center;

							}
							#menu-accordeon li li {
							   max-height:0;
							   overflow: hidden;
							   transition: all .8s;
							   border-radius:0;
							   box-shadow: none;
							   border:none;
							   margin:0
							}
							#menu-accordeon a {
							  display:block;
							  text-decoration: none;
							  color: #fff;
							  padding: 8px 0;
							  font-size:1.2em;
								  text-align: left;
							}
							#menu-accordeon ul li a, #menu-accordeon li:hover li a {
							  font-size:1em
							}
							#menu-accordeon li:hover{
								background-color: #2c3e4d;
								/*padding: 0 24px;*/
							}
							#menu-accordeon li:hover li {
							  max-height: 15em;

							}
							</style>

						</ul> 
						<?php  }} } ?> 
					</div>
				</div>     
			</div>     
		</div>
		<div class="content-area" onclick="">
			<div class="row mainnavbar">
				<div class="topbar__left noselect" >
					<i class="ion-ios-keypad topbar__icon fc-dropdown--trigger hidden"></i>
						<div class="fc-dropdown shortcut-menu grid">
								<div class="grid__col-6 shortcut--item"><i class="ion-ios-paper-outline shortcut--icon"></i> <?=$this->lang->line('application_create_invoice');?></div>
								<div class="grid__col-6 shortcut--item"><i class="ion-ios-pricetags shortcut--icon"></i> <?=$this->lang->line('application_create_ticket');?></div>
								<div class="grid__col-6 shortcut--item"><i class="ion-ios-email shortcut--icon"></i> <?=$this->lang->line('application_write_messages');?></div>
								</div>
								<div>
								<i class="ion-earth topbar__icon fc-dropdown--trigger"><?php if($notification_count > 0){ ?><span class="topbar__icon_alert"></span><?php } ?></i>
								</div>
								<div class="fc-dropdown notification-center">
									<div class="notification-center__header">
										<a href="#" class="active"><?=$this->lang->line('application_alerts');?> (<?=$notification_count;?>)</a>
									</div>
									<ul class="notificationlist">
										<?php 
										  foreach ($notification_list as $value): ?>            
											   <li class="">
													<p class="truncate"><?=$value;?></p>  
											   </li>
										<?php endforeach;?>
										<?php if($notification_count == 0) { ?>
											   <li> <p class="truncate"><?=$this->lang->line('application_no_events_yet');?></p></li>
										<?php } ?>
									</ul>   
								</div>
								<?php if($message_icon){ ?>
								<span class="hidden-xs">
								<a href="<?=site_url("messages");?>" title="<?=$this->lang->line('application_messages');?>">
								<i class="ion-archive topbar__icon"><?php if($messages_new[0]->amount != "0"){?><span class="topbar__icon_alert"></span><?php } ?></i>
							  </a>
						  </span>
						<?php } ?>
					</div>
			<?php  if($act_uri != 'forgotpass'){?>
			<div class="topbar noselect">
			  <?php  $userimage = get_user_pic($this->user->userpic, $this->user->email); ?>
				<span class="inline visible-xs">
					<a href="<?=site_url("agent");?>" data-toggle="mainmodal" title="<?=$this->lang->line('application_profile');?>">
					  <img class="img-circle topbar-userpic" src="<?=$userimage;?>" height="21px"> <i class="ion-chevron-down" style="padding-left: 2px;"></i>
					</a>
				</span>
				<img class="img-circle topbar-userpic hidden-xs" src="<?=$userimage;?>" height="21px">  
				<span class="hidden-xs topbar__name fc-dropdown--trigger">
				  <?php echo character_limiter($this->user->firstname." ".$this->user->lastname, 25);?> <i class="ion-chevron-down" style="padding-left: 2px;"></i>
				</span>
				<div class="fc-dropdown profile-dropdown">
					<ul>
						<li>
							<a href="<?=site_url("agent");?>" data-toggle="mainmodal">
								<span class="icon-wrapper"><i class="ion-gear-a"></i></span> <?=$this->lang->line('application_profile');?>
							</a>
						</li>
						<li class="profile-dropdown__logout">
							<a href="<?=site_url("logout");?>" title="<?=$this->lang->line('application_logout');?>">
								<span class="icon-wrapper"><i class="ion-power pull-right"></i></span> <?=$this->lang->line('application_logout');?> 
							</a>  
						</li>
					</ul>
				</div>
			</div> 
			<?php } ?>
			<!--------------Menu Support---->
			<div class="topbar noselect" id="id01">
				<span class="inline visible-xs">
					  <button type="button" class="btn" data-toggle="modal" data-target="#myModal" style="background-color:#fff;padding: 6px 6px; " > <i class="fa fa-question-circle" aria-hidden="true" style="    padding-right: 9px;font-size: 17px;color:#84878a;"></i></button>
					  <!-- Modal -->
					  <div class="modal fade" id="myModal" role="dialog">
					    <div class="modal-dialog">
					      <!-- Modal content-->
					      <div class="modal-content">
					        <div class="modal-header">
					          <button type="button" class="close" data-dismiss="modal">&times;</button>
					          <h4 class="modal-title">Support</h4>
					        </div>
					       
					    </div>
					</div>
				</div>
				</span>
				<span class="hidden-xs topbar__name fc-dropdown--trigger">
					<i class="fa fa-question-circle" aria-hidden="true" style="    padding-right: 9px;font-size: 17px;"></i>Support<i class="ion-chevron-down" style="padding-left: 2px;"></i>
				</span>
				<div class="fc-dropdown profile-dropdown" style="right:193px;">
					<ul>
						<li>
							<a target="blank" href="https://3click-solutions.com/guide-utilisateur-vision.pdf" data-toggle="" >
								<span class="icon-wrapper"><i class="fa fa-download" aria-hidden="true" style="    font-size: 11px; padding-right:10px;"></i></span>Télécharger la documentation

							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>   
		<?=$yield?>
	</div>
    <!-- Notify -->
    <?php if($this->session->flashdata('message')) { $exp = explode(':', $this->session->flashdata('message'))?>
        <div class="notify <?=$exp[0]?>"><?=$exp[1]?></div>
    <?php } ?>
    <!-- Modal -->
    <div class="modal fade" id="mainModal" data-easein="flipXIn" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="mainModalLabel" aria-hidden="true"></div>

    

    <!-- Bootstrap core JavaScript -->
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/bootstrap.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery-ui-1.10.3.custom.min.js?ver=<?=$core_settings->version;?>"></script>
    <!-- Plugins -->
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/bootstrap-colorpicker.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.knob.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/summernote.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/chosen.jquery.min.js?ver=<?=$core_settings->version;?>"></script> 
    
	
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/datatables.min.js?ver=<?=$core_settings->version;?>"></script> 
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.nanoscroller.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jqBootstrapValidation.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/nprogress.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery-labelauty.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/validator.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/timer.jquery.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.easypiechart.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/velocity.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/velocity.ui.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/moment-with-locales.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/chart.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/countUp.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.inputmask.bundle.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/fullcalendar/fullcalendar.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/fullcalendar/gcal.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/fullcalendar/lang-all.js?ver=<?=$core_settings->version;?>"></script> 
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.ganttView.js?ver=<?=$core_settings->version;?>"></script>   
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/dropzone.js?ver=<?=$core_settings->version;?>"></script>    
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/flatpickr.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/bootstrap-editable.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/blazy.min.js?ver=<?=$core_settings->version;?>"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/locales/flatpickr_<?=$current_language?>.js?ver=<?=$core_settings->version;?>"></script>
        <!--<script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.sparkline.min.js?ver=<?=$core_settings->version;?>"></script> -->
        
    <!-- Blueline Js -->  
    <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/blueline.js?ver=<?=$core_settings->version;?>"></script>

 </div> <!-- Mainwrapper end -->

<script type="text/javascript" charset="utf-8">
 function afficheMenu(obj){

	var idMenu     = obj.id;
	var idSousMenu = 'sous' + idMenu;
	var sousMenu   = document.getElementById(idSousMenu);
	for(var i = 1; i <= 4; i++){
		if(document.getElementById('sousmenu' + i) && document.getElementById('sousmenu' + i) != sousMenu){
			document.getElementById('sousmenu' + i).style.display = "none";
			
		}
	}
	if(sousMenu){
		if(sousMenu.style.display == "block"){
			sousMenu.style.display = "none";
		}
		else{
			sousMenu.style.display = "block";
		}
	}
	
}
function flatdatepicker(activeform){

      flatpickr.init.prototype.defaultConfig.prevArrow = "<i class='ion-chevron-left'></i>";
      flatpickr.init.prototype.defaultConfig.nextArrow = "<i class='ion-chevron-right'></i>";
      var required = "required";
      if($(".datepicker").hasClass("not-required")){required = "";}
      var datepicker = flatpickr('.datepicker', {
            dateFormat: 'Y-m-d', 
            timeFormat: '<?=$timeformat;?>',
            time_24hr: <?=$time24hours;?>,
            altInput:true, 
            static:true,
            altFormat:'<?=$dateformat?>',
            altInputClass: 'form-control '+required,
            onChange: function(d){ 
                                    if(activeform && !$(".datepicker").hasClass("not-required")){activeform.validator('validate');}
                                    if($(".datepicker-linked")[0]){ 
                                              datepickerLinked.calendars[0].set("minDate", d.fp_incr(0));
                                            } 
                                }
      });
       var required = "required";
      if($(".datepicker-time").hasClass("not-required")){required = "";}
      var datepicker = flatpickr('.datepicker-time', {
            //dateFormat: 'U', 
            timeFormat: '<?=$timeformat;?>',
            time_24hr: <?=$time24hours;?>,
            altInput:true, 
            static:true,
            altFormat:'<?=$dateformat?> <?=$timeformat;?>',
            onChange: function(d){ 
                                    if(activeform && !$(".datepicker").hasClass("not-required")){activeform.validator('validate');}
                                    if($(".datepicker-linked")[0]){ 
                                              datepickerLinked.calendars[0].set("minDate", d.fp_incr(0));
                                            } 
                                }
      });
      if($(".datepicker-linked").hasClass("not-required")){var required = "";}else{var required = "required";}
      var datepickerLinked = flatpickr('.datepicker-linked', {
            dateFormat: 'Y-m-d', 
            timeFormat: '<?=$timeformat;?>',
            time_24hr: <?=$time24hours;?>,
            altInput:true, 
            altFormat:'<?=$dateformat?>',
            static:true,
            altInputClass: 'form-control '+required,
            onChange: function(d){ 
                                  if(activeform && !$(".datepicker-linked").hasClass("not-required")){activeform.validator('validate');}
                                }
      });
        //set dummyfields to be required
        $(".required").attr('required', 'required');
        
		}
		flatdatepicker();

      $(document).ready(function(){
        sorting_list("<?=base_url();?>");
        $("form").validator();

        $("#menu li a, .submenu li a").removeClass("active");
        if("" == "<?php echo $act_uri_submenu; ?>"){$("#sidebar li a").first().addClass("active");}  
        <?php if($act_uri_submenu != "0"){ ?>$(".submenu li a#<?php echo $act_uri_submenu; ?>").parent().addClass("active");<?php } ?>
        $("#menu li#<?php echo $act_uri; ?>").addClass("active");

        //Datatables

        var dontSort = [];
                $('.data-sorting thead th').each( function () {
                    if ( $(this).hasClass( 'no_sort' )) {
                        dontSort.push( { "bSortable": false } );
                    } else {
                        dontSort.push( null );
                    }
                } );


        $('table.data').dataTable({
          "initComplete": function () {
            var api = this.api();
            api.$('td.add-to-search').click( function () {
                api.search( $(this).data("tdvalue") ).draw();
            } );
        },
          "iDisplayLength": 20,
          stateSave: true,
          "bLengthChange": false,
          "aaSorting": [[ 0, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>",
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": "<?=$this->lang->line('application_no_data_yet');?>",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });
	
	
	
        $('table.data-media').dataTable({
          "iDisplayLength": 15,
          stateSave: true,
          "bLengthChange": false,
          "bFilter": false, 
          "bInfo": false,
          "aaSorting": [[ 0, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>",
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": " ",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });
        $('table.data-no-search').dataTable({
          "iDisplayLength": 8,
          stateSave: true,
          "bLengthChange": false,
          "bFilter": false, 
          "bInfo": false,
          "aaSorting": [[ 1, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>",
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": " ",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          },
          fnDrawCallback: function (settings) {
              $(this).parent().toggle(settings.fnRecordsDisplay() > 0);
              if (settings._iDisplayLength > settings.fnRecordsDisplay()) {
            $(settings.nTableWrapper).find('.dataTables_paginate').hide();
        }

          }

        });
        $('table.data-sorting').dataTable({
          "iDisplayLength": 20,
          "bLengthChange": false,
          "aoColumns": dontSort,
          "aaSorting": [[ 1, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>",
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": "<?=$this->lang->line('application_no_data_yet');?>",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });
        $('table.data-small').dataTable({
          "iDisplayLength": 5,
          "bLengthChange": false,
          "aaSorting": [[ 2, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>", 
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": "<?=$this->lang->line('application_no_data_yet');?>",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });
	
	$('table.data-articles').dataTable({
          "iDisplayLength": 10,
          "bLengthChange": false,
          "aaSorting": [[ 4, 'asc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>", 
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": "<?=$this->lang->line('application_no_data_yet');?>",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });
	$('table.data-devFact').dataTable({
          "iDisplayLength": 10,
          "bLengthChange": false,
          "aaSorting": [[ 1, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>", 
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": "<?=$this->lang->line('application_no_data_yet');?>",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });	

        $('table.data-reports').dataTable({
          "iDisplayLength": 30,
          colReorder: true,
          buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
          ],

          "bLengthChange": false,
          "order": [[ 1, 'desc']],
          "columnDefs": [
                          { "orderable": false, "targets": 0 }
                        ],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>", 
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": "<?=$this->lang->line('application_no_data_yet');?>",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });

      });
      
      
      </script>
<script>
	    function validate() {
        if (document.getElementById('company').checked) {
			$( "div.clientPersistant" ).hide();
			$( "div.nomClient" ).show();
			document.getElementById("nomClient").required = true;
			$( "div.timbre_fiscal" ).show();
			$( "div.tva" ).show();
			$( "div.guarantee" ).show();
			document.getElementById("addproject").className = "btn btn-primary tt addprojectClient";
			$('input[type=submit]').prop('disabled',false);
        } else {
			$( "div.clientPersistant" ).show();
			$( "div.nomClient" ).hide();
			$( "div.timbre_fiscal" ).hide();
			$( "div.tva" ).hide();
			$( "div.guarantee" ).hide();
			document.getElementById("addproject").className = "btn btn-primary tt addproject";
        }
    }
	/*function myFunction(){
		var name = document.getElementById('name'); 
		if( name.value !== 'undefined'){
			//document.getElementById("start").required = true;
		}  else {
			//document.getElementById("start").required = false;
		}
	}    */
	

</script>

<script type="text/javascript" charset="utf-8">

</script>	
<script>
$('table.dataSorting').dataTable({
        "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "Tous"]],
		"aaSorting": [[ 1, 'desc']],
		"language": {
			"lengthMenu": "Affichage _MENU_ elements par page",
			"sSearch":"<?=$this->lang->line('application_search');?>",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>", 
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": "<?=$this->lang->line('application_no_data_yet');?>",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
 });

</script>
<script>


    }
function myFunction( ) {




	var countTicket = document.getElementById( 'countTickets' );
    if (typeof(countTicket) != 'undefined' && countTicket != null){
        var url = "<?=base_url()?>" + ('dashboard/CountItem/');
        $.ajax({
            type: 'POST',
            dataType: "text",
            url: url,
            success: function (response) {
                if (response != "false") {
                    countTicket.textContent = response;
                } else {
                    countTicket.textContent = "0";
                }
            }
        });
    }
	var countMessages = document.getElementById( 'countMessages' );
    if (typeof(countMessages) != 'undefined' && countMessages != null){
        var url = "<?=base_url()?>" + ('dashboard/CountMessage/' );
        $.ajax({
            type: 'POST',
            dataType: "text",
            url: url,
            success: function (response) {
                if(response != "false"){
                    countMessages.textContent = response;
                }else {
                    countMessages.textContent = "0";
                }
            }
        });
    }
}
</script>	
 </body>


</html>