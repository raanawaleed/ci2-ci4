<?php 

namespace App\Controllers;

use App\Controllers\BaseController;

class saisietemps1 extends BaseController
{
    Private $format_date = 'm-Y';
    Private $optionsMois = array();
    Private $moisCourant = null;
    Private $joursTravailMois = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('Ticket','ticket');
        $this->load->model('saisietemps_model','saisieTemps');
        $this->load->model('reftype_model','refType');
        $this->load->model('Ref_type_occurences_model','referentiels');

        if($this->client){
        }elseif($this->user){
        }else{
            redirect('login');
        }
        $idType = $this->refType->getRefTypeByName("ticket")->id;
        $submenus =  $this->referentiels->getReferentielsByIdType($idType);

        $this->view_data['submenu'] = array();
        foreach($submenus as $submenu){
            $this->view_data['submenu'][$this->lang->line('application_'.$submenu->name)] ='ctickets/filter/'.$submenu->id;
        }

        $this->type_ticket_projet= $this->referentiels->getReferentiels($this->config->item("type_id_type_ticket"), $this->config->item("ticket_projet") );
        $this->type_ticket_defaut= $this->referentiels->getReferentiels($this->config->item("type_id_type_ticket"), $this->config->item("ticket_par_defaut") );
        
        $this->type_ticket_projet->alias = 'P';
        $this->type_ticket_defaut->alias = 'D';

        $this->optionsMois = $this->getOptionsMois();
        $this->moisCourant = $this->getMoisCourant();
    }

    /**
     * Affichage de la saisie des temps par défaut : mois courant ou sur le mois choisi
     * @param null $mois
     * @param null $annee
     */
    function index($mois = null, $annee = null)
    {
        $this->getData(true, $mois, $annee );
    }


    /**
     * Affichage de la saisie des temps d'un utilisateur  sur le mois choisi
     * @param null $mois
     * @param null $annee
     */
    function view( $user , $mois , $annee )
    {
        $this->getData(true, $mois, $annee , $user);
    }

    //A TESTER
    private function getData($is_saisie, $mois = null, $annee = null, $xuser = null){
        if(isset($mois) && isset($annee) && strlen($mois)>0 && strlen($annee)>0){
            $this->moisCourant = $mois.'-'.$annee;
        }

        //L'utilisateur connecté
        $userLogin = User::find($this->user->id);
        
        //L'utilisateur choisi pour la saisie
        if(isset($xuser)){
            $xusers = User::find('all',array('conditions' => array('id = ?',$xuser)));
            if(count($xusers) != 1){
                log_message('error', "Une erreur est survenue lors de la recherche de l'utilisateur.");
                show_404("Une erreur est survenue lors de la recherche de l'utilisateur.");
            }else{
                $utilisateurCourant = $xusers[0];
            }
        }else{
            $utilisateurCourant = $this->user;
        }
        
        //Les tickets de l'utilisateur choisi
        $ticketsToadd[$this->type_ticket_projet->alias] = $tickets = $this->ticket->getTicketsParUtilisateur($utilisateurCourant->id);
        $ticketsToadd[$this->type_ticket_defaut->alias] = $ticketsParDefaut = $this->ticket->getTicketsParDefaut();
        
        //Jours du travail du mois courant
        $tab = explode('-', $this->moisCourant);
        $month = $tab[0];
        $year = $tab[1];
        $this->joursTravailMois = $this->getAllDaysInMonth($month, $year);

        //Dans les deux cas: Récupérer la saisie de l'utilisateur choisi dans ce mois
        $tab  = $this->getSaisieByUserAndDate(true, $utilisateurCourant->id,  $month, $year, $ticketsToadd);
        $saisieParTicket = $tab[0];
        $ticketsToadd = $tab[1];
        $validation_mois = $tab[2];

        if($is_saisie == true){
            //Récupérer  le total des heures pointées
            $this->getSaisieTotal(true, $this->user->id,  $month, $year);
            
        }else{//planification
            
            //Liste de tout les utilisateurs
            $users = User::find('all',array('conditions' => array("status not like ?", 'deleted')));
            
            //Récupérer  le total des heures pointées
            $this->getSaisieTotal(false, $xuser,  $month, $year);

            //Récupérer la planification de l'utilisateur dans ce mois
            $tab = $this->getSaisieByUserAndDate(true, $xuser,  $month, $year, $ticketsToadd);
            $tempsUserParTiket = $tab[0];
            $ticketsToadd = $tab[1];
            $validation_mois = $tab[2];

            $this->view_data['planification'] = true;
            $this->view_data['users'] = $users;
            $this->view_data['tempsUserParTiket'] = $tempsUserParTiket;
        }
        $limite_heures = $this->config->item('max_heures_pointees');
        if(!$limite_heures)
            show_404($this->lang->line('application_erreur_config_limite_saisie'));
        if($limite_heures === '-1'){
            $limite_heures = false;
        }

        $this->view_data['user'] = $userLogin;
        $this->view_data['utilisateurCourant'] = $utilisateurCourant;
        $this->view_data['tickets'] = $tickets;
        $this->view_data['ticketsToadd'] = $ticketsToadd;
        $this->view_data['ticketsParDefaut'] = $ticketsParDefaut;
        $this->view_data['joursTravailMois'] = $this->joursTravailMois;
        $this->view_data['saisieParTicket'] = $saisieParTicket;
        $this->view_data['optionsMois'] = $this->optionsMois;
        $this->view_data['moisCourant'] = $this->moisCourant;
        $this->view_data['mois'] = $month;
        $this->view_data['annee'] = $year;
        $this->view_data['validation_mois'] = $validation_mois;
        $this->view_data['limite_heures'] = $limite_heures;
        $this->view_data['type_ticket_projet'] = $this->type_ticket_projet;
        $this->view_data['type_ticket_defaut'] = $this->type_ticket_defaut;

        $this->content_view = 'saisietemps/time_update';

    }

    /**
     * index Planification
     * @param null $xuser
     * @param null $mois
     * @param null $annee
     */
    function planification($xuser=null , $mois = null, $annee = null)
    {

        $this->getData(false, $mois, $annee, $xuser );
    }

    function validation($mois = null, $annee = null)
    {
        //utilisateur connecté
        $userLogin = User::find($this->user->id);

        if(isset($mois) && isset($annee) && strlen($mois)>0 && strlen($annee)>0){
            $this->moisCourant = $mois.'-'.$annee;
        }

        //Jours du travail du mois courant
        $tab = explode('-', $this->moisCourant);
        $month = $tab[0];
        $year = $tab[1];


        //
        $tabSaisie = $this->saisieTemps->getSaisieAllUsersByMonth(1,  $month, $year);
        $tabPlanification = $this->saisieTemps->getSaisieAllUsersByMonth(0,  $month, $year);

        for ($i=0; $i<count($tabPlanification); $i++){
            foreach ($tabSaisie as $rowS){
                if($tabPlanification[$i]->id == $rowS->id){
                    $tabPlanification[$i]->totalSaisie = $rowS->total;
                    $tabPlanification[$i]->nb_daysSaisie = $rowS->nb_days;
                    $tabPlanification[$i]->nb_days_modSaisie = $rowS->nb_days_mod;
                    $tabPlanification[$i]->validation = $rowS->validation;
                }
            }
        }
        $this->view_data['tabPlanification'] = $tabPlanification;
        $this->view_data['user'] = $userLogin;
        $this->view_data['optionsMois'] = $this->optionsMois;
        $this->view_data['moisCourant'] = $this->moisCourant;
        $this->view_data['mois'] = $month;
        $this->view_data['annee'] = $year;

        $this->content_view = 'saisietemps/validation';
    }

    /**
     * Formater le temps 00:00
     * @param $nb_heures
     * @param $nb_minutes
     * @return string
     */
    function formaterTemps($sum_heures, $sum_minutes){
        return sprintf('%02d', getTotalHeures($sum_heures, $sum_minutes)) . ':' .str_pad(getResteMinutes( $sum_minutes), 2, '0', STR_PAD_RIGHT);
    }

    /**
     * Récupérer la total de la saisie/planification
     * @param $is_saisie
     * @param $user_id
     * @param $month
     * @param $year
     */
    function getSaisieTotal($is_saisie, $user_id,  $month, $year){
        $tabSaisieP =$this->saisieTemps->getSaisieByUserAndDate($is_saisie, $user_id, $month, $year, $this->type_ticket_projet, true);
        $tabSaisieD =$this->saisieTemps->getSaisieByUserAndDate($is_saisie, $user_id, $month, $year, $this->type_ticket_defaut, true);
        $tabTotal = array_merge($tabSaisieP, $tabSaisieD);
        
        foreach ($this->joursTravailMois as $key=>$item){
            foreach ($tabTotal as $i => $row){
                if($row->date == $item->date){
                   $this->joursTravailMois[$key]->nbhours = $this->formaterTemps($row->nb_heures, $row->nb_minutes);
                }
            }
        }
    }

    /**
     * Récupérer la sisie/planification d'un utilisateur  par date
     * @param $is_saisie
     * @param $user_id
     * @param $month
     * @param $year
     * @return array
     */
    function getSaisieByUserAndDate($is_saisie, $user_id,  $month, $year, $ticketsToadd){
        $saisieParTicket = array();
        $res_validation = $this->saisieTemps->getValidationSaisieByUserAndDate($user_id, $month, $year);
        if(count($res_validation)>1){
            //des saisies validés et d'autres non validés or on doit avoir soit l'un soit l'autre
            log_message('error', "Une erreur est survenue lors de la récupération de la saisie des temps de l'utilisateur $user_id au mois de $month-$year");
            show_404("Une erreur est survenue lors de la récupération de la saisie des temps.");
        }
        $validation_mois = (isset($res_validation[0])? $res_validation[0]->validation : 0);
       
        $tabSaisieP =$this->saisieTemps->getSaisieByUserAndDate($is_saisie, $user_id, $month, $year, $this->type_ticket_projet);
        $tabSaisieD =$this->saisieTemps->getSaisieByUserAndDate($is_saisie, $user_id, $month, $year, $this->type_ticket_defaut);

        foreach ($this->joursTravailMois as $key=>$item){
            $this->getSaisieParTypeTicket($saisieParTicket, $ticketsToadd, $tabSaisieP, $item, $this->type_ticket_projet);
            $this->getSaisieParTypeTicket($saisieParTicket, $ticketsToadd, $tabSaisieD, $item, $this->type_ticket_defaut);
        }   
        return array($saisieParTicket, $ticketsToadd, $validation_mois);
    }

    /**
    * Récupérer le tableau de saisie par type de ticket
    * Passage par référence
    *
    **/
    function getSaisieParTypeTicket(&$saisieParTicket, &$ticketsToadd, $tabSaisie, $item, $type_ticket){
        foreach ($tabSaisie as $i => $row){
                if($row->date == $item->date){
                    $citem = clone $item ;
                    $citem->nbhours = $this->formaterTemps($row->nb_heures_pointees, $row->nb_minutes_pointees) ;
                    $citem->row_id = $row->id;
                    $citem->autreSaisie = $row->autre_saisie;

                    if(!isset( $saisieParTicket[$type_ticket->alias][$row->ticket_id])) {
                        $saisieParTicket[$type_ticket->alias][$row->ticket_id] = new \stdClass();
                        $saisieParTicket[$type_ticket->alias][$row->ticket_id]->created_by = $row->user_name;
                        $saisieParTicket[$type_ticket->alias][$row->ticket_id]->created_at = $row->created_at;
                        $saisieParTicket[$type_ticket->alias][$row->ticket_id]->ticket_name = $row->ticket_name;
                        $saisieParTicket[$type_ticket->alias][$row->ticket_id]->project_name = $row->project_name;
                        $saisieParTicket[$type_ticket->alias][$row->ticket_id]->type_ticket = $type_ticket->id;
                        $saisieParTicket[$type_ticket->alias][$row->ticket_id]->temps = array();
                        $ticketsToadd[$type_ticket->alias] = $this->unsetTicket($ticketsToadd[$type_ticket->alias], $row->ticket_id);
                    }
                    $saisieParTicket[$type_ticket->alias][$row->ticket_id]->temps[$citem->day] = $citem;
                }
            }
    }
    /**
     * Récupérer la liste des jours de travail dans un mois
     * @param $month
     * @param $year
     * @return mixed
     */
    function getAllDaysInMonth($month, $year, $weekend = false){
        $option=array("id_vcompanies"=>$_SESSION['current_company']);
        $data["core_settings"] = Setting::find($option);

        $idType = $this->refType->getRefTypeByName("JourSemaine")->id;

        //Jour du mois
        $date =  $year.'-'.$month.'-'.'01';
        $sql = "SELECT '$date' + INTERVAL t.n - 1 DAY as date, 
                        LPAD(DAY ('$date'  + INTERVAL t.n - 1 DAY), 2, '0') AS day, 
                        WEEKDAY( '$date' + INTERVAL t.n - 1 DAY) as weekday
                FROM tally t
                WHERE t.n <= DATEDIFF(LAST_DAY('$date'), '$date') + 1";

        $joursMois = $this->db->query($sql)->result();


        $joursSemaine_id = array();
        if(! $weekend) { //nae pas inclure les weekends ou jours de non travail
            //Jours de travail de la semaine selon le référentiel
            $joursSemaine = $this->referentiels->getReferentielsByIdType($idType);
            foreach($joursSemaine as $jour) {
                $joursSemaine_id[$jour->name] = $jour->description;
            }

            //Construction du tableau final des jours de la semaine en enlevant les jours de weekend
            foreach ($joursMois as $key =>$row) {
                if(isset($joursSemaine_id[$row->weekday]) ){
                    $row->weekdaylib = $joursSemaine_id[$row->weekday];
                }else{
                    unset($joursMois[$key]);
                }
            }
        }else{ //inclure les weekends ou jours de non travail

            //Jours de travail de la semaine selon le référentiel
            $joursSemaine = $this->referentiels->getReferentielsByIdType($idType);
            foreach($joursSemaine as $jour) {
                $joursSemaine_id[$jour->name] = array('description'=>$jour->description, 'visible'=>$jour->visible);
            }

            //Construction du tableau final des jours de la semaine en laissant les jours de weekend
            foreach ($joursMois as $key =>$row) {
                if(isset($joursSemaine_id[$row->weekday]) ){
                    $row->weekdaylib = $joursSemaine_id[$row->weekday]['description'];
                    $row->isWorkDay = $joursSemaine_id[$row->weekday]['visible'];
                }
            }

        }
        return $joursMois;
    }

    /**
     * Récupérer le mois courant (formatage de date)
     * @return false|string
     */
    function getMoisCourant(){
        $now = new DateTime();
        $xdate   = strtotime($now->format("Y-m-d"));
        return date($this->format_date , $xdate);
    }


    /**
     * Récupérer les mois à afficher dans la liste select à l'écan:
     * Regle: dmin: -6mois /dmax:+3mois
     * @return array
     */
    function getOptionsMois()
    {
     $lib_mois = libelleMois();
    $year = new DateTime();
    $lastyear= date("Y",strtotime("-1 year"));
    $year = new DateTime();
     $y = $year->format("Y");
    $max = '31-12-'.$y;
    $min = '01-01-'.$lastyear;
    $dmax = (new DateTime($max))->modify('first day of this month');
    $dmin = (new DateTime($min))->modify('first day of this month');
    $output = [];
    $time   = strtotime($dmin->format("Y-m-d"));
    $last   = date('m-Y', strtotime($dmax->format("Y-m-d")));
    
    do {
        $xdate = date($this->format_date , $time);
        $xyear = date('Y', $time);
        $xmonth = date('m', $time);

        $output[] = [
            'date' => $xdate,
            'date_lib' => $lib_mois[$xmonth] ." ".$xyear,
            'month' => $xmonth,
            'year' => $xyear,
        ];

        $time = strtotime('+1 month', $time);
    } while ($xdate != $last);
    return $output;
    }

    /**
     * vérifier la saisie des temps :
     *  - temps total par jour
     *  - nombre de tableau par ticket
     */
    function verifierTemps(){
        $post =$this->input->post();

        //Veriifcation de la somme des heures par jours
        $tab_jour = array();
        $output = "-1";

        //date
        $date_tab = explode('-', $post['mois_annee']);
        $mois = $date_tab[0];
        $annee = $date_tab[1];

        if(isset($post['ticket']) && isset($post['mois_annee'])) {

            //Récuprérer les jours de travail
            reset($post['nbHeures'] );
            $first_tikect_id = key($post['nbHeures']);

            foreach ($post['nbHeures'][$first_tikect_id][0] as $jour => $nb) {
                //$tab_jour[$jour]->id = $jour;
                if(!isset($tab_jour[$jour])) $tab_jour[$jour] = new \stdClass();
                $tab_jour[$jour]->total = 0;
                $tab_jour[$jour]->total_heures = 0;
                $tab_jour[$jour]->total_minutes = 0;

                //parcours des tickets
                foreach ($post['ticket'] as $key => $ticket_id) {
                    if (isset($post['nbHeures'][$key][0])) {
                        //parcours des heures de tickets;
                        $saisi = $this->formaterSaisieTemps($post['nbHeures'][$key][0][$jour]);
                        $tab_jour[$jour]->total_heures += explode('.', $saisi)[0];
                        $tab_jour[$jour]->total_minutes += isset(explode('.', $saisi)[1])? explode('.', $saisi)[1] : 0;
                       // $tab_jour[$jour]->total += $post['nbHeures'][$key][0][$jour];
                    }
                }
            }
            $limite_heures = $this->config->item('max_heures_pointees');
            if($limite_heures !== '-1'){
                $message = "";
                foreach ($tab_jour as $jour => $item){

                    if( getTotalHeures($item->total_heures , $item->total_minutes)>$limite_heures){
                        $message .= "Vous avez saisi $item->total heures le $annee-$mois-$jour.</br>";
                    }
                }
            }
            //Vérification des ticktes
            $duplicates = $this->array_not_unique($post['ticket']);

            if(count($duplicates)>0){
                $output = "Vous avez séléctionné deux fois la même entrée.</br>";
                if(strlen($message )>0) {
                    $output .= $message;
                }
            }else{
                if(strlen($message )>0) {
                    $output = $message;
                }else{
                    $output =  "0";
                }
            }
        }else{
            redirect("saisietemps/index/$mois/$annee", "refresh");
        }
        $this->theme_view = 'blank';
        header('Content-Type: application/json');
        echo json_encode($output);
    }

    /**
     * Récupérer les colonnes dupliquées d'un tableau
     * @param $raw_array
     * @return array
     */
    function array_not_unique($raw_array) {
        $dupes = array();
        natcasesort($raw_array);
        reset($raw_array);

        $old_key   = NULL;
        $old_value = NULL;
        $old_type = NULL;
        foreach ($raw_array as $key => $value) {
            if ($value === NULL) { continue; }
            $tab = explode('-', $value);
            $ticket_id = $tab[0];
            $ticket_type = $tab[1];
            if(is_null($ticket_type))show_404("Veuillez vérifier le type de ticket");
            
            if ((strcasecmp($old_value, $ticket_id) === 0 ) && ($old_type === $ticket_type)){
                $dupes[] = $ticket_id;
            }
            $old_value = $ticket_id;
            $old_type = $ticket_type;
            $old_key   = $key;
        }
        return $dupes;
    }

    /**
     * La valeur saisie à l'écran contient : alors celle qui doit être sauvegardée dans la bd doit être avec un point
     * @param $temps
     * @return mixed
     */
    function formaterSaisieTemps($temps){
        return str_replace(':', '.', $temps);
    }
    /**
     * Mise à jour de la saisie des temps dans la base de données
     */
    function miseAJourTemps($action = '1')
    {
        $post = $this->input->post();
        $ticket_maj = array();
        
        $tab_insert = array();
        if (isset($post['ticket']) && isset($post['mois_annee']) && isset($post['utilisateurCourant'])) {
            $date_tab = explode('-', $post['mois_annee']);
            $mois = $date_tab[0];
            $annee = $date_tab[1];
            $user_id = $post['utilisateurCourant'];

            foreach ($post['ticket'] as $key => $ticket_id_type) {
                $tab = explode('-', $ticket_id_type);
                $ticket_id = $tab[0];
                $ticket_type = $tab[1];
                if(is_null($ticket_type))show_404("Veuillez vérifier le type de ticket");

            
                if (isset($post['nbHeures'][$key][0]) && isset($post['nbHeures'][$key][1])) {
                    if(! in_array($ticket_id, $ticket_maj)) $ticket_maj[] = $ticket_id;
                    
                    foreach ($post['nbHeures'][$key][0] as $jour => $nb) {
                        $date = date_create($annee . "-" . $mois . "-" . $jour);
                        $tab_insert[] = array('ticket_id' => $ticket_id,
                            'utilisateur_id' => $user_id,
                            'date' => date_format($date, "Y-m-d"),
                            'heures_pointees' => $this->formaterSaisieTemps( $nb),
                            'autre_saisie' => $post['nbHeures'][$key][1][$jour],
                            'type_ticket' => $ticket_type,
                            'created_by' => $this->user->id,
                            'created_at' => date("Y-m-d H:i:s"));
                    }
                }
            }

        }

        if ($action == '1'){
            $url = "saisietemps/index/$mois/$annee";
        }else if ($action == '0'){ //planification
            $url = "planification/$user_id/$mois/$annee";
        }else
            show_404("Une erreur est survenue lors de l'enregistrement des saisies des temps.");

            
        if(count($tab_insert)>0) {
            if ($action == '1')
                $this->saisieTemps->ajouterSaisieTickets($ticket_maj, $tab_insert, true, $user_id, $mois, $annee);
            else  //planification
                $this->saisieTemps->ajouterPlanificationTickets($ticket_maj, $tab_insert, true, $user_id, $mois, $annee);
            
            $this->session->set_flashdata('message', 'success:'.'Vos saisies des temps ont été sauvgardées');
        }else
            $this->session->set_flashdata('message', 'success:'."Vos n'avez rien à enregistrer");
        redirect($url, "refresh");
    }

    /**
     * Suppression de la saisie des temps d'un ticket
     * @param $ticket_id
     * @param $mois
     * @param $annee
     */
    function deleteTempsTicket($action, $ticket_id, $mois, $annee, $user_id = null){
        if($action == '1')  {
            $user_id = $this->user->id;
            $url = "saisietemps/index/$mois/$annee";
        }else{
            $url = "saisietemps/planification/$user_id/$mois/$annee";
        
        }
        
        $this->saisieTemps->supprimerTempsTickets($action,array($ticket_id), $user_id,$mois, $annee);
        redirect($url, "refresh");
        
        
    }

    function unsetTicket($tickets, $id){
        foreach ($tickets as $key => $ticket){
            if($ticket->id == $id)
                unset($tickets[$key]);
        }
        return $tickets;
    }


    /**
     * Export de la saisie/planification des temps
     * @param $action
     * @param $month
     * @param $year
     * @param null $xuser
     */
    public function export($action, $month, $year, $xuser=null ){
        $this->joursTravailMois = $this->getAllDaysInMonth($month, $year, true);

        if(is_null($xuser)){
            $xuser = $this->user->id;
        }
        //Utilisateur
        $user = User::find($xuser);

        $is_saisie = ($action == '1')? true: false;

        //Récupérer la saisie de l'utilisateur dans ce mois
        $tab  = $this->getSaisieByUserAndDate($is_saisie, $xuser,  $month, $year, array());

        //Libellé des mois
        $lib_mois = libelleMois();

        $this->CreerFichierExport($is_saisie, $month, $year, $user, $tab[0], $lib_mois);
    }


    /**
     * Export de la validation des temps
     * @param $action
     * @param $month
     * @param $year
     * @param null $xuser
     */
    public function exportValidation( $month, $year, $xuser=null ){
        $this->joursTravailMois = $this->getAllDaysInMonth($month, $year, true);

        if(is_null($xuser)){
            $xuser = $this->user->id;
        }
        //Utilisateur
        $user = User::find($xuser);

        //Récupérer la saisie de l'utilisateur dans ce mois
        $tabSaisie  = $this->getSaisieByUserAndDate(true, $xuser,  $month, $year, array(), true);

        //Récupérer la saisie de l'utilisateur dans ce mois
        $tabPlanification  = $this->getSaisieByUserAndDate(false, $xuser,  $month, $year, array(), true);

        //Libellé des mois
        $lib_mois = libelleMois();

        $this->CreerFichierExportValidation($month, $year, $user, $tabSaisie[0], $tabPlanification[0],  $lib_mois);
    }
    /**
     * Style du fichier excel
     * @return mixed
     */
    private function styleExport(){
        //Styles
        $style['entete_ligne'] = array(    'font' => array(  'bold'         => true,
            'color'        => array( 'rgb'=>'ffffff')
        ),
            'alignment' => array(   'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true
            ),
            'fill' => array(    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '333333')
            )
        );

        $style['entete_tab'] = array(
            'font' => array(    'bold'         => true,
                'color'        => array( 'rgb'=>'333333')
            ),
            'alignment' => array(   'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true
            ),
            'fill' => array(    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFAF0')
            )
        );

        $style['cel_weekend'] = array(
            'font' => array(    'bold'         => true,
                'color'        => array( 'rgb'=>'333333')
            ),
            'alignment' => array(   'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true
            ),
            'fill' => array(    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0C0C0')
            )
        );

        $style['cel_saisie'] = array(
            'alignment' => array(   'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $style['cel_ticket'] = array(
            'alignment' => array(  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true)
        );
        return $style;
    }
	
    /**
     * Ecriture du fichier
     * @param $month
     * @param $year
     * @param $user
     * @param $saisieParTicket
     * @param $lib_mois
     */
    private function creerFichierExport($is_saisie, $month, $year, $user, $saisieParTicket, $lib_mois){
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel;

        $largeur_colonne_ticket = 25;
        $largeur_colonne_saisie = 4;
        $hauteur_ligne = 140;

        $style = $this->styleExport();

        //ENTETE : Ligne 1 => Merge
        $col = 0;
        $row= 1;

        if($is_saisie)
            $msg_part1 = $this->lang->line('application_msg_export_saisie_part1');
        else
            $msg_part1 = $this->lang->line('application_msg_export_planification_part1');

        $message_entete = $msg_part1 .ucfirst($user->firstname). ' ' .ucfirst($user->lastname). $this->lang->line('application_msg_export_part2') . $lib_mois[$month].' '.$year;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $message_entete);
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['entete_ligne']);
        //merge
        $col=$col+count($this->joursTravailMois )+1;
        $cell_fin = PHPExcel_Cell::stringFromColumnIndex($col).$row;
        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cell_fin);


        $row++; //ligne vide

        //ENTETE-TABLEAU
        $col = 0;
        $row++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ucfirst($this->lang->line('application_ticket')));
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['entete_tab']);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ucfirst($this->lang->line('application_project')));
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['entete_tab']);
        $col++;

        foreach($this->joursTravailMois as $key=>$item){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ucfirst($this->lang->line('application_'.$item->weekdaylib)).' '.$item->day.' '.$lib_mois[$month].'  '.$year);
            $cell = PHPExcel_Cell::stringFromColumnIndex($col).$row;
            if($item->isWorkDay !== '0')
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['entete_tab']);
            else
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['cel_weekend']);

            $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setTextRotation(90);
            $col++;
        }
        //Hauteur de la ligne 3
        $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight($hauteur_ligne);

         //Tableau
        $this->ecrireTableauSaisieDansFichierParTypeTicket($objPHPExcel, $row, $col, $saisieParTicket[$this->type_ticket_projet->alias], $style);
        
        //Tableau
        $this->ecrireTableauSaisieDansFichierParTypeTicket($objPHPExcel, $row, $col, $saisieParTicket[$this->type_ticket_defaut->alias], $style);
        
        //Style des colonnes => largeur
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(0)->setWidth($largeur_colonne_ticket);
        $objPHPExcel ->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth($largeur_colonne_ticket);
        for ($i=2; $i<$col; $i++):
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth($largeur_colonne_saisie);
        endfor;

        //Style du tableau => border
        $objPHPExcel->getActiveSheet()->getStyle("A1:".$objPHPExcel->setActiveSheetIndex(0)->getHighestColumn().$row)->applyFromArray(
            array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'DDDDDD')
                    )
                )
            )
        );

        //Ecriture du fichier
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $filename = ucfirst(($is_saisie)?$this->lang->line('application_saisietemps'):$this->lang->line('application_planification')).'-'.ucfirst($user->firstname).' '. ucfirst($user->lastname).'-'. date("d-m-Y_H") . "-" . date("i").".xlsx";;

        //POUR ENLEVER LE CODE SOURCE DE LA PAGE !!!
        $this->theme_view = 'blank';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }


    /**
    * Ecrire dans le fichier la saisie Par type de ticket
    **/
    function ecrireTableauSaisieDansFichierParTypeTicket(&$objPHPExcel, &$row, &$col,  $saisieParTicket, $style){
        foreach($saisieParTicket as $ticket_id=>$ticket_data){
            $col = 0;
            $row++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $ticket_data->ticket_name);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->applyFromArray($style['cel_ticket']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $ticket_data->project_name);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->applyFromArray($style['cel_ticket']);
            $col++;
            foreach($this->joursTravailMois as $key=>$item){
                if(isset($ticket_data->temps[$item->day]) ) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $ticket_data->temps[$item->day]->nbhours);
                    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->applyFromArray($style['cel_saisie']);
                }else{
                    if ($item->isWorkDay !== '0') {
                        //on ne doit pas avoir ce cas
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Erreur');
                    }
                    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->applyFromArray($style['cel_weekend']);
                }
                $col++;
            }
        }
    }

    /**
     * Remplir une ligne de saisie dans le fichier de validation
     * @param $row
     * @param $col
     * @param $typeSaisie
     * @param $planifOuSaisie
     * @param $ticket_id
     * @param $ticket_data
     * @param $planificationParTicket
     * @param $style
     */
    private function remplireLigneExport($row, $col, $typeSaisie, $planifOuSaisie, $ticket_id, $ticket_data, $planificationParTicket, $style){
        $col++; $col_string = PHPExcel_Cell::stringFromColumnIndex($col);
        $this->objPHPExcel->getActiveSheet()->setCellValue($col_string.$row,ucfirst(($planifOuSaisie == 0)?$this->lang->line('application_export_saisie'):$this->lang->line('application_planification')));
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->applyFromArray($style['cel_ticket']);

        if($planifOuSaisie == 1 ){
            if(!isset($planificationParTicket[$ticket_id])){
                $tab = null;
                //return false;//cas où pas de planification
            }else{
                $tab = $planificationParTicket[$ticket_id];
            }
        }else{
            $tab = $ticket_data;
        }


        foreach($this->joursTravailMois as $key=>$item){
            $col++;
            if(isset($tab)){
                if(isset($tab->temps[$item->day])) {
                    $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($typeSaisie == 0) ? $tab->temps[$item->day]->nbhours : $tab->temps[$item->day]->autreSaisie);
                    $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->applyFromArray($style['cel_saisie']);
                }else{
                    if ($item->isWorkDay !== '0') {
                        //on ne doit pas avoir ce cas
                        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Erreur');
                    }
                    $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->applyFromArray($style['cel_weekend']);
                }
            }else{
                if ($item->isWorkDay === '0')
                    $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->applyFromArray($style['cel_weekend']);
            }
        }
    }

    /**
     * Fichier d'export; Merge colonnes
     * @param $row_deb
     * @param $row_fin
     * @param $col
     * @param $cell_value
     * @param $style_cell
     */
    private function remplirCellMerge($row_deb, $row_fin, $col, $cell_value, $style_cell){
        $col_string = PHPExcel_Cell::stringFromColumnIndex($col);
        $this->objPHPExcel->getActiveSheet()->setCellValue($col_string.$row_deb,$cell_value);
        $this->objPHPExcel->getActiveSheet()->mergeCells($col_string.$row_deb.':'.$col_string.$row_fin);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row_deb)->applyFromArray($style_cell);
    }

    /**
     * Remplir l'entête du tableau dans le fichier de validation
     * @param $row
     * @param $col
     * @param $style
     */
    private function remplirEntete($row, $col, $style){
        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ucfirst($this->lang->line('application_ticket')));
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['entete_tab']);
        $col++;
        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ucfirst($this->lang->line('application_project')));
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['entete_tab']);
        $col++;

        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ucfirst($this->lang->line('application_export_type_saisie1')));
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['entete_tab']);
        $col++;

        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ucfirst($this->lang->line('application_export_type_saisie2')));
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['entete_tab']);
        $col++;

        foreach($this->joursTravailMois as $key=>$item){
            $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ucfirst($this->lang->line('application_'.$item->weekdaylib)).' '.$item->day.' '.$lib_mois[$month].'  '.$year);
            $cell = PHPExcel_Cell::stringFromColumnIndex($col).$row;
            if($item->isWorkDay !== '0')
                $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['entete_tab']);
            else
                $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['cel_weekend']);

            $this->objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setTextRotation(90);
            $col++;
        }
    }

    /**
     * Ecriture du fichier de validation
     * @param $month
     * @param $year
     * @param $user
     * @param $saisieParTicket
     * @param $lib_mois
     */
    private function CreerFichierExportValidation($month, $year, $user, $saisieParTicket, $planificationParTicket, $lib_mois){
        $this->load->library('PHPExcel');
        $this->objPHPExcel = new PHPExcel;

        $largeur_colonne_ticket = 25;
        $largeur_colonne_saisie = 4;
        $hauteur_ligne = 140;

        $style = $this->styleExport();

        //ENTETE : Ligne 1 => Merge
        $col = 0;
        $row= 1;

        $msg_part1 = $this->lang->line('application_msg_export_validation_part1');
        $message_entete = $msg_part1 .ucfirst($user->firstname). ' ' .ucfirst($user->lastname). $this->lang->line('application_msg_export_part2') . $lib_mois[$month].' '.$year;
        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $message_entete);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style['entete_ligne']);
        //merge
        $col=$col+count($this->joursTravailMois )+3;
        $cell_fin = PHPExcel_Cell::stringFromColumnIndex($col).$row;
        $this->objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cell_fin);


        $row++; //ligne vide

        //ENTETE-TABLEAU
        $row++;
        $this->remplirEntete($row, 0,  $style);

        //Tableau
        $tab_rows = array();
        $this->EcrireTableauValidationDansFichierParTypeTicket($row, $col, $saisieParTicket[$this->type_ticket_projet->alias], $style);
        
        //Tableau
        $this->EcrireTableauValidationDansFichierParTypeTicket($row, $col, $saisieParTicket[$this->type_ticket_defaut->alias], $style);
        
        //Hauteur de la ligne 3
        $this->objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight($hauteur_ligne);

        //Style des colonnes => largeur
        $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(0)->setWidth($largeur_colonne_ticket);
        $this->objPHPExcel ->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth($largeur_colonne_ticket);
        $this->objPHPExcel ->getActiveSheet()->getColumnDimensionByColumn(2)->setWidth($largeur_colonne_ticket);
        $this->objPHPExcel ->getActiveSheet()->getColumnDimensionByColumn(3)->setWidth($largeur_colonne_ticket);
        for ($i=4; $i<$col; $i++):
            $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth($largeur_colonne_saisie);
        endfor;

        //Style du tableau => border
        $this->objPHPExcel->getActiveSheet()->getStyle("A1:".$this->objPHPExcel->setActiveSheetIndex(0)->getHighestColumn().$row)->applyFromArray(
            array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'DDDDDD')
                    )
                )
            )
        );

        foreach($tab_rows as $xrow ){
            //Style du tableau => border
            $this->objPHPExcel->getActiveSheet()->getStyle("A".$xrow.":".$this->objPHPExcel->setActiveSheetIndex(0)->getHighestColumn().$xrow)->applyFromArray(
                array(
                    'borders' => array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                            'color' => array('rgb' => 'DDDDDD')
                        )
                    )
                )
            );
        }

        //Ecriture du fichier
        $writer = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
        $filename = ucfirst($this->lang->line('application_validation')).'-'.ucfirst($user->firstname). ' ' .ucfirst($user->lastname). '-' . date("d-m-Y_H") . "-" . date("i").".xlsx";;

        //POUR ENLEVER LE CODE SOURCE DE LA PAGE !!!
        $this->theme_view = 'blank';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    /**
    * EcrireTableauValidationDansFichierParTypeTicket
    * Passage par référence
    **/
    function EcrireTableauValidationDansFichierParTypeTicket(&$row, &$col,  $saisieParTicket, $style){
        foreach($saisieParTicket as $ticket_id=>$ticket_data){//var_dump($ticket_id,$ticket_data ); exit();
            $col = 0;
            $row++;

            $row_deb = $row;

            //ticket
            $this->remplirCellMerge($row_deb, $row_deb + 3, $col, $ticket_data->ticket_name, $style['cel_ticket']);

            //projet
            $col++;
            $this->remplirCellMerge($row_deb, $row_deb + 3, $col, $ticket_data->project_name, $style['cel_ticket']);

            //type saisie 1
            $col++;
            $this->remplirCellMerge($row_deb, $row_deb + 1, $col, ucfirst($this->lang->line('application_saisietemps')), $style['cel_ticket']);
            $this->remplirCellMerge($row_deb+2, $row_deb + 3, $col, ucfirst($this->lang->line('application_export_autre_saisie')), $style['cel_ticket']);

            //4 rows ticket
            $this->remplireLigneExport($row, $col, 0, 1, $ticket_id, $ticket_data, $planificationParTicket, $style); //0:saisieTemps 1:Palnification
            $row++;
            $this->remplireLigneExport($row, $col, 0, 0, $ticket_id, $ticket_data, $planificationParTicket, $style); //0:saisieTemps 0:Saisie
            $row++;
            $this->remplireLigneExport($row, $col, 1, 1, $ticket_id, $ticket_data, $planificationParTicket, $style); //1:autreSaisie 1:Palnification
            $row++;
            $this->remplireLigneExport($row, $col, 1, 0, $ticket_id, $ticket_data, $planificationParTicket, $style); //0:autreSaisie 0:Saisie
            $tab_rows[] = $row;
        }

    }

    /**
     * Validation de la saisie des temps
     */
    function validerSaisie(){
        $post = $this->input->post();
        
        $valider_users = array();
        foreach ($post as $key => $value) {
            //users à valider 
            $matchU = preg_match_all('/^user_(\d+)/', $key, $matchesU);
            if($matchU){
               $valider_users[$matchesU[1][0]] = 0;
            }

            $matchC = preg_match_all('/^check_(\d+)/', $key, $matchesC);
            if($matchC){
               $valider_users[$matchesC[1][0]] = 1; //checked
            }
        }

        //date
        $date_tab = explode('-', $post['mois_annee']);
        $mois = $date_tab[0];
        $annee = $date_tab[1];

        if(count($valider_users)>0){
            if( $this->saisieTemps->executeValidation($valider_users, $mois, $annee))
                $this->session->set_flashdata('message', 'success:'.'Vos données ont été sauvgardées');
            else
                show_404('Une erreur a été rencontrée lors de la validation.');
        }else{
            $this->session->set_flashdata('message', 'success:'."Vos n'avez rien à enregistrer");
        }
        
        redirect($_SERVER['HTTP_REFERER']);
    }

}