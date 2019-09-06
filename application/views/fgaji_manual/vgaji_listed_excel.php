<table border="1">
                           <thead>
                           <tr >
							<th  >NO</th>
							<th  >NIK</th>
							<th  >NAMA</th>
							<th  >KANTOR</th>
							<th  >JABATAN</th>
							<th  >TGL MASUK</th>
							<th  >MASA KERJA</th>
							<th  >S/M/GM</th>							
							<th >PENDAPATAN</th>
							<th >POTONGAN</th>							
							<th  >TOTAL TERIMA</th>
							<th  >SLIP</th>
						  </tr>
						  </thead>
                       <tbody>
<?	$colspanAll=12;
	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";

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
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?></td>
		<td><?=str_replace(" "," ",$hasil->NAMA)?></td>
		<td><?=str_replace(" "," ",$rscab->kota)?></td>
		<td><?=str_replace("&"," dan ",$rsjab->nama_jab)?></td>
		<td><?=str_replace(" "," ", strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF)))?></td>
		<td><?=str_replace(" "," ",$strmasakerja);?></td>
<?	//get key yg dipakai smua komp
	$sumPer_row=0; 
	$sumPotPer_row=0;
	$j=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	$rsklaster=$this->gate_db->query("select klaster, kelompok_gaji from mst_jabatan where id_jab=".$id_jab)->row();
	$klaster=$rsklaster->klaster;
	$kelompok_gaji=$rsklaster->kelompok_gaji;
	
	echo '<td>'.$rsklaster->kelompok_gaji.'</td>';
	echo "<td>".$hasil->JML_TERIMA ."</td>";
	echo "<td>".$hasil->JML_POTONGAN."</td>";
	echo "<td>".$hasil->TOTAL."</td>";
	echo "<td></td></tr>";

		$i++;
		}
	}
?>			
</tbody>
</table>