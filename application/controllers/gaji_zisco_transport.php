<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gaji_zisco_transport extends MY_App {

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
		$this->template->set('pagetitle','Form Penggajian Transport Zisco');
		$this->config->set_item('mySubMenu', 'mn32');
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrIntBln'] = $this->arrIntBln;
		$data['arrThn'] = $this->getYearArr();
		$data['thn'] = $this->getYearArr();
		$this->template->load('default','fgaji_zisco_transport/filterForm',$data);
	}

	public function gaji_list(){
		$this->config->set_item('mySubMenu', 'mn32');
		$this->template->set('pagetitle','Daftar Gaji Tranport Zisco');		
		$bln=$this->input->post('cbBulan');		
		$thn=$this->input->post('cbTahun');
		$isXls=$this->input->post('isXls');	
		
		// zisco, spv zisco, no relawan
		$rsArrJabId=$this->gate_db->query("select id_jab from mst_jabatan where id_jab in (35,36)")->result();
			
		$strList = "SELECT p.*,  period_diff( date_format( now( ) , '%Y%m' ) , date_format( TGL_AWAL_KONTRAK, '%Y%m' ) ) SELISIH from pegawai p where  status_aktif=1 and p.id_jab  in (35,36) order by id_cabang, nama";		//zisco tgl_awal kontrak tabel pegawai ?
		
		//jenis = zisco_transport
		$strCek="select * from gaji_validasi where jenis='zisco_transport' and  tahun='$thn' and bulan='$bln'  ";
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
        
       // if ($rsRAZ_Cek->jml > 0){
            
        
		if ($sqlcek->num_rows()<=0) {
			$title="Daftar Data Gaji Transport Zisco (NEW)";
			$sts="new";
			$view="gaji_zisco_transport_list";
			$data['id_validasi']="";
			$data['strCek']=$strCek;
		}else{	
			$data['sts_validasi'] = $rsCek->VALIDASI;
			$data['id_validasi']=$rsCek->ID;
			$data['row'] = $this->db->query("select t.*, p.NIK, p.NAMA, p.TGL_AKTIF, p.TGL_AWAL_KONTRAK, p.ID_JAB, p.ID_CABANG from gaji_zisco_transport t, pegawai p where p.nik=t.nik  and bln='".$bln."' and thn='".$thn."' and id_validasi=".$rsCek->ID." order by p.id_cabang, p.nama")->result();	
			$tgl1=strtotime($thn_pre."-".$bln_pre."-26");
			$tgl2=strtotime($thn."-".$blnIdk[intval($bln)]."-25");
			$data['cekTgl']=$tgl1."#".$tgl2;
			if (strtotime(date('Y-m-d'))>=$tgl1 && strtotime(date('Y-m-d'))<=$tgl2){	//cek sysdate antara 26-bln-1 s.d 25-bln 
				//boleh edit, ambil data tabel thr
				$title="Daftar Data Gaji Transport Zisco (OPEN)";
				$sts="edit";				
			}else{
				$title="Daftar Data Gaji Transport Zisco (CLOSED)";
				$sts="disabled";
				
			}			
			$view="gaji_zisco_transport_listed";
		}
		
		$data['sts']=$sts;
		if ($isXls==0){
			$this->template->set('pagetitle',$title." ".$bln." ".$thn);	
			$this->template->load('default','fgaji_zisco_transport/'.$view,$data);
		}else{
			//$objPHPExcel = new PHPExcel();
			$html=$this->load->view('fgaji_zisco_transport/gaji_zisco_transport_excel' , $data, true);
			// Put the html into a temporary file
			$tmpfile = 'assets/excel/'.time().'.html';
			file_put_contents($tmpfile, $html);
		
			// Read the contents of the file into PHPExcel Reader class
			$reader = new PHPExcel_Reader_HTML; 			
			$content = $reader->load($tmpfile); 
			// Pass to writer and output as needed
			$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
			$objWriter->save('assets/excel/transport_'.$bln.$thn.'.xlsx');			
			
			$data['isi']='assets/excel/transport_'.$bln.$thn.'.xlsx';
			echo json_encode($data);


			// Delete temporary file
			unlink($tmpfile);
			
		}
	}


	public function save_gaji_zisco_transport(){
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
									'JENIS' => 'zisco_transport',
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
						//$id_validasi=5;
						$this->db->query("delete from gaji_zisco_transport where id_validasi=".$id_validasi);		
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
									'STATUS_PEGAWAI' => $this->input->post('stspeg_'.$r),
									'JML_IJIN' => $this->input->post('jml_tdkmasuk_'.$r),									
									'JML_ALPA' => $this->input->post('jml_alpa_'.$r),									
									'ACUAN_TRANSPORT' => $this->input->post('acu_transport_'.$r),
									'U_TRANS_DITERIMA' => $this->input->post('komp_'.$r.'_0'),									
									'T_JABATAN' => $this->input->post('komp_'.$r.'_1'),
									'BPJS_KESEHATAN' => $this->input->post('komp_'.$r.'_2'),
									'BPJS_NAKER' => $this->input->post('komp_'.$r.'_3'),
									'T_PENYESUAIAN' => $this->input->post('komp_'.$r.'_4'),
									'SERVIS_MOTOR' => $this->input->post('komp_'.$r.'_5'),
									'KOREKSI' => $this->input->post('komp_'.$r.'_6'),
									'POT_LAIN' => $this->input->post('komp_'.$r.'_7'),
									'JML_ANGSURAN' => $this->input->post('komp_'.$r.'_8'),
									'ANGSURAN_KE' => $this->input->post('cicilke_'.$r),
									'TOTAL' => $this->input->post('subGrandTotal_'.$r),
									'CREATED_BY' =>$this->session->userdata('auth')->id,
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>$this->session->userdata('auth')->id,
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);

								if ($this->db->insert('gaji_zisco_transport', $data)){									
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
			FROM `pegawai` p, gaji_zisco_transport gs
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
		$fileName="GAJI_TRANSPORT_".strtoupper($rsmaster->JENIS)."_".$rsmaster->WILAYAH."_".$rsmaster->TAHUN.$rsmaster->BULAN.".csv";
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
			FROM `gaji_zisco_transport` gs, pegawai p
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
		$totpendapatan=$rsOut->U_TRANS_DITERIMA+$rsOut->T_JABATAN+$rsOut->BPJS_KESEHATAN+$rsOut->BPJS_NAKER+$rsOut->T_PENYESUAIAN+$rsOut->SERVIS_MOTOR+$rsOut->KOREKSI;
		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Uang Transport</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->U_TRANS_DITERIMA,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Tunjangan Jabatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_JABATAN,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>BPJS Kesehatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BPJS_KESEHATAN,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>BPJS Ketenagakerjaan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BPJS_NAKER,0,',','.')."</td></tr>";
		$html.="<tr><td>5</td><td>Tunjangan Penyesuaian</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_PENYESUAIAN,0,',','.')."</td></tr>";
		$html.="<tr><td>6</td><td>Bantuan Servis Motor</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->SERVIS_MOTOR,0,',','.')."</td></tr>";
		$html.="<tr><td>7</td><td>Koreksi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->KOREKSI,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($totpendapatan,0,',','.')."</b></th></tr>";

		$totPotongan=$rsOut->JML_ANGSURAN+$rsOut->POT_LAIN;
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Angsuran Pinjaman</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->JML_ANGSURAN,0,',','.')."</td></tr>";
		$html.="<tr><td>1</td><td>Lain-lain</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_LAIN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format($totPotongan,0,',','.')."</b></th></tr>";

		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format(($totpendapatan-$totPotongan),0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip',$rsmaster->JENIS, "", $rsmaster->TAHUN, $rsmaster->BULAN  );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP_GAJI_TRANSPORT_ZISCO_".$rsOut->BLN.$rsOut->THN."-".$nik.".pdf";		
		
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
		
		$str = "select * from gaji_zisco_transport where id_validasi=".$id_validasi." ORDER BY `NIK` ";
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
			FROM `gaji_zisco_transport` gs, pegawai p
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
		$totpendapatan=$rsOut->U_TRANS_DITERIMA+$rsOut->T_JABATAN+$rsOut->BPJS_KESEHATAN+$rsOut->BPJS_NAKER+$rsOut->T_PENYESUAIAN+$rsOut->SERVIS_MOTOR+$rsOut->KOREKSI;
		$html.="<table class=\"mydata\" >";
		$html.="<tr><tD colspan=4><u>A. PENDAPATAN</u></td></tr>";

		$html.="<tr><td>1</td><td>Uang Transport</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->U_TRANS_DITERIMA,0,',','.')."</td></tr>";
		$html.="<tr><td>2</td><td>Tunjangan Jabatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_JABATAN,0,',','.')."</td></tr>";
		$html.="<tr><td>3</td><td>BPJS Kesehatan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BPJS_KESEHATAN,0,',','.')."</td></tr>";
		$html.="<tr><td>4</td><td>BPJS Ketenagakerjaan</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->BPJS_NAKER,0,',','.')."</td></tr>";
		$html.="<tr><td>5</td><td>Tunjangan Penyesuaian</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->T_PENYESUAIAN,0,',','.')."</td></tr>";
		$html.="<tr><td>6</td><td>Bantuan Servis Motor</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->SERVIS_MOTOR,0,',','.')."</td></tr>";
		$html.="<tr><td>7</td><td>Koreksi</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->KOREKSI,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Pendapatan</b></th><th style='text-align:right'><b>Rp. ".number_format($totpendapatan,0,',','.')."</b></th></tr>";

		$totPotongan=$rsOut->JML_ANGSURAN+$rsOut->POT_LAIN;
		$html.="<tr><tD colspan=4><u>B. POTONGAN</u></td></tr>";
		$html.="<tr><td>1</td><td>Angsuran Pinjaman</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->JML_ANGSURAN,0,',','.')."</td></tr>";
		$html.="<tr><td>1</td><td>Lain-lain</td><td>:</td><td style='text-align:right'>Rp. ".number_format($rsOut->POT_LAIN,0,',','.')."</td></tr>";
		$html.="<tr><th COLSPAN=3><b>Total Potongan</b></th><th style='text-align:right'><b>Rp. ".number_format($totPotongan,0,',','.')."</b></th></tr>";

		$html.="<tr><th COLSPAN=3><b>Total Diterima</b></th><th style='text-align:right'><b>Rp. ".number_format(($totpendapatan-$totPotongan),0,',','.')."</b></th></tr>";
		$html.="<tr><tD colspan=4></td></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="<tr><tD colspan=4 style='text-align:right'><i><b>HRD - Yatim Mandiri</b></i></td></tr>";
		$html.="<tr><tD colspan=4 style='text-align:center'>Jika terdapat kesalahan dalam penghitungan, dipersilahkan untuk<br>konfirmasi ke bagian HRD.Kekeliruan penghitungan akan dilakukan<br>koreksi bulan depan.</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();

		$path=$this->createPath('slip',$rsmaster->JENIS, "", $rsmaster->TAHUN, $rsmaster->BULAN  );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP_GAJI_TRANSPORT_ZISCO_".$rsOut->BLN.$rsOut->THN."-".$nik.".pdf";		
		
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
		
		$str="update gaji_validasi set VALIDASI=1, tgl_validasi='".date('Y-m-d')."' where id='".$id."'";

		if($this->db->query($str)){
			$respon['status'] = 'success';
			$respon['str'] = $str;			
		} else {			
			$respon['status'] = 'error';
			$respon['errormsg'] = 'Invalid Data';			
		}
		echo json_encode($respon);
	}
	
	public function check_rekap_absen(){
		$respon = new StdClass();
		$thn=$this->input->post('cbTahun');
		$bln=$this->input->post('cbBulan');
		//cek rekap absensi sudah validasi
        $strRAZ_Cek="select id_cab, validasi from rekap_validasi_zisco where periode='".$thn.$bln."'  ";
        $sqlRAZ_cek = $this->db->query($strRAZ_Cek);
        $yg_belum="Rekap Absen Zisco Cabang Yang belum divalidasi : <br>";
        $jml_yg_belum=0;
        $i=1;
        if ($sqlRAZ_cek->num_rows > 0){
		    $rsRAZ_Cek = $sqlRAZ_cek ->result();
		    	foreach ($rsRAZ_Cek as $row) {
		            if ($row->validasi==0){
		                $sqlcab=$this->gate_db->query("SELECT kota FROM mst_cabang WHERE id_cabang=".$row->id_cab)->result();
		                $jml_yg_belum++;
		                foreach ($sqlcab as $cab) {
		                    $yg_belum.=$i.". ".$cab->kota."<br>";
		                }
		                $i++;
		            }
		    	}
		   	if ($jml_yg_belum>0){
    				$respon->status = 'error';
    				$respon->pesan = $yg_belum;
    		}else{
    				$respon->status = 'success';
			}
			
		$respon->jml=$jml_yg_belum."#".$strRAZ_Cek;
		} else {
			$respon->status = 'error';
    		$respon->pesan = "Belum ada rekap absensi zisco periode '".$thn.$bln."'  ";
		}
		echo json_encode($respon);
	}
}
