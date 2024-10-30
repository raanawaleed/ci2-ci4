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

            <button id="btn-mois-trigger" type="button" class="btn btn-info" href="<?=site_url('validation'); ?>" ><?=$this->lang->line('application_Afficher');?></button>

        </div>

    </div>
</div>

<form id="form-validation-saisie"  action="<?=site_url('valider-saisie'); ?>" method="post" name="form-validation-saisie" class="">
    <input type="hidden" name="mois_annee" value="<?=$moisCourant;?>">

    <div class="col-xs-12 col-sm-12">
        <div class="table-head">
            <div class="col-md-10" style="">Total  : <?=$moisCourant;?> </div>
            <div class="col-md-2" style="">
                <div class="col-md-8">
                    <div class="btn-group pull-right MarginTop MarginRight">
                        <input id="btn-submit-form" type="submit" class="btn btn-primary" value="<?=$this->lang->line('application_valider');?>" form="form-validation-saisie" >
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="btn-group pull-right MarginTop MarginRight">
                        <a id="" class="btn btn-info" title="<?=$this->lang->line('application_cancel');?>" href="javascript:window.location.reload(true)">
                            <span class="menu-icon">
                                <i class="fa fa-undo" title="<?=$this->lang->line('application_cancel');?>"></i>
                            </span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <div class="table-div" id="table-div">
            <br class="clear"><br class="clear">
            <table class="dataSorting table" id="projects" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th rowspan="2" ><?=$this->lang->line('application_utilisateur');?></th>
                        <th class="center" colspan="2" scope="col">Planification</th>
                        <th class="center" colspan="2" scope="col">Saisie</th>
                        <th rowspan="2"><?=$this->lang->line('application_export');?></th>
                        <th rowspan="2" ><?=$this->lang->line('application_validationl');?></th>
                    </tr>
                    <tr>
                        <th class="center"><?=$this->lang->line('application_nbre_jours');?></th>
                        <th class="center"><?=$this->lang->line('application_nbre_heures');?></th>
                        <th class="center"><?=$this->lang->line('application_nbre_jours');?></th>
                        <th class="center"><?=$this->lang->line('application_nbre_heures');?></th>

                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($tabPlanification as $row):?>
                        <tr  class="row-ch" >
                            <td class="">
                                <a href="<?php echo site_url('/saisietemps/view/'. $row->id.'/'.$mois.'/'.$annee); ?>" target="_blank"><?=$row->user_name?></a>
                                <input type="hidden" name="user_<?=$row->id;?>">
                            </td>
                            <td class="center">
                                <?=(float)$row->total.$this->lang->line('application_heures_abrv');?>
                            </td>
                            <td class="center">
                                <?=(int)$row->nb_days.$this->lang->line('application_jours_abrv');?><?php echo ((int)$row->nb_days_mod>0)? (int)$row->nb_days_mod.$this->lang->line('application_heures_abrv'):'' ; ?>
                            </td>
                            <td class="center">
                                <?=(float)$row->totalSaisie.$this->lang->line('application_heures_abrv');?>
                            </td>
                            <td class="center">
                                <?=(int)$row->nb_daysSaisie.$this->lang->line('application_jours_abrv');?><?php echo ((float)$row->nb_days_modSaisie>0)? (float)$row->nb_days_modSaisie.$this->lang->line('application_heures_abrv'):'' ; ?>
                            </td>
                            <td class="center">
                                <div class="btn-group">
                                    <a id="" class="btn btn-success btn-small" title="<?=$this->lang->line('application_export');?>" href="<?=site_url('saisietemps/exportValidation/'.$mois.'/'.$annee.'/'.$row->id); ?>">
                                        <span class="menu-icon">
                                            <i class="fa fa-file-excel-o" title="<?=$this->lang->line('application_export');?>"></i>
                                        </span>
                                    </a>
                                </div>
                            </td>
                            <td class="hidden-xs no_sort simplecheckbox" style="width:70px">
                                <input class="checkbox-nolabel" type="checkbox" name="check_<?=$row->id;?>" value=<?=(int)$row->validation?> <?=((int)$row->validation ==1)? "checked":"";?> >
                            </td>

                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <br clear="all">
   </div>
</form>
<style>
    div.dataTables_wrapper div.dataTables_length label {
        font-weight: normal;
        text-align: left;
        white-space: nowrap;
        display: block;
    }
</style>

<script>

    //Changer le mois
    $('#btn-mois-trigger').click( function (e) {
        e.preventDefault();

        var xdate = $('#mois-trigger').val();
        var myarr = xdate.split("-");
        var moisAnnee = myarr[0] + "/" + myarr[1];
        $(this).attr("href", "<?=site_url('validation'); ?>/" + moisAnnee);
        location.href = "<?=site_url('validation/'); ?>/" + moisAnnee ;
    });

    // Avant le submit du formulaire pour sauvegarde en bd, faire une vérification des données
    $("#btn-submit-form").click(function( event ) {
        event.preventDefault();

        $('.row-ch').each(function(){
            if(!$(this).hasClass('changed')){
                //envoyer que ceux qui ont été modifiés
                $(this).find('input:hidden').attr("disabled", true);
            }
        });

        $("#form-validation-saisie").submit();

    });

    jQuery(document).ready(function($) {
        $('body').on('change', 'input', function(){
            $(this).closest('tr').addClass('changed');
        });



    });


</script>
