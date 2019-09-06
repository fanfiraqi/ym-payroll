<table border="1">
                            <thead>
                          <tr >
							<th  rowspan=2 >NO</th>
							<th  rowspan=2>NIK</th>
							<th  rowspan=2>NAMA</th>
							<th  rowspan=2>KANTOR</th>
							<th  rowspan=2>REK BSM</th>							
							<th  rowspan=2>JABATAN</th>
							<th rowspan=2>TGL MASUK</th>						
							<th rowspan=2>MASA KERJA</th>						
							<th rowspan=2>TARGET PENGAMBILAN</th>
							<th rowspan=2>REALISASI PENGAMBILAN</th>
							<th rowspan=2>TOTAL DONASI CABANG YG MASUK</th>							
							<th colspan=3>REALISASI PENGEMBANGAN (PRIBADI)</th>
							<th rowspan=2>PRESENTASE PENGAMBILAN</th>
							<th colspan=3>BONUS</th>
							<th rowspan=2>PENYESUAIAN</th>
							<th rowspan=2>TOTAL PENDAPATAN</th>
							<th colspan=3>POTONGAN</th>
							<th rowspan=2>TOTAL POTONGAN</th>
							<th rowspan=2>TOTAL TERIMA</th>
							<th rowspan=2>SLIP</th>
						  </tr>

						
						  <tr>
							<th>INSIDENTIL</th>
							<th>ZIS RUTIN</th>
							<th>WAKAF</th>
							<th>JABATAN</th>
							<th>KACAB</th>
							<th>PRESTASI</th>
							<th>DANSOS (2 persen)</th>
							<th>ZAKAT (2,5 persen)</th>
							<th>LAIN2</th>
							</tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan=\"".$colspanAll."\">Data Belum Ada</td></tr>";
	}else{
		$i=1;	//as row
		$blnIdk=$this->arrIntBln;
		//cek var bln tahun rekap absensi value DARI BULAN SEBLMNYA
		if ($bln==1){
			$bln_pre=12;
			$thn_pre=$thn-1;
		}else{
			$bln_pre=$blnIdk[$bln-1];
			$thn_pre=$thn;
		}

		foreach($row as $hasil){
			$masakerja=$hasil->MASA_KERJA_BLN;		//dlm bulan
			//hitung masa kerja
			if ($masakerja<12){				
				$strmasakerja=number_format($hasil->MASA_KERJA_BLN,0,',','')." Bln";	
				$thnMasakerja=0;
			}else{
				$strmasakerja=floor($hasil->MASA_KERJA_BLN/12)." Thn, ".($hasil->MASA_KERJA_BLN%12)." Bln";
				$thnMasakerja=floor($hasil->MASA_KERJA_BLN/12);
			}

		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
		$rscab=$this->gate_db->query("select * from mst_cabang where id_cabang=".$hasil->ID_CABANG )->row();
		
		$persen_ambil=$hasil->PERSEN_AMBIL;
		$str_persen_ambil=$persen_ambil." %" ;

		$totPendapatan=$hasil->TUNJAB + $hasil->BONUS_KACAB + $hasil->TUNJ_PRESTASI + $hasil->PENYESUAIAN;		//-penyesuaian
		$totPotongan=$hasil->DANSOS + $hasil->ZAKAT + $hasil->LAIN;	//-lain2

?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?></td>
		<td><?=str_replace(" "," ",$hasil->NAMA)?></td>
		<td><?=str_replace(" "," ",$rscab->kota)?></td>
		<td><?=str_replace(" "," ",$hasil->REKENING)?></td>
		<td><?=str_replace("&"," dan ",$rsjab->nama_jab)?></td>
		<td><?=str_replace(" "," ",$hasil->TGL_AKTIF)?></td>
		<td><?php echo str_replace(" "," ",$strmasakerja);?></td>	

		<td><?php echo round($hasil->TARGET_PENGAMBILAN,0);?></td>
		<td><?php echo round($hasil->REAL_PENGAMBILAN,0);?></td>
		<td><?php echo round($hasil->ALL_DONASI,0) ;?></td>
		<td><?php echo round($hasil->KACAB_INSI,0) ;?></td>
		<td><?php echo round($hasil->KACAB_RUTIN,0) ;?></td>
		<td><?php echo round($hasil->KACAB_WAKAF,0) ;?></td>
		<td><?php echo $str_persen_ambil ;?></td>

		<td><?php echo round($hasil->TUNJAB,0) ;?></td>
		<td><?php echo round($hasil->BONUS_KACAB,0) ;?></td>
		<td><?php echo round($hasil->TUNJ_PRESTASI,0) ;?></td>
		<td bgcolor="#ffffcc"><?php echo round($hasil->PENYESUAIAN,0);?></td>
		<td><?php echo round($totPendapatan,0);?></td>
<!-- potongan -->
		<td><?php echo round($hasil->DANSOS,0)  ;?></td>
		<td><?php echo round($hasil->ZAKAT,0)  ;?></td>
		<td bgcolor="#ffffcc"><?php echo round($hasil->LAIN,0);?></td>
		<td><?php echo round($totPotongan,0)  ;?></td>
		<td><?php echo round($totPendapatan-$totPotongan,0);?></td>
	<td></td>
</tr>
<?
		$i++;
		}
	}
?>			

	</tbody>
 </table>