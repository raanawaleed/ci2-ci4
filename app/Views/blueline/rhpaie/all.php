<?php $statut = $conges['statut'];
?>

<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>



	<div class="row">
	<div class="col-sm-3">
            <!-- salariés -->
            <div class="form-group">
                <label for="service_filter">Statut</label>
                <select class="chosen-select" id="service_filter">
				<option > </option>

                    <option value="all">Tous les Statuts</option>
                    <option value="enattent" <?= $conges['statut'] == 'enattent' ? 'selected' : '' ?>>En attente</option>
                    <option value="accepter" <?= $conges['statut'] == 'accepter' ? 'selected' : '' ?>>Accepter</option>
                </select>
            </div>
        </div>
    <div class="col-sm-3 right">
<a type="button" href="<?=base_url()?>calendar_conges_absences" class="btn btn-success btn-lg"><span class="fa fa-calendar"></span><br>Calendrier Congés & Absences</a><br>
</div>  
      
</div>
<div class="row"> 

	<div class="col-sm-12  col-md-12 main"> 


		<div class="table-head"> <?=$this->lang->line('application_liste_gestionconge');?>

        </div>

                   
	<div class="table-div">
		<table class="dataSorting table hover" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
            
            <thead>
                 <th>Salarié(e)</th>
                <th><?=$this->lang->line('application_date_debut');?></th>
                <th>Date Fin</th>
                <th><?=$this->lang->line('application_motif');?></th>
                <th><?=$this->lang->line('application_statut');?></th>
                <th>Solde Congés</th>
                <th>Valider</th>
                <th>Annuler</th>


            </thead>

            <?php 

            foreach ($conges as $value):    
                     
                ?>
                <tr id="<?=$value->statut;?>" >
              
                    <td class="hidden-xs">
                        <?php foreach($salaries as $salarie){
                         
                                if($salarie->id == $value->id_salarie)
                                {
                                    echo $salarie->nom. ' '.$salarie->prenom ;
                                }
                            ?>
                        <?php }?>
                    </td>
                    <?php setlocale(LC_TIME, 'fr_FR','fra');

                    ?>
                    <td class="hidden-xs">
                    
                    <?php   if($value->motif=="162")
                    {
                      echo utf8_encode(strftime('%A %d %B %Y / %H:%M',strtotime($value->date_debut)));

                    }else{
                      echo utf8_encode(strftime('%A %d %B %Y',strtotime($value->date_debut)));

                    }

                    ?>
                  </td>

                  
                  <?php setlocale (LC_TIME, 'fr_FR','fra');

                  ?>
                 <td class="hidden-xs">
                  
                  <?php   if($value->motif=="162")
                  {
                    echo utf8_encode(strftime('%H:%M',strtotime($value->date_fin)));

                  }else{
                    echo utf8_encode(strftime('%A %d %B %Y',strtotime($value->date_fin)));

                  }

                  ?>
                </td>
                    <td class="hidden-xs">
                            <?php get_texte_occurence($value->motif); ?>
                    </td>

                    <td class="hidden-xs">


                    
                    <?php if($value->statut=="123")
                            {?>
                           
                            <span style="color:red;font-weight:bold"><?php get_texte_occurence($value->statut); ?></span>                    
                            <?php
                          }?>    
                            <?php if($value->statut=="28")
                            {?>
                            <span style="color:green;font-weight:bold"><?php get_texte_occurence($value->statut); ?></span>                    
                 
                            <?php
                          }?>  

                    <?php if($value->statut=="162")
                {?>
                  <span class="notif" style="color:orange;font-weight:bold"><?php get_texte_occurence($value->statut); ?>
                  <i id="test" class="fa fa-bell fa-spin" ></i>
                </span>                   
<?php
                }?>
                    </td>

                    <td class="hidden-xs">
                           
                          <?php foreach($salaries as $salarie){
                                              
                            
                                              if($salarie->id == $value->id_salarie)
                                              {
                                                  echo $salarie->droit_conge ;
                                              }
                            
                      }?>                           
                            
                    </td>
                    <td class="hidden-xs">
                    <div class="col-md-5"> 

                         <a href="<?=base_url()?>gestionconge/valider/<?=$value->id?>"  type="submit" ><i class="fad fa-vote-yea" style="font-size:18px;color:green" title="Valider"></i></a>
              </div>
              </td>
              <td class="hidden-xs">

                         <div class="col-md-5"> 

                          <a href="<?=base_url()?>gestionconge/refuser/<?=$value->id;?>"  type="submit"><i class="fad fa-vote-nay" style="font-size:18px;color:red" title="Annuler"></i></a>
                 </div>
                            </td>
                </tr>
            <?php endforeach;?>
        </table>
        <br clear="all">
    </div>


	<script>
                var statut = '<?= $statut ?>';
                var base_url = '<?= base_url() ?>';

                $(function() {
                    $('.last-in-row').popover({
                        html: true,
                        content: $(" #monthSelector").html()
                    });
                    $year = $('#id').html();
                    $length = $(".dropdown-item").length;
                    for (var i = 0; i < $length; i++) {
                        $(".dropdown-item").eq(i).attr("href", base_url + "gestionconge?statut=" + statut);
                    }
                    $(statut).ready(function() {
                        $('#service_filter').change(function() {
                            var select = $(this).val();
                            var base_url = '<?php echo base_url(); ?>';
                            if (select !== 'all') window.location.href = `${base_url}gestionconge?statut=${select}`;
                            else window.location.href = `${base_url}gestionconge`;
                        });

                        $(".itemname").each(function() {
                            let seraffectation = $(this).data('affectation');
                            let ville = $(this).data('ville');
                            let phone = $(this).data('phone');
                            let image = $(this).data('file');
                            $(this).popover({
                                trigger: 'hover',
                                html: true,
                                content: '<div class="details"><div  style="display: flex!important;"><div class="container-b"></div><div class="container-c"><div class="field type-text"><div id="labelp" >Department:</div><div class="value" lang="en">' + seraffectation + '</div></div><div class="field type-text"><div id="labelp">Phone:</div><div class="value" lang="en">' + phone + '</div></div><div class="field type-link"><div id="labelp" >Ville:</div>' + ville + '</div></div></div></div>'
                            });
                        });
                        $(".iconnume").each(function() {
                            let content = $(this).data('value');
                            let icon = $(this).html();
                            $(this).popover({
                                html: true,
                                trigger: 'hover',
                                placement: 'right',
                                constraints: [{
                                    to: 'scrollParent',
                                    attachment: 'together',
                                    pin: true
                                }],
                                container: 'body',
                                content: '<div>' + icon + content + '</div>',
                            });
                        });
                    });
                });
				function select() {
                    $year = $('#id').html();
                    $length = $(".dropdown-item").length;
                    for (var i = 0; i < $length; i++) {
                        $(".dropdown-item").eq(i).attr("href", base_url + "suivi?year=" + $year + "&month=" + [i] + "&department=" + department);;
                    }
                }
				</script>



