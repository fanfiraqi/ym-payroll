<?php
			
class gaji_model extends MY_Model {
	var $table = 'set_gaji_staff';
	var $primaryID = 'ID';
	var $donasi_db;

	function __construct()
	{
		parent::__construct();		
		$this->gate_db=$this->load->database('gate', TRUE);
		$this->donasi_db=$this->load->database('donasi', TRUE);
	} 
	
}