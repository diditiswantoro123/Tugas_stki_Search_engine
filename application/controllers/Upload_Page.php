<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
date_default_timezone_set('Asia/Jakarta');

class Upload_Page extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
    }

    public function index()
    {
        $this->load->view('upload_page');
    }
}
?>