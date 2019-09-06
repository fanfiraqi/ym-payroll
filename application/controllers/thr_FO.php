<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class thr_FO extends MY_App {

	function __construct()
	{
		parent::__construct();
		$this->load->model('thr_model');
		$this->config->set_item('mymenu', 'menuTujuhDua');
		$this->load->helper('array');
		$this->load->helper('download');
		$this->load->dbutil();
	    $this->load->helper('file');
		$this->load->database();
		$this->auth->authorize();
	}
	
	public function index()
	{
		$this->template->set('pagetitle','Set Master THR FO');
		$data['row'] = $this->db->query("select * from mst_gaji_fo ")->result();		
		$this->template->load('default','fthr_FO/index',$data);
	}
	
	public function save_master(){
		$this->template->set('pagetitle','Saving...');
		
		if($this->input->post()) {		
			$this->load->library('form_validation');
			$rules = array();
			for($r=1;$r<=$this->input->post('jmlRow');$r++){	
				array_push($rules, array(
							'field' => 'nominalTHR_'.$r,
							'label' => 'nominalTHR_'.$r,
							'rules' => 'trim|xss_clean|required|numerical'));					
				
			}
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('required', 'Field %s harus diisi angka.');
			$respon = new StdClass();
			if ($this->form_validation->run() == TRUE){
				try {
					$this->db->trans_begin();
					$this->db->query("delete from mst_thr_fo");
					$this->db->trans_commit();

					for ($x=1; $x<=$this->input->post('jmlRow');$x++){
												
								$data = array(
									'ID_LEVEL' => $this->input->post('idLevel_'.$x),
									'NOMINAL_THR' => $this->input->post('nominalTHR_'.$x)
								);
								if ($this->db->insert('mst_thr_fo', $data)){
									$this->db->trans_commit();
								} else {
									throw new Exception("gagal simpan");
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
			exit;
		}


		
	}
	
	public function form()
	{
		$this->template->set('pagetitle','Form Daftar THR FO');
		$data['cabang'] = $this->common_model->comboCabang();
		$data['thn'] = $this->getYearArr();
		
		$this->template->load('default','fthr_FO/filterForm',$data);
	}

	public function thr_list(){
		$this->template->set('pagetitle','Daftar THR FO');		
		//query pegawai FO, FOFR not included
		$tahun=$this->input->post('tahun');		
		$id_cab=$this->input->post('id_cabang');
		
		
		$strList = "SELECT p.*, ev.ID_LEVEL, (select level from mst_gaji_fo where id=ev.id_level) NAMA_LEVEL, j.NAMA_JAB, period_diff( date_format( now( ) , '%Y%m' ) , date_format( tgl_aktif, '%Y%m' ) ) SELISIH from pegawai p, mst_jabatan j, evaluasi_fo ev where p.nik=ev.nik and p.id_jab=j.id_jab and status_aktif=1 and id_cabang=$id_cab and  p.id_jab =14 and ev.status_eval=1";

		$strCek="select count(*) CEK from thr_fo where tahun='$tahun' and nik in (select nik from pegawai where status_aktif=1 and id_cabang=$id_cab  and  id_jab =14)";
		//get tunjangan anak

		$rsCek = $this->db->query($strCek)->row();
		$data['cek']  = $this->db->query($strCek)->row();
		$data['row'] = $this->db->query($strList)->result();		
		$data['tahun'] = $tahun;		
		$data['id_cabang'] = $id_cab;
		
		$data['str'] = $strList;

		$nmCabang = $this->db->query("select KOTA from mst_cabang where id_cabang=".$this->input->post('id_cabang'))->row();
		
		if ($rsCek->CEK<=0){
			$title="Daftar Data THR FO (NEW)";
			$sts="new";
		}else{			
			if (date('Y')<=$tahun){	
				//boleh edit, ambil data master
				$title="Daftar Data THR FO (EDIT)";
				$sts="edit";
			}else{
				$title="Daftar Data THR FO (DISABLED)";
				$sts="disabled";
			}
		}
		
		$data['sts']=$sts;
		$this->template->set('pagetitle',$title." ".$tahun." Cabang ".strtoupper($nmCabang->KOTA));	
		$this->template->load('default','fthr_FO/thr_FO_list',$data);
	}


	public function save_thr_FO(){
		if($this->input->post()) {
			$this->load->library('form_validation');
			$rules = array();
			for($r=1;$r<=$this->input->post('jmlRow');$r++){	
				if ($this->input->post('flag_'.$r)=="1"){
					array_push($rules, array(
							'field' => 'thrnya_'.$r,
							'label' => 'thrnya_'.$r,
							'rules' => 'trim|xss_clean|required|numeric'));					
				}
			}
			
			$this->form_validation->set_rules($rules);
			//$out=$this->form_validation->run();
			$this->form_validation->set_message('numeric', 'Field %s harus diisi angka.');
			$respon = new StdClass();
			//$xGets="mulai";
			if ($this->form_validation->run() == TRUE){
				//$xGets.="masuk run";
				try {
					$this->db->trans_begin();
					for($r=1;$r<=$this->input->post('jmlRow');$r++){
						if ($this->input->post('flag_'.$r)=="1"){
						$this->db->delete('thr_fo', array("NIK"=>$this->input->post('nik_'.$r), 'TAHUN'=>$this->input->post('tahun')));
							$this->db->trans_commit();
							$data = array(									
									'TAHUN' => $this->input->post('tahun'),
									'NIK' => $this->input->post('nik_'.$r),
									'MASA_KERJA' => $this->input->post('masakerja_'.$r),
									'ID_LEVEL' => $this->input->post('idlevel_'.$r),
									'NOMINAL_THR' => $this->input->post('thrnya_'.$r),
									'CREATED_BY' =>'admin',
									'CREATED_DATE' =>date('Y-m-d H:i:s'),
									'UPDATED_BY' =>'admin',
									'UPDATED_DATE' =>date('Y-m-d H:i:s')
								);
								if ($this->db->insert('thr_fo', $data)){									
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
		$id_cabang=$this->input->post('id_cabang');
		$id_divisi=9;

		$rsnmcab=$this->db->query("select KOTA from mst_cabang where id_cabang=$id_cabang")->row();
		
		$rsRekNH=$this->db->query("select VALUE2 from params where id=1")->row();
		$total=$this->db->query("select sum(NOMINAL_THR) TOT, count(*) CNT from thr_fo where  TAHUN='$thn' and nik in (select distinct nik from pegawai where id_cabang=$id_cabang  and id_JAB=14 and status_aktif=1)")->row();

		$out=$rsRekNH->VALUE2.",YAYASAN NURUL HAYAT SURABAYA,IDR,".$total->TOT.",THR FO ".$rsnmcab->KOTA."  ".$thn.",".$total->CNT.",".$thn.",,,"."\r\n";
		//norek yayasan
		
		//row thr FO
		$strList="SELECT p.NIK, p.REKENING,  p.NAMA, gs.NOMINAL_THR
			FROM `pegawai` p, thr_fo gs
			WHERE p.nik=gs.nik  and tahun='$thn' and p.nik in (select distinct nik from pegawai where id_cabang=$id_cabang  and id_JAB=14 and status_aktif=1) ";
		$rsRes=$this->db->query($strList)->result();
		foreach($rsRes as $row){
			$out.=$row->REKENING.",".strtoupper($row->NAMA).",IDR,".$row->NOMINAL_THR.",PAYROLL,,"."\r\n";
		}
		//cek dir
		$path=$this->createPath('csv_thr',$id_cabang, $id_divisi, $thn, date('m') );	//csv path hanya s.d thn masuk nama file
		$fileName="thr_FO ".strtoupper($rsnmcab->KOTA)." ".$thn.".csv";
		write_file($path."/".$fileName,$out);	

		//SIMPAN RECORD
		$strcek="select * from file_csv_thr where id_cab=$id_cabang and id_div=$id_divisi and thn='$thn' and bln='".date('m')."' and NAMA_FILE='$fileName'";
		$cek=$this->db->query($strcek)->num_rows();
		if ($cek>0){
			$data = array('PATH' => $path,'NAMA_FILE' => $fileName, 'UPDATED_BY' =>'admin', 'UPDATED_DATE' =>date('Y-m-d H:i:s')	);
			$this->db->where(array('id_cab'=>$id_cabang, 'id_div'=> $id_divisi,  'thn'=>$thn,  'bln'=>date('m')))->update('file_csv_thr', $data);
		}else{
			$data = array(
						'ID_CAB' => $id_cabang,
						'ID_DIV' => $id_divisi,
						'THN' => $thn,
						'BLN' => date('m'),
						'PATH' => $path,
						'NAMA_FILE' => $fileName,
						'CREATED_BY' =>'admin',
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>'admin',
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
		$nik=$arrKey[1];
		$str="SELECT p.NIK,p.TGL_AKTIF, p.NAMA, p.ID_CABANG, p.ID_DIV, mst.NAMA_JAB, gs. *
			FROM `thr_fo` gs, pegawai p, mst_jabatan mst
			WHERE gs.nik = p.nik and p.id_jab=mst.id_jab and gs.TAHUN='".$arrKey[0]."' and gs.nik='".$nik."'";		
		$rsOut=$this->db->query($str)->row();
		
		$data['str']=$str;		
		$data['thn']=$arrKey[0];
		
		
		$jmlbln=$rsOut->MASA_KERJA;
		$masaKerja="";
		//hitung masa kerja
		if ($jmlbln<12){				
			$masaKerja=number_format($jmlbln,0,',','')." Bln";	
			
		}else{
			$masaKerja=floor($jmlbln/12)." Thn, ".($jmlbln%12)." Bln";
			
		}		
		
		$levelFO=$this->db->query("select LEVEL from mst_gaji_fo where ID=".$rsOut->ID_LEVEL)->row();

		$html=$this->commonlib->pdfHeadertemplate();
		//$html=$this->commonlib->reportHeader();
		$html.="<table border=0><tr><td><img src=\"".base_url('assets/css/logoNH.gif')."\" width=\"100\"></td></tr></table>";
		$html.="<table class=\"mydata\" >";
		$html.="<tr><th COLSPAN=4>SLIP THR</th></tr>";
		$html.="<tr><tD colspan=2>NAMA</td><tD>:</td><tD>".$rsOut->NAMA."</td></tr>";
		$html.="<tr><tD colspan=2>JABATAN</td><tD>:</td><tD>FO LEVEL ".$levelFO->LEVEL."</td></tr>";
		$html.="<tr><tD colspan=2>THR TAHUN</td><tD>:</td><tD>$thn</td></tr>";		
		$html.="<tr><th COLSPAN=3>JUMLAH THR</th><th style=\"text-align:right\">Rp. ".number_format($rsOut->NOMINAL_THR,0,',','.')."</th></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();
		//echo $html;
		//$html = $this->load->view('fgaji_fo/fo_slip', $data, true);
		//$namafile="slip_gs_".$arrKey[0]."_".$arrKey[1]."_".$nik;

		$path=$this->createPath('slip_thr','', '', $thn, date('m') );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP THR FO ".$thn."-".$nik.".pdf";		
		
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
						'CREATED_BY' =>'admin',
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>'admin',
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
		$id_cab=$this->input->get('cabang');
		$id_div=9;		
		$thn=$this->input->get('thn');
		$str = "select * from thr_fo where TAHUN='$thn' and nik in(select distinct nik from pegawai where id_cabang=$id_cab and  status_aktif=1 and id_jab=14) ORDER BY `NIK` ";
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
				$param=$thn."_".$result->NIK;
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
		$str="SELECT p.NIK,p.TGL_AKTIF, p.NAMA, p.ID_CABANG, p.ID_DIV, mst.NAMA_JAB, gs. *
			FROM `thr_fo` gs, pegawai p, mst_jabatan mst
			WHERE gs.nik = p.nik and p.id_jab=mst.id_jab and gs.TAHUN='".$arrKey[0]."' and gs.nik='".$nik."'";		
		$rsOut=$this->db->query($str)->row();
		
		$data['str']=$str;		
		$data['thn']=$arrKey[0];
		
		
		$jmlbln=$rsOut->MASA_KERJA;
		$masaKerja="";
		//hitung masa kerja
		if ($jmlbln<12){				
			$masaKerja=number_format($jmlbln,0,',','')." Bln";	
			
		}else{
			$masaKerja=floor($jmlbln/12)." Thn, ".($jmlbln%12)." Bln";
			
		}	
		$levelFO=$this->db->query("select LEVEL from mst_gaji_fo where ID=".$rsOut->ID_LEVEL)->row();

		$html=$this->commonlib->pdfHeadertemplate();
		//$html=$this->commonlib->reportHeader();
		$html.="<table border=0><tr><td><img src=\"".base_url('assets/css/logoNH.gif')."\" width=\"100\"></td></tr></table>";
		$html.="<table class=\"mydata\" >";
		$html.="<tr><th COLSPAN=4>SLIP THR</th></tr>";
		$html.="<tr><tD colspan=2>NAMA</td><tD>:</td><tD>".$rsOut->NAMA."</td></tr>";
		$html.="<tr><tD colspan=2>JABATAN</td><tD>:</td><tD>FO LEVEL ".$levelFO->LEVEL."</td></tr>";
		$html.="<tr><tD colspan=2>THR TAHUN</td><tD>:</td><tD>$thn</td></tr>";		
		$html.="<tr><th COLSPAN=3>JUMLAH THR</th><th style=\"text-align:right\">Rp. ".number_format($rsOut->NOMINAL_THR,0,',','.')."</th></tr>";
		$html.="<tr><td COLSPAN=4 >MASA KERJA : $masaKerja </td></tr>";		
		$html.="</table>";
		
		$html.=$this->commonlib->pdfFooterTemplate();
		//echo $html;
		//$html = $this->load->view('fgaji_fo/fo_slip', $data, true);
		//$namafile="slip_gs_".$arrKey[0]."_".$arrKey[1]."_".$nik;

		$path=$this->createPath('slip_thr','', '', $thn, date('m') );	//csv path hanya s.d thn masuk nama file
		$fileName="SLIP THR FO ".$thn."-".$nik.".pdf";		
		
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
						'CREATED_BY' =>'admin',
						'CREATED_DATE' =>date('Y-m-d H:i:s'),
						'UPDATED_BY' =>'admin',
						'UPDATED_DATE' =>date('Y-m-d H:i:s')
					);
			$this->db->insert('file_slip_thr', $data);
			
		}
		$this->db->trans_commit();
		$data['isi']=$path."/".$fileName;
		//echo json_encode($data);
		$this->db->trans_commit();
		return 1;
		//$data['isi']=$path."/".$fileName;
		//echo json_encode($data);

	}



}
