<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Home extends Controller {
	
	public function index()
	{
		$this->load->library('view');
		$this->view->load('example.php');
	}

}