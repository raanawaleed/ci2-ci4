<link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/settings-cnss.css"/>
<div id="row">
    <div class="col-md-3">
        <div class="list-group">
            <?php foreach ($submenu as $name => $value):
                $badge = "";
                $active = "";
                if ($value == "settings/updates" && $update_count) {
                    $badge = '<span class="badge badge-success">' . $update_count . '</span>';
                }
                if ($value == "settings/paiecnss") {
                    $active = 'active';
                } ?>
                <a class="list-group-item <?= $active; ?>" id="<?php $val_id = explode("/", $value);
                if (!is_numeric(end($val_id))) {
                    echo end($val_id);
                } else {
                    $num = count($val_id) - 2;
                    echo $val_id[$num];
                } ?>" href="<?= site_url($value); ?>"><?= $badge ?> <?= $name ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-9">
        <div class="row">
            <div class="span12 marginbottom20">
                <div class="table-head">
                    <?= $this->lang->line('application_fonction') ?>
                    <span class="pull-right">
						<a href="<?= base_url() ?>settings/add_fonction" data-toggle="mainmodal"
                           class="btn btn-success"><?= $this->lang->line('application-add'); ?></a>
					</span>
                </div>
                <div class="subcont">
                    <table class="data-no-search table dataTable no-footer" cellspacing="0" cellpadding="0" role="grid"
                           id="sample_1">
                        <thead>
                        <tr>
                            <th>Libelle</th>
                            <th class="hidden-480">Description</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <?php foreach ($outils as $value): ?>
                            <tr id="<?= $value->id; ?>">
                                <td class="hidden-xs">
                                    <?php if (isset($value->name)) {
                                        echo $value->name;
                                    } else {
                                        echo "-";
                                    } ?>
                                </td>
                                <td class="hidden-xs">
                                    <?php if (isset($value->description)) {
                                        echo $value->description;
                                    } else {
                                        echo "-";
                                    } ?>
                                </td>
                                <td class="option action">
                                    <button type="button" class="btn-option delete po" data-toggle="popover"
                                            data-placement="left"
                                            data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?= base_url() ?>settings/delete_fonction/<?= $value->id; ?>'><?= $this->lang->line('application_yes_im_sure'); ?></a> <button class='btn po-close'><?= $this->lang->line('application_no'); ?></button> <input type='hidden' name='td-id' class='id' value='<?= $value->id; ?>'>"
                                            data-original-title="<b><?= $this->lang->line('application_really_delete'); ?></b>">
                                        <i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <br clear="all">
                </div>
            </div>
        </div>
    </div>
    <?php
    $attributes = array('class' => '', 'id' => '_clients', 'autocomplete' => 'off');
    echo form_open_multipart($form_action, $attributes);
    ?>
    <?php if (isset($view)) { ?>
        <input id="view" type="hidden" name="view" value="true"/>
    <?php } ?>
    <div class="col-md-9">
        <div class="col-sm-12  col-md-12 main">
            <div class="row">
                <div class="table-head">
                    <img src="https://image.flaticon.com/icons/svg/180/180012.svg" width="25px">
                    <?= $this->lang->line('application_param_paie'); ?>
                    <span class="pull-right">
                        <input type="submit" name="send" class="btn btn-primary" value="<?= $this->lang->line('application_save'); ?>"/>
	                </span>
                </div>
                <div class="table-div" style="padding: 20px;">
                    <table class="table" cellspacing="0" cellpadding="0" id="setting-cnss">
                        <?php foreach ($item as $value): ?>
                            <?php echo("<input type='hidden' name='idparam' id='idparam' class='form-control' step='0.01' value='$value->id'>"); ?>
                            <tr>
                                <td><?= $this->lang->line('application_taux_cnss'); ?></td>
                                <td><?php echo("<input type='number' name='taux_cnss' id='taux_cnss' class='form-control' step='0.01' value='$value->taux_cnss'>"); ?></td>
                                <td>&nbsp;%</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_prime_marie'); ?></td>
                                <td><?php echo("<input type='number' name='prime_marie' id='prime_marie' class='form-control' value='$value->prime_marie'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_prime_zero_enfant'); ?></td>
                                <td><?php echo("<input type='number' name='prime_zero_enfant' id='prime_zero_enfant' class='form-control' value='$value->prime_zero_enfant'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_prime_un_enfant'); ?></td>
                                <td><?php echo("<input type='number' name='prime_un_enfant' id='prime_un_enfant' class='form-control' value='$value->prime_un_enfant'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_prime_deux_enfant'); ?></td>
                                <td><?php echo("<input type='number' name='prime_deux_enfant' id='prime_deux_enfant' class='form-control' value='$value->prime_deux_enfant'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_prime_troix_enfant'); ?></td>
                                <td><?php echo("<input type='number' name='prime_troix_enfant' id='prime_troix_enfant' class='form-control' value='$value->prime_troix_enfant'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_prime_4_enfant'); ?></td>
                                <td><?php echo("<input type='number' name='prime_quatre_plus_enfant' id='prime_quatre_plus_enfant' class='form-control' value='$value->prime_quatre_plus_enfant'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_revenue_1'); ?></td>
                                <td><?php echo("<input type='number' name='revenu_annuel1' id='revenu_annuel1' class='form-control'	value='$value->revenu_annuel1'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_revenue_2'); ?></td>
                                <td><?php echo("<input type='number' name='revenu_annuel2' id='revenu_annuel2' class='form-control'	value='$value->revenu_annuel2'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_revenue_3'); ?></td>
                                <td><?php echo("<input type='number' name='revenu_annuel3' id='revenu_annuel3' class='form-control' value='$value->revenu_annuel3'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_revenue_4'); ?></td>
                                <td><?php echo("<input type='number' name='revenu_annuel4' id='revenu_annuel4' class='form-control'	value='$value->revenu_annuel4'>	"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_revenue_5'); ?></td>
                                <td><?php echo("<input type='number' name='revenu_annuel5' id='revenu_annuel5' class='form-control' value='$value->revenu_annuel5'>"); ?></td>
                                <td> TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_impot1'); ?></td>
                                <td><?php echo("<input type='number' step='0.01' name='impot1' id='impot1' class='form-control'	value='$value->impot1'>"); ?></td>
                                <td>&nbsp;%</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_impot2'); ?></td>
                                <td><?php echo("<input type='number' step='0.01' name='impot2' id='impot2' class='form-control'	value='$value->impot2'>	"); ?></td>
                                <td> %</td>

                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_impot3'); ?></td>
                                <td><?php echo("<input type='number' step='0.01' name='impot3' id='impot3' class='form-control'	value='$value->impot3'>	"); ?></td>
                                <td>&nbsp;%</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_impot4'); ?></td>
                                <td><?php echo("<input type='number' step='0.01'  name='impot4' id='impot4' class='form-control'value='$value->impot4'>	"); ?></td>
                                <td>&nbsp;%</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_taux_coti_1'); ?></td>
                                <td><?php echo("<input type='number' step='0.01' name='taux1' id='taux1' class='form-control'value='$value->taux1'>"); ?></td>
                                <td>&nbsp;%</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_taux_coti_2'); ?></td>
                                <td><?php echo("<input type='number' step='0.01' name='taux2' id='taux2' class='form-control'value='$value->taux2'>"); ?></td>
                                <td>&nbsp;%</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_droit_nombre_j_moins'); ?></td>
                                <td><?php echo("<input type='number' name='droit_nombre_jour_conge' id='droit_nombre_jour_conge' class='form-control' value='$value->droit_nombre_jour_conge'>"); ?></td>
                                <td>&nbsp;Jours</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_nombre_moins'); ?></td>
                                <td><?php echo("<input type='number' name='nombre_moins' id='nombre_moins' class='form-control' value='$value->nombre_moins'>"); ?></td>
                                <td>&nbsp;Mois</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_enfant_handicape'); ?></td>
                                <td><?php echo("<input type='number' name='enfant_handicape' id='enfant_handicape' class='form-control' value='$value->enfant_handicape'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_enfant_boursier_25'); ?></td>
                                <td><?php echo("<input type='number' name='enfant_boursier_25' id='enfant_boursier_25' class='form-control' value='$value->enfant_boursier_25'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                            <tr>
                                <td><?= $this->lang->line('application_parents_a_charges'); ?></td>
                                <td><?php echo("<input type='number' name='parents_a_cahrges' id='parents_a_cahrges' class='form-control' value='$value->parents_a_cahrges'>"); ?></td>
                                <td>&nbsp;TND</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <!---------begin parametre rg---------------------->
                    <div class="col-md-12">
                        <div class="table-head" style="margin-bottom: 30px;">Paramétrage des documents RH</div>
                                <div class="form-setting">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-xs-12">
                                            <span class="text-setting">Afficher le logo de la société dans les fiches de paie</span>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-xs-12">
                                            <label for="logo-fiche-paie-yes">oui</label>
                                            <input type="radio" class="form-group" id="logo-fiche-paie-yes" name="logo_fiche_paie" value="1" <?php if($value->logo_fiche_paie==1){?>checked<?php } ?>>
                                            <label for="logo-fiche-paie-no">non</label>
                                            <input type="radio" class="form-group" id="logo-fiche-paie-no" name="logo_fiche_paie" value="0" <?php if($value->logo_fiche_paie==0){?>checked<?php } ?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-setting">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-xs-12">
                                            <span class="text-setting">Afficher le logo de la société dans les virements des salaires</span>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-xs-12">
                                            <label for="logo-vir-sal-yes">oui</label>
                                            <input type="radio" class="form-group" id="logo-vir-sal-yes" name="logo_virement_salaire" value="1" <?php if($value->logo_virement_salaire==1){?>checked<?php } ?>>
                                            <label for="logo-vir-sal-no">non</label>
                                            <input type="radio" class="form-group" id="logo-vir-sal-no" name="logo_virement_salaire" value="0" <?php if($value->logo_virement_salaire==0){?>checked<?php } ?>>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-setting">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-xs-12">
                                            <span class="text-setting">Afficher le logo de la société dans le journal de paie</span>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-xs-12">
                                            <label for="logo-journal-paie-yes">oui</label>
                                            <input type="radio" class="form-group" id="logo-journal-paie-yes" name="logo_journal_paie" value="1" <?php if($value->logo_journal_paie==1){?>checked<?php } ?>>
                                            <label for="logo-journal-paie-no">non</label>
                                            <input type="radio" class="form-group" id="logo-journal-paie-no" name="logo_journal_paie" value="0" <?php if($value->logo_journal_paie==0){?>checked<?php } ?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-setting">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-xs-12">
                                            <span class="text-setting">Afficher le logo de la société dans les documents administratifs</span>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-xs-12">
                                            <label for="logo-doc-adminis-yes">oui</label>
                                            <input type="radio" class="form-group" id="logo-doc-adminis-yes" name="logo_doc_adminis" value="1" <?php if($value->logo_doc_adminis==1){?>checked<?php } ?>>
                                            <label for="logo-doc-adminis-no">non</label>
                                            <input type="radio" class="form-group" id="logo-doc-adminis-no" name="logo_doc_adminis" value="0" <?php if($value->logo_doc_adminis==0){?>checked<?php } ?>>
                                        </div>
                                    </div>
                                </div>

                    </div>
                    <!---------end parametre rg---------------------->


                    <!---------begin Motif (Référentiel à mettre dans Paramètres / RH & paie "Motif d'absence" : Congé payé - maladie - formation)---------------------->
                    <div class="col-md-12">
                        <?php $this->load->view('blueline/settings/referentielObjets', $refTab['motif_absence'])?>
                    </div>
                    <div class="col-md-12">
                           <?php $this->load->view('blueline/settings/referentielObjets', $refTab['statut_conges'])?>
                    </div>
                    <!---------end Motif---------------------->
                    
                    <?php echo form_close(); ?>
                    <br clear="all">
                </div>
                <link href="<?=base_url()?>assets/blueline/css/setting-rh.css" rel="stylesheet">

            </div>
        </div>
    </div>
</div>