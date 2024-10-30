<?php   
$attributes = array('class' => '', 'id' => '_clients', 'autocomplete' => 'off');
echo form_open_multipart($form_action, $attributes); 
?>


<?php if(isset($view)){ ?>
<input id="view" type="hidden" name="view" value="true" />
<?php } ?>


<?php foreach ($item as $value):?>

                        <div class="form-group">
                                <label for="nom"><?=$this->lang->line('application_name');?>*</label>
                                <input type="text" name="nom" id="op" class="form-control" 
                                value="<?php if(isset($value)) { echo ($value->nom); } ?>" required >
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="prenom"><?=$this->lang->line('application_firstname');?>*</ label>
                                <input type="text" name="prenom" id="Prenom" class="form-control" value="<?php if(isset($value)) { echo $value->prenom; } ?>" required>
                        </div>

                         <div class="form-group  col-md-6">
                                <label for="matricule"><?=$this->lang->line('application_matricule');?>*</label>
                                <input type="text" name="matricule" id="matricule" class="form-control" value="<?php if(isset($value)) { echo $value->matricule; } ?>" >
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="numerocnss"><?=$this->lang->line('application_matricule_cnss');?>*</label>
                                <input type="text" name="numerocnss" id="CNSS" class="form-control" 
                                value="<?php if(isset($value)){echo $value->numerocnss;}?>"
                                >
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="adresse1"><?=$this->lang->line('application_address');?>1 *</label>
                                <input type="text" name="adresse1" id="adress1" class="form-control" 
                                 value="<?php if(isset($value)){echo $value->adresse1;}?>"
                                 >
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="adresse2"><?=$this->lang->line('application_address');?>2 *</label>
                                <input type="text" name="adresse2" id="adress1" class="form-control"
                                 value="<?php if(isset($value)){echo $value->adresse2;}?>"
                                 >
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="codepostal"><?=$this->lang->line('application_zip_code');?>2 *</label>
                                <input type="text" name="codepostal" id="codepostal" class="form-control" 
                                 value="<?php if(isset($value)) {echo $value->codepostal;}?>"
                                >
                        </div>

                        <div class="form-group  col-md-6">
                             <label for="pays"><?=$this->lang->line('application_country');?></label>
                                <select name="pays" id="pays" class="chosen-select">
                                <?php if(isset($value->pays))
                                {
                                    echo "<option value='$value->pays' selected>$value->pays</option>";
                                }
                                else
                                {
                                    ?>
                                   <option value="0" selected></option>

                                   <?php } ?>
                    
<option value="France" >France </option>

<option value="Afghanistan">Afghanistan </option>
<option value="Afrique_Centrale">Afrique_Centrale </option>
<option value="Afrique_du_sud">Afrique_du_Sud </option>
<option value="Albanie">Albanie </option>
<option value="Algerie">Algerie </option>
<option value="Allemagne">Allemagne </option>
<option value="Andorre">Andorre </option>
<option value="Angola">Angola </option>
<option value="Anguilla">Anguilla </option>
<option value="Arabie_Saoudite">Arabie_Saoudite </option>
<option value="Argentine">Argentine </option>
<option value="Armenie">Armenie </option>
<option value="Australie">Australie </option>
<option value="Autriche">Autriche </option>
<option value="Azerbaidjan">Azerbaidjan </option>

<option value="Bahamas">Bahamas </option>
<option value="Bangladesh">Bangladesh </option>
<option value="Barbade">Barbade </option>
<option value="Bahrein">Bahrein </option>
<option value="Belgique">Belgique </option>
<option value="Belize">Belize </option>
<option value="Benin">Benin </option>
<option value="Bermudes">Bermudes </option>
<option value="Bielorussie">Bielorussie </option>
<option value="Bolivie">Bolivie </option>
<option value="Botswana">Botswana </option>
<option value="Bhoutan">Bhoutan </option>
<option value="Boznie_Herzegovine">Boznie_Herzegovine </option>
<option value="Bresil">Bresil </option>
<option value="Brunei">Brunei </option>
<option value="Bulgarie">Bulgarie </option>
<option value="Burkina_Faso">Burkina_Faso </option>
<option value="Burundi">Burundi </option>

<option value="Caiman">Caiman </option>
<option value="Cambodge">Cambodge </option>
<option value="Cameroun">Cameroun </option>
<option value="Canada">Canada </option>
<option value="Canaries">Canaries </option>
<option value="Cap_vert">Cap_Vert </option>
<option value="Chili">Chili </option>
<option value="Chine">Chine </option>
<option value="Chypre">Chypre </option>
<option value="Colombie">Colombie </option>
<option value="Comores">Colombie </option>
<option value="Congo">Congo </option>
<option value="Congo_democratique">Congo_democratique </option>
<option value="Cook">Cook </option>
<option value="Coree_du_Nord">Coree_du_Nord </option>
<option value="Coree_du_Sud">Coree_du_Sud </option>
<option value="Costa_Rica">Costa_Rica </option>
<option value="Cote_d_Ivoire">Côte_d_Ivoire </option>
<option value="Croatie">Croatie </option>
<option value="Cuba">Cuba </option>

<option value="Danemark">Danemark </option>
<option value="Djibouti">Djibouti </option>
<option value="Dominique">Dominique </option>

<option value="Egypte">Egypte </option>
<option value="Emirats_Arabes_Unis">Emirats_Arabes_Unis </option>
<option value="Equateur">Equateur </option>
<option value="Erythree">Erythree </option>
<option value="Espagne">Espagne </option>
<option value="Estonie">Estonie </option>
<option value="Etats_Unis">Etats_Unis </option>
<option value="Ethiopie">Ethiopie </option>

<option value="Falkland">Falkland </option>
<option value="Feroe">Feroe </option>
<option value="Fidji">Fidji </option>
<option value="Finlande">Finlande </option>
<option value="France">France </option>

<option value="Gabon">Gabon </option>
<option value="Gambie">Gambie </option>
<option value="Georgie">Georgie </option>
<option value="Ghana">Ghana </option>
<option value="Gibraltar">Gibraltar </option>
<option value="Grece">Grece </option>
<option value="Grenade">Grenade </option>
<option value="Groenland">Groenland </option>
<option value="Guadeloupe">Guadeloupe </option>
<option value="Guam">Guam </option>
<option value="Guatemala">Guatemala</option>
<option value="Guernesey">Guernesey </option>
<option value="Guinee">Guinee </option>
<option value="Guinee_Bissau">Guinee_Bissau </option>
<option value="Guinee equatoriale">Guinee_Equatoriale </option>
<option value="Guyana">Guyana </option>
<option value="Guyane_Francaise ">Guyane_Francaise </option>

<option value="Haiti">Haiti </option>
<option value="Hawaii">Hawaii </option>
<option value="Honduras">Honduras </option>
<option value="Hong_Kong">Hong_Kong </option>
<option value="Hongrie">Hongrie </option>

<option value="Inde">Inde </option>
<option value="Indonesie">Indonesie </option>
<option value="Iran">Iran </option>
<option value="Iraq">Iraq </option>
<option value="Irlande">Irlande </option>
<option value="Islande">Islande </option>
<option value="Israel">Israel </option>
<option value="Italie">italie </option>

<option value="Jamaique">Jamaique </option>
<option value="Jan Mayen">Jan Mayen </option>
<option value="Japon">Japon </option>
<option value="Jersey">Jersey </option>
<option value="Jordanie">Jordanie </option>

<option value="Kazakhstan">Kazakhstan </option>
<option value="Kenya">Kenya </option>
<option value="Kirghizstan">Kirghizistan </option>
<option value="Kiribati">Kiribati </option>
<option value="Koweit">Koweit </option>

<option value="Laos">Laos </option>
<option value="Lesotho">Lesotho </option>
<option value="Lettonie">Lettonie </option>
<option value="Liban">Liban </option>
<option value="Liberia">Liberia </option>
<option value="Liechtenstein">Liechtenstein </option>
<option value="Lituanie">Lituanie </option>
<option value="Luxembourg">Luxembourg </option>
<option value="Lybie">Lybie </option>

<option value="Macao">Macao </option>
<option value="Macedoine">Macedoine </option>
<option value="Madagascar">Madagascar </option>
<option value="Madère">Madère </option>
<option value="Malaisie">Malaisie </option>
<option value="Malawi">Malawi </option>
<option value="Maldives">Maldives </option>
<option value="Mali">Mali </option>
<option value="Malte">Malte </option>
<option value="Man">Man </option>
<option value="Mariannes du Nord">Mariannes du Nord </option>
<option value="Maroc">Maroc </option>
<option value="Marshall">Marshall </option>
<option value="Martinique">Martinique </option>
<option value="Maurice">Maurice </option>
<option value="Mauritanie">Mauritanie </option>
<option value="Mayotte">Mayotte </option>
<option value="Mexique">Mexique </option>
<option value="Micronesie">Micronesie </option>
<option value="Midway">Midway </option>
<option value="Moldavie">Moldavie </option>
<option value="Monaco">Monaco </option>
<option value="Mongolie">Mongolie </option>
<option value="Montserrat">Montserrat </option>
<option value="Mozambique">Mozambique </option>

<option value="Namibie">Namibie </option>
<option value="Nauru">Nauru </option>
<option value="Nepal">Nepal </option>
<option value="Nicaragua">Nicaragua </option>
<option value="Niger">Niger </option>
<option value="Nigeria">Nigeria </option>
<option value="Niue">Niue </option>
<option value="Norfolk">Norfolk </option>
<option value="Norvege">Norvege </option>
<option value="Nouvelle_Caledonie">Nouvelle_Caledonie </option>
<option value="Nouvelle_Zelande">Nouvelle_Zelande </option>

<option value="Oman">Oman </option>
<option value="Ouganda">Ouganda </option>
<option value="Ouzbekistan">Ouzbekistan </option>

<option value="Pakistan">Pakistan </option>
<option value="Palau">Palau </option>
<option value="Palestine">Palestine </option>
<option value="Panama">Panama </option>
<option value="Papouasie_Nouvelle_Guinee">Papouasie_Nouvelle_Guinee </option>
<option value="Paraguay">Paraguay </option>
<option value="Pays_Bas">Pays_Bas </option>
<option value="Perou">Perou </option>
<option value="Philippines">Philippines </option>
<option value="Pologne">Pologne </option>
<option value="Polynesie">Polynesie </option>
<option value="Porto_Rico">Porto_Rico </option>
<option value="Portugal">Portugal </option>

<option value="Qatar">Qatar </option>

<option value="Republique_Dominicaine">Republique_Dominicaine </option>
<option value="Republique_Tcheque">Republique_Tcheque </option>
<option value="Reunion">Reunion </option>
<option value="Roumanie">Roumanie </option>
<option value="Royaume_Uni">Royaume_Uni </option>
<option value="Russie">Russie </option>
<option value="Rwanda">Rwanda </option>

<option value="Sahara Occidental">Sahara Occidental </option>
<option value="Sainte_Lucie">Sainte_Lucie </option>
<option value="Saint_Marin">Saint_Marin </option>
<option value="Salomon">Salomon </option>
<option value="Salvador">Salvador </option>
<option value="Samoa_Occidentales">Samoa_Occidentales</option>
<option value="Samoa_Americaine">Samoa_Americaine </option>
<option value="Sao_Tome_et_Principe">Sao_Tome_et_Principe </option>
<option value="Senegal">Senegal </option>
<option value="Seychelles">Seychelles </option>
<option value="Sierra Leone">Sierra Leone </option>
<option value="Singapour">Singapour </option>
<option value="Slovaquie">Slovaquie </option>
<option value="Slovenie">Slovenie</option>
<option value="Somalie">Somalie </option>
<option value="Soudan">Soudan </option>
<option value="Sri_Lanka">Sri_Lanka </option>
<option value="Suede">Suede </option>
<option value="Suisse">Suisse </option>
<option value="Surinam">Surinam </option>
<option value="Swaziland">Swaziland </option>
<option value="Syrie">Syrie </option>

<option value="Tadjikistan">Tadjikistan </option>
<option value="Taiwan">Taiwan </option>
<option value="Tonga">Tonga </option>
<option value="Tanzanie">Tanzanie </option>
<option value="Tchad">Tchad </option>
<option value="Thailande">Thailande </option>
<option value="Tibet">Tibet </option>
<option value="Timor_Oriental">Timor_Oriental </option>
<option value="Togo">Togo </option>
<option value="Trinite_et_Tobago">Trinite_et_Tobago </option>
<option value="Tristan da cunha">Tristan de cuncha </option>
<option value="Tunisie">Tunisie </option>
<option value="Turkmenistan">Turmenistan </option>
<option value="Turquie">Turquie </option>

<option value="Ukraine">Ukraine </option>
<option value="Uruguay">Uruguay </option>

<option value="Vanuatu">Vanuatu </option>
<option value="Vatican">Vatican </option>
<option value="Venezuela">Venezuela </option>
<option value="Vierges_Americaines">Vierges_Americaines </option>
<option value="Vierges_Britanniques">Vierges_Britanniques </option>
<option value="Vietnam">Vietnam </option>

<option value="Wake">Wake </option>
<option value="Wallis et Futuma">Wallis et Futuma </option>

<option value="Yemen">Yemen </option>
<option value="Yougoslavie">Yougoslavie </option>

<option value="Zambie">Zambie </option>
<option value="Zimbabwe">Zimbabwe </option>

</select>
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="numero1"><?=$this->lang->line('application_numero');?> 1</label>
                                <input type="text" name="tel1" id="numero1" class="form-control"
                                 value="<?php if(isset($value)){echo $value->tel1;}?>"
                                 >
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="numero2"><?=$this->lang->line('application_numero');?> 2</label>
                                <input type="text" name="tel2" id="numero2" class="form-control" 
                                 value="<?php if(isset($value)){echo $value->tel2;}?>"
                                >
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="skype"><?=$this->lang->line('application_skype');?></label>
                                <input type="text" name="skype" id="skype" class="form-control" 
                                 value="<?php if(isset($value)){echo $value->skype;}?>"
                                >
                        </div>

<script language="javascript">


   function verifiermail() {
   if ((document.getElementById("mail").value.indexOf("@") <= 0 ) || (document.getElementById("mail").value.indexOf(".") <= 0)) {

      document.getElementById("contanieralert2").style = "";
      document.getElementById("alert2").style = "color:red ;";
      document.getElementById("alert2").value = "e-mail invalide !";

      } else {
            document.getElementById("contanieralert2").style = "display: none;"
      }
   }
//-->
</script>



                        <div class="form-group  col-md-6">
                                <label for="mail"><?=$this->lang->line('application_email');?></label>
                                <input type="text"  name="mail" id="mail"  onkeyup="javascript:verifiermail();" class="form-control" 
                                 value="<?php if(isset($value)){echo $value->mail;}?>"
                                >
                        </div>
            <div id="contanieralert2" class="form-group col-md-6" style="display: none;" >
                
                <input type="text"   class="form-control" name="alert2" id="alert2" value="..." >   
            </div>


                        <div class="form-group  col-md-6">
                                <label for="genre"><?=$this->lang->line('application_genre');?>*</label>
                                <select name="genre" id="Genre" class="chosen-select">
                                        


                               <?php if((isset($value->genre))&& ($value->genre != 0))
                                    {

                                               foreach($genre as $ref)
                                               {

                                                    if($value->genre == $ref->id )
                                                    {
                                                        echo "<option value='$ref->id' selected>$ref->name</option>";
                                                        break;
                                                    }

                                                

                                                }

                                    
                                    }
                                    else
                                    { 
                                ?>
                                   <option value="0" selected></option>

                                   <?php } ?>


                                        <?php foreach($genre as $g){?>
                                                <option value="<?=$g->id?>"><?=$g->name?></option>
                                        <?php }?>


                                </select>
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="situationfamiliale"><?=$this->lang->line('application_Situation_familiale');?>*
                                </label>
                                <select name="situationfamiliale" id="Situation" class="chosen-select">
                               
                               <?php if((isset($value->situationfamiliale))&& ($value->situationfamiliale != 0))
                                    {

                                               foreach($situationfamiliale as $ref)
                                               {

                                                    if($value->situationfamiliale == $ref->id )
                                                    {
                                                        echo "<option value='$ref->id' selected>$ref->name</option>";
                                                        break;
                                                    }

                                                

                                                }

                                    }
                                    else
                                    {
                                ?>
                                   <option value="0" selected></option>

                                   <?php } ?>                                                
                                                <?php foreach($situations as $situation){?>
                                                        <option value="<?=$situation->id?>"><?=$situation->name?></option>
                                                <?php }?>
                                </select>
                        </div>
                        <div class="form-group  col-md-6">
                                <label for="datenaiss"><?=$this->lang->line('application_date_naissance');?>*</label>
                                <input type="text" class="form-control" name="datedenaissance" id="datenaiss" placeholder="yyyy-mm-dd"

                                 value="<?php
                                             if((isset($value)) &&($value->datedenaissance != null)&&($value->datedenaissance != '0000-00-00'))
                                                    {echo $value->datedenaissance;} 
                                            
                                        ?>" 

                                       
                                >
                        </div>


<script language="javascript">

function test() {
   if ((document.getElementById("Cin").value.length < 8 ) || (document.getElementById("Cin").value.length > 8 )) {
      // alert("La valeur est différente de bidule.");
      
      document.getElementById("contanieralert").style = ""
      document.getElementById("alert1").style = "color:red ; ";
      document.getElementById("alert1").value = "cin invalide !";

   }
   else
   {
        document.getElementById("contanieralert").style = "display: none;"
   }
}

</script>
                        <div class="form-group  col-md-6">
                                <label for="numerocin"><?=$this->lang->line('application_cin');?> *</label>
                                <input type="text" class="form-control" onkeyup="javascript:test();" name="numerocin" id="Cin" 
                                 value="<?php if(isset($value)){echo $value->numerocin;}?>"
                                >
                        </div>

                        <div id="contanieralert" class="form-group col-md-6" style="display: none;" >
                            
                            <input type="text"   class="form-control" name="alert1" id="alert1" value="..." >   
                        </div>

                     <div class="form-group  col-md-6">
                          <label for="lieunaiss"><?=$this->lang->line('application_lieu_de_naissance');?></label>
                                <input type="text" class="form-control" name="lieudenaissance" id="lieunaiss"
                                 value="<?php if(isset($value)){echo $value->lieudenaissance;}?>"
                                >
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="datedelivrance"><?=$this->lang->line('application_date_délivrance');?></label>
                                
                                <input type="text" class="form-control" name="datedelivrance"  id="datedelivrance" placeholder="yyyy-mm-dd"
                                 value="<?php if((isset($value))&&($value->datedelivrance != null) &&($value->datedelivrance != '0000-00-00') )

                                 {echo $value->datedelivrance;} ?>" 
                                >
                        </div>


                        <div class="form-group  col-md-6">
                                <label for="date_debut_embauche"><?=$this->lang->line('application_date_debut_embaucheee');?></label>
                                
                                <input type="text" class="form-control" name="date_debut_embauche"  id="date_debut_embauche" placeholder="yyyy-mm-dd"
                                 value="<?php if((isset($value))&&($value->date_debut_embauche != null) &&($value->date_debut_embauche != '0000-00-00'))

                                 {echo $value->date_debut_embauche;}   ?>" 
                                >
                        </div>

                        <div class="form-group  col-md-6">
                                <label for="date_fin_embauche"><?=$this->lang->line('application_date_fin_embaucheee');?></label>
                                
                                <input type="text" class="form-control" name="date_fin_embauche"  id="date_fin_embauche" placeholder="yyyy-mm-dd"
                                 value="<?php if((isset($value))&&($value->date_fin_embauche != null)&&($value->date_fin_embauche != '0000-00-00') )
                                 {echo $value->date_fin_embauche;}?>" 
                                >
                        </div>


            <div class="form-group  col-md-6">
                <label for="type_contart"><?=$this->lang->line('application_contrat_de_travail');?></label>
                        <select name="type_contart" id="type_contart" class="chosen-select">

                               <?php if((isset($value->type_contart))&& ($value->type_contart != 0))
                                    {

                                               foreach($typecontarts as $ref)
                                               {

                                                    if($value->type_contart == $ref->id )
                                                    {
                                                        echo "<option value='$ref->id' selected>$ref->name</option>";
                                                        break;
                                                    }

                                                

                                                }
                                     }
                                    else
                                    {
                                ?>
                                   <option value="0" selected></option>

                                   <?php } ?> 


                    <?php foreach($typecontarts as $g){?>

                        <option value="<?=$g->id?>"><?=$g->name?></option>

                    <?php }?>
                </select>

            </div>





                        <div class="form-group col-md-6">
                                <label for="idfonction"><?=$this->lang->line('application_fonction');?>*</label>

                                <select name="idfonction" id="Situation" class="chosen-select">

                               <?php if((isset($value->idfonction))&& ($value->idfonction != 0))
                                    {

                                               foreach($fonctions as $fonction)
                                               {

                                                    if( $value->idfonction == $fonction->id )
                                                    {
                                                        echo "<option value='$fonction->id' selected>$fonction->name</option>";
                                                        break;
                                                    }

                                                

                                                }


                                    
                                    }
                                    else
                                    {
                                ?>
                                   <option value="0" selected></option>

                                   <?php } ?>                                
                                               
                                                <?php foreach($fonctions as $fonction){?>
                                                        <option value="<?=$fonction->id?>">
                                                        <?=$fonction->name?></option>
                                                <?php }?>
                                </select>
                        </div>


                        <div class="form-group  col-md-6">
                                <label for="nombanque"><?=$this->lang->line('application_nom_banque');?> *</label>
                                <input type="text" class="form-control" name="nombanque" id="nombanque" 
                                 value="<?php if(isset($value)){echo $value->nombanque;}?>"
                                >
                        </div>
                        <div class="form-group  col-md-6">
                                <label for="rib"><?=$this->lang->line('application_rib');?> *</label>
                                <input type="text" class="form-control" name="rib" id="rib" 
                                 value="<?php if(isset($value)){echo $value->rib;}?>"
                                >
                        </div>
                        <div class="form-group  col-md-6">
                                <label for="iban"><?=$this->lang->line('application_iban');?> *</label>
                                <input type="text" class="form-control" name="iban" id="iban" 
                                 value="<?php if(isset($value)){echo $value->iban;}?>"
                                >
                        </div>
                        <div class="form-group  col-md-6">
                                <label for="bil"><?=$this->lang->line('application_bil');?> *</label>
                                <input type="text" class="form-control" name="bil" id="bil" 
                                 value="<?php if(isset($value)){echo $value->bil;}?>"
                                >
                        </div>

                        <?php endforeach;?>



<?php
$access = array();
if(isset($client)){ $access = explode(",", $client->access); }
?>

        <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
        <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
        </div>
<?php echo form_close(); ?>