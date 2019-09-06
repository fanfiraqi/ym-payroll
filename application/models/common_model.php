<?php
			
class common_model extends MY_Model {
	 function __construct()
	{
		parent::__construct();		
		$this->gate_db=$this->load->database('gate', TRUE);
		 $this->donasi_db=$this->load->database('donasi', TRUE);
	} 
	

	public function comboCabang($empty=''){
		$query = $this->gate_db->query("select * from mst_cabang where kota not like '%REGIONAL%' order by id_cabang")->result();
		
		$combo = array();
		if (!empty($query)){
			$combo = $this->commonlib->buildcombo($query,'id_cabang','kota',$empty);
		}
		return $combo;
	}
	public function comboTipeDonasi($empty=''){
			$query = $this->donasi_db->query("select distinct type from jenis_donasi")->result();
			$combo = array();
			if (!empty($query)){
				$combo = $this->commonlib->buildcombo($query,'type','type',$empty);
			}
			return $combo;
		}
	public function comboDivisi(){
		$query = $this->gate_db->select()
			->order_by('nama_div')
			->get('mst_divisi')
			->result();
		$combo = array();
		if (!empty($query)){
			$combo = $this->commonlib->buildcombo($query,'id_div','nama_div');
		}
		return $combo;
	}
	public function comboJabatan(){
		$query = $this->gate_db->select()
			->order_by('bobot_jabatan desc')
			->get('mst_jabatan')
			->result();
		$combo = array();
		if (!empty($query)){
			$combo = $this->commonlib->buildcombo($query,'id_jab','nama_jab');
		}
		return $combo;
	}
	
	function changeStat($id=null, $nm_table, $nm_field, $sts){
		$this->db->trans_begin();
		$sts=($sts=="1"?"0":"1");
		$str="";
		try {
			
			$str="update $nm_table set isactive =".$sts." where $nm_field=".$id;		

			if ($this->db->query($str) ) {
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
	
	
	public function getCabang($id){
		$query = $this->gate_db->query("select * from mst_cabang where id_cabang=".$id)->row();		
		return $query;
	}
	public function getDivisi(){
		$query = $this->gate_db->select()
			->order_by('NAMA_DIV')
			->get('mst_divisi');
		return $query;
	}
	public function getDivisi_noDirektur(){
		$query = $this->gate_db->query("select * from mst_divisi where ID_DIV <> 1 order by NAMA_DIV");
		return $query;
	}
	public function getJabatan(){
		$query = $this->gate_db->select()
			->order_by('NAMA_JAB')
			->get('mst_jabatan');
		return $query;
	}
	public function comboReff($reff){
		$query = $this->db->select()
			->where(array('reff'=>$reff))
			->order_by('ID_REFF')
			->get('gen_reff')
			->result();
		$combo = array();
		if (!empty($query)){
			$combo = $this->commonlib->buildcombo($query,'ID_REFF','VALUE1');
		}
		return $combo;
	}
	
	public function comboReffPeg($reff){
		$query = $this->db->query("select * from gen_reff where reff='".$reff."' and value2 in('SMA', 'SARJANA') order by id")->result();
		
		$combo = array();
		if (!empty($query)){
			$combo = $this->commonlib->buildcombo($query,'VALUE2','VALUE1');
		}
		return $combo;
	}
	
	public function getDivChild($parent){
		$row = $this->gate_db->query("SELECT LFT,RGT from mst_divisi where id_div=".$parent."")->row();
		$result = $this->gate_db->query("select * from mst_divisi where lft>=".$row->LFT." and rgt<=".$row->RGT."")->result();
		$arr = array();
		foreach($result as $item){
			$arr[]=$item->ID_DIV;
		}
		return $arr;
	}
	
	public function nextcol($cur="A"){ // generate next column di XLS: A,B,C, dst.
		$int = ord($cur);
		$int++;
		$chr = chr($int);
		return $chr;
	}



}