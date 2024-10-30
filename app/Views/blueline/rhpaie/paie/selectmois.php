<form id="form-creer-paie"  action="<?=site_url().$form_action?>" method="post" name="form-creer-paie" class="">
    <!-- boutons d'action -->
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-4">
                <!-- Mois -->
                <div class="form-group">
                    <label for="currency">Choisir le mois</label>
                    <select name="mois" class="chosen-select" >
                        <?php foreach ($libelleMonth as $nmonth => $lmonth) :?>
                            <option value=<?=$nmonth;?> <?php echo ($nmonth==$mois)? "selected":""; ?>><?=$lmonth;?></option>
                        <?php endforeach;; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">

            <!-- Année -->
                <div class="form-group">
                    <label for="currency">Choisir l'année</label>
                    <select name="annee" class="chosen-select" >
                        <?php foreach ($selectYears as $year) :?>
                            <option value=<?=$year;?> <?php echo ($year==$annee)? "selected":""; ?>><?=$year;?></option>
                        <?php endforeach;; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Créer la paie -->
                <div class="btn-group pull-right MarginTop MarginRight">
                    <input type="submit" class="btn btn-warning" value="Créer la paie selectionnée" style="margin-bottom:4px;white-space: normal;">
                </div>
            </div>
        </div>
    </div>
</form>