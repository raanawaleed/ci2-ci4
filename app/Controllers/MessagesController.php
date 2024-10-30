<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MessagesController extends BaseController {
               
	function __construct()
	{
		parent::__construct();
		$access = FALSE;
		if($this->client){	
			redirect('cprojects');
		}elseif($this->user){
			foreach ($this->view_data['menu'] as $key => $value) { 
				if($value->link == "messages"){ $access = TRUE;}
			}
			if(!$access){redirect('login');}
		}else{
			redirect('login');
		}
		$this->view_data['submenu'] = array(
				 		$this->lang->line('application_new_messages') => 'messages',
				 		$this->lang->line('application_read_messages') => 'messages/filter/read',
				 		);	
		$this->load->database();
	}	
	
	function index()
	{	
		$this->content_view = 'messages/all';
	}
	
	function messagelist($con = FALSE, $deleted = FALSE)
	{
		$max_value = 60;
		if($deleted == "deleted"){ $qdeleted = " AND privatemessages.status = 'deleted' OR privatemessages.deleted = 1 ";}else{ $qdeleted = ' AND privatemessages.status != "deleted" AND privatemessages.deleted = 0 ';  }
		if(is_numeric($con)){ $limit = $con.','; } else{$limit = FALSE;}
		$sql2 = 'SELECT * FROM (SELECT privatemessages.id, privatemessages.`status`, privatemessages.`deleted`, privatemessages.attachment, privatemessages.attachment_link, privatemessages.subject, privatemessages.conversation, privatemessages.sender, privatemessages.recipient, privatemessages.message, privatemessages.`time`, clients.`userpic` as userpic_c, users.`userpic` as userpic_u , users.`email` as email_u , clients.`email` as email_c , CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c
				FROM privatemessages
				LEFT JOIN clients ON CONCAT("c",clients.id) = privatemessages.sender
				LEFT JOIN users ON CONCAT("u",users.id) = privatemessages.sender 
				GROUP by privatemessages.id HAVING privatemessages.recipient = "u'.$this->user->id.'" '.$qdeleted.' ORDER BY privatemessages.`time` DESC) as messages GROUP BY conversation ORDER BY `time` DESC';
				
		$this->db->from('privatemessages'); 
        $this->db->where('recipient',$this->user->id);
        $this->db->where('deleted',0);
        $query = $this->db->get()->result();
		$query2 = $this->db->query($sql2);				
		$rows = $query2->num_rows();
		$this->view_data["message"] = array_filter($query);
		$this->view_data["message_rows"] = $rows;
		if($deleted){$this->view_data["deleted"] = "/".$deleted;}
		$this->view_data["message_list_page_next"] = $con+$max_value;
		$this->view_data["message_list_page_prev"] = $con-$max_value;
		$this->view_data["filter"] = FALSE;
		$this->theme_view = 'ajax';
		foreach($query as $val){
			$val->recipient = User::find($val->recipient);
			$val->sender = User::find($val->sender);
		}
		
		$this->content_view = 'messages/list';
	}
	
	function filter($condition = FALSE, $con = FALSE)
	{
	    $max_value = 60;
	    if(is_numeric($con)){ $limit = $con.','; } else{$limit = FALSE;}
		switch ($condition) {
			case 'read': 
				$sql = 'SELECT privatemessages.id, privatemessages.`status`, privatemessages.subject, privatemessages.attachment, privatemessages.attachment_link, privatemessages.message, privatemessages.sender, privatemessages.recipient, privatemessages.`time`, clients.`userpic` as userpic_c, users.`userpic` as userpic_u , users.`email` as email_u , clients.`email` as email_c , CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c
				FROM privatemessages
				LEFT JOIN clients ON CONCAT("c",clients.id) = privatemessages.sender
				LEFT JOIN users ON CONCAT("u",users.id) = privatemessages.sender 
				GROUP by privatemessages.conversation HAVING privatemessages.recipient = "u'.$this->user->id.'" AND (privatemessages.`status`="Replied" OR privatemessages.`status`="Read") ORDER BY privatemessages.`time` DESC LIMIT '.$limit.$max_value;
				$sql2 = 'SELECT privatemessages.id, privatemessages.`status`, privatemessages.subject, privatemessages.attachment, privatemessages.attachment_link, privatemessages.conversation, privatemessages.sender, privatemessages.recipient, privatemessages.message, privatemessages.`time`, clients.`userpic` as userpic_c, users.`userpic` as userpic_u , users.`email` as email_u , clients.`email` as email_c , CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c
				FROM privatemessages
				LEFT JOIN clients ON CONCAT("c",clients.id) = privatemessages.sender
				LEFT JOIN users ON CONCAT("u",users.id) = privatemessages.sender 
				GROUP by privatemessages.conversation HAVING privatemessages.recipient = "u'.$this->user->id.'" ORDER BY privatemessages.`time` DESC';
				$this->view_data["filter"] = "Read";
				$query = $this->db->query($sql)->result();
				$query2 = $this->db->query($sql2);
				$rows = $query2->num_rows();
				break;
			case 'sent':
				$this->db->from('privatemessages'); 
				$this->db->where('sender',$this->user->id);
				$this->db->where('deleted',0);
				$query = $this->db->get()->result();
			    $sql2 = 'SELECT privatemessages.id, privatemessages.`status`, privatemessages.subject, privatemessages.attachment, privatemessages.attachment_link, privatemessages.conversation, privatemessages.sender, privatemessages.recipient, privatemessages.message, privatemessages.`time`, clients.`userpic` as userpic_c, users.`userpic` as userpic_u , users.`email` as email_u , clients.`email` as email_c , CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c
				FROM privatemessages
				LEFT JOIN clients ON CONCAT("c",clients.id) = privatemessages.recipient
				LEFT JOIN users ON CONCAT("u",users.id) = privatemessages.recipient
				GROUP by privatemessages.id HAVING privatemessages.sender = "u'.$this->user->id.'" ORDER BY privatemessages.`time` DESC';
				$query2 = $this->db->query($sql2);
				$rows = $query2->num_rows();
			    $this->view_data["filter"] = "Sent";
				break;
			case 'marked':
				$sql = 'SELECT privatemessages.id, privatemessages.`status`, privatemessages.`deleted`, privatemessages.attachment, privatemessages.attachment_link, privatemessages.subject, privatemessages.conversation, privatemessages.sender, privatemessages.recipient, privatemessages.message, privatemessages.`time`, clients.`userpic` as userpic_c, users.`userpic` as userpic_u , users.`email` as email_u , clients.`email` as email_c , CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c
				FROM privatemessages
				LEFT JOIN clients ON CONCAT("c",clients.id) = privatemessages.sender
				LEFT JOIN users ON CONCAT("u",users.id) = privatemessages.sender 
				GROUP by privatemessages.id HAVING privatemessages.recipient = "u'.$this->user->id.'" AND privatemessages.`status`="Marked" ORDER BY privatemessages.`time` DESC LIMIT '.$limit.$max_value;
		
				$sql2 = 'SELECT privatemessages.id, privatemessages.`status`, privatemessages.`deleted`, privatemessages.attachment, privatemessages.attachment_link, privatemessages.subject, privatemessages.conversation, privatemessages.sender, privatemessages.recipient, privatemessages.message, privatemessages.`time`, clients.`userpic` as userpic_c, users.`userpic` as userpic_u , users.`email` as email_u , clients.`email` as email_c , CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c
				FROM privatemessages
				LEFT JOIN clients ON CONCAT("c",clients.id) = privatemessages.sender
				LEFT JOIN users ON CONCAT("u",users.id) = privatemessages.sender 
				GROUP by privatemessages.id HAVING privatemessages.recipient = "u'.$this->user->id.'" AND privatemessages.`status`="Marked" ORDER BY privatemessages.`time` DESC';

				$query = $this->db->query($sql)->result();
				$query2 = $this->db->query($sql2);
				$rows = $query2->num_rows();
				$this->view_data["filter"] = "Marked";
				break;
			case 'deleted':
				$sql = 'SELECT * FROM privatemessages
						WHERE sender = "'.$this->user->id.'" OR recipient = "'.$this->user->id.'"
						AND deleted = "1";';
				$query = $this->db->query($sql)->result();
		        $sql2 = 'SELECT privatemessages.id, privatemessages.`status`, privatemessages.`deleted`, privatemessages.attachment, privatemessages.attachment_link, privatemessages.subject, privatemessages.conversation, privatemessages.sender, privatemessages.recipient, privatemessages.message, privatemessages.`time`, clients.`userpic` as userpic_c, users.`userpic` as userpic_u , users.`email` as email_u , clients.`email` as email_c , CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c
				FROM privatemessages
				LEFT JOIN clients ON CONCAT("c",clients.id) = privatemessages.sender
				LEFT JOIN users ON CONCAT("u",users.id) = privatemessages.sender 
				GROUP by privatemessages.id HAVING privatemessages.recipient = "u'.$this->user->id.'" AND (privatemessages.status = "deleted" OR privatemessages.deleted = 1) ORDER BY privatemessages.`time` DESC';
				$this->view_data["filter"] = "Deleted";
				$query2 = $this->db->query($sql2);
				$rows = $query2->num_rows();
				break;
			default:
				$sql = 'SELECT privatemessages.id, privatemessages.`status`, privatemessages.subject, privatemessages.attachment, privatemessages.attachment_link, privatemessages.message, privatemessages.sender, privatemessages.recipient, privatemessages.`time`, clients.`userpic` as userpic_c, users.`userpic` as userpic_u , users.`email` as email_u , clients.`email` as email_c , CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c
				FROM privatemessages
				LEFT JOIN clients ON CONCAT("c",clients.id) = privatemessages.sender
				LEFT JOIN users ON CONCAT("u",users.id) = privatemessages.sender 
				GROUP by privatemessages.conversation HAVING privatemessages.recipient = "u'.$this->user->id.'" AND privatemessages.`status`="New" ORDER BY privatemessages.`time` DESC LIMIT '.$limit.$max_value;
				$this->view_data["filter"] = FALSE;
				$query = $this->db->query($sql)->result();
				$query2 = $this->db->query($sql2);
				$rows = $query2->num_rows();
				break;
		}
		foreach($query as $val){
			$val->recipient = User::find($val->recipient);
			$val->sender = User::find($val->sender);
		}
		$this->view_data["message"] = array_filter($query);
		$this->view_data["message_rows"] = $rows;
		$this->view_data["message_list_page_next"] = $con+$max_value;
		$this->view_data["message_list_page_prev"] = $con-$max_value;	
	    $this->theme_view = 'ajax';
		$this->content_view = 'messages/list';
	}
	
	//Ecrire un message
	function write($ajax = FALSE)
	{	
		if($_POST){
			$tab_recipient = $_POST['recipient'];
			$_POST["company_id"] = $_SESSION['current_company'] ; 
			$config['upload_path'] = './files/media/';
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = '*';
			$this->load->library('upload', $config);
			$this->load->helper('notification');
			unset($_POST['userfile']);
			unset($_POST['file-name']);
			unset($_POST['send']);
			unset($_POST['note-codable']);
			unset($_POST['files']);
			unset($_POST['recipient']);

			$message = $_POST['message'];
			//pièce jointe
			$attachment = FALSE;
			if ( ! $this->upload->do_upload())
			{
				$error = $this->upload->display_errors('', ' ');
				if($error != "You did not select a file to upload."){
				}
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());
				$_POST['attachment'] = $data['upload_data']['orig_name'];
				$_POST['attachment_link'] = $data['upload_data']['file_name'];
				$attachment = $data['upload_data']['file_name'];
			}
			$_POST = array_map('htmlspecialchars', $_POST);
			$_POST['message'] = $message;
			$_POST['new_created'] = 1;
			$_POST['time'] = date('Y-m-d H:i', time());
			$_POST['sender'] = $this->user->id;
			$_POST['status'] = "New";
				//var_dump($tab_recipient); exit();
			//Liste destination
			foreach ($tab_recipient as $key => $rec_user) {
				$receiverart = substr($rec_user, 0, 1);
				$receiverid = substr($rec_user, 1, 9999);	
				$receiver = User::find($rec_user);
				$receiveremail = $receiver->email;	

				$post = $_POST;
				if(!isset($post['conversation'])){$post['conversation'] = random_string('sha1');}
			
				if(isset($post['previousmessage']))
				{
					$status = Privatemessage::find_by_id($post['previousmessage']);					 
					if($receiveremail == $this->user->email)
					{
						$post['recipient'] = $status->recipient;
						$receiver = User::find($status->recipient);
						$receiveremail = $receiver->email;
					}else{
						$post['recipient'] = $rec_user; //à vérifier
					}
	        		$status->status = 'Replied';
	        		$status->save();
	        		unset($post['previousmessage']);
				}else{
					$post['recipient'] = $rec_user;
				}

				//sauvegarde du message
				$message = Privatemessage::create($post);
			}
			
			redirect('messages'); 
		}else
		{
			if($this->user->admin != 1){
				$comp_array = array();
				$thisUserHasNoCompanies = (array) $this->user->companies;
					if(!empty($thisUserHasNoCompanies)){
					foreach ($this->user->companies as $value) {
						array_push($comp_array, $value->id);
					}
					$this->view_data['clients'] = Client::find('all',array('conditions' => array('inactive=? AND company_id in (?)','0', $comp_array)));
				}else{
					$this->view_data['clients'] = (object) array();
				}
			}else{
				$this->view_data['clients'] = Client::find('all',array('conditions' => array('inactive=?','0')));
			}
			$this->view_data['users'] = User::find('all',array('conditions' => array('status=?','active')));
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_write_message');
			$this->view_data['form_action'] = 'messages/write';
			$users=$this->db->select('*')->from('users')->get()->result();
			
			$this->content_view = 'messages/_messages';
			
		}	
	}	
	function update($id = FALSE, $getview = FALSE)
	{	
		if($_POST){
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['id'];
			$message = Privatemessage::find($id);
			$message->update_attributes($_POST);
       		if(!$message){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_write_message_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_write_message_success'));}
			if(isset($view)){redirect('messages/view/'.$id);}else{redirect('messages');}	
		}else
		{
			$this->view_data['id'] = $id;
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_message');
			$this->view_data['form_action'] = 'messages/update';
			$this->content_view = 'messages/_messages_update';
		}	
	}
	function delete($id = FALSE)
	{
		
		$message = Privatemessage::find_by_id($id);
		
		if($message->new_created == 0){
			$message->status = 'deleted';
			$message->deleted = '1';
			$message->save();
			$saved = "ok"; 
		}else{
			$saved = "notok"; 
		}
		$this->content_view = 'messages/all';
		if($saved == "notok"){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_message_error'));}
		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_message_success'));}
		redirect('messages');
	}	
	function mark($id = FALSE)
	{	
		$message = Privatemessage::find_by_id($id);
		if($message->status == 'Marked'){
		    $message->status = 'Read';
		}else{
		    $message->status = 'Marked';
		}
		$message->save();
		$this->content_view = 'messages/all';
		
	}
	function attachment($id = FALSE){
				$this->load->helper('download');
				$this->load->helper('file');

		$attachment = Privatemessage::find_by_id($id);

		$file = './files/media/'.$attachment->attachment_link;
		$mime = get_mime_by_extension($file);

		if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: '.$mime);
            header('Content-Disposition: attachment; filename='.basename($attachment->attachment));
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
	function view($id = FALSE, $filter = FALSE, $additional = FALSE)
	{
		
		$this->view_data['submenu'] = array(
						$this->lang->line('application_back') => 'messages',
				 		);	
		$message = Privatemessage::find_by_id($id);		
		$this->view_data["count"] = "1"; 	
		if(!$filter || $filter == "Marked" || $filter == "Deleted"){ 
			if($message->status == "New"){
				$message->status = 'Read';
				$message->save();
			}
			$this->view_data["filter"] =$filter;
			$this->db->from("privatemessages");
			$this->db->where('id',$id);
			$query = $this->db->get();	
			$row = $query->row();
			$sql2 = 'SELECT privatemessages.id, privatemessages.`status`, privatemessages.conversation, privatemessages.attachment, privatemessages.attachment_link, privatemessages.subject, privatemessages.message, privatemessages.sender, privatemessages.recipient, privatemessages.`time`, privatemessages.`sender` , clients.`userpic` as userpic_c, users.`userpic` as userpic_u , users.`email` as email_u , clients.`email` as email_c , CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c, CONCAT(rec_u.firstname," ", rec_u.lastname) as recipient_u, CONCAT(rec_c.firstname," ", rec_c.lastname) as recipient_c
			FROM privatemessages
			LEFT JOIN clients ON CONCAT("c",clients.id) = privatemessages.sender
			LEFT JOIN users ON CONCAT("u",users.id) = privatemessages.sender
			LEFT JOIN clients AS rec_c ON CONCAT("c",rec_c.id) = privatemessages.recipient
			LEFT JOIN users AS rec_u ON CONCAT("u",rec_u.id) = privatemessages.recipient
			GROUP by privatemessages.id HAVING privatemessages.conversation = "'.$row->conversation.'" ORDER BY privatemessages.`id` DESC LIMIT 100';
			$query2 = $this->db->query($sql2);
			$this->view_data["conversation"] = array_filter($query2->result());
			$sender = User::find($row->sender);
			$this->view_data["sender"] = $sender->firstname.' '.$sender->lastname;
			$this->view_data["count"] = count ($this->view_data["conversation"]);
			}else{	
		        if($filter == "Sent"){
						$this->db->from('privatemessages'); 
						$this->db->where('sender',$this->user->id);
						$this->db->order_by("time", "desc");
						$query = $this->db->get();
						$receiver = User::find($additional);
						$this->view_data["recipient"] = $receiver->firstname.' '.$receiver->lastname;
						
		        	}else{
		        		 $sql = 'SELECT privatemessages.id, privatemessages.`status`, privatemessages.conversation, privatemessages.attachment, privatemessages.attachment_link, privatemessages.subject, privatemessages.message, privatemessages.sender, privatemessages.recipient, privatemessages.`time`, privatemessages.`sender` , clients.`userpic` as userpic_c, users.`userpic` as userpic_u , users.`email` as email_u , clients.`email` as email_c , CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c, CONCAT(users.firstname," ", users.lastname) as recipient_u, CONCAT(clients.firstname," ", clients.lastname) as recipient_c
        				FROM privatemessages
        				LEFT JOIN clients ON (CONCAT("c",clients.id) = privatemessages.sender) OR (CONCAT("c",clients.id) = privatemessages.recipient)
        				LEFT JOIN users ON (CONCAT("u",users.id) = privatemessages.sender) OR (CONCAT("u",users.id) = privatemessages.recipient)
        				GROUP by privatemessages.id HAVING privatemessages.id = "'.$id.'" AND (privatemessages.sender = "u'.$this->user->id.'" OR privatemessages.recipient = "u'.$this->user->id.'") ORDER BY privatemessages.`id` DESC LIMIT 100';
						$query = $this->db->query($sql);
					}
		        $this->view_data["conversation"] = array_filter($query->result());
		        $this->view_data["filter"] = $filter;
		    }
		$this->theme_view = 'ajax';
		foreach($this->view_data["conversation"]  as $val){
			$sender = User::find($val->sender);
			$val->sender_u= $sender->firstname.' '.$sender->lastname;
		} 
		$this->view_data['form_action'] = 'messages/write';
		$this->view_data['id'] = $id;
		$this->content_view = 'messages/view';
	}
	public function viewMessage($id)
	{
		$privateMessage =  Privatemessage::find($id); 
		if($privateMessage->new_created == 0){
			$output = false; 
		}else{
			$_POST["new_created"] = 0;
			$this->db->where('id',$id);
			$output =$this->db->set($_POST);
			$output =$this->db->update('privatemessages');
		}	 
		echo json_encode($output);
		exit();
	}
}