<?php echo form_open(null,array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
	<!-- <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	Informasi : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Open = Sudah ada data yang disimpan dan masih bisa diedit. <br>
	&nbsp;&nbsp;3. Closed = Melewati Periode aktif<br>
	"Slip-Email All" yang dikirim hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut terkirim<br>
	
	</div> -->
	
					<div class="table-responsive">
					    <input type="hidden" name="thn" id="thn" value="<?=$thn?>">	
										<input type="hidden" name="bln" id="bln" value="<?=$bln?>">	
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">
										<input type="hidden" name="id_validasi" id="id_validasi" value="<?=$id_validasi?>">
										<input type="hidden" name="id_cabang" id="id_cabang" value="<?=$id_cabang?>">
									
						<table class="table table-striped table-bordered table-hover"  style="max-height:650px;overflow:scroll;" id="myTable">
                           <thead>
                          <tr >
							<th  rowspan=3 >NO</th>
							<th  rowspan=3>NIK</th>
							<th  rowspan=3>NAMA</th>
							<th  rowspan=3>KANTOR</th>
							<th  rowspan=3>REGIONAL</th>
							<th  rowspan=3>JABATAN</th>
							<th  rowspan=3>DONASI RUTIN MUNDUR < 6 BLN</th>							
							<th colspan=4>PENGAMBILAN RUTIN</th>
							<th colspan=5>REALISASI PENGEMBANGAN NON TERIKAT</th>
							<th colspan=7>PENGEMBANGAN INSIDENTIL  TERIKAT</th>
							<th rowspan=3>PRESENTASE PENGAMBILAN</th>
						
							<th colspan="13">PENDAPATAN</th>
							<th rowspan=3>TOTAL PENDAPATAN</th>
							<th colspan="4">POTONGAN</th>
							<th rowspan=3>TOTAL POTONGAN</th>
							<th rowspan=3>TOTAL TERIMA</th>
							<th rowspan=3>TOTAL FUNDRAISING</th>
							<th rowspan=3>SLIP</th>

							<th rowspan=3>PAYROLL TRANSPORT</th>
							<th colspan=2>PAYROLL BONUS</th>
							<th rowspan=3>TOTAL TERIMA BONUS</th>
						  </tr>

						   <tr>	
							<th colspan=2 >Infaq</th>
							<th colspan=2 >Zakat</th>
							<th colspan=2 >Infaq</th>
							<th colspan=2 >Zakat Mal</th>
							<th rowspan=2 >Zakat Fithrah</th>							
							<th colspan=3 >wakaf</th>
							<th colspan=4 >dana terikat non wakaf</th>
							
							<th rowspan=2>BANTUAN TRANSPORT</th>
							<th rowspan=2> TUNJANGAN PENGAMBILAN </th>
							<th rowspan=2> INSENTIF PENGAMBILAN </th>
							<th rowspan=2> BONUS PRESTASI PENGAMBILAN  </th>
							<th rowspan=2> BONUS PENGEMBANGAN INFAQ RUTIN </th>
							<th rowspan=2> INSIDENTIL, ZAKAT, WAKAF TUNAI  </th>
							<th rowspan=2> BONUS PATUNGAN SAPI </th>
							<th rowspan=2> BONUS QURBAN SAPI </th>
							<th rowspan=2> KOREKSI</th>
							<th rowspan=2> PENYESUAIAN</th>
							<th rowspan=2> PENGEMBALIAN BONUS 40%</th>
							<th rowspan=2> TUNJANGAN JABATAN</th>
							<th rowspan=2> TUNJANGAN PRESTASI</th>

							<th rowspan=2> DANSOS</th>
							<th rowspan=2> ZAKAT</th>
							<th rowspan=2> MYM</th>
							<th rowspan=2> LAIN-LAIN</th>

							<th rowspan=2> BONUS</th>
							<th rowspan=2> POTONGAN</th>
						  </tr>
						  <tr>
							<th>Target</th>
							<th>Realisasi</th>
							<th>Target</th>
							<th>Realisasi</th>
							<th>Rutin</th>
							<th>Insidental</th>
							<th>Rutin</th>
							<th>Insidental</th>

							<th>ICMBS</th>
							<th>STAINIM</th>
							<th>MASJID</th>
							<th>Al&nbsp;quran</th>
							<th>Qurban</th>
							<th>Kemanusiaan</th>
							<th>fidyah</th>

							</tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";
	$keyDisabled=0;
	$pesan="";$listBkm="";
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan=46>Data Belum Ada</td></tr>";
	}else{
		$i=1;	//as row
		$blnIdk=$this->arrIntBln;
		//cek var bln tahun  value DARI BULAN SEBLMNYA
		if ($bln==1){
			$bln_pre=12;
			$thn_pre=$thn-1;
		}else{
			$bln_pre=$blnIdk[$bln-1];
			$thn_pre=$thn;
		}
	
	    $rscek=$this->donasi_db->query("select count(*) jml from donasi where status='DRAFT' and cabang_id=".$id_cabang." and concat(year, month)='".$thn.intval($bln)."'" )->row();
	    if ($rscek->jml>0){
	        $keyDisabled=1;
	        $pesan="Masih ada kuitansi yang belum diproses";
	    }
	    
		foreach($row as $hasil){
			$cektransport="";
			$cekprestasi="";
			$masakerja=0;
			$strmasakerja="-";
			if ($hasil->SELISIH=="" || empty($hasil->SELISIH ) || $hasil->SELISIH<=0){
				$masakerja=0;
				$strmasakerja="-";
				//$keyDisabled=1;
			}else{
				$masakerja=$hasil->SELISIH;		//dlm bulan
				//hitung masa kerja
				if ($masakerja<12){				
					$strmasakerja=number_format($hasil->SELISIH,0,',','')." Bln";	
					$thnMasakerja=0;
				}else{
					$strmasakerja=floor($hasil->SELISIH/12)." Thn, ".($hasil->SELISIH%12)." Bln";
					$thnMasakerja=floor($hasil->SELISIH/12);
				}
			}

		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
		$rscab=$this->gate_db->query("SELECT m.*, (SELECT DISTINCT kota FROM mst_cabang WHERE mst_cabang.`id_cabang`=m.ID_cabang_PARENT) nama_parent FROM mst_cabang m where id_cabang=".$hasil->ID_CABANG )->row();
		$rs_stspeg=$this->db->query("select value1 from gen_reff where reff='STSPEGAWAI' and id_reff=".$hasil->STATUS_PEGAWAI)->row();
		

		//GET ABSEN VALUE
		//hari kerja ambil dari database
			$jml_hadir=0;
			$jml_tidak_hadir=0;
			
			$skerja="select * from rekap_absensi_zisco where periode='".$thn.$bln."' and nik='".$hasil->NIK."'";
			if ($this->db->query($skerja)->num_rows()>0){
				$rskerja=$this->db->query($skerja)->row();
				$jml_hadir=$rskerja->JML_MASUK;
				$jml_tidak_hadir=$rskerja->JML_TIDAK_MASUK;
				
			}
			//$tr=($hasil->SELISIH=="" || empty($hasil->SELISIH));
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik[<?php echo ($i-1)?>]" id="nik[<?php echo ($i-1)?>]" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?></td>
		<td><?=str_replace(" ","&nbsp;",$rscab->kota)?></td>
		<td><?=str_replace(" ","&nbsp;",$rscab->nama_parent)?></td>
		<td><?=str_replace(" ","&nbsp;",$rsjab->nama_jab)?><input type="hidden" name="idjab[<?php echo ($i-1)?>]" id="idjab[<?php echo ($i-1)?>]" value="<?=$hasil->ID_JAB?>"></td>
		<td bgcolor="#ffffcc"><input type="text" name="zisco_mundur[<?php echo ($i-1)?>]" id="zisco_mundur[<?php echo ($i-1)?>]" value="0" onkeyup="goPengembalian40(this, <?=($i-1)?>)"></td>			
<?	

/*(SELECT  IFNULL(SUM(dd.paid),0)  
FROM donasi dns, detail_donasi dnsd	
WHERE  dns.id=dnsd.donasi_id and dns.bkm_id in (select id from bkm where status='VALIDATED')
AND dns.id=d.id) realisasi*/
	
	
$strTargetInfaq1 = "select 
((tgt.target - pgb.target) - pgb_faild.target) target,
  pgb_faild.target pgb_faild_target ,
	 ((tgt.nominal_tervalidasi + tgt.kurang)- pgb.realisasi ) realisasi,
	 (tgt.lebih+pgb.realisasi) pengembangan,	 
	 tgt.kurang kurangbayar,	 
	 pgb.target pgbtarget, 
	 pgb.realisasi pgbreal,
	 (pgb.target - pgb.realisasi) pgbselisih
          
from (SELECT  IFNULL(SUM(dd.nominal),0) target, 
    
    (SELECT IFNULL(SUM(dnsd.nominal),0) 
	FROM donasi dns, detail_donasi dnsd 
	WHERE dns.id=dnsd.donasi_id 
	and dns.bkm_id in (select id from bkm where status='VALIDATED') 
	AND dns.zisco_id=d.zisco_id 
 	and (case  WHEN dns.paid_at is null then  CONCAT(dns.year, dns.month) ELSE date_format(dns.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
	and dnsd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE payroll_column='infaq' AND `type`='ROUTINE')
	
	) nominal_tervalidasi, 
	
    (
		SELECT IFNULL(SUM(case when dnsd.diff < 0 then  dnsd.diff else 0 end),0) slsh
		FROM donasi dns, (select donasi_id, jenis_donasi_id, nominal, paid, ( IFNULL(paid,0) -  IFNULL(nominal,0)) diff from detail_donasi) dnsd 
		WHERE dns.id=dnsd.donasi_id 
		and dns.bkm_id in (select id from bkm where status='VALIDATED')
		AND dns.zisco_id=d.zisco_id 
		and (case WHEN dns.paid_at is null then CONCAT(dns.year, dns.month) ELSE date_format(dns.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
		and dnsd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE payroll_column='infaq' AND `type`='ROUTINE')
	) kurang,

	(
		SELECT IFNULL(SUM(case when dnsd.diff > 0 then  dnsd.diff else 0 end),0) slsh
		FROM donasi dns, (select donasi_id, jenis_donasi_id, nominal, paid, ( IFNULL(paid,0) -  IFNULL(nominal,0)) diff from detail_donasi) dnsd 
		WHERE dns.id=dnsd.donasi_id 
		and dns.bkm_id in (select id from bkm where status='VALIDATED')
		AND dns.zisco_id=d.zisco_id 
		and (case WHEN dns.paid_at is null then CONCAT(dns.year, dns.month) ELSE date_format(dns.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
		and dnsd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE payroll_column='infaq' AND `type`='ROUTINE')
	) lebih
	
FROM donatur dnt, donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id AND d.id=dd.donasi_id 
AND  (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID." and d.status in ('DRAFT','PAID', 'FAILED')
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE  payroll_column='infaq' AND `type`='ROUTINE')) tgt,



(SELECT  IFNULL(SUM(dd.nominal),0) target, 
(SELECT IFNULL(SUM(dnsd.paid),0) 
	FROM donasi dns, detail_donasi dnsd 
	WHERE dns.id=dnsd.donasi_id 
	and dns.bkm_id in (select id from bkm where status='VALIDATED') 
	AND dns.zisco_id=d.zisco_id AND dns.id=d.id
 	and (case  WHEN dns.paid_at is null then  CONCAT(dns.year, dns.month) ELSE date_format(dns.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
	and dnsd.jenis_donasi_id IN 
	(SELECT id FROM jenis_donasi WHERE payroll_column='infaq' AND `type`='ROUTINE')) realisasi, 
	
	IFNULL(SUM(dd.paid),0) -IFNULL(SUM(dd.nominal),0)  selisih 
FROM  donasi d, detail_donasi dd, donatur don
WHERE d.id=dd.donasi_id and don.id=d.donatur_id  and  d.status in ('PAID', 'FAILED')
and don.registered_at like '".$thn.'-'.$bln."%'
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID."
and d.bkm_id in (select id from bkm where status='VALIDATED') 
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE  payroll_column='infaq' AND `type`='ROUTINE')) pgb,

   
      
    (SELECT     IFNULL(SUM(dd.nominal), 0) target,
    IFNULL(SUM(dd.paid), 0) - IFNULL(SUM(dd.nominal), 0) selisih 
  FROM  donasi d,    detail_donasi dd,    donatur don 
  WHERE d.id = dd.donasi_id 
    AND don.id = d.donatur_id 
    AND d.status = 'FAILED' 
    AND don.registered_at LIKE '".$thn.'-'.$bln."%' 
    AND (CASE    WHEN d.paid_at IS NULL 
        THEN CONCAT(d.year, d.month) 
        ELSE DATE_FORMAT(d.paid_at, '%Y%c') 
      END
    ) = '".$thn.intval($bln)."' 
    AND d.zisco_id = ".$hasil->ID." 
    AND dd.jenis_donasi_id IN 
    (SELECT    id 
    FROM      jenis_donasi 
    WHERE payroll_column = 'infaq' 
      AND `type` = 'ROUTINE')) pgb_faild";

	$rsTargetInfaq=$this->donasi_db->query($strTargetInfaq1)->row();
	$rutin_infaq_target=$rsTargetInfaq->target;
	$rutin_infaq_realisasi=($rsTargetInfaq->realisasi > $rsTargetInfaq->target?$rsTargetInfaq->target: $rsTargetInfaq->realisasi);
	//$rutin_infaq_selisih=($rsTargetInfaq->pgbtarget+$rsTargetInfaq->selisih<=0?0:$rsTargetInfaq->pgbtarget+$rsTargetInfaq->selisih);	
	$rutin_infaq_selisih=($rsTargetInfaq->pengembangan<=0?0:$rsTargetInfaq->pengembangan);  //pengembangan


$strTargetZakat="select 
((tgt.target - pgb.target) - pgb_faild.target) target,
  pgb_faild.target pgb_faild_target ,
	 ((tgt.nominal_tervalidasi + tgt.kurang)- pgb.realisasi )  realisasi,
	  (tgt.lebih+pgb.realisasi) pengembangan,	 
	 tgt.kurang kurangbayar,	 
	 pgb.target pgbtarget, 
	 pgb.realisasi pgbreal,
	 (pgb.target - pgb.realisasi) pgbselisih
           
from (SELECT  IFNULL(SUM(dd.nominal),0) target, 
(SELECT IFNULL(SUM(dnsd.nominal),0) 
	FROM donasi dns, detail_donasi dnsd 
	WHERE dns.id=dnsd.donasi_id 
	and dns.bkm_id in (select id from bkm where status='VALIDATED') 
	AND dns.zisco_id=d.zisco_id  
 	and (case  WHEN dns.paid_at is null then  CONCAT(dns.year, dns.month) ELSE date_format(dns.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
	and dnsd.jenis_donasi_id IN 
	(SELECT id FROM jenis_donasi WHERE payroll_column='zakat' AND `type`='ROUTINE')
	) nominal_tervalidasi,
	
 (
		SELECT IFNULL(SUM(case when dnsd.diff < 0 then  dnsd.diff else 0 end),0) slsh
		FROM donasi dns, (select donasi_id, jenis_donasi_id, nominal, paid, ( IFNULL(paid,0) -  IFNULL(nominal,0)) diff from detail_donasi) dnsd 
		WHERE dns.id=dnsd.donasi_id 
		and dns.bkm_id in (select id from bkm where status='VALIDATED')
		AND dns.zisco_id=d.zisco_id 
		and (case WHEN dns.paid_at is null then CONCAT(dns.year, dns.month) ELSE date_format(dns.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
		and dnsd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE payroll_column='zakat' AND `type`='ROUTINE')
	) kurang,

	(
		SELECT IFNULL(SUM(case when dnsd.diff > 0 then  dnsd.diff else 0 end),0) slsh
		FROM donasi dns, (select donasi_id, jenis_donasi_id, nominal, paid, ( IFNULL(paid,0) -  IFNULL(nominal,0)) diff from detail_donasi) dnsd 
		WHERE dns.id=dnsd.donasi_id 
		and dns.bkm_id in (select id from bkm where status='VALIDATED')
		AND dns.zisco_id=d.zisco_id 
		and (case WHEN dns.paid_at is null then CONCAT(dns.year, dns.month) ELSE date_format(dns.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
		and dnsd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE payroll_column='zakat' AND `type`='ROUTINE')
	) lebih
	
FROM donatur dnt, donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id 
AND  (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID." and d.status in ('DRAFT','PAID', 'FAILED')
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE  payroll_column='zakat' AND `type`='ROUTINE' ) ) tgt,

(SELECT  IFNULL(SUM(dd.nominal),0) target, (SELECT IFNULL(SUM(dnsd.paid),0) 
	FROM donasi dns, detail_donasi dnsd 
	WHERE dns.id=dnsd.donasi_id 
	and dns.bkm_id in (select id from bkm where status='VALIDATED') 
	AND dns.zisco_id=d.zisco_id AND dns.id=d.id
 	and (case  WHEN dns.paid_at is null then  CONCAT(dns.year, dns.month) ELSE date_format(dns.paid_at,'%Y%c') end) ='".$thn.intval($bln)."'
	and dnsd.jenis_donasi_id IN 
	(SELECT id FROM jenis_donasi WHERE payroll_column='zakat' AND `type`='ROUTINE') ) realisasi,
	IFNULL(SUM(dd.paid),0) -IFNULL(SUM(dd.nominal),0) selisih 
	
	
FROM  donasi d, detail_donasi dd, donatur don
WHERE  d.id=dd.donasi_id and don.id=d.donatur_id  and   d.status in ('PAID', 'FAILED')
and don.registered_at like '".$thn.'-'.$bln."%'
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."'  
AND d.zisco_id=".$hasil->ID."
and d.bkm_id in (select id from bkm where status='VALIDATED') 
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE  payroll_column='zakat' AND `type`='ROUTINE')) pgb,

   
      
    (SELECT     IFNULL(SUM(dd.nominal), 0) target,
    IFNULL(SUM(dd.paid), 0) - IFNULL(SUM(dd.nominal), 0) selisih 
  FROM  donasi d,    detail_donasi dd,    donatur don 
  WHERE d.id = dd.donasi_id 
    AND don.id = d.donatur_id 
    AND d.status = 'FAILED' 
    AND don.registered_at LIKE '".$thn.'-'.$bln."%' 
    AND (CASE    WHEN d.paid_at IS NULL 
        THEN CONCAT(d.year, d.month) 
        ELSE DATE_FORMAT(d.paid_at, '%Y%c') 
      END
    ) = '".$thn.intval($bln)."' 
    AND d.zisco_id = ".$hasil->ID." 
    AND dd.jenis_donasi_id IN 
    (SELECT    id 
    FROM      jenis_donasi 
    WHERE payroll_column = 'zakat' 
      AND `type` = 'ROUTINE')) pgb_faild";


	$rsTargetZakat=$this->donasi_db->query($strTargetZakat)->row();
	$rutin_zakat_target=$rsTargetZakat->target;
	$rutin_zakat_realisasi=($rsTargetZakat->realisasi > $rsTargetZakat->target?$rsTargetZakat->target: $rsTargetZakat->realisasi);
	$rutin_zakat_selisih=($rsTargetZakat->pengembangan<=0?0:$rsTargetZakat->pengembangan);

$strInsiInfaq="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID."
and d.bkm_id in (select id from bkm where status='VALIDATED') 
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE  payroll_column='infaq' AND `type`='INSIDENTAL')";
	$rsInsiInfaq=$this->donasi_db->query($strInsiInfaq)->row();
	$insi_infaq_paid=$rsInsiInfaq->realisasi;

$strInsiZakat="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID."   
and d.bkm_id in (select id from bkm where status='VALIDATED')
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE  payroll_column='zakat' AND `type`='INSIDENTAL')";
	$rsInsiZakat=$this->donasi_db->query($strInsiZakat)->row();
	$insi_zakat_paid=$rsInsiZakat->realisasi;


$strInsiZakatFitrah="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id  and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID."  
and d.bkm_id in (select id from bkm where status='VALIDATED')
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE  payroll_column='zakat fitrah' AND `type`='INSIDENTAL')";
	$rsInsiZakatFitrah=$this->donasi_db->query($strInsiZakatFitrah)->row();
	$insi_zakat_fitrah=$rsInsiZakatFitrah->realisasi;


$strWakafIcmb="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id  and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID." 
and d.bkm_id in (select id from bkm where status='VALIDATED')
AND dd.jenis_donasi_id =35";
	$rsWakafIcmb=$this->donasi_db->query($strWakafIcmb)->row();
	$insi_wakaf_icmb=$rsWakafIcmb->realisasi;

$strWakafMasjid="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id  and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID."  
and d.bkm_id in (select id from bkm where status='VALIDATED')
AND dd.jenis_donasi_id =37";
	$rsWakafMasjid=$this->donasi_db->query($strWakafMasjid)->row();
	$insi_wakaf_masjid=$rsWakafMasjid->realisasi;

$strWakafStainim="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id  and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID." 
and d.bkm_id in (select id from bkm where status='VALIDATED')
AND dd.jenis_donasi_id =38";
	$rsWakafStainim=$this->donasi_db->query($strWakafStainim)->row();
	$insi_wakaf_stainim=$rsWakafStainim->realisasi;

//and d.type='ROUTINE'
$strnon_wakaf_quran="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id  and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID."  
and d.bkm_id in (select id from bkm where status='VALIDATED')
AND dd.jenis_donasi_id =41";
	$rsnon_wakaf_quran=$this->donasi_db->query($strnon_wakaf_quran)->row();
	$non_wakaf_quran=$rsnon_wakaf_quran->realisasi;

$strnon_wakaf_qurban="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id  and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID."  
and d.bkm_id in (select id from bkm where status='VALIDATED')
AND dd.jenis_donasi_id =39";
	$rsnon_wakaf_qurban=$this->donasi_db->query($strnon_wakaf_qurban)->row();
	$non_wakaf_qurban=$rsnon_wakaf_qurban->realisasi;

$strnon_wakaf_bencana="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id  and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID."  
and d.bkm_id in (select id from bkm where status='VALIDATED')
AND dd.jenis_donasi_id =43";
	$rsnon_wakaf_bencana=$this->donasi_db->query($strnon_wakaf_bencana)->row();
	$non_wakaf_bencana=$rsnon_wakaf_bencana->realisasi;

$strnon_wakaf_fidyah="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id  and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID." 
and d.bkm_id in (select id from bkm where status='VALIDATED')
AND dd.jenis_donasi_id =29";
	$rsnon_wakaf_fidyah=$this->donasi_db->query($strnon_wakaf_fidyah)->row();
	$non_wakaf_fidyah=$rsnon_wakaf_fidyah->realisasi;

$persen_ambil=0;
$str_persen_ambil="";
$sumrutin_target=$rutin_infaq_target + $rutin_zakat_target;
$sumrutin_real=$rutin_infaq_realisasi + $rutin_zakat_realisasi;

    if ($sumrutin_target <=0){
    	$str_persen_ambil="CEK TARGET";
    }elseif($sumrutin_real / $sumrutin_target > 1 ){
    	$str_persen_ambil="CEK REALISASI";
    }else{
    	$persen_ambil= ($sumrutin_real / $sumrutin_target)*100;
    	$str_persen_ambil= round($persen_ambil,2)."%";
    }

?>
		<td><input type="text" name="rutin_infaq_target[<?php echo ($i-1)?>]" id="rutin_infaq_target[<?php echo ($i-1)?>]" value="<?php echo $rutin_infaq_target?>"></td>
		<td><input type="text" name="rutin_infaq_realisasi[<?php echo ($i-1)?>]" id="rutin_infaq_realisasi[<?php echo ($i-1)?>]" value="<?php echo $rutin_infaq_realisasi?>"></td>
		<td><input type="text" name="rutin_zakat_target[<?php echo ($i-1)?>]" id="rutin_zakat_target[<?php echo ($i-1)?>]" value="<?php echo $rutin_zakat_target?>"></td>
		<td><input type="text" name="rutin_zakat_realisasi[<?php echo ($i-1)?>]" id="rutin_zakat_realisasi[<?php echo ($i-1)?>]" value="<?php echo $rutin_zakat_realisasi?>"></td>
		<td><input type="text" name="rutin_infaq_selisih[<?php echo ($i-1)?>]" id="rutin_infaq_selisih[<?php echo ($i-1)?>]" value="<?php echo $rutin_infaq_selisih?>"></td>
		<td><input type="text" name="insi_infaq_paid[<?php echo ($i-1)?>]" id="insi_infaq_paid[<?php echo ($i-1)?>]" value="<?php echo $insi_infaq_paid?>"></td>
		<td><input type="text" name="rutin_zakat_selisih[<?php echo ($i-1)?>]" id="rutin_zakat_selisih[<?php echo ($i-1)?>]" value="<?php echo $rutin_zakat_selisih?>"></td>
		<td><input type="text" name="insi_zakat_paid[<?php echo ($i-1)?>]" id="insi_zakat_paid[<?php echo ($i-1)?>]" value="<?php echo $insi_zakat_paid?>"></td>
		<td><input type="text" name="insi_zakat_fitrah[<?php echo ($i-1)?>]" id="insi_zakat_fitrah[<?php echo ($i-1)?>]" value="<?php echo $insi_zakat_fitrah?>"></td>
		<td  ><input type="text" name="wakaf_icmb[<?php echo ($i-1)?>]" id="wakaf_icmb[<?php echo ($i-1)?>]" value="<?php echo $insi_wakaf_icmb?>"></td>
		<td  ><input type="text" name="wakaf_stainim[<?php echo ($i-1)?>]" id="wakaf_stainim[<?php echo ($i-1)?>]" value="<?php echo $insi_wakaf_stainim?>"></td>
		<td  ><input type="text" name="wakaf_masjid[<?php echo ($i-1)?>]" id="wakaf_masjid[<?php echo ($i-1)?>]" value="<?php echo $insi_wakaf_masjid?>"></td>

		<td  ><input type="text" name="non_wakaf_quran[<?php echo ($i-1)?>]" id="non_wakaf_quran[<?php echo ($i-1)?>]" value="<?php echo $non_wakaf_quran?>"></td>
		<td  ><input type="text" name="non_wakaf_qurban[<?php echo ($i-1)?>]" id="non_wakaf_qurban[<?php echo ($i-1)?>]" value="<?php echo $non_wakaf_qurban?>"></td>
		<td  ><input type="text" name="non_wakaf_bencana[<?php echo ($i-1)?>]" id="non_wakaf_bencana[<?php echo ($i-1)?>]" value="<?php echo $non_wakaf_bencana?>"></td>
		<td  ><input type="text" name="non_wakaf_fidyah[<?php echo ($i-1)?>]" id="non_wakaf_fidyah[<?php echo ($i-1)?>]" value="<?php echo $non_wakaf_fidyah?>"></td>
		
		<td ><?php echo $str_persen_ambil?></td> 
<?	

	//get key yg dipakai smua komp
	$totPendapatan=0; 
	$totPotongan=0;
	$acuan_trans=0;
	$tunj_transport=0;
	$j=0;
	$display_angsuran=0; $cicilke=0; $id_header=0;
	$str="select ACUAN_TRANSPORT, U_TRANS_DITERIMA from gaji_zisco_transport where BLN='".$bln."' and THN='".$thn."' and nik='".$hasil->NIK."' and id_cabang=".$hasil->ID_CABANG ;
	$rstrans=$this->db->query($str)->row();
	if (sizeof($rstrans)>0){
		$acuan_trans = $rstrans->ACUAN_TRANSPORT;					
		$tunj_transport = $rstrans->U_TRANS_DITERIMA;					
	}else{
	    if ($hasil->ID_JAB==37){
	        $acuan_trans=0;
	        $tunj_transport=0;
	    }else{
    		$cektransport="Uang Transport blm ada";
    		$keyDisabled=1;
	    }
	}

$rs_stspeg=$this->db->query("select value1 from gen_reff where reff='STSPEGAWAI' and id_reff=".$hasil->STATUS_PEGAWAI)->row();
//tunj pengambilan IF(Transport!J10="BELUM";0;IF((I13+K13)>(H13+J13);"Realisasi Lebih Besar";IF((I13+K13)<0;"SALAH";IF((I13+K13)<2000000;0;IF((I13+K13)<=10000000;150000;IF((I13+K13)<=20000000;250000;IF((I13+K13)<=40000000;350000;450000)))))))
$tunj_pengambilan=0;
$str_tunj_pengambilan="";
if ($hasil->ID_JAB!=37){
    if ($rs_stspeg->value1=="Belum"){
    	$tunj_pengambilan=0;
    	$str_tunj_pengambilan="Belum";
    }elseif($sumrutin_real > $sumrutin_target){
    	$str_tunj_pengambilan="Realisasi Lebih Besar";
    }elseif($sumrutin_real < 0){
    	$str_tunj_pengambilan="SALAH";
    }elseif($sumrutin_real < 2000000){
    	$tunj_pengambilan=0;
    }elseif($sumrutin_real <= 10000000){
    	$tunj_pengambilan=150000;
    }elseif($sumrutin_real <= 20000000){
    	$tunj_pengambilan=250000;
    }elseif($sumrutin_real <= 40000000){
    	$tunj_pengambilan=350000;
    }else{
    	$tunj_pengambilan=450000;
    }
}

//insentif_pengambilan_ IF((I13+K13)>(H13+J13);"Realisasi Lebih Besar";IF(X13>=97%;ROUNDUP(((I13+K13)*2%);0);ROUNDUP(((I13+K13)*1%);0)))
$insentif_pengambilan=0;
$str_insentif_pengambilan="";
if ($hasil->ID_JAB!=37){
    if($sumrutin_real > $sumrutin_target){
    	$str_insentif_pengambilan="Realisasi Lebih Besar";
    }elseif($persen_ambil>=97){
    	$insentif_pengambilan=round($sumrutin_real*0.02);
    }else{
    	$insentif_pengambilan=round($sumrutin_real*0.01);
    }
}
//bonus_prestasi_  IF(X13<97%;0;IF(X13<99%;ROUNDUP(((I13+K13)*0,4%);0);IF(X13<=100%;ROUNDUP(((I13+K13)*0,5%);0);"% Terlalu besar")))
$bonus_prestasi=0;
$str_bonus_prestasi="";
if ($hasil->ID_JAB!=37){
    if($persen_ambil< 97){
    	$bonus_prestasi=0;
    }elseif($persen_ambil<99){
    	$bonus_prestasi=round($sumrutin_real*0.004);
    }elseif($persen_ambil<=100){
    	$bonus_prestasi=round($sumrutin_real*0.005);
    }else{
    	$str_bonus_prestasi="% Terlalu besar";
    }
}

//bonus_pengembangan ROUNDUP((L8*40%);0)
$bonus_pengembangan=0;
if ($hasil->ID_JAB!=37){
    $bonus_pengembangan=round($rutin_infaq_selisih * 0.4);
}

//insi_zakat_wakaf_tunai ROUNDUP(((M8+N8+O8+Q8+R8+S8+T8+V8+W8)*6%);0)
$insi_zakat_wakaf_tunai=0;
if ($hasil->ID_JAB!=37){
    $insi_zakat_wakaf_tunai=round($insi_infaq_paid + $rutin_zakat_selisih + $insi_zakat_paid + $insi_wakaf_icmb + $insi_wakaf_stainim + $insi_wakaf_masjid + $non_wakaf_quran + $non_wakaf_bencana + $non_wakaf_fidyah)*0.06;
}

//pengembalian_40_ =-ROUNDDOWN((F7*40%);0) = event

//tunj_jabatan_
$tunj_jabatan=0;    //zisco & relawan=0;
$str3="select ifnull(nominal,0) nominal from mst_komp_var where	id_komp=10";
$rsJab=$this->db->query($str3)->row();
if (sizeof($rsJab)>0){
	if ($hasil->ID_JAB ==35){	//supervisor
		$tunj_jabatan = $rsJab->nominal;
	}
}
$total_fundraising=0;
if ($hasil->ID_JAB==37){//relawan
    $total_fundraising = $rutin_infaq_selisih+$rutin_zakat_selisih +$insi_infaq_paid + $insi_zakat_paid+$insi_zakat_fitrah+$insi_wakaf_icmb+$insi_wakaf_stainim+$insi_wakaf_masjid+$non_wakaf_quran+$non_wakaf_qurban+$non_wakaf_bencana+$non_wakaf_fidyah;
}else{
    $total_fundraising = $sumrutin_real+$rutin_infaq_selisih+$rutin_zakat_selisih +$insi_infaq_paid + $insi_zakat_paid+$insi_zakat_fitrah+$insi_wakaf_icmb+$insi_wakaf_stainim+$insi_wakaf_masjid+$non_wakaf_quran+$non_wakaf_qurban+$non_wakaf_bencana+$non_wakaf_fidyah;    
}




//tunj_prestasi_ kriteria ukur dari total fundraising
$tunj_prestasi=0;
if ($hasil->ID_JAB!=37){
    $strtp="select ifnull(nominal,0) nominal from mst_tunj_prestasi where (".$sumrutin_real.">=batas_bawah AND ".$sumrutin_real."<batas_atas)";
    $rsTP=$this->db->query($strtp)->row();
    if (sizeof($rsTP)>0){	
    	$tunj_prestasi = $rsTP->nominal;
    	}
}

if ($hasil->ID_JAB==37){//relawan
    $totPendapatan= $total_fundraising * 0.1;   //10%
}else{
    $totPendapatan=$tunj_transport+$tunj_pengambilan+$insentif_pengambilan+$bonus_prestasi+$bonus_pengembangan+$insi_zakat_wakaf_tunai+$tunj_jabatan+$tunj_prestasi;
}

//pot dansos=ROUNDUP(IF(AO85<=3203846;AO85*2,5%;IF(AO85>=3203846;AO85*1%));0)
//$pot_dansos=($totPendapatan<3203846? round($totPendapatan * 0.025): ($totPendapatan>=3203846?round($totPendapatan * 0.01 ):0));
$pot_dansos=0;
if ($hasil->ID_JAB!=37){
    $pot_dansos=$totPendapatan*0.025;
}
//$zakat=($totPendapatan<3203846? 0: round($totPendapatan * 0.025 ));
//semua (zisco, relawan, spv zisco)
$zakat=($hasil->KESEDIAAN_ZAKAT=="1"?$totPendapatan*0.025:0);
$totPotongan=$pot_dansos+$zakat;

?>

<td><input type="hidden" name="acuan_transport[<?php echo ($i-1)?>]" id="acuan_transport_<?php echo ($i-1)?>" value="<?php echo $acuan_trans ?>"><?php echo ($cektransport==""?"":$cektransport).number_format($acuan_trans,0,',','.')?>
<?=form_input(array('name'=>'tunj_transport['.($i-1).']','id'=>'tunj_transport_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>$tunj_transport, "readonly"=>true ));?></td>
<td>
<input type="hidden" name="tunj_pengambilan[<?php echo ($i-1)?>]" id="tunj_pengambilan_<?php echo ($i-1)?>" value="<?php echo $tunj_pengambilan ?>">
<?=form_input(array('name'=>'strtunj_pengambilan['.($i-1).']','id'=>'strtunj_pengambilan['.($i-1).']','class'=>'myform-control','size'=>10, 'value'=>($str_tunj_pengambilan==""?$tunj_pengambilan:$str_tunj_pengambilan), "readonly"=>true ));?>
</td>
<td>
<input type="hidden" name="insentif_pengambilan[<?php echo ($i-1)?>]" id="insentif_pengambilan_<?php echo ($i-1)?>" value="<?php echo $insentif_pengambilan ?>">
<?=form_input(array('name'=>'str_insentif_pengambilan['.($i-1).']','id'=>'str_insentif_pengambilan_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>($str_insentif_pengambilan==""?$insentif_pengambilan:$str_insentif_pengambilan), "readonly"=>true ));?>
</td>
<td><input type="hidden" name="bonus_prestasi[<?php echo ($i-1)?>]" id="bonus_prestasi_<?php echo ($i-1)?>" value="<?php echo $bonus_prestasi ?>">
<?=form_input(array('name'=>'str_bonus_prestasi['.($i-1).']','id'=>'str_bonus_prestasi_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>($str_bonus_prestasi==""?$bonus_prestasi:$str_bonus_prestasi), "readonly"=>true ));?>
</td>
<td><?=form_input(array('name'=>'bonus_pengembangan['.($i-1).']','id'=>'bonus_pengembangan_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>$bonus_pengembangan, "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'insi_zakat_wakaf_tunai['.($i-1).']','id'=>'insi_zakat_wakaf_tunai_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>$insi_zakat_wakaf_tunai, "readonly"=>true ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'bonus_patungan_sapi['.($i-1).']','id'=>'bonus_patungan_sapi_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>0, "onkeyup"=>"countRevenue(".($i-1).", this)", "onkeypress"=>"return numericVal(this,event)"  ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'bonus_qurban_sapi['.($i-1).']','id'=>'bonus_qurban_sapi_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>0, "onkeyup"=>"countRevenue(".($i-1).", this)", "onkeypress"=>"return numericVal(this,event)" ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'koreksi['.($i-1).']','id'=>'koreksi_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>0, "onkeyup"=>"countRevenue(".($i-1).", this)", "onkeypress"=>"return numericVal(this,event)" ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'penyesuaian['.($i-1).']','id'=>'penyesuaian_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>0, "onkeyup"=>"countRevenue(".($i-1).", this)", "onkeypress"=>"return numericVal(this,event)" ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'pengembalian_40['.($i-1).']','id'=>'pengembalian_40_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>0, "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'tunj_jabatan['.($i-1).']','id'=>'tunj_jabatan_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>$tunj_jabatan, "readonly"=>true ));?></td>
<td><?=($cekprestasi==""?"":$cekprestasi).form_input(array('name'=>'tunj_prestasi['.($i-1).']','id'=>'tunj_prestasi_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>$tunj_prestasi, "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'totPendapatan['.($i-1).']','id'=>'totPendapatan_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan,0) ));?></td>

<td><?=form_input(array('name'=>'pot_dansos['.($i-1).']','id'=>'pot_dansos_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>round($pot_dansos), "readonly"=>true ));?></td>
<td><?='<input type="hidden" name="sedia_zakat['.($i-1).']" id="sedia_zakat_'.($i-1).'" value="'.$hasil->KESEDIAAN_ZAKAT.'">'.form_input(array('name'=>'pot_zakat['.($i-1).']','id'=>'pot_zakat_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>round($zakat), "readonly"=>true ));?></td>
<!-- angsuran/cicilan MYM saat ini di set di gaji transport -->
<td><input type="hidden" name="cicilke[<?php echo ($i-1)?>]" id="cicilke_<?php echo ($i-1)?>" value="0">
<?=form_input(array('name'=>'angsuran['.($i-1).']','id'=>'angsuran_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>0, "readonly"=>true ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'lain_lain['.($i-1).']','id'=>'lain_lain_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>0,  "onkeyup"=>"countExpense(".($i-1).", this)", "onkeypress"=>"return numericVal(this,event)" ));?></td>
<td><?=form_input(array('name'=>'totPotongan['.($i-1).']','id'=>'totPotongan_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>round($totPotongan,0) , "readonly"=>true));?></td>
<td><?=form_input(array('name'=>'totTerima['.($i-1).']','id'=>'totTerima_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan-$totPotongan,0), "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'total_fundraising['.($i-1).']','id'=>'total_fundraising_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>round($total_fundraising,0), "readonly"=>true ));?></td>
<td>-</td>

<td><?php echo number_format($tunj_transport,0,',','.')?></td>
<td><?=form_input(array('name'=>'payroll_bonus['.($i-1).']','id'=>'payroll_bonus_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan-($tunj_transport+$tunj_jabatan),0), "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'pot_payroll_bonus['.($i-1).']','id'=>'pot_payroll_bonus_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>round($totPotongan,0), "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'total_payroll_bonus['.($i-1).']','id'=>'total_payroll_bonus_'.($i-1),'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan-($totPotongan+$tunj_transport+$tunj_jabatan),0), "readonly"=>true ));?></td>


<?		
	//cek bkm
	$str="select * from bkm where zisco_id = ".$hasil->ID." and CONCAT(year, month) ='".$thn.intval($bln)."'  and status='NOT_VALIDATED'";
	$sql=$this->donasi_db->query($str);
	$jmlBkm=$sql->num_rows();
	if ($sql->num_rows()>0){
		$keyDisabled=1;
		$listBkm.="Zisco : ".$hasil->NAMA." [".$jmlBkm." BKM belum divalidasi]<br>";
	}
		$i++;
		}
	}
?>			

                                    </tbody>
                                </table>
                            </div> <!-- /.table-responsive -->
                       
                        
						<div class="row">
							<div class="col-md-12">
							<? 	if (sizeof($row)>0){ 
									if ($sts=="new" || $sts=="edit"){
									    if (date('Y-m-d')>=$thn_next."-".$blnIdk[intval($bln_next)]."-03" && date('Y-m-d')<=$thn_next."-".$blnIdk[intval($bln_next)]."-05"){
									        $keyDisabled=0;
									    }
							?>								
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">	
										<input type="hidden" name="bln" id="bln" value="<?=$bln?>">	
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">
										<input type="hidden" name="id_validasi" id="id_validasi" value="<?=$id_validasi?>">
										<input type="hidden" name="id_cabang" id="id_cabang" value="<?=$id_cabang?>">
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan" <?php echo ($keyDisabled==1 ?"disabled":"")?>>		
							<?
									}
											
								}
								
								$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												//'onclick'=>"cekit()",
												'onclick'=>"backTo('".base_url('gaji_zisco_bonus/index')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
								?>
								</div><!-- col -->
						    </div><!-- row -->
								<?
								//echo "keyDisabled=".$keyDisabled."#pesan=".$pesan."#listBkm=".$listBkm;
								if ($keyDisabled==1 && $pesan !="") {
								    echo '<br><div class="row"><div class="col-md-6"><div class="alert alert-info alert-dismissable">'.$pesan."<br>".$listBkm.'</div></div></div>';
								}
							?>
						

						
             
</div>
<hr />
</div>

<?php echo form_close();?>
<script type="text/javascript">
function cekit()
{ alert($('input[name="nik[]"]').length); 

}
$('#myTable').DataTable( {
	//"bJQueryUI": true,
	"scrollY": "500px",
	"scrollX": true,
	"scrollCollapse": true,
	"paging": false, 
	"searching": false, 
	fixedColumns:   {
            leftColumns: 3
        },
    fixedHeader: true
} );
$('#btsimpankel').click(function(){

		var form_data = $('#myform').serialize();
		$().showMessage('Sedang diproses.. Harap tunggu..');
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('gaji_zisco_bonus/save_gaji_zisco_bonus');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					
					$().showMessage('Data Gaji Bonus zisco berhasil disimpan.','success',1000);
					setInterval(window.location.reload(), 3000);
					
				} else {
					$().showMessage('Terjadi kesalahan. Data gagal disimpan.','danger',700);
					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
		
	});
	
	function goPengembalian40(obj, idx){
		var myval=parseFloat(obj.value) * 0.4 * -1;
		$("#pengembalian_40_"+idx).val( Math.floor(myval) );
		countRevenue(idx, obj);
	}
	function countRevenue(idx, obj){
		//alert($("#tunj_transport_"+idx).val());
		var total= parseFloat( $("#tunj_transport_"+idx).val()) +  parseFloat( $("#tunj_pengambilan_"+idx ).val()) +  parseFloat( $("#insentif_pengambilan_"+idx ).val())
			+  parseFloat( $("#bonus_prestasi_"+idx).val())	+ parseFloat( $("#bonus_pengembangan_"+idx ).val()) +  parseFloat( $("#insi_zakat_wakaf_tunai_"+idx ).val())
			+  parseFloat( $("#bonus_patungan_sapi_"+idx ).val()) +  parseFloat( $("#bonus_qurban_sapi_"+idx).val())+  parseFloat( $("#koreksi_"+idx ).val())
			+  parseFloat( $("#penyesuaian_"+idx ).val()) +  parseFloat( $("#pengembalian_40_"+idx ).val()) +  parseFloat( $("#tunj_jabatan_"+idx).val()) +  parseFloat( $("#tunj_prestasi_"+idx).val())	;
		
		//total pendptn bruto, total pend diterima, tot pend netto (payroll bonus diterima)
		$("#totPendapatan_"+idx).val(total);

		var dansos=0;
		dansos=Math.round(total * 0.025);
		if ($("#sedia_zakat_"+idx).val()=="1")	{	//zakat
			$("#pot_zakat_"+idx).val(dansos);
		}
		$("#pot_dansos_"+idx).val(dansos);
		countExpense(idx,obj);

		$("#totTerima_"+idx).val( parseFloat( $("#totPendapatan_"+idx).val() ) - parseFloat(  $("#totPotongan_"+idx).val()) ) ;
		var payroll_bonus = total - parseFloat( $("#tunj_transport_"+idx ).val()) ;
		var payroll_bonus_diterima = total - ( parseFloat( $("#totPotongan_"+idx ).val()) + parseFloat( $("#tunj_transport_"+idx ).val()));
		$("#payroll_bonus_"+idx).val(payroll_bonus);
		$("#pot_payroll_bonus_"+idx).val( parseFloat(  $("#totPotongan_"+idx).val())  );
		$("#total_payroll_bonus_"+idx).val( payroll_bonus_diterima ) ;
	}
	function countExpense(idx,  obj){
		var total= parseFloat( $("#pot_dansos_"+idx).val() ) +  parseFloat( $("#pot_zakat_"+idx).val() ) +  parseFloat( $("#angsuran_"+idx).val() ) +  parseFloat( $("#lain_lain_"+idx).val() );

		$("#totPotongan_"+idx).val(total);
		$("#totTerima_"+idx).val( parseFloat( $("#totPendapatan_"+idx).val() ) - parseFloat(  $("#totPotongan_"+idx).val()) ) ;
		var payroll_bonus = total - parseFloat( $("#tunj_transport_"+idx ).val()) ;
		var payroll_bonus_diterima = total - ( parseFloat( $("#totPotongan_"+idx ).val()) + parseFloat( $("#tunj_transport_"+idx ).val()));
		$("#payroll_bonus_"+idx).val(payroll_bonus);
		$("#pot_payroll_bonus_"+idx).val( parseFloat(  $("#totPotongan_"+idx).val())  );
		$("#total_payroll_bonus_"+idx).val( payroll_bonus_diterima ) ;
	}


	
</script>