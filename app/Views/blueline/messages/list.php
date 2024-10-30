<style>
.styleStar {
    color:black !important; 
}
.styleDot {
	border-color: #75aad6 !important;
}
</style>
<?php  
if($message){
    foreach ($message as $value):
     ?>
	<li class="<?php if($filter){echo $filter;}else{echo $value->status."-dot";}?> hidden" data-link="<?=base_url()?>messages/view/<?=$value->id;?><?php if(isset($filter)){echo "/".$filter;} ?><?php if(isset($filter)){ if($filter == "Sent"){echo "/".$value->recipient->id;} } ?>">
		<div class="col col-1" id="<?=$value->id;?>">
			<span class="dot" id="<?=$value->id;?>"></span>
			<p class="title"><?php if(isset($value->sender_u)){echo $value->sender_u;}else{ echo $value->sender_c; } ?>
			</p>
			
			<span  id="<?=$value->id;?>" class="fa fa-star star<?=$value->id;?>" <?php 
			if($filter == false){
				if($value->new_created  == "1"){ ?> style="color:red" <?php }}?>>
			</span>
			<span  id="<?=$value->id;?>"  style="padding-left: 60px;">
				<?php if($filter == "Sent"){
					echo($value->recipient->firstname.' '.$value->recipient->lastname);
				}else { 
					echo($value->sender->firstname.' '.$value->sender->lastname);
				}?>
			</span>
		</div>
		<div class="col col-2" >
			<div class="subject" id="<?=$value->id;?>"><?php if($value->attachment != ""){ echo '<i class="fa fa-paperclip"></i>';}  ?> <?=$value->subject;?>
			</div>
			<div class="date" id="<?=$value->id;?>"><?php echo time_ago($value->time);?>
			</div>
		</div>
    </li>
 <?php endforeach;?>
  		<?php } else{ ?>
        <li style="padding-left:21px"><?=$this->lang->line('application_no_messages');?></li>
        <?php } ?> 
<script>
jQuery(document).ready(function($) {
$("#main .message-list li").removeClass("hidden").delay(300).addClass("visible");
	var cols = {},
	messageIsOpen = false;

	cols.showOverlay = function() {
		$('body').addClass('show-main-overlay');
	};
	cols.hideOverlay = function() {
		$('body').removeClass('show-main-overlay');
	};

	cols.showMessage = function() {
		$('body').addClass('show-message');
		messageIsOpen = true;
	};
	
	cols.hideMessage = function() {
		$('body').removeClass('show-message');
		$('#main .message-list li').removeClass('active');
		messageIsOpen = false;
	};

	cols.showSidebar = function() {
		$('body').addClass('show-sidebar');
	};
	
	cols.hideSidebar = function() {
		$('body').removeClass('show-sidebar');
	};
	
	// Show sidebar when trigger is clicked
	$('.trigger-toggle-sidebar').on('click', function() {
		cols.showSidebar();
		cols.showOverlay();
	});

	$('.trigger-message-close').on('click', function() {
		cols.hideMessage();
		cols.hideOverlay();
	});
	// When you click on a message, show it
	$('#main .message-list li').on('click', function(e) {	
		var item = $(this),
		target = $(e.target);
		var id = e.target.id;
		var filter = "<?=$filter;?>"; 
		if(filter == ""){
			var url = "<?=base_url()?>" + ('messages/viewMessage/' )+ id; 
			$.ajax({
				type: 'POST',
				dataType: "text",
				url: url,
				success: function (response) {  
					var doc = document.getElementById("countMessages");
					var star ="star"+ id; 
					var Nbrmessages = doc.innerHTML;
					if(response == "true"){		
						$("."+ star).addClass("styleStar");
						if(Nbrmessages > 0){
							document.getElementById("countMessages").innerHTML = Nbrmessages  - 1 ;
						}						
					}			
				}
			});
		}
		
		
		NProgress.start();
		if(target.is('label')) {
			item.toggleClass('selected');
		} else {
			if(messageIsOpen && item.is('.active')) {
				cols.hideMessage();
				cols.hideOverlay();
				NProgress.done();
			} else {
				if(messageIsOpen) {
					cols.hideMessage();
					item.addClass('active');
					setTimeout(function() {
						var url = item.data('link');
                        if (url.indexOf('#') === 0) {                             
						} else {
						$.get(url, function(data) { 
							$('#message').html(data);
						}).done(function() { 
							NProgress.done();
							cols.showMessage();                                     
						});
						}
					}, 300);
				} else {
					item.addClass('active');
					
					    var url = item.data('link');
                        if (url.indexOf('#') === 0) {
                                
						} else {
							$.get(url, function(data) { 
							$('#message').html(data);
						}).done(function() { 
								NProgress.done();
								cols.showMessage();
							   
						});
						}
				}
				cols.showOverlay();
			}
		}
	});
  
  	// This will prevent click from triggering twice when clicking checkbox/label
	$('input[type=checkbox]').on('click', function(e) {
		e.stopImmediatePropagation();
	});
	// When you click the overlay, close everything
	$('#main > .overlay').on('click', function() {
		cols.hideOverlay();
		cols.hideMessage();
		cols.hideSidebar();
	});
	
	$('.nano').nanoScroller();
	
});

</script>
