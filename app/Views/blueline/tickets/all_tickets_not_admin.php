<?php   $current_user=$this->db->select('*')
                ->from('users')
                ->where('id',$this->session->userdata["user_id"])
                ->get()
				->result();

				$current_name=	$current_user[0]->firstname	 ;
				$current_id=	$current_user[0]->id	 ;

				?>



<div class="row">
	<div class="table-head">TÂCHES</div>
		<div class="table-div">
			<table style="cursor: default" class="wb-tables table  table-hover table-responsive" id="tickets" cellspacing="0" cellpadding="0">

				<thead>

					<tr>
					<th>Actions</th>

					<th style="width:5%">Référence</th>
						<th style="width:20%">Sujet</th>
						<th style="width:20%">Projet</th>
						<th style="width:10%">Date de début</th>
						<th style="width:10%">Date de fin</th>
						<th style="width:15%" >Propriétaire(s)</th>
						<th style="width:10%">Catégorie</th>


					</tr>
				</thead>
			</table>
		</div>
	</div>
	</div>
	<div   class="hidden" id="current_id"><?=$current_id?></div>
	<script type="text/javascript">

 
$(document).ready(function() {

    //datatables
  var table = $('#tickets').DataTable({ 
			"destroy": true,
			"search": {
							"search": "<?=$current_user[0]->firstname?>"
						},
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
				var Afficher = '<a  href="<?=base_url()?>ctickets/view/'+data+'" class="btn btn-primary"><i class="fa fa-eye" aria-hidden="true" title="Afficher"></i></a>';
				return Afficher ;
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
				return '<span class="label label-info">'+full[2]+'</span>';
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
			// sujet de collaborater
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
		
    //click tr
	$('.dataTable').on('click', 'tbody td', function() {
            //to get currently clicked row object
            var rowx  = $(this).parents('tr')[0];
            var base_url = '<?php echo base_url() ?>';
            //for row data
            var id = table.row(rowx).data()[0];
            window.location = base_url+"ctickets/view/"+id;
           
        });
	

	
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
//refresh datatables
$('#btn-reload').on('click', function(){
    	table.search("").draw();
		$('#ddCategorie').val("");
		$('#ddtache').val("");


    });

	var current = $("#current_id").text();
		if((current !== "53") || (current !== "1") || (current !== "34") || (current !== "37") || (current !== "35")){
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
