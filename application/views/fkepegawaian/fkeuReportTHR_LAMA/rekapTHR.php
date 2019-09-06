<?	
	$viewKop=$this->commonlib->tableKop('KEU-PAYROLL',$title, '00', '__','__');
	echo $viewKop;
	//echo $strMaster;
	if ($display==0){ // autoscroll display
?>
 <div class="row" style="overflow:scroll; height:550px">
	<div class=".col-lg-12" >
	<?}?>
<table class='borderless' >
<?	$grandTotal=0;
	//echo "<tr><td >".print_r($arrCabang).$strx."</td></tr>";
	foreach ($arrCabang as $cabang){
	
	$totalStaff=0;
	$totalFR=0;
	$totalFO=0;
	echo "<tr><td align=center><h3>Cabang : ".$cabang->KOTA."</h3></td></tr>";
	?>
	<tr><td>
	<table class='bordered' >
	<thead>
	<tr><th colspan=7>STAFF</th></TR>
	<tr><th >NO</th><th >NIK</th><th COLSPAN=3>NAMA</th><th>MASA&nbsp;KERJA</th><th>NOMINAL THR</th></tr>
	</thead>
	<tbody>	
	<?	//Kelompok Staff
	$rsStaff=$this->db->query("select t.*, p.NAMA from thr_staff t, pegawai p where t.NIK=p.NIK and TAHUN='$thn' and p.ID_JAB<>'13'and p.ID_JAB<>'14' and p.ID_CABANG=".$cabang->ID_CAB)->result();
	if (sizeof($rsStaff)==0){	
		echo "<tr align=center><td colspan='7'>Data Belum Ada</td></tr>";
	}else{
		$i=1;
		
		foreach($rsStaff as $row){
			$masakerja=$row->MASA_KERJA_BLN;
			if ($masakerja<12){				
				$strmasakerja=number_format($masakerja,0,',','')." Bln";
			}else{
				$strmasakerja=floor($masakerja/12)." Thn, ".($masakerja%12)." Bln";
			}
			echo "<tr>";
			echo "<td>".$i."</td>";
			echo "<td>".$row->NIK."</td>";
			echo "<td COLSPAN=3>".$row->NAMA."</td>";
			echo "<td>".$strmasakerja."</td>";
			echo "<td style=\"text-align:right\">Rp.&nbsp;".number_format($row->NOMINAL_THR,2,',','.')."</td>";
			echo "</tr>";
			$totalStaff+=$row->NOMINAL_THR;
			$i++;

		}
		echo "<tr><td colspan=6><b>Total THR Staff Cabang ".$cabang->KOTA."</b></td><td style=\"text-align:right\"><b>Rp.&nbsp;".number_format($totalStaff,2,',','.')."</b></td></tr>";
	}
	
?>
<tr><th colspan=7>FO</th></TR>
<tr><th >NO</th><th >NIK</th><th COLSPAN=2>NAMA</th><th>MASA&nbsp;KERJA</th><th>LEVEL</th><th>NOMINAL THR</th></tr>
<?	//Kelompok FO
	$rsFO=$this->db->query("select t.*, p.NAMA, (select LEVEL from mst_gaji_fo where ID=t.ID_LEVEL) NMLEVEL from thr_fo t, pegawai p where t.NIK=p.NIK and TAHUN='$thn' and p.ID_JAB=14 and p.ID_CABANG=".$cabang->ID_CAB)->result();	
			
	if (sizeof($rsFO)==0){	
		echo "<tr align=center><td colspan='7'>Data Belum Ada</td></tr>";
	}else{
		$i=1;
		
		foreach($rsFO as $row){
			$masakerja=$row->MASA_KERJA;
			if ($masakerja<12){				
				$strmasakerja=number_format($masakerja,0,',','')." Bln";
			}else{
				$strmasakerja=floor($masakerja/12)." Thn, ".($masakerja%12)." Bln";
			}
			echo "<tr>";
			echo "<td>".$i."</td>";
			echo "<td>".$row->NIK."</td>";
			echo "<td COLSPAN=2>".$row->NAMA."</td>";
			echo "<td>".$strmasakerja."</td>";
			echo "<td>".$row->NMLEVEL."</td>";
			echo "<td style=\"text-align:right\">Rp.&nbsp;".number_format($row->NOMINAL_THR,2,',','.')."</td>";
			echo "</tr>";
			$i++;
			$totalFO+=$row->NOMINAL_THR;
		}
		echo "<tr><td colspan=6><b>Total THR FO Cabang ".$cabang->KOTA."</b></td><td style=\"text-align:right\"><b>Rp.&nbsp;".number_format($totalFO,2,',','.')."</b></td></tr>";
	}
	
?>
<tr><th colspan=7>FR</th></TR>
<tr><th >NO</th><th >NIK</th><th>NAMA</th><th>KELOMPOK</th><th>MASA&nbsp;KERJA</th><th>TARGET TERMIN 2</th><th>NOMINAL THR</th></tr>
<?	//Kelompok Staff
	$rsFR=$this->db->query("select t.*, p.NAMA from thr_fr t, pegawai p where t.NIK=p.NIK and TAHUN='$thn' and p.ID_JAB=13 and p.ID_CABANG=".$cabang->ID_CAB)->result();
	if (sizeof($rsFR)==0){	
		echo "<tr align=center><td colspan='7'>Data Belum Ada</td></tr>";
	}else{
		$i=1;
		
		foreach($rsFR as $row){
			$masakerja=$row->MASA_KERJA;
			if ($masakerja<12){				
				$strmasakerja=number_format($masakerja,0,',','')." Bln";
			}else{
				$strmasakerja=floor($masakerja/12)." Thn, ".($masakerja%12)." Bln";
			}
			echo "<tr>";
			echo "<td>".$i."</td>";
			echo "<td>".$row->NIK."</td>";
			echo "<td>".$row->NAMA."</td>";
			echo "<td>".$row->KELOMPOK."</td>";
			echo "<td>".$strmasakerja."</td>";
			echo "<td>".$row->TARGET_ALL_T2."</td>";
			echo "<td style=\"text-align:right\">Rp.&nbsp;".number_format($row->NOMINAL_THR,2,',','.')."</td>";
			echo "</tr>";
			$i++;
			$totalFR+=$row->NOMINAL_THR;
		}
		echo "<tr><td colspan=6><b>Total THR FR Cabang ".$cabang->KOTA."</b></td><td style=\"text-align:right\"><b>Rp.&nbsp;".number_format($totalFR,2,',','.')."</b></td></tr>";
	}
	
?>
</tbody></table>
</td></tr>

<?
	$grandTotal+=$totalStaff+$totalFO+$totalFR;
}

echo "<tr><th style=\"text-align:center\"><h3>Grand Total THR TAHUN ".$thn." : <b><u>Rp.&nbsp;".number_format($grandTotal,2,',','.')."</u></b></h3></th></tr>";
?>
</table>
<?  if ($display==0){ // autoscroll display?>
</DIV>
</DIV>
<? } ?>
<? if ($display==0){
	$param=$thn."_".$id_cabang."_1";
?>
<br>
<div class="row" style="text-align:center">
	<div class="col-md-12">	
		<a href="<?=base_url('keuReportTHR/rekapTHR/'.$param)?>" class="btn btn-success">Cetak/Download</a><br>		
	</div>
</div>	
<?}?>