

<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>



	<div class="row">
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
            </thead>
            <?php 

            foreach ($conges2 as $value):       
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

             
                </tr>
            <?php endforeach;?>
        </table>
        <br clear="all">
    </div>


   