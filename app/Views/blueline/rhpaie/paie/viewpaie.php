<!-- boutons d'action -->
<div class="row">
    <form id="form-creer-paie"  action="<?=base_url()?>gestionpaie/gestionpaie/" method="post" name="form-creer-paie" class="">
        <div class="col-md-4">
            <div class="col-md-8">
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
            <div class="col-md-4">
                <!-- Créer la paie -->
                <div class="btn-group pull-right MarginTop MarginRight">
                    <input id="btn-submit-form" type="submit" class="btn btn-success" value="Afficher" form="form-creer-paie" >
                </div>
            </div>
        </div>
    </form>

    <div class="col-md-8">
            <!-- Créer la paie -->
            <div class="btn-group pull-right MarginTop MarginRight">
                <a class="btn btn-primary" href="<?=site_url()?><?=$backlink;?>"  data-toggle="mainmodal">Créer la paie <i class="fa fa-arrow-right"></i> </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="table-head">
        <div class="col-md-10" style="">
            Paie de l'année <?=$annee; ?>
        </div>
    </div>
    <div class="table-div">
        <form id="form-generer-pdf"  action="<?=site_url()?>gestionpaie/genererFdp/" method="post" name="form-generer-pdf" class="">
            <table class=" table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th width="20px" class=""></th>
                        <th>Mois de la paie</th>
                        <th class="">Nombre salariés</th>
                        <th class="">Total Brut</th>
                        <th class="">Total Net</th>
                        <th class="">Total CNSS</th>
                        <th class="">Total IRPP</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($paies as $key => $value): ?>
                    <tr id="<?=$key;?>" >
                        <!-- poubelle + pdf -->
                        <td class="option" width="6%">
                            <!-- bouton pdf -->
                            <a  href="<?=base_url()?>gestionpaie/genererFdp/<?=$value->mois_paie.'/'.$annee;?>" class="btn-option">
                                <i class="" title="PDF"><img src="<?=base_url()?>assets/blueline/images/pdf.png" alt=""></i>
                            </a>
                       </td>

                        <td class=""><?=$libelleMois[str_pad($value->mois_paie, 2, '0', STR_PAD_LEFT)];?></td>
                        <td class=""><?=$value->count_salarie;?></td>
                        <td onclick=""><?=$value->sum_brut;?></td>
                        <td onclick=""><?=$value->sum_net;?></td>
                        <td onclick=""><?=$value->sum_cnss;?></td>
                        <td onclick=""><?=$value->sum_irpp;?></td>

                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <input type="hidden" name="mois" value="<?php echo (isset($mois)? $mois:'')?>"/>
            <input type="hidden" name="annee" value="<?php echo (isset($annee)? $annee:'')?>"/>
        </form>
    </div>
</div>