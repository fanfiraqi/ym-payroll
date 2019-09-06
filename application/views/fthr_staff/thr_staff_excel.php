<table border="1">
                           <thead>
                           <tr >
							<th >NO</th>
							<th >NIK</th>
							<th >NAMA</th>
							<th >JABATAN</th>
							<th >TGL MASUK</th>
							<th >MASA KERJA</th>
							<th >S/M/GM</th>
<?	$strMaster="select * from mst_komp_gaji  where isactive=1 and is_thr=1 and is_staff='on' order by ID";
	$master=$this->db->query($strMaster)->result();
	$p=1;	$th=""; $nominal=0;
	foreach($master as $rowmaster){		
		//$th.="<th>".$rowmaster->NAMA."</th>";
		echo "<th>".$rowmaster->NAMA."(".$rowmaster->FLAG.")</th>";
		$p++;
	}
	$colspanAll=$p+8;
?>														
							<th >TOTAL PENDAPATAN</th>
							<th >SLIP</th>
						  </tr>
						  
                         </thead>
                                    <tbody>
<?	
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan=\"".$colspanAll."\">Data Belum Ada</td></tr>";
	}else{
		$i=1;
		foreach($row as $hasil){
			$masakerja=$hasil->masa_kerja_bln;		//dlm bulan
			//hitung masa kerja
			if ($masakerja<12){				
				$strmasakerja=number_format($hasil->masa_kerja_bln,0,',','')." Bln";	
				$thnMasakerja=0;
			}else{
				$strmasakerja=floor($hasil->masa_kerja_bln/12)." Thn, ".($hasil->masa_kerja_bln%12)." Bln";
				$thnMasakerja=floor($hasil->masa_kerja_bln/12);
			}

		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?></td>
		<td><?=str_replace(" "," ",$hasil->NAMA)?></td>
		<td><?=str_replace("&"," dan ",$rsjab->nama_jab)?></td>
		<td><?=str_replace(" "," ", strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF)))?></td>
		<td><?=str_replace(" "," ",$strmasakerja);?></td>
<?	//get key yg dipakai smua komp
	$sumPer_row=0;
	$j=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	$rsgrade=$this->db->query("select * from mst_grade_cabang where id_cabang=".$id_cabang)->row();
	$rsklaster=$this->gate_db->query("select klaster, kelompok_gaji from mst_jabatan where id_jab=".$id_jab)->row();
	$klaster=$rsklaster->klaster;
	$grade=$rsgrade->grade;
	$gapok=0;
	echo '<td>'.$rsklaster->kelompok_gaji.'</td>';
	foreach($master as $rowmaster){
		
		$cekmasuk="";$str1=$str2=$str3=$str4=$str5=$str6="";
		switch ($rowmaster->ID){
			case "1":	//gapok, table
				$nominal = $hasil->gapok;	
				$gapok = $nominal;	
				$cekmasuk="1";
				break;
			case "2":	//Acuan Uang makan, table
				$nominal = $hasil->uang_makan;	
				$cekmasuk="2";
				break;
			case "5":	//Insentif Kehadiran, var
				$nominal = $hasil->tunj_keluarga;	
				$cekmasuk="3";
				break;
			case "6":	//Tj. Masa Kerja, table
				$nominal = $hasil->tunj_masakerja;	
				$cekmasuk="6";
				break;
			case "7":	//Tj. Jabatan, table
				$nominal = $hasil->tunj_jabatan;	
				$cekmasuk="7";
				break;
			default: 
				$nominal=0;$cekmasuk="default";
				break;

		}
		echo "<td>".$nominal."</td>";
		
		if ($rowmaster->FLAG=='+'){ 
			$sumPer_row+=$nominal;
		}else{
			$sumPer_row-=$nominal;
		}
		$j++;
		
	}


?>
	<td><?=$sumPer_row;?></td>
	<td> </td>
	</tr>			
			
<?
		$i++;
		}
	}
?>			

	</tbody>
</table>