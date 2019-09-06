<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class master_payroll extends MY_App {
	var $branch = array();
	function __construct()
	{
		parent::__construct();
		$this->load->model('master_payroll_model');
		$this->config->set_item('mymenu', 'mn1');
		$this->gate_db=$this->load->database('gate', TRUE);
		$this->auth->authorize();
	}
	
	public function grade()
	{	$this->config->set_item('mySubMenu', 'mn13');
		$data['grade']=array('a'=>'a','b'=>'b','c'=>'c','d'=>'d');
		$data['cabang']=$this->common_model->comboCabang('--Pilih--');
		$this->template->set('pagetitle','Data Pengelompokan Grade Cabang');		
		$this->template->load('default','fmaster_payroll/vgrade',$data);
	}
	
	public function gapok()
	{	$this->config->set_item('mySubMenu', 'mn14');
		$data['grade']=array('a'=>'a', 'b'=>'b', 'c'=>'c', 'd'=>'d');
		$data['cabang']=$this->common_model->comboCabang('--Pilih--');
		$data['jabatan']=$this->common_model->comboJabatan('--Pilih--');
		$this->template->set('pagetitle','Daftar Gaji Pokok Staff Dalam ');		
		$this->template->load('default','fmaster_payroll/vgapok',$data);
	}
	public function acuan_makan()
	{	$this->config->set_item('mySubMenu', 'mn15');
		$data['cabang']=$this->common_model->comboCabang('--Pilih--');
		$this->template->set('pagetitle','Daftar Acuan Uang Makan Staff Dalam ');		
		$this->template->load('default','fmaster_payroll/vacuan_makan',$data);
	}
	public function acuan_transport()
	{	$this->config->set_item('mySubMenu', 'mn16');
		$data['cabang']=$this->common_model->comboCabang('--Pilih--');
		$this->template->set('pagetitle','Daftar Acuan Uang Transport ZISCO ');		
		$this->template->load('default','fmaster_payroll/vacuan_transport',$data);
	}
	public function tunj_jabatan(){
		$this->config->set_item('mySubMenu', 'mn17');	
		$this->template->set('pagetitle','Daftar Komponen Tunjangan Jabatan Staff Dalam');		
		$this->template->load('default','fmaster_payroll/vtunj_jabatan',compact('str'));

	}
	public function tunj_jabatan_()
	{	$this->config->set_item('mySubMenu', 'mn17');	
		$data['grade']=array('a'=>'a','b'=>'b','c'=>'c','d'=>'d');
		$level_jabatan=$this->master_payroll_model->getLevelJabatan('--Pilih--');
		$arrKacab=array();
		foreach ($level_jabatan as $row){
			$arrKacab[]=$row;
		}
		$temp=array("Kepala Cabang - Cab Pembantu"=>"Kepala Cabang - Cab Pembantu",
					"Kepala Cabang - Cab Pratama"=>"Kepala Cabang - Cab Pratama",
					"Kepala Cabang - Cab Madya"=>"Kepala Cabang - Cab Madya",
					"Kepala Cabang - Cab Utama"=>"Kepala Cabang - Cab Utama",
					"Kepala Cabang - Cab Paripurna"=>"Kepala Cabang - Cab Paripurna");
		array_push($arrKacab, $temp	);
		$data['level_jabatan']=$arrKacab;
		$this->template->set('pagetitle','Daftar Komponen Tunjangan Jabatan Staff Dalam');		
		$this->template->load('default','fmaster_payroll/vtunj_jabatan',$data);
	}
	public function tunj_masakerja()
	{	$this->config->set_item('mySubMenu', 'mn18');		
		$this->template->set('pagetitle','Daftar Tunjangan Masa Kerja Staff Dalam');		
		$this->template->load('default','fmaster_payroll/vtunj_masakerja',compact('str'));
	}
	public function tunj_haritua()
	{	$this->config->set_item('mySubMenu', 'mn19');		
		$data['pendidikan'] = $this->common_model->comboReffPeg('PENDIDIKAN');
		$data['kelompok_jab']=array('S'=>'S','M'=>'M','GM'=>'GM');
		$this->template->set('pagetitle','Daftar Hari Tua Staff Dalam');		
		$this->template->load('default','fmaster_payroll/vtunj_haritua',$data);
	}
	public function tunj_pengambilan()
	{	$this->config->set_item('mySubMenu', 'mn120');		
		$this->template->set('pagetitle','Daftar Tunjangan Pengambilan Zisco');		
		$this->template->load('default','fmaster_payroll/vtunj_pengambilan',compact('str'));
	}
	public function tunj_prestasi()
	{	$this->config->set_item('mySubMenu', 'mn121');		
		$this->template->set('pagetitle','Daftar Tunjangan Prestasi Zisco');		
		$this->template->load('default','fmaster_payroll/vtunj_prestasi',compact('str'));
	}

	public function changeStat(){
		$id=$this->input->post('idx');
		$nmtable=$this->input->post('nmtable');
		$field=$this->input->post('field');
		$status=$this->input->post('status');
		$res = $this->common_model->changeStat($id,$nmtable,$field, $status);
		return $res;
	}

	public function editThis(){
		$id = $this->input->post('id');	
		$nmtable=$this->input->post('tabel');
		$field=$this->input->post('field');
		$str="select * from ".$nmtable."  where ".$field."='".$id."'";
		$query = $this->db->query($str)->row();		

		if(empty($query)){
			$respon['status'] = 'error';
			$respon['errormsg'] = 'Invalid Data';
		} else {
			if ($nmtable=="mst_acuan_makan" || $nmtable=="mst_acuan_transport" || $nmtable=="mst_grade_cabang"){
				//label master
				$rsname=$this->gate_db->query("SELECT kota nama_cabang FROM mst_cabang WHERE id_cabang=".$query->id_cabang)->row();
				$respon['master'] = $rsname;
			}
			$respon['status'] = 'success';
			$respon['data'] = $query;
			
			
		}
		echo json_encode($respon);
	}

	public function json_data_grade(){
		
			
			
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_grade_cabang  where  isactive=1  ";
			
			if (!empty($_GET['cabang'])){
				$str .= " AND ID_CABANG = ".$_GET['cabang'];			
			}
			
			if ( $_GET['sSearch'] != "" )
			{
			    $rsArrCab=$this->gate_db->query("select id_cabang from mst_cabang where kota like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'")->result();
        		$tags = array();
        			foreach ($rsArrCab as $row) {
        				$tags[] =htmlspecialchars( $row->id_cabang, ENT_NOQUOTES, 'UTF-8' );
        			}
        		$arCab= implode(',', $tags);
				$str.= "  and  (grade like '%".mysql_real_escape_string( $_GET['sSearch'] )."%' or id_cabang in  ( ".$arCab." ) ) ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .= " ORDER BY grade";
			}
			
			
			$strfilter = $str;
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
			}
			
			$iFilteredTotal = $this->db->query($str)->num_rows();
			$iTotal = $iFilteredTotal;
			$query = $this->db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				$sql=$this->gate_db->query("SELECT kota nama_cabang FROM mst_cabang WHERE id_cabang=".$row->id_cabang);
				if ($sql->num_rows()>0){
				    $rsname=$sql->row();
				}
				$aaData[] = array(
					'ID'=>$row->id,
					'GRADE'=>$row->grade,
					'CABANG'=>$rsname->nama_cabang,
					'KETERANGAN'=>$row->keterangan,
					'ISACTIVE'=>($row->isactive==1?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"changeStat(".$row->id.",'grade : ".$row->grade." - ".$rsname->nama_cabang."', ".$row->isactive.")\"><i class='fa fa-power-off' title='Ubah Status'></i></a>"
				);
			}
			
			$output = array(
			    "str" => $strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		
	}

	public function json_data_uang_makan(){
	
			$cabang = $this->input->get('cabang');
			
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_acuan_makan  where  isactive=1  ";
			
			if (!empty($_GET['cabang'])){
				$str .= " AND ID_CABANG = ".$_GET['cabang'];			
			}	
			if ( $_GET['sSearch'] != "" )
			{
			     $rsArrCab=$this->gate_db->query("select id_cabang from mst_cabang where kota like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'")->result();
        		$tags = array();
        			foreach ($rsArrCab as $row) {
        				$tags[] =htmlspecialchars( $row->id_cabang, ENT_NOQUOTES, 'UTF-8' );
        			}
        		$arCab= implode(',', $tags);
				
				$str.= "  and ( per_hari like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'   or id_cabang in  ( ".$arCab." )) ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .= " ORDER BY id_cabang";
			}
			
			
			$strfilter = $str;
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
			}
			
			$iFilteredTotal = $this->db->query($str)->num_rows();
			$iTotal = $iFilteredTotal;
			$query = $this->db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				$rsname=$this->gate_db->query("SELECT kota nama_cabang FROM mst_cabang WHERE id_cabang=".$row->id_cabang)->row();
				$aaData[] = array(
					'ID'=>$row->id,
					'CABANG'=>$rsname->nama_cabang,
					'PER_HARI'=>$row->per_hari,
					'PER_BULAN'=>$row->per_bulan,
					'ISACTIVE'=>($row->isactive==1?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"changeStat(".$row->id.",'".$rsname->nama_cabang."', ".$row->isactive.")\"><i class='fa fa-power-off' title='Ubah Status'></i></a>"
				);
			}
			
			$output = array(
			    "str" => $strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		
	}
	
	public function json_data_uang_transport(){
	
			$cabang = $this->input->get('cabang');
			
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_acuan_transport  where  isactive=1  ";
			
			if (!empty($_GET['cabang'])){
				$str .= " AND ID_CABANG = ".$_GET['cabang'];			
			}	
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= "  and  per_hari like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .= " ORDER BY id_cabang";
			}
			
			
			$strfilter = $str;
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
			}
			
			$iFilteredTotal = $this->db->query($str)->num_rows();
			$iTotal = $iFilteredTotal;
			$query = $this->db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				$rsname=$this->gate_db->query("SELECT kota nama_cabang FROM mst_cabang WHERE id_cabang=".$row->id_cabang)->row();
				$aaData[] = array(
					'ID'=>$row->id,
					'CABANG'=>$rsname->nama_cabang,
					'TRAINEE'=>$row->trainee,
					'PENUH'=>$row->penuh,
					'ISACTIVE'=>($row->isactive==1?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"changeStat(".$row->id.",'".$rsname->nama_cabang."', ".$row->isactive.")\"><i class='fa fa-power-off' title='Ubah Status'></i></a>"
				);
			}
			
			$output = array(
			    "str" => $strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		
	}
	public function json_data_tunj_jab_cab(){
		
			
			
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_tunj_jabatan_cabang  where  isactive=1  ";			
			
			
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= "  and  jabatan like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .= " ORDER BY jabatan";
			}
			
			
			$strfilter = $str;
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
			}
			
			$iFilteredTotal = $this->db->query($str)->num_rows();
			$iTotal = $iFilteredTotal;
			$query = $this->db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				
				$aaData[] = array(
					'ID'=>$row->id,
					'JABATAN'=>$row->jabatan,
					'NOMINAL'=>$row->nominal,
					'ISACTIVE'=>($row->isactive==1?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis_cab(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"changeStat_cab(".$row->id.",'".$row->jabatan."', ".$row->isactive.")\"><i class='fa fa-power-off' title='Ubah Status'></i></a>"
				);
			}
			
			$output = array(
			    "str" => $strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		
	}
	public function json_data_tunj_jab_pusat(){		
			
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_tunj_jabatan_pusat  where  isactive=1  ";			
			
			
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= "  and  level_jabatan like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .= " ORDER BY level_jabatan";
			}
			
			
			$strfilter = $str;
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
			}
			
			$iFilteredTotal = $this->db->query($str)->num_rows();
			$iTotal = $iFilteredTotal;
			$query = $this->db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				
				$aaData[] = array(
					'ID'=>$row->id,
					'LEVEL_JABATAN'=>$row->level_jabatan,
					'NOMINAL_DIREKTORAT'=>$row->nominal_direktorat,
					'NOMINAL_PUSAT'=>$row->nominal_pusat,
					'KETERANGAN'=>$row->keterangan,
					'ISACTIVE'=>($row->isactive==1?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"changeStat(".$row->id.",'".$row->level_jabatan."', ".$row->isactive.")\"><i class='fa fa-power-off' title='Ubah Status'></i></a>"
				);
			}
			
			$output = array(
			    "str" => $strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		
	}
	
	public function json_data_tht(){
			
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_tht  where  isactive=1  ";			
			
			
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= "  and  kelompok_jab like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  OR pendidikan like '%".mysql_real_escape_string( $_GET['sSearch'] )."%' ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .= " ORDER BY kelompok_jab";
			}
			
			
			$strfilter = $str;
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
			}
			
			$iFilteredTotal = $this->db->query($str)->num_rows();
			$iTotal = $iFilteredTotal;
			$query = $this->db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				$aaData[] = array(
					'ID'=>$row->id,
					'KELOMPOK_JAB'=>$row->kelompok_jab,
					'PENDIDIKAN'=>$row->pendidikan,
					'NOMINAL'=>$row->nominal,
					'ISACTIVE'=>($row->isactive==1?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"changeStat(".$row->id.",' ".$row->kelompok_jab." - ".$row->pendidikan."', ".$row->isactive.")\"><i class='fa fa-power-off' title='Ubah Status'></i></a>"
				);
			}
			
			$output = array(
			    "str" => $strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		
	}
	
	public function json_data_masakerja(){
		
			
			
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_tunj_masa_kerja  where  isactive=1  ";
			
			
			
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= "  and  tahun_ke like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .= " ORDER BY tahun_ke";
			}
			
			
			$strfilter = $str;
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
			}
			
			$iFilteredTotal = $this->db->query($str)->num_rows();
			$iTotal = $iFilteredTotal;
			$query = $this->db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				$aaData[] = array(
					'ID'=>$row->id,
					'TAHUN_KE'=>$row->tahun_ke,
					'NOMINAL'=>$row->nominal,
					'ISACTIVE'=>($row->isactive==1?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"changeStat(".$row->id.",'".$row->tahun_ke." - ".$row->nominal."', ".$row->isactive.")\"><i class='fa fa-power-off' title='Ubah Status'></i></a>"
				);
			}
			
			$output = array(
			    "str" => $strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		
	}
	public function json_data_pengambilan(){
		
			
			
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_tunj_pengambilan  where  isactive=1  ";
			
			
			
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= "  and  batas_bawah like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .= " ORDER BY id";
			}
			
			
			$strfilter = $str;
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
			}
			
			$iFilteredTotal = $this->db->query($str)->num_rows();
			$iTotal = $iFilteredTotal;
			$query = $this->db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				$aaData[] = array(
					'ID'=>$row->id,
					'BATAS_BAWAH'=>$row->batas_bawah,
					'BATAS_ATAS'=>$row->batas_atas,					
					'NOMINAL'=>$row->nominal,					
					'ISACTIVE'=>($row->isactive==1?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"changeStat(".$row->id.",".$row->batas_bawah." - ".$row->batas_atas."', ".$row->isactive.")\"><i class='fa fa-power-off' title='Ubah Status'></i></a>"
				);
			}
			
			$output = array(
			    "str" => $strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		
	}
	public function json_data_prestasi(){
		
			
			
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_tunj_prestasi  where  isactive=1  ";
			
			
			
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= "  and  prestasi like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .= " ORDER BY id";
			}
			
			
			$strfilter = $str;
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
			}
			
			$iFilteredTotal = $this->db->query($str)->num_rows();
			$iTotal = $iFilteredTotal;
			$query = $this->db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				$aaData[] = array(
					'ID'=>$row->id,
					'BATAS_BAWAH'=>$row->batas_bawah,
					'BATAS_ATAS'=>$row->batas_atas,
					'NOMINAL'=>$row->nominal,					
					'ISACTIVE'=>($row->isactive==1?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"changeStat(".$row->id.",".$row->batas_bawah.", ".$row->isactive.")\"><i class='fa fa-power-off' title='Ubah Status'></i></a>"
				);
			}
			
			$output = array(
			    "str" => $strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		
	}
	
	public function json_data_gapok(){
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_gapok  where  isactive=1  ";
			
			if (!empty($_GET['grade_cabang'])){
				$str .= " AND grade_cabang = '".$_GET['grade_cabang']."'";			
			}	
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= "  and  grade_cabang like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  or kelompok_lama like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .= " ORDER BY grade_cabang, kelompok_lama";
			}
			
			
			$strfilter = $str;
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
			}
			
			$iFilteredTotal = $this->db->query($str)->num_rows();
			$iTotal = $iFilteredTotal;
			$query = $this->db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				$rsname=$this->gate_db->query("SELECT nama_jab FROM mst_jabatan WHERE id_jab=".$row->id_jabatan)->row();
				$aaData[] = array(
					'ID'=>$row->id,
					'GRADE_CABANG'=>$row->grade_cabang,
					'ID_JABATAN'=>$rsname->nama_jab,
					'LABEL_LAMA'=>$row->label_lama,
					'LAMA_KERJA_AWAL'=>$row->lama_kerja_awal,
					'LAMA_KERJA_AKHIR'=>$row->lama_kerja_akhir,
					'NOMINAL'=>$row->nominal,
					'ISACTIVE'=>($row->isactive==1?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"changeStat(".$row->id.",'".$row->grade_cabang.' - '.$rsname->nama_jab."', ".$row->isactive.")\"><i class='fa fa-power-off' title='Ubah Status'></i></a>"
				);
			}
			
			$output = array(
			    "str" => $strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		
	}
	public function saveData_grade(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'grade',
					'label' => 'GRADE',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'cabang',
					'label' => 'CABANG',
					'rules' => 'trim|xss_clean|required'
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				
				$this->db->trans_begin();
				try {						
					$data = array(	
						'grade'=>$this->input->post('grade'),
						'id_cabang'=>$this->input->post('cabang'),
						'keterangan'=>$this->input->post('keterangan'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_grade_cabang', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_grade_cabang',$data)){
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}
				}
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
		
		} 
		
	}
	
	public function saveData_uangMakan(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'per_hari',
					'label' => 'PER_HARI',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'per_bulan',
					'label' => 'PER_BULAN',
					'rules' => 'trim|xss_clean|required'
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				
				$this->db->trans_begin();
				try {						
					$data = array(	
						'per_hari'=>$this->input->post('per_hari'),
						'id_cabang'=>$this->input->post('cabang'),
						'per_bulan'=>$this->input->post('per_bulan'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_acuan_makan', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_acuan_makan',$data)){
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}
				}
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
		
		} 		
	}

	public function saveData_uangTransport(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'trainee',
					'label' => 'TRAINEE',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'penuh',
					'label' => 'ZISCO_NON_TRAINEE',
					'rules' => 'trim|xss_clean|required'
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				
				$this->db->trans_begin();
				try {						
					$data = array(	
						'per_hari'=>$this->input->post('per_hari'),
						'id_cabang'=>$this->input->post('cabang'),
						'trainee'=>$this->input->post('trainee'),
						'penuh'=>$this->input->post('penuh'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_acuan_transport', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_acuan_transport',$data)){
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}
				}
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
		
		} 	
	}

	public function saveData_tunj_jab_pusat(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'level_jabatan',
					'label' => 'LEVEL_JABATAN',
					'rules' => 'trim|xss_clean|required'
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				
				$this->db->trans_begin();
				try {						
					$data = array(	
						'level_jabatan'=>$this->input->post('level_jabatan'),
						'nominal_direktorat'=>$this->input->post('nominal_direktorat'),
						'nominal_pusat'=>$this->input->post('nominal_pusat'),
						'keterangan'=>$this->input->post('keterangan'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_tunj_jabatan_pusat', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_tunj_jabatan_pusat',$data)){
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}
				}
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
		
		} 
		
	}

	public function saveData_tunj_jab_cabang(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state_cab');
			$rules = array(				
				array(
					'field' => 'jabatan',
					'label' => 'JABATAN',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'nominal',
					'label' => 'NOMINAL',
					'rules' => 'trim|xss_clean|required'
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				
				$this->db->trans_begin();
				try {						
					$data = array(	
						'jabatan'=>$this->input->post('jabatan'),
						'nominal'=>$this->input->post('nominal'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_tunj_jabatan_cabang', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_tunj_jabatan_cabang',$data)){
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}
				}
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
		
		} 
		
	}

public function saveData_tht(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'nominal',
					'label' => 'NOMINAL',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'kelompok_jab',
					'label' => 'KELOMPOK_JAB',
					'rules' => 'trim|xss_clean|required'
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				
				$this->db->trans_begin();
				try {						
					$data = array(	
						'kelompok_jab'=>$this->input->post('kelompok_jab'),
						'pendidikan'=>$this->input->post('pendidikan'),
						'nominal'=>$this->input->post('nominal'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_tht', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_tht',$data)){
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}
				}
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
		
		} 		
	}

	public function saveData_masakerja(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'tahun_ke',
					'label' => 'TAHUN_KE',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'nominal',
					'label' => 'NOMINAL',
					'rules' => 'trim|xss_clean|required'
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				
				$this->db->trans_begin();
				try {						
					$data = array(	
						'tahun_ke'=>$this->input->post('tahun_ke'),
						'nominal'=>$this->input->post('nominal'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_tunj_masa_kerja', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_tunj_masa_kerja',$data)){
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}
				}
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
		
		} 		
	}

	public function saveData_pengambilan(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'batas_bawah',
					'label' => 'BATAS_BAWAH',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'batas_atas',
					'label' => 'BATAS_ATAS',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'nominal',
					'label' => 'NOMINAL',
					'rules' => 'trim|xss_clean|required'
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				
				$this->db->trans_begin();
				try {						
					$data = array(	
						'batas_bawah'=>$this->input->post('batas_bawah'),
						'batas_atas'=>$this->input->post('batas_atas'),
						'nominal'=>$this->input->post('nominal'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_tunj_pengambilan', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_tunj_pengambilan',$data)){
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}
				}
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
		
		} 
		
	}

	public function saveData_prestasi(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'batas_bawah',
					'label' => 'BATAS_BAWAH',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'batas_atas',
					'label' => 'BATAS_ATAS',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'nominal',
					'label' => 'NOMINAL',
					'rules' => 'trim|xss_clean|required'
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				
				$this->db->trans_begin();
				try {						
					$data = array(	
						'batas_bawah'=>$this->input->post('batas_bawah'),
						'batas_atas'=>$this->input->post('batas_atas'),
						'nominal'=>$this->input->post('nominal'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_tunj_prestasi', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_tunj_prestasi',$data)){
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}
				}
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
		
		} 		
	}

	public function saveData_gapok(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'nominal',
					'label' => 'NOMINAL',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'lama_kerja_awal',
					'label' => 'LAMA_KERJA_AWAL',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'lama_kerja_akhir',
					'label' => 'LAMA_KERJA_AKHIR',
					'rules' => 'trim|xss_clean|required'
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				
				$this->db->trans_begin();
				try {						
					$data = array(	
						'grade_cabang'=>$this->input->post('grade_cabang'),
						'id_jabatan'=>$this->input->post('id_jabatan'),
						'kelompok_lama'=>$this->input->post('kelompok_lama'),
						'label_lama'=>$this->input->post('label_lama'),
						'lama_kerja_awal'=>$this->input->post('lama_kerja_awal'),
						'lama_kerja_akhir'=>$this->input->post('lama_kerja_akhir'),
						'nominal'=>$this->input->post('nominal'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_gapok', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_gapok',$data)){
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}
				}
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
		
		} 
		
	}


	public function changeCabang(){
		$grade = $this->input->post('grade');
		//$id_cabang = 1;
		$query = $this->db->query("select distinct id_cabang from mst_grade_cabang where isactive=1 ")->result();
		$tags = array();
			foreach ($query as $row) {
				$tags[] =htmlspecialchars( $row->id_cabang, ENT_NOQUOTES, 'UTF-8' );
			}
		$arid_cabang= implode(',', $tags);
		$queryCab = $this->gate_db->query("select distinct id_cabang, kota from mst_cabang where is_active=1 and id_cabang not in(".$arid_cabang.")")->result();

		$respon = new StdClass();
		$respon->status = 0;
		if (!empty($queryCab)){
			$respon->status = 1;
			$respon->data = $queryCab;
		}
		echo json_encode($respon);
	}
}
