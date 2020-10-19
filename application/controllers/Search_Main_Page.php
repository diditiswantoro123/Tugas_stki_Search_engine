<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_Main_Page extends CI_Controller
{
	public function index()
	{
		$this->load->view('search_main_page');
	}
}