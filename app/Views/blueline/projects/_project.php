<?php
$attributes = array('class' => '', 'id' => '_project');
echo form_open($form_action, $attributes);
if(isset($project)){ ?>
<input id="id" type="hidden" name="id" value="<?php echo $project->id; ?>" />
<?php } ?>

<!-- Numéro & Référence & Nom du Projet -->
<div class="row">
	<!-- N° -->
	<div class="col-sm-3 ">
		<div class="form-group" id="object">

  
		  	<label for="name"><?=$this->lang->line('application_reference_id');?> *</label>
		  	<div class="input-group">
			<?php if(empty($project)){?>
			<div class="input-group-addon"><?php
				$reverse = strrev($core_settings->project_prefix);
				$splitReverse = explode('-', $reverse);
				$splitDate= explode('-', date("d-m-y"));
				if($splitReverse[0] == 'YY') {
					$output = strrev($splitReverse[1]).$splitDate[2];
				}else if ($splitReverse[0] =='MM') {
					$output = strrev($splitReverse[2]).$splitDate[2].$splitDate[1];
				}
				echo $output;
			?></div> <?php } ?>
			<input id="reference" type="text" name="reference" class="form-control"  onblur="testReference(this.value,'<?=$output;?>')"
			value="<?php if(isset($project)){
							echo $project->project_num;
						} else
						{
							
  $i=1;
  foreach($data as $row)
  {
 
  $i++;
  $c='0';

							if($core_settings->project_reference<1000){
								$row->id=$row->id-552;
							echo  '0'.$row->id.$c->project_reference;
							}else
								{
									echo $core_settings->project_reference;
								}
							}}?>" />
		</div>
	  	</div>
  	</div>
  	<!-- Nom du projet -->
	<div class="col-sm-6 ">
		<div class="form-group" id="object">

		  	<label for="name"><?=$this->lang->line('application_Name');?> *</label>
		  	<input type="text" name="name" class="form-control" id="name" value="<?php if(isset($project)){echo $project->name;} ?>" required/>
	  	</div>
  	</div>
  	<!-- Référence -->
  	<div class="col-sm-3 ">
		<div class="form-group">
		  	<label for="ref_projet"><?=$this->lang->line('application_estimate_id');?></label>
		  	<input type="text" name="ref_projet" class="form-control" id="ref_projet" value="<?php if(isset($project)){echo $project->ref_projet;} ?>"/>
	  	</div>
	</div>
</div>
<div class="help-block"></div>

<!-- Type  & Nature projet -->
<div class="row">
	<!-- Type  projet -->
	<div class="col-sm-6 col-md-5">
		<div class="form-group">
		  	<label for="ref_projet">Catégorie projet *</label>
		  	<select name="type_projet"  id="type_projet" class="chosen-select" data-placeholder="Choisir une catégorie">
				<option value="0"></option>
				<?php foreach($categorie_projets as $cat) : ?>
				<option class="pere " value="<?=$cat->id?>" <?php echo ($cat->id == $project->type_projet)? "selected":""; ?>>
				   		<?=$cat->name?>
				</option>
			   <?php endforeach; ?>
			</select>
		</div>
	</div>

    <!-- Nature projet -->
	<div class="col-sm-6 col-md-7">
		<div class="form-group">
		  	<label for="ref_projet">Nature projet *</label>
		  	<select name="nature_projet"  id="nature_projet" class="chosen-select chosen_select_L" style="width:100%" data-placeholder="Choisir la nature du projet">
				<option value="0"></option>
				<?php foreach($categorie_projets as $cat) : ?>

					<?php if($cat->id == $project->type_projet) : ?>
			   			<?php if(count($natures_projetcs) == 0) :?>
			   				<option value="#">Aucune nature pour cette catégorie</option>
						<?php else : ?>
							<option value="#">Choisir la nature</option>
	   						<?php foreach($natures_projetcs as $value) : ?>

					   			<option value="<?=$value->id?>" <?php echo ($value->id == $project->nature_projet)? "selected":""; ?>>
							   		<?=$value->name; ?>
								</option>
						   	<?php endforeach; ?>
					   	<?php endif; ?>
			   		<?php endif; ?>
				<?php endforeach; ?>
			</select> 
		</div>
	</div>
</div>

<!-- liste des clients & Chef Client  -->
<div class="row">
	<!-- liste des clients -->
	<div class="col-md-12 col-md-6">
		<div class="form-group ">
			<label for="collaborater"><?=$this->lang->line('application_client');?></label>
			<select name="company_id"  id="company_id" class="chosen-select" data-placeholder="Veuillez choisir un client">
					<option value=0></option>
			   		<?php foreach($companies as $value) : ?>
				   	<option class="pere " value="<?=$value->id?>" <?php echo ($value->id == $project->company_id->id)? "selected":""; ?>>
				   		<?=$value->name?>
					</option>
			   <?php endforeach; ?>
			</select>
		</div>
	</div>

	<!--contacts clients -->
	<div class="col-md-12 col-md-6">
		<div class="form-group ">
			<label for="collaborater">Chef projet client</label>
			<select name="sub_client_id"  id="sub_client_id" class="chosen-select chosen_select_L" style="width:100%" data-placeholder="Veuillez choisir le chef projet client">
				<?php foreach($companies as $value) : ?>

					<?php if($value->id == $project->company_id->id) : ?>
			   			<?php if(count($contacts_client) == 0) :?>
			   				<option value="#">Aucun contact pour ce client</option>
						<?php else : ?>
							<option value="#">Choisir un Chef projet client</option>
	   						<?php foreach($contacts_client as $value) : ?>

					   			<option value="<?=$value->id?>" <?php echo ($value->id == $project->sub_client_id)? "selected":""; ?>>
							   		<?=$value->firstname.' '.$value->lastname; ?>
								</option>
						   	<?php endforeach; ?>
					   	<?php endif; ?>
			   		<?php endif; ?>
				<?php endforeach; ?>
			</select> 
		</div> 
	</div> 
</div>

<!-- date début & date fin -->
<div class="row">
	<!-- date début -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
	  		<label for="start"><?=$this->lang->line('application_start_date');?> *</label>
	 		<input class="form-control datepicker" name="start" id="start" type="text" value="<?php if(isset($project)){echo $project->start;} ?>" required/>
		</div>
	</div>
	<!-- date fin -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
	  		<label for="end"><?=$this->lang->line('application_end_date');?> *</label>
	  		<input class="form-control datepicker-linked" name="end" id="end" type="text" value="<?php if(isset($project)){echo $project->end;} ?>" required/>
		</div>
	</div>
</div>

<div class="row">
	<!-- Etat du projet -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group" >
			<label for="etat_projet">Etat Projet</label><br>
			<?php   $options = array();
					$options['0'] = '-';
				foreach ($etats_projet as $key=>$value):
				$options[$value->id] = $value->name;
				endforeach;
			if(isset($project) && isset($project->etat_projet)){$etatselected = $project->etat_projet;}else{$etatselected = $etat_encours->id;}
			echo form_dropdown('etat_projet', $options, $etatselected, 'style="width:100%" class="chosen-select"');?>
		</div>
	</div>
	<!-- date de livraison  -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
	  		<label for="delivery">Date de Livraison </label>
	  		<input class="form-control datepicker" name="delivery" id="delivery" type="text" value="<?php if(isset($project)){echo $project->delivery;} ?>" />
		</div>
	</div>
</div>

<!-- liste des utilisateurs -->
<div class="row">
	<div class="col-sm-12">
		<div class="form-group">
			<label for="client">Chef Projet </label><br>
			<?php $options = array();
					$options['0'] = '-';
				foreach ($chef_projet as $value):
					$options[$value->id] = $value->firstname.' '.$value->lastname;
				endforeach; 
			if(isset($project) && isset($project->chef_projet_id)){$nom_chef_projet = $project->chef_projet_id;}else{$nom_chef_projet = "";}	
			echo form_dropdown('chef_projet_id', $options, $nom_chef_projet, 'style="width:100%" class="chosen-select"');?>
		</div>
	</div>
</div>
<!-- Quantité  -->
<div class="row">
	<!-- Surface  -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group" >
			<label for="Surface">Surface (m²)</label><br>
			<input id="surface" type="number" min="0" step="0.1" name="surface" class="form-control number" placeholder="1,0" 
				value="<?php if(isset($project)){echo $project->surface;} ?>"/>
		</div>
	</div>
	<!-- Longueur  -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group" >
			<label for="Longueur">Longueur (ml)</label><br>
			<input id="longueur" type="number" min="0" step="0.001" name="longueur" class="form-control number" placeholder="1,000" 
				value="<?php if(isset($project)){echo $project->longueur;} ?>"/>
		</div>
	</div>
</div>

<!--la progression -->
<div class="form-group">
	<label for="progress"><?=$this->lang->line('application_progress');?> <span id="progress-amount"><?php if(isset($project)){echo $project->progress;}else{echo "0";} ?></span> %</label>
		<div class="slider-group">
			<div id="slider-range"></div>
			</div>
		<input type="hidden" class="hidden" id="progress" name="progress" value="<?php if(isset($project)){echo $project->progress;}else{echo "0";} ?>">
</div>

<!-- description du projet -->

<div style="height: 30%;">
	<div class="form-group" style="clear:both;">
		<label for="textfield"><?=$this->lang->line('application_description');?></label>
		<textarea class="input-block-level form-control"  id="textfield" name="description" style="max-height: 80px;"><?php if(isset($project)){echo $project->description;} ?></textarea>
	</div>
</div>

<!-- boutons sauvegarder et fermer -->
<div class="modal-footer">
	<input type="submit" name="send" id="btnSubmit" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn btn-default" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">

$( "#type_projet" ).change(function() {
		
		var valueSelected = this.value;
		
	  	$.ajax({
                type: "post",
                url: "<?php echo site_url("projects/get_nature_projet"); ?>",
                data: {type_projet: valueSelected},
                dataType: "text",
                cache: false,
                success: function( data ) {
                	if(data == "false"){
                		$('#nature_projet').attr("disabled", true);
            		}else{
	                    $(".chosen_select_L").chosen('destroy'); //<-- A mettre avant innerHTML
	            		$('#nature_projet').html(data);
	            		$(".chosen_select_L").chosen();
	                }
	            }
              });
	});


	$( "#company_id" ).change(function() {
		
		var valueSelected = this.value;
		
	  	$.ajax({
                type: "post",
                url: "<?php echo site_url("projects/get_contacts_clients"); ?>",
                data: {company_id : valueSelected},
                dataType: "text",
                cache: false,
                success: function( data ) {
                	if(data == "false"){
                		$('#sub_client_id').attr("disabled", true);
            		}else{
	                    $(".chosen_select_L").chosen('destroy'); //<-- A mettre avant innerHTML
	            		$('#sub_client_id').html(data);
	            		$(".chosen_select_L").chosen();
	                }
	            }
              });
	});

	jQuery(document).ready(function($) {
        $(".chosen_select_L").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!"
        });
    });

     $(document).ready(function(){ 
	  //slider config
		$( "#slider-range" ).slider({
		  range: "min",
		  min: 0,
		  max: 100,
		  <?php if(isset($project) && $project->progress_calc == "1"){ ?>disabled: true,<?php } ?>
		  value: <?php if(isset($project)){echo $project->progress;}else{echo "0";} ?>,
		  slide: function( event, ui ) {
			$( "#progress-amount" ).html( ui.value );
			$( "#progress" ).val( ui.value );
		  }
		});
	});
</script>