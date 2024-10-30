<?php
$act_uri = $this->uri->segment(1, 0);
$lastsec = $this->uri->total_segments();
$act_uri_submenu = $this->uri->segment($lastsec);
if (!$act_uri) {
    $act_uri = 'dashboard';
}
if (is_numeric($act_uri_submenu)) {
    $lastsec = $lastsec - 1;
    $act_uri_submenu = $this->uri->segment($lastsec);
}
$message_icon = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="notification-demo-style.css" type="text/css">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <META Http-Equiv="Cache-Control" Content="no-cache">
    <META Http-Equiv="Pragma" Content="no-cache">
    <META Http-Equiv="Expires" Content="0">
    <meta name="robots" content="none"/>
    <link rel="SHORTCUT ICON" href="<?= base_url() ?>assets/blueline/img/favicon.ico"/>
    <title><?= $core_settings->company; ?></title>

    <!-- Bootstrap core CSS and JS -->
    <script src="<?= base_url() ?>assets/blueline/js/plugins/jquery-1.12.4.min.js?ver=<?= $core_settings->version; ?>"></script>
    <link rel="stylesheet" href="<?= base_url() ?>assets/blueline/css/accordion-menu.css">
    <script src="<?= base_url() ?>assets/blueline/css/accordion-menu.js"></script>
    <script src="<?= base_url() ?>assets/blueline/js/message.js"></script>
    <link rel="stylesheet" href="<?= base_url() ?>assets/blueline/css/super-panel.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/blueline/css/jquery.dataTables.min.css">
    <script src="<?= base_url() ?>assets/blueline/css/super-panel.js"></script>
    <!-- Google Font Loader -->
    <script type="text/javascript">
        WebFontConfig = {
            google: {families: ['Open+Sans:400italic,400,300,600,700:latin,latin-ext']}
        };
        (function () {
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
    <script src="<?= base_url() ?>assets/blueline/js/plugins/jquery-1.12.4.min.js?ver=<?= $core_settings->version; ?>"></script>

    <!-- Google Font Loader -->
    <script type="text/javascript">
        WebFontConfig = {
            google: {families: ['Open+Sans:400italic,400,300,600,700:latin,latin-ext']}
        };
        (function () {
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
        #load {
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
    <script src="<?= base_url() ?>assets/3cs/js/bs_leftnavi.js"></script>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/3cs/css/vision.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/3cs/css/bs_leftnavi.css">

    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/bootstrap.min.css?ver=<?= $core_settings->version; ?>"/>
    <!-- Plugins -->
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/jquery-ui-1.10.3.custom.min.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/colorpicker.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/jquery-slider.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/summernote.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/chosen.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/datatables.min.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/nprogress.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/jquery-labelauty.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/easy-pie-chart-style.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/fullcalendar.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/reflex.min.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/animate.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/flatpickr.dark.min.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/font-awesome.min.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/ionicons.min.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/bootstrap-editable.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/jquery.ganttView.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/plugins/dropzone.min.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet"
          href="<?= base_url() ?>assets/blueline/css/blueline.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet" href="<?= base_url() ?>assets/blueline/css/user.css?ver=<?= $core_settings->version; ?>"/>
    <?= get_theme_colors($core_settings); ?>
</head>

<body id="window-main-page" onload="myFunction()">
<?php //var_dump($menu);exit; ?>
<div id="load"></div>
<div id="mainwrapper">
    <div class="side">
        <div class="sidebar-bg"></div>
        <?php if ($act_uri != 'forgotpass'){ ?>

        <div class="sidebar">
            <!-- logo vision -->
            <div class="companyName">
                <img src="<?php echo base_url() . "assets/blueline/images/logo-vision-blanc.png" ?>">
            </div>
            <!-- le nouveau menu -->

            <div class="gw-sidebar">
                <div id="gw-sidebar" class="gw-sidebar">
                    <div class="nano-content">
                        <ul class="gw-nav gw-nav-list">
                        <?php 
                            foreach ($menu as $key => $value) {

                                //Vérifier les sous modules -->
                                $s_modules = $this->db->query("SELECT * FROM `modules_sous`  WHERE `id_modules` =" . $value->id . " and id IN (" . implode(',', $accessSubmenu) . ")")->result();
                                if (empty($s_modules)) { ?>
                                    <!-- les modules -->

                                    <li class="init-un-active <?php if (($act_uri == strtolower($value->link)) || (substr(strtolower($value->link), 0, strlen($act_uri) + 1) == $act_uri . '/')) {
                                        echo 'active';
                                    } ?>">
                                        <a href="<?= site_url($value->link); ?>">
											<span class="gw-menu-text">
												<?= $this->lang->line('application_' . ($value->name)); ?>
											<!-- Notification menu -->
										<?php if ($value->name == 'ctickets') { ?>
                                            <span class="notification-badge" id="countTickets"
                                                  style="background: #ed5564;"><?= $count_ticket; ?></span>
                                        <?php }
                                        if ($value->name == 'messages') { ?>
                                            <p class="notification-badge" id="countMessages"
                                               style="background: #ed5564;"><?= $count_messages; ?></p>
                                        <?php } ?>
											<!-- Fin Notification menu -->
											</span>
                                        </a>
                                    </li>
                                    
                                <?php } else { ?>
                                    <!-- possède des sous menu -->

                                    <li class="init-arrow-down"><a href="javascript:void(0)">
											<span class="gw-menu-text">
												<?= $this->lang->line('application_' . ($value->name)); ?>
											</span> <b class="gw-arrow icon-arrow-up8"></b> </a>

                                        <!----- afficher les sous menus -->
                                        
                                        <ul class="gw-submenu">
                                        
                                            <?php foreach ($s_modules as $key => $value) { ?>
                                              
                                                <li <?php if ($act_uri == strtolower($value->link)) {
                                                    echo "class='active'";
                                                } ?>><a href="<?= site_url($value->link); ?>">
                                                        <?= $this->lang->line('application_' . ($value->name)); ?></a>
                                                </li>
                                           
                                                
                                            <?php } ?>
                                           
                                        </ul>
                                      
                                    </li>
                                    
                                    
                                <?php } ?>
                                
                            <?php } 
							$idadmin =$this->user->id ;
				
				if ($idadmin==1) {
							
							
							?>
								

							
                             <li>	<a href="https://vision.bimmapping.com/saisietmp"><i class="fa fa-plane"></i><span>  jours fériés </span></a>
                                                </li>
												<?php } ?>  
                        </ul>
                      
                    </div>
                </div>
               
            </div>

            <!-- widget utilisateur en ligne -->
            <?php foreach ($widgets as $key => $val) {

                if ($user_online && $val->link == "useronline") { ?>

                    <ul class="nav nav-sidebar user-online menu-sub hidden-sm hidden-xs  enLigne">
                        <ul id="menu-accordeon">
                            <li><a href="#"><h4
                                            style="margin-top:0;"><?= $this->lang->line('application_user_online'); ?></h4>
                                </a>
                                <ul>
                                    <?php foreach ($user_online as $value):
                                        if ($value->last_active + (15 * 60) > time()) {
                                            $status = "online";
                                        } else {
                                            $status = "away";
                                        } ?>
                                        <li>
                                            <a href="#">
                                                <p class="truncate"><img class="img-circle"
                                                                         src="<?= get_user_pic($value->userpic, $value->email); ?>"
                                                                         width="21px"/>
                                                    <span class="user_online__indicator user_online__indicator--<?= $status; ?>"></span>
                                                    <?php echo $value->firstname . " " . $value->lastname; ?>
                                                </p>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>


                    </ul>
                <?php }
            }
            } ?>

        </div>
        <!-- fin de la sidebar -->
    </div>
</div>

<div class="content-area">
    <!-- this is the header -->
    <header class="row mainnavbar">
        <div class="topbar__left noselect">
            <i class="ion-ios-keypad topbar__icon fc-dropdown--trigger hidden"></i>
            <div class="fc-dropdown shortcut-menu grid">
                <div class="grid__col-6 shortcut--item"><i
                            class="ion-ios-paper-outline shortcut--icon"></i> <?= $this->lang->line('application_create_invoice'); ?>
                </div>
                <div class="grid__col-6 shortcut--item"><i
                            class="ion-ios-pricetags shortcut--icon"></i> <?= $this->lang->line('application_create_ticket'); ?>
                </div>
                <div class="grid__col-6 shortcut--item"><i
                            class="ion-ios-email shortcut--icon"></i> <?= $this->lang->line('application_write_messages'); ?>
                </div>
            </div>
            <!-- nom de la société -->
            <div class="marg-15">
                <?= $nom_licence[0]->name; ?>
            </div>


        </div>
        <?php if ($act_uri != 'forgotpass') { ?>
            <div class="topbar noselect">
                <?php $userimage = get_user_pic($this->user->userpic, $this->user->email); ?>
                <span class="inline visible-xs">
					<a href="<?= site_url("agent"); ?>" data-toggle="mainmodal"
                       title="<?= $this->lang->line('application_profile'); ?>">
					  <img class="img-circle topbar-userpic" src="<?= $userimage; ?>" height="21px"> <i
                                class="ion-chevron-down" style="padding-left: 2px;"></i>
					</a>
				</span>
                <img class="img-circle topbar-userpic hidden-xs" src="<?= $userimage; ?>" height="21px">
                <span class="hidden-xs topbar__name fc-dropdown--trigger">
				  <?php echo character_limiter($this->user->firstname . " " . $this->user->lastname, 25); ?> <i
                            class="ion-chevron-down" style="padding-left: 2px;"></i>
				</span>
                <div class="fc-dropdown profile-dropdown">
                    <ul>
                        <li>
                            <a href="<?= site_url("agent"); ?>" data-toggle="mainmodal">
                                <span class="icon-wrapper"><i
                                            class="ion-gear-a"></i></span> <?= $this->lang->line('application_profile'); ?>
                            </a>
                        </li>
                        <li class="profile-dropdown__logout">
                            <a href="<?= site_url("logout"); ?>"
                               title="<?= $this->lang->line('application_logout'); ?>">
                                <span class="icon-wrapper"><i
                                            class="ion-power pull-right"></i></span> <?= $this->lang->line('application_logout'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php } ?>
        <!-- help(?) into menu bar  -->
        <div class = "topbar noselect">
            <!-- features for only small devices -->
            <span class="inline visible-xs">

            </span>
            <div class="btn-group" class="inline visible-xs">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button"  style=" margin: 4px; border-radius: 5px ;opacity: 1;">
                    <a  style="text-decoration: none;" target="_blank" href="<?=site_url("support");?>"><span style="color: whitesmoke ; ">Support<span style =" margin-right: -10px ; margin-left: 6px"class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></span></a> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="<?=site_url("support");?>">Guide utilisateur</a></li>
                    <li><a href="<?=site_url("faq");?>">FAQ</a></li>
                </ul>
            </div>
            <?php 
            if($user_online[0]->admin=="1")
                    {
            ?>
                            <div class="btn-group" class="inline visible-xs">
                                 <div id="notification-header">
                                    <button type="button" class="btn btn-warning notification"  type="button" >
                                        <?php
                                            $options = array('conditions' => array('statut=?','162'));
                                            $conges = Conges::find('all',$options); 
                                    
                                         ?>
                                         <a  style="text-decoration: none;"  href="<?=base_url()?>gestionconge/all_attente" ><span style="color: whitesmoke ; ">Demandes de congés<span id="notification-count"><?php echo '  ('.count($conges).')'; ?></span></span></span></a> <span class="caret"></span>
                                     
                                     </button>	
                                </div>
                            </div>
            <?php
            } 
            ?>

    </header>

    <!-- le contenu de la page -->
    <div style="">
        <?= $yield ?>
    </div>

    <!-- footer pied de page -->
    <!-- TODO I will delete margin-left  when i re-orginize the sider bar -->
    <!-- TODO change this code whith inclde   include('../inc/footer.php');  don't miss to adjust the path  -->
    <footer class=" main-footer col-md-12 col-xs-12" style="width: 100% position: fixed; bottom: 0; " ;>

        <div class="container" style="width: 100%;position: fixed; bottom:10px;  color: black;  text-align: center; " >
            <div class="row"  style="opacity: 1;">
                
                <div  style="position: fixed ; right: 0;">
                  
                  <div class="btn-group dropup"  style="margin-right: 10px;" >
                  

                  <button type="button" class="btn btn-primary dropdown-toggle  " data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" >
                  <span class="caret"></span>
                  <span class="sr-only"></span>
                </button>
                <div class="dropdown-menu " class="width: 100%; " style="background: white;  opacity:1;position: absolute; z-index:1; margin-left: -100px; weight:700;" >

                  <div class=" btn-primary col-md-8" style="color: white; text-align: center ;opacity:1;">
                    <h2 class="col-md-8" style=" color: white;text-align: center;weight: 700;"> Assistance </h2>
                  </div>


                      <!---- form -->

                        <div class="scrollable  container" style=" z-index:1; padding-left: 0!important; padding-right: 0!important;">



                  <form class=" col-md-12 well well-md well well-xs well well-lg " style=" z-index:1; margin-bottom: -5px;">

                  <div class="row" >
                    <p style="color: #0099cc; weight: 300; margin-left:-10px; padding: 20px;"> Laissez-nous un message et nous vous recontacterons! </p>

                          <div class="form-group  col-md-11 col-xs-11">
                              <label for="name">
                                Présentez-vous *</label>
                              <input type="text" class="form-control" id="name" placeholder="Nom, e-mail" required="required" />
                          </div>
                          <div class="form-group  col-md-9 col-xs-11">
                              <label for="name">
                                  Nom de téléphone</label>
                              <input type="text" class="form-control" id="name" placeholder="" required="required" />
                          </div>


                      <div class="col-md-12 col-xs-12">
                          <div class="form-group">
                              <label for="name">
                                  Message *</label>
                              <textarea name="message" id="message" class="form-control" rows="12" cols="25" required="required"
                                  placeholder="Message"></textarea>
                          </div>


                  </div>
                  <div class="col-md-12 col-xs-12">
                      <button type="submit" class="btn btn-danger " id="btnContactUs">
                          Envoyer un message</button>
                  </div>
                </div>
                  </form>



  </div>

                      <!-- end form -->

                    </div>

                    </div>


               </div>
              </div>
            </div>
            </div>



    </footer>

</div>
<!-- Notify -->
<?php if ($this->session->flashdata('message')) {
    $exp = explode(':', $this->session->flashdata('message')) ?>
    <div class="notify <?= $exp[0] ?>"><?= $exp[1] ?></div>
<?php } ?>
<!-- Modal -->
<div class="modal fade" id="mainModal" data-easein="flipXIn" tabindex="-1" role="dialog" data-backdrop="static"
     aria-labelledby="mainModalLabel" aria-hidden="true"></div>

<!-- Bootstrap core JavaScript -->
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/bootstrap.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/jquery-ui-1.10.3.custom.min.js?ver=<?= $core_settings->version; ?>"></script>
<!-- Plugins -->
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/bootstrap-colorpicker.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/jquery.knob.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/summernote.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/chosen.jquery.min.js?ver=<?= $core_settings->version; ?>"></script>


<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/datatables.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/jquery.nanoscroller.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/jqBootstrapValidation.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/nprogress.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/jquery-labelauty.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/validator.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/timer.jquery.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/jquery.easypiechart.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/velocity.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/velocity.ui.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/moment-with-locales.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/chart.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/countUp.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/jquery.inputmask.bundle.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/fullcalendar/fullcalendar.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/fullcalendar/gcal.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/fullcalendar/lang-all.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/jquery.ganttView.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/dropzone.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/flatpickr.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/bootstrap-editable.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/plugins/blazy.min.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/locales/flatpickr_<?= $current_language ?>.js?ver=<?= $core_settings->version; ?>"></script>
<!--<script type="text/javascript" src="<?= base_url() ?>assets/blueline/js/plugins/jquery.sparkline.min.js?ver=<?= $core_settings->version; ?>"></script> -->

<!-- Blueline Js -->
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/blueline.js?ver=<?= $core_settings->version; ?>"></script>

</div> <!-- Mainwrapper end -->

<script type="text/javascript" charset="utf-8">
    function afficheMenu(obj) {

        var idMenu = obj.id;
        var idSousMenu = 'sous' + idMenu;
        var sousMenu = document.getElementById(idSousMenu);
        for (var i = 1; i <= 4; i++) {
            if (document.getElementById('sousmenu' + i) && document.getElementById('sousmenu' + i) != sousMenu) {
                document.getElementById('sousmenu' + i).style.display = "none";

            }
        }
        if (sousMenu) {
            if (sousMenu.style.display == "block") {
                sousMenu.style.display = "none";
            } else {
                sousMenu.style.display = "block";
            }
        }

    }

    function flatdatepicker(activeform) {

        flatpickr.init.prototype.defaultConfig.prevArrow = "<i class='ion-chevron-left'></i>";
        flatpickr.init.prototype.defaultConfig.nextArrow = "<i class='ion-chevron-right'></i>";
        var required = "required";
        if (!$('.datepicker').hasClass("required")) {
            required = "";
        }

        var datepicker = flatpickr('.datepicker', {
            dateFormat: 'Y-m-d',
            timeFormat: '<?=$timeformat;?>',
            time_24hr: <?=$time24hours;?>,
            altInput: true,
            static: true,
            altFormat: '<?=$dateformat?>',
            altInputClass: 'form-control ' + required,
            onChange: function (d) {
                if (activeform && !$(".datepicker").hasClass("not-required")) {
                    activeform.validator('validate');
                }
                if ($(".datepicker-linked")[0]) {
                    datepickerLinked.calendars[0].set("minDate", d.fp_incr(0));
                }
            }
        });
        var required = "required";
        if ($(".datepicker-time").hasClass("not-required")) {
            required = "";
        }
        var datepicker = flatpickr('.datepicker-time', {
            //dateFormat: 'U',
            timeFormat: '<?=$timeformat;?>',
            time_24hr: <?=$time24hours;?>,
            altInput: true,
            static: true,
            altFormat: '<?=$dateformat?> <?=$timeformat;?>',
            onChange: function (d) {
                if (activeform && !$(".datepicker").hasClass("not-required")) {
                    activeform.validator('validate');
                }
                if ($(".datepicker-linked")[0]) {
                    datepickerLinked.calendars[0].set("minDate", d.fp_incr(0));
                }
            }
        });
        if ($(".datepicker-linked").hasClass("not-required")) {
            var required = "";
        } else {
            var required = "required";
        }
        var datepickerLinked = flatpickr('.datepicker-linked', {
            dateFormat: 'Y-m-d',
            timeFormat: '<?=$timeformat;?>',
            time_24hr: <?=$time24hours;?>,
            altInput: true,
            altFormat: '<?=$dateformat?>',
            static: true,
            altInputClass: 'form-control ' + required,
            onChange: function (d) {
                if (activeform && !$(".datepicker-linked").hasClass("not-required")) {
                    activeform.validator('validate');
                }
            }
        });
        //set dummyfields to be required
        $(".required").attr('required', 'required');

    }

    flatdatepicker();

    $(document).ready(function () {
        sorting_list("<?=base_url();?>");
        $("form").validator();

        $("#menu li a, .submenu li a").removeClass("active");
        if ("" == "<?php echo $act_uri_submenu; ?>") {
            $("#sidebar li a").first().addClass("active");
        }
        <?php if($act_uri_submenu != "0"){ ?>$(".submenu li a#<?php echo $act_uri_submenu; ?>").parent().addClass("active");<?php } ?>
        $("#menu li#<?php echo $act_uri; ?>").addClass("active");

        //Datatables

        var dontSort = [];
        $('.data-sorting thead th').each(function () {
            if ($(this).hasClass('no_sort')) {
                dontSort.push({"bSortable": false});
            } else {
                dontSort.push(null);
            }
        });


        $('table.data').dataTable({
            "initComplete": function () {
                var api = this.api();
                api.$('td.add-to-search').click(function () {
                    api.search($(this).data("tdvalue")).draw();
                });
            },
            "iDisplayLength": 20,
            stateSave: true,
            "bLengthChange": false,
            "aaSorting": [[0, 'desc']],
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
            "aaSorting": [[0, 'desc']],
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
            "aaSorting": [[1, 'desc']],
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
            "aaSorting": [[1, 'desc']],
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
            "aaSorting": [[2, 'desc']],
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
            "aaSorting": [[4, 'asc']],
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
            "aaSorting": [[1, 'desc']],
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
            "order": [[1, 'desc']],
            "columnDefs": [
                {"orderable": false, "targets": 0}
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
            $("div.clientPersistant").hide();
            $("div.nomClient").show();
            document.getElementById("nomClient").required = true;
            $("div.timbre_fiscal").show();
            $("div.tva").show();
            $("div.guarantee").show();
            document.getElementById("addproject").className = "btn btn-primary tt addprojectClient";
            $('input[type=submit]').prop('disabled', false);
        } else {
            $("div.clientPersistant").show();
            $("div.nomClient").hide();
            $("div.timbre_fiscal").hide();
            $("div.tva").hide();
            $("div.guarantee").hide();
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

<script>
    $('table.dataSorting').dataTable({
        "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "Tous"]],
        "aaSorting": [[1, 'desc']],
        "language": {
            "lengthMenu": "Affichage _MENU_ elements par page",
            "sSearch": "<?=$this->lang->line('application_search');?>",
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


function loadContactForm(){
  $(document).ready(function() {
    $('#contact_form').bootstrapValidator({
        // To use feedback icons, ensure that you use Bootstrap v3.1.0 or later
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {

            email: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your email address'
                    },
                    emailAddress: {
                        message: 'Please supply a valid email address'
                    }
                }
            },
            phone: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your phone number'
                    },
                    phone: {
                        country: 'US',
                        message: 'Please supply a vaild phone number with area code'
                    }
                }
            },
            comment: {
                validators: {
                      stringLength: {
                        min: 10,
                        max: 200,
                        message:'Please enter at least 10 characters and no more than 200'
                    },
                    notEmpty: {
                        message: 'Please supply a description of your project'
                    }
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            $('#success_message').slideDown({ opacity: "show" }, "slow") // Do something ...
                $('#contact_form').data('bootstrapValidator').resetForm();

            // Prevent form submission
            e.preventDefault();

            // Get the form instance
            var $form = $(e.target);

            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

            // Use Ajax to submit form data
            $.post($form.attr('action'), $form.serialize(), function(result) {
                console.log(result);
            }, 'json');
        });
});


}
    function myFunction( ) {

          <?php  if ($act_uri == 'forgotpass') {?>
            $(".forgot-password").hide();
            $(".sidebar-bg").hide();
            $(".main-footer").hide();
            $(".mainnavbar").hide();
            $("body").css("background-image", "url('<?php echo base_url() ?>assets/blueline/images/backgrounds/field.jpg')");


         <?php } ?>


        var countTicket = document.getElementById('countTickets');
        if (typeof (countTicket) != 'undefined' && countTicket != null) {
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
        var countMessages = document.getElementById('countMessages');
        if (typeof (countMessages) != 'undefined' && countMessages != null) {
            var url = "<?=base_url()?>" + ('dashboard/CountMessage/');
            $.ajax({
                type: 'POST',
                dataType: "text",
                url: url,
                success: function (response) {
                    if (response != "false") {
                        countMessages.textContent = response;
                    } else {
                        countMessages.textContent = "0";
                    }
                }
            });
        }
    }

</script>

</body>

</html>
