 <div class="row">
	<div class="col-xs-12">
<? $viewKop=$this->commonlib->tableKop('KEU-PAYROLL',$titlekop, '00', '__','__');
	echo $viewKop;
	//echo $strlist;
	echo '<table class="bordered" >';
	echo "<tr><th>Nama</th><th>Kantor</th><th>Jumlah Pendapatan</th><th>Jumlah Potongan</th><th>Penggajian</th></tr>";
	$strCab="";
	if ($akses=="pusat"){
		$strCab="select * from mst_cabang where is_active=1 and kota not like '%REGIONAL%' order by kota";
	}elseif($akses=="RO"){
		$strCab="select * from mst_cabang where is_active=1 and id_cabang_parent=".$this->session->userdata('auth')->ID_CABANG."  order by kota";
	}else{
		$strCab="select * from mst_cabang where is_active=1 and id_cabang=".$this->session->userdata('auth')->ID_CABANG;
	}
	
	$rscab=$this->gate_db->query($strCab)->result();
	$totall_pend=0; $totall_pot=0; $totall_pay=0;
	foreach ($rscab as $rowcab){

		$strPay=$strlist_peg." and id_cabang=".$rowcab->id_cabang." group by namapeg";
		$rspay=$this->db->query($strPay)->result();
		$pend=0; $pot=0; $payroll=0;
		$totcab_pend=0; $totcab_pot=0; $totcab_pay=0;
		if (sizeof($rspay)>0){
			
			foreach ($rspay as $rowpay){
				$pend=$rowpay->pendapatan;				
				$pot=$rowpay->potongan;
				$payroll=($rowpay->payroll<0?0:$rowpay->payroll);
				$totcab_pend+=$pend;
				$totcab_pot+=$pot;
				$totcab_pay+=$payroll;
				echo "<tr>";
				echo "<td>".$rowpay->namapeg."</td>";
				echo "<td>".$rowcab->kota."</td>";
				echo "<td>Rp.&nbsp;".number_format($pend,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($pot,2,',','.')."</td>";
				echo "<td>Rp.&nbsp;".number_format($payroll,2,',','.')."</td>";
				echo "</tr>";
			}
			echo "<tr><th colspan=2>Total</th><th>Rp.&nbsp;".number_format($totcab_pend,2,',','.')."</th><th>Rp.&nbsp;".number_format($totcab_pot,2,',','.')."</th><th>Rp.&nbsp;".number_format($totcab_pay,2,',','.')."</th></tr>";
			$totall_pend+=$totcab_pend;
			$totall_pot+=$totcab_pot;
			$totall_pay+=$totcab_pay;
		}else{
			echo "<tr><td colspan=5>Data Penggajian ".$rowcab->kota." ".$thn.$bln." tidak ada</td></tr>";
		}
		
		echo "<tr><td colspan=5>&nbsp;</td></tr>";
	}
	echo "<tr><th colspan=2>GRAND TOTAL </th><th>Rp.&nbsp;".number_format($totall_pend,2,',','.')."</th> <th>Rp.&nbsp;".number_format($totall_pot,2,',','.')."</th><th>Rp.&nbsp;".number_format($totall_pay,2,',','.')."</th></tr>";
?>
</table>
<? if ($display==0){
	$param=$jenis."_".$thn."_".($jenis<>"thr"?$bln."_":"").$penggajian."_1_".$daftar;
?>
<br>
<div class="row" style="text-align:center">
	<div class="col-md-12">	
		<a href="<?=base_url('rptPayroll/rekapPayroll/'.$param)?>" class="btn btn-success">Cetak/Download</a><br>		
	</div>
</div>	
<?}?>

</div>
</div>