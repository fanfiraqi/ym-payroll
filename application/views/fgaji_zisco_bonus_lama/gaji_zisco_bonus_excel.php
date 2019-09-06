<table border="1">
                           <thead>
                          <tr >
							<th  rowspan=3 >NO</th>
							<th  rowspan=3>NIK</th>
							<th  rowspan=3>NAMA</th>
							<th  rowspan=3>KANTOR</th>
							<th  rowspan=3>REGIONAL</th>
							<th  rowspan=3>JABATAN</th>
							<th  rowspan=3>DONASI RUTIN MUNDUR SBLM 6 BLN</th>							
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
							<th>Al quran</th>
							<th>Qurban</th>
							<th>Bencana</th>
							<th>fidyah</th>

							</tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";
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

		foreach($row as $hasil){
			
		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
		$rscab=$this->gate_db->query("SELECT m.*, (SELECT DISTINCT kota FROM mst_cabang WHERE mst_cabang.`id_cabang`=m.ID_cabang_PARENT) nama_parent FROM mst_cabang m where id_cabang=".$hasil->ID_CABANG )->row();
		$rs_stspeg=$this->db->query("select value1 from gen_reff where reff='STSPEGAWAI' and id_reff=".$hasil->STATUS_PEGAWAI)->row();
				
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?></td>
		<td><?=str_replace("&"," ",$hasil->NAMA)?></td>
		<td><?=str_replace("&"," ",$rscab->kota)?></td>
		<td><?=str_replace("&"," ",$rscab->nama_parent)?></td>
		<td><?=str_replace("&"," dan ",$rsjab->nama_jab)?></td>
		<td bgcolor="#ffffcc"><?php echo $hasil->DONASI_RUTIN_MUNDUR?></td>			
<?	//get data target&pengambilan	
	$rutin_infaq_target=$hasil->RUTIN_INFAQ_TARGET;
	$rutin_infaq_realisasi=$hasil->RUTIN_INFAQ_REALISASI;
	$rutin_infaq_selisih=$hasil->RUTIN_INFAQ_PENGEMBANGAN;
	
	$rutin_zakat_target=$hasil->RUTIN_ZAKAT_TARGET;
	$rutin_zakat_realisasi= $hasil->RUTIN_ZAKAT_REALISASI;
	$rutin_zakat_selisih=$hasil->RUTIN_ZAKAT_PENGEMBANGAN;

	$insi_infaq_paid=$hasil->INSI_INFAQ;
	$insi_zakat_paid=$hasil->INSI_ZAKAT_MAL;
	$insi_zakat_fitrah=$hasil->INSI_ZAKAT_FITHRAH;
	$insi_wakaf_icmb=$hasil->WAKAF_ICMB;
	$insi_wakaf_masjid=$hasil->WAKAF_STAINIM;
	$insi_wakaf_stainim=$hasil->WAKAF_MASJID;
	$non_wakaf_quran=$hasil->NON_WAKAF_QURAN;
	$non_wakaf_qurban=$hasil->NON_WAKAF_QURBAN;
	$non_wakaf_bencana=$hasil->NON_WAKAF_BENCANA;
	$non_wakaf_fidyah=$hasil->NON_WAKAF_FIDYAH;

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
	$str_persen_ambil= $persen_ambil."%";
}
?>
		<td><?php echo $rutin_infaq_target?></td>
		<td><?php echo $rutin_infaq_realisasi?></td>
		<td><?php echo $rutin_zakat_target?></td>
		<td><?php echo $rutin_zakat_realisasi?></td>
		<td><?php echo $rutin_infaq_selisih?></td>
		<td><?php echo $insi_infaq_paid?></td>
		<td><?php echo $rutin_zakat_selisih?></td>
		<td><?php echo $insi_zakat_paid?></td>
		<td><?php echo $insi_zakat_fitrah?></td>
		<td><?php echo $insi_wakaf_icmb?></td>
		<td><?php echo $insi_wakaf_stainim?></td>
		<td><?php echo $insi_wakaf_masjid?></td>

		<td><?php echo $non_wakaf_quran?></td>
		<td><?php echo $non_wakaf_qurban?></td>
		<td><?php echo $non_wakaf_bencana?></td>
		<td><?php echo $non_wakaf_fidyah?></td>
		
		<td><?php echo $str_persen_ambil?></td> 
<?	

	//get key yg dipakai smua komp
	$totPendapatan=0; 
	$totPotongan=0;
	
	$acuan_trans = $hasil->ACUAN_TRANSPORT;					
	$tunj_transport = $hasil->TUNJ_TRANSPORT;
	$tunj_pengambilan= $hasil->TUNJ_PENGAMBILAN; $str_tunj_pengambilan="";
	$insentif_pengambilan= $hasil->INSENTIF_PENGAMBILAN; $str_insentif_pengambilan="";
	$bonus_prestasi= $hasil->BONUS_PRESTASI; $str_bonus_prestasi="";
	$bonus_pengembangan= $hasil->BONUS_PENGEMBANGAN;
	$insi_zakat_wakaf_tunai= $hasil->INSI_ZAKAT_WAKAF_TUNAI;
	$pengembalian_40= $hasil->PENGEMBALIAN_40;
	$tunj_jabatan = $hasil->TUNJ_JABATAN;
	$tunj_prestasi = $hasil->TUNJ_PRESTASI;
	$bonus_patungan_sapi = $hasil->BONUS_PATUNGAN_SAPI;
	$bonus_qurban_sapi = $hasil->BONUS_QURBAN_SAPI;
	$koreksi = $hasil->KOREKSI;
	$penyesuaian = $hasil->PENYESUAIAN;

$total_fundraising = $sumrutin_real+$rutin_infaq_selisih+$rutin_zakat_selisih +$insi_infaq_paid + $insi_zakat_paid+$insi_zakat_fitrah+$insi_wakaf_icmb+$insi_wakaf_stainim+$insi_wakaf_masjid+$non_wakaf_quran+$non_wakaf_qurban+$non_wakaf_bencana+$non_wakaf_fidyah;


$totPendapatan=$tunj_transport+$tunj_pengambilan+$insentif_pengambilan+$bonus_prestasi+$bonus_pengembangan+$insi_zakat_wakaf_tunai+$tunj_jabatan+$tunj_prestasi +$bonus_patungan_sapi + $bonus_qurban_sapi+ $koreksi+ $penyesuaian;

$pot_dansos=$hasil->POT_DANSOS;
$zakat=$hasil->POT_ZAKAT;
$lain_lain=$hasil->LAIN_LAIN;
$angsuran=$hasil->ANGSURAN;
$totPotongan=$pot_dansos+$zakat+$lain_lain+$angsuran;

?>

<td><?php echo $tunj_transport;?></td>
<td><?php echo ($str_tunj_pengambilan==""?$tunj_pengambilan:$str_tunj_pengambilan);?></td>
<td><?php echo ($str_insentif_pengambilan==""?$insentif_pengambilan:$str_insentif_pengambilan);?></td>
<td><?php echo ($str_bonus_prestasi==""?$bonus_prestasi:$str_bonus_prestasi);?></td>
<td><?php echo $bonus_pengembangan;?></td>
<td><?php echo $insi_zakat_wakaf_tunai;?></td>
<td bgcolor="#ffffcc"><?php echo $bonus_patungan_sapi;?></td>
<td bgcolor="#ffffcc"><?php echo $bonus_qurban_sapi;?></td>
<td bgcolor="#ffffcc"><?php echo $koreksi;?></td>
<td bgcolor="#ffffcc"><?php echo $penyesuaian;?></td>
<td bgcolor="#ffffcc"><?php echo $pengembalian_40;?></td>
<td><?php echo $tunj_jabatan;?></td>
<td><?php echo $tunj_prestasi;?></td>
<td><?php echo round($totPendapatan,0);?></td>

<td><?php echo $pot_dansos;?></td>
<td><?php echo $zakat;?></td>
<!-- angsuran/cicilan MYM saat ini di set di gaji transport -->
<td><?php echo $angsuran;?></td>
<td bgcolor="#ffffcc"><?php echo $lain_lain ;?></td>
<td><?php echo round($totPotongan,0) ;?></td>
<td><?php echo round($totPendapatan-$totPotongan,0);?></td>
<td><?php echo round($total_fundraising,0);?></td>
<td>  </td>
<td><?php echo number_format($tunj_transport,0,',','.')?></td>
<td><?php echo round($totPendapatan-($tunj_transport+$tunj_jabatan),0);?></td>
<td><?php echo round($totPotongan,0);?></td>
<td><?php echo round($totPendapatan-($totPotongan+$tunj_transport+$tunj_jabatan),0);?></td>

<?		
		$i++;
		}
	}
?>			

                                    </tbody>
                                </table>
                          