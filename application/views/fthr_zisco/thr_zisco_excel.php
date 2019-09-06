<table border="1">
                           <thead>
                           <tr >
							<th rowspan=2>NO</th>
							<th rowspan=2>NIK</th>
							<th rowspan=2>NAMA</th>
							<th  rowspan=2>KANTOR</th>
							<th  rowspan=2>JABATAN</th>
							<th  rowspan=2>TGL MASUK</th>
							<th  rowspan=2>KONTRAK KERJA</th>
							<th  rowspan=2>MASA KERJA</th>
							<th  rowspan=2>STATUS PEGAWAI</th>
							<th  rowspan=2>KETIDAKHADIRAN ZISCO</th>
<?	$strMaster="select * from mst_komp_gaji  where isactive=1 and is_thr=1 and is_zisco='on' order by flag, ID";
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
		//echo "<th rowspan=2>".$rowmaster->NAMA."(".$rowmaster->FLAG.")"."</th>";
		
	}
	$colspanAll=$p+8;
?>														
							<th colspan="<?php echo $p-1?>">PENDAPATAN</th>
							<th rowspan=2>TOTAL PENDAPATAN</th>
							<th colspan="<?php echo $t-1?>">POTONGAN</th>
							<th rowspan=2>TOTAL POTONGAN</th>
							<th rowspan=2>TOTAL TERIMA</th>
							<th rowspan=2>SLIP</th>
						  </tr>
						   <tr>							
						  <?php echo $thPend.$thPot?>
						  </tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan='".$colspanAll."'>sql= $str</td></tr>";
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
		$rscab=$this->gate_db->query("select * from mst_cabang where id_cabang=".$hasil->ID_CABANG )->row();
		$rs_stspeg=$this->db->query("select value1 from gen_reff where reff='STSPEGAWAI' and id_reff=".$hasil->STATUS_PEGAWAI)->row();
		$jml_tidak_hadir=0;
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?></td>
		<td><?=str_replace(" "," ",$hasil->NAMA)?></td>
		<td><?=str_replace(" "," ",$rscab->kota)?></td>
		<td><?=str_replace("&"," dan ",$rsjab->nama_jab)?></td>
		<td><?=str_replace(" "," ", strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF)))?></td>
		<td><?=str_replace(" "," ", strftime("%d %B %Y",strtotime($hasil->TGL_AWAL_KONTRAK)))?></td>
		<td><?=str_replace(" "," ",$strmasakerja);?></td>
		<td><?=str_replace(" "," ", $rs_stspeg->value1)?></td>
		<td><?php echo $jml_tidak_hadir?></td>		

<?	//get key yg dipakai smua komp
	$sumPer_row=0; 
	$gapok=0;$THT=0;
	$sumPotPer_row=0; 
	$j=0;
	$display_angsuran=0; $cicilke=0; $id_header=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	/*$rsgrade=$this->db->query("select * from mst_grade_cabang where id_cabang=".$id_cabang)->row();
	$grade=$rsgrade->grade;*/
	$rsklaster=$this->gate_db->query("select klaster, kelompok_gaji from mst_jabatan where id_jab=".$id_jab)->row();
	$klaster=$rsklaster->klaster;
	
	$sts_pegawai=$hasil->STATUS_PEGAWAI;	

	foreach($master as $rowmaster){
		$bgcolor=""; $readonly=true;
		$cekmasuk="";$str1=$str2=$str3=$str4=$str5=$str6="";
		if ($rowmaster->FLAG=='+'){
		switch ($rowmaster->ID){
			case "9":	//Tj. Transport, Tabel
				$nominal=$hasil->uang_transport;				
				break;
			case "10":	//Tj. Jabatan zisco, var
				$nominal=$hasil->tunj_jabatan;	
				break;
			case "13":	//Penyesuaian
				$nominal=$hasil->penyesuaian;		
				$bgcolor="#ffffcc";$readonly=false;
				break;			
			
			case "23":	//Koreksi
				$nominal=$hasil->koreksi;		
				$bgcolor="#ffffcc";$readonly=false;
				break;
			
			default: 
				$nominal=0;
				$bgcolor="#ffffcc";$readonly=false;
				break;

		}
		echo "<td>".$nominal."</td>";
		if ($rowmaster->FLAG=='+'){ 
			$sumPer_row+=$nominal;
		}else{
			$sumPer_row-=$nominal;
		}
		
		
	
	if ($rowmaster->ID==23){?>
			<td><?=round($sumPer_row,0);?></td>

		<? }

	}else{
		//POTONGAN
			
			switch ($rowmaster->ID){
				case "15":	//dansos
					$nominal=$hasil->potongan_dansos;	
					$bgcolor="#ffffcc";$readonly=false;
					break;
				/*case "24":	//Angsuran Pinjaman
					$jmlcicilan=$rsPinj->JML_CICILAN;
					$cicilke=$rsPinj->CICILAN_KE;
					$display_angsuran=1;
					$nominal=$jmlcicilan;
					$bgcolor="#ffffcc";$readonly=false;
					break;*/
				case "36":	//Pot lain2
					$nominal=$hasil->potongan_lain;			
					$bgcolor="#ffffcc";$readonly=false;
					break;
			}
			echo "<td>";
			/*if ($display_angsuran==1){
				echo 'Cicilan ke:'.$cicilke.'<input type="hidden" name="cicilke_'.$i.'" id="cicilke_'.$i.'" value="'.$cicilke.'"><input type="hidden" name="id_header_'.$i.'" id="id_header_'.$i.'" value="'.$id_header.'">';
			}*/
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

if ($rowmaster->ID==36){
	?>
	<td><?=round($sumPotPer_row*-1,0);?></td>
	<?
	
}?>	

<td><?=round($sumPer_row+$sumPotPer_row,0);?>

</td><td> -</td></tr>	
<?
		$i++;
		}
	}
?>			

	</tbody>
</table>