
	<div class="col-sm-12  col-md-12 main"> 
		<div class="row">

			<a href="<?=base_url()?>demandeConge/create" class="btn btn-success" data-toggle="mainmodal">Demande de congés</a>
			</div>
      

<!--<a href="<?=base_url()?>demandeConge/create2" class="btn btn-success" data-toggle="mainmodal">test</a>-->

			</div>


	<div class="col-sm-12  col-md-12 main"> 

		<div class="row"> 

		<div class="table-head right"></div>


	<div class="table-div">
		<table class="dataSorting table" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
            
            <thead>
              <th>Action</th>
                <th><?=$this->lang->line('application_date_debut');?></th>
                <th>Date Fin</th>
                <th><?=$this->lang->line('application_motif');?></th>
                <th><?=$this->lang->line('application_statut');?></th>
            </thead>

            <?php foreach ($conges as $value):
       
                ?>
                <tr >

                <tr  id="<?=$value->id;?>" >
               
                    <?php setlocale (LC_TIME, 'fr_FR','fra');

                    ?>
                    <td>
                    <a href="<?=base_url()?>demandeConge/updatedemande/<?=$value->id;?>" class="'btn btn-sm btn-info" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
			              <button type="button" class="btn btn-sm btn-danger delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-sm btn-danger po-delete' href='<?=base_url()?>demandeConge/deletedemande/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
                    </td>
                    <td class="hidden-xs">
                    
                      <?php   
                     
                        echo utf8_encode(strftime('%A %d %B %Y',strtotime($value->date_debut)));
 ?>
                    </td>

                    
                    <?php setlocale (LC_TIME, 'fr_FR','fra');?>
                   <td class="hidden-xs">
                    
                    <?php   
                      
                      echo utf8_encode(strftime('%A %d %B %Y',strtotime($value->date_fin)));
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
                            <span style="color:orange;font-weight:bold"><?php get_texte_occurence($value->statut); ?></span>                    
                 
                            <?php
                          }?>               
                     </td>

                </tr>
            <?php endforeach;?>
        </table>
        <br clear="all">
    </div>



