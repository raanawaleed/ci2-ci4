<div class="row">
	<div class="col-sm-12  col-md-12 main">
        <div class="subcont">
		<div class="table-head"> <?=$this->lang->line('application_archive_salarié');?></div>


	<div class="table-div">
		<table class="data table"  id="gestionsalarie" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
		<thead>
			<th><?=$this->lang->line('application_matricule');?></th>
			<th><?=$this->lang->line('application_prénom_nom');?></th>
			<th><?=$this->lang->line('application_address');?></th>
			<th><?=$this->lang->line('application_rh_fonction');?></th>
			<th><?=$this->lang->line('application_matricule_cnss');?></th>
			<th><?=$this->lang->line('application_Situation_familiale');?></th>
			<th><?=$this->lang->line('application_numerocin');?></th>
			<th><?=$this->lang->line('application_outils');?></th>
			<th><?=$this->lang->line('application_action');?></th>
		</thead>

		<?php foreach ($salaries as $value):?>

		<tr  id="<?=$value->id;?>" >

			<td class="hidden-xs"><?php
					if(isset($value->matricule))
						{ echo $value->matricule;}
					else
						{ echo "-";} ?>

			</td>


			<td class="hidden-xs"><?php
					if(isset($value->nom))
						{ echo $value->nom.' '.$value->prenom;}
					else
						{ echo "-";} ?>

			</td>


			<td class="hidden-xs">
				<?php if(isset($value->adresse1)){ echo $value->adresse1;}else{ echo "-";} ?>

				</td>

				<td class="hidden-xs">

					<?php foreach($fonctions as $fonction){

							if($fonction->id == $value->idfonction)
							{
								echo $fonction->libelle ;
							}
						?>

					<?php }?>

				</td>


<!--  		<td class="hidden-xs" style="width:70px"><?=$core_settings->company_prefix;?><?php if(isset($value->reference)){ echo sprintf("%04d",$value->reference);} ?></td>	 -->

			<td class="hidden-xs">
				<?php if(isset($value->numerocnss)){ echo $value->numerocnss;}else{ echo "-";} ?>

				</td>

			<td class="hidden-xs">
				<?php if(isset($value->situationfamiliale)){ echo $value->situationfamiliale;}else{ echo "-";} ?>

				</td>

			<td class="hidden-xs">
				<?php if(isset($value->numerocin)){ echo $value->numerocin;}else{ echo "-";} ?>

				</td>

			<td lass="hidden-xs" >

				<button class="btn btn-danger" type="button" disabled >

				<?=$this->lang->line('application_disabled');?>


				</button>

			</td>





			<td class="option action">

				 				<button class="btn btn-danger" type="button" disabled >

				<?=$this->lang->line('application_disabled');?>


				</button>

			</td>

		</tr>
		<?php endforeach;?>
	 	</table>
	 	<br clear="all">
	 	   <div class="modal-footer">

        <a class="btn"  href="<?=base_url()?>gestionsalarie"><?=$this->lang->line('application_close');?></a>
      </div>
	</div>
