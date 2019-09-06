<?php
			
class komponengaji_model extends MY_Model {
	var $table = 'mst_komponengaji';
	var $primaryID = 'ID';

	
	function getEdited($id=null){
						
		$query = $this->db->select()
			->from("mst_komp_gaji")
			->where('ID',$id)		
			->get()->row();
            //->result_array();
	
	return $query;
	}
	public function comboKomponen($empty=''){
		$query = $this->db->select()
			->order_by('nama')
			->where('isactive', 1)
			->where('tipe_simpan', 'Variabel')
			->get('mst_komp_gaji')
			->result();
		$combo = array();
		if (!empty($query)){
			$combo = $this->commonlib->buildcombo($query,'ID','NAMA',$empty);
		}
		return $combo;
	}
	public function comboVariabel($empty=''){
		$query = $this->db->select()
			->order_by('nama')
			->where('isactive', 1)
			->get('mst_komp_var')
			->result();
		$combo = array();
		if (!empty($query)){
			$combo = $this->commonlib->buildcombo($query,'id','nama',$empty);
		}
		return $combo;
	}
	function ubahStatus($id=null, $sts=null){
		$this->db->trans_begin();
		$sts=($sts=="1"?"0":"1");
		try {
			if ($this->db->where('ID',$id)->update('mst_komp_gaji', array("ISACTIVE"=>$sts))){
				$this->db->trans_commit();
				$respon->status = 'success';
			} else {
				throw new Exception("gagal simpan");
			}
		} catch (Exception $e) {
			$respon->status = 'error';
			$respon->errormsg = $e->getMessage();
				$this->db->trans_rollback();
		}
	
	return $respon;
	}
}
