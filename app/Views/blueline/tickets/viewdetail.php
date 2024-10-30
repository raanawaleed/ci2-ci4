<div class="row tab-pane active" role="tabpanel" id="projectdetails-tab">
   <div class="col-xs-12 col-md-3">
	 	<div class="table-head"><?=$this->lang->line('application_ticket_details');?>
		 <div class="col-md-1 col-xs-6" style="float:right;  ">
				<a style=""  href="<?=base_url()?>ctickets/editTicket/<?=$ticket->id;?>" data-toggle="mainmodal" title="Modifier la tâche " data-target="#mainModal"><i class='fa fa-edit' aria-hidden='true'></i></a>
				</div>
		 </div>

			<div class="subcont">
				<!-- Détail du projet -->
				<ul class="details">
				<?php $lable = ""; 
				//var_dump($ticket->from);?>

				<!-- Projet -->
					<li><span><?=$this->lang->line('application_project');?> 
					</span>
						<?php if(!isset($ticket->project_id->name)): ?> 	
							<a  data-toggle="tooltip" title="<?php echo $ticket->project_id->name ?>" href="#" class="label label-info"><?php echo $this->lang->line('application_no_project_assigned');  ?>
						</a>
						<?php else : ?>
							<a data-toggle="tooltip" data-placement="left" title="<?php echo $ticket->project_id->name ?>" class="label label-info"
							href="<?=base_url() .'projects/view/'.$ticket->project_id->id ?>">
							<?php echo $ticket->project_id->project_num.'--'.$ticket->project_id->name; ?>
							</a>
						<?php endif; ?>
					</li> 

			<!-- Sous Projet -->
			<?php //var_dump($ticket);?>
					<li><span><?=$this->lang->line('application_sous_projets');?> </span>
						<?php if(!isset($ticket->sub_project_id->name)): ?> 	
							<a  data-toggle="tooltip" title="<?php echo $ticket->sub_project_id->name ?>" href="#" class="label label-warning">
								<?php echo $this->lang->line('application_no_project_assigned'); ?>
							</a>
						<?php else : ?>
							<a data-toggle="tooltip" data-placement="left" title="<?php echo $ticket->sub_project_id->name ?>" class="label label-warning" href="#">
							<?php echo $ticket->sub_project_id->name; ?>
							</a>
						<?php endif;  ?>
					</li>

					<li><span>Priorité</span>
						<span class="label label-important <?php echo $lable; ?>"><?=$this->referentiels->getReferentielsById($ticket->priority)->name;?></span>
					</li>
<?php //var_dump($this->referentiels->getReferentielsById($ticket->priority));?>
					<li><span><?=$this->lang->line('application_status');?></span> 
						<span class="label <?php echo $lable; ?>"><?=$ticket->status;?></span>
					</li>
					<li><span><?=$this->lang->line('application_etat');?></span>
						<span class="label <?php echo $lable; ?>"><?=$this->referentiels->getReferentielsById($ticket->etat_id)->name;?></span>
					</li>

					<!--<li><span><?=$this->lang->line('application_from');?></span> <?php if(isset($ticket->from)){ echo '<a class="tt" title="'.$ticket->from.'">'.$ticket->from.'</a>';
					$emailsender = $ticket->client->email;
					}
					else{
						$explode = explode(' - ', $ticket->from);
						if(isset($explode[1])){
						$emailsender = $explode[1];
						$emailname = str_replace('"', '', $explode[0]);
						$emailname = str_replace('<', '', $emailname);
						$emailname = str_replace('>', '', $emailname);
						$emailname = explode(' ', $emailname);
						$emailname = $emailname[0];
					}else{ $explodeemail = "-"; }
					echo '<a class="tt" title="'.addslashes($emailsender).'">'.$emailname.'</a>'; } ?></li>-->
					<!--<li><span><?=$this->lang->line('application_queue');?></span> <?php if(isset($ticket->queue->name)){ ?><?=$ticket->queue->name;?> <?php } ?></li>-->
					
					<li ><span>Date début</span> <?php $unix = human_to_unix($ticket->start.' 00:00'); echo date($core_settings->date_format, $unix); ?></li>

					<li ><span><?=$this->lang->line('application_deadline');?></span> <?php $unix = human_to_unix($ticket->end.' 00:00'); echo date($core_settings->date_format, $unix); ?></li>

					<li><span><?=$this->lang->line('application_owner');?></span> <?php if(isset($ticket)){ ?><?=$ticket->collaborater_id->firstname;?> <?=$ticket->collaborater_id->lastname;?> <?php } else{ echo "-";} ?></li>
					<li><span>Temps</span><p class="label label-info"><?=$periode;?></p></li>
					<li><span>Quantité :</span><p class="label label-info">
						<?php 
							if($ticket->surface!= 0 && $ticket->longueur!= 0) echo ($ticket->surface.' m²'.'&nbsp&nbsp'. $ticket->longueur.' ml'); 
							elseif($ticket->surface!= 0) echo ($ticket->surface.' m²') ;   
							 else echo ($ticket->longueur.' ml') ;
						?>
					</p></li>

					<li><span>Rendement :</span><p class="label label-info">
						<?php 
							if($ticket->surface!= 0 && $ticket->longueur!= 0) echo (round($ticket->surface/$periode).' m² /heures'.'&nbsp&nbsp&nbsp&nbsp&nbsp'. round($ticket->longueur/$nb_heures).' ml / heures'); 
							elseif($ticket->surface!= 0) echo (round($ticket->surface/$periode).' m² / heures');   
							else echo (round($ticket->longueur/$periode).' ml / heures');
						?>
					</p></li>

					<li><span>CRÉÉ PAR<br><p class="label label-info"><?=$ticket->from;?></li>
					<li><span>CRÉÉ LE<br><p class="label label-info"><p class="label label-info"> <?php echo date($core_settings->date_format.'  '.$core_settings->date_time_format, $ticket->created); ?></li>

				</ul>

			</div>
		</div>
			<div class="col-xs-12 col-md-9">
				<?php if($ticket->status != 'closed'){ ?>
					<a class="btn btn-success" style="margin-top: -2px;" id="note" data-toggle="mainmodal" href="<?=base_url()?>ctickets/article/<?=$ticket->id;?>/add"><?=$this->lang->line('application_add_note');?></a>
				<?php } ?>
	 		 	<div class="btn-group nav-tabs hidden-xs ">
					<!--<a class="btn btn-primary backlink" id="back" href="<?=base_url()?>ctickets"><?=$this->lang->line('application_back');?></a>-->
					<?php if($ticket->status != 'closed'){ ?>
						<a class="btn btn-primary" id="assign" data-toggle="mainmodal" href="<?=base_url()?>ctickets/assign/<?=$ticket->id;?>"><?=$this->lang->line('application_assign');?></a>
						<a class="btn btn-primary" id="type" data-toggle="mainmodal" href="<?=base_url()?>ctickets/etat/<?=$ticket->id;?>"><?=$this->lang->line('application_etat');?></a>
						<a class="btn btn-primary" id="status" data-toggle="mainmodal" href="<?=base_url()?>ctickets/status/<?=$ticket->id;?>"><?=$this->lang->line('application_status');?></a>
						<a class="btn btn-primary" id="close" data-toggle="mainmodal" href="<?=base_url()?>ctickets/close/<?=$ticket->id;?>"><?=$this->lang->line('application_close');?></a>
					<?php } ?>
				</div>

				<div class="col-md-3 col-xs-3" style="float:right; ">

<a style="" href="<?=base_url()?>ctickets" class="btn btn-warning right">Liste des <?=$this->lang->line('application_ctickets');?></a>
</div>
				
			




	        <div class="btn-group pull-left visible-xs">
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			    <i class="fa fa-edit" title="Modifier"></i> <!--<span class="caret"></span>-->
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a class=" backlink" id="back" href="#"><?=$this->lang->line('application_back');?></a>
					<li><a id="note" data-toggle="mainmodal" href="<?=base_url()?>ctickets/article/<?=$ticket->id;?>/add"><?=$this->lang->line('application_add_note');?></a></li>

				</ul>
			</div>
			<!--Reply-->
	        <div class="message-content-reply fadein no-padding">
				<?php
				$attributes = array('class' => '', 'id' => '_article');
				echo form_open('ctickets/article/'.$ticket->id.'/add', $attributes);
				?>
				<input id="ticket_id" type="hidden" name="ticket_id" value="<?php echo $ticket->id; ?>" />
				<input type="hidden" name="to" value="<?php if($ticket->user_id != 0){echo addslashes($ticket->user->email);}?>">
				<input type="hidden" name="notify" value="yes">
				<input type="hidden" name="subject" value="<?=$ticket->subject;?>">
				<textarea id="reply" name="message" class="summernote" placeholder="<?=$this->lang->line('application_quick_reply');?>"></textarea>
				<!-- file-->
				<input id="uploadBtn" type="file" name="userfile" class="upload" />
				<div class="textarea-footer">
				<button id="send" name="send" class="btn btn-primary button-loader"><?=$this->lang->line('application_send');?></button>
				</div>
				<?php echo form_close(); ?>
			</div>



	        <div class="article-content">
				<h4><p class="truncate">[#<?=$ticket->reference;?>] <?=$ticket->subject;?>			
					<a href="<?=base_url()?>ctickets/copyTicket/<?=$ticket->id;?>" class="btn-option tt right" title="Copier la tache " data-toggle="mainmodal"><i class="fa fa-copy"></i></a>
</p></h4>

				<hr>

				<div class="article">

					<?=$ticket->text;?>
					<?php if(isset($ticket->ticket_has_attachments[0])){echo '<hr>'; } ?>
					<?php foreach ($ticket->ticket_has_attachments as $ticket_attachments):  ?>
                            <!--<a class="label label-info" href="<?=base_url()?>ctickets/attachment/<?php echo $ticket_attachments->savename; ?>"><?php echo $ticket_attachments->filename; ?></a>-->
                            <img style="width:100px;display:block;margin-top:4px;cursor: pointer;" src="<?=base_url()?>files/media/<?php echo $ticket_attachments->savename; ?>"/>
                            <a class="label label-info" href="<?=base_url()?>files/media/<?php echo $ticket_attachments->savename; ?>"><?php echo $ticket_attachments->filename; ?></a>
                    <?php endforeach;?>

				</div>
			</div>
				<?php
			    $i = 0;
			    foreach ($ticket->ticket_has_articles as $value):
					$i = $i+1;
					if($i == 1){ ?>

					<?php } ?>
					<?php if($value->internal == "0"){ ?>
					<div class="article-content">
						<div class="article-header">
							<div class="article-title"><?=$value->subject;?>
							</div>
							<span class="article-sub"><?php $from_explode = explode(' - ', $value->from); echo '<span class="tt" title="'.$from_explode[1].'">'.$from_explode[0].'</span>'; ?></span>
							<span class="article-sub"><?php echo date($core_settings->date_format.' '.$core_settings->date_time_format, $value->datetime); ?></span>
						</div>
						<div class="article-body">
							<?php $text = preg_replace('#(^\w.+:\n)?(^>.*(\n|$))+#mi', "", $value->message); echo $text;?>

							<?php if(isset($value->article_has_attachments[0])){echo "<hr>"; } ?>
							<?php foreach ($value->article_has_attachments as $attachments):  ?>
									<!--<a class="label label-success" href="<?=base_url()?>ctickets/articleattachment/<?php echo $attachments->savename; ?>"><?php echo $attachments->filename; ?></a>-->
									<img src="<?=base_url()?>ctickets/articleattachment/<?php echo $attachments->savename; ?>" style="width:100px;margin-top:4px;cursor: pointer;display:block;">
									<div class="label label-info"><?php echo $attachments->filename; ?></div>






							<?php endforeach;?>
						</div>
					</div>
			 		<?php } ?>
			  <?php endforeach;?>
			</div>
</div>


<style>
.modal{
	white-space:normal;

}
.product-image-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
    display: none;
}

.product-image-overlay .product-image-overlay-close {
    display: block;
    position: absolute;
    bottom: 16%;
    right: 5%;
    width: 40px;
    height: 40px;
    border-radius: 100%;
    border: 1px solid #eee;
    line-height: 35px;
    font-size: 20px;
    color: #eee;
    text-align: center;
    cursor: pointer;
    z-index:111;
}

.product-image-overlay img {
    width: auto;
    max-width: 80%;
    position: absolute;
    top: 50%;
    left: 50%;
    -webkit-transform: translate(-50%, -50%);
    -moz-transform: translate(-50%, -50%);
    -o-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
}
</style>


<script type="text/javascript">
		$('.content-area').append('<div class="product-image-overlay"><span class="product-image-overlay-close">x</span><img src="" /></div>');
		var productImage = $('.article-content img');
		var productOverlay = $('.product-image-overlay');
		var productOverlayImage = $('.product-image-overlay img');

		productImage.click(function () {
	    	var productImageSource = $(this).attr('src');

	    productOverlayImage.attr('src', productImageSource);
	    productOverlay.fadeIn(100);
	    	$('.content-area').css('overflow', 'hidden');

	    $('.product-image-overlay-close').click(function () {
	        productOverlay.fadeOut(100);
	        $('.content-area').css('overflow', 'auto');
	    });


	});

</script>