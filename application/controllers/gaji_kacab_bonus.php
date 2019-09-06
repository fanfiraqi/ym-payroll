<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gaji_kacab_bonus extends MY_App {

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
		$this->donasi_db=$this->load->database('donasi', TRUE);
		$this->auth->authorize();
	}

	public function index()
	{
		$this->template->set('pagetitle','Form Penggajian Bonus Kacab');
		$this->config->set_item('mySubMenu', 'mn34');
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrIntBln'] = $this->arrIntBln;
		$data['arrThn'] = $this->getYearArr();
		$data['thn'] = $this->getYearArr();
		$this->template->load('default','fgaji_kacab_bonus/filterForm',$data);
	}

	public function gaji_list(){
		$this->template->set('pagetitle','Daftar Gaji Bonus Kacab');		
		$bln=$this->input->post('cbBulan');		
		$thn=$this->input->post('cbTahun');
		$isXls=$this->input->post('isXls');	
			
		$strList = "SELECT p.*,  period_diff( date_format( now( ) , '%Y%m' ) , date_format( TGL_AKTIF, '%Y%m' ) ) SELISIH from pegawai p where  status_aktif=1 and p.id_jab  in (16, 17) order by id_cabang, nama";	
		
		//jenis = kacab_bonus
		$strCek="select * from gaji_validasi where jenis='kacab_bonus' and  tahun='$thn' and bulan='$bln' ";
		$sqlcek = $this->db->query($strCek);
		$rsCek = $sqlcek ->row();


		$data['cek']  = $this->db->query($strCek)->row();
		$data['row'] = $this->db->query($strList)->result();		
		$data['bln'] = $bln;		
		$data['thn'] = $thn;		
		$data['tahun'] = $thn;		
			
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
			$title="Daftar Data Gaji Bonus Kacab (NEW)";
			$sts="new";
			$view="gaji_kacab_bonus_list";
			$data['id_validasi']="";
		}else{	
			$data['id_validasi']=$rsCek->ID;
			$data['sts_validasi'] = $rsCek->VALIDASI;	
			$data['row'] = $this->db->query("select t.*, p.ID, p.NIK, p.NAMA, p.TGL_AKTIF, p.TGL_AWAL_KONTRAK, p.ID_JAB, p.ID_CABANG, p.REKENING from gaji_kacab_bonus t, pegawai p where p.nik=t.nik  and bln='".$bln."' and thn='".$thn."' and id_validasi=".$rsCek->ID)->result();	
			$tgl1=strtotime($thn_pre."-".$bln_pre."-06");
			$tgl2=strtotime($thn."-".$blnIdk[intval($bln)]."-05");
			$data['cekTgl']=$tgl1."#".$tgl2;
			if (strtotime(date('Y-m-d'))>=$tgl1 && strtotime(date('Y-m-d'))<=$tgl2){	//cek sysdate antara 26-bln-1 s.d 25-bln 
				//boleh edit, ambil data tabel thr
				$title="Daftar Data Gaji Bonus Kacab (OPEN)";
				$sts="edit";				
			}else{
				$title="Daftar Data Gaji Bonus Kacab (CLOSED)";
				$sts="disabled";
				
			}			
			$view="gaji_kacab_bonus_listed";
		}
		//view="coba";
		$data['sts']=$sts;
		if ($isXls==0){
			$this->template->set('pagetitle',$title." ".$bln." ".$thn);	
			$this->template->load('default','fgaji_kacab_bonus/'.$view,$data);
		}else{
			//$objPHPExcel = new PHPExcel();
			$html=$this->load->view('fgaji_kacab_bonus/gaji_kacab_bonus_excel' , $data, true);
			// Put the html into a temporary file
			$tmpfile = 'assets/excel/'.time().'.html';
			file_put_contents($tmpfile, $html);
		
			// Read the contents of the file into PHPExcel Reader class
			$reader = new PHPExcel_Reader_HTML; 			
			$content = $reader->load($tmpfile); 
			// Pass to writer and output as needed
			$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
			$objWriter->save('assets/excel/bonuskacab_'.$bln.$thn.'.xlsx');			
			
			$data['isi']='assets/excel/bonuskacab_'.$bln.$thn.'.xlsx';
			echo json_encode($data);


			// Delete temporary file
			unlink($tmpfile);
			
		}
	}


	public function save_gaji_kacab_bonus(){
		if($this->input->post()) {
			$this->load->library('form_validation');
			$rules = array();
			
			$sts=$this->input->post('sts');
			
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
									'JENIS' => 'kacab_bonus',
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
						
						$this->db->query("delete from gaji_kacab_bonus where id_validasi=".$id_validasi);		
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
									'MASA_KERJA_BLN' => $this->input->post('masakerja_'.$r),

									'TARGET_PENGAMBILAN' => $this->input->post('target_pengambilan_'.$r),
									'REAL_PENGAMBILAN' => $this->input->post('real_pengambilan_'.$r),									
									'ALL_DONASI' => $this->input->post('all_donasi_'.$r),
									'KACAB_INSI' => $this->input->post('kacab_insi_'.$r),									
									'KACAB_RUTIN' => $this->input->post('kacab_rutin_'.$r),
									'KACAB_WAKAF' => $this->input->post('kacab_wakaf_'.$r),
									'PERSEN_AMBIL' => $this->input->post('persen_ambil_'.$r),
									'TUNJAB' => $this->input->post('tunjab_'.$r),
									'BONUS_KACAB' => $this->input->post('bonus_kacab_'.$r),
									'TUNJ_PRESTASI' => $this->input->post('tunj_prestasi_'.$r),
									'PENYESUAIAN' => $this->input->post('penyesuaian_'.$r),
									'DANSOS' => $this->input->post('dansos_'.$r),
									'ZAKAT' => $this->input->post('zakat_'.$r),
									'LAIN' => $this->input->post('lain_'.$r),
									'TOTAL' => $this->input->post('totTerima_'.$r),
									'CREATED_BY' =>$this->session->userdata('auth')->id,
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>$this->session->userdata('auth')->id,
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);

								if ($this->db->insert('gaji_kacab_bonus', $data)){									
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
		
		//row GAJI 
		$strList="SELECT p.NIK, p.REKENING,  p.NAMA, gs.TOTAL 
			FROM `pegawai` p, gaji_kacab_bonus gs
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
		$path=$this->createPath('csv',$rsmaster->JENIS, "", $rsmaster->TAHUN, $rsmaster->BULAN );	//csv path hanya s.d thn masuk nama file
		$fileName="GAJI_BONUS_".strtoupper($rsmaster->JENIS)."_".$rsmaster->WILAYAH."_".$rsmaster->TAHUN.$rsmaster->BULAN.".csv";
		write_file($path."/".$fileName,$out);	

		//SIMPAN RECORD
		$strcek="select * from file_csv where jenis='".$rsmaster->JENIS."' and thn='".$rsmaster->TAHUN."' and bln='".$rsmaster->BULAN."' ";
		$cek=$this->db->query($strcek)->num_rows();
		$data = array(						
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
			FROM `gaji_kacab_bonus` gs, pegawai p
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
		$totpendapatan=$rsOut->TUNJAB+$rsOut->BONUS_KACAB+$rsOut->TUNJ_PRESTASI+$rsOut->PENYESUAIAN;
		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Tunjangan Jabatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->TUNJAB,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Bonus Kacab</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BONUS_KACAB,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Tunjangan Prestasi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->TUNJ_PRESTASI,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>Penyesuaian</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->PENYESUAIAN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($totpendapatan,0,',','.')."</b></th></tr>";

		$totPotongan=$rsOut->DANSOS+$rsOut->ZAKAT + $rsOut->LAIN;
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>DANSOS</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->DANSOS,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Zakat</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->ZAKAT,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Lain-lain</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->LAIN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format($totPotongan,0,',','.')."</b></th></tr>";

		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format(($totpendapatan-$totPotongan),0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip',$rsmaster->JENIS, "", $rsmaster->TAHUN, $rsmaster->BULAN  );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP_GAJI_BONUS_KACAB_".$rsOut->BLN.$rsOut->THN."-".$nik.".pdf";		
		
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
		
		$str = "select * from gaji_kacab_bonus where id_validasi=".$id_validasi." ORDER BY `NIK` ";
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
			FROM `gaji_kacab_bonus` gs, pegawai p
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
		$totpendapatan=$rsOut->TUNJAB+$rsOut->BONUS_KACAB+$rsOut->TUNJ_PRESTASI+$rsOut->PENYESUAIAN;
		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Tunjangan Jabatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->TUNJAB,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Bonus Kacab</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BONUS_KACAB,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Tunjangan Prestasi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->TUNJ_PRESTASI,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>Penyesuaian</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->PENYESUAIAN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($totpendapatan,0,',','.')."</b></th></tr>";

		$totPotongan=$rsOut->DANSOS+$rsOut->ZAKAT + $rsOut->LAIN;
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>DANSOS</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->DANSOS,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Zakat</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->ZAKAT,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>Lain-lain</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->LAIN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format($totPotongan,0,',','.')."</b></th></tr>";


		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format(($totpendapatan-$totPotongan),0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip',$rsmaster->JENIS, "", $rsmaster->TAHUN, $rsmaster->BULAN  );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP_GAJI_BONUS_KACAB_".$rsOut->BLN.$rsOut->THN."-".$nik.".pdf";		
		
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
