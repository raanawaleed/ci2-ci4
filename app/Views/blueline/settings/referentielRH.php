<div id="row">
	
		<div class="col-md-3">
			<div class="list-group">
				<?php foreach ($submenu as $name=>$value):
				$badge = "";
				$active = "";
				if($value == "settings/societe"){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
				if($name == $breadcrumb){ $active = 'active';}?>
	               <a class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
	            <?php endforeach;?>
			</div>
		</div>
<div class="col-md-9">
<div class="row">
		<div class="span12 marginbottom20">
		<div class="table-head"><?=$this->lang->line('application_type_journé')?><span class="pull-right"><a href="<?=base_url()?>settings/ajoutTypejourne" data-toggle="mainmodal" class="btn btn-success"><?=$this->lang->line('application-add');?></a> </span></div>
		<div class="subcont">
           <table class="data-no-search table dataTable no-footer" cellspacing="0" cellpadding="0" role="grid" id="sample_1">
                                <thead> 
                                    <tr> 
                                        <th>Libelle de l'occurrence du référentiel</th>
                                        <th class="hidden-480">Description</th>
                                        <th>Actions </th>
                
                                    </tr>
                                </thead>
                                <tbody>
                               <?php foreach ($journé as $key ) {
                                    ?>
                                    <tr class="odd gradeX">
                                            
                                           
                                            <?php
                                            
                                             
                                                 
                                               echo("<td>".$key->name."</td>");

                                               echo("<td>".$key->description."</td>");
                                               echo('<td width="8%"><a href="editrh/'.$key->id.'" data-toggle="mainmodal" class="btn-option"><i class="fa fa-edit" title="Modifier"></i></a>');
                                               ?>
                                               <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>settings/deleteref/<?=$key->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
                                        </tr>
			       <?php } ?>
                                </tbody>
                            </table>
		<br clear="all">
		</div>
		</div>
		</div></div>
<div class="col-md-9">
<div class="row">
		<div class="span12 marginbottom20">
		<div class="table-head"><?=$this->lang->line('application_rh_typepaiement')?><span class="pull-right"><a href="<?=base_url()?>settings/ajoutTypepai" data-toggle="mainmodal" class="btn btn-success"><?=$this->lang->line('application-add');?></a> </span></div>
		<div class="subcont">
           <table class="data-no-search table dataTable no-footer" cellspacing="0" cellpadding="0" role="grid" id="sample_1">
                                <thead> 
                                    <tr> 
                                        <th>Libelle de l'occurrence du référentiel</th>
                                        <th class="hidden-480">Description</th>
                                        <th>Actions </th>
                
                                    </tr>
                                </thead>
                                <tbody>
                               <?php foreach ($paiement as $key ) {
                                    ?>
                                    <tr class="odd gradeX">
                                            
                                           
                                            <?php
                                            
                                             
                                                 
                                               echo("<td>".$key->name."</td>");

                                               echo("<td>".$key->description."</td>");
                                               echo('<td width="8%"><a href="editrh/'.$key->id.'" data-toggle="mainmodal" class="btn-option"><i class="fa fa-edit" title="Modifier"></i></a>');
                                           ?>
                                           <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>settings/deleteref/<?=$key->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
                                        </tr>
			       <?php } ?>
                                </tbody>
                            </table>
		<br clear="all">
		</div>
		</div>
		</div></div>
        <div class="col-md-3"></div>
        <div class="col-md-9">
<div class="row">
		<div class="span12 marginbottom20">
		<div class="table-head"><?=$this->lang->line('application_rh_typepret')?><span class="pull-right"><a href="<?=base_url()?>settings/ajoutTypepret" data-toggle="mainmodal" class="btn btn-success"><?=$this->lang->line('application-add');?></a> </span></div>
		<div class="subcont">
           <table class="data-no-search table dataTable no-footer" cellspacing="0" cellpadding="0" role="grid" id="sample_1">
                                <thead> 
                                    <tr> 
                                        <th>Libelle de l'occurrence du référentiel</th>
                                        <th class="hidden-480">Description</th>
                                        <th>Actions </th>
                
                                    </tr>
                                </thead>
                                <tbody>
                               <?php foreach ($pret as $key ) {
                                    ?>
                                    <tr class="odd gradeX">
                                            
                                           
                                            <?php
                                            
                                             
                                                 
                                               echo("<td>".$key->name."</td>");

                                               echo("<td>".$key->description."</td>");
                                               echo('<td width="8%"><a href="editrh/'.$key->id.'" data-toggle="mainmodal" class="btn-option"><i class="fa fa-edit" title="Modifier"></i></a>');
                                              ?>
                                               <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>settings/deleteref/<?=$key->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
                                        </tr>
			       <?php } ?>
                                </tbody>
                            </table>
		<br clear="all">
		</div>
		</div>
		</div></div>
         <div class="col-md-3"></div>
        <div class="col-md-9">
<div class="row">
		<div class="span12 marginbottom20">
		<div class="table-head"><?=$this->lang->line('application_rh_typeremboursement')?><span class="pull-right"><a href="<?=base_url()?>settings/ajoutTyperembrou" data-toggle="mainmodal" class="btn btn-success"><?=$this->lang->line('application-add');?></a> </span></div>
		<div class="subcont">
           <table class="data-no-search table dataTable no-footer" cellspacing="0" cellpadding="0" role="grid" id="sample_1">
                                <thead> 
                                    <tr> 
                                        <th>Libelle de l'occurrence du référentiel</th>
                                        <th class="hidden-480">Description</th>
                                        <th>Actions </th>
                
                                    </tr>
                                </thead>
                                <tbody>
                               <?php foreach ($remboursement as $key ) {
                                    ?>
                                    <tr class="odd gradeX">
                                            
                                           
                                            <?php
                                            
                                             
                                                 
                                               echo("<td>".$key->name."</td>");

                                               echo("<td>".$key->description."</td>");
                                               echo('<td width="8%"><a href="editrh/'.$key->id.'" data-toggle="mainmodal" class="btn-option"><i class="fa fa-edit" title="Modifier"></i></a>');
                                ?>
                                <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>settings/deleteref/<?=$key->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
                                        </tr>
			       <?php } ?>
                                </tbody>
                            </table>
		<br clear="all">
		</div>
		</div>
		</div></div>
        <div class="col-md-3"></div>
        <div class="col-md-9">
<div class="row">
		<div class="span12 marginbottom20">
		<div class="table-head"><?=$this->lang->line('application_rh_codeabsence')?><span class="pull-right"><a href="<?=base_url()?>settings/ajoutcodeab" data-toggle="mainmodal" class="btn btn-success"><?=$this->lang->line('application-add');?></a> </span></div>
		<div class="subcont">
           <table class="data-no-search table dataTable no-footer" cellspacing="0" cellpadding="0" role="grid" id="sample_1">
                                <thead> 
                                    <tr> 
                                        <th>Libelle de l'occurrence du référentiel</th>
                                        <th class="hidden-480">Description</th>
                                        <th>Actions </th>
                
                                    </tr>
                                </thead>
                                <tbody>
                               <?php foreach ($absence as $key ) {
                                    ?>
                                    <tr class="odd gradeX">
                                            
                                           
                                            <?php
                                            
                                             
                                                 
                                               echo("<td>".$key->name."</td>");

                                               echo("<td>".$key->description."</td>");
                                               echo('<td width="8%"><a href="editrh/'.$key->id.'" data-toggle="mainmodal" class="btn-option"><i class="fa fa-edit" title="Modifier"></i></a>');
                                ?>
                                <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>settings/deleteref/<?=$key->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
                                        </tr>
			       <?php } ?>
                                </tbody>
                            </table>
		<br clear="all">
		</div>
		</div>
		</div></div>