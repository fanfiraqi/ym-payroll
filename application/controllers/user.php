<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class user extends MY_App {

	function __construct()
	{
		parent::__construct();
		$this->auth->authorize('index');
	}
	
	
	public function index()
	{
		if ($this->auth->is_login()){
			// query cek permohonan ijin baru
				$data['cekCuti']=0;
				if ($this->db->query('SELECT * FROM v_cuti  WHERE `ISACTIVE` = 1')->num_rows()>0){
					$data['cekCuti']=1;
					$data['rowCuti'] = $this->db->query('SELECT * FROM v_cuti  WHERE `ISACTIVE` = 1')->result();				
				}
				
				// query cek permohonan lembur baru
					$strLembur="SELECT p.NIK, p.NAMA, m.NO_TRANS, d.TGL_LEMBUR, d.JML_JAM, DATE_FORMAT(d.JAM_MULAI,'%H:%i:%s') MULAI, DATE_FORMAT(d.JAM_SELESAI,'%H:%i:%s') SELESAI
					FROM lembur m, `lembur_d` d, pegawai p
					WHERE m.no_trans=d.no_trans and p.nik=m.nik and m.`ISACTIVE` = 1";
				$data['cekLembur']=0;
				if ($this->db->query($strLembur)->num_rows()>0){
					$data['cekLembur']=1;
					$data['rowLembur'] = $this->db->query($strLembur)->result();
				}
								
			
				$strKontrak = "SELECT P.NIK, P.NAMA, JENIS,g.VALUE1, TGL_AWAL_KONTRAK, TGL_AKHIR_KONTRAK TGL_AKHIR, DATEDIFF(TGL_AKHIR_KONTRAK, NOW()) SISA
						FROM pegawai P, kontrak K, gen_reff g
						WHERE P.NIK=K.NIK 
						AND K.JENIS=g.ID_REFF
						AND g.REFF='STSPEGAWAI'
						AND STATUS_AKTIF=1
						AND K.ISACTIVE=1
						AND (DATEDIFF(TGL_AKHIR_KONTRAK, NOW()) >=1 AND DATEDIFF(TGL_AKHIR_KONTRAK, NOW())<=14) ";
				$data['cekKontrak']=0;
				if ($this->db->query($strKontrak)->num_rows()>0){
					$data['cekKontrak']=1;
					$data['rowKontrak'] = $this->db->query($strKontrak)->result();
					$data['strKontrak'] = $strKontrak;
				}

				
			$this->template->set('pagetitle','Dashboard');			
			$this->template->load('default','user/index',$data);
		} else {
			redirect($this->config->item('gate_link')."/keluar");
		}
	}


	public function xlsCuti(){
		// query cek permohonan ijin baru			
		$result = $this->db->query('SELECT * FROM v_cuti  WHERE `ISACTIVE` = 1')->result();					

		//judul file XLS
		$title = "DATA PERMOHONAN CUTI";
		
		// header tabel
		$headertext = array(
			'NO.',
			'NIK',
			'NAMA',
			'CABANG',
			'DIVISI',
			'JABATAN',
			'TGL IJIN AWAL',
			'TGL IJIN AKHIR',
			'KETERANGAN'
		);
		
		//nama field yg akan ditampilkan. Kolom NO. pada header otomatis (+1)
		$rowitem = array(
			'NIK',
			'NAMA',
			'NAMA_CABANG',
			'NAMA_DIV',
			'NAMA_JAB',
			'TGL_AWAL',
			'TGL_AKHIR',
			'JENISCUTI2'
		);
		$xlsfile = "DATA_CUTI.xls";
		
		if (!empty($result)){
			$this->commonlib->printXLS($title,$result,$headertext,$rowitem,$xlsfile);
		} else {
			echo "XLSX Failed. No Valid Data";
		}

	}


	public function xlsLembur(){
		$strLembur="SELECT p.NIK, p.NAMA, m.NO_TRANS, d.TGL_LEMBUR, d.JML_JAM, DATE_FORMAT(d.JAM_MULAI,'%H:%i:%s') MULAI, DATE_FORMAT(d.JAM_SELESAI,'%H:%i:%s') SELESAI
		FROM lembur m, `lembur_d` d, pegawai p
		WHERE m.no_trans=d.no_trans and p.nik=m.nik and m.`ISACTIVE` = 1";
				
		$result = $this->db->query($strLembur)->result();					

		//judul file XLS
		$title = "DATA PERMOHONAN LEMBUR";
		
		// header tabel
		$headertext = array(
			'NO.',
			'NIK',
			'NAMA',
			'TGL LEMBUR',
			'MULAI',
			'SELESAI',
			'JML JAM'
		);
		
		//nama field yg akan ditampilkan. Kolom NO. pada header otomatis (+1)
		$rowitem = array(
			'NIK',
			'NAMA',
			'TGL_LEMBUR',
			'MULAI',
			'SELESAI',
			'JML_JAM'
		);
		$xlsfile = "DATA_LEMBUR.xls";
		
		if (!empty($result)){
			$this->commonlib->printXLS($title,$result,$headertext,$rowitem,$xlsfile);
		} else {
			echo "XLSX Failed. No Valid Data";
		}

	}

	public function xlsUltah(){
		$strUltah="SELECT p.NIK COL1, p.NAMA COL2, p.TGL_LAHIR, concat('Karyawan cabang : ', mc.kota,' - ', md.nama_div,' - ', mj.nama_jab) KET, p.EMAIL
							FROM pegawai p, mst_cabang mc, mst_divisi md, mst_jabatan mj
							WHERE p.id_cabang=mc.id_cabang and p.status_aktif=1 and
							p.id_div=md.id_div and
							p.id_jab=mj.id_jab and
							date_format(tgl_lahir, '%m')='".date('m')."'
							UNION
							SELECT ' ' COL1, adm.NAMA COL2, adm.TGL_LAHIR, concat( gen.value1, ' dari ', p.NAMA, '-', p.NIK ) KET, p.EMAIL
							FROM pegawai p, adm_hubkel adm, gen_reff gen 
							WHERE p.nik = adm.nik and p.status_aktif=1 
							AND gen.id_reff = adm.id_hubkel
							AND gen.reff = 'KELUARGA'
							AND date_format( adm.tgl_lahir, '%m' ) = '".date('m')."'";
			
		$result = $this->db->query($strUltah)->result();					

		//judul file XLS
		$title = "DATA KARYAWAN ULANG TAHUN";
		
		// header tabel
		$headertext = array(
			'NO.',
			'NIK',
			'NAMA',
			'TGL LAHIR',
			'KETERANGAN',
			'EMAIL'
		);
		
		//nama field yg akan ditampilkan. Kolom NO. pada header otomatis (+1)
		$rowitem = array(		
			'COL1',
			'COL2',
			'TGL_LAHIR',
			'KET',
			'EMAIL'
		);
		$xlsfile = "DATA_ULTAH_".date('m').".xls";
		
		if (!empty($result)){
			$this->commonlib->printXLS($title,$result,$headertext,$rowitem,$xlsfile);
		} else {
			echo "XLSX Failed. No Valid Data";
		}

	}

	public function xlsJatahCuti(){
		$strJatahCuti="SELECT p.NIK, NAMA, TGL_AKTIF,  period_diff(date_format(now(),'%Y%m'), date_format(tgl_aktif, '%Y%m')) MASA,concat('Karyawan cabang : ', mc.kota,' - ', md.nama_div,' - ', mj.nama_jab) KET
							FROM pegawai p, mst_cabang mc, mst_divisi md, mst_jabatan mj
							WHERE  p.id_cabang=mc.id_cabang and p.id_div=md.id_div and
							p.id_jab=mj.id_jab and p.nik not in (select distinct nik from cuti) and status_aktif=1
							and (period_diff(date_format(now(),'%Y%m'), date_format(tgl_aktif, '%Y%m'))) > 15 order by TGL_AKTIF desc ";
		$result = $this->db->query($strJatahCuti)->result();					

		//judul file XLS
		$title = "DATA KARYAWAN MULAI DAPAT JATAH CUTI (15 BLN DARI TGL MASUK)";
		
		// header tabel
		$headertext = array(
			'NO.',
			'NIK',
			'NAMA',
			'POSISI',
			'TGL MULAI KERJA',
			'MASA KERJA(BLN)'
		);
		
		//nama field yg akan ditampilkan. Kolom NO. pada header otomatis (+1)
		$rowitem = array(		
			'NIK',
			'NAMA',
			'KET',
			'TGL_AKTIF',
			'MASA'
		);
		$xlsfile = "DATA_DAPAT_JATAH_CUTI.xls";
		
		if (!empty($result)){
			$this->commonlib->printXLS($title,$result,$headertext,$rowitem,$xlsfile);
		} else {
			echo "XLSX Failed. No Valid Data";
		}

	}

	public function xlsKontrak(){
		$strKontrak = "SELECT P.NIK, P.NAMA, JENIS,g.VALUE1, TGL_AWAL_KONTRAK, TGL_AKHIR_KONTRAK TGL_AKHIR, DATEDIFF(TGL_AKHIR_KONTRAK, NOW()) SISA
						FROM pegawai P, kontrak K, gen_reff g
						WHERE P.NIK=K.NIK 
						AND K.JENIS=g.ID_REFF
						AND g.REFF='STSPEGAWAI'
						AND STATUS_AKTIF=1
						AND K.ISACTIVE=1
						AND (DATEDIFF(TGL_AKHIR_KONTRAK, NOW()) >=1 AND DATEDIFF(TGL_AKHIR_KONTRAK, NOW())<=14) ";
					
		$result = $this->db->query($strKontrak)->result();					

		//judul file XLS
		$title = "DATA JATUH TEMPO & UPGRADE MASA KONTRAK";
		
		// header tabel
		$headertext = array(
			'NO.',
			'NIK',
			'NAMA',
			'JENIS KONTRAK',
			'TGL AKHIR',
			'SISA HARI'
		);
		
		//nama field yg akan ditampilkan. Kolom NO. pada header otomatis (+1)
		$rowitem = array(		
			'NIK',
			'NAMA',
			'VALUE1',
			'TGL_AKHIR',
			'SISA'
		);
		$xlsfile = "DATA_DAPAT_JATAH_CUTI.xls";
		
		if (!empty($result)){
			$this->commonlib->printXLS($title,$result,$headertext,$rowitem,$xlsfile);
		} else {
			echo "XLSX Failed. No Valid Data";
		}

	}
	
}
