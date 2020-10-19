<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
date_default_timezone_set('Asia/Jakarta');

class Search extends CI_Controller
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
    	$search_query = htmlentities($this->input->get('q'), ENT_QUOTES, 'UTF-8');

    	if($search_query != "")
    	{
    		$baseUrl = base_url() . 'documents/';

			$start = microtime(true);

			$querynya = "SELECT DISTINCT token.dokumen_id, token.token, token.tokenstem, dokumen.nama_file, dokumen.file_format, dokumen.file_url, dokumen.timestamp, dokumen.content FROM dokumen INNER JOIN token ON BINARY dokumen.dokumen_id = token.dokumen_id WHERE MATCH (token.token, token.tokenstem) AGAINST (? IN BOOLEAN MODE) ORDER BY dokumen.timestamp DESC";
			$doQuerynya = $this->db->query($querynya, array($search_query));

			if($doQuerynya)
			{
				$resultQuery = $doQuerynya->result_array();

				$end = microtime(true);
				$duration = $end - $start;
				$duration = number_format((float)$duration, 2, '.', '');

				//var_dump($resultQuery);

				$output = array();

				foreach($resultQuery as $rowResultSearch)
				{
					$postDescFind = stripos($rowResultSearch['content'], rawurldecode($search_query));

					$limitPostDesc = $rowResultSearch['content'];
					$limitPostDesc = substr($limitPostDesc, $postDescFind, 300) . " ......";
					$dokumen_id_nya = $rowResultSearch['dokumen_id'];
					$getBaseUrl = $baseUrl . $rowResultSearch['file_url'];

					$output2 = [
									'dokumen_id'=>$dokumen_id_nya,
									'nama_file'=>$rowResultSearch['nama_file'],
									'file_format'=>$rowResultSearch['file_format'],
									'timestamp'=>(int)$rowResultSearch['timestamp'],
									'desc'=>htmlentities($limitPostDesc, ENT_QUOTES, 'UTF-8'),
									'file_url'=>$getBaseUrl
									];

					array_push($output, $output2);
				}

				$fixArray = array();

				for($ulang = 0; $ulang < sizeof($output); $ulang++)
				{
					if(!in_array($output[$ulang], $fixArray))
					{
						$fixArray2 = [
											'dokumen_id'=>$output[$ulang]['dokumen_id'],
											'nama_file'=>$output[$ulang]['nama_file'],
											'file_format'=>$output[$ulang]['file_format'],
											'timestamp'=>(int)$output[$ulang]['timestamp'],
											'desc'=>$output[$ulang]['desc'],
											'file_url'=>$getBaseUrl
											];

						array_push($fixArray, $fixArray2);
					}
				}

				//var_dump($fixArray);

				if(count($fixArray) <= 0)
				{
					$this->session->set_flashdata('query_duration', $duration);
					$this->session->set_flashdata('current_query', $search_query);
					$this->session->set_flashdata('search_notif', 'Tidak ada dokumen pdf ditemukan!');
    				$this->load->view('search');
				}

				else
				{
					$this->session->set_flashdata('query_duration', $duration);
					$this->session->set_flashdata('current_query', $search_query);
					$this->session->set_flashdata('search_notif', 'sukses');
					$this->session->set_flashdata('search_data_array', $fixArray);
					$this->load->view('search');
				}
			}

			else
			{
				$this->session->set_flashdata('query_duration', $duration);
				$this->session->set_flashdata('current_query', $search_query);
				$this->session->set_flashdata('search_notif', 'Terjadi kesalahan saat mengambil data!');
    			//echo $this->session->flashdata('search_notif');
    			$this->load->view('search');
			}
    	}

    	if($search_query == "")
    	{
    		$this->session->set_flashdata('query_duration', $duration);
    		$this->session->set_flashdata('current_query', $search_query);
    		$this->session->set_flashdata('search_notif', 'Kata kunci kosong!');
    		//echo $this->session->flashdata('search_notif');
    		$this->load->view('search');
    	}
    }
}