<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>


<?php  $user=$this->session->userdata["user_id"];
//$idS = $_GET['iddCategorie'] ;
//var_dump($idS); exit;
	$idsal =$this->user->salaries_id ;
	$sal =$this->salaries->id ;
$idp =$this->project->id ;
		//$this->view_data['project'] = $project; 
	//	$x=$this->view_data['salaries'] = $this->db->query('select seraffectation from salaries where id = '.$idsal.'');
			//$w=$this->view_data['project'] = project::find_by_sql('select * from project');
		//var_dump($x);exit;
?>




<div class="row">
<div class="col-md-1">

<a href="projects/create" class="btn btn-primary" data-toggle="mainmodal">Nouveau</a></div>
<div class="col-md-2">
<a href="https://vision.bimmapping.com/exportProjet/indexx.php" class="btn btn-primary" data-toggle="mainmodal">Exporter</a>
<button id="btn-reload" class="btn btn-warning right">Rafraîchir</button>

</div>
<div class="col-md-8">

		<form>
			<div class="col-md-3">
				<div class="form-group">
					<label>Etat de Projet</label>
						<select  id="ddProgress" class="chosen-select" aria-label="Default select example">
						        <option value="" >Tous</option>
								<option selected value="Ouvert" >Ouverts</option>
								<option value="Fermé">Fermés</option>
						</select>
			</div>
				</div>
				
			
				
				
						<div class="col-md-3">
				<div class="form-group">
					<label>Catégorie de Projet</label>
						<select  id="ddCategorie" class="chosen-select" aria-label="Default select example" >
								<option value="" >Tous</option>
								<option value="BIM2D">BIM 2D</option>
								<option value="BIM3D">BIM 3D</option>
								<option value="MMS">MMS</option>
						</select>	
				</div>
			</div>
			
				
			
			
			<div class="col-md-3">

				<div class="form-group">
					<label>Client</label>
						<select  id="ddClient" class="chosen-select" aria-label="Default select example">
								<option value="" >Tous</option>
								<option value="QUARTA" >QUARTA</option>
								<option value="opsia - France">opsia - France</option>
						</select>	
				</div>
			</div>
		</form>

		</div>

<div class="col-md-2">
<a type="button" href="<?=base_url()?>calendar" title="calendrier Projets" class="btn btn-success btn-lg"><span class="fa fa-calendar"></span><br>calendrier Projets</a>


</div>    
        
</div>

<div class="row">
	<div class="table-head"><?=$this->lang->line('application_projects');?></div>
		<div class="table-div">
	
		<table class="dataSorting table"  id="projects" cellspacing="0" cellpadding="0">
				<thead>

					<tr>
					<th style="width:10%">Actions</th>
						<th style="width:10px">Numéro</th>
						<th>Nom</th>
						<th>Client</th>
						<th>Date Début</th>
						<th>Date Fin</th>
						<th style="width:10px">Gatégorie</th>
						<th data-column="1" id="select-filter" style="width:10px">Etat</th>


					</tr>
				</thead>

			</table>
		</div>
	</div>
	


		<script type="text/javascript">


$(document).ready(function() {
	var table;

	$.fn.dataTable.ext.errMode = 'throw';	
		//datatables
		table = $('#projects').DataTable({ 	
			"destroy": true,
			
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
			
			"order":[],
			"pageLength": 20,
			"dom": '<"top"fi>rt<"bottom"p><"clear">' ,	           
			"language": 
			{
				"search": "RechercheRR ",
				"lengthMenu": "Display _MENU_ records per page",
				"info": "Affichage _START_ à _END_ de total _TOTAL_ projects",
				"infoFiltered": "( filtrés de _MAX_  projets)",
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
								
								var Afficher = '<a id="view" href="<?=base_url()?>projects/view/'+full[0]+'" class="btn bn-sm btn-primary btview" data-id="'+full[0]+'"><i class="fa fa-eye" aria-hidden="true"  title="Afficher"> </i></a>';
								var Supprimer = '<a  href="<?=base_url()?>projects/delete/'+full[0]+'" class="btn btn-danger"><i class="fa fa-remove" aria-hidden="true" title="Supprimer"> </i></a>';
							
										return Afficher +' '+ Supprimer;

								}
						},
			
						{
							// Num de projet
							"render": function( data, type, full, meta ) {
								return '<span  class="label label-info">'+data+'</span>';

								}
						}
						,
						{
							// nom de projet
							"render": function( data, type, full, meta ) {
								return '<span class="label label-info">'+data+'</span>';
								}
						}
						,

						{
							// Nom de client
							"render": function( data, type, full, meta ) {
								return '<span class="label label-info">'+data+'</span>';
								}
						}
						,
						{
							// Colonne Date dedut
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
							// Nom de nature
							"render": function( data, type, full, meta ) {
								return '<span class="label label-info">'+data+'</span>';
								}
						}
						,
						{
							// Colonne Statut
							"render": function(data, type, full, meta) {
								
									return '<span class="label label-warning">'+data+'</span>';
							
							}
						}
						,
						

			]			
	,


			// Load data for the table's content from an Ajax source
			"ajax": 
			{
				
				"url": "<?php echo site_url('projects/getProjects');?>",
				"type": "post",
				
				
			},
		

	});


	
	table.columns( '#select-filter' ).every( function () {
			$('#ddProgress').change(function () {
                table.search($('#ddProgress').val());
                table.draw();
            });
		});
		table.columns( '#select-filter' ).every( function () {
			$('#ddCategorie').change(function () {
                table.search($('#ddCategorie').val());
                table.draw();
            });
		});
		table.columns( '#select-filter' ).every( function () {
			$('#ddClient').change(function () {
                table.search($('#ddClient').val());
                table.draw();
            });
		});
	


	$('#btn-reload').on('click', function(){
    	table.search("").draw();
		$('#ddProgress').val("");
		$('#ddCategorie').val("");
		$('#ddClient').val("");

    });
	//click tr
$('.table').on('click', 'tbody td', function() {
            //to get currently clicked row object
            var rowx  = $(this).parents('tr')[0];
            var base_url = '<?php echo base_url() ?>';
            //for row data
            var id = table.row(rowx).data()[0];
            window.location = base_url+"projects/view/"+id;
           
        });
 

});

	
	

	</script>  