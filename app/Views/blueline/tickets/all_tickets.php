<head>
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

</head>

<?php   $current_user=$this->db->select('*')
                ->from('users')
                ->where('id',$this->session->userdata["user_id"])
                ->get()
				->result();

				$current_name=	$current_user[0]->firstname	 ;
				$current_id=	$current_user[0]->id	 ;

				?>


<div class="row">
		<div class="col-md-1">
		<a href="<?=site_url('ctickets/create')?>" class="btn btn-primary" data-toggle="mainmodal" id="new_ticket"> NOUVEAU </a>
		
</div>				
		<div class="col-md-1">
<a href="https://vision.bimmapping.com/exportTicket/indexx.php" class="btn btn-green" data-toggle="mainmodal">Exporter</a>		
</div>	
		
		<div class="col-md-8">

		<form>
			
			<div class="col-md-3"  id="select_categorie">
				<div class="form-group">
					<label>Catégorie de tâche </label>
						<select  id="ddCategorie" class="chosen-select" aria-label="Default select example" >
								<option value="" >Tous</option>
								<option value="95">BIM 2D</option>
								<option value="130">BIM 3D</option>
								<option value="96">MMS</option>
						</select>	
				</div>
			</div>
			<div class="col-md-4" id="select_tache">
				<div class="form-group">
					<label>Propriétaire de tâche </label>
						<select  id="ddtache" class="chosen-select" aria-label="Default select example">
								<option value="" >Tous</option>
								<option selected value="<?=$current_name?>">Mes tâches</option>
						</select>	
				</div>
			</div>
		</form>
		</div>



<div class="col-md-2">
<a type="button" href="<?=base_url()?>calendar_tickets" title="calendrier Projets" class="btn btn-success btn-lg"><span class="fa fa-calendar"></span><br>Calendrier Tâches</a>
</div>  
           
        
</div>
<div class="row">
	<div class="table-head">TÂCHES</div>
		<div class="table-div">
			<table style="cursor: default" class="wb-tables table  table-hover table-responsive dataTable" id="tickets" cellspacing="0" cellpadding="0">

				<thead>

					<tr>
						<th style="width:10%">Actions</th>
						<th style="width:5%">Référence</th>
						<th style="width:18%">Sujet</th>
						<th style="width:20%">Projet</th>
						<th style="width:10%">Date de début</th>
						<th style="width:12%">Date de fin</th>
						<th style="width:15%" >Propriétaire(s)</th>
						<th style="width:10%">Catégorie</th>


					</tr>
				</thead>
				<tbody class="body">
				</tbody>
			</table>
		</div>
	</div>
	</div>

	<script type="text/javascript" charset="utf-8">


 
$(document).ready(function() {

    //datatables
  var table = $('#tickets').DataTable({ 
			"destroy": true,
			"search": true,
			"processing": "encours....veuillez patienter", //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"responsive": true,
			"lengthMenu":false,	
			"autoFill": false,
			"dom": 'lFrtip',
			"autoWidth": true,
			"bJQueryUI": true,
			"SemanticUI":true ,
			"ordering":false,
			"bSearchable": true ,
			"order":[],
			"pageLength": 20,
			"dom": '<"top"fi>rt<"bottom"p><"clear">' ,	           
			"language": 
			{
				"search": "Recherche ",
				"lengthMenu": "Display _MENU_ records per page",
				"info": "Affichage _START_ à _END_ de total _TOTAL_ tâches",
				"infoFiltered": "( filtrés de _MAX_  tâches)",
				"infoEmpty": "Aucune donnée à afficher",
				"paginate": {
				"next": '<i class="fa fa-fw fa-long-arrow-right">',
				"previous": '<i class="fa fa-fw fa-long-arrow-left">' , 
				},	
 			},
			
		"columns": [
			{
			// Actions
			"render": function( data, type, full, meta ) {
				
				var Afficher = '<a id="view" href="<?=base_url()?>ctickets/view/'+full[0]+'" class="btn bn-sm btn-primary btview" data-id="'+full[0]+'"><i class="fa fa-eye" aria-hidden="true"  title="Afficher"> </i></a>';
				var Supprimer = '<a  href="<?=base_url()?>ctickets/deleteTicket/'+full[0]+'" class="btn bn-sm btn-danger bn-sm"><i class="fa fa-remove" aria-hidden="true"  title="Supprimer"> </i></a>';
							
				return Afficher+' '+Supprimer;
				}
		}
		,
		
		{
			
			// ref de ticket
			"render": function( data, type, full, meta ) {
				return '<span class="label label-info">'+data+'</span>';

				}
		}
		,
		{
			// sujet de ticket
			"render": function( data, type, full, meta ) {
				return '<span class="label label-info">'+data+'</span>';
				}
		}
		,
	

		{
			// projet de ticket
			"render": function( data, type, full, meta ) {
				return '<span class="label label-info">'+data+'</span>';
				}
		}
		,
		
		{
			// Colonne Date debut
			"render": function( data, type, full, meta ) {
				return '<span class="label label-info">'+data+'</span>';
				}
		},
 
		{
			// Colonne Date fin
			"render": function( data, type, full, meta ) {
				return '<span class="label label-info">'+data+'</span>';
				}
		},
		{
			// sujet de ticket
			"render": function( data, type, full, meta ) {
				return '<span class="label label-info">'+data+'</span>';
				}
		}
		,
		{
			// type de ticket
			"render": function( data, type, full, meta ) {
				var type="";
						if(data=="95")
						{
							type="BIM 2D"
						}
						if(data=="96")
						{
							type="MMS"
						}
						if(data=="130")
						{
						type="BIM 3D"
						}
						
				return '<span class="label label-info">'+type+'</span>';
				}
		}
		,
		
		],



        // Load data for the table's content from an Ajax source
        "ajax": {
			"type": "POST",
			"url": "<?php echo site_url('ctickets/getTicketsToDatatables');?>",

			
        },
    });
	
    
        $('.dataTable').on('click', 'tbody td', function() {
            //to get currently clicked row object
            var rowx  = $(this).parents('tr')[0];
            var base_url = '<?php echo base_url() ?>';
            //for row data
            var id = table.row(rowx).data()[0];
            window.location = base_url+"ctickets/view/"+id;
           
        })
	


	
	$('#ddCategorie').change(function () {
                table.search($('#ddCategorie').val());
                //hit search on server
                table.draw();
            });

			$('#ddtache').change(function () {
                table.search($('#ddtache').val());
                //hit search on server
                table.draw();
            });


	var current = $("#current_id").text();
		if((current !== "53") ||(current !== "34") || (current !== "37") || (current !== "35")){
			$('#select_tache').show();
			$('#select_categorie').show();
			$('#new_ticket').show();
			$('#btn-reload').show();

			//$('#delete').hide();



		}
		else{
			$('#select_tache').hide();
			$('#select_categorie').hide();
			$('#new_ticket').hide();
			$('#btn-reload').hide();
		}

   
});
</script>  
