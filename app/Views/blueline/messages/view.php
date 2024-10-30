<?php  if($conversation){ 
        $i = 0;
        foreach ($conversation as $value):
          $own = false;
      	  $unix = human_to_unix($value->time); 
          if("u".$this->user->id == $value->sender){ $own = " own";}else{$own = "-previous";}
          $i = $i+1;
		 
?>     
<?php if($i == "1" && $own != "own"){ ?>
<div id="message-nano-wrapper" class="nano ">
    <div class="nano-content">
       <div class="header">
			<div class="message-content-menu">
			  <?php if($value->status != 'deleted'){ ?>
              <a class="message-reply-button btn btn-success" role="button"><i class="fa fa-reply"></i> <?=$this->lang->line('application_reply');?></a>
			  <?php } ?>
			  <a class="btn btn-primary ajax-silent" href="<?=base_url()?>messages/mark/<?=$value->id?>" role="button">
              <?php if($value->status == 'Marked'){ ?>
              <i class="fa fa-star-o"></i> <?=$this->lang->line('application_unmark');?>
              <?php }else{ ?>
              <i class="fa fa-star"></i> <?=$this->lang->line('application_mark');?>
              <?php } ?>
              </a>
              <?php if($value->status != "deleted"){ ?>
              <a class="btn btn-danger" href="<?=base_url()?>messages/delete/<?=$value->id?>" role="button"><i class="fa fa-trash-o"></i> <?=$this->lang->line('application_delete');?></a>
              <?php } ?>
            </div>  
    <h1 class="page-title"><a class="icon glyphicon glyphicon-chevron-right trigger-message-close"></a><br><span class="dot"></span><?=$value->subject;?><span class="grey">(<?=$count;?>)</span></h1>
	<p class="subtitle"> 
	<?php if($value->recipient == $this->user->id){ 
		echo($this->lang->line('application_from'));
	}else {
		echo($this->lang->line('application_to'));
	}?><a href="#">
	<?php  if($value->recipient == $this->user->id){ 
		echo $sender;
	}else {
		echo $recipient;
	}
	?></a>,<?=$this->lang->line('application_started_on');?> <?php  echo date($core_settings->date_format.' '.$core_settings->date_time_format, $unix); ?></p>
	</div>
    <ul class="message-container">
        <div class="message-content-reply no-padding">
          <?php   
                $attributes = array('class' => 'ajaxform', 'id' => 'replyform');
                echo form_open_multipart('messages/write/reply', $attributes); 
                ?>
                <input type="hidden" name="recipient" value="<?=$value->sender;?>">
                <input type="hidden" name="subject" value="<?=$value->subject;?>">
                <input type="hidden" name="conversation" value="<?=$value->conversation;?>">
                <input type="hidden" name="previousmessage" value="<?=$value->id;?>">
				<div class="form-group">
					<label><?=$this->lang->line('application_reply');?></label>
					<textarea class="input-block-level summernote-ajax" id="reply" name="message"></textarea>     
					<div class="textarea-footer">
						<button id="send" name="send" class="btn btn-primary button-loader"><?=$this->lang->line('application_send');?></button>
				  
						<div class="pull-right small-upload"><input id="uploadFile" class="form-control uploadFile" placeholder="" disabled="disabled" />
							<div class="fileUpload btn btn-primary">
								<span><i class="fa fa-upload"></i><span class="hidden-xs"></span></span>
								<input id="uploadBtn" type="file" name="userfile" class="upload" />
							</div>
						</div>
					</div>
				</div>
				<?php echo form_close(); ?>
				<hr>
				<br>
				</div>
				<?php } ?> 
        <li class="item sent <?=$own;?>">
			<div class="details">
				<div class="left">
					<?php echo($value->sender_u); ?>
				</div>
				<div class="right"><?php  echo date($core_settings->date_format.' '.$core_settings->date_time_format, $unix); ?>
				</div>
			</div>
			<div class="message">
				<?=$value->message;?> 
				<?php if(isset($value->attachment)){ ?>
				<div class="attachments">
				<a class="label label-info" href="<?=base_url()?>messages/attachment/<?php echo $value->id; ?>"><?php echo $value->attachment; ?></a>
			</div>
            <?php } ?>
			</div>
        </li>
		<?php endforeach;?>
    </ul>
    </div>
    <?php } ?>
    <br><br>
<script>
jQuery(document).ready(function($) { 
	$('.nano').nanoScroller();
    $('.trigger-message-close').on('click', function() {
		$('body').removeClass('show-message');
		$('#main .message-list li').removeClass('active');
		messageIsOpen = false;
		$('body').removeClass('show-main-overlay');
	});
});
</script>