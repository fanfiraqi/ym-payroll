<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class hrdReportPersonal extends MY_App {

	function __construct()
	{
		parent::__construct();
		$this->load->model('emp_model');
		$this->load->model('report_model');
		$this->load->helper('file');
		$this->load->library('CI_Pdf');
		$this->load->helper('download');
		$this->config->set_item('mymenu', 'menuEnamSatu');
		$this->auth->authorize();
	}
	
	public function index()
	{
		$this->template->set('pagetitle','Informasi Data Personal (View/Cetak)');		
		$this->template->load('default','fhrdReport/index',compact('str'));
	}
	
	
	public function personalData($param=null){
		$header=$this->commonlib->reportHeader();
		$footer=$this->commonlib->reportFooter();
		if ($param!=null){
			$arr=explode("_",$param);
			$display=$arr[2];
			$nik=$arr[0];
			$sub=$arr[1];
		}else{
			$display=$this->input->post('display');
			$nik=$this->input->post('nik');
			$sub=$this->input->post('sub');
		}
		$data['rowPrestasi'] = $this->report_model->view_prestasi($nik);
		$data['rowPelatihan'] = $this->report_model->view_pelatihan($nik);
		$data['rowPelanggaran'] = $this->report_model->view_pelanggaran($nik);
		$data['rowMutasi'] = $this->report_model->view_mutasi($nik);
		$data['rowResign'] = $this->report_model->view_resign($nik);
		$data['rowLembur'] = $this->report_model->view_lembur($nik);
		$data['rowCuti'] = $this->report_model->view_cuti($nik);
		$data['row'] = $this->emp_model->view_emp_byNik($nik);
		$data['rowKel'] = $this->emp_model->view_hubkel($nik);
		$data['viewKop']=$this->commonlib->tableKop('HRD-DK','Data Karyawan', '00', 1,3);
		$data['display']=$display;
		$data['nik']=$nik;
		$html=$header;
		$filename="";
		if ($display==0){
			$this->template->set('pagetitle','Informasi Data Personal (View/Cetak)');		
			$this->template->load('default','fhrdReport/pd_index',$data);
		}else{
			switch($sub){
				case "pribadi":
					$html.=$this->commonlib->tableKop('HRD-DK','Data Karyawan', '00', 1,3);
					$html.=$this->load->view('fhrdReport/pd_dataPribadi', $data, true);
					$html.=$footer;
					//echo $html;
					$namafile="dataPribadi_".$nik;					
					break;
				case "keluarga":
					$html.=$this->commonlib->tableKop('HRD-DK','Data Karyawan', '00', 2,3);
					$html.=$this->load->view('fhrdReport/pd_dataKeluarga', $data, true);
					$html.=$footer;
					//echo $html;
					$namafile="dataKeluarga_".$nik;					
					break;
				case "pekerjaan":
					$html.=$this->commonlib->tableKop('HRD-DK','Data Karyawan', '00', 3,3);
					$html.=$this->load->view('fhrdReport/pd_dataPekerjaan', $data, true);
					$html.=$footer;
					//echo $html;
					$namafile="dataPekerjaan_".$nik;					
					break;
			}
		$this->ci_pdf->pdf_create($html, $namafile);
		/*	$theFile=$this->ci_pdf->pdf_create_report($html, $namafile, false);			
			//write_file("assets/".$namafile.".pdf", $theFile);
			//force_download("assets/".$namafile.".pdf", $theFile);

			file_put_contents("assets/".$namafile.".pdf", $theFile); 
			//print the pdf file to the screen for saving
			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="file.pdf"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize("assets/".$namafile.".pdf"));
			header('Accept-Ranges: bytes');
			readfile("assets/".$namafile.".pdf");*/
			
		}
		
	}
	
}
