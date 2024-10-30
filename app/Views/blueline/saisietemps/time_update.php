<style>
    .table input{
        width: 60px !important;
        text-align: center;
        font-weight: bold;
    }
    .table th{
        width: 25px !important;
    }

    table, th, td {
        border: 1px solid #ddd;
        font-size: 10px !important;
    }

    table th{
        padding-left: 0px !important;
        padding-right: 0px !important;
        text-align: center;
        vertical-align: middle;
    }
    table td{
        padding-left: 0px !important;
        padding-right: 0px !important;
        text-align: center;
        vertical-align: middle;
    }


    .tab-saisie{
        padding-left: 0px;
    }
    .tab-div{
        background: #FFFFFF;
        padding: 0 15px;
        min-height: 20px !important;
        margin-bottom: 0px !important;
        padding-bottom: 1px !important;
    }
    .table-head-2{
        color: #505458;
        background: #FFFFFF;
        line-height: 46px;
        height: 70px;
        padding: 0 15px;
    }

    .table-head-2 .tab-saisie{
        height: 45px;
    }

    .table-head-2 .tab-saisie .form-group{
        height: 41px !important;
        border-top-width: 1px;
        margin-top: 6px;
    }
    .td-error{
        color: #ff0000;
    }
    #total{
     position: sticky !important; 
     background-color: #D8DCE3 !important;
     border-color:#D8DCE3 !important;    
    } 
 

</style>


<div class="" id="content-saisie" >
    <!-- Choix du mois/année -->
    <div class="row form-inline">
        <!-- Choix du mois/année -->
        <div class="col-xs-12 col-md-<?=isset($planification)? '3':'5';?>">
            <div class="form-group mb-2">
                <label for="collaborater">
                    <div ><?=$this->lang->line('application_ChangerMois');?></div>
                </label>
                <select name="mois-trigger"  id="mois-trigger" class="chosen-select inbox-folder" title="Inbox">
                    <?php foreach($optionsMois as $item):?>
                        <option value="<?=$item['date'];?>" <?=($item['date'] == $moisCourant)? "selected":""?>>
                            <?=$item['date_lib'];?>
                        </option>
                    <?php  endforeach; ?>
                </select>
            </div>
            <?php if(! isset($planification)): ?>
                <button id="btn-mois-trigger" type="button" class="btn btn-info" href="<?=site_url('saisietemps'); ?>" ><?=$this->lang->line('application_affichier');?></button>
            <?php endif; ?>
        </div>
        <!-- Choix de l'utilisateurs -->
        <?php if(isset($planification)): ?>
            <div class="row form-inline">
                <div class="col-xs-12 col-md-5">
                    <div class="form-group mb-2">
                        <label for="collaborater">
                            <div ><?=$this->lang->line('application_ChangerUtilisateur');?></div>
                        </label>
                        <select name="user-trigger"  id="user-trigger" class="chosen-select inbox-folder" title="Inbox">
                            <?php foreach($users as $row):?>
                                <option value="<?=$row->id;?>" <?=($row->id == $utilisateurCourant->id)? "selected":""?>>
                                    <?=$row->firstname .' '. $row->lastname;?>
                                </option>
                            <?php  endforeach; ?>
                        </select>
                    </div>
                    <button id="btn-mois-palnification-trigger" type="button" class="btn btn-info" href="<?=site_url('palnification'); ?>" ><?=$this->lang->line('application_affichier');?></button>
                </div>
            </div>
        <?php endif; ?>
    </div>




    <!-- Total -->
    <div class="row navbar"  id="total">
        <div class="col-xs-12 col-md-12">
            <!-- Entête  -->
            <div class="table-head">
                <div class="col-md-10" style="">
                    <?=(isset($planification))? $this->lang->line('application_msg_export_planification_part1'): $this->lang->line('application_msg_export_saisie_part1');?> 
                    <?php echo $utilisateurCourant->firstname   .' '. $utilisateurCourant->lastname; ?> <?=$this->lang->line('application_msg_export_part2');?> <?=$moisCourant;?> 
                 </div>
                <div class="col-md-2" style="">
                    <div class="col-md-3">
                        <div class="btn-group pull-right MarginTop MarginRight">
                            <input id="btn-submit-form" type="submit" class="btn btn-success" value="<?=$this->lang->line('application_valider');?>" form="form-saisie-temps" <?=($validation_mois ==1)? "disabled":"";?>>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="btn-group pull-right MarginTop MarginRight">
                            <a id="ticket-trigger" class="btn btn-warning" title="<?=$this->lang->line('application_ajouterTicket');?>" <?=($validation_mois ==1)? "disabled":"";?>>
                                <span class="menu-icon">
                                    <i class="fa fa-plus" title="<?=$this->lang->line('application_add');?>"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group pull-right MarginTop MarginRight">
                            <a id="" class="btn btn-info" title="<?=$this->lang->line('application_cancel');?>" href="javascript:window.location.reload(true)" <?=($validation_mois ==1)? "disabled":"";?>>
                                <span class="menu-icon">
                                    <i class="fa fa-undo" title="<?=$this->lang->line('application_cancel');?>"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group pull-right MarginTop MarginRight">
                            <a id="" class="btn btn-primary" title="<?=$this->lang->line('application_export');?>" href="<?=(isset($planification)?site_url('saisietemps/export/0/'.$mois.'/'.$annee.'/'.$utilisateurCourant->id):site_url('saisietemps/export/1/'.$mois.'/'.$annee)); ?>">
                                <span class="menu-icon">
                                    <i class="fa fa-file-excel-o" title="<?=$this->lang->line('application_export');?>"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-12">
            <!-- Tableau des jours du travail  -->
            <div class="tab-div table-responsive "><?php echo "&nbsp"; ?>
                <table class="table table-hover" id="ctickets" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
                    <thead>
                    <?php foreach($joursTravailMois as $jour) :?>
                        <th align="center" class="" style="width:2px; !important;" >
                            <?php echo strtoupper(substr($this->lang->line('application_'.$jour->weekdaylib), 0, 2)); ?>
                            </br>
                            <?php echo $jour->day; ?>
                        </th>
                    <?php endforeach; ?>
                    </thead>
                    <tbody>
                    <?php foreach($joursTravailMois as $jour) :?>
                        <td align="center" class="" style="width:2px; !important;">
                            <input  disabled class="disabled 
                            <?=((isset($jour->nbhours) && $limite_heures !== false)? (($jour->nbhours<$limite_heures)? ' td-error': '') : ''); ?> " 
                                type="time" name="temps-total[<?=$jour->day?>]" value="<?=(isset($jour->nbhours))? $jour->nbhours:'00:00'; ?>"
                                <?=($limite_heures == false)? "":"max='".$limite_heures.":00'" ?> min="00:00">
                        </td>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <!--  Fin Total -->

    <!--  Message d'erreur -->
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div id="message-error">
            </div>
        </div>
    </div>
    <!--  Message d'erreur -->

    <!-- Tab de saisie pour les tickets-->
    <form id="form-saisie-temps"  action="<?=(isset($planification)?site_url('planification/miseAJourTemps'):site_url('saisietemps/miseAJourTemps')); ?>" method="post" name="add_file_point" class="">
        <?php $i=0; ?>
        <input type="hidden" name="mois_annee" value="<?=$moisCourant;?>">
        <input type="hidden" name="utilisateurCourant" value="<?=$utilisateurCourant->id;?>">

        <!--  Tableau Par Ticket -->
        <div id="tickets-saisie-tab">
            <?php foreach(array_reverse($tickets) as $ticket) :?>
                <?php   $this->load->view('blueline/saisietemps/_generer_vue_par_type_ticket', array(
                    'config_type_ticket' => $type_ticket_projet,
                    'ticket'=>$ticket,
                    'i'=>$i));
                                    ?>
            <?php $i++;  endforeach; ?>

            <?php foreach(array_reverse($ticketsParDefaut) as $ticket) :?>
                <?php   $this->load->view('blueline/saisietemps/_generer_vue_par_type_ticket', array(
                    'config_type_ticket' => $type_ticket_defaut,
                    'ticket'=>$ticket,
                    'i'=>$i));
                                    ?>
            <?php $i++;  endforeach; ?>
             <HR>
            <?php if($i>0):?> <hr> <?php endif; ?>
        </div>

        <input type="hidden" id="count" value="<?=count($ticketsParDefaut);?>"?>
        <input type="hidden" id="count_i" value="<?=$i;?>"?>
        <!--   Fin Tableau Par Ticket -->

        <!-- Ajout; Nouveau Tab de saisie pour les tickets  -->
        <div id="new-tabs-tickets">
        </div>
    </form>
</div>


<!--  Tableau template -->
<?php   $this->load->view('blueline/saisietemps/_template_tab_saisie', array(
    'xjoursTravailMois'=>$joursTravailMois,
    'xticketsToadd'=>$ticketsToadd,
    'xindex'=>$i,
    'xmoisCourant'=>$moisCourant
    ));?>


<!-- Modal de confirlation de suppression -->
<div class="modal fade" id="confirm-delete-db" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>
<div class="modal fade" id="confirm-delete-tab" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>
<div id="template-modal" style="display: none">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <?=$this->lang->line('application_saisietemps');?>
            </div>
            <div class="modal-body">
                <?=$this->lang->line('application_confirmationSuppressionTemps');?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <a class="btn btn-danger btn-ok" style="margin-right: 5px;">Ok</a>
            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="zoomModal" tabindex="-1" role="dialog" aria-labelledby="zoomModalLabel" aria-hidden="true" >
    <div class="modal-dialog" role="document">
        <div class="modal-header">
            <h5 class="modal-title zoom-date"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <input type="hidden" name="zoom-name-id" class="zoom-name-id">
                <textarea id="description-text" class="form-control" rows="3" style="margin: 0px; resize: vertical;"></textarea>
            </div>
        </div>
    </div>
</div>


<script>


    //Changer le mois
    $('#btn-mois-trigger').click( function (e) {
        e.preventDefault();

        var xdate = $('#mois-trigger').val();
        var myarr = xdate.split("-");
        var moisAnnee = myarr[0] + "/" + myarr[1];
        $(this).attr("href", "<?=site_url('saisietemps/index'); ?>/" + moisAnnee);
        location.href = "<?=site_url('saisietemps/index'); ?>/" + moisAnnee ;
    });


    //
    $('#btn-mois-palnification-trigger').click( function (e) {
        e.preventDefault();

        var xdate = $('#mois-trigger').val();
        var myarr = xdate.split("-");
        var moisAnnee = myarr[0] + "/" + myarr[1];
        var xuser = $('#user-trigger').val();
        $(this).attr("href", "<?=site_url('saisietemps/planification/'); ?>/" + xuser + "/" + moisAnnee );
        location.href = "<?=site_url('planification'); ?>/" + xuser + "/" + moisAnnee ;
    });


    function messageheader(active) {
        var classes = $(active).attr("class").split(/\s/);
        if(classes[3]){
            $('.message-list-header span').hide();
            $('.message-list-header #'+classes[3]).fadeIn('slow');
        }
    }

    //Ajouter un nouveau tableau pour un ticket
    var nbTab = $('#count_i').val();
x = $('#count_i').val();
    $('#ticket-trigger').click( function (e) {
        e.preventDefault();
        NProgress.start();

        messageheader(this);

        $('.message-list-footer').fadeOut('fast');
      
        if(!document.getElementById('tab-saisi-ticket_'+nbTab))
        {
            
            //Création du nouveau tableau
            bf = document.getElementById('tab-saisi-ticket_'+nbTab- 1);
            
            // find(bf).prev().before(container);
            var container = document.getElementById("new-tabs-tickets");

            var ele = document.createElement("div");
            ele.setAttribute("id","tab-saisi-ticket_"+nbTab);
            ele.setAttribute("class","inner newTab tab-saisi-ticket");
            $(".chosen_select_L").chosen('destroy'); //<-- A mettre avant innerHTML
            ele.innerHTML = $("#template-tab").html();
            container.append(ele);
            var i =$('#count_i').val();
            // var item_height = container.height();
            // container.parent().height(item_height);
          var   first = $('#tickets-saisie-tab .tab-saisi-ticket:first');
          console.log(first);
            
           
               console.log(x, nbTab);
               var nn = document.getElementById('tab-saisi-ticket_'+nbTab);
               var xx = document.getElementById('form-saisie-temps');
               console.log(xx);
               $('#tab-saisi-ticket_'+nbTab).insertBefore(first,null);
            x= nbTab;
           
           
            
            

            //Mise à jour du nom du select
            var xName= "";
            var children = container.getElementsByTagName('select');
            for (i = 0; i < children.length; i++) {
                xName =children[i].name;
                children[i].name = xName.replace('X',nbTab );
            }
            

            //Mise à jour du nom des input
            xName= "";
            var childrenTd = container.getElementsByTagName('input');
            for (i = 0; i < childrenTd.length; i++) {
                xName =childrenTd[i].name;
                childrenTd[i].name = xName.replace('X',nbTab );
            }
            
            //Mise à jour du data-id du modal
            xName= "";
            var tdChildren = container.getElementsByTagName('td');
            for (i = 0; i < tdChildren.length; i++) {
                for (k = 0; k < tdChildren[i].attributes.length; k++) {
                    if(tdChildren[i].attributes[k].name === "data-id"){
                        xName = tdChildren[i].attributes[k].value ;
                        tdChildren[i].attributes[k].value = xName.replace('X',nbTab );
                    }
                }
            }
            

            var childrenA = container.getElementsByTagName('a');
            var xRel = "";
            for (i = 0; i < childrenA.length; i++) {
                xRel =childrenA[i].rel;
                childrenA[i].rel = xRel.replace('X',nbTab );

                /**xId =childrenA[i].id;
                childrenA[i].id = xId.replace('X',nbTab );
                $('#'+childrenA[i].id).attr('data-href', "<?php //echo site_url('/saisietemps/deleteTempsTicket');?>"+"/"+nbTab);
                alert($('#'+childrenA[i].id).attr('data-href'));**/
            }
            //Mise à jour du nom du table
            var childrenT = container.getElementsByTagName('table');
            for (i = 0; i < childrenT.length; i++) {
                childrenT[i].name = "tab-saisie_" + nbTab;
                childrenT[i].id = "tab-saisie_" + nbTab;
            }

            $(".chosen_select_L").chosen();
         
        }
       
        nbTab++;

    });

    //Supprimer un tableau d'un ticket de la bd
    $('#confirm-delete-db').on('show.bs.modal', function(e) {
        $(this).html($('#template-modal').html());
        //tableau existant
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));

    });

    //Supprimer un tableau d'un ticket de la page
    $('#confirm-delete-tab').on('show.bs.modal', function(e) {
        $(this).html($('#template-modal').html());

        //nouveau tableau
        var tab_id = $(e.relatedTarget).attr('rel');
        $(this).find('.btn-ok').attr("data-dismiss","modal");
        $(this).find('.btn-ok').attr("data-id",tab_id);
        $(this).find('.btn-ok').attr("href","#");

    });

    $("#confirm-delete-tab").on("click",".btn-ok",function(){
        var tab_id =  $(this).data('id');
        $('#' + tab_id).remove();
    });

    // Avant le submit du formulaire pour sauvegarde en bd, faire une vérification des données
    $("#btn-submit-form").click(function( event ) {
        event.preventDefault();
        $.ajax({
                type: "post",
                url: "<?php echo site_url("saisietemps/verifierTemps"); ?>",
                data: $("#form-saisie-temps").serialize(),// $("#collaborater_id option:selected").text()},
                dataType: "json",
                cache: false,
                success: function( data ) {
                    if (data.localeCompare('0') !== 0) {
                        var ele = document.createElement("div");
                        ele.className += " alert";
                        ele.className += " alert-danger";

                        if (data == '-1') {
                            ele.innerHTML = "Une erreur a été survenue."
                        } else {
                            ele.innerHTML = data;
                        }
                        var container = document.getElementById("message-error");
                        container.innerHTML= "";
                        container.appendChild(ele);
                    }else{
                        $('.tab-saisi-ticket').each(function(){
                            if(!$(this).hasClass('newTab') && !$(this).hasClass('changed')){    
                                $(this).find('input').attr("disabled", true);
                                $(this).find('select').attr("disabled", true);
                            }
                        });

                        $("#form-saisie-temps").submit();
                    }
                }
              });
    });

    jQuery(document).ready(function($) {
        $(".chosen_select_L").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!"
        });
        $('body').on('change', '.row_ch', function(){
           var rel = $(this).parent().parent().attr('rel');
           $('#tab-saisi-ticket_'+rel).addClass('changed');
               
        });

        $('.tab-saisi-ticket select option:not(:selected)').each(function(){
            $(this).attr('disabled', true);
            $(this).trigger("chosen:updated");
        });

        // $('body').on('dblclick', '.autre-saisie-to-zoom', function(){
        //     alert('kk');
        //     $('#to-zoom').css("display", "block");
        //     $('#zoomModal').show();

        //   });
        
        $('#zoomModal').on('show.bs.modal', function(e) {
            var date = $(e.relatedTarget).data('date-id');
            var ticket_id = $(e.relatedTarget).data('id');
            var jour_id = $(e.relatedTarget).data('jour-id');
            var value_AutreSaisie = $(e.relatedTarget).data('value');
            
            $('#zoomModal').find('.zoom-date').html(date);
            document.getElementsByName('zoom-name-id')[0].value = "["+ticket_id+"][1]["+jour_id+"]";
            var elemID = document.getElementsByName('zoom-name-id')[0].value ;
            $('#description-text').val(document.getElementsByName("nbHeures"+elemID)[0].value) ;

        });


        $('#zoomModal').on('hidden.bs.modal', function(e) {
            var elemID = document.getElementsByName('zoom-name-id')[0].value ;
            document.getElementsByName("nbHeures"+elemID)[0].value = $('#description-text').val() ;
            var elem = document.getElementsByName("nbHeures"+elemID)[0];
            $(elem).trigger("change");
            
            //rénitialiser les données
            $('#description-text').val('') ;
            $(this).data('bs.modal', null);
        });


    });

    function messageheader(active) {
        var classes = $(active).attr("class").split(/\s/);
        if(classes[3]){
            $('.message-list-header span').hide();
            $('.message-list-header #'+classes[3]).fadeIn('slow');
        }
    }
    $('.search-box input').on('focus', function() {
        if($(window).width() <= 1360) {
            cols.hideMessage();
        }
    });

    $(window).bind('scroll', function() {
    var navHeight = 130; // custom nav height
    ($(window).scrollTop() > navHeight) ? 
        $('#total').addClass('navbar-fixed-top') :
        $('#total').removeClass('navbar-fixed-top');
});

</script>