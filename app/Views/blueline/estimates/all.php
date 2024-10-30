<?php $document = $estimates['document'];
 $department = $estimates['department'];
?><div class="col-sm-12  col-md-12 main">
	
	<!-- statistiques sur les devis -->
	<div class="row tile-row">
		<div class="col-md-2 col-xs-12 tile blue">
			<h1><span><?=$this->lang->line('application_estimate');?></span></h1>
		</div>
		
		<div class="col-md-3 col-xs-3 tile">
			<div class="icon-frame hidden-xs">
				<i class="fa fa-files-o"></i>
			</div>
			<a href="<?=base_url()?>estimates/filter/invoiced"><h1> <?php if(isset($estimates_factured_this_month)){echo $estimates_factured_this_month;} ?><span> <?= $this->lang->line('application_estimate');?></span></h1>
			<h2><?=$this->lang->line('application_factured_month');?></h2>
			</a>
		</div>
		
		<div class="col-md-3 col-xs-3 tile">
			<div class="icon-frame hidden-xs">
				<i class="ion-ios-bell"></i>
			</div>
			<a href="<?=base_url()?>estimates/filter/declined">
				<h1> <?php if(isset($estimates_refused_this_month)){echo $estimates_refused_this_month;} ?><span> <?=$this->lang->line('application_estimate');?></span></h1>
				<h2><?=$this->lang->line('application_refused_month');?></h2>
			</a>
		</div>
		
		<div class="col-md-3 col-xs-3 tile">
			<div class="icon-frame secondary hidden-xs">
				<i class="ion-ios-analytics"></i>
			</div>
			<a href="<?=base_url()?>estimates/filter/accepted">
			<h1><?php if(isset($estimates_accepted_this_month)){echo $estimates_accepted_this_month;} ?> <span> <?=$this->lang->line('application_estimate');?></span></h1>
			<h2><?=$this->lang->line('estimates_accepted_this_month');?></h2>
			</a>
		</div>
	</div>
	<!-- boutons d'actions -->
	<div class="row"><div class="col-sm-3">
            <!-- salariés -->
            <div class="form-group">
                <label for="service_filter">Service</label>
                <select class="chosen-select" id="service_filterr">
				<option > </option>
                    <option value="all">Tous les services</option>
                    <option value="mms" <?= $estimates['department'] == 'mms' ? 'selected' : '' ?>>MMS</option>
                    <option value="bim2d" <?= $estimates['department'] == 'bim2d' ? 'selected' : '' ?>>BIM 2D</option>
                    <option value="bim3d" <?= $estimates['department'] == 'bim3d' ? 'selected' : '' ?>>BIM 3D</option>
                </select>
            </div>
        </div>
	    <div class="col-sm-3">
            <!-- salariés -->


			
            <div class="form-group">
                <label for="service_filter">Document</label>
                 
        <select class="chosen-select" id="service_filter">
		<option > </option>
			<option value="all"  >Tous les documents</option>
            <option value="devis"  <?= $estimates['document'] == 'devis' ? 'selected' : 'devis' ?>>Devis</option>
            <option value="attachement"  <?= $estimates['document'] == 'attachement' ? 'selected' : 'attachement' ?>>Attachement</option>
        </select>
            </div>
        </div>
				


		<div class="row">
        <a href="<?= base_url() ?>projects" class="btn btn-primary right">Liste des projets </a>
			
					
        
		
		
			
		
    </div>
	
	
		<a href="<?=base_url()?>estimates/create" class="btn btn-primary" data-toggle="mainmodal">Devis</a>
		<a href="<?=base_url()?>estimates/createb" class="btn btn-primary" data-toggle="mainmodal">ATT-PRJ</a>
			<a href="https://vision.bimmapping.com/exportDevis/indexx.php"  class="btn btn-primary" data-toggle="mainmodal">Exporter 2</a>

		<a type="button" class="btn btn-success" href="<?=base_url()?>exporter/devis_as_excel"><?=$this->lang->line('application_export')?></a>
		<!--<a href="<?=base_url()?>invoices" class="btn btn-primary"><?=$this->lang->line('application_invoices');?></a> -->
		<!--<a href="<?=base_url()?>boncommande" class="btn btn-primary"><?=$this->lang->line('application_commande');?></a>
		<a href="#" class="btn btn-primary"><?=$this->lang->line('application_livraison');?></a>-->
		<div class="btn-group pull-right-responsive margin-right-3">
			 <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
            <?php $last_uri = $this->uri->segment($this->uri->total_segments());
				if($last_uri != "estimates" && is_numeric($last_uri) == false){
					if($this->lang->line('application_'.$last_uri) == false) { 
						echo urldecode($last_uri); 
					} else {
						echo $this->lang->line('application_'.$last_uri);
					}
					}else{echo $this->lang->line('application_all');} ?> <span class="caret"></span>
        </button>
			<ul class="dropdown-menu pull-right" role="menu">
				<?php foreach ($submenu as $name=>$value):?>
				<li><a id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" 
				<?php $filter = substr($val_id[1], 0, 6);
					$val_id[1] = str_replace($filter , '', $val_id[1]);
						$value = $val_id[0].'/'.$filter.'/'.$val_id[1];  ?> href="<?=site_url($value);?>"><?=$name?></a></li>
	        <?php endforeach;?>
			</ul>
		</div>
		<?php $years = array_combine(range(date("Y"), 2013), range(date("Y"),2013)); ?>
		<div class="btn-group pull-right-responsive margin-right-3">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">	
				<?php $last_uri = $this->uri->segment($this->uri->total_segments());			
					if($last_uri != "estimates" && is_numeric($last_uri) == true){
						echo  $last_uri; 
					}else
					{
						echo  date("Y");
					} ?> <span class="caret"></span>
			</button>
			<ul class="dropdown-menu pull-right" role="menu">
				<?php foreach($years as $year){ ?>
							<li><a href="<?=base_url()?>estimates/filter/False/<?=$year?>"><?=$year;?></a></li>
				<?php  }?>
			  </ul>
		</div>
	</div>
	<!-- tableau recap des devis -->
	<div class="row">
		<div class="table-head"><?=$this->lang->line('application_estimate');?></div>
		<div class="table-div">
			<table class="dataSorting table" id="estimates" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
				<thead>
					<th width="10%">DV 
						<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAABM0lEQVR4nO3YP0oDQRQH4E8NFkKsLCxS2FhYWVl5AL2AloJNrmBt5xVs7QQPoIV/MAQLsbSxsbGzMqAiYiILL2AhKiERZ5wPHmyyGzK/Zfft7FAURVEURfHvHeMK86mfiV5UB+syCNKL2sWkBPWiNvAY25eYk2iQygKu4/M9ViUapFLHfnzXxQ4mJBikr4mX2HeCWYkGqSzhNvbfYVmiQSozOIxjXrGFsUH/rP1Jmxx2fWUc23iLYw8wPUiQUYc4/+E4VqKbVb+5weKgQf6Cxocr5DmaQpJBKrVoy90Y1x6mJBikbw0PMbYzmQQ5TTFILYdLqzGMm31U1fqt9tvK5YGYzRRllLKfNDZTn8bXc3ixWsjhVXcTT7HdjudFUrJbDuqkvkB3hIsclkyLoiiKoih87x2cD+ALQSWjjwAAAABJRU5ErkJggg==" width="20px" height="20px">
						
						ATT
					</th>
					<th width="10%">ATT / PRJ</th>
					<th  width="10%"><?=$this->lang->line('application_estimate_id');?></th>
					<th width="10%">Réference projet</th>
					<th width="10%"><?=$this->lang->line('application_client');?></th>
					<th width="10%"><?=$this->lang->line('application_asset');?></th>
					<th width="10%"><?=$this->lang->line('application_issue_date');?></th>
					<th  width="10%"><?=$this->lang->line('application_total_ttc');?></th>
					<th width="5%">Etat</th>
					<th width="1%"><?=$this->lang->line('application_action');?></th>
					<th width="0%"></th>
				</thead>
				<?php
				foreach ($estimates as $value):
					$chiffre = $value->currency; 
					$change_date = "";				
					
					 ?>
					<tr id="<?=$value->id;?>" >
						<!-- poubelle + pdf -->
						<td class="option" width="10%">
							<!-- bouton poubelle -->
							<a type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>estimates/delete/<?=$value->id;?>'>
								<?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?>
								</b>"><i class="fa fa-trash" title="Supprimer"></i>
							</a>
							<!-- bouton pdf -->
							<a target="_blank" href="<?=base_url()?>estimates/preview/<?=$value->id;?>/show" class="btn-option">
								<i class="" title="PDF"><img src="<?=base_url()?>assets/blueline/images/pdf.png" alt=""></i>
							</a> 
							<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAABM0lEQVR4nO3YP0oDQRQH4E8NFkKsLCxS2FhYWVl5AL2AloJNrmBt5xVs7QQPoIV/MAQLsbSxsbGzMqAiYiILL2AhKiERZ5wPHmyyGzK/Zfft7FAURVEURfHvHeMK86mfiV5UB+syCNKL2sWkBPWiNvAY25eYk2iQygKu4/M9ViUapFLHfnzXxQ4mJBikr4mX2HeCWYkGqSzhNvbfYVmiQSozOIxjXrGFsUH/rP1Jmxx2fWUc23iLYw8wPUiQUYc4/+E4VqKbVb+5weKgQf6Cxocr5DmaQpJBKrVoy90Y1x6mJBikbw0PMbYzmQQ5TTFILYdLqzGMm31U1fqt9tvK5YGYzRRllLKfNDZTn8bXc3ixWsjhVXcTT7HdjudFUrJbDuqkvkB3hIsclkyLoiiKoih87x2cD+ALQSWjjwAAAABJRU5ErkJggg==" width="20px" height="20px">
						
							<a target="_blank" href="<?=base_url()?>estimates/previewe/<?=$value->id;?>/show" class="btn-option">
								<i class="" title="PDF"><img src="<?=base_url()?>assets/blueline/images/pdf.png" alt=""></i>
							</a>

						
						</td>	
						

	<td class="option" width="10%">
							<!-- bouton poubelle -->
			
							<!-- bouton pdf -->
							<a target="_blank" href="<?=base_url()?>estimates/previewb/<?=$value->id;?>/show" class="btn-option">
								<i class="" title="PDF"><img src="<?=base_url()?>assets/blueline/images/pdf.png" alt=""></i>
							</a>
						</td>						
						<td width="10%" class="hidden-xs" hidden><?=$value->id;?></td>
						<!-- N° devis -->
						<td width="10%"><?=$value->estimate_num;?></td>
						<td width="10%"><?=$value->project_ref;?></td>
						<!-- Client -->
						<td   width="10%">
							<span class="label label-info">
								Quarta
								<!-- utilisation d'une fonction du helper my_function_helper (ne pas oublier de l'ajouter dans config/autoload) 
								 <?php  echo ($value->company->name); ?>-->
							</span>
						</td>
						<!-- L'objet du devis -->
						
						<td  width="10%" class="<?php echo ' " title="'.$value->subject; ?>">
							<?=$value->subject?> <?php echo '  '.$value->project_name; ?>
						</td>
								
						<!-- Date émission -->
						<td width="10%" class="hidden-xs">
							<span><?php $unix = human_to_unix($value->issue_date.' 00:00'); echo '<span class="hidden">'.$unix.'</span> '; echo date($core_settings->date_format, $unix);?></span>
						</td>
						<!-- Total TTC -->
						<?php $this->load->helper('mydbhelper_helper');
						$company = getcompany($value->company_id);
						$ht = $value->sumht; 
						$ttc = $value->sum;  
						/*if($company->timbre_fiscal == 0){
							$ht = $ht + $value->timbre_fiscal; 
						} */
						?>
						<td width="10%" class="hidden-xs"><?= ($company->tva == 1) ? number_format($ht, $chiffre, '.', ' ') : number_format($ttc, $chiffre, '.', ' ') ?></td>
						<!-- Etat -->
						<td  width="10%" class="<?=$value->status?>"><?php get_etat_color(intval($value->status)) ?> </td>
						
						<td class="option" width="12%">
							<div class="dropdown">
								<button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
								   <i class="fa fa-cogs"></i> 
								</button>
								<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
								  <li role="presentation"><a href="<?=base_url()?>estimates/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"> Modifier</i></a></li>
								  
									<li role="presentation">
									  	<a href="<?=base_url()?>estimates/view/<?=$value->id;?>" class="btn-option"><i class="fa fa-eye" title="Visualisez"> Visualisez</i></a>
									</li>
									<li role="presentation">
										<a href="<?=base_url()?>estimates/facture/<?=$value->id?>" class="btn-option"><i class="fa fa-files-o" title="Facturé"> Facturé</i></a>
									</li>
									<li role="presentation">
										<a  href="<?=base_url()?>estimates/duplicate/<?=$value->id?>" class="btn-option"><i class="fa fa-clone" aria-hidden="true" title="Dupliquer"> Dupliquer</i></a>
									</li>
									<!--sent mail-->
									<li role="presentation"><a  href="<?=base_url()?>estimates/sendfiles/<?=$value->id?>" class="btn-option"><i class="fa fa-envelope" aria-hidden="true" title="Sent"> <?=$this->lang->line('application_sent_to');?></i></a></li>						
								</ul>
							</div>
						</td>
					</tr>
					<?php endforeach;?>
			</table>
		</div>
	</div>
</div>

<script>
                var document = '<?= $document ?>';
                var base_url = '<?= base_url() ?>';

                $(function() {
                    $('.last-in-row').popover({
                        html: true,
                        content: $(" #monthSelector").html()
                    });
                    $year = $('#id').html();
                    $length = $(".dropdown-item").length;
                    for (var i = 0; i < $length; i++) {
                        $(".dropdown-item").eq(i).attr("href", base_url + "estimates?document=" + document);
                    }
                    $(document).ready(function() {
                        $('#service_filter').change(function() {
                            var select = $(this).val();
                            var base_url = '<?php echo base_url(); ?>';
                            if (select !== 'all') window.location.href = `${base_url}estimates?document=${select}`;
                            else window.location.href = `${base_url}estimates`;
                        });

						$('#service_filterr').change(function() {
                            var select = $(this).val();
                            var base_url = '<?php echo base_url(); ?>';
                            if (select !== 'all') window.location.href = `${base_url}estimates?department=${select}`;
                            else window.location.href = `${base_url}estimates`;
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