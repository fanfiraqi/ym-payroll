<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class komponengaji extends MY_App {
	var $branch = array();
	function __construct()
	{
		parent::__construct();
		$this->load->model('komponengaji_model');
		$this->config->set_item('mymenu', 'mn1');
		$this->donasi_db=$this->load->database('donasi', TRUE);
		$this->auth->authorize();
	}
	
	public function index()
	{	$this->config->set_item('mySubMenu', 'mn11');
		$this->template->set('pagetitle','Daftar Komponen Gaji Tunjangan/Potongan Staff ');		
		$this->template->load('default','fkomponengaji/index',compact('str'));
	}
	public function jenisdonasi()
	{	
		$this->config->set_item('mysubmenu', 'menuSatuTujuh');
		$data['tipe'] =$this->common_model->comboTipeDonasi();
		$this->template->set('breadcrumbs','<li><a href="#">Pengaturan</a></li><li class="active"><span>Set Kelompok Jenis Donasi</span></li>');
		$this->template->set('pagetitle','Set Kelompok Jenis Donasi');		
		$this->template->load('default','fkomponengaji/vsetdonasi',$data);
	}
	public function parameter()
	{	$this->config->set_item('mySubMenu', 'mn12');
		$data['komponen'] =$this->komponengaji_model->comboKomponen('--Pilih--');
		$this->template->set('pagetitle','Daftar Komponen Gaji Tunjangan/Potongan Staff ');		
		$this->template->load('default','fkomponengaji/v_variabel',$data);
	}
	public function setnominal()
	{	$this->config->set_item('mySubMenu', 'mn13');
		$data['komponen'] =$this->komponengaji_model->comboKomponen('--Pilih--');
		$data['variabel'] =$this->komponengaji_model->comboVariabel('--Pilih--');
		$this->template->set('pagetitle','Daftar Komponen Gaji Tunjangan/Potongan Staff ');		
		$this->template->load('default','fkomponengaji/v_set_variabel',$data);
	}
	public function json_data(){
		//if ($this->input->is_ajax_request()){
		
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from mst_komp_gaji ";
			
						
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= " where nama like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
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
					'ID'=>$row->ID,
					'NAMA'=>$row->NAMA,
					'KETERANGAN'=>$row->KETERANGAN,
					'FUNGSI'=>$row->FLAG,
					'STAFF'=>($row->IS_STAFF=="on"?"Ya":"Tidak"),
					'ZISCO'=>($row->IS_ZISCO=="on"?"Ya":"Tidak"),
					'IS_THR'=>($row->IS_THR==1?"Ya":"Tidak"),
					'TIPE_SIMPAN'=>$row->TIPE_SIMPAN,
					'ISACTIVE'=>($row->ISACTIVE=="1"?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->ID."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"delThis(".$row->ID.",'".$row->NAMA."')\"><i class='fa fa-trash-o' title='Delete'></i></a>"

					//'ACTION'=>'	<a href="'.base_url('komponengaji/edit/'.$row->ID).'"><i class="fa fa-edit" title="Edit"></i></a>  | <a href="javascript:void()" onclick="ubahStatus('.$row->ID.', '.$row->ISACTIVE.')"><i class="fa fa-power-off" title="status"></i></a>'
				);
			}
			
			$output = array(
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		//}
	}	
	
	public function json_data_variabel(){
		//if ($this->input->is_ajax_request()){
			$komponen = $this->input->get('komponen');
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select mst_komp_var.*, (select nama from mst_komp_gaji where id=mst_komp_var.id_komp) nmkomp from mst_komp_var where isactive=1";
			
			if (!empty($komponen)){
				$str .= " AND id_komp = ".$komponen;
			}
				
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= " and nama like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
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
					'KOMPONEN'=>$row->nmkomp,					
					'NAMA_VAR'=>$row->nama_var,					
					'NOMINAL'=>$row->nominal,					
					'ISACTIVE'=>($row->isactive=="1"?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"delThis(".$row->id.",'".$row->nama_var."')\"><i class='fa fa-trash-o' title='Delete'></i></a>"

					//'ACTION'=>'	<a href="'.base_url('komponengaji/edit/'.$row->ID).'"><i class="fa fa-edit" title="Edit"></i></a>  | <a href="javascript:void()" onclick="ubahStatus('.$row->ID.', '.$row->ISACTIVE.')"><i class="fa fa-power-off" title="status"></i></a>'
				);
			}
			
			$output = array(
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		//}
	}	


	public function json_data_set_var(){
		//if ($this->input->is_ajax_request()){
			$komponen = $this->input->get('komponen');
			$variabel = $this->input->get('variabel');
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select mst_set_kom_var.*, (select nama from mst_komp_gaji where id=mst_set_kom_var.id_komp) nmkomp, (select nama from mst_komp_var where id=mst_set_kom_var.id_var) nmvar from mst_set_kom_var where isactive=1"; 
			
			if (!empty($komponen)){
				$str .= " AND mst_set_kom_var.id_komp=".$komponen;
			}
			if (!empty($variabel)){
				$str .= " AND  mst_set_kom_var.id_var=".$variabel;
			}		
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= " and  satuan like '%".mysql_real_escape_string( $_GET['sSearch'] )."%' or nominal like '%".mysql_real_escape_string( $_GET['sSearch'] )."%'  ";
				
			}
			
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
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
					'KOMPONEN'=>$row->nmkomp,					
					'VARIABEL'=>$row->nmvar,					
					'SATUAN'=>$row->satuan,					
					'NOMINAL'=>$row->nominal,					
					'ISACTIVE'=>($row->isactive=="1"?"Aktif":"Tidak Aktif"),
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> | <a href='javascript:void()' onclick=\"delThis(".$row->id.",'".$row->nmkomp." - ".$row->nmvar."')\"><i class='fa fa-trash-o' title='Delete'></i></a>"

					//'ACTION'=>'	<a href="'.base_url('komponengaji/edit/'.$row->ID).'"><i class="fa fa-edit" title="Edit"></i></a>  | <a href="javascript:void()" onclick="ubahStatus('.$row->ID.', '.$row->ISACTIVE.')"><i class="fa fa-power-off" title="status"></i></a>'
				);
			}
			
			$output = array(
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		//}
	}

	public function saveData(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'nama',
					'label' => 'NAMA',
					'rules' => 'trim|xss_clean|required'
				),
				array(
					'field' => 'keterangan',
					'label' => 'KETERANGAN',
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
						'NAMA'=>$this->input->post('nama'),
						'KETERANGAN'=>$this->input->post('keterangan'),
						'FLAG'=>$this->input->post('fungsi'),
						'IS_STAFF'=>$this->input->post('ck_staff'),
						'IS_ZISCO'=>$this->input->post('ck_zisco'),
						'TIPE_SIMPAN'=>$this->input->post('tipe_simpan'),
						'IS_THR'=>$this->input->post('is_thr'),
						'ISACTIVE'=>$this->input->post('status'),
						'CREATED_BY' =>$this->session->userdata('auth')->id,
						'CREATED_DATE' =>date('Y-m-d H:i:s')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_komp_gaji', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					$data = array(						
						'NAMA'=>$this->input->post('nama'),
						'KETERANGAN'=>$this->input->post('keterangan'),
						'FLAG'=>$this->input->post('fungsi'),
						'IS_STAFF'=>$this->input->post('ck_staff'),
						'IS_ZISCO'=>$this->input->post('ck_zisco'),
						'ISACTIVE'=>$this->input->post('status'),
						'UPDATED_BY' =>$this->session->userdata('auth')->id,
						'UPDATED_DATE' =>date('Y-m-d H:i:s')
					);
					if ($this->db->where('ID',$state)->update('mst_komp_gaji',$data)){
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
	
	public function saveData_var(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'variabel',
					'label' => 'NAMA_VARIABEL',
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
						'nama_var'=>$this->input->post('variabel'),
						'id_komp'=>$this->input->post('komponen'),
						'nominal'=>$this->input->post('nominal'),
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_komp_var', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_komp_var',$data)){
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
	
	public function saveData_set_var(){
		if($this->input->is_ajax_request()) {		
			$this->load->library('form_validation');
			$state=$this->input->post('state');
			$rules = array(				
				array(
					'field' => 'variabel',
					'label' => 'NAMA_VARIABEL',
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
						'id_komp'=>$this->input->post('komponen'),
						'id_var'=>$this->input->post('variabel'),
						'satuan'=>$this->input->post('satuan'),
						'nominal'=>$this->input->post('nominal'),						
						'isactive'=>$this->input->post('status')
					);
				if($state=="add"){ 
					if ($this->db->insert('mst_set_kom_var', $data)){
						$this->db->trans_commit();
					} else {
						throw new Exception("gagal simpan");
					}
				}else{
					
					if ($this->db->where('ID',$state)->update('mst_set_kom_var',$data)){
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
	public function fillComboVar($emptyval=true){
		$komponen = $this->input->post('komponen');
		//$id_cabang = 1;
		$query = $this->db->select()
			->where(array('id_komp'=>$komponen))
			->get('mst_komp_var')->result_array();
		//debug_last();
		$respon = new StdClass();
		$respon->status = 0;
		if (!empty($query)){
			if (!$emptyval){
				array_unshift($query,array('id'=>0,'nama'=>'-- Pilih --'));
			}
			$respon->status = 1;
			$respon->data = $query;
		}
		echo json_encode($respon);
	}
	public function delThis(){
		$id=$this->input->post('idx');
		$nmtable=$this->input->post('proses');
		$field=$this->input->post('field');
		$res = $this->common_model->delThis($id,$nmtable,$field);
		return $res;
	}

	public function editThis(){
		$id = $this->input->post('id');	//id as nik
		
		$str="select * from mst_komp_gaji where id='".$id."'";
		$query = $this->db->query($str)->row();		

		if(empty($query)){
			$respon['status'] = 'error';
			$respon['errormsg'] = 'Invalid Data';
		} else {			
			$respon['status'] = 'success';
			$respon['data'] = $query;
			
			
		}
		echo json_encode($respon);
	}
	public function editThis_var(){
		$id = $this->input->post('id');	//id as nik
		
		$str="select * from mst_komp_var where id='".$id."'";
		$query = $this->db->query($str)->row();		

		if(empty($query)){
			$respon['status'] = 'error';
			$respon['errormsg'] = 'Invalid Data';
		} else {			
			$respon['status'] = 'success';
			$respon['data'] = $query;
			
			
		}
		echo json_encode($respon);
	}
	public function editThis_set_var(){
		$id = $this->input->post('id');	//id as nik
		
		$str="select * from mst_set_kom_var where id='".$id."'";
		$query = $this->db->query($str)->row();		

		if(empty($query)){
			$respon['status'] = 'error';
			$respon['errormsg'] = 'Invalid Data';
		} else {			
			$respon['status'] = 'success';
			$respon['data'] = $query;
			
			
		}
		echo json_encode($respon);
	}
	public function view($id){
		$this->template->set('pagetitle','View Data Komponen Gaji/Tunjangan Staff ');
		$data['row'] = $this->komponengaji_model->getEdited($id);
		if (empty($data['row'])){
			flashMessage('Data Invalid','danger');
			redirect('komponengaji');
		}
		
		$this->template->load('default','fkomponengaji/view',$data);
	
	}

	
	public function ubahStatus(){
		$id=$this->input->post('idx');
		$sts=$this->input->post('status');
		$res = $this->komponengaji_model->ubahStatus($id, $sts);
		return $res;
	}

	public function json_data_donasi(){
			
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$level3=(!empty($_GET['lvl3'])?$_GET['lvl3']:'1-1100');
			$tipe=(!empty($_GET['tipe'])?$_GET['tipe']:"");

			$rscolumn=$this->donasi_db->query("select distinct payroll_column from jenis_donasi")->result();
			$combo = array();
				if (!empty($rscolumn)){
					$combo = $this->commonlib->buildcombo($rscolumn,'payroll_column','payroll_column','');
				}

			$str = "select * from jenis_donasi where  type='".$tipe."' and active=1";
			
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$str .= " ORDER BY id, ".$_GET['mDataProp_'.$_GET['iSortCol_0']]." ".$_GET['sSortDir_0'];
			}else{
				$str .=" ORDER BY  id";
			}
			
			$strfilter = $str;
			
			$iFilteredTotal = $this->donasi_db->query($str)->num_rows();
			if ($iFilteredTotal>1){	//if level 4 is lowest =>pendapatan zis
				if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
				{
					$strfilter .= " LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
				}
			}
			$iTotal = $iFilteredTotal;
			$query = $this->donasi_db->query($strfilter)->result();
			
			$aaData = array();
			foreach($query as $row){
				$aaData[] = array(
						'ID'=>$row->id,
						'TIPE'=>$row->type,
						'TITLE'=>$row->title,
						'PAYROLL_COLUMN'=>'<input type="hidden" name="txtid[]" id="txtid[]" value="'.$row->id.'">'.form_dropdown('payroll_column[]',$combo,$row->payroll_column,'id="payroll_column[]" class="form-control"'),
						'NOTES'=>form_textarea(array('name'=>'ket[]','id'=>'ket[]','class'=>'form-control', "rows"=>2, "cols"=>15,"value"=>$row->notes))
					);			
			}
			
			$output = array(
				"squery"=>$strfilter,
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
	}

	public function saveAkunDonasi(){
		$teks="awal<br>";
		if ($this->input->is_ajax_request()){	
			$respon = new StdClass();			
				$this->donasi_db->trans_begin();
				try {						
					$arrId=$this->input->post('txtid');
					$arrpayroll_column=$this->input->post('payroll_column');
					$arrket=$this->input->post('ket');
					for($i=0; $i<sizeof($arrId); $i++){
						$strUpdate="update jenis_donasi set payroll_column='".$arrpayroll_column[$i]."', notes='".$arrket[$i]."'  where id='".$arrId[$i]."'";
						$this->donasi_db->query($strUpdate);
						$teks.=$strUpdate."<br>";
						
					}
					$this->donasi_db->trans_commit();
					$respon->status = 'success';			
					$respon->data = $arrId;			
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->donasi_db->trans_rollback();
				}
				//$respon->status = 'success';
			
			$respon->teks=$teks;	
			echo json_encode($respon);
			//exit;
		
		} 
	}
}
