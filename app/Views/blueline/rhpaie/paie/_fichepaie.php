<style>
    .tab-sal{
        font-size: 12px !important;
    }
    .border-r{
        border-right: 1px solid #868686 !important;
    }
    .tab-cc{
        /**border: 1px solid #868686;**/
    }
    .border-bt{
        border-bottom: 1px solid #868686 !important;
    }
    .titre-tab {
        background:#b2b1b1;
    }
    .br-bloc {border:1px solid;}
    .pad-cell td, .pad-td{ padding:2px 5px;}

    /** #entete, #pdp { display:none }**/
    .page_break { page-break-before: always; }

    @media print {
        body { background-color: #f5f5f5 !important;}
        @page {
            /**     background-color: #f5f5f5 !important;**/
                size: A4;
                margin-top: 0;
                margin-bottom: 0;

            /**   background: none !important;**/
                /*              padding: 0;
                  border: 0;
                  float: none !important;
                  color: black;
                  background: transparent;*/
        }
        .noprint  {
            display:none;

        }
        thead {
            display: table-header-group;
        }
        .links-print {display:none;}
        .test { height: 100%;}

        h4 {
            page-break-after: avoid;
            page-break-before: always;
        }
    }

</style>
<div class="page-content">

    <form class="" id="fdp_Form"  style='font-size: 10px !important;' >
        <?php foreach ($paies as $key=>$salaire): ?>
            <?php //$salaire = $paie['salaire']; $salaries = $paie['salaries'];
            //var_dump($employeur, $salaire); //foreach ($paie as $salaire): ?>
            <div>
                <div class="<?php echo ($key>0)? 'page_break':''; ?>" >
                    <body>
                        <br/><br/>
                        <input type="hidden" name="ids[]" value="<?php echo $salaire->id ?>"</input>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td height="1" valign="top">
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-size:20px !important; font-weight:bold;">BULLETIN DE PAIE</td>
                                            <td align="right">
                                                <?php if($employeur->picture): ?>
                                                <img src="<?php  echo 'files/media/'.$employeur->picture; ?>" />
                                                <?php  endif;  ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="197" colspan="3">
                                                <div  style="font-size:17px !important; font-weight:bold;" colspan="3" width="70">
                                                    <?php   $dated=$salaire->Paie_du ?>
                                                    <?php   $datea=$salaire->Paie_au ?>
                                                    <strong>  <?php  echo 'Du ' . date_format(date_create($dated),"d-m-Y").' au '. date_format(date_create($datea),"d-m-Y") ?></strong>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellpadding="5" cellspacing="5" >
                                        <tr>
                                            <td width="50%" class="br-bloc">
                                                <table border="0" cellpadding="5" cellspacing="2" class="t-pad pad-cell" style="height:50px;">
                                                    <tr>
                                                        <td width="45%"><strong>Matricule</strong></td>
                                                        <td width="55%"><?php echo $salaire->code.' - '.  $salaire->nom.' '.$salaire->prenom ; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <td><b>N° CNSS </b></td>
                                                        <td><?php echo $salaire->numerocnss; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <td><b>Echelon: </b><?php echo $salaire->echelon; ?></td>
                                                        <td><b>Catégorie: </b><?php  echo $salaire->categorie; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <td><b>Mode de paiement </b></td>
                                                        <td><?php echo $salaire->mode_paiement; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <td><strong>Fonction</strong></td>
                                                        <td><?php echo $salaire->fct_name; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <td><strong>Sit. familiale</strong></td>
                                                        <td><?php echo $salaire->ref_sitfam_name; ?> </td>
                                                    </tr>

                                                    <tr>
                                                        <td><strong>Nbre d'enfants</strong></td>
                                                        <td><?php echo $salaire->nb_enfants; ?></td>
                                                    </tr>
                                                </table>
                                            </td>

                                            <td width="2%">
                                                &nbsp;
                                            </td>

                                            <td width="48%" class="br-bloc pad-td">
                                                <strong><?php echo $employeur->name; ?></strong><br />
                                                <?php echo $employeur->address; ?><br />
                                                <?php echo $employeur->city; ?><br />
                                                CNSS Employeur :  <?php echo $employeur->cnss; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <?php if(isset($salaire->conges)){
                                if( $salaire->getSalaries()->getCongePaie()  !=1 ): ?>
                                <tr>
                                    <td>
                                        <table border="1" cellspacing="0" cellpadding="0"  class="tab-cc xls" width="88%" >
                                            <tr>
                                                <td rowspan="2"><b>Droit congés payés</b></td>
                                                <td align="center">Ancien solde</td>
                                                <td align="center">(+) Droits</td>
                                                <td align="center">(-) Pris</td>
                                                <td align="center">Nouv. solde</td>
                                            </tr>
                                            <tr>
                                                <?php $st= $salaire->getNbCngRst();  ?>
                                                <td align="center"><?php echo $st ?> </td>
                                                <?php $s=$salaire->getSalaries()->getDroitConge(); ?>
                                                <td align="center"><?php echo $s;?></td>
                                                <td align="center"><?php echo  $salaire->getNbJourAbsence(); ?></td>
                                                <td align="center"><?php echo $st+$s-$salaire->getNbJourAbsence(); ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            <?php endif; } ?>

                            <tr>
                                <td>&nbsp;</td>
                            </tr>

                            <tr>
                                <td>
                                    <table class="tab-sal"  width="100%" border="1" cellpadding="0" cellspacing="0" class="xls">
                                        <tr class="titre-tab">
                                            <td height="40"><strong>Désignation</strong></td>
                                            <td><strong>Nombre</strong></td>
                                            <td><strong>Base</strong></td>
                                            <td><strong>Gains</strong></td>
                                            <td><strong>Retenues</strong></td>
                                        </tr>

                                        <tr>
                                            <td>Nombre de jours de présence</td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right"><?php echo $salaire->nb_jour_presence; ?></td>
                                            <td align="right">&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td>Salaire de base</td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right"><?php echo number_format($salaire->salaire_brut,3,',',' '); ?></td>
                                            <td align="right"><?php echo number_format($salaire->salaire_brut,3,',',' ');  ?></td>
                                            <td align="right">&nbsp;</td>
                                        </tr>

                                        <!-- autant de lignes que de primes dans la conv collective -->
                                        <?php if ($salaire->idfonction!=60) :
                                            // echo 'aaa'.count($conv_collective).'bbbb';exit();
                                            foreach ($conv_collective as $conv) :
                                                $titre_prime=$conv->getTitrePrime();
                                                if($prdpaie['archive'] == 0){
                                                    $conv_collective_prime_valeur = $this->em->getRepository('Entities\conv_collective_primes_valeur')->findBy(array('convention_primes' => $conv, 'titre_conv_salarie' => $prdpaie['typeSalarie'] ));
                                                }else{
                                                    $conv_collective_prime_valeur = $this->em->getRepository('Entities\conv_archive_primes_valeur')->findBy(array('archive_primes' => $conv, 'titre_conv_salarie' => $prdpaie['typeSalarie'] ));
                                                }

                                                foreach ($conv_collective_prime_valeur as $conv_pr_val) : ?>
                                                    <tr>
                                                        <td><?php  echo $titre_prime; ?></td>
                                                        <td align="right">&nbsp; </td>
                                                        <td align="right"><?php echo number_format($conv_pr_val->getValeurPrime(),3,',',' '); ?></td>
                                                        <td align="right"><?php echo number_format($conv_pr_val->getValeurPrime(),3,',',' '); ?></td>
                                                        <td align="right">&nbsp;</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>


                                            <?php  //primes
                                            /**$this->load->library('form_validation');
                                            $this->load->model("prime_model");
                                            $primes_salaries  =  $this->prime_model->findAllPrimeSalarie();
                                            foreach ($primes_salaries as $prime) :
                                                $date=$salaire->getPaieDu();
                                                $tab = explode("-",$date->format('Y-m-d'));
                                                $mois= $tab[1];
                                                $annee=$tab[0];

                                                if($prime->ppp_id_salarie==$salaire->getSalaries()->getId() && $prime->ppp_concerne_mois==$mois && $prime->ppp_annee==$annee )
                                                {
                                                    $primeUsers = $this->prime_model->findAllPrimeUtilisateur();
                                                    foreach ($primeUsers as $primeUser)
                                                    {
                                                        if($primeUser->plp_id==$prime->ppp_id_prime) :
                                                            $titrePrime=$primeUser->plp_titre_prime;
                                                            $valeur=$primeUser->plp_valeur_prime;
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $titrePrime; ?></td>
                                                                <td align="right">&nbsp; </td>
                                                                <td align="right"><?php echo number_format($valeur,3,',',' '); ?></td>
                                                                <td align="right"><?php echo number_format($valeur,3,',',' '); ?></td>
                                                                <td align="right">&nbsp;</td>
                                                            </tr>
                                                        <?php endif;
                                                    }
                                                }
                                            endforeach; **/?>
                                        <?php endif; ?>
                                        <tr>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;<b>Total Brut</b></td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right"><?php echo number_format($salaire->salaire_brut,3,',',' ');  ?></td>
                                            <td align="right">&nbsp;</td>
                                        </tr>
                                        <?php if ($salaire->idfonction!=60){?>
                                            <tr>
                                                <td>Retenu C.N.S.S</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right"><?php echo number_format($salaire->salaire_brut,3,',',' ');  ?></td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right"><?php echo number_format($salaire->cotisation_cnss, 3,',',''); ?></td>
                                            </tr>

                                            <tr>
                                                <td>IRPP</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right"></td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right"><?php echo number_format($salaire->impot_revenue, 3,',',''); ?></td>
                                            </tr>

                                            <tr>
                                                <td>C.S.S</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right"></td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right"><?php echo number_format($salaire->css, 3,',',''); ?></td>
                                            </tr>

                                        <?php } ?>
                                        <tr>
                                            <td><b>SALAIRE IMPOSABLE</b></td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right"><?php echo number_format($salaire->salaire_imposable,3,',',' '); ?></td>
                                        </tr>
                                       <tr>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;<b>Total Cotisations</b></td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right">&nbsp;</td>
                                            <td align="right"><?php echo number_format(($salaire->impot_revenue+ $salaire->cotisation_cnss + $salaire->css),3,',',' '); ?></td>
                                        </tr>


                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <table class="tab-sal"  cellpadding="0" cellspacing="2" border="0" width="100%">
                                        <tbody>
                                            <tr height="11">
                                                <td align="right">&nbsp;</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                            <tr height="11">
                                                <td width="83%" align="right"><strong>Salaire </strong></td>
                                                <td width="3%"></td>
                                                <td width="2%"></td>
                                                <td width="12%" colspan="2" align="right" class="tab-cc"id="red"><span style="font-weight:bold;padding-right: 4px;"><?php echo number_format(($salaire->salaire_net),3,',',' '); ?></span> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td><table class="tab-sal"  cellpadding="0" cellspacing="2" border="0" width="100%">
                                        <tbody>
                                        <tr height="11">
                                            <td align="right">&nbsp;</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <?php if(intval(date_format(date_create($dated),"Y") )<2016){ ?>
                                            <tr height="11">
                                                <td width="83%" align="right"><strong>Redevance </strong></td>
                                                <td width="3%"></td>
                                                <td width="2%"></td>
                                                <td width="12%" colspan="2" align="right" class="tab-cc"id="red"><span style="font-weight:bold;padding-right: 4px;"><?php echo number_format($salaire->redevance,3,',',' '); ?></span> </td>
                                            </tr>
                                        <?php } ?>

                                        </tbody>
                                    </table></td>

                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td><table class="tab-sal"  cellpadding="0" cellspacing="2" border="0" width="100%">
                                        <tbody>

                                        <tr height="11">
                                            <td width="83%" align="right"><strong>Avance sur Salaire</strong></td>
                                            <td width="3%"></td>
                                            <td width="2%"></td>
                                            <td width="12%" colspan="2" align="right" class="tab-cc"id="red"><span style="font-weight:bold;padding-right: 4px;"><?php echo number_format($salaire->avance,3,',',' '); ?></span> </td>
                                        </tr>



                                        </tbody>
                                    </table></td>

                            </tr>
                            <tr>
                                <td><table class="tab-sal" cellpadding="0" cellspacing="2" border="0" width="100%">
                                        <tbody>
                                        <tr height="11">
                                            <td align="right">&nbsp;</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr height="11">
                                            <td width="83%" align="right"><strong>Prêt</strong></td>
                                            <td width="3%"></td>
                                            <td width="2%"></td>
                                            <td width="12%" colspan="2" align="right" class="tab-cc"id="red"><span style="font-weight:bold;padding-right: 4px;"><?php echo number_format($salaire->mnt_remb,3,',',' '); ?></span> </td>
                                        </tr>
                                        </tbody>
                                    </table></td>

                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>

                                <!--modr-->
                                <td><table class="tab-sal"  cellpadding="0" cellspacing="2" border="0" width="100%">
                                        <tbody>

                                        <tr height="11">
                                            <td width="83%" align="right"><strong>Net à Payer</strong></td>
                                            <td width="3%"></td>
                                            <td width="2%"></td>
                                            <td width="12%" colspan="2" align="right" class="tab-cc"><span style="font-size:15px;  font-weight:bold;padding-right: 4px;"><?php echo number_format($salaire->salaire_net,3,',',' '); ?></span> </td>
                                        </tr>
                                        <tr height="11">
                                            <td align="right">&nbsp;</td>

                                            <td></td>
                                        </tr>

                                        </tbody>
                                    </table></td>
                                <!--fin-->
                            </tr>
                    </table>
                    <table border="3" cellpadding="0" cellspacing="0" class="tab-cc t-pad" width="100%" style="height:50px;">
                        <tr>
                            <td width="177" align="center"><strong> Emargement & Cachet Employeur </strong></td>
                            <td width="177" align="center"><strong> Emargement Employé </strong></td>
                        </tr>
                        <tr>
                            <td width="177" height="70" align="center"></td>
                            <td width="177" height="70" align="center"></td>
                        </tr>

                    </table>
                    <h4></h4>
                    </body>

                </div>
        <?php endforeach; ?>

            <div class="links-print">


                <?php if($type == 1): ?>
                    <hr>
                    <?php   //$this->load->view('tools/_PrintPCancelButton'); ?>
                <?php endif;  ?>
            </div>
    </form>
</div> <?php  // $this->load->view('tools/_PrintPCancelButton'); ?>