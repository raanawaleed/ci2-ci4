$(document).ready(function(){
    $('#service_filter').change(function(){
      var select = $(this).val();
      console.log(select);
      if(select == 'MMS')
      {
       $(".user").empty();
       console.log('datta change1');
         $('.user').append(<?php foreach(json_encode($data['usersmms']) as $user): ?>;+   
                               "<div class='item'>"+
                                  "<div class='name-column'>"+
                                   "<div class='name-wrap'>"+
                                   "<span class='name'>"+ <?php echo get_salaries_icon($user->genre)?><?php echo($user->nom.$user->prenom) ?> +"</span>"+
                                  "</div>"+
                                    "</div>"+
                                    "<div class='days'>"+<?php foreach ($data['data_m'] as $day): ?>
                                    <?php foreach ( $day as $d):?> 
                                           +"<div class='day week-num-'+<?php echo $d['week_num'] ?> <?php echo ($d['is_today'] ? 'today' : ''); ?>+'>"+
                                                  <?php $dateoff = get_dayoff_icon($user->id); foreach( $dateoff as $off): $f=array_values($off);  $date_s = date_parts_iso($f[0]); if(($d['num'] == $date_s[2])&&($data['month'] == $date_s[1])&&($data['year'] == $date_s[0])): ?>+
                                                    "<div class='num'><?php echo chouse_icon($f[1])?></div>"+
                                                       <?php endif ?>+
                                                   <?php endforeach; ?>+
                                                   </div>+
                                           <?php endforeach; ?>+
                                       <?php endforeach; ?>+
                                   "</div>"+ 
                                   "</div>"+
                                   <?php endforeach; ?>);     
      }
     if(select == 'BIM 2D')
      {
       $(".user").empty();
       console.log('datta change2');
         $('.user').append('<?php foreach($data["users2d"] as $user): ?>'+   
                               '<div class="item">'+
                                  '<div class="name-column">'+
                                   '<div class="name-wrap">'+
                                   '<span class="name"><?php echo get_salaries_icon($user->genre)?><?php echo($user->nom." ".$user->prenom) ?></span>'+
                                  '</div>'+
                                    '</div>'+
                                    '<div class="days">'+
                                       '<?php foreach ($data["data_m"] as $day): ?>'+
                                           '<?php foreach ( $day as $d):  ?>'+
                                           '<div class="day week-num-<?php echo $d["week_num"] ?> <?php echo ($d["is_today"] ? "today" : ""); ?>">'+
                                '<?php $dateoff = get_dayoff_icon($user->id); foreach( $dateoff as $off):  $f=array_values($off); $date_s = date_parts_iso($f[0]); if(($d["num"] == $date_s[2])&&($data["month"] == $date_s[1])&&($data["year"] == $date_s[0])):  ?>'+
                                                       '<div class="num"><?php echo chouse_icon($f[1])?></div>'+
                                                       '<?php endif ?>'+
                                                   '<?php endforeach; ?>'+
                                                   '</div>'+
                                           '<?php endforeach; ?>'+
                                       '<?php endforeach; ?>'+
                                   '</div>'+ 
                                   '</div>'+
                                   '<?php endforeach; ?>'); 
      }
      if(select == 'BIM 3D')
      {
       
       $(".user").empty();
       console.log('datta change3');
         $('.user').append('<?php foreach($data["users3d"] as $user): ?>'+   
                               '<div class="item">'+
                                  '<div class="name-column">'+
                                   '<div class="name-wrap">'+
                                   '<span class="name"><?php echo get_salaries_icon($user->genre)?><?php echo($user->nom." ".$user->prenom) ?></span>'+
                                  '</div>'+
                                    '</div>'+
                                    '<div class="days">'+
                                       '<?php foreach ($data["data_m"] as $day): ?>'+
                                           '<?php foreach ( $day as $d): ?>'+
                                           
                                           '<div class="day week-num-<?php echo $d["week_num"] ?> <?php echo ($d["is_today"] ? "today" : ""); ?>">'+
                                           '<?php $dateoff = get_dayoff_icon($user->id); foreach( $dateoff as $off):  $f=array_values($off); $date_s = date_parts_iso($f[0]); if(($d["num"] == $date_s[2])&&($data["month"] == $date_s[1])&&($data["year"] == $date_s[0])):  ?>'+
                                                       '<div class="num"><?php echo chouse_icon($f[1])?></div>'+
                                                       '<?php endif ?>'+
                                                   '<?php endforeach; ?>'+
                                                   '</div>'+
                                           '<?php endforeach; ?>'+
                                       '<?php endforeach; ?>'+
                                   '</div>'+ 
                                   '</div>'+
                                   '<?php endforeach; ?>'); 
      }
      if(select == 'all')
      {
       $(".user").empty();
         $('.user').append('<?php foreach($data["users"] as $user): ?>'+   
                               '<div class="item">'+
                                  '<div class="name-column">'+
                                   '<div class="name-wrap">'+
                                   '<span class="name"><?php echo get_salaries_icon($user->genre)?><?php echo($user->nom." ".$user->prenom) ?></span>'+
                                  '</div>'+
                                    '</div>'+
                                    '<div class="days">'+
                                       '<?php foreach ($data["data_m"] as $day): ?>'+
                                           '<?php foreach ( $day as $d): ?>'+
                                           
                                           '<div class="day week-num-<?php echo $d["week_num"] ?> <?php echo ($d["is_today"] ? "today" : ""); ?>">'+
   
                                           '<?php $dateoff = get_dayoff_icon($user->id); foreach( $dateoff as $off):  $f=array_values($off); $date_s = date_parts_iso($f[0]); if(($d["num"] == $date_s[2])&&($data["month"] == $date_s[1])&&($data["year"] == $date_s[0])):  ?>'+
                                                       '<div class="num"><?php echo chouse_icon($f[1])?></div>'+
                                                       '<?php endif ?>'+
                                                   '<?php endforeach; ?>'+
                                                   '</div>'+
                                           '<?php endforeach; ?>'+
                                       '<?php endforeach; ?>'+
                                   '</div>'+ 
                                   '</div>'+
                                   '<?php endforeach; ?>'); 
      }
   
    });
   })