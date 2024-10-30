 <div class="row">
	<div class="col-md-12">
		<h2><?=$company->name;?></h2> 
	</div>
</div>

<div class="row">
	<div class="col-md-12 marginbottom20">
		<div class="table-head">
			<?=$this->lang->line('application_détail_salarie');?>

			<span class="pull-right modifie"><a href="<?=base_url()?>gestionsalarie/update/<?=$idpnl;?>/view" class="btn btn-primary" data-toggle="mainmodal"><i class="icon-edit"></i> <?=$this->lang->line('application_edit');?></a></span>
            <span class="pull-right listesalarie" style="margin-right:10px;color:#"><a href="<?=base_url()?>gestionsalarie/" class="btn btn-warning"><i class="icon-edit"></i> <?=$this->lang->line('application_gestiondessalaries');?></a></span>
		</div>
<!-- begin foreach -->
 
<?php foreach ($item as $value):?>

		<div class="subcont">
			<ul class="details col-md-6">

				<li><span><?=$this->lang->line('application_name');?>:</span> <?php    
					 if(isset($value))
	                     {echo $value->nom;} 
	                  else {echo "-";}  ?>
                 </li>

				<li><span><?=$this->lang->line('application_firstname');?>:</span> <?php 
                                            if(isset($value))
                                                    {echo $value->prenom;} 
                                            else {echo "-";} 
                                            ?></li>

				<li><span><?=$this->lang->line('application_matricule');?>:</span> <?php    
					 if(isset($value))
	                     {echo $value->matricule;} 
	                  else {echo "-";}  ?>
                 </li>

				<li><span><?=$this->lang->line('application_cin');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->numerocin;} 
                                            else {echo "-";}  ?></li>

				<li><span><?=$this->lang->line('application_matricule_cnss');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->numerocnss;} 
                                            else {echo "-";}  ?></li>

				<li><span><?=$this->lang->line('application_address');?>1:</span> <?php                                              if(isset($value))
                                                    {echo $value->adresse1;} 
                                            else {echo "-";} ?></li>

				<li><span><?=$this->lang->line('application_address');?>2:</span> <?php                                              if(isset($value))
                                                    {echo $value->adresse2;} 
                                            else {echo "-";} ?></li>

				<li><span><?=$this->lang->line('application_zip_code');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->codepostal;} 
                                            else {echo "-";} ?></li>

				<li><span><?=$this->lang->line('application_date_naissance');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->datedenaissance;} 
                                            else {echo "-";}  ?></li>

				<li><span><?=$this->lang->line('application_nom_banque');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->nombanque;} 
                                            else {echo "-";}  ?></li>

				<li><span><?=$this->lang->line('application_rib');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->rib;} 
                                            else {echo "-";}  ?></li>
				
				<li><span><?=$this->lang->line('application_iban');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->iban;} 
                                            else {echo "-";} ?></li>			
			</ul>

			<span class="visible-xs"></span>

			<ul class="details col-md-6">
				
				<li><span><?=$this->lang->line('application_country');?>:</span> <?php  if(isset($value))
                                                    {echo $value->pays;} 
                                            else {echo "-";} ?></li>

				
				<li><span><?=$this->lang->line('application_numero');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->tel1;} 
                                            else {echo "-";}  ?></li>

				<li><span><?=$this->lang->line('application_skype');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->skype;} 
                                            else {echo "-";}?></li>

				<li><span><?=$this->lang->line('application_email');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->mail;} 
                                            else {echo "-";}  ?></li>

				<li><span><?=$this->lang->line('application_genre');?>:</span> <?php                                

                               if((isset($value->genre))&& ($value->genre != 0))
                                    {

                                               foreach($genre as $ref)
                                               {

                                                    if($value->genre == $ref->id )
                                                    {
                                                        echo "$ref->name";
                                                        break;
                                                    }

                                                

                                                }

                                    
                                    }



                                            else {echo "-";} ?></li>

				<li><span><?=$this->lang->line('application_Situation_familiale');?>:</span> <?php




                              if((isset($value->situationfamiliale))&& ($value->situationfamiliale != 0))
                                    {

                                               foreach($situations as $ref)
                                               {

                                                    if($value->situationfamiliale == $ref->id )
                                                    {
                                                        echo "$ref->name";
                                                        break;
                                                    }

                                                

                                                }

                                    
                                    }


                                            else {echo "-";} ?></li>

				<li><span><?=$this->lang->line('application_lieu_de_naissance');?>:</span> <?php                                            if(isset($value))
                                                    {echo $value->lieudenaissance;} 
                                            else {echo "-";} ?></li>
			
				<li><span><?=$this->lang->line('application_date_délivrance');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->datedelivrance;} 
                                            else {echo "-";} ?></li>

				<li><span><?=$this->lang->line('application_date_debut_embaucheee');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->date_debut_embauche;} 
                                            else {echo "-";} ?></li>


				<li><span><?=$this->lang->line('application_date_fin_embaucheee');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->date_fin_embauche;} 
                                            else {echo "-";} ?></li>


				<li><span><?=$this->lang->line('application_contrat_de_travail');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->type_contart;} 
                                            else {echo "-";} ?></li> 

				<li><span><?=$this->lang->line('application_fonction');?>:</span> <?php 

						foreach($fonctions as $fonction){

							if((int)$fonction->id == (int)$value->idfonction)
							{
								echo $fonction->libelle;
								break;
							}

						}
						

				?></li>




				<li><span><?=$this->lang->line('application_bil');?>:</span> <?php                                              if(isset($value))
                                                    {echo $value->bil;} 
                                            else {echo "-";} ?></li>	

				

				<?php if($company->timbre_fiscal > 0){ 
				echo "<li><span>".$this->lang->line('application_timbre')." : <span><br>";
				echo "<span style='color:red !important;'>".'Client exonéré du timbre fiscale pour la facturation'."<span></li>";} ?>

			</ul>
			<br clear="all">
		</div>
<?php endforeach;?>
<!-- end of foreach  -->
	</div>
</div>

<style>
@media screen and (max-width: 413px) {
    .btn-warning{
        font-size:8px !important;
       
    }
    .listesalarie{
         margin-right:0 !important;
    }
     .btn-primary{
        font-size:8px !important;
    }
}
@media screen and (max-width: 360px) {
   
    .listesalarie{
         margin-top:-24px !important;
    }
    .modifie{
        margin-right:0 !important;
    }
    
}
</style>