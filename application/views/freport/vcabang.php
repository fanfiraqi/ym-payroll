 <div class="row">
	<div class="col-xs-12">
<? $viewKop=$this->commonlib->tableKop('KEU-PAYROLL',$titlekop, '00', '__','__');
	echo $viewKop;
	//echo $strlist;
	echo '<table class="bordered" >';
	echo "<thead style='display:block;'><tr><th style='width: 200px;'>Nama</th><th style='width: 200px;'>Kantor</th><th style='width: 200px;'>Jumlah Pendapatan</th><th style='width: 200px;'>Jumlah Potongan</th><th style='width: 200px;'>Penggajian</th></tr></thead>";
	$strCab="";
	if ($akses=="pusat"){
		$strCab="select * from mst_cabang where is_active=1 and kota not like '%REGIONAL%' order by kota";
	}elseif($akses=="RO"){
		$strCab="select * from mst_cabang where is_active=1 and id_cabang_parent=".$this->session->userdata('auth')->ID_CABANG."  order by kota";
	}else{
		$strCab="select * from mst_cabang where is_active=1 and id_cabang=".$this->session->userdata('auth')->ID_CABANG;
	}
	echo "<tbody style='overflow:auto; height: 260px; table-layout:fixed; display:block;'>";
	$rscab=$this->gate_db->query($strCab)->result();
	foreach ($rscab as $rowcab){
		$strPay=$strlist." and id_cabang=".$rowcab->id_cabang;
		$rspay=$this->db->query($strPay)->row();
		$pend=0; $pot=0; $payroll=0;
		if (sizeof($rspay)>0){
			$pend=$rspay->pendapatan;
			$pot=$rspay->potongan;
			$payroll=$rspay->payroll;

		}
		echo "<tr>";
		echo "<td>".$rowcab->kota."</td>";
		echo "<td>Rp.&nbsp;".number_format($pend,2,',','.')."</td>";
		echo "<td>Rp.&nbsp;".number_format($pot,2,',','.')."</td>";
		echo "<td>Rp.&nbsp;".number_format($payroll,2,',','.')."</td>";
		echo "</tr>";
	}
	echo "</tbody>";
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