<span class="pull-right">
	<a href="<?=base_url().(isset($url_add_ref)? $url_add_ref:'#') ?>" data-toggle="mainmodal" class="to-modal btn btn-success"><?=$this->lang->line('application-add');?>
	</a> 
</span>
<table class=" table data-media dataTable no-footer" cellspacing="0" cellpadding="0" role="grid" id="sample_1">
	<thead> 
		<tr> 
			<th class="hidden-480"><?=$this->lang->line('application_id')?></th>
			<th ><?=$this->lang->line('application_subject')?></th>
			<th class="hidden-480"><?=$this->lang->line('application_description')?></th>
			<th class="hidden-480"><?=$this->lang->line('application_statut')?></th>
			<th>Actions </th>
		</tr>
	</thead>
	<tbody >
		<?php foreach ($categorie_tickets as $key ) : ?>
			<tr class="odd gradeX">
				<td><?=$key->id?></td>
				<td><?=$key->subject?></td>
				<td><?=$key->description?></td>
				<td>
					<?php if($key->status == '1') : ?>
						<center><span class="menu-icon"><i class="fa fa-check"></i></span></center>
					<?php  endif;?>
				</td>
				<td width="8%">
					<a href="<?=(isset($url_update_ref)? $url_update_ref.'/'.$key->id:'#')?>" data-toggle="mainmodal" class="btn-option">
						<i class="fa fa-edit" title="Modifier"></i>
					</a>

					<button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="
							 <a class='btn btn-danger po-delete ajax-silent' href='<?=base_url().(isset($url_delete_ref)? $url_delete_ref.'/'.$key->id:'#') ?>'>
							 	<?=$this->lang->line('application_yes_im_sure');?>
							 </a> 
							 <button class='btn po-close'><?=$this->lang->line('application_no');?></button> 
							 <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" 
					 data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>">
					 	<i class="fa fa-trash" title="Supprimer"></i>
					</button>
				</td> 
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
