<?php
			
class report_model extends MY_Model {
	function view_prestasi($nik=null){
		$str = "SELECT * from prestasi
				WHERE NIK='".$nik."' ORDER BY ID ASC";
		return $this->db->query($str)->result();		
	}
	function view_pelatihan($nik=null){
		$str = "SELECT * from pelatihan
				WHERE NIK='".$nik."' ORDER BY ID ASC";
		return $this->db->query($str)->result();		
	}
	function view_pelanggaran($nik=null){
		$str = "SELECT * from pelanggaran
				WHERE NIK='".$nik."' ORDER BY ID ASC";
		return $this->db->query($str)->result();		
	}
	function view_resign($nik=null){
		$str = "SELECT * from resign
				WHERE NIK='".$nik."' ORDER BY ID ASC";
		return $this->db->query($str)->result();		
	}
	function view_mutasi($nik=null){
		$str = "SELECT  p.NAMA, concat(c.KOTA,' - ', d.NAMA_DIV, ' - ',j.NAMA_JAB) MUTASI_BARU,
						CONCAT((select kota from mst_cabang where id_cabang=m.old_id_cab) , ' - ',
						(select nama_div from mst_divisi where id_div=m.old_id_div) , ' - ',
						(select nama_jab from mst_jabatan where id_jab=m.old_id_jab) ) MUTASI_LAMA,
						m.*
						FROM mutasi m, pegawai p, mst_cabang c, mst_divisi d, mst_jabatan j
						WHERE m.nik=p.nik  and m.id_cab=c.id_cabang and m.id_div=d.id_div and m.id_jab=j.id_jab and m.NIK='$nik' order by m.ID desc";
		return $this->db->query($str)->result();		
	}
	function view_lembur($nik=null){
		$str = "SELECT l.NIK, APPROVED, APPROVED_BY, APPROVED_DATE, CREATED_DATE, d.*
				FROM lembur l, lembur_d d
				where l.no_trans=d.no_trans
				and l.nik='$nik'";
		return $this->db->query($str)->result();		
	}
	function view_cuti($nik=null){
		$str = "SELECT * from v_cuti
				WHERE NIK='".$nik."' ORDER BY ID ASC";
		return $this->db->query($str)->result();		
	}
	
	function getArr_cabang(){
		$str = "SELECT distinct mst.ID_CAB, mc.KOTA from mst_struktur mst left join mst_cabang mc on mst.id_cab=mc.id_cabang
				ORDER BY mc.id_cabang ASC";
		return $this->gate_db->query($str)->result();		
	}
	function getArr_divisi(){
		$str = "SELECT distinct mst.ID_DIV,md.NAMA_DIV from mst_struktur mst left join mst_divisi md on mst.id_div=md.id_div
				ORDER BY md.id_div ASC";
		return $this->db->query($str)->result();		
	}
	function getArr_divAtCab($id_cab){
		$str = "SELECT distinct mst.ID_DIV, md.NAMA_DIV from mst_struktur mst inner join mst_divisi md on mst.id_div=md.id_div
				and mst.id_cab=$id_cab and md.id_div<>1
				ORDER BY md.id_div ASC";
		return $this->gate_db->query($str)->result();		
	}
	
	function get_cabang($id_cab){
		$str = "SELECT ID_CABANG, KOTA from mst_cabang where id_cabang=$id_cab";
		return $this->gate_db->query($str)->row();		
	}

	function getDiv_payroll($id_cab){
		$str = "SELECT distinct md.idLev1, md.lev1, md.idLev2, md.lev2, md.idLev3, md.lev3
				from mst_struktur mst right join 
				(SELECT t1.id_div idLev1, t1.nama_div AS lev1, t2.id_div idLev2,t2.nama_div as lev2, t3.id_div idLev3,t3.nama_div as lev3
				FROM mst_divisi AS t1
				LEFT JOIN mst_divisi AS t2 ON t2.id_div_parent = t1.id_div
				LEFT JOIN mst_divisi AS t3 ON t3.id_div_parent = t2.id_div
				WHERE t1.id_div=1) md on (mst.id_div=md.idLev3 or  mst.id_div=md.idLev2 or   mst.id_div=md.idLev2)
				and mst.id_cab=$id_cab
				ORDER BY md.idLev2,md.idLev3 ASC";
		return $this->db->query($str)->result();		
	}

	function getStaff_payroll($id_cab, $thn, $bln){
				$str="select sum(gapok) KEL1, (sum(T_JABATAN)+sum(T_MASAKERJA)+sum(T_MAKAN)+sum(T_TRANSPORT)+sum(T_FUNGSIONAL)) KEL2, sum(TUNJ_UBUDIAH) KEL_ANYAR, 0 KEL4, sum(T_ANAK) KEL3, (sum(JML_POT_GAPOK)+sum(JML_ANGSURAN)) KEL5, sum(SEDEKAH_TAAWUN) KEL6, SUM(SUBSIDI_JHT) KEL7, SUM(PREMI_JHT) KEL8
					from gaji_staff g, (SELECT  p.NIK, p.NAMA, p.id_div, p.ID_JAB
					FROM mst_struktur mst, pegawai p
					WHERE p.ID_CABANG = mst.ID_CAB
					AND p.ID_DIV = mst.ID_DIV
					AND p.ID_JAB = mst.ID_JAB
					and p.status_aktif=1 and p.id_jab not in(13, 14)
					AND p.id_div  in (SELECT id_div FROM mst_divisi WHERE (id_div_parent IN (1,2)) OR (id_div IN (1,2)))
					and p.id_cabang=$id_cab) s
					where g.nik=s.nik and g.bln='$bln' and g.thn='$thn'";
			return $this->db->query($str)->row();	
	}
	function getFO_payroll($id_cab,  $thn, $bln){
			$str="select sum(gapok) KEL1, sum(TRANSPORT) KEL2,sum(TUNJ_UBUDIAH) KEL_ANYAR, sum(TUNJ_ANAK) KEL3,  (sum(INSENTIF_50)+sum(BONUS_100_80)) KEL4,sum(JML_ANGSURAN) KEL5, sum(SEDEKAH_TAAWUN) KEL6, SUM(SUBSIDI_JHT) KEL7, SUM(PREMI_JHT) KEL8
					from gaji_fo g, (SELECT MST.ID_JAB, P.NIK, P.NAMA
					FROM mst_struktur MST, pegawai P
					WHERE P.ID_CABANG = MST.ID_CAB
					AND P.ID_DIV = MST.ID_DIV
					AND P.ID_JAB = MST.ID_JAB
					and P.status_aktif=1 and P.id_jab=14
					and P.id_cabang=$id_cab ) s
					where g.nik=s.nik and g.bln='$bln' and g.thn='$thn'";
			return $this->db->query($str)->row();	
	}
	function getFR_payroll($id_cab,  $thn, $bln){
			$strTermin1="select sum(GAPOK) KEL1, sum(SERVICE) KEL2, sum(TUNJ_UBUDIAH) KEL_ANYAR, sum(TUNJ_ANAK) KEL3,  0 KEL4,sum(JML_ANGSURAN) KEL5, sum(SEDEKAH_TAAWUN) KEL6, SUM(SUBSIDI_JHT) KEL7, SUM(PREMI_JHT) KEL8
				from gaji_fr_termin1 g, (SELECT MST.ID_JAB, P.NIK, P.NAMA
				FROM mst_struktur MST, pegawai P
				WHERE P.ID_CABANG = MST.ID_CAB
				AND P.ID_DIV = MST.ID_DIV
				AND P.ID_JAB = MST.ID_JAB
				and P.status_aktif=1 and P.id_jab=13
				and P.id_cabang=$id_cab ) s
				where g.nik=s.nik and g.bln='$bln' and g.thn='$thn'";
			$rsTermin1=$this->db->query($strTermin1)->row();
			
			$strTermin2="select (sum(BONUS_DL)+SUM(BONUS_DB)+SUM(BONUS_INS)) BON1, (SUM(TUNJ_KK)+SUM(TUNJ_PRESTASI)+SUM(TUNJ_TRANSPORT)) TUNJ
				from gaji_fr_termin2 g, (SELECT MST.ID_JAB, P.NIK, P.NAMA
				FROM mst_struktur MST, pegawai P
				WHERE P.ID_CABANG = MST.ID_CAB
				AND P.ID_DIV = MST.ID_DIV
				AND P.ID_JAB = MST.ID_JAB
				and P.status_aktif=1 and P.id_jab=13
				and P.id_cabang=$id_cab ) s
				where g.nik=s.nik and g.bln='$bln' and g.thn='$thn'";
			$rsTermin2=$this->db->query($strTermin2)->row();

			$hasil=array(
				'KEL1'=>$rsTermin1->KEL1,
				'KEL2'=>$rsTermin1->KEL2+$rsTermin2->TUNJ,
				'KEL3'=>$rsTermin1->KEL3,
				'KEL4'=>$rsTermin2->BON1,
				'KEL5'=>$rsTermin1->KEL5,
				'KEL6'=>$rsTermin1->KEL6,
				'KEL7'=>$rsTermin1->KEL7,
				'KEL8'=>$rsTermin1->KEL8,
				'KEL_ANYAR'=>$rsTermin1->KEL_ANYAR
			);
			return (object)$hasil;
			//return $hasil;

	}
	function getUsaha_payroll($id_cab,$id_div, $thn, $bln){
				$str="select sum(gapok) KEL1, (sum(T_JABATAN)+sum(T_MASAKERJA)+sum(T_MAKAN)+sum(T_TRANSPORT)+sum(T_FUNGSIONAL)) KEL2, sum(TUNJ_UBUDIAH) KEL_ANYAR, 0 KEL4, sum(T_ANAK) KEL3, (sum(JML_POT_GAPOK)+sum(JML_ANGSURAN)) KEL5, sum(SEDEKAH_TAAWUN) KEL6, SUM(SUBSIDI_JHT) KEL7, SUM(PREMI_JHT) KEL8
					from gaji_staff g, (SELECT  P.NIK, P.NAMA, P.id_div, P.ID_JAB
					FROM mst_struktur MST, pegawai P
					WHERE P.ID_CABANG = MST.ID_CAB
					AND P.ID_DIV = MST.ID_DIV
					AND P.ID_JAB = MST.ID_JAB
					and P.status_aktif=1 and P.id_jab not in(13, 14)
					AND P.id_div=$id_div
					and P.id_cabang=$id_cab) s
					where g.nik=s.nik and g.bln='$bln' and g.thn='$thn'";
			return $this->db->query($str)->row();	
	}

	
	function myStr($id_cab, $id_div){
				$str="select sum(gapok) KEL1, sum(TRANSPORT) KEL2, (sum(INSENTIF_50)+sum(BONUS_100_80)) KEL3, sum(TUNJ_ANAK) KEL4, sum(JML_ANGSURAN) KEL5
					from gaji_fo g, (SELECT MST.ID_JAB, P.NIK, P.NAMA
					FROM mst_struktur MST, pegawai P
					WHERE P.ID_CABANG = MST.ID_CAB
					AND P.ID_DIV = MST.ID_DIV
					AND P.ID_JAB = MST.ID_JAB
					and P.status_aktif=1 and P.id_jab=14
					and P.id_cabang=$id_cab and P.id_div=$id_div) s
					where g.nik=s.nik and g.bln='$bln' and g.thn='$thn'";
			return $str;	
	}

	function getStaff_thr($id_cab, $thn){
				$str="select sum(NOMINAL_THR) TOT 
					from thr_staff g, (SELECT  p.NIK, p.NAMA, p.id_div, p.ID_JAB
					FROM mst_struktur mst, pegawai p
					WHERE p.ID_CABANG = mst.ID_CAB
					AND p.ID_DIV = mst.ID_DIV
					AND p.ID_JAB = mst.ID_JAB
					and p.status_aktif=1 and p.id_jab not in(13, 14)
					AND p.id_div  in (SELECT id_div FROM mst_divisi WHERE (id_div_parent IN (1,2)) OR (id_div IN (1,2)))
					and p.id_cabang=$id_cab) s
					where g.nik=s.nik and g.tahun='$thn'";
			return $this->db->query($str)->row();	
	}
	function getFO_thr($id_cab,  $thn){
			$str="select sum(NOMINAL_THR) TOT 
					from thr_fo g, (SELECT MST.ID_JAB, P.NIK, P.NAMA
					FROM mst_struktur MST, pegawai P
					WHERE P.ID_CABANG = MST.ID_CAB
					AND P.ID_DIV = MST.ID_DIV
					AND P.ID_JAB = MST.ID_JAB
					and P.status_aktif=1 and P.id_jab=14
					and P.id_cabang=$id_cab ) s
					where g.nik=s.nik and g.tahun='$thn'";
			return $this->db->query($str)->row();	
	}
	function getFR_thr($id_cab,  $thn){
			$strTermin1="select sum(NOMINAL_THR) TOT 
				from thr_fr g, (SELECT MST.ID_JAB, P.NIK, P.NAMA
				FROM mst_struktur MST, pegawai P
				WHERE P.ID_CABANG = MST.ID_CAB
				AND P.ID_DIV = MST.ID_DIV
				AND P.ID_JAB = MST.ID_JAB
				and P.status_aktif=1 and P.id_jab=13
				and P.id_cabang=$id_cab ) s
				where g.nik=s.nik and g.tahun='$thn'";
			return $rsTermin1=$this->db->query($strTermin1)->row();
			
	}
	function getUsaha_thr($id_cab,$id_div, $thn){
				$str="select sum(NOMINAL_THR) TOT 
					from thr_staff g, (SELECT  P.NIK, P.NAMA, P.id_div, P.ID_JAB
					FROM mst_struktur MST, pegawai P
					WHERE P.ID_CABANG = MST.ID_CAB
					AND P.ID_DIV = MST.ID_DIV
					AND P.ID_JAB = MST.ID_JAB
					and P.status_aktif=1 and P.id_jab not in(13, 14)
					AND P.id_div=$id_div
					and P.id_cabang=$id_cab) s
					where g.nik=s.nik and g.tahun='$thn'";
			return $this->db->query($str)->row();	
	}
}
