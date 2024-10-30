<?php   
$attributes = array('class' => '', 'id' => '_company');
echo form_open_multipart($form_action, $attributes);

$core_settings=Setting::find(array("id_vcompanies"=>$_SESSION['current_company'])); 
?>



    <div class="col-sm-12 col-md-12 main"> 
 
<div class="row" >
  
<div class="form-group">
    <label for="pays"><?=$this->lang->line('application_liste_des_annes');?></label>

                    <select name="annee" id="annee" class="chosen-select">

                    <?php

                            for($i = $mindate ; $i <= $datenow ; $i++)
                            { 
                                echo("<option value='$i'>$i</option>");
                            }

                    ?>
                        

                    </select>
</div>

 <div class="form-group">

    <label for="pays"><?=$this->lang->line('application_liste_des_moins');?></label>


        <div class="table-div">
        <table class="data table"  rel="<?=base_url()?>" cellspacing="0" cellpadding="0" width ="100px">
        <thead>
            <th></th>
            <th><?=$this->lang->line('application_moins');?></th>

        </thead>


        <tr>

                        <td><input type="checkbox" name="Janvier"></td>
                        <td value="1">Janvier</td>

        </tr>
         <tr>

                         <td><input type="checkbox" name="Fevrier"></td>
                        <td value="2">Fevrier</td>

          </tr>

           <tr>

                         <td><input type="checkbox" name="Mars"></td>
                        <td value="3">Mars</td>
                        
        </tr>
        <tr>

                         <td><input type="checkbox" name="Avril"></td>
                        <td value="4">Avril</td>

         </tr>

                                <tr>

                         <td><input type="checkbox" name="Mai"></td>
                        <td value="5">Mai</td>
                        </tr>
                                <tr>

                         <td><input type="checkbox" name="Juin"></td>
                        <td value="6">Juin</td>
                        </tr>

                                                        <tr>

                         <td><input type="checkbox" name="Juillet"></td>
                        <td value="7">Juillet</td>
                        </tr>
                                <tr>

                         <td><input type="checkbox" name="Aout"></td>
                        <td value="8">Aout</td>
</tr>
                                <tr>

                         <td><input type="checkbox" name="Septembre"></td>
                        <td value="9">Septembre</td>
                        </tr>

                                <tr>

                         <td><input type="checkbox" name="Octobre"></td>
                        <td value="10">Octobre</td></tr>

                                <tr>

                         <td><input type="checkbox" name="Novembre"></td>
                        <td value="11">Novembre</td>
                        </tr>

                                <tr>

                         <td><input type="checkbox" name="Decembre"></td>
                        <td value="12">Decembre</td>
                </tr>
       

        </table>

        </div>








                
</div> 



 <div class="form-group">

    <label for="pays" > <?=$this->lang->line('application_liste_des_salaries');?></label>


        <div class="table-div">
        <table class="data table"  rel="<?=base_url()?>" cellspacing="0" cellpadding="0" width ="100px">
        <thead style="background-color: #DE2821 ; color: white" > 

            <th><img src="https://image.flaticon.com/icons/svg/180/180012.svg" width="25px"></th>
            <th><?=$this->lang->line('application_id');?></th>
             <th><?=$this->lang->line('application_name');?></th>
              <th><?=$this->lang->line('application_firstname');?></th>

        </thead>

                <?php foreach ($salaries as $value) { 
                        $a12 = $value->id;
                    ?>

        <tr>

        <?php

        echo("<td><input type='checkbox' name='$a12' /></td>");
        
        ?>

            <td class="hidden-xs"><?php 
                    if(isset($value->id))
                        { echo $value->id;}
                    ?>
            
            </td>
                        <td class="hidden-xs">

                        <?php 
                    if(isset($value->nom))
                        { echo $value->nom;}
                   ?>
            
            </td>

                        
                        <td class="hidden-xs">

                        <?php 
                    if(isset($value->prenom))
                        { echo $value->prenom;}
                   ?>
            
            </td>

            </tr>

            <?php } ?>


       

        </table>

        </div>








                
</div> 



</div>
</div>

   <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-success" value="<?=$this->lang->line('application_save');?>"/>
       
        <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
      </div>
<?php echo form_close(); ?>


