<?php
			
class emp_model extends MY_Model {
	var $table = 'pegawai';
	var $primaryID = 'ID';
	
	
	function emp_data($id=null){
		$query = $this->db->select()
			->from('v_pegawai');
		
		if ($id==null){
			$query = $query->get()->result();
		} else {
			$query = $query->where('ID',$id)
				->get()
				->row();
		}
		return $query;
	}
	function view_hubkel($nik=null){
		$str = "SELECT a.*,didik.VALUE1 PENDIDIKAN, kel.VALUE1, kel.VALUE2, kel.VALUE3,sex.VALUE1 JNSKEL
				FROM adm_hubkel a
				left join gen_reff didik on didik.ID_REFF=a.ID_PENDIDIKAN
				left join gen_reff kel on kel.ID_REFF=a.ID_HUBKEL
				left join gen_reff sex on sex.ID_REFF=a.SEX
				WHERE didik.reff='PENDIDIKAN'
				AND kel.reff='KELUARGA'
				AND sex.reff='SEX'
				AND a.NIK='".$nik."' ORDER BY ID_HUBKEL, ANAK_KE ASC";
		return $this->db->query($str)->result();
		
	}
	function view_emp_byNik($nik=null){
		$query = $this->db->select()
			->from('v_pegawai');
		
		if ($nik==null){
			$query = $query->get()->result();
		} else {
			$query = $query->where('NIK',$nik)
				->get()
				->row();
		}
		return $query;
	}
	function getNIK($nik){
		$query = $this->db->select()
			->from('v_pegawai')
			->where('NIK',$nik)
			->get()
			->row();
		
		return $query;
	}
	
}