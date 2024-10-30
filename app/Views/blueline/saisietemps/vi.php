<?php $user =$this->user->id ;
				
				if ($user==1) {	
	
	
	
	
	?>
	<iframe width="100%"   height="700" allowfullscreen src="https://vision.bimmapping.com/eventt" ></iframe>  
	
				<?php	} else {
	
	$i=1;
  foreach($data as $row)
  { $i++;
 //var_dump($row->seraffectation);exit;
  ?>
				
<iframe width="100%"   height="700" allowfullscreen src="https://vision.bimmapping.com/tmp/tmp/dashboard.php?idd=<?php echo $row->seraffectation; ?>&idu=<?php echo $user; ?>" ></iframe>
   

	<?php }}?>