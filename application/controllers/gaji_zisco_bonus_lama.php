<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gaji_zisco_bonus extends MY_App {

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
		 $this->donasi_db=$this->load->database('donasi', TRUE);
	}

	public function index()
	{
		$this->template->set('pagetitle','Form Penggajian Bonus Zisco');
		$this->config->set_item('mySubMenu', 'mn33');
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrIntBln'] = $this->arrIntBln;
		$data['arrThn'] = $this->getYearArr();
		$data['thn'] = $this->getYearArr();
		$data['cabang'] = $this->common_model->comboCabang();
		$this->template->load('default','fgaji_zisco_bonus/filterForm',$data);
	}

	public function gaji_list(){
		$this->template->set('pagetitle','Daftar Gaji Bonus Zisco');		
		$bln=$this->input->post('cbBulan');		
		$thn=$this->input->post('cbTahun');
		$id_cabang=$this->input->post('id_cabang');
		$isXls=$this->input->post('isXls');	
		
		// no zisco, dps, dokter
		$rsArrJabId=$this->gate_db->query("select id_jab from mst_jabatan where id_jab in (35,36)")->result();
			
		$strList = "SELECT p.*,  period_diff( date_format( now( ) , '%Y%m' ) , date_format( TGL_AWAL_KONTRAK, '%Y%m' ) ) SELISIH from pegawai p where  p.id_cabang=".$id_cabang." and status_aktif=1 and p.id_jab  in (35,36) order by id_cabang, nama";		//zisco tgl_awal kontrak tabel pegawai ?
		
		//jenis = zisco_Bonus
		$strCek="select * from gaji_validasi where jenis='zisco_bonus' and wilayah='".$id_cabang."' and tahun='$thn' and bulan='$bln' ";
		$sqlcek = $this->db->query($strCek);
		$rsCek = $sqlcek ->row();


		$data['cek']  = $this->db->query($strCek)->row();
		$data['row'] = $this->db->query($strList)->result();		
		$data['bln'] = $bln;		
		$data['thn'] = $thn;		
		$data['tahun'] = $thn;		
		$data['id_cabang'] = $id_cabang;		
			
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

		if ($sqlcek->num_rows()<=0){
			$title="Daftar Data Gaji Bonus Zisco (NEW)";
			$sts="new";
			$view="gaji_zisco_bonus_list";
			$data['id_validasi']="";
		}else{	
			$data['id_validasi']=$rsCek->ID;
			$data['sts_validasi'] = $rsCek->VALIDASI;	
			$data['row'] = $this->db->query("select t.*,p.ID, p.NIK, p.NAMA,p.STATUS_PEGAWAI, p.TGL_AKTIF, p.TGL_AWAL_KONTRAK, p.ID_JAB, p.ID_CABANG from gaji_zisco_bonus t, pegawai p where p.nik=t.nik  and p.id_cabang=".$id_cabang." and bln='".$bln."' and thn='".$thn."' and id_validasi=".$rsCek->ID." order by p.nama")->result();	
			$tgl1=strtotime($thn_pre."-".$bln_pre."-06");
			$tgl2=strtotime($thn."-".$blnIdk[intval($bln)]."-05");
			$data['cekTgl']=$tgl1."#".$tgl2;
			if (strtotime(date('Y-m-d'))>=$tgl1 && strtotime(date('Y-m-d'))<=$tgl2){	//cek sysdate antara 26-bln-1 s.d 25-bln 
				//boleh edit, ambil data tabel thr
				$title="Daftar Data Gaji Bonus Zisco (OPEN)";
				$sts="edit";				
			}else{
				$title="Daftar Data Gaji Bonus Zisco (CLOSED)";
				$sts="disabled";
				
			}			
			$view="gaji_zisco_bonus_listed";
		}
		//view="coba";
		$data['sts']=$sts;
		if ($isXls==0){
			$this->template->set('pagetitle',$title." ".$bln." ".$thn);	
			$this->template->load('default','fgaji_zisco_bonus/'.$view,$data);
		}else{
			//$objPHPExcel = new PHPExcel();
			$html=$this->load->view('fgaji_zisco_bonus/gaji_zisco_bonus_excel' , $data, true);
			// Put the html into a temporary file
			$tmpfile = 'assets/excel/'.time().'.html';
			file_put_contents($tmpfile, $html);
		
			// Read the contents of the file into PHPExcel Reader class
			$reader = new PHPExcel_Reader_HTML; 			
			$content = $reader->load($tmpfile); 
			// Pass to writer and output as needed
			$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
			$objWriter->save('assets/excel/bonuszisco_'.$id_cabang.'_'.$bln.$thn.'.xlsx');			
			
			$data['isi']='assets/excel/bonuszisco_'.$id_cabang.'_'.$bln.$thn.'.xlsx';
			echo json_encode($data);


			// Delete temporary file
			unlink($tmpfile);
			
		}
	}


	public function save_gaji_zisco_bonus(){
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
									'JENIS' => 'zisco_bonus',
									'WILAYAH' =>  $this->input->post('id_cabang'),
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
						$this->db->query("delete from gaji_zisco_bonus where id_validasi=".$id_validasi." and id_cabang=".$this->input->post('id_cabang'));		
						$this->db->trans_commit();
					}

					for($r=1;$r<=$this->input->post('jmlRow');$r++){
						//if ($this->input->post('flag_'.$r)=="1"){
						//$xGets.=" flag ";
					

							$data = array(									
									'ID_VALIDASI' => $id_validasi,
									'BLN' => $this->input->post('bln'),
									'THN' => $this->input->post('thn'),
									'ID_CABANG' => $this->input->post('id_cabang'),
									'NIK' => $this->input->post('nik_'.$r),
									'ID_JAB' => $this->input->post('idjab_'.$r),

									'DONASI_RUTIN_MUNDUR' => $this->input->post('zisco_mundur_'.$r),
									'RUTIN_INFAQ_TARGET' => $this->input->post('rutin_infaq_target_'.$r),
									'RUTIN_INFAQ_REALISASI' => $this->input->post('rutin_infaq_realisasi_'.$r),									
									'RUTIN_ZAKAT_TARGET' => $this->input->post('rutin_zakat_target_'.$r),
									'RUTIN_ZAKAT_REALISASI' => $this->input->post('rutin_zakat_realisasi_'.$r),									
									'RUTIN_INFAQ_PENGEMBANGAN' => $this->input->post('rutin_infaq_selisih_'.$r),
									'RUTIN_ZAKAT_PENGEMBANGAN' => $this->input->post('rutin_zakat_selisih_'.$r),
									'INSI_INFAQ' => $this->input->post('insi_infaq_paid_'.$r),
									'INSI_ZAKAT_MAL' => $this->input->post('insi_zakat_paid_'.$r),
									'INSI_ZAKAT_FITHRAH' => $this->input->post('insi_zakat_fitrah_'.$r),
									'WAKAF_ICMB' => $this->input->post('wakaf_icmb_'.$r),
									'WAKAF_STAINIM' => $this->input->post('wakaf_stainim_'.$r),
									'WAKAF_MASJID' => $this->input->post('wakaf_masjid_'.$r),
									'NON_WAKAF_QURAN' => $this->input->post('non_wakaf_quran_'.$r),
									'NON_WAKAF_QURBAN' => $this->input->post('non_wakaf_qurban_'.$r),
									'NON_WAKAF_BENCANA' => $this->input->post('non_wakaf_bencana_'.$r),
									'NON_WAKAF_FIDYAH' => $this->input->post('non_wakaf_fidyah_'.$r),
									'ACUAN_TRANSPORT' => $this->input->post('acuan_transport_'.$r),
									'TUNJ_TRANSPORT' => $this->input->post('tunj_transport_'.$r),
									'TUNJ_PENGAMBILAN' => $this->input->post('tunj_pengambilan_'.$r),
									'INSENTIF_PENGAMBILAN' => $this->input->post('insentif_pengambilan_'.$r),
									'BONUS_PRESTASI' => $this->input->post('bonus_prestasi_'.$r),
									'BONUS_PENGEMBANGAN' => $this->input->post('bonus_pengembangan_'.$r),
									'INSI_ZAKAT_WAKAF_TUNAI' => $this->input->post('insi_zakat_wakaf_tunai_'.$r),
									'BONUS_PATUNGAN_SAPI' => $this->input->post('bonus_patungan_sapi_'.$r),
									'BONUS_QURBAN_SAPI' => $this->input->post('bonus_qurban_sapi_'.$r),
									'KOREKSI' => $this->input->post('koreksi_'.$r),
									'PENYESUAIAN' => $this->input->post('penyesuaian_'.$r),
									'PENGEMBALIAN_40' => $this->input->post('pengembalian_40_'.$r),
									'TUNJ_JABATAN' => $this->input->post('tunj_jabatan_'.$r),
									'TUNJ_PRESTASI' => $this->input->post('tunj_prestasi_'.$r),
									'POT_DANSOS' => $this->input->post('pot_dansos_'.$r),
									'POT_ZAKAT' => $this->input->post('pot_zakat_'.$r),
									'LAIN_LAIN' => $this->input->post('lain_lain_'.$r),

									'TOTAL_BONUS' => $this->input->post('payroll_bonus_'.$r),
									'TOTAL_POTONGAN' => $this->input->post('pot_payroll_bonus_'.$r),
									'TOTAL' => $this->input->post('total_payroll_bonus_'.$r),
									'CREATED_BY' =>$this->session->userdata('auth')->id,
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>$this->session->userdata('auth')->id,
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);

								if ($this->db->insert('gaji_zisco_bonus', $data)){									
									$this->db->trans_commit();
									$respon->status ="success";
								} else {
									throw new Exception("gagal simpan");
									$respon->status ="error";
									$respon->errormsg ="gagal simpan detil gaji";
								}
								
						//}
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
		
		//row GAJI 
		$strList="SELECT p.NIK, p.REKENING,  p.NAMA, gs.TOTAL 
			FROM `pegawai` p, gaji_zisco_bonus gs
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
		$fileName="GAJI_BONUS_".strtoupper($rsmaster->JENIS)."_".$rsmaster->WILAYAH."_".$rsmaster->TAHUN.$rsmaster->BULAN.".csv";
		write_file($path."/".$fileName,$out);	

		//SIMPAN RECORD
		$strcek="select * from file_csv where jenis='".$rsmaster->JENIS."' and thn='".$rsmaster->TAHUN."' and bln='".$rsmaster->BULAN."' ";
		$cek=$this->db->query($strcek)->num_rows();
		$data = array(						
						'THN' => $rsmaster->TAHUN,
						'BLN' => $rsmaster->BULAN,
						'JENIS' => $rsmaster->JENIS,
						'WILAYAH' => $rsmaster->WILAYAH,
						'PATH' => $path,
						'NAMA_FILE' => $fileName,
						'CREATED_BY' =>$this->session->userdata('auth')->id,
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>$this->session->userdata('auth')->id,
						'UPDATED_DATE' =>date('Y-m-d H:i:s')
					);
		if ($cek>0){
			//$data = array('PATH' => $path,'NAMA_FILE' => $fileName, 'UPDATED_BY' =>$this->session->userdata('auth')->id, 'UPDATED_DATE' =>date('Y-m-d H:i:s')	);
			$this->db->where(array('id'=>$rsmaster->ID))->update('file_csv', $data);
		}else{
			
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
			FROM `gaji_zisco_bonus` gs, pegawai p
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

		$totpendapatan=$rsOut->TUNJ_PENGAMBILAN+$rsOut->INSENTIF_PENGAMBILAN+$rsOut->BONUS_PRESTASI+$rsOut->BONUS_PENGEMBANGAN+$rsOut->INSI_ZAKAT_WAKAF_TUNAI
			+$rsOut->BONUS_PATUNGAN_SAPI+$rsOut->BONUS_QURBAN_SAPI +$rsOut->KOREKSI +$rsOut->PENYESUAIAN +$rsOut->PENGEMBALIAN_40 +$rsOut->TUNJ_JABATAN+$rsOut->TUNJ_PRESTASI; 

		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Tunjangan Pengambilan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->TUNJ_PENGAMBILAN,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Insentif Pengambilan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->INSENTIF_PENGAMBILAN,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Bonus Prestasi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BONUS_PRESTASI,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>Bonus Pengembangan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BONUS_PENGEMBANGAN,0,',','.')."</td></tr>";
		$html.="<tr><td>5</td><td>Bonus Insidentil, Zakat, Wakaf tunai</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->INSI_ZAKAT_WAKAF_TUNAI,0,',','.')."</td></tr>";
		$html.="<tr><td>6</td><td>Bonus Patungan Sapi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BONUS_PATUNGAN_SAPI,0,',','.')."</td></tr>";
		$html.="<tr><td>7</td><td>Bonus Qurban Sapi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BONUS_QURBAN_SAPI,0,',','.')."</td></tr>";
		$html.="<tr><td>8</td><td>Koreksi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->KOREKSI,0,',','.')."</td></tr>";
		$html.="<tr><td>9</td><td>Penyesuaian</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->PENYESUAIAN,0,',','.')."</td></tr>";
		$html.="<tr><td>10</td><td>Pengembalian 40%</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->PENGEMBALIAN_40,0,',','.')."</td></tr>";
		$html.="<tr><td>11</td><td>Tunjangan Jabatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->TUNJ_JABATAN,0,',','.')."</td></tr>";
		$html.="<tr><td>12</td><td>Tunjangan Prestasi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->TUNJ_PRESTASI,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($totpendapatan,0,',','.')."</b></th></tr>";

		$totPotongan=$rsOut->POT_DANSOS+$rsOut->POT_ZAKAT+$rsOut->ANGSURAN+$rsOut->LAIN_LAIN;
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Dana Sosial</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_DANSOS,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Zakat</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_ZAKAT,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Angsuran Pinjaman</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->ANGSURAN,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>Lain-lain</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->LAIN_LAIN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format($totPotongan,0,',','.')."</b></th></tr>";

		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format(($totpendapatan-$totPotongan),0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip',$rsmaster->JENIS, $rsmaster->WILAYAH, $rsmaster->TAHUN, $rsmaster->BULAN  );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP_GAJI_BONUS_ZISCO_".$rsOut->BLN.$rsOut->THN."-".$nik.".pdf";		
		
		//$this->ci_pdf->pdf_create($html, $path."/".$fileName, FALSE);
		$this->ci_pdf->pdf_create_my($html, $path."/".$fileName, 'a6','portrait', FALSE);
		
		//SIMPAN RECORD
		$strcek="select * from file_slip where nik='".$nik."' and jenis='".$rsmaster->JENIS."'  and thn='".$rsmaster->TAHUN."' and bln='".$rsmaster->BULAN."' ";
		$cek=$this->db->query($strcek)->num_rows();
		$data = array(
						'NIK' => $nik,						
						'THN' => $rsmaster->TAHUN,
						'BLN' => $rsmaster->BULAN,
						'JENIS' => $rsmaster->JENIS,				
						'WILAYAH' => $rsmaster->WILAYAH,				
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
		
		$str = "select * from gaji_zisco_bonus where id_validasi=".$id_validasi." ORDER BY `NIK` ";
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
			FROM `gaji_zisco_bonus` gs, pegawai p
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
		$totpendapatan=$rsOut->TUNJ_PENGAMBILAN+$rsOut->INSENTIF_PENGAMBILAN+$rsOut->BONUS_PRESTASI+$rsOut->BONUS_PENGEMBANGAN+$rsOut->INSI_ZAKAT_WAKAF_TUNAI
			+$rsOut->BONUS_PATUNGAN_SAPI+$rsOut->BONUS_QURBAN_SAPI +$rsOut->KOREKSI +$rsOut->PENYESUAIAN +$rsOut->PENGEMBALIAN_40 +$rsOut->TUNJ_JABATAN+$rsOut->TUNJ_PRESTASI; 

		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Tunjangan Pengambilan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->TUNJ_PENGAMBILAN,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Insentif Pengambilan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->INSENTIF_PENGAMBILAN,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Bonus Prestasi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BONUS_PRESTASI,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>Bonus Pengembangan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BONUS_PENGEMBANGAN,0,',','.')."</td></tr>";
		$html.="<tr><td>5</td><td>Bonus Insidentil, Zakat, Wakaf tunai</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->INSI_ZAKAT_WAKAF_TUNAI,0,',','.')."</td></tr>";
		$html.="<tr><td>6</td><td>Bonus Patungan Sapi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BONUS_PATUNGAN_SAPI,0,',','.')."</td></tr>";
		$html.="<tr><td>7</td><td>Bonus Qurban Sapi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BONUS_QURBAN_SAPI,0,',','.')."</td></tr>";
		$html.="<tr><td>8</td><td>Koreksi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->KOREKSI,0,',','.')."</td></tr>";
		$html.="<tr><td>9</td><td>Penyesuaian</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->PENYESUAIAN,0,',','.')."</td></tr>";
		$html.="<tr><td>10</td><td>Pengembalian 40%</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->PENGEMBALIAN_40,0,',','.')."</td></tr>";
		$html.="<tr><td>11</td><td>Tunjangan Jabatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->TUNJ_JABATAN,0,',','.')."</td></tr>";
		$html.="<tr><td>12</td><td>Tunjangan Prestasi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->TUNJ_PRESTASI,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($totpendapatan,0,',','.')."</b></th></tr>";

		$totPotongan=$rsOut->POT_DANSOS+$rsOut->POT_ZAKAT+$rsOut->ANGSURAN+$rsOut->LAIN_LAIN;
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Dana Sosial</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_DANSOS,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Zakat</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_ZAKAT,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Angsuran Pinjaman</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->ANGSURAN,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>Lain-lain</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->LAIN_LAIN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format($totPotongan,0,',','.')."</b></th></tr>";

		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format(($totpendapatan-$totPotongan),0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip',$rsmaster->JENIS, $rsmaster->WILAYAH, $rsmaster->TAHUN, $rsmaster->BULAN  );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP_GAJI_BONUS_ZISCO_".$rsOut->BLN.$rsOut->THN."-".$nik.".pdf";		
		
		//$this->ci_pdf->pdf_create($html, $path."/".$fileName, FALSE);
		$this->ci_pdf->pdf_create_my($html, $path."/".$fileName, 'a5','portrait', FALSE);
		
		//SIMPAN RECORD
		$strcek="select * from file_slip where nik='".$nik."' and jenis='".$rsmaster->JENIS."'  and thn='".$rsmaster->TAHUN."' and bln='".$rsmaster->BULAN."' ";
		$cek=$this->db->query($strcek)->num_rows();
		$data = array(
						'NIK' => $nik,						
						'THN' => $rsmaster->TAHUN,
						'BLN' => $rsmaster->BULAN,
						'JENIS' => $rsmaster->JENIS,
						'WILAYAH' => $rsmaster->WILAYAH,				
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
	public function validasi(){
		$id = $this->input->post('id_validasi');	//id as nik
		
		$str="update gaji_validasi set VALIDASI=1 where id='".$id."'";

		if($this->db->query($str)){
			$respon['status'] = 'success';
			$respon['data'] = "OK";			
		} else {			
			$respon['status'] = 'error';
			$respon['errormsg'] = 'Invalid Data';			
		}
		echo json_encode($respon);
	}
}
