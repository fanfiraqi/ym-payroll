<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gaji_staf extends MY_App {

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
		$this->template->set('pagetitle','Form Penggajian Staff Dalam');
		$this->config->set_item('mySubMenu', 'mn31');
		$data['cabang'] = $this->common_model->comboCabang();
		$data['komponen'] = $this->db->query("select * from mst_komp_gaji where isactive=1")->result();
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrIntBln'] = $this->arrIntBln;
		$data['arrThn'] = $this->getYearArr();
		$data['divisi'] = array();
		$data['thn'] = $this->getYearArr();
		$this->template->load('default','fgaji_staff/filterForm',$data);
	}

	public function check_master(){
		$respon = new StdClass();
		if ($this->input->post('wilayah')=="Cabang"){
		$str="select distinct(id_cabang)  from pegawai where id_jab not in (35,36,103, 104, 6,7) and status_aktif=1";
		$sqlcabs=$this->db->query($str)->result();
		$pesan="";
		foreach ($sqlcabs as $row) {
			$sqlg=$this->db->query("select * from mst_grade_cabang where id_cabang=".$row->id_cabang);
			if ($sqlg->num_rows<=0){
				$pesan.="Cabang ".$row->id_cabang." belum disetting master grade cabang<br>";
			}else {
				$rsg=$sqlg->row();
				$sqlmg=$this->db->query("select * from mst_gapok where grade_cabang='".$rsg->grade."'");
				if ($sqlmg->num_rows<=0){
					$pesan.="Cabang ".$row->id_cabang." belum disetting master gaji pokok<br>";
				}
			}
		}
		
			if ($pesan!=""){
				$respon->status = 'error';
				$respon->pesan = $pesan;
			}else{
				$respon->status = 'success';
			}
		}else{
			$respon->status = 'success';
		}
		echo json_encode($respon);
	}
	public function gaji_list(){
		$this->template->set('pagetitle','Daftar GAJI Staff');		
		$isXls=$this->input->post('isXls');		
		$bln=$this->input->post('cbBulan');		
		$thn=$this->input->post('cbTahun');		
		$laz_tasharuf=$this->input->post('laz_tasharuf');
		$wilayah=$this->input->post('wilayah');
		// no zisco, dps, dokter
		$rsArrJabId=$this->gate_db->query("select id_jab from mst_jabatan where laz_tasharuf='".strtoupper($laz_tasharuf)."' and id_jab not in (35,36,37,103, 104, 6,7)")->result();
		$tags = array();
			foreach ($rsArrJabId as $row) {
				$tags[] =htmlspecialchars( $row->id_jab, ENT_NOQUOTES, 'UTF-8' );
			}
		$arIdJab= implode(',', $tags);
	
		$strList = "SELECT p.*,  period_diff( date_format( now( ) , '%Y%m' ) , date_format( tgl_aktif, '%Y%m' ) ) SELISIH from pegawai p where  status_aktif=1 and p.id_cabang".($wilayah=="Pusat"?" = 1 " : " <> 1 ")." and p.id_jab  in (".$arIdJab.") order by id_cabang, nama";
		
		//jenis = staff_laz, staff_tasharuf
		$strCek="select * from gaji_validasi where jenis='".$laz_tasharuf."' and  tahun='$thn' and bulan='$bln' and wilayah='$wilayah'";
		//get tunjangan anak

		$sqlcek = $this->db->query($strCek);
		$rsCek = $sqlcek ->row();
		$data['cek']  = $this->db->query($strCek)->row();
		$data['row'] = $this->db->query($strList)->result();		
		$data['bln'] = $bln;		
		$data['thn'] = $thn;		
		$data['tahun'] = $thn;		
		//$data['id_cabang'] = $id_cab;
		$data['laz_tasharuf'] = $laz_tasharuf;
		$data['wilayah'] = $wilayah;
			
		$data['str'] = $strList;
		$sts="";
		$blnStr=$this->arrBulan2;
		$blnIdk=$this->arrIntBln;
		if (intval($bln)==1){
			$bln_pre=12;
			$thn_pre=$thn-1;
		}else{
			$bln_pre=$blnIdk[intval($bln)-1];
			$thn_pre=$thn;
		}
		//$nmCabang = $this->gate_db->query("select KOTA from mst_cabang where id_cabang=".$this->input->post('id_cabang'))->row();
		if ($sqlcek->num_rows()<=0){
			$title="Daftar Data GAJI Karyawan/Staff ".$laz_tasharuf." ".$wilayah." (NEW)";
			$sts="new";
			$view="gaji_staff_list";
			$data['id_validasi']="";
		}else{	
			$data['sts_validasi'] = $rsCek->VALIDASI;
			$data['id_validasi']=$rsCek->ID;
			$data['row'] = $this->db->query("select t.*, p.NIK, p.NAMA, p.TGL_AKTIF, p.ID_JAB, p.ID_CABANG from gaji_staff t, pegawai p where p.nik=t.nik and t.id_cabang".($wilayah=="Pusat"?" = 1 " : " <> 1 ")." and bln='".$bln."' and thn='".$thn."' and id_validasi=".$rsCek->ID." order by p.ID_CABANG, NAMA")->result();	
			$tgl1=strtotime($thn_pre."-".$bln_pre."-21");
			$tgl2=strtotime($thn."-".$blnIdk[intval($bln)]."-20");
			$data['cekTgl']=$tgl1."#".$tgl2;
			if (strtotime(date('Y-m-d'))>=$tgl1 && strtotime(date('Y-m-d'))<=$tgl2){	//cek sysdate antara 26-bln-1 s.d 25-bln 
				//boleh edit, ambil data tabel thr
				$title="Daftar Data GAJI Karyawan/Staff ".$laz_tasharuf." ".$wilayah." (OPEN)";
				$sts="edit";				
			}else{
				$title="Daftar Data GAJI Karyawan/Staff ".$laz_tasharuf." ".$wilayah." (CLOSED)";
				$sts="disabled";
				
			}
			
			$view="gaji_staff_listed";
		}
		
		$data['sts']=$sts;
		if ($isXls==0){
			$this->template->set('pagetitle',$title." ".$bln." ".$thn);	
			$this->template->load('default','fgaji_staff/'.$view,$data);
		}else{
			//$objPHPExcel = new PHPExcel();
			$html=$this->load->view('fgaji_staff/gaji_excel' , $data, true);
			// Put the html into a temporary file
			$tmpfile = 'assets/excel/'.time().'.html';
			file_put_contents($tmpfile, $html);
		
			// Read the contents of the file into PHPExcel Reader class
			$reader = new PHPExcel_Reader_HTML; 
			//$reader = new PHPExcel; 
			$content = $reader->load($tmpfile); 
			// Pass to writer and output as needed
			$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
			$objWriter->save('assets/excel/staff_'.$bln.$thn.$laz_tasharuf.'.xlsx');
			
			/*$inputFileType = 'HTML';
			$inputFileName = $tmpfile;
			$outputFileType = 'Excel2007';
			$outputFileName = 'assets/excel/'.$bln.$thn.$laz_tasharuf.'.xlsx';

			$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objPHPExcelReader->load($tmpfile);

			$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,$outputFileType);
			$objPHPExcel = $objPHPExcelWriter->save($outputFileName);
			*/
			$data['isi']='assets/excel/staff_'.$bln.$thn.$laz_tasharuf.'.xlsx';
			echo json_encode($data);


			// Delete temporary file
			unlink($tmpfile);
			//$this->ci_pdf->pdf_create_report($html, $nmfile, 'a4', 'portrait');
		}
	}


	public function save_gaji_staff(){
		if($this->input->post()) {
			$this->load->library('form_validation');
			$rules = array();
			/*for($r=1;$r<=$this->input->post('jmlRow');$r++){	
				if ($this->input->post('flag_'.$r)=="1"){
					array_push($rules, array(
							'field' => 'nik_'.$r,
							'label' => 'nik_'.$r,
							'rules' => 'trim|xss_clean|required'));					
				}
			}*/
			$sts=$this->input->post('sts');
			//$this->form_validation->set_rules($rules);
			//$out=$this->form_validation->run();
			//$this->form_validation->set_message('required', 'Field %s harus diisi angka.');
			$respon = new StdClass();
			$xGets=" mulai";
			//if ($this->form_validation->run() == TRUE){
				$xGets.=" masuk run".$this->input->post('jmlRow');
				try {
					$this->db->trans_begin();
					$id_validasi="";
					if (trim($sts)=="new"){
						$dataMaster = array(
									'TAHUN' => $this->input->post('thn'),
									'BULAN' => $this->input->post('bln'),
									'JENIS' =>  $this->input->post('laz_tasharuf'),
									'WILAYAH' =>  $this->input->post('wilayah'),
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
						$xGets.=" flag ";
					

							$data = array(									
									'ID_VALIDASI' => $id_validasi,
									'BLN' => $this->input->post('bln'),
									'THN' => $this->input->post('thn'),
									'ID_CABANG' => $this->input->post('id_cabang_'.$r),
									'NIK' => $this->input->post('nik_'.$r),
									'ID_JAB' => $this->input->post('idjab_'.$r),
									'MASA_KERJA_BLN' => $this->input->post('masakerja_'.$r),
									'KELOMPOK_JAB' => $this->input->post('kelompok_gaji_'.$r),
									'JML_ALPA' => $this->input->post('jml_alpa_'.$r),
									'JML_CUTI' => $this->input->post('jml_cuti_'.$r),
									'JML_SAKIT' => $this->input->post('jml_sakit_'.$r),
									'JML_IJIN' => $this->input->post('jml_ijin_'.$r),
									'JML_HADIR' => $this->input->post('jml_hadir_'.$r),
									'JML_T10_1' => $this->input->post('T10_1_'.$r),
									'JML_T10_2' => $this->input->post('T10_2_'.$r),
									'JML_T10_3' => $this->input->post('T10_3_'.$r),
									'JML_LEMBUR' => $this->input->post('jam_lembur_'.$r),
									'PULANG_CEPAT_JML' => $this->input->post('PC_jml_'.$r),
									'PULANG_CEPAT_MNT' => $this->input->post('PC_mnt_'.$r),
									'GAPOK' => $this->input->post('komp_'.$r.'_0'),
									'ACUAN_UANG_MAKAN' => $this->input->post('acu_makan_'.$r),
									'U_MAKAN_DITERIMA' => $this->input->post('komp_'.$r.'_1'),									
									'I_KEHADIRAN' => $this->input->post('komp_'.$r.'_2'),
									'U_LEMBUR' => $this->input->post('komp_'.$r.'_3'),
									'T_KELUARGA' => $this->input->post('komp_'.$r.'_4'),
									'T_MASAKERJA' => $this->input->post('komp_'.$r.'_5'),
									'T_JABATAN' => $this->input->post('komp_'.$r.'_6'),
									'T_THT' => $this->input->post('komp_'.$r.'_7'),																	
									'BPJS_KESEHATAN' => $this->input->post('komp_'.$r.'_8'),
									'BPJS_NAKER' => $this->input->post('komp_'.$r.'_9'),
									'T_PENYESUAIAN' => $this->input->post('komp_'.$r.'_10'),	
									'POT_DANSOS' => $this->input->post('komp_'.$r.'_11'),
									'POT_THT' => $this->input->post('komp_'.$r.'_12'),
									'POT_LAIN' => $this->input->post('komp_'.$r.'_13'),
									'POT_FAMGATH' => $this->input->post('komp_'.$r.'_14'),
									'POT_QURBAN' => $this->input->post('komp_'.$r.'_15'),
									'JML_ANGSURAN' => $this->input->post('komp_'.$r.'_16'),
									'ANGSURAN_KE' => $this->input->post('cicilke_'.$r),
									'TOTAL' => $this->input->post('subGrandTotal_'.$r),
									'CREATED_BY' =>$this->session->userdata('auth')->id,
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>$this->session->userdata('auth')->id,
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);

								if ($this->db->insert('gaji_staff', $data)){									
									$this->db->trans_commit();
									$respon->status ="success";
								} else {
									throw new Exception("gagal simpan");
									$respon->status ="error";
									$respon->errormsg ="gagal simpan detil gaji";
								}
								
						}
					}

					$xGets.="<br>".$this->db->last_query();
					$xGets.="abis insert";
					
				} catch (Exception $e) {
					$xGets.=" error exception";
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$respon->errormsg ="err exception";
					$this->db->trans_rollback();
				}
				
				
			/*} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				$xGets.="error validasi";
				
			}*/
			$respon->xGets=$xGets;
			echo json_encode($respon);
			//echo json_encode($respon)."<br>".$xGets;
			exit;
		}

		$this->template->set('pagetitle','Saving...');

	}


	public function exportCsv(){		
		$id_validasi=$this->input->post('id_validasi');	

		$rsmaster=$this->db->query("select * from gaji_validasi where id=".$id_validasi)->row();
		
		//row GAJI staff
		$strList="SELECT p.NIK, p.REKENING,  p.NAMA, gs.TOTAL 
			FROM `pegawai` p, gaji_staff gs
			WHERE p.nik=gs.nik  and gs.id_validasi= ".$id_validasi;
		$rsRes=$this->db->query($strList)->result();
		$i=1;
		//clean nama
		$out="";
		foreach($rsRes as $row){
			$out.=$i.", ".strtoupper(preg_replace('/[^a-zA-Z0-9\s]/', '', $row->NAMA)).", ".$row->REKENING.",".$row->TOTAL."\r\n";
			$i++;
		}
		//cek dir
		$path=$this->createPath('csv',$rsmaster->JENIS, $rsmaster->WILAYAH, $rsmaster->TAHUN, $rsmaster->BULAN );	//csv path hanya s.d thn masuk nama file
		$fileName="GAJI_STAF_".strtoupper($rsmaster->JENIS)."_".$rsmaster->WILAYAH."_".$rsmaster->TAHUN.$rsmaster->BULAN.".csv";
		write_file($path."/".$fileName,$out);	

		//SIMPAN RECORD
		$strcek="select * from file_csv where jenis='".$rsmaster->JENIS."' and wilayah='".$rsmaster->WILAYAH."' and thn='".$rsmaster->TAHUN."' and bln='".$rsmaster->BULAN."' ";
		$cek=$this->db->query($strcek)->num_rows();
		if ($cek>0){
			$data = array('PATH' => $path,'NAMA_FILE' => $fileName, 'UPDATED_BY' =>$this->session->userdata('auth')->id, 'UPDATED_DATE' =>date('Y-m-d H:i:s')	);
			$this->db->where(array('jenis'=>$rsmaster->JENIS, "wilayah"=>$rsmaster->WILAYAH,  'thn'=>$rsmaster->TAHUN,  'bln'=>$rsmaster->BULAN))->update('file_csv', $data);
		}else{
			$data = array(
						'WILAYAH' => $rsmaster->WILAYAH,
						'THN' => $rsmaster->TAHUN,
						'BLN' => $rsmaster->BULAN,
						'JENIS' => $rsmaster->JENIS,
						'PATH' => $path,
						'NAMA_FILE' => $fileName,
						'CREATED_BY' =>$this->session->userdata('auth')->id,
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>$this->session->userdata('auth')->id,
						'UPDATED_DATE' =>date('Y-m-d H:i:s')
					);
			$this->db->insert('file_csv', $data);
			
		}
		$this->db->trans_commit();
		$data['isi']=$path."/".$fileName;
		echo json_encode($data);
	}

	

	public function cetak_slip(){
		$this->load->library('CI_Pdf');
		$param=$this->input->get('param');
		$arrBulan=$this->$arrBulan2;
		$arrKey=explode('_',$param);
		$id_validasi=$arrKey[0];
		$nik=$arrKey[1];
		$str="SELECT p.NIK,p.TGL_AKTIF, p.NAMA, p.ID_CABANG, p.ID_DIV,p.ID_JAB, gs. *
			FROM `gaji_staff` gs, pegawai p
			WHERE gs.nik = p.nik and gs.id_validasi='".$arrKey[0]."' and gs.nik='".$nik."'";		
		$rsOut=$this->db->query($str)->row();
		$rsmaster=$this->db->query("select * from gaji_validasi where id=".$id_validasi)->row();
		$rsMst=$this->gate_db->query("select NAMA_JAB from mst_jabatan where id_jab=".$rsOut->ID_JAB)->row();
		
		$data['str']=$str;		
		$data['id_validasi']=$arrKey[0];
		
		
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
		$html.="<tr><td colspan=2 style='text-align:center'><h2>SLIP GAJI KARYAWAN YATIM MANDIRI<br>".$arrBulan[$rsmaster->BULAN]." ".$rsmaster->TAHUN."</h2></td></tr>";
		$html.="<tr><tD colspan=2></td></tr>";
		$html.="<tr><tD >NAMA </td><tD> : ".$rsOut->NAMA."</td></tr>";
		$html.="<tr><tD >JABATAN</td><tD> : ".$rsMst->NAMA_JAB."</td></tr>";
		$html.="</table>";
		$totpendapatan=$rsOut->GAPOK+$rsOut->U_MAKAN_DITERIMA+$rsOut->I_KEHADIRAN+$rsOut->U_LEMBUR+$rsOut->T_KELUARGA+$rsOut->T_MASAKERJA+$rsOut->T_JABATAN+$rsOut->T_THT+$rsOut->BPJS_KESEHATAN+$rsOut->BPJS_NAKER+$rsOut->T_PENYESUAIAN;
		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Gaji Pokok</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->GAPOK,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Uang Makan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->U_MAKAN_DITERIMA,0,',','.')."</td ></tr>";
		$html.="<tr><td>3</td><td>Insentif Kehadiran</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->I_KEHADIRAN,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>Uang Lembur</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->U_LEMBUR,0,',','.')."</td></tr>";
		$html.="<tr><td>5</td><td>Tunjangan Keluarga</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_KELUARGA,0,',','.')."</td></tr>";
		$html.="<tr><td>6</td><td>Tunjangan Masa kerja</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_MASAKERJA,0,',','.')."</td></tr>";
		$html.="<tr><td>7</td><td>Tunjangan Jabatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_JABATAN,0,',','.')."</td></tr>";
		$html.="<tr><td>8</td><td>Tunjangan Hari Tua</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_THT,0,',','.')."</td></tr>";
		$html.="<tr><td>9</td><td>BPJS Kesehatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BPJS_KESEHATAN,0,',','.')."</td></tr>";
		$html.="<tr><td>10</td><td>BPJS Ketenagakerjaan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BPJS_NAKER,0,',','.')."</td></tr>";
		$html.="<tr><td>11</td><td>Tunjangan Penyesuaian</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_PENYESUAIAN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($totpendapatan,0,',','.')."</b></th></tr>";
		$totPotongan=$rsOut->POT_DANSOS+$rsOut->POT_THT+$rsOut->POT_FAMGATH+$rsOut->POT_QURBAN+$rsOut->JML_ANGSURAN+$rsOut->POT_LAIN;
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Dana Sosial</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_DANSOS,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Tunjangan hari Tua</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_THT,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Family gathering</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_FAMGATH,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>Iuran Qurban</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_QURBAN,0,',','.')."</td></tr>";
		$html.="<tr><td>5</td><td>Angsuran Pinjaman</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->JML_ANGSURAN,0,',','.')."</td></tr>";
		$html.="<tr><td>6</td><td>Lain-lain</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_LAIN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format($totPotongan,0,',','.')."</b></th></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format(($totpendapatan-$totPotongan),0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip',$rsmaster->JENIS, $rsmaster->WILAYAH, $rsmaster->TAHUN, $rsmaster->BULAN  );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP GAJI STAFF_".$rsOut->BLN.$rsOut->THN."-".$nik.".pdf";		
		
		//$this->ci_pdf->pdf_create($html, $path."/".$fileName, FALSE);
		$this->ci_pdf->pdf_create_my($html, $path."/".$fileName, 'a6','portrait', FALSE);
		
		//SIMPAN RECORD
		$strcek="select * from file_slip where nik='".$nik."' and jenis='".$rsmaster->JENIS."' and wilayah='".$rsmaster->WILAYAH."' and thn='".$rsmaster->TAHUN."' and bln='".$rsmaster->BULAN."' ";
		$cek=$this->db->query($strcek)->num_rows();
		$data = array(
						'NIK' => $nik,
						'WILAYAH' => $rsmaster->WILAYAH,
						'THN' => $rsmaster->TAHUN,
						'BLN' => $rsmaster->BULAN,
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
			$this->db->where(array('id'=>$rs->ID))->update('file_slip', $data);
		}else{
			
			$this->db->insert('file_slip', $data);
			
		}
		
		$this->db->trans_commit();
		$data['isi']=$path."/".$fileName;
		echo json_encode($data);

	}

	


	function slipLoop(){
		//slipLoop param : alamat email, path file slip
		$id_validasi=$this->input->get('id_validasi');
		
		$str = "select * from gaji_staff where id_validasi=".$id_validasi." ORDER BY `NIK` ";
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
				$param=$id_validasi."_".$result->NIK;
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
		$arrBulan=$this->arrBulan2;		
		$arrKey=explode('_',$param);		
		$id_validasi=$arrKey[0];
		$nik=$arrKey[1];
		$str="SELECT p.NIK,p.TGL_AKTIF, p.NAMA, p.ID_CABANG, p.ID_DIV,p.ID_JAB, gs. *
			FROM `gaji_staff` gs, pegawai p
			WHERE gs.nik = p.nik and gs.id_validasi='".$arrKey[0]."' and gs.nik='".$nik."'";		
		$rsOut=$this->db->query($str)->row();
		$rsmaster=$this->db->query("select * from gaji_validasi where id=".$id_validasi)->row();
		$rsMst=$this->gate_db->query("select NAMA_JAB from mst_jabatan where id_jab=".$rsOut->ID_JAB)->row();
		
		$data['str']=$str;		
		$data['id_validasi']=$arrKey[0];
		
		
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
		$html.="<tr><td colspan=2 style='text-align:center'><h2>SLIP GAJI KARYAWAN YATIM MANDIRI<br>".$arrBulan[$rsmaster->BULAN]." ".$rsmaster->TAHUN."</h2></td></tr>";
		$html.="<tr><tD colspan=2></td></tr>";
		$html.="<tr><tD >NAMA </td><tD> : ".$rsOut->NAMA."</td></tr>";
		$html.="<tr><tD >JABATAN</td><tD> : ".$rsMst->NAMA_JAB."</td></tr>";
		$html.="</table>";
		$totpendapatan=$rsOut->GAPOK+$rsOut->U_MAKAN_DITERIMA+$rsOut->I_KEHADIRAN+$rsOut->U_LEMBUR+$rsOut->T_KELUARGA+$rsOut->T_MASAKERJA+$rsOut->T_JABATAN+$rsOut->T_THT+$rsOut->BPJS_KESEHATAN+$rsOut->BPJS_NAKER+$rsOut->T_PENYESUAIAN;
		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Gaji Pokok</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->GAPOK,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Uang Makan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->U_MAKAN_DITERIMA,0,',','.')."</td ></tr>";
		$html.="<tr><td>3</td><td>Insentif Kehadiran</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->I_KEHADIRAN,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>Uang Lembur</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->U_LEMBUR,0,',','.')."</td></tr>";
		$html.="<tr><td>5</td><td>Tunjangan Keluarga</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_KELUARGA,0,',','.')."</td></tr>";
		$html.="<tr><td>6</td><td>Tunjangan Masa kerja</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_MASAKERJA,0,',','.')."</td></tr>";
		$html.="<tr><td>7</td><td>Tunjangan Jabatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_JABATAN,0,',','.')."</td></tr>";
		$html.="<tr><td>8</td><td>Tunjangan Hari Tua</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_THT,0,',','.')."</td></tr>";
		$html.="<tr><td>9</td><td>BPJS Kesehatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BPJS_KESEHATAN,0,',','.')."</td></tr>";
		$html.="<tr><td>10</td><td>BPJS Ketenagakerjaan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BPJS_NAKER,0,',','.')."</td></tr>";
		$html.="<tr><td>11</td><td>Tunjangan Penyesuaian</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_PENYESUAIAN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($totpendapatan,0,',','.')."</b></th></tr>";
		$totPotongan=$rsOut->POT_DANSOS+$rsOut->POT_THT+$rsOut->POT_FAMGATH+$rsOut->POT_QURBAN+$rsOut->JML_ANGSURAN+$rsOut->POT_LAIN;
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Dana Sosial</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_DANSOS,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Tunjangan hari Tua</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_THT,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Family gathering</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_FAMGATH,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>Iuran Qurban</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_QURBAN,0,',','.')."</td></tr>";
		$html.="<tr><td>5</td><td>Angsuran Pinjaman</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->JML_ANGSURAN,0,',','.')."</td></tr>";
		$html.="<tr><td>6</td><td>Lain-lain</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_LAIN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format($totPotongan,0,',','.')."</b></th></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format(($totpendapatan-$totPotongan),0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip',$rsmaster->JENIS, $rsmaster->WILAYAH, $rsmaster->TAHUN, $rsmaster->BULAN  );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP GAJI STAFF_".$rsOut->BLN.$rsOut->THN."-".$nik.".pdf";		
		
		//$this->ci_pdf->pdf_create($html, $path."/".$fileName, FALSE);
		$this->ci_pdf->pdf_create_my($html, $path."/".$fileName, 'a5','portrait', FALSE);
		
		//SIMPAN RECORD
		$strcek="select * from file_slip where nik='".$nik."' and jenis='".$rsmaster->JENIS."' and wilayah='".$rsmaster->WILAYAH."' and thn='".$rsmaster->TAHUN."' and bln='".$rsmaster->BULAN."' ";
		$cek=$this->db->query($strcek)->num_rows();
		$data = array(
						'NIK' => $nik,
						'WILAYAH' => $rsmaster->WILAYAH,
						'THN' => $rsmaster->TAHUN,
						'BLN' => $rsmaster->BULAN,
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
			$this->db->where(array('id'=>$rs->ID))->update('file_slip', $data);
		}else{
			
			$this->db->insert('file_slip', $data);
			
		}
		//$this->db->trans_commit();
		//$data['isi']=$path."/".$fileName;
		//echo json_encode($data);
		$this->db->trans_commit();
		return 1;
		

	}

}
