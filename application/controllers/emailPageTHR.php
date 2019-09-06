<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class emailPageTHR extends MY_App {

	function __construct()
	{
		parent::__construct();
		$this->load->model('emp_model');
		$this->config->set_item('mymenu', 'mn4');
		$this->auth->authorize();
	}
	
	public function index()
	{	$options = array(
                  'laz'  => 'LAZ Staff Dalam',
                  'tasharuf'  => 'Tasharuf Staff Dalam',
                  'zisco_transport'    => 'Zisco Transport',
                  'non_sistem'    => 'Non Sistem'
                );
		$this->config->set_item('mySubMenu', 'mn43');
		$data['cabang'] = $this->common_model->comboCabang();
		$data['divisi'] = $this->divTree($this->common_model->getDivisi()->result_array());		
		$data['arrThn'] = $this->getYearArr();
		$data['jenis'] = $options;
		$this->template->set('pagetitle','Proses Pengiriman Slip THR');		
		$this->template->load('default','femailTHR/filterForm',$data);
	}
	function slipLoop(){
		//slipLoop param : alamat email, path file slip
		$jenis=$this->input->get('jenis');
		$wilayah=$this->input->get('wilayah');		
		$thn=$this->input->get('thn');

		$str = "select p.NAMA, p.EMAIL,f.* from file_slip_thr f, pegawai p where p.nik=f.nik and   thn='$thn' and f.jenis='".$jenis."'  ".($jenis=="laz" || $jenis=="tasharuf" ?" and f.wilayah='".$wilayah."'  " :"")." and p.status_aktif=1 ORDER BY f.`NIK`	 ";
		
		$cnt = $this->input->get('cnt');
		$step = $this->input->get('step');
		if (empty($step)){
			$count = $this->db->query($str)->num_rows();
			if ($count>0){
				$data['status']=1;
				$data['jumlah']=$count;
			} else {
				$data['status']=0;
			}
			$data["str"]=$str;
			echo json_encode($data);
		} else {
			$str .= " LIMIT ".($step-1).",1 ";
			$result = $this->db->query($str)->row();
			if (!empty($result)){
				$emailTo=$result->EMAIL;
				$subject="Slip THR Tahun ".$thn;
				$attach=$result->PATH."/".$result->NAMA_FILE;
				$msg="No-Reply, Automatic Emailing System. Pengiriman Slip THR Karyawan ".$result->NIK." - ".$result->NAMA." tahun ".$thn;
				if (!empty($emailTo) || $emailTo!=""){
					$data['status']=$this->slipEmail($emailTo, $subject, $msg, $attach);
				}
				//$this->sendMassEmailSlip($thn, $bln, $result->NIK);
				$data['status']=1;
				$data['nextstep']=$step+1;
				$data['complete']=0;
				$data['percent']= number_format((float)($step/$cnt*100), 0, '.', '');
			} else {
				$data['status']=1;
				$data['complete']=1;
			}
			sleep(2);
			echo json_encode($data);
		}
	}
	


	public function showResultSingle(){	
		$this->config->set_item('mySubMenu', 'mn43');
		$thn = $this->input->post('thn');
		$nik = $this->input->post('nik');
		$str="select p.nik, p.nama, f.path, f.nama_file from pegawai p, file_slip_thr f where p.nik=f.nik and thn='$thn' and f.nik='$nik'";
		if ($this->db->query($str)->num_rows()<=0){
			$out="File Slip THR $nik pada tahun $thn belum digenerate. Silakan Generate terlebih dahulu di halaman Payroll Staff";
		}else{
			$this->db->query($str)->row();
			$out="&nbsp;==>&nbsp;<a href=\"javascript:void(0)\" data-url=\"".base_url('emailPageTHR/sendEmailSlip')."\"  onclick=\"sendEmailSlip(this, '$nik', '".date('m')."', '$thn')\"><i class=\"fa fa-envelope\" title=\"Send Email\">Kirim Email</i></a>";
		}
		$data['str']=$str;
		$data['hasil']=$out;
		echo json_encode($data);
	}
	public function sendEmailSlip(){
		$str="select f.*,p.NAMA, p.EMAIL from pegawai p, file_slip_thr f where p.nik=f.nik and thn='".$this->input->get('thn')."' and f.nik='".$this->input->get('nik')."'";
		$count = $this->db->query($str)->num_rows();
		
		if ($count>0){		
			$result = $this->db->query($str)->row();
			$emailTo=$result->EMAIL;
			$subject="Slip Gaji ".$this->input->get('bln')."-".$this->input->get('thn');
			$attach=$result->PATH."/".$result->NAMA_FILE;
			$msg="No-Reply, Automatic Emailing System. Pengiriman Slip THR Karyawan ".$result->NIK." - ".$result->NAMA." tahun ".$this->input->get('thn');
					
			$data['status']=$this->slipEmail($emailTo, $subject, $msg, $attach);
		}else{
			$data['status']=0;
		}
		$data['str']=$str;
		echo json_encode($data);
	}

	public function getNik(){
		$keyword = $this->input->post('term');
		$data['response'] = 'false';
		$query = $this->emp_model->getByNama($keyword);
		if( ! empty($query) )
		{
			$data['response'] = 'true'; //Set response
			$data['message'] = array(); //Create array
			foreach( $query as $row )
			{	
				$data['message'][] = array(
					'id'=>$row->NIK,
					'label' => $row->NIK.' - '.$row->NAMA,
					'value' => $row->NAMA,
					'cabang' => $row->ID_CABANG,
					'divisi' => $row->ID_DIV,
					'jabatan' => $row->ID_JAB,
					''
				);
			}
		}
		echo json_encode($data);
	}
	
}
