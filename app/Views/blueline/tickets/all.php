<div class="col-sm-12  col-md-12 main">
	<div class="row">
        <a href="<?=base_url()?>ctickets/create" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_create_new_ticket');?></a>

        <!--filtre-->
		<div class="btn-group pull-right-responsive margin-right-3">
			<button id="bulk-button" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" >
				Filtre <span class="caret"></span>
			</button>
			<ul class="dropdown-menu pull-right" role="menu">
				<!-- tous -->
				<li><a id="" href="<?=base_url()?>ctickets/filter/0"><?=$this->lang->line('application_all');?></a></li>
				<!-- fermés -->
				<li><a id="" href="<?=base_url()?>ctickets/filter/1">Fermés</a></li>
				<!-- mes tickets-->
				<li><a id="" href="<?=base_url()?>ctickets/filter/False/<?=$this->user->id;?>"><?=$this->lang->line('application_my_tickets');?></a></li>
				<!-- referentiel -->
				<!--<?php foreach ($submenu as $name=>$value):?>
					<li><a id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$name?></a></li>
				<?php endforeach;?> -->
			</ul>
		</div>

		<!--projet
		<div class="btn-group pull-right-responsive margin-right-3">
			<button id="bulk-button" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" >
				Projets <span class="caret"></span>
			</button>
			<ul class="dropdown-menu pull-right" role="menu">
				<?php foreach ($projets as $key=>$proj):?>
					<li>
						<a  href="<?=base_url()?>ctickets/tous/<?=$proj->id;?>"><?=$proj->name;?></a>
					</li>
				<?php endforeach;?>
			</ul>
		</div>-->

		<!--Actions multiples -->
		<div class="btn-group pull-right-responsive margin-right-3">
			<button id="bulk-button" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
				<?=$this->lang->line('application_bulk_actions');?> <span class="caret"></span>
			</button>
		<ul class="dropdown-menu pull-right bulk-dropdown" role="menu">
			<li data-action="/close"><a id="" href="#"><?=$this->lang->line('application_close');?></a></li>
		</ul>
		<?php
			$form_action ='ctickets/bulk/';
			$attributes = array('class' => '', 'id' => 'bulk-form');
			echo form_open($form_action, $attributes); ?>
			<input type="hidden" name="list" id="list-data"/>
		</form>
		</div>

		<!-- Catégories projet -->
		<div class="btn-group pull-right-responsive margin-right-3">
			<button id="bulk-button" type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" >
				Catégories projets  <span class="caret"></span>
			</button>
			<ul class="dropdown-menu pull-right" role="menu">
				<?php foreach ($categorie_projet as $name=>$value):?>
					<li><a id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$name?></a></li>
				<?php endforeach;?>
			</ul>
		</div>
</div>

	<div class="row col-xs-12 col-md-12 col-lg-12">
		<div class="table-head col-xs-12 col-md-12 col-lg-12"><?=$this->lang->line('application_tickets');?><?php echo (isset($occDeleted))? " SUPPRIMEES":""; ?><?php echo (isset($filter_proj)? ' ASSOCIEES AU  PROJET: ' . $filter_proj->name: ''); ?></div>
		<div class="table-div col-xs-12 col-md-12 col-lg-12">
		<table class="dataSorting tabl col-xs-12 col-md-12 col-lg-12e" id="ctickets" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
		<thead>
            <?php if(!isset($occDeleted)) :?>
                <th class="no_sort simplecheckbox" style="width:16px"><input class="checkbox-nolabel" type="checkbox" id="checkAll" name="selectall" value=""></th>
            <?php endif; ?>
            <th   style="text-align: center;" class="col-xs-1 col-md-1 col-lg-1" style="width:30px">Id</th>
            <th  style="text-align: center;" class="col-xs-2 col-md-2 col-lg-2"><?=$this->lang->line('application_project');?></th>
            <th  style="text-align: center;" class="col-xs-2 col-md-2 col-lg-2"><?=$this->lang->line('application_sous_projet');?></th>
			<th style="text-align: center;"class="col-xs-1 col-md-1 col-lg-1"><?=$this->lang->line('application_status');?></th>
			<th style="text-align: center;"class="col-xs-1 col-md-1 col-lg-1">Priorité</th>
			<th  style="text-align: center;" class="no_sort col-xs-1 col-md-1 col-lg-1" style="width:5px; padding-right: 5px;" ><i class="fa fa-bell"></i></th>
			<th  style="text-align: center;" class="col-xs-3 col-md-3 col-lg-3"><?=$this->lang->line('application_subject');?></th>
			<th  style="text-align: center;" class="col-xs-1 col-md-1 col-lg-1"><?=$this->lang->line('application_etat');?></th>
			<th  style="text-align: center;" class="col-xs-1 col-md-1 col-lg-1"><?=$this->lang->line('application_Owner');?></th>
			
			
            <?php if(!isset($occDeleted)) :?>
                <th  style="text-align: center;" class="col-xs-2 col-md-2 col-lg-2" style="width:100px ; align-content: center;"><?=$this->lang->line('application_action');?></th>
            <?php endif; ?>
		</thead>
		<?php foreach ($ticket as $value): ?>
			<?php $lable = ""; ?>
		<tr id="<?=$value->id;?>" <?php echo (isset($occDeleted))? "class='danger '":""?> >
            <?php if(!isset($occDeleted)) :?>
                <td class=" noclick simplecheckbox" style="width:16px"> <input class="checkbox-nolabel bulk-box" type="checkbox" name="bulk[]" value="<?=$value->id?>"></td>
            <?php endif; ?>
            <td  class=" col-xs-1 col-md-1 col-lg-1 <?php echo (isset($occDeleted))? "noclick":""?>" style="width:70px"><?=$value->reference;?></td>
            <td class="col-xs-2 col-md-2 col-lg-2 <?php echo (isset($occDeleted))? "noclick":""?>">
				<?php if(!isset($value->project_id->name)): ?>
					<a  data-toggle="tooltip" title="<?php echo $value->project_id->name ?>" href="#" class="label label-info"><?php echo $this->lang->line('application_no_project_assigned'); ?>
					</a>
				 <?php else : ?>
					<a data-toggle="tooltip" data-placement="left" title="<?php echo $value->project_id->name ?>" class="label label-info"
					href="<?=base_url() .'projects/view/'.$value->project_id->id ?>">
					<?php echo $value->project_id->project_num."-". $value->project_id->name; ?>
					</a>
				<?php endif;?>
			</td>

			<td class="col-xs-2 col-md-2 col-lg-2 <?php echo (isset($occDeleted))? "noclick":""?>">
				<!-- Sous Projet -->
		  		<?php if(!isset($value->sub_project_id->name)): ?>
					<a  data-toggle="tooltip" title="<?php echo $value->sub_project_id->name ?>" href="#" class="label label-warning"><?php echo $this->lang->line('application_no_project_assigned');
				else : ?>
					<a data-toggle="tooltip" data-placement="left" title="<?php echo $value->sub_project_id->name ?>" class="label label-warning" href="#">
					<?php echo (strlen($value->sub_project_id->name)>15)? substr($value->sub_project_id->name, 0, 15).'...':$value->sub_project_id->name; ?>
					</a>
				<?php endif;  ?>
			</td>
			<td  class=" col-xs-1 col-md-1 col-lg-1 <?php echo (isset($occDeleted))? "noclick":""?>" style="width:50px"><span class="label <?php echo $lable; ?>"><?=$value->status;?></span></td>
			<td  class=" col-xs-1 col-md-1 col-lg-1 <?php echo (isset($occDeleted))? "noclick":""?>" style="width:50px"><span class="label label-important <?php echo $lable; ?>"><?=$this->referentiels->getReferentielsById($value->priority)->name;?></span></td>
			<?php if(isset($value->user->id)){$user_id = $value->user->id; }else{ $user_id = FALSE; }?>
			<td  class=" col-xs-1  col-md-1  col-lg-1 <?php echo (isset($occDeleted))? "noclick":""?>" style="width:15px">
			<?php if($value->new_created == "1" && $value->collaborater_id->id==$this->user->id){?><i class="fa fa-star" style="color: red;"></i><?php }else{?> <i class="fa fa-bell" style="opacity: 0.2;"></i><?php } ?>
			</td>
			<td class="col-xs-3 col-md-3 col-lg-3 <?php echo (isset($occDeleted))? "noclick":""?>"><?=$value->subject;?></td>
			<td class=" col-xs-1 col-md-1 col-lg-1 <?php echo (isset($occDeleted))? "noclick":""?>"><?=$this->referentiels->getReferentielsById($value->etat_id)->name; ?></td>
			<td class=" col-xs-1 col-md-1 col-lg-1 <?php echo (isset($occDeleted))? "noclick":""?>"><?php if(!isset($value->collaborater_id->email)){echo '<span class="label">'.$this->lang->line('application_no_collaborater_assigned').'</span>'; }else{ echo '<span class="label label-info">'.$value->collaborater_id->firstname.' '.$value->collaborater_id->lastname.'</span>'; }?></td>
			
            <?php if(!isset($occDeleted)) :?>

                <td class="option col-xs-3 col-md-3 col-lg-3" style="width: 100%; margin: 0 auto;">
                    <div>
                        <span role="presentation" class="col-xs-1 col-md-1 col-lg-1"><a data-toggle="mainmodal" href="<?=base_url()?>ctickets/editTicket/<?=$value->id;?>" class="btn-option"><i class="fa fa-edit" title="Modifier"></i></a></span>
                        <span role="presentation" class="col-xs-1 col-md-1 col-lg-1"><a href="<?=base_url()?>ctickets/view/<?=$value->id;?>" class="btn-option"><i class="fa fa-eye" title="Visualiser"></i></a></span>
                        <span role="presentation" class="col-xs-1 col-md-1 col-lg-1"><a href="<?=base_url()?>ctickets/copyTicket/<?=$value->id;?>" class="btn-option"><i class="fa fa-copy" title="Dupliquer"></i></a></span>
                        <span role="presentation" class="col-xs-1 col-md-1 col-lg-1">
                            <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>ctickets/deleteTicket/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-trash" title="Supprimer"></i></button>
                        </span>


                    </div>
                </td>
            <?php endif; ?>
		</tr>
		<?php endforeach;?>
	 	</table>
	 	</div>
	</div>
</div>
