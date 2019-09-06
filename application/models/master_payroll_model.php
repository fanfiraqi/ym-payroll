<?php
			
class master_payroll_model extends MY_Model {
	var $table = 'pelatihan';
	var $primaryID = 'ID';
	 function __construct()
	{
		parent::__construct();		
		$this->gate_db=$this->load->database('gate', TRUE);
	}
	
	public function getLevelJabatan($empty=''){
		$query = $this->gate_db->query("select distinct klaster  from mst_jabatan order by bobot_jabatan ")->result();
		
		$combo = array();
		if (!empty($query)){
			$combo = $this->commonlib->buildcombo($query,'klaster','klaster',$empty);
		}
		return $combo;
	}

	
	/*public function jabatan_gapok($empty=''){
		$qawal = $this->db->query("select id_jabatan from mst_gapok")->result();
		$query = $this->gate_db->query("select * from mst_cabang where kota not like '%REGIONAL%' order by kota")->result();
		
		$combo = array();
		if (!empty($query)){
			$combo = $this->commonlib->buildcombo($query,'id_cabang','kota',$empty);
		}
		return $combo;
	}*/
}
