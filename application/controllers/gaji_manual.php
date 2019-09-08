<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gaji_manual extends MY_App {

	function __construct()
	{
		parent::__construct();
		$this->load->model('gaji_model');
		$this->config->set_item('mymenu', 'mn3');
		$this->load->helper('array');
		$this->load->helper('download');
		$this->load->dbutil();
	    $this->load->helper('file');
		$this->load->database();
		$this->auth->authorize();
	}

	public function index()
	{
		$this->template->set('pagetitle','Form Penggajian Non Sistem');
		$this->config->set_item('mySubMenu', 'mn36');
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrIntBln'] = $this->arrIntBln;
		$data['arrThn'] = $this->getYearArr();
		$data['thn'] = $this->getYearArr();
		$data['cont'] = "gaji_manual";
		$this->template->load('default','fgaji_manual/filterForm',$data);
	}

	public function gaji_list(){
		$this->template->set('pagetitle','Daftar Penggajian Non-Sistem ');		
		$bulan=$bln=$this->input->post('cbBulan');		
		$tahun=$thn=$this->input->post('cbTahun');
		$isXls=$this->input->post('isXls');	
		// no zisco, dps, dokter
			
		$strList = "SELECT p.*,  period_diff( date_format( now( ) , '%Y%m' ) , date_format( tgl_aktif, '%Y%m' ) ) SELISIH from pegawai p where  status_aktif=1  and p.id_jab in (103, 104, 6,7) order by id_cabang, nik";
		
		//jenis = staff_laz, staff_tasharuf
		$strCek="select count(*) CEK, VALIDASI, ID  from gaji_validasi where jenis='non_sistem' and  tahun='$tahun' and bulan='$bulan' ";

		$rsCek = $this->db->query($strCek)->row();
		$data['cek']  = $this->db->query($strCek)->row();
		$data['row'] = $this->db->query($strList)->result();		
		$data['bln'] = $bulan;		
		$data['thn'] = $tahun;		
		$data['tahun'] = $tahun;		
		$arrBulan=$this->arrBulan2;
		$data['str'] = $strList;
		$sts="";
		$title="Data Penggajian Non-Sistem ".$arrBulan[$bulan]." ".$tahun;
		$view="vgaji_list";
		//$nmCabang = $this->gate_db->query("select KOTA from mst_cabang where id_cabang=".$this->input->post('id_cabang'))->row();
		if ($rsCek->CEK<=0){
			$title.=" (NEW)";
			$sts="new";			
		}else{	
			$data['sts_validasi'] = $rsCek->VALIDASI;
			$data['id_validasi']=$rsCek->ID;
			$data['row'] = $this->db->query("select t.*, p.NIK, p.NAMA, p.TGL_AKTIF, p.ID_JAB, p.ID_CABANG from gaji_non_sistem t, pegawai p where p.nik=t.nik AND BLN='".$bulan."' and thn='".$tahun."'")->result();	
			if (date('Y')>=$tahun){	
				//boleh edit, ambil data tabel thr
				$title.=" (OPEN)";
				$sts="edit";				
			}else{
				$title.=" (CLOSED)";
				$sts="disabled";
				
			}
			$view.="ed";
		}
		
		$data['sts']=$sts;
		if ($isXls==0){
			$this->template->set('pagetitle',$title." ".$bulan." ".$tahun);	
			$this->template->load('default','fgaji_manual/'.$view,$data);
		}else{
			//$objPHPExcel = new PHPExcel();
			$html=$this->load->view('fgaji_manual/vgaji_listed_excel' , $data, true);
			// Put the html into a temporary file
			$tmpfile = 'assets/excel/'.time().'.html';
			file_put_contents($tmpfile, $html);
		
			// Read the contents of the file into PHPExcel Reader class
			$reader = new PHPExcel_Reader_HTML; 			
			$content = $reader->load($tmpfile); 
			// Pass to writer and output as needed
			$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
			$objWriter->save('assets/excel/nonsistem_'.$bln.$thn.'.xlsx');			
			
			$data['isi']='assets/excel/nonsistem_'.$bln.$thn.'.xlsx';
			echo json_encode($data);


			// Delete temporary file
			unlink($tmpfile);
			
		}
	}


	public function save_gaji(){
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
			//$out=$this->form_validation->run();
			$this->form_validation->set_message('required', 'Field %s harus diisi angka.');
			$respon = new StdClass();
			//$xGets="mulai";
			if ($this->form_validation->run() == TRUE){
				//$xGets.="masuk run";
				try {
					$this->db->trans_begin();
					$id_validasi="";
					if (trim($sts)=="new"){
						$dataMaster = array(
									'TAHUN' => $this->input->post('thn'),
									'BULAN' => $this->input->post('bln'),
									'JENIS' =>  'non_sistem',
									'WILAYAH' =>  '',
									'VALIDASI' => 0,
									'CREATED_BY' =>$this->session->userdata('auth')->id,
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>$this->session->userdata('auth')->id,
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);
						if ($this->db->insert('gaji_validasi', $dataMaster)){	
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
						$this->db->query("delete from gaji_staff where id_validasi=".$id_validasi);		
						$this->db->trans_commit();
					}
					for($r=1;$r<=$this->input->post('jmlRow');$r++){
						if ($this->input->post('flag_'.$r)=="1"){
						$this->db->delete('gaji_non_sistem', array("NIK"=>$this->input->post('nik_'.$r), 'thn'=>$this->input->post('thn'), 'bln'=>$this->input->post('bln') ));
						//$xGets.="<br>".$this->db->last_query();
							$this->db->trans_commit();
							$data = array(									
									'ID_VALIDASI' => $id_validasi,
									'BLN' => $this->input->post('bln'),
									'THN' => $this->input->post('thn'),
									'ID_CABANG' => $this->input->post('id_cab_'.$r),
									'NIK' => $this->input->post('nik_'.$r),
									'ID_JAB' => $this->input->post('idjab_'.$r),
									'MASA_KERJA_BLN' => $this->input->post('masakerja_'.$r),
									'JML_TERIMA' => $this->input->post('pendapatan_'.$r),
									'JML_POTONGAN' => $this->input->post('potongan_'.$r),									
									'TOTAL' => $this->input->post('total_'.$r),
									'CREATED_BY' =>$this->session->userdata('auth')->id,
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>$this->session->userdata('auth')->id,
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);
								if ($this->db->insert('gaji_non_sistem', $data)){									
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
			echo json_encode($respon);
			//echo json_encode($respon)."<br>".$xGets;
			exit;
		}

		$this->template->set('pagetitle','Saving...');

	}


	public function exportCsv(){		
		$thn=$this->input->post('thn');
		$bln=$this->input->post('bln');
		

		$rsnmcab=$this->gate_db->query("select KOTA from mst_cabang where id_cabang=$id_cabang")->row();
		//$rsnmdiv=$this->db->query("select NAMA_DIV from mst_divisi where id_div=$id_divisi")->row();
		$rsRekNH=$this->db->query("select value1, VALUE2 from params where id=1")->row();
		$total=$this->db->query("select sum(total) TOT, count(*) CNT from gaji_non_sistem where  TAHUN='$thn' and  id_cabang=$id_cabang ")->row();

		$out="";
		//$out=$rsRekNH->VALUE2.", ".$rsRekNH->value1.",IDR,".$total->TOT.",GAJI STAFF ".$rsnmcab->KOTA." - ".$thn.",".$total->CNT.",".$thn.",,,"."\r\n";
		//norek yayasan
		
		//row GAJI staff
		$strList="SELECT p.NIK, p.REKENING,  p.NAMA, gs.TOTAL NOMINAL_THR
			FROM `pegawai` p, gaji_non_sistem gs
			WHERE p.nik=gs.nik  and tahun='$thn'  ";
		$rsRes=$this->db->query($strList)->result();
		$i=1;
		foreach($rsRes as $row){
			$out.=$i.", ".strtoupper($row->NAMA).", ".$row->REKENING.", ".round($row->NOMINAL_THR,0)." \r\n";
			$i++;
		}
		//cek dir
		$path=$this->createPath('csv',$id_cabang, '', $thn, date('m') );	//csv path hanya s.d thn masuk nama file
		$fileName="gaji_non_sistem_".$bln."_".$thn.".csv";
		write_file($path."/".$fileName,$out);	

		//SIMPAN RECORD
		$strcek="select * from file_csv where id_cab=$id_cabang and thn='$thn' and bln='".date('m')."' and NAMA_FILE='$fileName'";
		$cek=$this->db->query($strcek)->num_rows();
		if ($cek>0){	//id_cabang ?
			$data = array('PATH' => $path,'NAMA_FILE' => $fileName, 'UPDATED_BY' =>$this->session->userdata('auth')->id, 'UPDATED_DATE' =>date('Y-m-d H:i:s')	);
			$this->db->where(array('id_cab'=>$id_cabang, "jenis"=>"staff",  'thn'=>$thn,  'bln'=>date('m')))->update('file_csv_thr', $data);
		}else{
			$data = array(
						'ID_CAB' => $id_cabang,
						'THN' => $thn,
						'BLN' => date('m'),
						'JENIS' => 'staff',
						'PAtH' => $path,
						'NAMA_FILE' => $fileName,
						'CREATED_BY' =>$this->session->userdata('auth')->id,
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>$this->session->userdata('auth')->id,
						'UPDATED_DATE' =>date('Y-m-d H:i:s')
					);
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
		$thn=$arrKey[0];
		$bln=$arrKey[1];
		$nik=$arrKey[2];
		$str="SELECT p.NIK,p.TGL_AKTIF, p.NAMA, p.ID_CABANG, p.ID_DIV,p.ID_JAB, gs. *
			FROM `gaji_non_sistem` gs, pegawai p
			WHERE gs.nik = p.nik and gs.thn='".$arrKey[0]."'  and gs.bln='".$arrKey[1]."' and gs.nik='".$nik."'";		
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
		$html.="Telp. 031 8283488<br>";
		$html.="SURABAYA 60232<br>";
		$html.="</td>";
		$html.="</tr>";
		$html.="<tr><td colspan=2 style='text-align:center'><h2>SLIP GAJI KARYAWAN YATIM MANDIRI<br>TAHUN ".$thn."</h2></td></tr>";
		$html.="<tr><tD colspan=2></td></tr>";
		$html.="<tr><tD >NAMA </td><tD> : ".$rsOut->NAMA."</td></tr>";
		$html.="<tr><tD >JABATAN</td><tD> : ".$rsMst->NAMA_JAB."</td></tr>";
		$html.="</table>";
		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Gaji Pokok</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->gapok,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Tunjangan Masa kerja</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->tunj_masakerja,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Uang Makan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->uang_makan,0,',','.')."</td ></tr>";
		$html.="<tr><td>4</td><td>Insentif Kehadiran</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->tunj_kehadiran,0,',','.')."</td></tr>";
		$html.="<tr><td>5</td><td>Tunjangan Jabatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->tunj_jabatan,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($rsOut->total,0,',','.')."</b></th></tr>";
		
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Dansos</td><td>:</td><td style='text-align:right'>Rp. ".number_format(0,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>THT</td><td>:</td><td style='text-align:right'>Rp. ".number_format(0,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Zakat</td><td>:</td><td style='text-align:right'>Rp. ".number_format(0,0,',','.')."</td ></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format(0,0,',','.')."</b></th></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format($rsOut->total,0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip_thr','', '', $thn, date('m') );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP GAJI STAFF ".$thn."-".$nik.".pdf";		
		
		//$this->ci_pdf->pdf_create($html, $path."/".$fileName, FALSE);
		$this->ci_pdf->pdf_create_my($html, $path."/".$fileName, 'a6','portrait', FALSE);
		
		//SIMPAN RECORD
		$strcek="select * from file_slip where nik='".$nik."' and thn='$thn'";
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
			$this->db->insert('file_slip', $data);
			
		}
		$this->db->trans_commit();
		$data['isi']=$path."/".$fileName;
		echo json_encode($data);

	}

	function slipLoop(){
		//slipLoop param : alamat email, path file slip
		//$id_cab=$this->input->get('cabang');
		//$id_div=$this->input->get('divisi');		
		$thn=$this->input->get('thn');
		$bln=$this->input->get('bln');
		$str = "select * from gaji_non_sistem where thn='$thn' and bln='$bln' ORDER BY `NIK` ";
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
				$param=$thn."_".$result->nik;
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
		$thn=$arrKey[0];
		$nik=$arrKey[1];
		$str="SELECT p.NIK,p.TGL_AKTIF, p.NAMA, p.ID_CABANG, p.ID_DIV,p.ID_JAB, gs. *
			FROM `gaji_staff` gs, pegawai p
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
		$html.="Telp. 031 8283488<br>";
		$html.="SURABAYA 60232<br>";
		$html.="</td>";
		$html.="</tr>";
		$html.="<tr><td colspan=2 style='text-align:center'><h2>SLIP THR KARYAWAN YATIM MANDIRI<br>TAHUN ".$thn."</h2></td></tr>";
		$html.="<tr><tD colspan=2></td></tr>";
		$html.="<tr><tD >NAMA </td><tD> : ".$rsOut->NAMA."</td></tr>";
		$html.="<tr><tD >JABATAN</td><tD> : ".$rsMst->NAMA_JAB."</td></tr>";
		$html.="</table>";
		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Gaji Pokok</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->gapok,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Tunjangan Masa kerja</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->tunj_masakerja,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Uang Makan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->uang_makan,0,',','.')."</td ></tr>";
		$html.="<tr><td>4</td><td>Insentif Kehadiran</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->tunj_kehadiran,0,',','.')."</td></tr>";
		$html.="<tr><td>5</td><td>Tunjangan Jabatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->tunj_jabatan,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($rsOut->total,0,',','.')."</b></th></tr>";
		
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Dansos</td><td>:</td><td style='text-align:right'>Rp. ".number_format(0,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>THT</td><td>:</td><td style='text-align:right'>Rp. ".number_format(0,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Zakat</td><td>:</td><td style='text-align:right'>Rp. ".number_format(0,0,',','.')."</td ></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format(0,0,',','.')."</b></th></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format($rsOut->total,0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip_thr','', '', $thn, date('m') );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP THR STAFF ".$thn."-".$nik.".pdf";		
		
		//$this->ci_pdf->pdf_create($html, $path."/".$fileName, FALSE);
		$this->ci_pdf->pdf_create_my($html, $path."/".$fileName, 'a6','portrait', FALSE);
		
		//SIMPAN RECORD
		$strcek="select * from file_slip where nik='".$nik."' and thn='$thn'";
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
			$this->db->insert('file_slip', $data);
			
		}
		//$this->db->trans_commit();
		//$data['isi']=$path."/".$fileName;
		//echo json_encode($data);
		$this->db->trans_commit();
		return 1;
		

	}



}
