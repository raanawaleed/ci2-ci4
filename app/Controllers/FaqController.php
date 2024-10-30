<?php 

namespace App\Controllers;

use App\Controllers\BaseController;

class FaqController extends BaseController
{
	function __construct()
	{
		parent::__construct();
		if($this->client){	
		}elseif($this->user){
		}else{
			redirect('login');
		}
	}
	
	function index()
	{
		$this->content_view = 'support/faq';
	}	
}