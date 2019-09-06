<?	
	$viewKop=$this->commonlib->tableKop('KEU-PAYROLL',$title, '00', '__','__');
	echo $viewKop;
	//echo $strMaster;
	
?>
<table class='borderless' >
<?	$grandTotal=0;
	foreach ($arrCabang as $cabang){
	echo "<tr><td align=center><h3>Cabang : ".$cabang->KOTA."</h3></td></tr>";
	$str="SELECT T1.ID_DIV IDLEV1, T1.NAMA_DIV AS LEV1, T2.ID_DIV AS IDLEV2,T2.NAMA_DIV AS LEV2, T3.ID_DIV AS IDLEV3,T3.NAMA_DIV AS LEV3
			FROM mst_divisi AS t1
			LEFT JOIN mst_divisi AS t2 ON t2.id_div_parent = t1.id_div
			LEFT JOIN mst_divisi AS t3 ON t3.id_div_parent = t2.id_div where t1.id_div=1 order by idlev2, idlev3 ";
	$rsDiv=$this->db->query($str)->result();
	$lvl2Reset=0;
	$level1=true;
	$idL2Temp=0;
	?>
	<tr><td>
	<table class='bordered' >
	<thead><tr><th colspan=2>KELOMPOK</th><th>GAJI POKOK</th><th>UM,TRANSPORT, TUNJ2 NON-ANAK</th><th>TUNJ.ANAK</th><th>BONUS2</th><th>POTONGAN</th><th>TOTAL</th></tr></thead>
	<tbody>
	<?	
		$kel1=0;
		$kel2=0;
		$kel3=0;
		$kel4=0;
		$kel5=0;
		foreach ($rsDiv as $divLoop){
			
			$scekL2="select distinct id_div from mst_struktur where id_cab=".$cabang->ID_CAB." and id_div=".$divLoop->IDLEV2;
			$cnumL2=$this->db->query($scekL2)->num_rows();
			
			$scekL3="select distinct id_div from mst_struktur where id_cab=".$cabang->ID_CAB." and id_div='".$divLoop->IDLEV3."' order by id_div";
			$cnumL3=$this->db->query($scekL3)->num_rows();
			
			if ($idL2Temp!=$divLoop->IDLEV2){
				$lvl2Reset=0;
				$level1=true;
			}
			if ($cnumL2>=1 &&   $level1==true){	
				//$myStr=$this->report_model->myStr($cabang->ID_CAB, $divLoop->IDLEV2);
				$staffPayroll=$this->report_model->getStaff_payroll($cabang->ID_CAB, $divLoop->IDLEV2, $thn, $bln);
				$kel1+=$staffPayroll->KEL1;
				$kel2+=$staffPayroll->KEL2;
				$kel3+=$staffPayroll->KEL3;
				$kel4+=$staffPayroll->KEL4;
				$kel5+=$staffPayroll->KEL5;
				echo "<tr><td COLSPAN=2>Divisi&nbsp;:&nbsp;".$divLoop->LEV2."</td>";
				$TOTAL=($staffPayroll->KEL1+$staffPayroll->KEL2+$staffPayroll->KEL3+$staffPayroll->KEL4)-$staffPayroll->KEL5;
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL1,0,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL2,0,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL3,0,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL4,0,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL5,0,',','.')."</td>";	
				echo "<td>Rp.&nbsp;".number_format($TOTAL,0,',','.')."</td>";	
				echo "</TR>";
				$level1=false;
				$idL2Temp=$divLoop->IDLEV2;
				
			}
			if ($cnumL3>=1){
				if ($divLoop->IDLEV3==9){	//ZIS => FO & FR
					echo "<tr><td>&nbsp;</td><td colspan=6>Divisi&nbsp;:&nbsp;".$divLoop->LEV3."</td></TR>";
					echo "<tr><td>&nbsp;</td><td>FR</td>";
					//$myStr=$this->report_model->myStr($cabang->ID_CAB, $divLoop->IDLEV3);
					$frPayroll=$this->report_model->getFR_payroll($cabang->ID_CAB, $divLoop->IDLEV3, $thn, $bln);
					$TOTALFR=($frPayroll->KEL1+$frPayroll->KEL2+$frPayroll->KEL3+$frPayroll->KEL4)-$frPayroll->KEL5;
					$kel1+=$frPayroll->KEL1;
					$kel2+=$frPayroll->KEL2;
					$kel3+=$frPayroll->KEL3;
					$kel4+=$frPayroll->KEL4;
					$kel5+=$frPayroll->KEL5;
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL1,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL2,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL3,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL4,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($frPayroll->KEL5,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($TOTALFR,0,',','.')."</td>";
					echo "</TR>";
					echo "<tr><td>&nbsp;</td><td>FO</td>";
					$foPayroll=$this->report_model->getFO_payroll($cabang->ID_CAB, $divLoop->IDLEV3, $thn, $bln);
					$TOTALFO=($foPayroll->KEL1+$foPayroll->KEL2+$foPayroll->KEL3+$foPayroll->KEL4)-$foPayroll->KEL5;
					$kel1+=$foPayroll->KEL1;
					$kel2+=$foPayroll->KEL2;
					$kel3+=$foPayroll->KEL3;
					$kel4+=$foPayroll->KEL4;
					$kel5+=$foPayroll->KEL5;
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL1,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL2,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL3,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL4,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($foPayroll->KEL5,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($TOTALFO,0,',','.')."</td>";
					echo "</TR>";
					//echo var_dump($frPayroll);

				}else{
					
					echo "<tr><td>&nbsp;</td><td>Divisi&nbsp;:&nbsp;".$divLoop->LEV3."</td>";
					$staffPayroll=$this->report_model->getStaff_payroll($cabang->ID_CAB, $divLoop->IDLEV3, $thn, $bln);
					$kel1+=$staffPayroll->KEL1;
					$kel2+=$staffPayroll->KEL2;
					$kel3+=$staffPayroll->KEL3;
					$kel4+=$staffPayroll->KEL4;
					$kel5+=$staffPayroll->KEL5;
					$TOTAL=($staffPayroll->KEL1+$staffPayroll->KEL2+$staffPayroll->KEL3+$staffPayroll->KEL4)-$staffPayroll->KEL5;
					echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL1,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL2,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL3,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL4,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($staffPayroll->KEL5,0,',','.')."</td>";
					echo "<td>Rp.&nbsp;".number_format($TOTAL,0,',','.')."</td>";
					echo "</TR>";
					//echo var_dump($staffPayroll);
				}
			}
		}
		//total
		$subTotal=$kel1+$kel2+$kel3+$kel4+$kel5;
		$grandTotal+=$subTotal;
		echo "<tr>";
		echo "<th colspan=2>Sub Total</th>";
		echo "<th>Rp.&nbsp;".number_format($kel1,0,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($kel2,0,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($kel3,0,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($kel4,0,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($kel5,0,',','.')."</th>";
		echo "<th>Rp.&nbsp;".number_format($subTotal,0,',','.')."</th>";
		echo "</tr>";
?>
</tbody></table>
</td></tr>
<?

}

echo "<tr><th style=\"text-align:center\"><h3>Grand Total Bulan ".strtoupper($strBulan)." ".$thn." : <b><u>Rp.&nbsp;".number_format($grandTotal,0,',','.')."</u></b></h3></th></tr>";
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