<style type="text/css">
	.pere{
	    font-weight: bold;
			width: 100%;
	}
	.fils{
		padding-left: 25px !important;
	}
	.search-field , .search-field input.default {

		width: 500px!important;

	}



</style>
<?php
$attributes = array('class' => '', 'id' => '_article');
echo form_open_multipart($form_action, $attributes); ?>
<input id="id" type="hidden" name="id" class=" form-control" value="<?php if(isset($ticket)){ echo($ticket->id); }?>" />
<!-- Projet -->
<div class="row">
	<div class="col-md-6">
		<div class="form-group ">
			<label for="collaborater"><?=$this->lang->line('application_projects');?> *</label>
			<select name="project_id"  id="project_id" id="" class="chosen-select" required data-placeholder="Veuillez choisir un projet">
					<option  ></option>
			   		<?php foreach($projects as $project) : ?>
				   	<option class="pere " value="<?=$project->id?>" <?php echo ($project->id == $ticket->project_id)? "selected":""; ?>>
				   		<?=$project->project_num." - ".$project->name?>
					</option>
			   <?php endforeach; ?>
			</select>
		</div>
	</div>

<!--Sous Projet -->
	<div class="col-md-6">
		<div class="form-group ">
			<label for="collaborater"><?=$this->lang->line('application_sous_projets');?></label>
			<select name="sub_project_id"  id="sub_project_id" class="chosen-select chosen_select_L" style="width:100%">
				<?php foreach($projects as $project) : ?>
			   		<?php if($project->id == $ticket->project_id) : ?>
			   			<?php if(count($project->project_has_sub_projects) == 0) :?>
							<option value="#">Aucun sous projet n'est rattaché à ce projet</option>
						<?php else : ?>
							<option value="#">Vous pouvez choisir un sous projet</option>
	   						<?php foreach($project->project_has_sub_projects as $sub_project) : ?>
					   			<option value="<?=$sub_project->id?>" <?php echo ($sub_project->id == $ticket->sub_project_id)? "selected":""; ?>>
							   		<?=$sub_project->name?>
								</option>
						   	<?php endforeach; ?>
					   	<?php endif; ?>
			   		<?php endif; ?>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
</div>

<!-- Sujet -->
	<div class="form-group"><label for="subject"><?=$this->lang->line('application_subject');?> *</label>
	<input id="subject" type="text" name="subject" class=" form-control"
	value="<?php if(isset($ticket)){ echo($ticket->subject); }?>" required />
</div>

<div class="row">
	<!-- propriétaire(s) -->
	<div class="col-md-12 col-xs-12">
		<div class="form-group" style="width:100%;">
			<label for="collaborater"><?=$this->lang->line('application_Owner');?></label>

			<select name="collaborater_id[]"  id="collaborater_id" id="" class="chosen-select"  data-placeholder="Veuillez choisir un propriétaire" style="width: inherit; width:100%; display: block;" multiple>

			<?php if(isset($ticket)){ ?>
        <option  disabled selected  value=""></option>
				<option style="width:100%;" value="" >-----</option>
			<?php } ?>

			   <?php foreach($collaboraters as $collaborater){
					if($collaborater->id == $ticket->collaborater_id) { ?>
				<option value="<?=$collaborater->id?>" selected><?=$collaborater->firstname.' '.$collaborater->lastname?></option>
					<?php } else { ?>
				<option value="<?=$collaborater->id?>" ><?=$collaborater->firstname.' '.$collaborater->lastname?></option>
					<?php } ?>
			   <?php } ?>
			</select>
		</div>
	</div>
</div>
<div class="row">
	<!-- statut -->
	<div class="col-md-4">
		 <div class="form-group">
			<label for="status"><?=$this->lang->line('application_status');?></label>
			<select name="status"  id="status" id="" class="chosen-select">
			   <?php foreach($status as $val){
					if($val->id == $ticket->status) { ?>
				<option value="<?=$val->id?>" selected><?=$val->name?></option>
					<?php } else { ?>
				<option value="<?=$val->id?>" ><?=$val->name;?></option>
					<?php } ?>
			   <?php } ?>
			</select>
		</div>
	</div>
	<!-- Etat -->
	<div class="col-md-4">
		 <div class="form-group">
			<label for="etat_id"><?=$this->lang->line('application_etat');?></label>
			<select name="etat_id"  id="etat_id" id="" class="chosen-select"  data-placeholder="choisir l'état">
			   <?php foreach($etats as $val){
					if($val->id == $ticket->etat_id) { ?>
				<option value="<?=$val->id?>" selected><?=$val->name?></option>
					<?php } else { ?>
				<option value="<?=$val->id?>" ><?=$val->name;?></option>
					<?php } ?>
			   <?php } ?>
			</select>
		</div>
	</div>
	<!-- Priorité -->
	<div class="col-md-4">
		 <div class="form-group">
			<label for="priority">Priorité</label>
			<select name="priority"  id="priority"  class="chosen-select"  data-placeholder="Veuillez choisir un type">
			   <?php foreach($priorite as $val){
					if($val->id == $ticket->priority) { ?>
				<option value="<?=$val->id?>" selected><?=$val->name?></option>
					<?php } else { ?>
				<option value="<?=$val->id?>"><?=$val->name;?></option>
					<?php } ?>
			   <?php } ?>
			</select>
		</div>
	</div>
</div>
<!-- date début & date fin -->
<div class="row">
	<!-- date début -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
	  		<label for="start">Date début *</label>
	 		<input class="form-control datepicker" name="start" id="start" type="text" value="<?php if(isset($ticket)){echo $ticket->start;} ?>" required/>
		</div>
	</div>
	<!-- date fin -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group">
	  		<label for="end"><?=$this->lang->line('application_deadline');?> *</label>
	  		<input class="form-control datepicker-linked" name="end" id="end" type="text" value="<?php if(isset($ticket)){echo $ticket->end;} ?>" required/>
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
				value="<?php if(isset($ticket)){echo $ticket->surface;} ?>"/>
		</div>
	</div>
	<!-- Longueur  -->
	<div class="col-sm-12 col-md-6">
		<div class="form-group" >
			<label for="Longueur">Longueur (ml)</label><br>
			<input id="longueur" type="number" min="0" step="0.001" name="longueur" class="form-control number" placeholder="1,000" 
				value="<?php if(isset($ticket)){echo $ticket->longueur;} ?>"/>
		</div>
	</div>
</div>

<!-- message -->
<div class="form-group">
	<label for="text"><?=$this->lang->line('application_message');?></label>
	<textarea id="text" name="text" class="textarea summernote-modal form-control" style="height:100px"><?php if(isset($ticket)) {echo $ticket->text;} ?></textarea>
</div>

<!-- attachement -->
<div class="form-group">
    <label for="userfile"><?=$this->lang->line('application_attachment');?></label>
    <span id="uploadFileNb" style="font-size: 11px;"></span>
    <div>
    <input id="uploadFile" type="text" name="dummy" class="form-control uploadFile" placeholder="Choisir un fichier" disabled="disabled" />
        <div class="fileUpload btn btn-primary">
            <span><i class="fa fa-upload"></i><span class="hidden-xs"> <?=$this->lang->line('application_select');?></span></span>
            <input id="uploadBtn" type="file" name="userfile[]" class="upload" multiple="multiple" />
        </div>
    </div>
</div>

<div class="modal-footer">
	<input type="submit" name="send" class="btn btn-primary input-loader submit"  value="<?=$this->lang->line('application_save');?>"/>
	<a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>

<?php echo form_close(); ?>
<script type="text/javascript">
	$( "#project_id" ).change(function() {
		var valueSelected = this.value;
	  	$.ajax({
                type: "post",
                url: "<?php echo site_url("projects/get_sub_projects"); ?>",
                data: {project_id : valueSelected},
                dataType: "text",
                cache: false,
                success: function( data ) {
                	if(data == "false"){
                		$('#sub_project_id').attr("disabled", true);
            		}else{
	                    $(".chosen_select_L").chosen('destroy'); //<-- A mettre avant innerHTML
	            		$('#sub_project_id').html(data);
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

    $(document).ready(function() {
        //pièces jointes multiple
        $('#uploadBtn').change(function () {
            var // Define maximum number of files.
                max_file_number = 4,
                // Define your form id or class or just tag.
                $form = $('_article'),
                // Define your submit class or id or tag.
                $button = $('.submit', $form);

            // Disable submit button on page ready.
            $button.prop('disabled', 'disabled');
            if (this.files.length == 0) {
                alert(`Vous n'avez rien choisi.`);
                $button.prop('disabled', 'disabled');
            }else if (this.files.length > max_file_number) {
                alert(`Vous ne pouvez sélectionnez au maximum que ${max_file_number} fichier.`);
                $(this).val('');
                $('#uploadFileNb').html('');
                $button.prop('disabled', 'disabled');
            } else {
                var do_submit = true;
                for (var i = 0; i < this.files.length; ++i) {
                    var size = this.files.item(i).size;
                    if (size/ 1024 / 1024 > 1){
                        do_submit = false;
                        $(this).val('');
                        $('#uploadFileNb').html('');
                        $button.prop('disabled', 'disabled');
                        alert("Un des fichiers sélectionnés dépasse 1 MO.");

                    }
                }
                if(do_submit){
                    var log = (this.files.length > 1)?  " fichiers sélectionnés":" fichier sélectionné";
                    $('#uploadFileNb').html( this.files.length + log);
                    $button.prop('disabled', false);
                }
            }
        });
    });

</script>
