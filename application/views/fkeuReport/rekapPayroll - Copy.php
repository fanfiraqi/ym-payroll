<?	
	$viewKop=$this->commonlib->tableKop('KEU-PAYROLL',$title, '00', '__','__');
	echo $viewKop;
	//echo $strMaster;
	
?>
<table class='borderless' >
<?	$grandTotal=0;

	foreach ($arrCabang as $cabang){
	echo "<tr><td align=center><h3>Cabang : ".$cabang->KOTA."</h3></td></tr>";
		$kel1=0;
		$kel2=0;
		$kel3=0;
		$kel4=0;
		$kel5=0;
		$kel_Anyar=0;
	?>
	<tr><td>
	<table class='bordered' >
	<thead><tr><th colspan=2>KELOMPOK</th><th>GAJI POKOK</th><th>UM,TRANSPORT, TUNJ2 NON-ANAK</th><th>TUNJ.UBUDIAH</th><th>TUNJ.ANAK</th><th>BONUS2</th><th>POTONGAN</th><th>TOTAL</th></tr></thead>
	<tbody>
	<?	//Kelompok Staff
				$staffPayroll=$this->report_model->getStaff_payroll($cabang->ID_CAB, $thn, $bln);
				$kel1+=$staffPayroll->KEL1;
				$kel2+=$staffPayroll->KEL2;
				$kel3+=$staffPayroll->KEL3;
				$kel4+=$staffPayroll->KEL4;
				$kel5+=$staffPayroll->KEL5;
				$kel_Anyar+=$staffPayroll->KEL_ANYAR;
				echo "<tr><td COLSPAN=2>STAFF</td>";
				$TOTAL=($staffPayroll->KEL1+$staffPayroll->KEL2+$staffPayroll->KEL3+$staffPayroll->KEL4+$staffPayroll->KEL_ANYAR)-$staffPayroll->KEL5;
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL1,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL2,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL_ANYAR,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL3,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL4,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL5,2,',','.')."</td>";	
				echo "<td>Rp.&nbsp;".number_format($TOTAL,2,',','.')."</td>";	
				echo "</TR>";

			//kelompok fr
				
					echo "<tr><td COLSPAN=2>FR</td>";
					//$myStr=$this->report_model->myStr($cabang->ID_CAB, $divLoop->IDLEV3);
					$frPayroll=$this->report_model->getFR_payroll($cabang->ID_CAB,  $thn, $bln);
					$TOTALFR=($frPayroll->KEL1+$frPayroll->KEL2+$frPayroll->KEL3+$frPayroll->KEL4+$frPayroll->KEL_ANYAR)-$frPayroll->KEL5;
					$kel1+=$frPayroll->KEL1;
					$kel2+=$frPayroll->KEL2;
					$kel3+=$frPayroll->KEL3;
					$kel4+=$frPayroll->KEL4;
					$kel5+=$frPayroll->KEL5;
					$kel_Anyar+=$frPayroll->KEL_ANYAR;
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL1,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL2,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL_ANYAR,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL3,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL4,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL5,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($TOTALFR,2,',','.')."</td>";
					echo "</TR>";
					echo "<tr><td COLSPAN=2>FO</td>";
			//kelompok FO
					$foPayroll=$this->report_model->getFO_payroll($cabang->ID_CAB, $thn, $bln);
					$TOTALFO=($foPayroll->KEL1+$foPayroll->KEL2+$foPayroll->KEL3+$foPayroll->KEL4+$foPayroll->KEL_ANYAR)-$foPayroll->KEL5;
					$kel1+=$foPayroll->KEL1;
					$kel2+=$foPayroll->KEL2;
					$kel3+=$foPayroll->KEL3;
					$kel4+=$foPayroll->KEL4;
					$kel5+=$foPayroll->KEL5;
					$kel_Anyar+=$foPayroll->KEL_ANYAR;
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL1,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL2,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL_ANYAR,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL3,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL4,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL5,2,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($TOTALFO,2,',','.')."</td>";
					echo "</TR>";
		//kelompok usaha
		$strUsaha="select distinct ID_DIV, NAMA_DIV from mst_divisi where id_div_parent in (22, 16) ";
		$rsDiv=$this->db->query($strUsaha)->result();
		foreach ($rsDiv as $rsUsaha){
				$usahaPayroll=$this->report_model->getUsaha_payroll($cabang->ID_CAB, $rsUsaha->ID_DIV, $thn, $bln);
				$kel1+=$usahaPayroll->KEL1;
				$kel2+=$usahaPayroll->KEL2;
				$kel3+=$usahaPayroll->KEL3;
				$kel4+=$usahaPayroll->KEL4;
				$kel5+=$usahaPayroll->KEL5;
				$kel_Anyar+=$usahaPayroll->KEL_ANYAR;
				echo "<tr><td COLSPAN=2>".$rsUsaha->NAMA_DIV."</td>";
				$TOTAL=($usahaPayroll->KEL1+$usahaPayroll->KEL2+$usahaPayroll->KEL3+$usahaPayroll->KEL4+$usahaPayroll->KEL_ANYAR)-$usahaPayroll->KEL5;
				echo "<td>Rp.&nbsp;".number_format($usahaPayroll->KEL1,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($usahaPayroll->KEL2,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($usahaPayroll->KEL_ANYAR,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($usahaPayroll->KEL3,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($usahaPayroll->KEL4,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($usahaPayroll->KEL5,2,',','.')."</td>";	
				echo "<td>Rp.&nbsp;".number_format($TOTAL,2,',','.')."</td>";	
				echo "</TR>";
		}
		
		//kelompok PAS
		
		
		$subTotal=$kel1+$kel2+$kel3+$kel4+$kel5;
		$grandTotal+=$subTotal;
		echo "<tr>";
		echo "<th colspan=2>Sub Total</th>";
		echo "<th>Rp.&nbsp;".number_format($kel1,2,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($kel2,2,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($kel_Anyar,2,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($kel3,2,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($kel4,2,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($kel5,2,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($subTotal,2,',','.')."</th>";
		echo "</tr>";
?>
</tbody></table>
</td></tr>
<?
	
}

echo "<tr><th style=\"text-align:center\"><h3>Grand Total Bulan ".strtoupper($strBulan)." ".$thn." : <b><u>Rp.&nbsp;".number_format($grandTotal,2,',','.')."</u></b></h3></th></tr>";
?>
</table>
<? if ($display==0){
	$param=$thn."_".$bln."_1";
?>
<br>
<div class="row" style="text-align:center">
	<div class="col-md-12">	
		<a href="<?=base_url('keuReportPayroll/rekapPayroll/'.$param)?>" class="btn btn-success">Cetak/Download</a><br>		
	</div>
</div>	
<?}?>