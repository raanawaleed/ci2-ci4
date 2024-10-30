<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CTicketsController extends BaseController {
               
	function __construct()
	{
		parent::__construct();
		$access = FALSE;
		$this->load->model('Ref_type_occurences_model','referentiels');
		$this->load->model('RefType_model','refType');
		$this->load->model('ticket_model');
		if($this->client){
		}elseif($this->user){
		}else{
			redirect('login');
		}
		//filtre des types de tickets
		$idType = $this->refType->getRefTypeByName("ticket")->id;		
		$submenus =  $this->referentiels->getReferentielsByIdType($idType);
		$this->view_data['submenu'] = array(); 
		foreach($submenus as $submenu){
			$this->view_data['submenu'][$submenu->name] ='ctickets/filter/'.$submenu->id; 
		}
		//filtre des catégories projets
		$idType = $this->refType->getRefTypeByName("catégorie projet")->id;
		$categorie_project = $this->referentiels->getReferentielsByIdType($idType);
		$this->view_data['categorie_projet'] = array();
		$this->view_data['categorie_projet']['Tous']='ctickets/categorieprojet/0';
		foreach($categorie_project as $cat){
			$this->view_data['categorie_projet'][$cat->name] ='ctickets/categorieprojet/'.$cat->id; 
		}
		
	}	
	
 //test
 function getTicketsToDatatables()
 {

	
	mb_internal_encoding('UTF-8');

	 $data = $row = array();
	 //var_dump($data);exit;
	
	 $this->load->model('ticket_model');
	
	 $tickets = $this->ticket_model->getRows($_POST);

	 $i = $_POST['start'];
	 foreach($tickets as $ticket){
		 $i++;
		 $data[] = array(
			             $ticket->ticket_id,
						 $ticket->ticket_id,
						 $ticket->subject,
						 $ticket->project,
						 $ticket->start,
						 $ticket->end,
						 $ticket->collaborater_firstname .' '. $ticket->collaborater_lastname  ,
						 $ticket->type,

		 );
	 }

	$output = array(
		 "draw" => $_POST['draw'],
		 "recordsTotal" => $this->ticket_model->countAll($_POST),
		 "recordsFiltered" => $this->ticket_model->countFiltered($_POST),
		 "data" => $data
		 
	 );
	 $this->theme_view = 'blank';
	 //Output to JSON format
	 echo json_encode($output);
			//return $output ;
 }

	// Afficher tous les tickets fermés
	function closed()
	{
		$options = array('conditions' => array("closed = 1"));
		$tickets = Ticket::all('all',$options);	

		foreach($tickets as $key => $ticket){
			if(!is_null($ticket->project_id) && $ticket->project_id != 0 && $ticket->project_id != '0') 
				$ticket->project_id = Project::find($ticket->project_id);
			
			if(!is_null($ticket->sub_project_id) && $ticket->sub_project_id != 0 && $ticket->sub_project_id != '0')
				$ticket->sub_project_id = ProjectHasSubProject::find(array('id' => $ticket->sub_project_id));
			
			if($ticket->collaborater_id != 0)
			{ 	 
				$ticket->collaborater_id =User::find($ticket->collaborater_id); 
			}	
			$ticket->status= $this->referentiels->getReferentielsById($ticket->status)->name; 

		}
		$projets = Project::all();	

		$this->view_data['user'] = $user; 
		$this->view_data['ticket'] = $tickets; 	 
		$this->view_data['projets'] = $projets; 	 
		$this->content_view = 'tickets/all';

	}


	//Afficher tous les tickets (sans restriction sur le user)
	function index()
	{
		$user = User::find($this->user->id);

		if(($user->admin ==="1")){
			$this->content_view = 'tickets/all_tickets';

		}
		else{
			$this->content_view = 'tickets/all_tickets_not_admin';
		}
		}
	
	//Filtrer sur les catégories projets
	function categorieprojet($id)
	{
		$user = User::find($this->user->id);
		
		//afficher tout type de projets
		if($id == 0) {
			redirect('ctickets');
		}
		else {
			$tickets = $this->ticket_model->getTicketByTypeProjet($id);
		}
		
		foreach($tickets as $ticket){
			if(!is_null($ticket->project_id) && $ticket->project_id != 0 && $ticket->project_id != '0') 
				$ticket->project_id = Project::find($ticket->project_id);
			
			if(!is_null($ticket->sub_project_id) && $ticket->sub_project_id != 0 && $ticket->sub_project_id != '0')
				$ticket->sub_project_id = ProjectHasSubProject::find(array('id' => $ticket->sub_project_id));
			
			if($ticket->collaborater_id != 0)
			{ 	 
				$ticket->collaborater_id =User::find($ticket->collaborater_id); 
			}	
			$ticket->status= $this->referentiels->getReferentielsById($ticket->status)->name; 
		}
		
		$this->view_data['user'] = $user; 
		$this->view_data['ticket'] = $tickets;
		$this->content_view = 'tickets/all';
	}
	
	//Filtrer sur les types tickets
	function filter($condition =False, $id =FALSE)
	{
		$user = User::find($this->user->id); 
		$idType = $this->refType->getRefTypeByName("ticket")->id;
        $idClosed = $this->referentiels->getReferentiels($idType,"Fermé")->id;
        $occDeleted = $this->referentiels->getReferentiels($idType,"Supprimé");

        if($id == FALSE){
				//je filtre sur les statuts
        		switch ($condition) {
        			//filtrer sur tous
        			case 0:
        				redirect('ctickets/tous');
        				break;
        			//filtrer sur closed
        			case 1:
        				redirect('ctickets/closed');
        				break;
        			//filtrer sur deleted
        			case 2:
        				$options = array('conditions' => " deleted = 1");
        				break;
        			//filtrer sur les occurences du status
        			default:
        				$options = array('conditions' => 'status = '.$condition . ' and deleted = 0');
        		}
				
		}else {
            if($condition == $occDeleted->id) {
                $options = array('conditions' => 'collaborater_id = '.$id." AND deleted = 1");
            } else{
                $options = array('conditions' => 'collaborater_id = '.$id.' 
			            AND closed =0 AND deleted = 0');
            }
		}

        $tickets = Ticket::all($options);
		foreach($tickets as $ticket){
			if(!is_null($ticket->project_id) &&  $ticket->project_id !== 0 && $ticket->project_id != '0')
				$ticket->project_id = Project::find($ticket->project_id);
			
			if(!is_null($ticket->sub_project_id) && $ticket->sub_project_id !== 0 && $ticket->sub_project_id != '0')
				$ticket->sub_project_id = ProjectHasSubProject::find(array('id' => $ticket->sub_project_id));
		
			if($ticket->collaborater_id != 0)
			{ 	 
				$ticket->collaborater_id =User::find($ticket->collaborater_id); 
			}
		
			$ticket->status= $this->referentiels->getReferentielsById($ticket->status)->name; 
		}
		$this->view_data['user'] = $user;
        $this->view_data['ticket'] = $tickets;
        if($condition == $occDeleted->id) $this->view_data['occDeleted'] = $occDeleted;
        $this->content_view = 'tickets/all';
	}


    //Filtrer sur les types tickets
    function filter_deleted($id =FALSE)
    {
        $user = User::find($this->user->id);

        if($id == FALSE){
            $options = array('conditions' => 'deleted = 1');
        }else {
            $options = array('conditions' => 'collaborater_id = '.$id.' 
			AND status != '.$idClosed ." and deleted = 1");
        }

        $tickets = Ticket::all($options);
        foreach($tickets as $ticket){
            if(!is_null($ticket->project_id) &&  $ticket->project_id !== 0 && $ticket->project_id != '0')
                $ticket->project_id = Project::find($ticket->project_id);

            if(!is_null($ticket->sub_project_id) && $ticket->sub_project_id !== 0 && $ticket->sub_project_id != '0')
                $ticket->sub_project_id = ProjectHasSubProject::find(array('id' => $ticket->sub_project_id));

            if($ticket->collaborater_id != 0)
            {
                $ticket->collaborater_id =User::find($ticket->collaborater_id);
            }

            $ticket->status= $this->referentiels->getReferentielsById($ticket->status)->name;
        }
        $this->view_data['user'] = $user;
        $this->view_data['ticket'] = $tickets;
        $this->content_view = 'tickets/all';
    }
	//Créer un nouveau ticket
	function create()
	{
		if($_POST) {
            $config['upload_path'] = './files/media/';
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jpe|pdf|doc|docx|rtf|text|txt|xls|xlsx';

            $this->load->library('upload', $config);
            $this->load->helper('notification');

            unset($_POST['userfile']);
            unset($_POST['file-name']);

            unset($_POST['send']);
            unset($_POST['_wysihtml5_mode']);
            unset($_POST['files']);
            $option = array("id_vcompanies" => $_SESSION['current_company']);
            $settings = Setting::find($option);
            $client = Client::find_by_id($_SESSION['current_company']);
            $user = User::find_by_id($settings->ticket_default_owner);
            $_POST['from'] = $this->user->id;
           
            $_POST['user_id'] = $settings->ticket_default_owner;
			mb_internal_encoding('UTF-8');

            $_POST['created'] = time();
            $_POST['subject'] = htmlspecialchars($_POST['subject']);
			//$_POST['nbre_heures'] = time();

            $_POST['new_created'] = 1;

            //projets
            if ($_POST['project_id'] == "#")
                $_POST['project_id'] = NULL;
            if ($_POST['sub_project_id'] == "#")
                $_POST['sub_project_id'] = NULL;

            //Liste destination
            $tab_recipient = $_POST['collaborater_id'];
            $post = $_POST;
            if (empty($post['project_id']) || !isset($post['project_id'])){
            	$this->session->set_flashdata('message', 'error:Veuillez vérifier le projet sélectionné');
            	redirect("ctickets");
        	}
            //upload de la pièce jointe:
            $upload_data = false;
            if(! empty($_FILES)){
                $upload_data = $this->upload_pj();
            }
            //sauvegarde du/des tickets
            $this->db->trans_begin();
            foreach ($tab_recipient as $key => $rec_user) {
                $post['collaborater_id'] = $rec_user;
                $ticket_reference = Setting::find($option);
                $post['reference'] = $ticket_reference->ticket_reference;

                $ticket = Ticket::create($post);
                $ticket_reference->update_attributes(array('ticket_reference' => $post['reference']+1));

                //sauvegarde de la pièce jointe
                if($upload_data){
                    foreach ($upload_data as $i => $up) {
                        $attributes = array('ticket_id' => $ticket->id, 'filename' => $up['upload_data']['orig_name'], 'savename' => $up['upload_data']['file_name']);
                        TicketHasAttachment::create($attributes);
                    }
                }
                
            } //fin des tickers mutliple

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_create_ticket_error'));
                redirect('ctickets');
            }else {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_create_ticket_success'));
                redirect('ctickets/');
            }
		}else
		{
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_create_ticket');
			$users=$this->db->select('*')->from('users')->get()->result();
			$this->view_data['collaboraters'] =  User::find('all',array('conditions' => array('status=?','active')));
			$options = array('conditions' => array('progress != ?', 100));
			$this->view_data['projects'] =  Project::all($options);			
			//les types du ticket
			$idType = $this->refType->getRefTypeByName("ticket")->id;	
			$this->view_data['status'] = $this->referentiels->getReferentielsByIdType($idType);
			$idetat = $this->config->item("type_id_etat_tache");
			$this->view_data['etats'] = $this->referentiels->getReferentielsByIdType($idetat);
			$idPriorite =  $this->config->item("type_id_priorite_tache");
			$this->view_data['priorite'] = $this->referentiels->getReferentielsByIdType($idPriorite);
			$this->content_view = 'tickets/_ticket';
		}	
	}

    /**
     * upmoad multiple des pièces jointes
     * @return array
     */
	function upload_pj(){
        $upload_data = array();
        $filesCount = count($_FILES['userfile']['name']);

        for($i = 0; $i < $filesCount; $i++){
            $_FILES['file']['name']     = $_FILES['userfile']['name'][$i];
            $_FILES['file']['type']     = $_FILES['userfile']['type'][$i];
            $_FILES['file']['tmp_name'] = $_FILES['userfile']['tmp_name'][$i];
            $_FILES['file']['error']     = $_FILES['userfile']['error'][$i];
            $_FILES['file']['size']     = $_FILES['userfile']['size'][$i];

        	if( !empty($_FILES['file']['tmp_name']) ){
        		// Upload file to server
                if($this->upload->do_upload('file')){
                    // Uploaded file data
                    $upload_data[$i]['upload_data'] = $this->upload->data();
               }else{
                    $error = $this->upload->display_errors('', ' ');
                    $this->session->set_flashdata('message', 'error:' . $error);
                    redirect('ctickets');
                }
            }
        }
        

        return $upload_data;
    }
	//Editer un ticket
	function editTicket($id)
	{	
		if($_POST){
			$_POST['collaborater_id'] = $_POST['collaborater_id'][0];
			$config['upload_path'] = './files/media/';
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = '*';
			$this->load->library('upload', $config);
			$this->load->helper('notification');
			unset($_POST['userfile']);
			unset($_POST['file-name']);
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$_POST['from'] = $this->user->id;
			$_POST['created'] = time();
			$_POST['subject'] = htmlspecialchars($_POST['subject']);
			if($_POST['collaborater_id'] == ''){
				unset($_POST['collaborater_id']);
			}else {
				$_POST['new_created'] =  1 ; 
			}
			$ticket = Ticket::find($_POST['id']);
			$ticket->update_attributes($_POST);
			$email_attachment = FALSE;
			if ( ! $this->upload->do_upload())
			{
				$error = $this->upload->display_errors('', ' ');
				$this->session->set_flashdata('message', 'error:'.$error);
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());
				$attributes = array('ticket_id' => $ticket->id, 'filename' => $data['upload_data']['orig_name'], 'savename' => $data['upload_data']['file_name']);
				$option=array("ticket_id"=>$id);
				$attachment = TicketHasAttachment::find($option);
				$attachment->update_attributes($attributes);
				$email_attachment = $data['upload_data']['file_name'];
			}
       		if(!$ticket){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_update_ticket_error'));
				redirect('ctickets');
			}
       		else{
				$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_update_ticket_success'));
				redirect('ctickets/view/'.$id);
			   }
		}else{
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_ticket');	
			$this->view_data['ticket'] =  Ticket::find($id);	
			$this->view_data['collaboraters'] =User::find('all',array('conditions' => array('status=?','active')));
			$this->view_data['projects'] =  Project::find('all');
			$refType = $this->refType->getRefTypeByName("ticket")->id;
			$this->view_data['status'] = $this->referentiels->getReferentielsByIdType($refType); 
			$idType = $this->config->item("type_id_type_tache");
			$this->view_data['types'] = $this->referentiels->getReferentielsByIdType($idType);
			$idPriorite =  $this->config->item("type_id_priorite_tache");
	$refEtat =$this->config->item("type_id_etat_tache");
			$this->view_data['etats'] = $this->referentiels->getReferentielsByIdType($refEtat);
			$this->view_data['priorite'] = $this->referentiels->getReferentielsByIdType($idPriorite);
			$this->content_view = 'tickets/_ticket';
		}	
	}	

    //Copier un ticket
    function copyTicket($id)
    {
        $ticket_source = Ticket::find($id)->attributes();;
        $tab_ticket_source = [];
        foreach ($ticket_source as $k => $v){
            $tab_ticket_source[$k] = $v;
        }
        unset($tab_ticket_source['id']);

        $option=array("id_vcompanies"=>$_SESSION['current_company']);
        $ticket_reference = Setting::find($option);
        $tab_ticket_source['reference'] = $ticket_reference->ticket_reference;

        $ticket_copie = Ticket::create($tab_ticket_source);
        $ticket_reference->update_attributes(array('ticket_reference' => $tab_ticket_source['reference'] +1));

        //sauvegarde de la pièce jointe
        $source = TicketHasAttachment::find('all', $option=array('id' =>$id));
        if($source){
            $attachement_source = $source->attributes();
//var_dump($attachement_source);exit;
            $tab_attachement_source = [];
            foreach ($attachement_source as $k => $v){
                $tab_attachement_source[$k] = $v;
            }
            if(count($tab_attachement_source) > 0) {
                unset($tab_attachement_source['id']);
                $tab_attachement_source['ticket_id'] = $ticket_copie->id;

                TicketHasAttachment::create($tab_attachement_source);
            }
			
        }

        $this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_duplicate_ticket_success'));
			redirect('ctickets/view/'.$id);
        //redirect('ctickets/view/'.$id);
		//$this->content_view = 'tickets/viewdetail';	
		//header("Refresh:0");
		//redirect('ctickets');
    }

    // Afficher le détail d'un ticket
	function view($id = FALSE ,  $taskId = FALSE)
	{ 
		$current_user=$this->db->select('*')
		->from('users')
		->where('id',$this->session->userdata["user_id"])
		->get()
		->result();
			$test=$this->ticket_model->getPeriodPerTicket($id);
			$this->view_data['periode']=$test->periode;
	     $this->load->database();
		 $this->view_data["current_user"] = $current_user;
		 
		 //var_dump($test[0]->periode);exit;

		$this->view_data['submenu'] = array();
		$ticket= Ticket::find_by_id($id);
		$user=$this->ticket_model->getUserCreatedTicket($ticket->user_id)[0];
		$this->view_data['user'] = $user;

		//var_dump($user);exit;
		if(!is_null($ticket->from) && $ticket->from !== '0'){
			$user= User::find($ticket->from);
			$ticket->from = $user->firstname.' '.$user->lastname;
		}
		foreach($ticket->ticket_has_articles as $val){
			$user =  User::find($val->from); 
			$val->from = $user->firstname.' '.$user->lastname; 	
		}
		$ticket->status= $this->referentiels->getReferentielsById($ticket->status)->name; 
		$this->view_data['ticket_type']  = $this->referentiels->getReferentielsById($ticket->type_id)->name;
		if(!is_null($ticket->collaborater_id))
			$ticket->collaborater_id= User::find($ticket->collaborater_id);

		if(!is_null($ticket->project_id) &&  $ticket->project_id !== 0 && $ticket->project_id != '0')
			$ticket->project_id = Project::find($ticket->project_id);
			
		if(!is_null($ticket->sub_project_id) && $ticket->sub_project_id !== 0 && $ticket->sub_project_id != '0')
			$ticket->sub_project_id = ProjectHasSubProject::find(array('id' => $ticket->sub_project_id));
		//var_dump($ticket->sub_project_id);exit;
		$this->view_data['ticket'] = $ticket; 
		$val = Ticket::find($ticket->id);
		if($ticket->collaborater_id->id == $this->user->id){ 
			$data['new_created'] = 0;
			$val->update_attributes($data);
		}

		$this->content_view = 'tickets/viewdetail';	
	}
	
	//Ajouter une note à un ticket
	function article($id = FALSE, $condition = FALSE, $article_id = FALSE)
	{
		
		$this->view_data['submenu'] = array(
											$this->lang->line('application_back') => 'ctickets',
											$this->lang->line('application_overview') => 'ctickets/view/'.$id,
										);
		switch ($condition) {
			case 'add':
				$this->content_view = 'tickets/_note';
				if($_POST){  
					$config['upload_path'] = './files/media/';
					$config['encrypt_name'] = TRUE;
					$config['allowed_types'] = '*';
					$this->load->library('upload', $config);
					$this->load->helper('notification');
					unset($_POST['userfile']);
					unset($_POST['file-name']);
					unset($_POST['send']);
					unset($_POST['_wysihtml5_mode']);
					unset($_POST['files']);
					$ticket = Ticket::find_by_id($id);
					$ticket->updated = "1";
					$ticket->save();
					$_POST['internal'] = "0";			
					unset($_POST['notify']);
					$_POST['subject'] = htmlspecialchars($_POST['subject']);
					$_POST['datetime'] = time();
					$_POST['ticket_id'] = $id;
					$_POST['from'] = $this->user->id;
					$_POST['reply_to'] = $ticket->user_id; 
					$article = TicketHasArticle::create($_POST);
					if ( ! $this->upload->do_upload())
						{
							$error = $this->upload->display_errors('', ' ');
							$this->session->set_flashdata('message', 'error:'.$error);
						}
						else
						{
							$data = array('upload_data' => $this->upload->data());
							$attributes = array('article_id' => $article->id, 'filename' => $data['upload_data']['orig_name'], 'savename' => $data['upload_data']['file_name']);
							
							$attachment = ArticleHasAttachment::create($attributes);
						}
		       		if(!$article){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_article_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_article_success'));}
					redirect('ctickets/view/'.$id);
				}else
				{
					$this->theme_view = 'modal';
					$this->view_data['ticket'] = Ticket::find($id);
					$this->view_data['title'] = $this->lang->line('application_add_note');
					$this->view_data['form_action'] = 'ctickets/article/'.$id.'/add';
					$this->content_view = 'tickets/_note';
				}	
				break;
				default:
				redirect('ctickets');
				break;
		}
	}

	//Ajouter un attachement à un ticket
	function attachment($id = FALSE){
		$this->load->helper('download');
		$this->load->helper('file');
		$attachment = TicketHasAttachment::find_by_savename($id);
		$file = './files/media/'.$attachment->savename;
		$mime = get_mime_by_extension($file);
		if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: '.$mime);
            header('Content-Disposition: attachment; filename='.basename($attachment->filename));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            ob_clean();
            flush();
            exit; 
        }
	}
	
	function articleattachment($id = FALSE){
		$this->load->helper('download');
		$this->load->helper('file');
		$attachment = ArticleHasAttachment::find_by_savename($id); 
		$file = './files/media/'.$attachment->savename;
		$mime = get_mime_by_extension($file);
		if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: '.$mime);
            header('Content-Disposition: attachment; filename='.basename($attachment->filename));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            ob_clean();
            flush();  
            exit; 
        }
	}
	
	// Assigner un ticket à un collaborateur
	function assign($id = FALSE)
	{	
		$this->load->helper('notification');
		if($_POST){
			$config['upload_path'] = './files/media/';
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = '*';
			$this->load->library('upload', $config);
			unset($_POST['userfile']);
			unset($_POST['file-name']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			$id = $_POST['id'];
			unset($_POST['id']);
			unset($_POST['notify']);
			$assign = Ticket::find('all',$id);
			$attr = array(); 
			$attr['collaborater_id'] = $_POST['to'];
			$attr['new_created'] ="1" ; 
			$assign->update_attributes($attr);
			$_POST['subject'] = htmlspecialchars($_POST['subject']);
			$_POST['datetime'] = time();
			$_POST['from'] =$this->user->id;
			$_POST['reply_to'] = $assign->user_id; 
			$_POST['ticket_id'] = $id;
			$_POST['internal'] = 0; 
			unset($_POST['user_id']);
			$article = TicketHasArticle::create($_POST);
			$lastArticle = TicketHasArticle::Last();
			if ( ! $this->upload->do_upload())
			{
				$error = $this->upload->display_errors('', ' ');
				$this->session->set_flashdata('message', 'error:'.$error);
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());
				$attributes = array('article_id' => $lastArticle->id, 'filename' => $data['upload_data']['orig_name'], 'savename' => $data['upload_data']['file_name']);
				$attachment = ArticleHasAttachment::create($attributes);
			}
       		if(!$assign){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_ticket_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_assign_ticket_success'));}
			redirect('ctickets/');
		}
		else
		{
			$this->theme_view = 'modal';
			$this->view_data['ticket'] =  Ticket::find($id);	
			$this->view_data['collaboraters'] =User::find('all',array('conditions' => array('status=? And 
			id !=?','active',$this->user->id)));
			$this->view_data['projects'] =  Project::find('all');
			$this->content_view = 'tickets/_ticket';
			$this->view_data['title'] = $this->lang->line('application_assign_to_agents');
			$this->view_data['form_action'] = 'ctickets/assign';
			$this->content_view = 'tickets/_assign';
		}	
	}	
	
	// Afficher le statut d'un ticket
	function status($id = FALSE)
	{	
		$this->load->helper('notification');
		if($_POST){
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['id'];
			unset($_POST['id']);
			$ticket = Ticket::find_by_id($id);
			$attr = array('status' => $_POST['status']);
			$ticket->update_attributes($attr);
       		if(!$ticket){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_status_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_status_success'));}
			redirect('ctickets/view/'.$id);
		}else
		{
			$refType = $this->refType->getRefTypeByName("ticket")->id;
			$this->view_data['status'] = $this->referentiels->getReferentielsByIdType($refType);
			$this->view_data['ticket'] = Ticket::find_by_id($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_status');
			$this->view_data['form_action'] = 'ctickets/status';
			$this->content_view = 'tickets/_status';
		}
	}


		
	// Fermer un ticket, il n'est plus visible dans la liste des tickets
	function close($id = FALSE)
	{	
		$this->load->helper('notification');
		if($_POST){
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['ticket_id'];
			unset($_POST['ticket_id']);
			$ticket = Ticket::find_by_id($id);
			$attr['closed'] = 1;
			$ticket->update_attributes($attr);
			if(isset($ticket->client->email)){ $email = $ticket->client->email; } else {$emailex = explode(' - ', $ticket->from); $email = $emailex[1]; }
			if(isset($_POST['notify']))
			{
				send_ticket_notification($email, '[Ticket#'.$ticket->reference.'] - '.$ticket->subject, $_POST['message'], $ticket->id);
			}
			send_ticket_notification($ticket->user->email, '[Ticket#'.$ticket->reference.'] - '.$ticket->subject, $_POST['message'], $ticket->id);
			$_POST['internal'] = "0";
			unset($_POST['notify']);
			$_POST['subject'] = htmlspecialchars($_POST['subject']);
			$_POST['datetime'] = time();
			$_POST['from'] = $this->user->id;
			$_POST['reply_to'] = $this->user->id;
			$_POST['ticket_id'] = $id;
			$_POST['to'] = $email;
			unset($_POST['client_id']);
			$article = TicketHasArticle::create($_POST);
       		if(!$ticket){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_ticket_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_ticket_close_success'));}
			redirect('ctickets');
		}else
		{
			$this->view_data['ticket'] = Ticket::find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_close');
			$this->view_data['form_action'] = 'ctickets/close';
			$this->content_view = 'tickets/_close';
		}	
	}
	
	// Charger - sauvegarder le type d'une tâche
	function type($id = FALSE)
	{	
		$this->load->helper('notification');
		if($_POST){
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['id'];
			unset($_POST['id']);
			$ticket = Ticket::find_by_id($id); 
			$attr = array('type_id' => $_POST['type_id']);
			$ticket->update_attributes($attr);

       		if(!$ticket){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_assign_type_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_assign_type_success'));}
			redirect('ctickets/view/'.$id);
		}else
		{
			
			$refType =$this->config->item("type_id_type_tache");
			$this->view_data['types'] = $this->referentiels->getReferentielsByIdType($refType);
			$this->view_data['ticket'] = Ticket::find_by_id($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_type');
			$this->view_data['form_action'] = 'ctickets/type';
			$this->content_view = 'tickets/_type';
		}	
	}
	// Charger - sauvegarder l'etat d'une tâche
	function etat($id = FALSE)
	{	
		$this->load->helper('notification');
		if($_POST){
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['id'];
			unset($_POST['id']);
			$ticket = Ticket::find_by_id($id); 
			$attr = array('etat_id' => $_POST['etat_id']);
			$ticket->update_attributes($attr);

       		if(!$ticket){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_assign_etat_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_assign_etat_success'));}
			redirect('ctickets/view/'.$id);
		}else
		{
			
			$refEtat =$this->config->item("type_id_etat_tache");
			$this->view_data['etats'] = $this->referentiels->getReferentielsByIdType($refEtat);
			$this->view_data['ticket'] = Ticket::find_by_id($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_etat');
			$this->view_data['form_action'] = 'ctickets/etat';
			$this->content_view = 'tickets/_etat';
		}	
	}
		/* Charger - sauvegarder la surface d'une tâche
	function surface($id = FALSE)
	{	
		$this->load->helper('notification');
		if($_POST){
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['id'];
			unset($_POST['id']);
			$ticket = Ticket::find_by_id($id); 
			$attr = $_POST['surface'];
			$ticket->"UPDATE "($attr);

       		if(!$ticket){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_assign_etat_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_assign_etat_success'));}
			redirect('ctickets/view/'.$id);
		}else
		{
			
			//$ref =$this->config->item("type_id_etat_tache");
			$this->view_data['surface'] = $attr ;
			//var_dump($ref);exit;
			$this->view_data['ticket'] = Ticket::find_by_id($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = "Quantité";
			$this->view_data['form_action'] = 'ctickets/surface';
			$this->content_view = 'tickets/_surface';
		}	
	}
	*/
	//bluk
	function bulk($action){	
		$this->load->helper('notification'); 
		if($_POST['list'] != ''){
			$list = explode(",", $_POST['list']); 
			switch ($action) {
				case 'close':
					
					$attr['closed'] = 1;
					$email_message = $this->lang->line('messages_bulk_ticket_closed');
					$success_message = $this->lang->line('messages_bulk_ticket_closed_success');
					break;
				default:
					redirect('ctickets');
				break;
			}
			foreach ($list as $value) {
				$ticket = Ticket::find_by_id($value);
				$ticket->update_attributes($attr);
				if(!$ticket){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_ticket_error'));}
       			else{$this->session->set_flashdata('message', 'success:'.$success_message);}
			}
			redirect('ctickets');
		}else
		{
			redirect('ctickets');
		}	
	}
	
	//Supprimer un ticket
	function deleteTicket($id){
        /*$ticket = Ticket::find_by_id($id);
        $attr['deleted'] = 1;
        $ticket->update_attributes($attr);
        if(!$ticket){$this->session->set_flashdata('message', 'error:Attention: Le ticket n\'a été suprrimé');}
        else{$this->session->set_flashdata('message', 'success:Le ticket a été supprimé');}
        redirect('ctickets');*/
		$ticket = Ticket::find_by_id($id);
		$ticket->delete();
		$sql = 'DELETE FROM ticket_has_articles WHERE ticket_id = "'.$id.'"';
		$this->db->query($sql);
		$sql = 'DELETE FROM tickets where id ="'.$id.'"';
		$this->db->query($sql);
		$this->content_view = 'ctickets/all_tickets';
		if(!$ticket){$this->session->set_flashdata('message', 'error:Attention: La tache  n\'a été suprrimé');}
		else{$this->session->set_flashdata('message', 'success:La tache a été supprimé');}
	{redirect('ctickets');}
	}

}
