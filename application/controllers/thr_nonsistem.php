<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class thr_nonsistem extends MY_App {

	function __construct()
	{
		parent::__construct();
		$this->load->model('thr_model');
		$this->config->set_item('mymenu', 'mn4');
		$this->load->helper('array');
		$this->load->helper('download');
		$this->load->dbutil();
	    $this->load->helper('file');
		$this->load->database();
		$this->auth->authorize();
	}
	

	public function index()
	{
		$this->template->set('pagetitle','Form Daftar THR Non Sistem');
		$this->config->set_item('mySubMenu', 'mn44');
		$data['cabang'] = $this->common_model->comboCabang('--Semua Cabang--');
		$data['komponen'] = $this->db->query("select * from mst_komp_gaji where isactive=1")->result();
		$data['divisi'] = array();
		$data['thn'] = $this->getYearArr();
		
		$this->template->load('default','fthr_nonsistem/filterForm',$data);
	}

	public function thr_list(){
		$this->config->set_item('mySubMenu', 'mn44');
		$this->template->set('pagetitle','Daftar THR Non Sistem');		
		$tahun=$this->input->post('tahun');	
		$isXls=$this->input->post('isXls');
		
		$strList = "SELECT p.*,  period_diff( date_format( now( ) , '%Y%m' ) , date_format( tgl_aktif, '%Y%m' ) ) SELISIH from pegawai p where  status_aktif=1  and p.id_jab in (103, 104, 6,7) order by id_cabang, nik";
		
		$strCek="select count(*) CEK, VALIDASI, ID from thr_validasi where tahun='$tahun'  and jenis='non_sistem'";

		$rsCek = $this->db->query($strCek)->row();
		
		$data['cek']  = $this->db->query($strCek)->row();
		$data['row'] = $this->db->query($strList)->result();		
		$data['tahun'] = $tahun;	
		
		
		$view="";
		$sts="";
		//$nmCabang = $this->gate_db->query("select KOTA from mst_cabang where id_cabang=".$this->input->post('id_cabang'))->row();
		//$rsnmdiv=$this->db->query("select NAMA_DIV from mst_divisi where id_div=$id_div")->row();
		if ($rsCek->CEK<=0){
			$title="Daftar Data THR Non Sistem (NEW)";
			$sts="new";
			$view="thr_nonsistem_list";
		}else{	
			$data['sts_validasi'] = $rsCek->VALIDASI;
			$data['id_validasi']=$rsCek->ID;
			$strList="select t.*, p.NIK, p.NAMA, period_diff( date_format( now( ) , '%Y%m' ) , date_format( tgl_aktif, '%Y%m' ) ) SELISIH, p.TGL_AKTIF, p.TGL_AWAL_KONTRAK,p.STATUS_PEGAWAI, p.ID_JAB, p.ID_CABANG from thr_nonsistem t, pegawai p where p.nik=t.nik   and tahun='".$tahun."' and id_validasi=".$rsCek->ID." order by p.id_cabang, p.nik";
			//$data['row'] = $this->db->query()->result();	
			$data['row'] = $this->db->query($strList)->result();	
			if (date('Y')>=$tahun){	
				//boleh edit, ambil data tabel thr
				$title="Daftar Data THR Non Sistem (OPEN)";
				$sts="edit";					
			}else{
				$title="Daftar Data THR Non Sistem (CLOSED)";
				$sts="disabled";
				
			}
			$view="thr_nonsistem_listed";
		}
		$data['strList'] = $strList;
		$data['sts']=$sts;
		if ($isXls==0){
			$this->template->set('pagetitle',$title." ".$tahun);	
			$this->template->load('default','fthr_nonsistem/'.$view,$data);
		}else{
			//$objPHPExcel = new PHPExcel();
			$html=$this->load->view('fthr_nonsistem/thr_nonsistem_excel' , $data, true);
			// Put the html into a temporary file
			$tmpfile = 'assets/excel/'.time().'.html';
			file_put_contents($tmpfile, $html);
		
			// Read the contents of the file into PHPExcel Reader class
			$reader = new PHPExcel_Reader_HTML; 
			//$reader = new PHPExcel; 
			$content = $reader->load($tmpfile); 
			// Pass to writer and output as needed
			$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
			$objWriter->save('assets/excel/thrnonsistem_'.$tahun.'.xlsx');		
			
			$data['isi']='assets/excel/thrnonsistem_'.$tahun.'.xlsx';
			echo json_encode($data);

			// Delete temporary file
			unlink($tmpfile);
			//$this->ci_pdf->pdf_create_report($html, $nmfile, 'a4', 'portrait');
		}
	}


	public function save_thr_nonsistem(){
		if($this->input->post()) {
			$this->load->library('form_validation');
			$rules = array();
			for($r=1;$r<=$this->input->post('jmlRow');$r++){	
				if ($this->input->post('flag_'.$r)=="1"){
					array_push($rules, array(
							'field' => 'nik_'.$r,
							'label' => 'nik_'.$r,
							'rules' => 'trim|xss_clean|required'));					
				}
			}
			$sts=$this->input->post('sts');
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi angka.');
			$respon = new StdClass();
			$xGets="mulai";
			if ($this->form_validation->run() == TRUE){
				$xGets.="masuk run";
				try {
					$this->db->trans_begin();
					$id_validasi="";
					if (trim($sts)=="new"){
						
						$dataMaster = array(
									'TAHUN' => $this->input->post('tahun'),
									'JENIS' =>  'non_sistem',
									'VALIDASI' => 0,
									'CREATED_BY' =>$this->session->userdata('auth')->id,
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>$this->session->userdata('auth')->id,
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);
						if ($this->db->insert('thr_validasi', $dataMaster)){	
							$id_validasi = $this->db->insert_id();							
									$this->db->trans_commit();
									$respon->status ="success";
								} else {
									throw new Exception("gagal simpan");
									$respon->status ="error";
									$respon->errormsg ="master validasi";
								}
					}else{
						$xGets=" mlebu else ".$sts;
						$id_validasi=$this->input->post('id_validasi');						
						$this->db->query("delete from thr_nonsistem where id_validasi=".$id_validasi);		
						$this->db->trans_commit();
					}

					for($r=1;$r<=$this->input->post('jmlRow');$r++){
						if ($this->input->post('flag_'.$r)=="1"){
						
							$data = array(									
									'ID_VALIDASI' => $id_validasi,
									'TAHUN' => $this->input->post('tahun'),
									'ID_CABANG' => $this->input->post('id_cabang_'.$r),
									'NIK' => $this->input->post('nik_'.$r),
									'MASA_KERJA_BLN' => $this->input->post('masakerja_'.$r),
									'JML_TERIMA' => $this->input->post('pendapatan_'.$r),
									'JML_POTONGAN' => $this->input->post('potongan_'.$r),									
									'TOTAL' => $this->input->post('total_'.$r)
								);
								if ($this->db->insert('thr_nonsistem', $data)){									
									$this->db->trans_commit();
								} else {
									throw new Exception("gagal simpan");
								}
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
			$respon->xGets=$xGets;
			echo json_encode($respon);
			exit;
		}

		$this->template->set('pagetitle','Saving...');

	}


	public function exportCsv(){		
		$thn=$this->input->post('thn');
		$id_validasi=$this->input->post('id_validasi');	
		$rsmaster=$this->db->query("select * from thr_validasi where id=".$id_validasi)->row();
		//row THR Non Sistem
		$strList="SELECT p.NIK, p.REKENING,  p.NAMA, gs.TOTAL 
			FROM `pegawai` p, thr_nonsistem gs
			WHERE p.nik=gs.nik  and gs.id_validasi= ".$id_validasi;
		$rsRes=$this->db->query($strList)->result();
		$i=1;
		//clean nama
		$out="";
		foreach($rsRes as $row){
			$out.=$i.", ".strtoupper(preg_replace('/[^a-zA-Z0-9\s]/', '', $row->NAMA)).", ".$row->REKENING.", ".$row->TOTAL."\r\n";
			$i++;
		}
		//cek dir
		$path=$this->createPath('csv_thr',$rsmaster->JENIS, $rsmaster->WILAYAH, $rsmaster->TAHUN,'' );	//csv path hanya s.d thn masuk nama file
		$fileName="thr_nonsistem_".$thn.".csv";
		write_file($path."/".$fileName,$out);	

		//SIMPAN RECORD
		$strcek="select * from file_csv_thr where jenis='".$rsmaster->JENIS."' and thn='".$rsmaster->TAHUN."' ";
		$cek=$this->db->query($strcek)->num_rows();
		$data = array(
						
						'THN' => $rsmaster->TAHUN,						
						'JENIS' => $rsmaster->JENIS,
						'PATH' => $path,
						'NAMA_FILE' => $fileName,
						'CREATED_BY' =>$this->session->userdata('auth')->id,
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>$this->session->userdata('auth')->id,
						'UPDATED_DATE' =>date('Y-m-d H:i:s')
					);

		$cek=$this->db->query($strcek)->num_rows();
		if ($cek>0){
			$this->db->where(array('ID'=>$rsmaster->ID))->update('file_csv_thr', $data);
		}else{			
			$this->db->insert('file_csv_thr', $data);
			
		}
		$this->db->trans_commit();
		$data['isi']=$path."/".$fileName;
		echo json_encode($data);
	}

	

	public function cetak_slip(){
		$this->load->library('CI_Pdf');
		$param=$this->input->get('param');
		$arrKey=explode('_',$param);		
		$id_validasi=$arrKey[0];
		$nik=$arrKey[1];
		$str="SELECT p.NIK,p.TGL_AKTIF, p.NAMA, p.ID_CABANG, p.ID_DIV,p.ID_JAB, gs. *
			FROM `thr_nonsistem` gs, pegawai p
			WHERE gs.nik = p.nik and gs.TAHUN='".$arrKey[0]."' and gs.nik='".$nik."'";		
		$rsOut=$this->db->query($str)->row();
		$rsMst=$this->gate_db->query("select NAMA_JAB from mst_jabatan where id_jab=".$rsOut->ID_JAB)->row();
		
		$data['str']=$str;		
		$data['thn']=$arrKey[0];
		
		
		$interval = date_diff(date_create(), date_create($rsOut->TGL_AKTIF));
		$masaKerja= $interval->format("  %Y Tahun, %M Bulan, %d Hari");
		

		$html=$this->commonlib->pdfHeadertemplate();
		//$html=$this->commonlib->reportHeader();
		$html.="<table border=0>";
		$html.="<tr><td width='35%'><img src=\"".base_url('assets/img/logo2.png')."\" width=\"100\"></td>";
		$html.="<td style='text-align:center'>";
		$html.="<h2>YAYASAN YATIM MANDIRI</h2>";
		$html.='Jl. Raya Jambangan 135-137<br>';
		$html.="Telp. 031-8283844, Fax. 031-8291757<br>";
		$html.="SURABAYA 60400<br>";
		$html.="</td>";
		$html.="</tr>";
		$html.="<tr><td colspan=2 style='text-align:center'><h2>SLIP THR KARYAWAN YATIM MANDIRI<br>TAHUN ".$thn."</h2></td></tr>";
		$html.="<tr><tD colspan=2></td></tr>";
		$html.="<tr><tD >NAMA </td><tD> : ".$rsOut->NAMA."</td></tr>";
		$html.="<tr><tD >JABATAN</td><tD> : ".$rsMst->NAMA_JAB."</td></tr>";
		$html.="</table>";
		$tot_pendapatan=$rsOut->jml_terima;
		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Jumlah pendapatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->jml_terima,0,',','.')."</td></tr>";		
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($tot_pendapatan,0,',','.')."</b></th></tr>";
		$tot_pot=$rsOut->jml_potongan;
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Jumlah Potongan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->jml_potongan,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format($tot_pot,0,',','.')."</b></th></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format($tot_pendapatan-$tot_pot,0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip_thr','non_sistem', '', $thn, date('m') );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP_thr_nonsistem_".$thn."-".$nik.".pdf";		
		
		//$this->ci_pdf->pdf_create($html, $path."/".$fileName, FALSE);
		$this->ci_pdf->pdf_create_my($html, $path."/".$fileName, 'a6','portrait', FALSE);
		
		//SIMPAN RECORD
		$strcek="select * from file_slip_thr where nik='".$nik."' and thn='$thn'";
		$cek=$this->db->query($strcek)->num_rows();
		if ($cek>0){
			$data = array('PATH' => $path,'NAMA_FILE' => $fileName, 'UPDATED_BY' =>'admin', 'UPDATED_DATE' =>date('Y-m-d H:i:s')	);
			$this->db->where(array('nik'=>$nik,  'thn'=>$thn))->update('file_slip', $data);
		}else{
			$data = array(
						'NIK' => $nik,
						'THN' => $thn,						
						'PATH' => $path,
						'NAMA_FILE' => $fileName,
						'CREATED_BY' =>$this->session->userdata('auth')->id,
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>$this->session->userdata('auth')->id,
						'UPDATED_DATE' =>date('Y-m-d H:i:s')
					);
			$this->db->insert('file_slip_thr', $data);
			
		}
		$this->db->trans_commit();
		$data['isi']=$path."/".$fileName;
		echo json_encode($data);

	}

	function slipLoop(){
		//slipLoop param : alamat email, path file slip
		$id_validasi=$this->input->get('id_validasi');		
		$thn=$this->input->get('thn');
		$str = "select * from thr_nonsistem where TAHUN='$thn' and id_validasi=".$id_validasi." ORDER BY `NIK` ";
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
			echo json_encode($data);
		} else {
			$str .= " LIMIT ".($step-1).",1 ";
			$result = $this->db->query($str)->row();
			if (!empty($result)){
				$param=$id_validasi."_".$result->nik;
				$this->loop_cetak_slip($param);
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


	public function loop_cetak_slip($param){
		$this->load->library('CI_Pdf');		
		$arrKey=explode('_',$param);		
		$id_validasi=$arrKey[0];
		$nik=$arrKey[1];
		$str="SELECT p.NIK,p.TGL_AKTIF, p.NAMA, p.ID_CABANG, p.ID_DIV,p.ID_JAB, gs. *
			FROM `thr_nonsistem` gs, pegawai p
			WHERE gs.nik = p.nik and gs.id_validasi='".$arrKey[0]."' and gs.nik='".$nik."'";		
		$rsOut=$this->db->query($str)->row();

		$rsmaster=$this->db->query("select * from thr_validasi where id=".$id_validasi)->row();
		$rsMst=$this->gate_db->query("select NAMA_JAB from mst_jabatan where id_jab=".$rsOut->ID_JAB)->row();
		
		$data['str']=$str;		
		$thn=$rsmaster->TAHUN;
		$data['thn']=$thn;
		
		
		$interval = date_diff(date_create(), date_create($rsOut->TGL_AKTIF));
		$masaKerja= $interval->format("  %Y Tahun, %M Bulan, %d Hari");
		

		$html=$this->commonlib->pdfHeadertemplate();
		$html.="<table border=0>";
		$html.="<tr><td width='35%'><img src=\"".base_url('assets/img/logo2.png')."\" width=\"100\"></td>";
		$html.="<td style='text-align:center'>";
		$html.="<h2>YAYASAN YATIM MANDIRI</h2>";
		$html.='Jl. Raya Jambangan 135-137<br>';
		$html.="Telp. 031-8283844, Fax. 031-8291757<br>";
		$html.="SURABAYA 60400<br>";
		$html.="</td>";
		$html.="</tr>";
		$html.="<tr><td colspan=2 style='text-align:center'><h2>SLIP THR KARYAWAN YATIM MANDIRI<br>TAHUN ".$thn."</h2></td></tr>";
		$html.="<tr><tD colspan=2></td></tr>";
		$html.="<tr><tD >NAMA </td><tD> : ".$rsOut->NAMA."</td></tr>";
		$html.="<tr><tD >JABATAN</td><tD> : ".$rsMst->NAMA_JAB."</td></tr>";
		$html.="</table>";
		$tot_pendapatan=$rsOut->jml_terima;
		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Jumlah pendapatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->jml_terima,0,',','.')."</td></tr>";		
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($tot_pendapatan,0,',','.')."</b></th></tr>";
		$tot_pot=$rsOut->jml_potongan;
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Jumlah Potongan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->jml_potongan,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format($tot_pot,0,',','.')."</b></th></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format($tot_pendapatan-$tot_pot,0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip_thr',$rsmaster->JENIS, "" , $rsmaster->TAHUN,'' );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP_thr_nonsistem_".$thn."-".$nik.".pdf";		
		
		//$this->ci_pdf->pdf_create($html, $path."/".$fileName, FALSE);
		$this->ci_pdf->pdf_create_my($html, $path."/".$fileName, 'a6','portrait', FALSE);
		
		//SIMPAN RECORD
		$strcek="select * from file_slip_thr where nik='".$nik."' and jenis='".$rsmaster->JENIS."' and thn='".$rsmaster->TAHUN."'  ";
		$cek=$this->db->query($strcek)->num_rows();

		$data = array(
						'NIK' => $nik,
						'THN' => $thn,	
						'JENIS' => $rsmaster->JENIS,
						'PATH' => $path,
						'NAMA_FILE' => $fileName,
						'CREATED_BY' =>$this->session->userdata('auth')->id,
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>$this->session->userdata('auth')->id,
						'UPDATED_DATE' =>date('Y-m-d H:i:s')
					);

		if ($cek>0){
			$rs=$this->db->query($strcek)->row();
			$this->db->where(array('id'=>$rs->ID))->update('file_slip_thr', $data);
		}else{
			
			$this->db->insert('file_slip_thr', $data);
			
		}
		//$this->db->trans_commit();
		//$data['isi']=$path."/".$fileName;
		//echo json_encode($data);
		$this->db->trans_commit();
		return 1;
		

	}



}
