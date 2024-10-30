<div class="row">
	<?php   
		$attributes = array('class' => '', 'id' => '_company', 'autocomplete' => 'off');
		echo form_open_multipart($form_action, $attributes); 
	?>
	<div class="span12 marginbottom20">
		<div class="table-head">
			<?=$libelle?>
			<span class="pull-right">
				<input type="submit" name="send" class="btn btn-success" value="<?=$this->lang->line('application_save');?>"/>
    		</span>
		</div>
		<div class="subcont">
			<table class="data-no-search table dataTable no-footer" cellspacing="0" cellpadding="0" role="grid" >
				<thead> 
					<tr> 
						<th><?=$this->lang->line('application_libelle_occurrence')?></th>
						<th><?=$this->lang->line('application_description_occurrence')?></th>
						<th class="hidden-480"><?=$this->lang->line('application_statut')?></th>
					</tr>
				</thead>
				<tbody>
			   <?php foreach ($tab as $key ) :?>
					<tr class="odd gradeX">
						<td><?=$key->name?></td>
						<td><?=$key->description?></td>
						<td>
							<input type="radio" class="form-group" name="visible" value="<?=$key->id; ?>" <?php if($key->visible) echo "checked" ?>>
                        </td>
					</tr>
				<?php endforeach; ?>
				</tbody>
            </table>
			<br clear="all">
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
