<?php
$year = $data['year'];
$month = $data['month'];
$department = $data['department'];
$daysCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);
?>

<link rel="stylesheet" href="<?= base_url() ?>assets/suivi/css/style.css">
<div class="col-sm-12  col-md-12 main">



				
			
    <div class="row">
        <a href="<?= base_url() ?>projects" class="btn btn-primary right">Liste des projets </a>
			<?php  $idadmin =$this->user->salaries_id ;
				
				if ($idadmin==NULL) {

				?>
					
        <div class="col-sm-3">
            <!-- salariés -->
            <div class="form-group">
                <label for="service_filter">Service</label>
                <select class="chosen-select" id="service_filter">
                    <option value="all">Tous les services</option>
                    <option value="mms" <?= $data['department'] == 'mms' ? 'selected' : '' ?>>MMS</option>
                    <option value="2d" <?= $data['department'] == '2d' ? 'selected' : '' ?>>BIM 2D</option>
                    <option value="3d" <?= $data['department'] == '3d' ? 'selected' : '' ?>>BIM 3D</option>
                </select>
            </div>
        </div>
		
		
		<?php } else {?>
		
		
		
		
		      <div class="col-sm-2">
            <!-- salariés -->
            <div class="form-group">
               	<?php	$i=1;
  foreach($dataa as $row)
  { $i++;?>
				
              
								
				<span>	  <label >Service :	
						
 <h4 style='align-items-center'><?php

 echo $row->seraffectation; ?></h4> </label>	</span>
							
					<?php }?>
            </div>
        </div>
		
		
		   <div class="col-sm-3">
            <!-- salariés -->
            <div class="form-group">
                <label for="service_filter">Service</label>
                <select class="chosen-select" id="service_filter">
                    
                    <option value="mms" <?= $data['department'] == 'mms' ? 'selected' : '' ?>>MMS</option>
                    <option value="2d" <?= $data['department'] == '2d' ? 'selected' : '' ?>>BIM 2D</option>
                    <option value="3d" <?= $data['department'] == '3d' ? 'selected' : '' ?>>BIM 3D</option>
                </select>
            </div>
        </div>
		<?php }?>
				
			
		
    </div>
	
	
	
    <div class="row">
        <div class="table-head"><?= $this->lang->line('application_calendar'); ?></div>
        <div class="table-div">
            <!--HEADER BUTTON -->
            <div class="header-button">
                <a href="<?php echo site_url('suivi?year=' . $data['prev_year'] . '&month=' . $data['prev_month'] . '&department=' . $department); ?>" class="btn btn-light btn-sm">
                    Previous month
                </a>

                <a href="<?php echo site_url('suivi?year=' . $data['next_year'] . '&month=' . $data['next_month'] . '&department=' . $department); ?>" class="btn btn-light btn-sm">
                    Next month
                </a>

                <button type="button" class="btn btn-light btn-sm last-in-row" onclick="select();" data-toggle="popover"><img src="<?php echo base_url('./assets/suivi/img/icon1.png'); ?>" class="icon small"></img><span>Select...</span> </button>
            </div>

            <div class="calendars">
                <div class="cali">
                    <div class="header">
                        <div class="name-column">
                            <div class="wrap">
                                <?= date('F', mktime(0, 0, 0, $month)) ?> <?= $year ?>
                            </div>
                        </div>

                        <div class="days">
                            <?php for ($i = 1; $i <= $daysCount; $i++) : ?>
                                <?php $today = date('Ymd') == $year . sprintf('%02d', $month) . sprintf('%02d', $i); ?>
                                <div class="day week-num-<?= date('N', strtotime($year . sprintf('%02d', $month) . sprintf('%02d', $i))) ?> <?php echo ($today ? 'today' : ''); ?>">
                                    <div class="num"><?= $i ?></div>
                                    <div class="week-name"><?= date('D', strtotime($year . sprintf('%02d', $month) . sprintf('%02d', $i))) ?></div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="user">
                        <?php foreach ($data['users'] as $user) : ?>
                            <div class="item" data-id="<?php echo ($user->id); ?>">
                                <div class="name-column">
                                    <div class="name-wrap">
                                        <span class="name itemname" data-toggle="popover" data-placement="right" data-trigger="focus" data-html="true" data-title="<?php echo ($user->nom . ' ' . $user->prenom) ?>" data-ville="<?php echo ($user->ville) ?>" data-affectation="<?php echo ($user->seraffectation) ?>" data-file="<?php echo ($user->file); ?>" data-phone="<?php echo ($user->tel1) ?>">
                                            <?php echo get_salaries_icon($user->genre) ?>
                                            <span class="user--name" data-value="">
                                                <?php echo ($user->nom . ' ' . $user->prenom) ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <div class="days">
                                    <?php for ($i = 1; $i <= $daysCount; $i++) : ?>
                                        <?php $today = date('Ymd') == $year . sprintf('%02d', $month) . sprintf('%02d', sprintf('%02d', $i)); ?>
                                        <?php $date = date('Y-m-d', mktime(0, 0, 0, $month, $i, $year)); ?>
                                        <?php $dateNumber = date('N', strtotime($year . sprintf('%02d', $month) . sprintf('%02d', $i)));
								?>

                                        <div class="day week-num-<?= date('N', strtotime($year . sprintf('%02d', $month) . sprintf('%02d', $i))) ?> <?php echo ($today ? 'today' : ''); ?>">
                                            <?php if ($dateNumber != 7) : ?>
                                                <div class="num iconnume" data-value="<?= suiviTooltipContent($data['projets'], $data['sujets'], $user->id, $date) ?>">
                                                    <?= iconeDeSuivi($data['events'], $user->id, $date); ?>
                                                </div>
                                            <?php endif; ?>

                                        </div>
											
                                    <?php endfor; ?>

                                </div>
                            </div>						
                        <?php endforeach; ?>
										
                    </div>
                </div>
            </div>

            <style>
                .calendars .header .wrap {
                    text-transform: capitalize;
                }

                .item .name-column {
                    height: 30px;
                }

                span.user--name {
                    text-overflow: ellipsis;
                    overflow: hidden;
                    display: inline-block;
                    width: 136px;
                    line-height: 1;
                    top: 5px;
                    position: relative;
                }
            </style>

            <script>
                var department = '<?= $department ?>';
                var base_url = '<?= base_url() ?>';

                $(function() {
                    $('.last-in-row').popover({
                        html: true,
                        content: $(" #monthSelector").html()
                    });
                    $year = $('#id').html();
                    $length = $(".dropdown-item").length;
                    for (var i = 0; i < $length; i++) {
                        $(".dropdown-item").eq(i).attr("href", base_url + "suivi?year=" + $year + "&month=" + [i] + "&department=" + department);
                    }
                    $(document).ready(function() {
                        $('#service_filter').change(function() {
                            var select = $(this).val();
                            var month = <?= $month ?>;
                            var base_url = '<?php echo base_url(); ?>';
                            if (select !== 'all') window.location.href = `${base_url}suivi?year=${$year}&month=${month}&department=${select}`;
                            else window.location.href = `${base_url}suivi?year=${$year}&month=${month}`;
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

                function increment() {
                    $year = $('#id').html();
                    $res = parseInt($year) + 1;
                    $('#id').html($res);
                    console.log($res);
                    $year = $('#id').html();
                    $length = $(".dropdown-item").length;
                    for (var i = 0; i < $length; i++) {
                        $(".dropdown-item").eq(i).attr("href", base_url + "suivi?year=" + $year + "&month=" + [i] + "&department=" + department);;
                    }
                }

                function decrement() {
                    $year = $('#id').html();
                    $res = parseInt($year) - 1;
                    $('#id').html($res);
                    console.log($res);
                    $year = $('#id').html();
                    $length = $(".dropdown-item").length;
                    for (var i = 0; i < $length; i++) {
                        $(".dropdown-item").eq(i).attr("href", base_url + "suivi?year=" + $year + "&month=" + [i] + "&department=" + department);;
                    }
                }

                function select() {
                    $year = $('#id').html();
                    $length = $(".dropdown-item").length;
                    for (var i = 0; i < $length; i++) {
                        $(".dropdown-item").eq(i).attr("href", base_url + "suivi?year=" + $year + "&month=" + [i] + "&department=" + department);;
                    }
                }
            </script>

            <div id="monthSelector" data-month="<?php echo $data['month'] ?>">
                <div class="controls d-flex justify-content-center align-items-center">
                    <button type="button" class="btn btn-light btn-sm" onclick="decrement();" data-prev>&laquo;</button>
                    <div class="year text-center mx-4" id="#mx-4">
                        <p id="id"><?php echo $data['year'] ?></p>
                    </div>
                    <button type="button" class="btn btn-light btn-sm" onclick="increment();" data-next>&raquo;</button>
                </div>
                <div class="months mt-2">
                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                        <a class="dropdown-item px-2 <?php echo (($i == $data['month']) ? 'active' : ''); ?>" href="<?php echo site_url('suivi?year=' . $data['year'] . '&month=' . $i . '&department=' . $department); ?>" data-id="<?php echo ($i); ?>" data-num="<?php echo $i; ?>"><?php echo get_month_name($i); ?></a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>