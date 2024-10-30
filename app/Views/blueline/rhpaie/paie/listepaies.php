<!-- boutons d'action -->
<div class="row">
    <div class="col-sm-12 col-md-5">
        <form id="form-creer-paie"  action="<?=site_url() .'/gestionpaie/gestionpaie/';?>" method="post" name="form-creer-paie" class="">

             <div class="col-md-5">
                <!-- Année -->
                <div class="form-group">
                    <label for="currency">Choisir l'année</label>
                    <select name="annee" id="annee" class="chosen-select" >
                        <?php foreach ($selectYears as $year) :?>
                            <option value=<?=$year;?> <?php echo ($year==$annee)? "selected":""; ?>><?=$year;?></option>
                        <?php endforeach;; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-7">
                <!-- Créer la paie -->
                <div class="btn-group pull-right MarginTop MarginRight">
                    <!--<input id="btn-submit-form" type="submit" class="btn btn-warning" value="Récapitulatif de l'année" form="form-creer-paie" >-->
                    <button id="btn-submit-form"  class="btn btn-warning" type="submit" form="form-creer-paie">Récapitulatif de l'année <i class="fa fa-mail-reply"></i>  </button>

                </div>
            </div>
        </form>
    </div>

    <div class="col-md-7">
        <div class="btn-group pull-right MarginTop MarginRight" role="group" aria-label="Basic example">
            <?php if(isset($paie_calcule_avant)) : ?>
                <button type="button" class="btn btn-danger delete po" data-toggle="popover" data-placement="left"
                        data-content="
                                                 <a class='btn btn-danger po-delete ajax-silent'
                                                    href='<?php echo site_url().'/gestionpaie/calculerpaie/'.$annee.'/'.$mois; ?>') ?>
                                                    <?=$this->lang->line('application_yes_im_sure');?>
                                                 </a>
                                                 <button class='btn po-close'><?=$this->lang->line('application_no');?></button>
                                                 "
                        data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"
                >
                    Recalculer la paie ?
                    <i class="fa fa-trash" title="La paie sera écraser et recalculer"></i>
                </button>
            <?php endif; ?>
            <button id="btn-submit-form"  class="btn btn-success" type="submit" form="form-generer-pdf">Générer le PDF de la paie  <i class="fa fa-file-pdf-o"></i>  </button>
            <a class="btn btn-primary" href="<?=site_url().'/'.$backlink;?>"  data-toggle="mainmodal">Créer une nouvelle paie <i class="fa fa-arrow-right"></i> </a>

        </div>
    </div>
</div>
<div class="row">
    <div class="table-head">
        <?php if(isset($paie_calcule_avant)) : ?>
            <div class="col-md-5">
                Paie du <?=$mois.'-'.$annee;?> :
                <span class="alert alert-danger"  style="    padding: 4px !important;">
                    Cette paie a été calculée précédemment.
                </span>
            </div>
        <?php else :?>
            <div class="col-md-7">
                Paie du <?=$mois.'-'.$annee;?>
            </div>
        <?php endif; ?>

        <!-- <div class="col-md-8" style="">
            <div class="btn-group">
                <input id="btn-submit-form" type="submit" class="btn btn-success" value="Générer le PDF de la paie <?php //=$mois.'-'.$annee;?>" form="form-generer-pdf">
            </div>
        </div>  -->
    </div>

    <div class="table-div">


        <form id="form-generer-pdf"  action="<?=site_url().'gestionpaie/genererFdp/'?>" method="post" name="form-generer-pdf" class="">
            <table class="dataSorting table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th width="20px" class=""></th>
                        <th>Salarié</th>
                        <th class="">Salaire Brut</th>
                        <th class="">Salaire Net</th>
                        <th class="">Cotisation CNSS</th>
                        <th class="">IRPP</th>
                        <th class="">CSS</th>
                        <th class="">Paie DU</th>
                    </tr>
                </thead>
            <tbody>
                <?php foreach ($paies as $value): ?>
                    <tr id="<?=$value->id;?>" >
                        <!-- poubelle + pdf -->
                        <td class="option" width="6%">
                            <input type="checkbox" name="fdp[]" value="<?=$value->paie_id;?>" checked data-labelauty="">
                        </td>
                        <td class="text-right"><?=$value->salairie_nomprenom;?></td>
                        <td class="text-right"><?=number_format($value->salaire_brut,3,',','');?></td>
                        <td class="text-right"><?=number_format($value->salaire_net,3,',','');?></td>
                        <td class="text-right"><?=number_format($value->cotisation_cnss,3,',','');?></td>
                        <td class="text-right"><?=number_format($value->impot_revenue,3,',','');?></td>
                        <td class="text-right"><?=number_format($value->css,3,',','');?></td>
                        <td class="text-right"><?php echo dateFR(trim(str_replace("00:00:00", "", $value->Paie_du)));?></td>

                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <input type="hidden" name="mois" value="<?php echo (isset($mois)? $mois:'')?>"/>
            <input type="hidden" name="annee" value="<?php echo (isset($annee)? $annee:'')?>"/>
        </form>

    </div>
</div>