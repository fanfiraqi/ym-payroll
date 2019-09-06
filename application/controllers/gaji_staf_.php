<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gaji_staf extends MY_App {

	function __construct()
	{
		parent::__construct();
		$this->load->model('gaji_staf_model');
		$this->config->set_item('mymenu', 'mn3');
		$this->load->helper('array');
		$this->load->helper('download');
		$this->load->dbutil();
	    $this->load->helper('file');
		$this->load->database();
		$this->gate_db=$this->load->database('gate', TRUE);
		$this->auth->authorize();
	}
	
		
	public function print_down(){
		$param=$this->input->get('param');
		$par=explode("_",$param);
		$rsPath=$this->db->query("select * from file_slip where thn='".$par[0]."' and bln='".$par[1]."' and nik='".$par[2]."'")->row();
		$mydata = file_get_contents(base_url($rsPath->PATH."/".$rsPath->NAMA_FILE));
		force_download($rsPath->NAMA_FILE, $mydata);
		$data['respon']=1;
		$data['teks']="select * from file_slip where thn='".$par[0]."' and bln='".$par[1]."' and nik='".$par[2]."'";
		//echo json_encode($data);
		
	}
	public function index()
	{
		$this->template->set('pagetitle','Daftar Gaji/Tunjangan Staff');	
		$this->config->set_item('mySubMenu', 'mn31');
		$data['cabang'] = $this->common_model->comboCabang();
		$data['divisi'] = $this->divTree($this->common_model->getDivisi()->result_array());
		$data['arrBulan'] = $this->arrBulan;
		$data['arrIntBln'] = $this->arrIntBln;
		$data['arrThn'] = $this->getYearArr();
		$this->template->load('default','fgaji_staf/payroll_filter',$data);
	}
	public function staff_payroll_list(){
		$this->template->set('pagetitle','Form Entri Penggajian Staff Dalam');				//query pegawai staff, FOFR not included
		$bln=$this->input->post('cbBulan');
		$thn=$this->input->post('cbTahun');
		$id_cab=$this->input->post('id_cabang');
		$id_div=$this->input->post('id_divisi');
		$blnStr=$this->arrBulan;
		$blnIdk=$this->arrIntBln;
		if ($bln==1){
			$bln_pre=12;
			$thn_pre=$thn-1;
		}else{
			$bln_pre=$blnIdk[$bln-1];
			$thn_pre=$thn;
		}
		$strList = "SELECT p.*, period_diff( date_format( now( ) , '%Y%m' ) , date_format( tgl_aktif, '%Y%m' ) ) SELISIH from pegawai p  where  status_aktif=1 and id_cabang=$id_cab and id_div=$id_div and p.id_jab <>36 ";

		$strCek="select count(*) CEK from gaji_staff where bln='".$blnIdk[$bln]."' and thn='$thn' and id_cabang=$id_cab ";
		//get tunjangan anak

		$rsCek = $this->db->query($strCek)->row();
		$data['cek']  = $this->db->query($strCek)->row();
		$data['row'] = $this->db->query($strList)->result();
		$data['strBulan'] = $blnStr[$bln];
		$data['digitBln'] = $blnIdk[$bln];
		$data['thn'] = $thn;
		$data['bln'] = $bln;
		$data['id_cabang'] = $id_cab;
		$data['id_divisi'] = $id_div;
		$data['str'] = $strList;

		$nmCabang = $this->gate_db->query("select KOTA from mst_cabang where id_cabang=".$this->input->post('id_cabang'))->row();
		$rsnmdiv=$this->gate_db->query("select NAMA_DIV from mst_divisi where id_div=$id_div")->row();
		if ($rsCek->CEK<=0){
			$data['stsform']="New";
			$this->template->set('pagetitle','Form Penggajian Staff Dalam (NEW) '.$blnStr[$bln]." ".$thn." Cabang ".strtoupper($nmCabang->KOTA)."-".$rsnmdiv->NAMA_DIV);	
			$this->template->load('default','fgaji_staf/staff_payroll_list',$data);
		}else{
			$tgl1=strtotime($thn_pre."-".$bln_pre."-26");
			$tgl2=strtotime($thn."-".$blnIdk[$bln]."-25");
			$data['cekTgl']=$tgl1."#".$tgl2;
			if (strtotime(date('Y-m-d'))>=$tgl1 && strtotime(date('Y-m-d'))<=$tgl2){	//cek sysdate antara 26-bln-1 s.d 25-bln 
				//boleh edit, ambil data master
				$data['stsform']="New";
				$this->template->set('pagetitle','Form Penggajian Staff Dalam (OPEN) '.$blnStr[$bln]." ".$thn." Cabang ".strtoupper($nmCabang->KOTA)."-".$rsnmdiv->NAMA_DIV);	
				$this->template->load('default','fgaji_staf/staff_payroll_list_edit',$data);
			}else{
				$strRes="select gs.*, p.NAMA from gaji_staff gs, pegawai p where  p.id_cabang=$id_cab and  p.id_div=$id_div and p.NIK=gs.nik and  bln='".$blnIdk[$bln]."' and thn='$thn'";
				$data['strRes'] = $strRes;
				$data['row'] = $this->db->query($strRes)->result();
				//tdk boleh edit, ambil data transaksi gaji_fo smua  
				$this->template->set('pagetitle','Form Penggajian Staff Dalam (CLOSED/VIEW ONLY) '.$blnStr[$bln]." ".$thn." Cabang ".strtoupper($nmCabang->KOTA)."-".$rsnmdiv->NAMA_DIV);	
				$this->template->load('default','fgaji_staf/staff_payroll_list_disabled',$data);
			}
		}


	}
	
	public function exportCsv(){
		$bln=$this->input->post('bln');
		$thn=$this->input->post('thn');
		$id_cabang=$this->input->post('id_cabang');
		$id_divisi=$this->input->post('id_divisi');
		$blnStr=$this->arrBulan;
		$strBulan=$blnStr[intval($bln)];
		

		$rsnmcab=$this->db->query("select KOTA from mst_cabang where id_cabang=$id_cabang")->row();
		$rsnmdiv=$this->db->query("select NAMA_DIV from mst_divisi where id_div=$id_divisi")->row();
		$rsRekNH=$this->db->query("select VALUE2 from params where id=1")->row();
		$total=$this->db->query("select sum(TOTAL) TOT, count(*) CNT from gaji_staff where bln='$bln' and thn='$thn' and nik in (select distinct nik from pegawai where id_cabang=$id_cabang and id_div=$id_divisi)")->row();

		$out=$rsRekNH->VALUE2.",YAYASAN NURUL HAYAT SURABAYA,IDR,".$total->TOT.",PAYROLL ".$rsnmcab->KOTA."-".$rsnmdiv->NAMA_DIV." ".$strBulan." ".$thn.",".$total->CNT.",".$thn.$bln."01".",,,"."\r\n";
		//norek yayasan
		
		//row gaji fo
		$strList="SELECT p.NIK, p.REKENING,  p.NAMA, gs.TOTAL
			FROM `pegawai` p, gaji_staff gs
			WHERE p.nik=gs.nik and bln='$bln' and thn='$thn' and p.nik in (select distinct nik from pegawai where id_cabang=$id_cabang and id_div=$id_divisi) ";
		$rsRes=$this->db->query($strList)->result();
		foreach($rsRes as $row){
			$out.=$row->REKENING.",".strtoupper($row->NAMA).",IDR,".$row->TOTAL.",PAYROLL,,"."\r\n";
		}
		//cek dir
		$path=$this->createPath('csv',$id_cabang, $id_divisi, $thn, $bln );	//csv path hanya s.d thn, bln masuk nama file
		$fileName="PAYROLL ".strtoupper($rsnmcab->KOTA)." STAFF ".$rsnmdiv->NAMA_DIV." ".strtoupper($strBulan)." ".$thn.".csv";
		write_file($path."/".$fileName,$out);	

		//SIMPAN RECORD
		$strcek="select * from file_csv where id_cab=$id_cabang and id_div=$id_divisi and thn='$thn' and bln='$bln'";
		$cek=$this->db->query($strcek)->num_rows();
		if ($cek>0){
			$data = array('PATH' => $path,'NAMA_FILE' => $fileName, 'UPDATED_BY' =>'admin', 'UPDATED_DATE' =>date('Y-m-d H:i:s')	);
			$this->db->where(array('id_cab'=>$id_cabang, 'id_div'=> $id_divisi,  'thn'=>$thn,  'bln'=>$bln))->update('file_csv', $data);
		}else{
			$data = array(
						'ID_CAB' => $id_cabang,
						'ID_DIV' => $id_divisi,
						'THN' => $thn,
						'BLN' => $bln,
						'PATH' => $path,
						'NAMA_FILE' => $fileName,
						'CREATED_BY' =>'admin',
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>'admin',
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
		$arrKey=explode('_',$param);
		$blnStr=$this->arrBulan;
		$strBulan=$blnStr[intval($arrKey[1])];
		$thn=$arrKey[0];
		$bln=$arrKey[1];
		$str="SELECT p.NIK,p.TGL_AKTIF, p.NAMA, mst.NAMA_JAB, gs. *
			FROM `gaji_staff` gs, pegawai p, mst_jabatan mst
			WHERE gs.nik = p.nik and p.id_jab=mst.id_jab and
			gs.bln='".$arrKey[1]."' and gs.thn='".$arrKey[0]."' and gs.nik='".$arrKey[2]."'";		
		$rsOut=$this->db->query($str)->row();
		$strCariCicilan="SELECT LAMA
					FROM `pinjaman_angsuran` pa, pinjaman_header ph
					WHERE pa.id_header=ph.id and pa.cicilan_ke=".$rsOut->ANGSURAN_KE." and ph.nik='".$arrKey[2]."'";
		$lamaAngs=0;
		if ($this->db->query($strCariCicilan)->num_rows()<=0){
			$lamaAngs=0;
		}else{
			$rsL=$this->db->query($strCariCicilan)->row();
			$lamaAngs=$rsL->LAMA;
		}
		$data['str']=$str;
		$data['strbulan']=$strBulan;
		$data['thn']=$arrKey[0];
		
		$subTotal=$rsOut->GAPOK+$rsOut->T_JABATAN+$rsOut->T_FUNGSIONAL+$rsOut->TUNJ_UBUDIAH+$rsOut->T_MASAKERJA+$rsOut->T_MAKAN+$rsOut->T_TRANSPORT+$rsOut->T_ANAK+$rsOut->SEDEKAH_TAAWUN;
		$gTotal=$subTotal-($rsOut->JML_ANGSURAN+$rsOut->JML_POT_GAPOK+$rsOut->PREMI_JHT);
		
		$interval = date_diff(date_create(), date_create($rsOut->TGL_AKTIF));
		$masaKerja= $interval->format("  %Y Tahun, %M Bulan, %d Hari");
		$terlambat=0;

		$html=$this->commonlib->pdfHeadertemplate();
		//$html=$this->commonlib->reportHeader();
		$html.="<table border=0><tr><td><img src=\"".base_url('assets/css/logoNH.gif')."\" width=\"100\"></td></tr></table>";
		$html.="<table class=\"mydata\" >";
		$html.="<tr><th COLSPAN=4>SLIP GAJI</th></tr>";
		$html.="<tr><tD colspan=2>NAMA</td><tD>:</td><tD>".$rsOut->NAMA."</td></tr>";
		$html.="<tr><tD colspan=2>JABATAN</td><tD>:</td><tD>".$rsOut->NAMA_JAB."</td></tr>";
		$html.="<tr><tD colspan=2>GAJI BULAN</td><tD>:</td><tD>".$strBulan." ".$arrKey[0]."</td></tr>";
		$html.="</table >";
		$html.="<table class=\"mydata\" >";
		$html.="<tr><th COLSPAN=4>RINCIAN GAJI</th></tr>";
		$html.="<tr><td>1.</td><td>GAJI POKOK</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->GAPOK,0,',','.')."</td></tr>";
		$html.="<tr><td>2.</td><td>TUNJANGAN JABATAN</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->T_JABATAN,0,',','.')."</td></tr>";		
		$html.="<tr><td>3.</td><td>TUNJANGAN UBUDIAH</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->TUNJ_UBUDIAH,0,',','.')."</td></tr>";
		$html.="<tr><td>3.</td><td>TUNJANGAN ISO</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->T_FUNGSIONAL,0,',','.')."</td></tr>";
		$html.="<tr><td>4.</td><td>TUNJANGAN MASA KERJA</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->T_MASAKERJA,0,',','.')."</td></tr>";
		$html.="<tr><td>5.</td><td>UANG MAKAN</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->T_MAKAN,0,',','.')."</td></tr>";
		$html.="<tr><td>6.</td><td>TUNJANGAN TRANSPORT</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->T_TRANSPORT,0,',','.')."</td></tr>";
		$html.="<tr><td>7.</td><td>TUNJANGAN ANAK</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format(($rsOut->T_ANAK),0,',','.')."</td></tr>";
		$html.="<tr><td>8.</td><td>TUNJANGAN TA'AWUN</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->SEDEKAH_TAAWUN,0,',','.')."</td></tr>";
		
		$html.="<tr><td>&nbsp;</td><td COLSPAN=3><HR></td></tr>";
		$html.="<tr><td>&nbsp;</td><td>SUB TOTAL</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($subTotal,0,',','.')."</td></tr>";
		$html.="<tr><td>9.</td><td>ANGSURAN PINJAMAN ".$rsOut->ANGSURAN_KE."/".$lamaAngs."</td><td>:</td><td style=\"text-align:right\">(Rp. ".number_format($rsOut->JML_ANGSURAN,0,',','.').")</td></tr>";
		$html.="<tr><td>10.</td><td>POTONGAN GAJI POKOK</td><td>:</td><td style=\"text-align:right\">(Rp. ".number_format($rsOut->JML_POT_GAPOK,0,',','.').")</td></tr>";		
		$html.="<tr><td>11.</td><td>SEDEKAH TA'AWUN</td><td>:</td><td style=\"text-align:right\">(Rp. ".number_format($rsOut->SEDEKAH_TAAWUN,0,',','.').")</td></tr>";

		$html.="<tr><td>12.</td><td>PREMI JHT : (Rp. ".number_format(($rsOut->SUBSIDI_JHT+$rsOut->PREMI_JHT),0,',','.').")</td><td></td><td style=\"text-align:right\"></td></tr>";
		$html.="<tr><td></td><td>SUBSIDI PREMI JHT : <u>Rp. ".number_format(($rsOut->SUBSIDI_JHT),0,',','.')."</u></td><td></td><td style=\"text-align:right\"></td></tr>";		
		
		$html.="<tr><td></td><td>POTONGAN PREMI JHT KE ".$rsOut->JHT_KE."</td><td>:</td><td style=\"text-align:right\">(Rp. ".number_format($rsOut->PREMI_JHT,0,',','.').")</td></tr>";
		$html.="<tr><td>&nbsp;</td><td COLSPAN=3><HR></td></tr>";
		$html.="<tr><th COLSPAN=3 >THP</th><th style=\"text-align:right\">Rp. ".number_format($rsOut->TOTAL,0,',','.')."</th></tr>";
		$html.="<tr><td COLSPAN=4 >Masa Kerja : $masaKerja </td></tr>";
		$html.="<tr><td COLSPAN=4 >Akumulasi Keterlambatan : $terlambat Jam</td></tr>";
		$html.="<tr><td COLSPAN=4 >Tidak Hadir : ".$rsOut->JML_ALPA." hari</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();
		//echo $html;
		//$html = $this->load->view('fgaji_fo/fo_slip', $data, true);
		//$namafile="slip_gs_".$arrKey[0]."_".$arrKey[1]."_".$arrKey[2];

		$path=$this->createPath('slip','', '', $thn, $bln );	//csv path hanya s.d thn, bln masuk nama file
		$fileName="SLIP STAFF ".strtoupper($strBulan)." ".$thn."-".$arrKey[2].".pdf";		
		
		//$this->ci_pdf->pdf_create($html, $path."/".$fileName, FALSE);
		$this->ci_pdf->pdf_create_my($html, $path."/".$fileName, 'a6','portrait', FALSE);
		
		//SIMPAN RECORD
		$strcek="select * from file_slip where nik='".$arrKey[2]."' and thn='$thn' and bln='$bln'";
		$cek=$this->db->query($strcek)->num_rows();
		if ($cek>0){
			$data = array('PATH' => $path,'NAMA_FILE' => $fileName, 'UPDATED_BY' =>'admin', 'UPDATED_DATE' =>date('Y-m-d H:i:s')	);
			$this->db->where(array('nik'=>$arrKey[2],  'thn'=>$thn,  'bln'=>$bln))->update('file_slip', $data);
		}else{
			$data = array(
						'NIK' => $arrKey[2],
						'THN' => $thn,
						'BLN' => $bln,
						'PATH' => $path,
						'NAMA_FILE' => $fileName,
						'CREATED_BY' =>'admin',
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>'admin',
						'UPDATED_DATE' =>date('Y-m-d H:i:s')
					);
			$this->db->insert('file_slip', $data);
			
		}
		$this->db->trans_commit();
		$data['isi']=$path."/".$fileName;
		echo json_encode($data);

	}

	public function save_gaji_staff(){
		if($this->input->post()) {
			$this->load->library('form_validation');
			$rules = array();
			for($r=1;$r<=$this->input->post('jmlRow');$r++){	
				if ($this->input->post('flag_'.$r)=="1"){
					array_push($rules, array(
							'field' => 'nik_'.$r,
							'label' => 'nik_'.$r,
							'rules' => 'trim|xss_clean|required'));
					array_push($rules, array(
							'field' => 'tunjFungsional_'.$r,
							'label' => 'tunjFungsional_'.$r,
							'rules' => 'trim|xss_clean|required|numeric'));
				}
			}
			
			$this->form_validation->set_rules($rules);
			//$out=$this->form_validation->run();
			$this->form_validation->set_message('required', 'Field %s harus diisi angka.');
			$respon = new StdClass();
			//$xGets="mulai";
			if ($this->form_validation->run() == TRUE){
				//$xGets.="masuk run";
				try {
					$this->db->trans_begin();
					for($r=1;$r<=$this->input->post('jmlRow');$r++){
						if ($this->input->post('flag_'.$r)=="1"){
						$this->db->delete('gaji_staff', array("NIK"=>$this->input->post('nik_'.$r), 'BLN'=>$this->input->post('bln'), 'THN'=>$this->input->post('thn')));
						//$xGets.="<br>".$this->db->last_query();
							$this->db->trans_commit();
							$data = array(
									'BLN' => $this->input->post('bln'),
									'THN' => $this->input->post('thn'),
									'NIK' => $this->input->post('nik_'.$r),
									'ID_JAB' => $this->input->post('idjab_'.$r),
									'MASA_KERJA' => $this->input->post('masakerja_'.$r),
									'T_JABATAN' => $this->input->post('tunjJabatan_'.$r),
									'T_MASAKERJA' => $this->input->post('tunjMasakerja_'.$r),
									'GAPOK' => $this->input->post('gapok_'.$r),
									'JML_HADIR' => $this->input->post('jmlhadir_'.$r),
									'T_MAKAN' => $this->input->post('uangMakan_'.$r),
									'T_TRANSPORT' => $this->input->post('transport_'.$r),
									'JML_ANAK' => $this->input->post('jml_anak_'.$r),
									'T_ANAK' => $this->input->post('tunjAnak_'.$r),
									'DAILY_ISO' => $this->input->post('dailyReport_'.$r),	
									'T_FUNGSIONAL' => $this->input->post('tunjFungsional_'.$r),	
									'NILAI_UBUDIAH' => $this->input->post('ubudiah_'.$r),										
									'TUNJ_UBUDIAH' => $this->input->post('tunjUbudiah_'.$r),	
									'JML_ALPA' => $this->input->post('jmlAlpa_'.$r),
									'JML_POT_GAPOK' => $this->input->post('potGapok_'.$r),
									'JML_ANGSURAN' => $this->input->post('jmlcicilan_'.$r),
									'ANGSURAN_KE' => $this->input->post('cicilke_'.$r),
									'SEDEKAH_TAAWUN' => $this->input->post('taawun_'.$r),
									'SUBSIDI_JHT' => $this->input->post('hiddenSubsidi_jht_'.$r),
									'PREMI_JHT' => $this->input->post('premi_jht_'.$r),
									'JHT_KE' => $this->input->post('jht_ke_'.$r),
									'TOTAL' => $this->input->post('totalGaji_'.$r),
									'CREATED_BY' =>'admin',
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>'admin',
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);
								if ($this->db->insert('gaji_staff', $data)){
									//$xGets.="<br>".$this->db->last_query();
									$thnbln=$this->input->post('thn').$this->input->post('bln');
									if ($this->input->post('penanda_jht_ke')=="new"){
										$jht_ke_update=$this->db->query("update pegawai set jht_ke=".$this->input->post('jht_ke_'.$r)." where NIK ='".$this->input->post('nik_'.$r)."'");
										$this->db->trans_commit();
									}
									
									if ($this->input->post('pinj_idheader_'.$r)!="" ){
										$scekCicil="select count(*) STSCEK from pinjaman_angsuran where id_header=".$this->input->post('pinj_idheader_'.$r)." and cicilan_ke=".$this->input->post('cicilke_'.$r);
										$cekStatusCicilan= $this->db->query($scekCicil)->row();
										if ($cekStatusCicilan->STSCEK>=1){
											//UPDATE TRANSAKSI PINJAMAN
											$data2 = array(
											'TGL_BAYAR' => date('Y-m-d'),
											'JML_BAYAR' => $this->input->post('jmlcicilan_'.$r)
											);
											$this->db->where('ID_HEADER',$this->input->post('pinj_idheader_'.$r))->where('CICILAN_KE',$this->input->post('cicilke_'.$r))->update('pinjaman_angsuran', $data2);
											//$xGets.="<br>".$this->db->last_query();
											$this->db->trans_commit();

											//update header pinjaman
											$strHeader="SELECT h.NIK, h.ID, h.JUMLAH, SUM( a.JML_BAYAR ) ANGSUR, IF( SUM( a.JML_BAYAR ) >= h.JUMLAH, 'Lunas', 'Belum Lunas' ) STS FROM pinjaman_header h, pinjaman_angsuran a
												WHERE h.id = a.id_header
												AND a.id_header = '".$this->input->post('pinj_idheader_'.$r)."'";
												$query = $this->db->query($strHeader)->row();
											$this->db->where('ID',$this->input->post('pinj_idheader_'.$r))->update('pinjaman_header', array('STATUS'=>$query->STS));
											//$xGets.="<br>".$this->db->last_query();
										}
									}
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

	function slipLoop(){
		//slipLoop param : alamat email, path file slip
		$id_cab=$this->input->get('cabang');
		$id_div=$this->input->get('divisi');
		$bln=$this->input->get('bln');
		$thn=$this->input->get('thn');
		$str = "select * from gaji_staff where bln='$bln' and thn='$thn' and nik in(select distinct nik from pegawai where id_cabang=$id_cab and id_div=$id_div and status_aktif=1 and id_jab<>13 and id_jab<>14) ORDER BY `NIK` ";
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
				$param=$thn."_".$bln."_".$result->NIK;
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
		$blnStr=$this->arrBulan;
		$strBulan=$blnStr[intval($arrKey[1])];
		$thn=$arrKey[0];
		$bln=$arrKey[1];
		$str="SELECT p.NIK,p.TGL_AKTIF, p.NAMA, mst.NAMA_JAB, gs. *
			FROM `gaji_staff` gs, pegawai p, mst_jabatan mst
			WHERE gs.nik = p.nik and p.id_jab=mst.id_jab and
			gs.bln='".$arrKey[1]."' and gs.thn='".$arrKey[0]."' and gs.nik='".$arrKey[2]."'";		
		$rsOut=$this->db->query($str)->row();
		$strCariCicilan="SELECT LAMA
					FROM `pinjaman_angsuran` pa, pinjaman_header ph
					WHERE pa.id_header=ph.id and pa.cicilan_ke=".$rsOut->ANGSURAN_KE." and ph.nik='".$arrKey[2]."'";
		$lamaAngs=0;
		if ($this->db->query($strCariCicilan)->num_rows()<=0){
			$lamaAngs=0;
		}else{
			$rsL=$this->db->query($strCariCicilan)->row();
			$lamaAngs=$rsL->LAMA;
		}
		$data['str']=$str;
		$data['strbulan']=$strBulan;
		$data['thn']=$arrKey[0];
		
		$subTotal=$rsOut->GAPOK+$rsOut->T_JABATAN+$rsOut->T_FUNGSIONAL+$rsOut->TUNJ_UBUDIAH+$rsOut->T_MASAKERJA+$rsOut->T_MAKAN+$rsOut->T_TRANSPORT+$rsOut->T_ANAK+$rsOut->SEDEKAH_TAAWUN;
		$gTotal=$subTotal-($rsOut->JML_ANGSURAN+$rsOut->JML_POT_GAPOK+$rsOut->PREMI_JHT);
		
		$interval = date_diff(date_create(), date_create($rsOut->TGL_AKTIF));
		$masaKerja= $interval->format("  %Y Tahun, %M Bulan, %d Hari");
		$terlambat=0;

		$html=$this->commonlib->pdfHeadertemplate();
		$html.="<table border=0><tr><td><img src=\"".base_url('assets/css/logoNH.gif')."\" width=\"100\"></td></tr></table>";
		$html.="<table class=\"mydata\" >";
		$html.="<tr><th COLSPAN=4>SLIP GAJI</th></tr>";
		$html.="<tr><tD colspan=2>NAMA</td><tD>:</td><tD>".$rsOut->NAMA."</td></tr>";
		$html.="<tr><tD colspan=2>JABATAN</td><tD>:</td><tD>".$rsOut->NAMA_JAB."</td></tr>";
		$html.="<tr><tD colspan=2>GAJI BULAN</td><tD>:</td><tD>".$strBulan." ".$arrKey[0]."</td></tr>";
		$html.="</table >";
		$html.="<table class=\"mydata\">";
		$html.="<tr><th COLSPAN=4>RINCIAN GAJI</th></tr>";
		$html.="<tr><td>1.</td><td>GAJI POKOK</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->GAPOK,0,',','.')."</td></tr>";
		$html.="<tr><td>2.</td><td>TUNJANGAN JABATAN</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->T_JABATAN,0,',','.')."</td></tr>";		
		$html.="<tr><td>3.</td><td>TUNJANGAN UBUDIAH</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->TUNJ_UBUDIAH,0,',','.')."</td></tr>";
		$html.="<tr><td>3.</td><td>TUNJANGAN ISO</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->T_FUNGSIONAL,0,',','.')."</td></tr>";
		$html.="<tr><td>4.</td><td>TUNJANGAN MASA KERJA</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->T_MASAKERJA,0,',','.')."</td></tr>";
		$html.="<tr><td>5.</td><td>UANG MAKAN</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->T_MAKAN,0,',','.')."</td></tr>";
		$html.="<tr><td>6.</td><td>TUNJANGAN TRANSPORT</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->T_TRANSPORT,0,',','.')."</td></tr>";
		$html.="<tr><td>7.</td><td>TUNJANGAN ANAK</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format(($rsOut->T_ANAK),0,',','.')."</td></tr>";
		$html.="<tr><td>8.</td><td>TUNJANGAN TA'AWUN</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($rsOut->SEDEKAH_TAAWUN,0,',','.')."</td></tr>";
		
		$html.="<tr><td>&nbsp;</td><td COLSPAN=3><HR></td></tr>";
		$html.="<tr><td>&nbsp;</td><td>SUB TOTAL</td><td>:</td><td style=\"text-align:right\">Rp. ".number_format($subTotal,0,',','.')."</td></tr>";
		$html.="<tr><td>9.</td><td>ANGSURAN PINJAMAN ".$rsOut->ANGSURAN_KE."/".$lamaAngs."</td><td>:</td><td style=\"text-align:right\">(Rp. ".number_format($rsOut->JML_ANGSURAN,0,',','.').")</td></tr>";
		$html.="<tr><td>10.</td><td>POTONGAN GAJI POKOK</td><td>:</td><td style=\"text-align:right\">(Rp. ".number_format($rsOut->JML_POT_GAPOK,0,',','.').")</td></tr>";
		
		$html.="<tr><td>11.</td><td>SEDEKAH TA'AWUN</td><td>:</td><td style=\"text-align:right\">(Rp. ".number_format($rsOut->SEDEKAH_TAAWUN,0,',','.').")</td></tr>";

		$html.="<tr><td>12.</td><td>PREMI JHT : (Rp. ".number_format(($rsOut->SUBSIDI_JHT+$rsOut->PREMI_JHT),0,',','.').")</td><td></td><td style=\"text-align:right\"></td></tr>";
		$html.="<tr><td></td><td>SUBSIDI PREMI JHT : <u>Rp. ".number_format(($rsOut->SUBSIDI_JHT),0,',','.')."</u></td><td></td><td style=\"text-align:right\"></td></tr>";		
		
		$html.="<tr><td></td><td>POTONGAN PREMI JHT KE ".$rsOut->JHT_KE."</td><td>:</td><td style=\"text-align:right\">(Rp. ".number_format($rsOut->PREMI_JHT,0,',','.').")</td></tr>";

		$html.="<tr><td>&nbsp;</td><td COLSPAN=3><HR></td></tr>";
		$html.="<tr><th COLSPAN=3 >THP</th><th style=\"text-align:right\">Rp. ".number_format($rsOut->TOTAL,0,',','.')."</th></tr>";
		$html.="<tr><td COLSPAN=4 >Masa Kerja : $masaKerja </td></tr>";
		$html.="<tr><td COLSPAN=4 >Akumulasi Keterlambatan : $terlambat Jam</td></tr>";
		$html.="<tr><td COLSPAN=4 >Tidak Hadir : ".$rsOut->JML_ALPA." hari</td></tr>";
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();
		//echo $html;
		//$html = $this->load->view('fgaji_fo/fo_slip', $data, true);
		//$namafile="slip_gs_".$arrKey[0]."_".$arrKey[1]."_".$arrKey[2];

		$path=$this->createPath('slip','', '', $thn, $bln );	//csv path hanya s.d thn, bln masuk nama file
		$fileName="SLIP STAFF ".strtoupper($strBulan)." ".$thn."-".$arrKey[2].".pdf";		
		
		//$this->ci_pdf->pdf_create($html, $path."/".$fileName, FALSE);
		$this->ci_pdf->pdf_create_my($html, $path."/".$fileName, 'a6','portrait', FALSE);

		//SIMPAN RECORD
		$strcek="select * from file_slip where nik='".$arrKey[2]."' and thn='$thn' and bln='$bln'";
		$cek=$this->db->query($strcek)->num_rows();
		if ($cek>0){
			$data = array('PATH' => $path,'NAMA_FILE' => $fileName, 'UPDATED_BY' =>'admin', 'UPDATED_DATE' =>date('Y-m-d H:i:s')	);
			$this->db->where(array('nik'=>$arrKey[2],  'thn'=>$thn,  'bln'=>$bln))->update('file_slip', $data);
		}else{
			$data = array(
						'NIK' => $arrKey[2],
						'THN' => $thn,
						'BLN' => $bln,
						'PATH' => $path,
						'NAMA_FILE' => $fileName,
						'CREATED_BY' =>'admin',
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>'admin',
						'UPDATED_DATE' =>date('Y-m-d H:i:s')
					);
			$this->db->insert('file_slip', $data);
			
		}
		$this->db->trans_commit();
		return 1;
		//$data['isi']=$path."/".$fileName;
		//echo json_encode($data);

	}

	function email($emailto,$pdf=null){
		$config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'payrollnurulhayat@gmail.com',
            'smtp_pass' => 'bambangheriyanto',
            'mailtype' => 'html'
        );
 
        // recipient, sender, subject, and you message
        $to = $emailto;
        $from = "payrollnurulhayat@gmail.com";
        $subject = "Absensi CSV ".date('dmYHis');
        $message = "This is a test email using CodeIgniter. If you can view this email, it means you have successfully send an email using CodeIgniter.";
 
        
        $this->load->library('email', $config);
        $this->email->attach('./assets/files/template/absensi.csv');
        $this->email->set_newline("\r\n");
        $this->email->from($from, 'No Reply');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
		
 
        // send your email. if it produce an error it will print 'Fail to send your message!' for you
        if($this->email->send()) {
           return 1;
        }
        else {
            return 0;
        }
	}



	// END PENGGAJIAN - START SET MASTER GAJI
	public function set_gaji()
	{
		$this->template->set('pagetitle','Set Gaji/Tunjangan Staff');		
		$data['cabang'] = $this->common_model->comboCabang();
		if ($this->session->userdata('auth')->ROLE=='Direktur Keuangan'){
			$data['divisi'] = $this->divTree($this->common_model->getDivisi()->result_array());
		}else{
			$data['divisi'] = $this->divTree($this->common_model->getDivisi_noDirektur()->result_array(),1);
		}
		$this->template->load('default','fgaji_staf/setFilter',$data);
	}
	public function comboDivByCab(){
		$id_cab = $this->input->post('id_cabang');
		if ($this->session->userdata('auth')->ROLE=='Direktur Keuangan'or'Manager HRD'){
		$query = $this->gate_db->select('d.ID_DIV,d.NAMA_DIV')
			->join('mst_divisi d','d.id_div=s.id_div','left')
			->where(array('s.id_cab'=>$id_cab))
			->distinct()
			->get('mst_struktur s')->result();
		}else{
			$query = $this->gate_db->query("SELECT DISTINCT `d`.`ID_DIV`, `d`.`NAMA_DIV` FROM `mst_struktur` s LEFT JOIN `mst_divisi` d ON `d`.`id_div`=`s`.`id_div` WHERE `s`.`id_cab` =  '$id_cab' AND `d`.`ID_DIV` <> 1 ")->result();
		}
		$respon = new StdClass();
		$respon->status = 0;
		if (!empty($query)){
			$respon->status = 1;
			$respon->data = $query;
		}else{
			$respon->status = 0;
		}
		echo json_encode($respon);
		//echo $this->db->last_query()."#".var_dump($query);
	}

	public function set_listEntri()
	{
				
		//query pegawai staff, FOFR not included
		$id_cab=$this->input->post('id_cabang');
		$id_div=$this->input->post('id_divisi');
		
		//yg belum diset
		$str = "SELECT p.*, j.NAMA_JAB from pegawai p, mst_jabatan j where p.id_jab=j.id_jab and status_aktif=1 and id_cabang=$id_cab and id_div=$id_div and p.id_jab <>36 and p.nik not in(select distinct nik from set_gaji_staff)";
		
		$strSudah = "SELECT p.*, j.NAMA_JAB from pegawai p, mst_jabatan j where p.id_jab=j.id_jab and status_aktif=1 and id_cabang=$id_cab and id_div=$id_div and p.id_jab <>36 and p.nik in(select distinct nik from set_gaji_staff)";

			$strTunj = "select * from mst_komp_gaji where isactive=1 ";
			
			$data['str'] = $str;
			$data['rowBelum'] = $this->db->query($str)->result();
			$data['rowSudah'] = $this->db->query($strSudah)->result();
			$data['rsTunj'] = $this->db->query($strTunj)->result();
			$nmCabang = $this->db->query("select KOTA from mst_cabang where id_cabang=".$this->input->post('id_cabang'))->row();
			$nmDiv= $this->db->query("select NAMA_DIV from mst_divisi where id_div=".$this->input->post('id_divisi'))->row();
			$this->template->set('pagetitle','Form Set Gaji/Tunjangan Staff Cabang : '.strtoupper($nmCabang->KOTA).', Divisi : '.strtoupper($nmDiv->NAMA_DIV));
			$this->template->load('default','fgaji_staf/setGajiEntry',$data);
			
		
	}

	public function temporar(){
		$pilih=$this->input->post('cbPilih');
		$idHid="";
		$nominal="";
		for ($x=0; $x<sizeof($pilih);$x++){
			for($j=1;$j<=$this->input->post('jml_j');$j++){
				$arr=$pilih[$x];
				$idHid.='hidIdTunj_'.$arr.'_'.$j."=".$this->input->post('hidIdTunj_'.$arr.'_'.$j)."#";
				$nominal.='valTunj_'.$arr.'_'.$j."=".$this->input->post('valTunj_'.$arr.'_'.$j)."#";
			}
		}
		$data['idHid']=$idHid;
		$data['nominal']=$nominal;
		$data['output']=$this->input->post('cbPilih');
		$this->template->set('pagetitle','Test Output...');
		$this->template->load('default','temporar.php',$data);
	}
	
//####
public function set_saveExistList(){
		$this->template->set('pagetitle','Tes');
		if($this->input->post()) {		
			$this->load->library('form_validation');
			$rules = array();
			for($r=1;$r<=$this->input->post('jmlRow');$r++){
				for($j=1;$j<=$this->input->post('jml_j');$j++){
					$stack=array('field' => 'valTunj_'.$r.'_'.$j,
					'label' => 'VALTUNJ_'.$r.'_'.$j,
					'rules' => 'trim|xss_clean|required|numeric');
				}
				array_push($rules, $stack);
			}
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi angka.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				try {
					$this->db->trans_begin();
					
					for($r=1;$r<=$this->input->post('jmlRow');$r++){
						$arrNIK[$r]=$this->input->post('nik_'.$r);
							for($j=1;$j<=$this->input->post('jml_j');$j++){
								$this->db->delete('set_gaji_staff', array("NIK"=>$arrNIK[$r], 'ID_KOMP_GAJI'=>$this->input->post('hidIdTunj_'.$r.'_'.$j)));
								$this->db->trans_commit();
								$data = array(
									'NIK' => $arrNIK[$r],
									'ID_KOMP_GAJI' => $this->input->post('hidIdTunj_'.$r.'_'.$j),
									'NOMINAL' => $this->input->post('valTunj_'.$r.'_'.$j),
									'CREATED_BY' =>'admin',
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>'admin',
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);
								if ($this->db->insert('set_gaji_staff', $data)){
									$this->db->trans_commit();
								} else {
									throw new Exception("gagal simpan");
								}								
								
							}
							
					}
				
				} catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();;
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
				
			} else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
			exit;
		}	
	}
//####

	public function set_saveList(){
		$this->template->set('pagetitle','Saving...');
		$pilih=$this->input->post('cbPilih');
		$isi_j=$this->input->post('jml_j');
		if($this->input->post()) {		
			$this->load->library('form_validation');
			$rules = array();
			$stack = array();
			//$tes="";
			for ($x=0; $x<sizeof($pilih);$x++){
				$r=$pilih[$x];
				for($j=1;$j<=$this->input->post('jml_j');$j++){
					$stack=array('field' => 'valTunj_'.$r.'_'.$j,
					'label' => 'VALTUNJ_'.$r.'_'.$j,
					'rules' => 'trim|xss_clean|required|numeric');
					
				}
				//$tes.="pilih=".$r." # jml_j=".$isi_j.",<br>";
				array_push($rules, $stack);
			}
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi angka.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				try {
					$this->db->trans_begin();
					
					for ($x=0; $x<sizeof($pilih);$x++){
						$r=$pilih[$x];
						$arrNIK[$r]=$this->input->post('nik_'.$r);
						for($j=1;$j<=$this->input->post('jml_j');$j++){
								$this->db->delete('set_gaji_staff', array("NIK"=>$arrNIK[$r], 'ID_KOMP_GAJI'=>$this->input->post('hidIdTunj_'.$r.'_'.$j)));
								$this->db->trans_commit();
								$data = array(
									'NIK' => $arrNIK[$r],
									'ID_KOMP_GAJI' => $this->input->post('hidIdTunj_'.$r.'_'.$j),
									'NOMINAL' => $this->input->post('valTunj_'.$r.'_'.$j),
									'CREATED_BY' =>'admin',
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>'admin',
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);
								if ($this->db->insert('set_gaji_staff', $data)){
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
			//$respon->tes = $tes;
			//$respon->isi = var_dump($_POST);
			echo json_encode($respon);
			exit;
		}


		
	}
	
	public function daily_report(){
		$this->template->set('pagetitle','Form Entri Penilaian ISO Staff Bulanan');		
		$data['cabang'] = $this->common_model->comboCabang();
		$data['divisi'] = $this->divTree($this->common_model->getDivisi()->result_array());
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrIntBln'] = $this->arrIntBln;
		$data['arrThn'] = $this->getYearArr();
		$this->template->load('default','fgaji_staf/dailyReportFilter',$data);
	}
	public function entri_ubudiah(){
		$this->template->set('pagetitle','Form Entri Penilaian Ubudiah Staff Bulanan');		
		$data['cabang'] = $this->common_model->comboCabang();
		$data['divisi'] = $this->divTree($this->common_model->getDivisi()->result_array());
		$data['arrBulan'] = $this->arrBulan2;
		$data['arrIntBln'] = $this->arrIntBln;
		$data['arrThn'] = $this->getYearArr();
		$this->template->load('default','fgaji_staf/ubudiahFilter',$data);
	}
	public function dailyReport_entry(){
		$bln=$this->input->post('cbBulan');
		$thn=$this->input->post('cbTahun');
		$id_cab=$this->input->post('id_cabang');
		$id_div=$this->input->post('id_divisi');
		$blnStr=$this->arrBulan2;
		$blnIdk=$this->arrIntBln;
		$nmCabang = $this->db->query("select KOTA from mst_cabang where id_cabang=".$this->input->post('id_cabang'))->row();
		$rsnmdiv=$this->db->query("select NAMA_DIV from mst_divisi where id_div=$id_div")->row();
		if ($bln==1){
			$bln_pre=12;
			$thn_pre=$thn-1;
		}else{
			$bln_pre=$blnIdk[intval($bln)-1];
			$thn_pre=$thn;
		}
		$strCek="select count(*) CEK from daily_report where bln='".$bln."' and thn='$thn' and nik in (select distinct nik from pegawai where id_cabang=$id_cab and id_div=$id_div and status_aktif=1 and id_jab <>13 and id_jab <>14)";
		$rsCek = $this->db->query($strCek)->row();
		
		$strList = "SELECT p.*, j.NAMA_JAB, period_diff( date_format( now( ) , '%Y%m' ) , date_format( tgl_aktif, '%Y%m' ) ) SELISIH from pegawai p, mst_jabatan j where p.id_jab=j.id_jab and status_aktif=1 and id_cabang=$id_cab and id_div=$id_div and p.id_jab <>36 ";
		$data['row'] = $this->db->query($strList)->result();
		$data['strList'] = $strList;
		$data['strBulan'] = $blnStr[$bln];
		$data['digitBln'] = $bln;
		$data['id_cabang'] = $id_cab;
		$data['id_divisi'] = $id_div;
		$data['thn'] = $thn;

		if ($rsCek->CEK<=0 && $thn.$bln>=date('Ym')){
			$this->template->set('pagetitle','Form Entri Penilaian ISO Staff Bulanan (NEW) '.$blnStr[$bln]." ".$thn." Cabang ".strtoupper($nmCabang->KOTA)."-".$rsnmdiv->NAMA_DIV);	
			$this->template->load('default','fgaji_staf/daily_report',$data);
		}else{

			$tgl1=strtotime($thn_pre."-".$bln_pre."-26");
			$tgl2=strtotime($thn."-".$bln."-31");
			$data['cekTgl']=$tgl1."#".$tgl2;
			if (strtotime(date('Y-m-d'))>=$tgl1 && strtotime(date('Y-m-d'))<=$tgl2){	//cek sysdate antara 26-bln-1 s.d 25-bln 
				//boleh edit, ambil data master
				$this->template->set('pagetitle','Form Entri Penilaian ISO Staff Bulanan (EDIT) '.$blnStr[$bln]." ".$thn." Cabang ".strtoupper($nmCabang->KOTA)."-".$rsnmdiv->NAMA_DIV);	
				$this->template->load('default','fgaji_staf/daily_report',$data);
			}else{
				$strDR = "SELECT p.*, dr.JML_HARI,  j.NAMA_JAB, period_diff( date_format( now( ) , '%Y%m' ) , date_format( tgl_aktif, '%Y%m' ) ) SELISIH from pegawai p, mst_jabatan j, daily_report dr where p.nik=dr.nik and thn='$thn' and bln='$bln' and p.id_jab=j.id_jab and status_aktif=1 and id_cabang=$id_cab and id_div=$id_div and p.id_jab <>36 ";
				$data['row'] = $this->db->query($strDR)->result();
				$this->template->set('pagetitle','Form Entri Penilaian ISO Staff Bulanan (DISABLED) '.$blnStr[$bln]." ".$thn." Cabang ".strtoupper($nmCabang->KOTA)."-".$rsnmdiv->NAMA_DIV);	
				$this->template->load('default','fgaji_staf/daily_report_disabled',$data);
			}
		}
	}

public function ubudiah_entry(){
		$bln=$this->input->post('cbBulan');
		$thn=$this->input->post('cbTahun');
		$id_cab=$this->input->post('id_cabang');
		$id_div=$this->input->post('id_divisi');
		$blnStr=$this->arrBulan2;
		$blnIdk=$this->arrIntBln;
		$nmCabang = $this->db->query("select KOTA from mst_cabang where id_cabang=".$this->input->post('id_cabang'))->row();
		$rsnmdiv=$this->db->query("select NAMA_DIV from mst_divisi where id_div=$id_div")->row();
		if ($bln==1){
			$bln_pre=12;
			$thn_pre=$thn-1;
		}else{
			$bln_pre=$blnIdk[intval($bln)-1];
			$thn_pre=$thn;
		}
		$strCek="select count(*) CEK from ubudiah_staff where bln='".$bln."' and thn='$thn' and nik in (select distinct nik from pegawai where id_cabang=$id_cab and id_div=$id_div and status_aktif=1 and id_jab <>13 and id_jab <>14)";
		$rsCek = $this->db->query($strCek)->row();
		
		$strList = "SELECT p.*, j.NAMA_JAB, period_diff( date_format( now( ) , '%Y%m' ) , date_format( tgl_aktif, '%Y%m' ) ) SELISIH from pegawai p, mst_jabatan j where p.id_jab=j.id_jab and status_aktif=1 and id_cabang=$id_cab and id_div=$id_div and p.id_jab <>36 ";
		$data['row'] = $this->db->query($strList)->result();
		$data['strBulan'] = $blnStr[$bln];
		$data['digitBln'] = $bln;
		$data['id_cabang'] = $id_cab;
		$data['id_divisi'] = $id_div;
		$data['thn'] = $thn;

		if ($rsCek->CEK<=0 && $thn.$bln>=date('Ym')){
			$this->template->set('pagetitle','Form Entri Penilaian Ubudiah Staff Bulanan (NEW) '.$blnStr[$bln]." ".$thn." Cabang ".strtoupper($nmCabang->KOTA)."-".$rsnmdiv->NAMA_DIV);	
			$this->template->load('default','fgaji_staf/ubudiah_staff',$data);
		}else{

			$tgl1=strtotime($thn_pre."-".$bln_pre."-26");
			$tgl2=strtotime($thn."-".$bln."-31");
			$data['cekTgl']=$tgl1."#".$tgl2;
			if (strtotime(date('Y-m-d'))>=$tgl1 && strtotime(date('Y-m-d'))<=$tgl2){	//cek sysdate antara 26-bln-1 s.d 25-bln 
				//boleh edit, ambil data master
				$this->template->set('pagetitle','Form Entri Penilaian Ubudiah Staff Bulanan (EDIT) '.$blnStr[$bln]." ".$thn." Cabang ".strtoupper($nmCabang->KOTA)."-".$rsnmdiv->NAMA_DIV);	
				$this->template->load('default','fgaji_staf/ubudiah_staff',$data);
			}else{
				$strDR = "SELECT p.*, dr.JML_HARI,  j.NAMA_JAB, period_diff( date_format( now( ) , '%Y%m' ) , date_format( tgl_aktif, '%Y%m' ) ) SELISIH from pegawai p, mst_jabatan j, ubudiah_staff dr where p.nik=dr.nik and thn='$thn' and bln='$bln' and p.id_jab=j.id_jab and status_aktif=1 and id_cabang=$id_cab and id_div=$id_div and p.id_jab <>36 ";
				$data['row'] = $this->db->query($strDR)->result();
				$this->template->set('pagetitle','Form Entri Penilaian Ubudiah Staff Bulanan (DISABLED) '.$blnStr[$bln]." ".$thn." Cabang ".strtoupper($nmCabang->KOTA)."-".$rsnmdiv->NAMA_DIV);	
				$this->template->load('default','fgaji_staf/ubudiah_staff_disabled',$data);
			}
		}
	}
	public function save_daily_report(){
		if($this->input->post()) {
			$this->load->library('form_validation');
			$rules = array();
			for($r=1;$r<=$this->input->post('jmlRow');$r++){	
				array_push($rules, array(
							'field' => 'jmlHari_'.$r,
							'label' => 'jmlHari_'.$r,
							'rules' => 'trim|xss_clean|required|numeric'));
				}
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi angka.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				try {
					$this->db->trans_begin();
					for($r=1;$r<=$this->input->post('jmlRow');$r++){
						$this->db->delete('daily_report', array("NIK"=>$this->input->post('nik_'.$r), 'BLN'=>$this->input->post('bln'), 'THN'=>$this->input->post('thn')));
						$this->db->trans_commit();
						$data = array(
									'BLN' => $this->input->post('bln'),
									'THN' => $this->input->post('thn'),
									'NIK' => $this->input->post('nik_'.$r),
									'JML_HARI' => $this->input->post('jmlHari_'.$r),
									'CREATED_BY' =>'admin',
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>'admin',
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
							);
						if ($this->db->insert('daily_report', $data)){
							$this->db->trans_commit();
						} else {
									throw new Exception("gagal simpan");
						}
					}
				}	catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			}else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
			//echo json_encode($respon)."<br>".$xGets;
			exit;

		}
		$this->template->set('pagetitle','Saving...');

	}

	public function notif_unset_gaji(){
		// query cek gaji staff yg blm di set
		/*$str="select c.KOTA, j.NAMA_JAB, d.NAMA_DIV, p.*
				from pegawai p, mst_cabang c, mst_jabatan j, mst_divisi d
				where p.ID_CABANG=c.ID_CABANG
				AND p.ID_DIV=d.ID_DIV
				AND p.ID_JAB=j.ID_JAB
				AND p.STATUS_AKTIF=1 
				AND p.ID_JAB<>13 AND p.ID_JAB<>14
				AND nik not in (select distinct(nik) from set_gaji_staff)
				order by p.ID_CABANG, p.ID_DIV, p.ID_JAB ";*/
		$str="select (select KOTA from mst_cabang where mst_cabang.ID_CABANG=pegawai.ID_CABANG) MY_CABANG,
				(select NAMA_DIV from mst_divisi where mst_divisi.ID_DIV=pegawai.ID_DIV) MY_DIVISI, 
				(select NAMA_JAB from mst_jabatan where mst_jabatan.ID_JAB=pegawai.ID_JAB) MY_JABATAN,
				pegawai.* 
				from pegawai 
				where nik not in (select distinct(nik) from set_gaji_staff) and status_aktif=1 and id_jab<>13 and id_jab<>14
				order by pegawai.ID_CABANG, pegawai.ID_DIV, pegawai.ID_JAB ";
		$rsRows = $this->db->query($str)->result();
		$strTunj = "select * from mst_komp_gaji where isactive=1 ";
		//group by cabang, div, jab?
		$data['rowBelum'] = $rsRows;
		$data['rsTunj'] = $this->db->query($strTunj)->result();
		$this->template->set('pagetitle','Form Master Gaji/Tunjangan Staff Yang belum diset');
		$this->template->load('default','fgaji_staf/notifSetGaji',$data);

	}
	
	public function save_ubudiah_staff(){
		if($this->input->post()) {
			$this->load->library('form_validation');
			$rules = array();
			for($r=1;$r<=$this->input->post('jmlRow');$r++){	
				array_push($rules, array(
							'field' => 'jmlHari_'.$r,
							'label' => 'jmlHari_'.$r,
							'rules' => 'trim|xss_clean|required|numeric'));
				}
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi angka.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				try {
					$this->db->trans_begin();
					for($r=1;$r<=$this->input->post('jmlRow');$r++){
						$this->db->delete('ubudiah_staff', array("NIK"=>$this->input->post('nik_'.$r), 'BLN'=>$this->input->post('bln'), 'THN'=>$this->input->post('thn')));
						$this->db->trans_commit();
						$data = array(
									'BLN' => $this->input->post('bln'),
									'THN' => $this->input->post('thn'),
									'NIK' => $this->input->post('nik_'.$r),
									'JML_HARI' => $this->input->post('jmlHari_'.$r),
									'CREATED_BY' =>'admin',
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>'admin',
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
							);
						if ($this->db->insert('ubudiah_staff', $data)){
							$this->db->trans_commit();
						} else {
									throw new Exception("gagal simpan");
						}
					}
				}	catch (Exception $e) {
					$respon->status = 'error';
					$respon->errormsg = $e->getMessage();
					$this->db->trans_rollback();
				}
				$respon->status = 'success';
			}else {
				$respon->status = 'error';
				$respon->errormsg = validation_errors();
				
			}
			echo json_encode($respon);
			//echo json_encode($respon)."<br>".$xGets;
			exit;

		}
		$this->template->set('pagetitle','Saving...');

	}
}
