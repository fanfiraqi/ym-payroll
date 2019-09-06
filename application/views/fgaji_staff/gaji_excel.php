<table border="1">
                           <thead>
                           <tr >
							<th rowspan=2>NO</th>
							<th rowspan=2>NIK</th>
							<th rowspan=2>NAMA</th>
							<th rowspan=2>KANTOR</th>
							<th rowspan=2>JABATAN</th>
							<th rowspan=2>TGL MASUK</th>

							<th rowspan=2>CUTI</th>
							<th rowspan=2>SAKIT</th>
							<th rowspan=2>IJIN</th>
							<th rowspan=2>ALPA</th>
							<th rowspan=2>LEMBUR</th>
							<th colspan=3>T10 MENIT</th>							
							<th colspan=2>PULANG CEPAT</th>

							<th rowspan=2>MASA KERJA</th>
							<th rowspan=2>S/M/GM</th>
							
<?	$strMaster="select * from mst_komp_gaji  where isactive=1 and  is_staff='on' order by ID";
	$master=$this->db->query($strMaster)->result();
	$p=1;
	$t=1;
	$thPend=""; 
	$thPot=""; $nominal=0;
	foreach($master as $rowmaster){	
		if ($rowmaster->FLAG=='+'){
			$thPend.="<th>".$rowmaster->NAMA."</th>";
			$p++;
		}else{
			$thPot.="<th>".$rowmaster->NAMA."</th>";
			$t++;
		}
		//echo "<th>".$rowmaster->NAMA."(".$rowmaster->FLAG.")</th>";
		
	}
	$colspanAll=$p+8+$t;
?>							
							<th colspan="<?php echo $p-1?>">PENDAPATAN</th>
							<th rowspan=2>TOTAL PENDAPATAN</th>
							<th colspan="<?php echo $t-1?>">POTONGAN</th>
							<th rowspan=2>TOTAL POTONGAN</th>
							<th rowspan=2>TOTAL TERIMA</th>
							<th rowspan=2>SLIP</th>
						  </tr>
						  <tr>
							<th >I</th>
							<th >II</th>
							<th>III</th>
							<th >JUMLAH</th>
							<th >MENIT</th>
						  <?php echo $thPend.$thPot?>
						  </tr>
						  
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan=\"".$colspanAll."\">Data Belum Ada</td></tr>";
	}else{
		$i=1;	//as row
		
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
		//echo "<tr><td colspan=$p>select * from mst_jabatan where id_jab=".$hasil->ID_JAB."<br>idjab=".$rsjab->id_jab."</td></tr>";

		
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?></td>
		<td><?=str_replace("&"," ",$hasil->NAMA)?></td>
		<td><?=str_replace("&"," ",$rscab->kota)?></td>
		<td><?=str_replace("&"," dan ",$rsjab->nama_jab)?></td>
		<td><?=str_replace(" "," ", strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF)))?></td>
		<!--  -->
		<td ><?php echo $hasil->JML_CUTI?></td>
		<td ><?php echo $hasil->JML_SAKIT?></td>
		<td ><?php echo $hasil->JML_IJIN?></td>
		<td ><?php echo $hasil->JML_ALPA?></td>
		<td ><?php echo $hasil->JML_LEMBUR?></td>
		<td ><?php echo $hasil->JML_T10_1?></td>
		<td ><?php echo $hasil->JML_T10_2?></td>
		<td ><?php echo $hasil->JML_T10_3?></td>
		
		<td ><?php echo $hasil->PULANG_CEPAT_JML?></td>
		<td ><?php echo $hasil->PULANG_CEPAT_MNT?></td>
		<!--  -->
		<td><?php echo str_replace(" "," ",$strmasakerja);?></td>
<?	//get key yg dipakai smua komp
	$sumPer_row=0; 
	$gapok=0;$THT=0;
	$sumPotPer_row=0;
	$j=0;
	$display_acuan=0; $acuan=0;
	$display_angsuran=0; $cicilke=0; $id_header=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	$rsgrade=$this->db->query("select * from mst_grade_cabang where id_cabang=".$id_cabang)->row();
	$grade=$rsgrade->grade;	
	$rsklaster=$this->gate_db->query("select klaster, kelompok_gaji from mst_jabatan where id_jab=".$id_jab)->row();
	$klaster=$rsklaster->klaster;
	$kelompok_gaji=$rsklaster->kelompok_gaji;
	//$bgcolor="";
	echo '<td>'.$rsklaster->kelompok_gaji.'</td>';
	foreach($master as $rowmaster){
		$bgcolor=""; $readonly=true;
		//PENDAPATAN
		if ($rowmaster->FLAG=='+'){
		$cekmasuk="";$str1=$str2=$str3=$str4=$str5=$str6="";
		switch ($rowmaster->ID){
			case "1":	//gapok, table
				$nominal=$hasil->GAPOK;
				break;
			case "2":	//Acuan Uang makan, table
			//W7-((H7+I7+(J7*2)+M7)*(W7/25))-(K7*5000)-(L7*10000)
				$str2="select per_bulan from mst_acuan_makan where	id_cabang=".$id_cabang;
				$rsmakan=$this->db->query($str2)->row();
				if (sizeof($rsmakan)>0){
					$display_acuan=1;
					$acuan = $rsmakan->per_bulan;					
				}
				$nominal = $hasil->U_MAKAN_DITERIMA;
				break;
			case "3":	//Insentif Kehadiran, var
			//IF(H7>1;0;IF(I7>0;0;IF(J7>0;0;IF((K7+L7+M7)>3;0;150000))))
				$nominal = $hasil->I_KEHADIRAN;			
				break;
			case "4":	//Insentif Lembur, var
				//=IF(V25="S";N25*8500;0)
				$nominal=$hasil->U_LEMBUR;
			
				break;
			case "5":	//Tj. Keluarga, lain2
				$nominal=$hasil->T_KELUARGA;
				break;
			case "6":	//Tj. Masa Kerja, table
				$nominal=$hasil->T_MASAKERJA;
				break;
			case "7":	//Tj. Jabatan, table
				$nominal=$hasil->T_JABATAN;
				$bgcolor="#ffffcc";
				$readonly=false;
				break;
			case "8":	//Tj. Hari Tua (THT), table
				$nominal=$hasil->T_THT;				
				break;
			case "11":	//BPJS Kesehatan
				$nominal=$hasil->BPJS_KESEHATAN;
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "12":	//BPJS Ketenagakerjaan
				$nominal=$hasil->BPJS_NAKER;	
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "13":	//Penyesuaian
				$nominal=$hasil->T_PENYESUAIAN;
				$bgcolor="#ffffcc";$readonly=false;
				break;
			default: 
				$nominal=0;$cekmasuk="default";
				$bgcolor="#ffffcc";$readonly=false;
				break;

		}
		$nominal=round($nominal, 0);
		echo "<td>";
		/*if ($display_acuan==1){
			echo number_format($acuan,0,',','.');
		}*/
		
		echo $nominal."</td>";
		if ($rowmaster->FLAG=='+'){ 
			$sumPer_row+=$nominal;
		}else{
			$sumPer_row-=$nominal;
		}
		if ($rowmaster->ID==13){ 
			echo "<td>".round($sumPer_row,0)."</td>"; 
		}
		//$j++; => bertambah 1 utk komp
		}else{	//end pendapatan
			//POTONGAN
			
			switch ($rowmaster->ID){
			case "15":	//Dana Sosial
				$nominal=$hasil->POT_DANSOS;				
				break;
			case "16":	//THT
				$nominal=$hasil->POT_ZAKAT;				
				break;
			case "17":	//THT
				$nominal=$hasil->POT_THT;				
				break;
			case "19":	// Lain-lain
				$nominal=$hasil->POT_LAIN;
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "20":	//Family Gathering
				$nominal=$hasil->POT_FAMGATH;				
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "21":	//Iuran Qurban
				$nominal=$hasil->POT_QURBAN;		
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "22":	//Angsuran Pinjaman
				
				$display_angsuran=1;
				$nominal=$hasil->JML_ANGSURAN;
				$cicilke=$hasil->ANGSURAN_KE;
				$bgcolor="#ffffcc";$readonly=false;
				break;
			}
			echo "<td>";
			if ($display_angsuran==1){
				echo 'Cicilan ke:'.$cicilke." ";
			}
			echo round($nominal,0)."</td>";
			if ($rowmaster->FLAG=='-'){ 
				$sumPotPer_row-=$nominal;
			}else{
				$sumPotPer_row+=$nominal;
			}
		}
		$j++;
		$display_acuan=0;
	}

	

	if ($rowmaster->ID==22){ 
		echo "<td>".round($sumPotPer_row*-1,0)."</td>";	
	} 
	echo "<td>".round($sumPer_row+$sumPotPer_row,0)."</td>";
	echo "<td> link</td></tr>";
	
	$i++;
		}
	}
?>			

                                    </tbody>
                                </table>
