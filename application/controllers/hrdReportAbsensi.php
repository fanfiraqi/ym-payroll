<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class hrdReportAbsensi extends MY_App {

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
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrThn'] = $this->getYearArr();
		$data['cabang'] = $this->common_model->comboCabang();
		$this->template->set('pagetitle','Laporan Rekap Absensi Staff Bulanan ');		
		$this->template->load('default','fhrdReport/absensiFilter',$data);
	}
	
	
	public function rekapAbsenRes($param=null){
		$header=$this->commonlib->reportHeader();
		$footer=$this->commonlib->reportFooter();
		$blnStr=$this->arrBulan2;
		$title="Laporan Rekap Absensi Staff Bulanan";
		if ($param!=null){
			$arr=explode("_",$param);			
			$display=$arr[1];
			$cabang=$arr[0];
			$bln=$arr[2];
			$thn=$arr[3];
			
		}else{
			
			$display=$this->input->post('display');
			$cabang=$this->input->post('id_cabang');
			$bln=$this->input->post('cbBulan');
			$thn=$this->input->post('cbTahun');
		}
		$arrDivisi=$this->report_model->getArr_divAtCab($cabang);
		$nmCabang=$this->report_model->get_cabang($cabang);
		$data['display']=$display;
		$data['cabang']=$cabang;
		$data['bln']=$bln;
		$data['thn']=$thn;
		$data['arrDivisi']=$arrDivisi;
		$title.=" ".strtoupper($nmCabang->KOTA)." ".$blnStr[$bln]." ".$thn;
		$data['title']=$title;
		$data['strPeriode']=strtoupper($blnStr[$bln]." ".$thn);
		$namafile="rekap_absensi_".$nmCabang->KOTA."_".$thn.$bln;
		if ($display==0){
			$this->template->set('pagetitle',$title);		
			$this->template->load('default','fhrdReport/rekapAbsen',$data);
		}else{
			$html=$header;
			$html.=$this->load->view('fhrdReport/rekapAbsen', $data, true);
			$html.=$footer;
			$this->ci_pdf->pdf_create_report($html, $namafile, 'a4', 'landscape');
		}
		
	}
	
	
}
