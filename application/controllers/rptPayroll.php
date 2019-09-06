<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class rptPayroll extends MY_App {
	var $role;
	function __construct()
	{
		parent::__construct();
		$this->load->model('emp_model');
		$this->load->model('report_model');
		$this->load->helper('file');
		$this->load->library('CI_Pdf');
		$this->load->helper('download');
		$this->config->set_item('mymenu', 'mn5');
		$this->auth->authorize();
		$sess=$this->session->userdata('gate');
		$this->role=$sess['group_id'];
	}
	
	public function staff()
	{
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrThn'] = $this->getYearArr();
		$data['role'] = $this->role;
		$data['jenis'] = "staff";
		$this->config->set_item('mySubMenu', 'mn51');
		$data['cabang'] = $this->common_model->comboCabang('--- Semua Cabang ---');
		$this->template->set('pagetitle','Rekap Penggajian Staf Dalam (View/Cetak)');		
		$this->template->load('default','freport/filterForm',$data);
	}
	public function zisco()
	{
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrThn'] = $this->getYearArr();
		$data['role'] = $this->role;
		$data['jenis'] = "zisco";
		$this->config->set_item('mySubMenu', 'mn52');
		$data['cabang'] = $this->common_model->comboCabang('--- Semua Cabang ---');
		$this->template->set('pagetitle','Rekap Penggajian Zisco (View/Cetak)');		
		$this->template->load('default','freport/filterForm',$data);
	}
	public function bonus_kacab()
	{
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrThn'] = $this->getYearArr();
		$data['role'] = $this->role;
		$data['jenis'] = "bonus_kacab";
		$data['jns_gaji'] = "kacab_bonus";
		$this->config->set_item('mySubMenu', 'mn53');
		$data['cabang'] = $this->common_model->comboCabang('--- Semua Cabang ---');
		$this->template->set('pagetitle','Rekap Bonus Kacab(View/Cetak)');		
		$this->template->load('default','freport/filterForm',$data);
	}
	public function nonsistem()
	{
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrThn'] = $this->getYearArr();
		$data['role'] = $this->role;
		$data['jenis'] = "nonsistem";
		$data['jns_gaji'] = "non_sistem";
		$this->config->set_item('mySubMenu', 'mn54');
		$data['cabang'] = $this->common_model->comboCabang('--- Semua Cabang ---');
		$this->template->set('pagetitle','Rekap Penggajian Non-sistem (View/Cetak)');		
		$this->template->load('default','freport/filterForm',$data);
	}

	public function thr()
	{
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrThn'] = $this->getYearArr();
		$data['role'] = $this->role;
		$data['jenis'] = "thr";
		$this->config->set_item('mySubMenu', 'mn55');
		//$data['action'] = "staff";
		$data['cabang'] = $this->common_model->comboCabang('--- Semua Cabang ---');
		$this->template->set('pagetitle','Rekap THR (View/Cetak)');		
		$this->template->load('default','freport/filterForm',$data);
	}

	public function nasional()
	{
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrThn'] = $this->getYearArr();
		$data['role'] = $this->role;
		$data['jenis'] = "nasional";
		$this->config->set_item('mySubMenu', 'mn56');
		//$data['action'] = "staff";
		$data['cabang'] = $this->common_model->comboCabang('--- Semua Cabang ---');
		$this->template->set('pagetitle','Laporan Penggajian Nasional(View/Cetak)');		
		$this->template->load('default','freport/filterForm',$data);
	}

	
	public function rekapPayroll($param=null){
		$header=$this->commonlib->reportHeader();
		$footer=$this->commonlib->reportFooter();
		$blnStr=$this->arrBulan2;
		
		//cek p,ro,c
		$akses="";
		if ($this->role==20){
			$akses="RO";
		}elseif($this->role==26){
			$akses="cabang";
		}else{
			$akses="pusat";
		}
		$jenis="";$thn="";$bln="";$display=0;
		$tblgaji="";$judul="";$view="";$strlist="";$strlist_peg="";
		if ($param!=null){
			$arr=explode("_",$param);
			$jenis=$arr[0];
			if ($jenis=="thr"){
				$thn=$arr[1];				
				$penggajian=$arr[2];
				$display=$arr[3];	
				$daftar=$arr[4];	
			}else{
				$thn=$arr[1];
				$bln=$arr[2];
				$penggajian=$arr[3];
				$display=$arr[4];	
				$daftar=($jenis!="nasional"?$arr[5]:"");
			}

			
		}else{
			$display=$this->input->post('display');
			$thn=$this->input->post('cbTahun');
			$bln=$this->input->post('cbBulan');
			$jenis=$this->input->post('jenis');
			$penggajian=$this->input->post('penggajian');
			$daftar=($jenis!="nasional"?$this->input->post('daftar'):"");
			
		}
		
		switch($jenis){
			case "staff":
				$tblgaji="gaji_staff";
				$judul=($penggajian=='laz'?"LAZ/Amil":"Tasharuff")." Staff Dalam";
				$view=($daftar=="karyawan"?"vkaryawan":"vcabang");
				$strlist="SELECT IFNULL(SUM(gapok)+SUM(U_MAKAN_DITERIMA)+SUM(I_KEHADIRAN)+SUM(U_LEMBUR)+SUM(T_KELUARGA)+SUM(T_MASAKERJA)+SUM(T_JABATAN)+SUM(T_THT) +SUM(BPJS_KESEHATAN)+SUM(BPJS_NAKER)+SUM(T_PENYESUAIAN),0) pendapatan, IFNULL(SUM(POT_THT)+SUM(POT_LAIN)+SUM(POT_DANSOS)+SUM(POT_FAMGATH)+SUM(POT_QURBAN)+SUM(JML_ANGSURAN),0) potongan, IFNULL(SUM(total),0) payroll FROM gaji_staff where id_validasi in (select id from gaji_validasi where tahun='".$thn."' and bulan='".$bln."' and jenis='".$penggajian."') ";

				$strlist_peg="SELECT IFNULL((SELECT nama FROM pegawai WHERE nik=gaji_staff.nik),'') namapeg,  IFNULL(SUM(gapok)+SUM(U_MAKAN_DITERIMA)+SUM(I_KEHADIRAN)+SUM(U_LEMBUR)+SUM(T_KELUARGA)+SUM(T_MASAKERJA)+SUM(T_JABATAN)+SUM(T_THT) +SUM(BPJS_KESEHATAN)+SUM(BPJS_NAKER)+SUM(T_PENYESUAIAN),0) pendapatan, IFNULL(SUM(POT_THT)+SUM(POT_LAIN)+SUM(POT_DANSOS)+SUM(POT_FAMGATH)+SUM(POT_QURBAN)+SUM(JML_ANGSURAN),0) potongan, IFNULL(SUM(total),0) payroll FROM gaji_staff where id_validasi in (select id from gaji_validasi where tahun='".$thn."' and bulan='".$bln."' and jenis='".$penggajian."') ";
				break;
			case "zisco":
				$tblgaji=($penggajian=="zisco_transport"?"gaji_zisco_transport":"gaji_zisco_bonus");
				$judul=($penggajian=="zisco_transport"?" Transport ":" Bonus ")." Zisco";
				if ($penggajian=="zisco_transport"){
					$strlist="SELECT IFNULL(SUM(U_TRANS_DITERIMA)+SUM(T_JABATAN)+SUM(BPJS_KESEHATAN)+SUM(BPJS_NAKER)+SUM(T_PENYESUAIAN)+SUM(SERVIS_MOTOR)+SUM(KOREKSI),0) pendapatan, IFNULL(SUM(POT_LAIN)+SUM(JML_ANGSURAN),0) potongan, IFNULL(SUM(total),0) payroll FROM gaji_zisco_transport where id_validasi in (select id from gaji_validasi where tahun='".$thn."' and bulan='".$bln."' and jenis='".$penggajian."') ";
					
					$strlist_peg="SELECT IFNULL((SELECT nama FROM pegawai WHERE nik=gaji_zisco_transport.nik),'') namapeg,  IFNULL(SUM(U_TRANS_DITERIMA)+SUM(T_JABATAN)+SUM(BPJS_KESEHATAN)+SUM(BPJS_NAKER)+SUM(T_PENYESUAIAN)+SUM(SERVIS_MOTOR)+SUM(KOREKSI),0) pendapatan, IFNULL(SUM(POT_LAIN)+SUM(JML_ANGSURAN),0) potongan, IFNULL(SUM(total),0) payroll FROM gaji_zisco_transport where id_validasi in (select id from gaji_validasi where tahun='".$thn."' and bulan='".$bln."' and jenis='".$penggajian."') ";
				}else{
					$strlist="SELECT IFNULL(SUM(TUNJ_PENGAMBILAN)+SUM(INSENTIF_PENGAMBILAN)+SUM(BONUS_PRESTASI)+SUM(BONUS_PENGEMBANGAN)+SUM(INSI_ZAKAT_WAKAF_TUNAI)+SUM(BONUS_PATUNGAN_SAPI) +SUM(BONUS_QURBAN_SAPI)+SUM(KOREKSI)+SUM(PENYESUAIAN)+SUM(PENGEMBALIAN_40)+SUM(TUNJ_JABATAN)+SUM(TUNJ_PRESTASI),0) pendapatan, IFNULL(SUM(POT_DANSOS)+SUM(POT_ZAKAT)+SUM(LAIN_LAIN)+SUM(ANGSURAN),0) potongan, IFNULL(SUM(total),0) payroll FROM gaji_zisco_bonus where id_validasi in (select id from gaji_validasi where tahun='".$thn."' and bulan='".$bln."' and jenis='".$penggajian."') ";

					$strlist_peg="SELECT IFNULL((SELECT nama FROM pegawai WHERE nik=gaji_zisco_bonus.nik),'') namapeg,  IFNULL(SUM(TUNJ_PENGAMBILAN)+SUM(INSENTIF_PENGAMBILAN)+SUM(BONUS_PRESTASI)+SUM(BONUS_PENGEMBANGAN)+SUM(INSI_ZAKAT_WAKAF_TUNAI)+SUM(BONUS_PATUNGAN_SAPI) +SUM(BONUS_QURBAN_SAPI)+SUM(KOREKSI)+SUM(PENYESUAIAN)+SUM(PENGEMBALIAN_40)+SUM(TUNJ_JABATAN)+SUM(TUNJ_PRESTASI),0) pendapatan, IFNULL(SUM(POT_DANSOS)+SUM(POT_ZAKAT)+SUM(LAIN_LAIN)+SUM(ANGSURAN),0) potongan, IFNULL(SUM(total),0) payroll FROM gaji_zisco_bonus where id_validasi in (select id from gaji_validasi where tahun='".$thn."' and bulan='".$bln."' and jenis='".$penggajian."') ";
				}
				$view=($daftar=="karyawan"?"vkaryawan":"vcabang");
				break;
			case "bonus_kacab":
				$tblgaji="gaji_kacab_bonus";
				$judul="Bonus Kacab";
				$strlist="SELECT IFNULL(SUM(TUNJAB)+SUM(BONUS_KACAB)+SUM(TUNJ_PRESTASI)+SUM(PENYESUAIAN),0) pendapatan, IFNULL(SUM(DANSOS)+SUM(ZAKAT)+SUM(LAIN),0) potongan, IFNULL(SUM(total),0) payroll FROM gaji_kacab_bonus where id_validasi in (select id from gaji_validasi where tahun='".$thn."' and bulan='".$bln."' and jenis='kacab_bonus') ";

				$strlist_peg="SELECT IFNULL((SELECT nama FROM pegawai WHERE nik=gaji_kacab_bonus.nik),'') namapeg,  IFNULL(SUM(TUNJAB)+SUM(BONUS_KACAB)+SUM(TUNJ_PRESTASI)+SUM(PENYESUAIAN),0) pendapatan, IFNULL(SUM(DANSOS)+SUM(ZAKAT)+SUM(LAIN),0) potongan, IFNULL(SUM(total),0) payroll FROM gaji_kacab_bonus where id_validasi in (select id from gaji_validasi where tahun='".$thn."' and bulan='".$bln."' and jenis='kacab_bonus')  ";

				$view=($daftar=="karyawan"?"vkaryawan":"vcabang");
				break;
			case "nonsistem":
				$tblgaji="gaji_non_sistem";
				$strlist="SELECT IFNULL(SUM(JML_TERIMA),0) pendapatan, IFNULL(SUM(JML_POTONGAN),0) potongan, ifnull(sum(total),0) payroll FROM gaji_non_sistem where id_validasi in (select id from gaji_validasi where tahun='".$thn."' and bulan='".$bln."' and jenis='non_sistem') ";
				$strlist_peg="SELECT IFNULL((SELECT nama FROM pegawai WHERE nik=gaji_non_sistem.nik),'') namapeg,  IFNULL(SUM(JML_TERIMA),0) pendapatan, IFNULL(SUM(JML_POTONGAN),0) potongan, ifnull(sum(total),0) payroll FROM gaji_non_sistem where id_validasi in (select id from gaji_validasi where tahun='".$thn."' and bulan='".$bln."' and jenis='non_sistem')  ";
				$judul="Non Sistem";
				$view=($daftar=="karyawan"?"vkaryawan":"vcabang");
				break;
			case "thr":
				$tblgaji=($penggajian=="staff"?"thr_staff":"thr_zisco");
				if ($penggajian=="staff"){ //thr_staff
					$strlist="SELECT IFNULL(SUM(tunj_masakerja) + SUM(gapok)+SUM(uang_makan)+SUM(tunj_jabatan)+SUM(tunj_keluarga)+SUM(penyesuaian) ,0) pendapatan, IFNULL(SUM(dansos),0) potongan, IFNULL(SUM(total),0) payroll FROM thr_staff where id_validasi in (select id from thr_validasi where tahun='".$thn."' ) ";
					$strlist_peg="SELECT IFNULL((SELECT nama FROM pegawai WHERE nik=thr_staff.nik),'') namapeg,  IFNULL(SUM(tunj_masakerja) + SUM(gapok)+SUM(uang_makan)+SUM(tunj_jabatan)+SUM(tunj_keluarga)+SUM(penyesuaian) ,0) pendapatan, IFNULL(SUM(dansos),0) potongan, IFNULL(SUM(total),0) payroll FROM thr_staff where id_validasi in (select id from thr_validasi where tahun='".$thn."' )  ";
				}else{
					$strlist="SELECT IFNULL(SUM(uang_transport) + SUM(tunj_jabatan)+SUM(penyesuaian)+SUM(koreksi) ,0) pendapatan, IFNULL(SUM(potongan_dansos)+SUM(potongan_lain),0) potongan, IFNULL(SUM(total),0) payroll FROM thr_zisco where id_validasi in (select id from thr_validasi where tahun='".$thn."'  and jenis='zisco_transport') ";
					$strlist_peg="SELECT IFNULL((SELECT nama FROM pegawai WHERE nik=thr_zisco.nik),'') namapeg,  IFNULL(SUM(uang_transport) + SUM(tunj_jabatan)+SUM(penyesuaian)+SUM(koreksi) ,0) pendapatan, IFNULL(SUM(potongan_dansos)+SUM(potongan_lain),0) potongan, IFNULL(SUM(total),0) payroll FROM thr_zisco where id_validasi in (select id from thr_validasi where tahun='".$thn."'  and jenis='zisco_transport') ";
				}
				$judul=($penggajian=="staff"?" THR Staff ":"THR Zisco");
				$view=($daftar=="karyawan"?"vkaryawan":"vcabang");
				break;
			case "nasional":
				$judul="Nasional";
				$view="vnasional";
				break;
		}
		
		$title=($jenis=="thr"?'Laporan Rekap Penggajian '.$judul.' '.$thn:'Laporan Rekap Penggajian '.$judul.' '.$blnStr[$bln].' '.$thn);
		$titlekop=($jenis=="thr"?'Laporan Rekap Penggajian <br> '.$judul.' <br> '.$thn:'Laporan Rekap Penggajian <br> '.$judul.' <br>'.$blnStr[$bln].' '.$thn);
		$data['strlist_peg']=$strlist_peg;		  
		$data['strlist']=$strlist;		  
		$data['title']=$title;		  
		$data['titlekop']=$titlekop;		  
		$data['jenis']=$jenis;
		$data['daftar']=$daftar;
		$data['penggajian']=$penggajian;
		$data['tblgaji']=$tblgaji;
		$data['akses']=$akses;
		$data['display']=$display;
		$data['thn']=$thn;
		$data['bln']=$bln;
		//$data['id_cabang']=$id_cabang;
		if ($jenis<>"thr"){
		$data['strBulan']=$blnStr[$bln];
		}
		$strx="";
		
		$namafile="rekap_payroll_".$jenis."_".$view."_".$thn.$bln;
		if ($display==0){
			$this->template->set('pagetitle',$title.' (View/Cetak)');		
			$this->template->load('default','freport/'.$view,$data);
		}else{
			$html=$header;
			$html.=$this->load->view('freport/'.$view, $data, true);
			$html.=$footer;
			$this->ci_pdf->pdf_create_report($html, $namafile, 'a4', 'landscape');
		}	

	}
	
}