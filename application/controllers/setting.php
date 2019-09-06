<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class setting extends MY_App {

	function __construct()
	{
		parent::__construct();
		$this->config->set_item('mymenu', 'mn1');
		$this->auth->authorize();
	}
	
	
	
	public function parameter(){
		$this->template->set('pagetitle','Setting Parameter');
		$this->config->set_item('mySubMenu', 'mn122');
		$this->template->load('default','setting/parameter');
	}
	
	public function savingParams(){
			$id = $this->input->post('id');
						
			$this->db->trans_begin();
			$respon = new StdClass();
		try {
				$data= array(
						'value1' =>$this->input->post('txt2'),
						'value2' =>$this->input->post('txt3')
				);				
					$this->db->where('id',1)->update('params',$data);				
					$this->db->trans_commit();
					$respon->status = 'success';
		}catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
		}
				
		$this->template->set('breadcrumbs','<h1>Pusat Data<small> <i class="ace-icon fa fa-angle-double-right"></i>Data Rekening Payroll</small></h1>');
		$this->template->set('pagetitle','Data Rekening Payroll');
		$this->template->load('default','setting/parameter');

	}
	
	public function getParams(){
		$id = $this->input->post('id');
		$str="select * from params where id=1";
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
	public function json(){
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "SELECT * FROM params";
			
						
			if ( $_GET['sSearch'] != "" )
			{
				
				$str.= " WHERE value1 LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' ";
				
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
					'id'=>$row->id,
					'value1'=>$row->value1,
					'value2'=>$row->value2,
					'ACTION'=>'<a href="javascript:void(0)"	onclick="editParams(this)" data-id="'.$row->id.'" ><i class="fa fa-edit" title="Edit Detail"></i></a> '
				);
			}
			
			$output = array(
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
	}


	public function email()
	{	
		$data['row'] = $this->db->query("select * from email_setting where id=1")->row();	
		$this->config->set_item('mysubmenu', 'mn123');
		$this->template->set('breadcrumbs','<h5>Pusat Data<small> <i class="ace-icon fa fa-angle-double-right"></i> Setting E-mail Sender</small></h5>');
		$this->template->set('pagetitle','Data Setting  E-mail Sender');		
		$this->template->load('default','setting/vemail_setting',$data);
	}

	public function json_data_email(){
		//if ($this->input->is_ajax_request()){
		
			$start = $this->input->get('iDisplayStart');
			$limit = $this->input->get('iDisplayLength');
			$sortby = $this->input->get('iSortCol_0');
			$srotdir = $this->input->get('sSortDir_0');
			
			$str = "select * from email_setting where id = 1 ";
			
						
			if ( $_GET['sSearch'] != "" )
			{
				
				$str .= " and  email_host like '%".mysql_real_escape_string( $_GET['sSearch'] )."'  ";
				
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
					'id'=>$row->id,
					'email_host'=>$row->email_host,
					'email_port'=>$row->email_port,
					'email_user'=>$row->email_user,										
					'email_from_name'=>$row->email_from_name,
					'ACTION'=>"<a href='javascript:void(0)' onclick='editThis(this)' data-id='".$row->id."'><i class='fa fa-edit' title='Edit'></i></a> "
					
				);
			}
			
			$output = array(
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"strfilter" => $strfilter,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $aaData
			);
			echo json_encode($output);
		//}
	}
	public function editThis(){
		$id = $this->input->post('id');	//id as nik=>peg_pendidikan
		$nmtable=$this->input->post('tabel');
		$field=$this->input->post('field');
		$str="select * from ".$nmtable."  where ".$field."='".$id."'";
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
	public function saveData_email(){
		$isi="awal<br>";
		if ($this->input->is_ajax_request()){	
			$this->load->library('form_validation');
			$state=$this->input->post('state');
				$rules = array(				
					array(
						'field' => 'email_host',
						'label' => 'EMAIL_HOST',
						'rules' => 'trim|xss_clean|required'
					),
					array(
						'field' => 'email_port',
						'label' => 'EMAIL_PORT',
						'rules' => 'trim|xss_clean|required'
					),
					array(
						'field' => 'email_user',
						'label' => 'EMAIL_USER',
						'rules' => 'trim|xss_clean|required'
					),
					array(
						'field' => 'email_from',
						'label' => 'EMAIL_FROM',
						'rules' => 'trim|xss_clean|required'
					)
				);
		

		

			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				$isi.="masuk form valid<br>";
				$state=$this->input->post('state');
				$insert_id=''; $id_is_active="";
				
				$this->db->trans_begin();
				try {	
					
					$data = array(
							'email_host' => $this->input->post('email_host'),
							'email_port' => $this->input->post('email_port'),
							'email_user' => $this->input->post('email_user'),
							'email_pass' => $this->input->post('email_pass'),							
							'email_from' => $this->input->post('email_from'),
							'email_from_name' => $this->input->post('email_from_name')
						);
				
					
												
						if ($this->db->where('id',$state)->update('email_setting',$data)){
								$id_is_active = $state;
									$this->db->trans_commit();
									$respon->status = 'success';
						} else {
							throw new Exception("gagal simpan");
						}					

					
					$isi.="Proses query<br>";
					
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
			$respon->data=$isi;	
			echo json_encode($respon);
			
		} 
		
	}
}
