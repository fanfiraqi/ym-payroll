 <div class="row">
	<div class="col-xs-12">
<? $viewKop=$this->commonlib->tableKop('KEU-PAYROLL',$titlekop, '00', '__','__');
	echo $viewKop;
	//echo $strlist;
	echo '<table class="bordered" >';
	echo "<tr><th colspan=3>STAFF DALAM</th><th colspan=2>KACAB </th><th colspan=3>ZISCO</th><th colspan=2>Non Sistem</th></tr>";
	echo "<thead style='display:block;'><tr><th style='width: 200px;'>Nama</th><th style='width: 200px;'>Kantor</th><th style='width: 200px;'>Jumlah Pendapatan</th><th style='width: 200px;'>Jumlah Potongan</th><th style='width: 200px;'>Penggajian</th></tr></thead>";
	$strCab="";
	if ($akses=="pusat"){
		$strCab="select * from mst_cabang where is_active=1 and kota not like '%REGIONAL%' order by kota";
	}elseif($akses=="RO"){
		$strCab="select * from mst_cabang where is_active=1 and id_cabang_parent=".$this->session->userdata('auth')->ID_CABANG."  order by kota";
	}else{
		$strCab="select * from mst_cabang where is_active=1 and id_cabang=".$this->session->userdata('auth')->ID_CABANG;
	}
	
	$rscab=$this->gate_db->query($strCab)->result();
	$i=1;
	$totpayroll_dalam=0;$totpayroll_kacab=0;$totpayroll_zistrans=0;$totpayroll_zisbonus=0;$totpayroll_nonsistem=0;
	echo "<tbody style='overflow:auto; height: 260px; table-layout:fixed; display:block;'>";
	foreach ($rscab as $rowcab){
		$strDalam="SELECT  IFNULL(SUM(total),0) payroll FROM gaji_staff where thn='".$thn."' and bln='".$bln."'  and id_cabang=".$rowcab->id_cabang;
		$strKacab="SELECT  IFNULL(SUM(total),0) payroll FROM gaji_kacab_bonus where thn='".$thn."' and bln='".$bln."'  and id_cabang=".$rowcab->id_cabang;
		$strzistrans="SELECT  IFNULL(SUM(total),0) payroll FROM gaji_zisco_transport where thn='".$thn."' and bln='".$bln."'  and id_cabang=".$rowcab->id_cabang;
		$strzisbonus="SELECT  IFNULL(SUM(total),0) payroll FROM gaji_zisco_bonus where thn='".$thn."' and bln='".$bln."'  and id_cabang=".$rowcab->id_cabang;
		$strnonsistem="SELECT  IFNULL(SUM(total),0) payroll FROM gaji_non_sistem where thn='".$thn."' and bln='".$bln."'  and id_cabang=".$rowcab->id_cabang;
		
		$rsdalam=$this->db->query($strDalam)->row();		
		$rskacab=$this->db->query($strKacab)->row();		
		$rszistrans=$this->db->query($strzistrans)->row();		
		$rszisbonus=$this->db->query($strzisbonus)->row();		
		$rsnonsistem=$this->db->query($strnonsistem)->row();		
		$payroll_dalam=0;$payroll_kacab=0;$payroll_zistrans=0;$payroll_zisbonus=0;$payroll_nonsistem=0;
		
		if (sizeof($rsdalam)>0){ $payroll_dalam=$rsdalam->payroll; }
		if (sizeof($rskacab)>0){ $payroll_kacab=$rskacab->payroll; }
		if (sizeof($rszistrans)>0){ $payroll_zistrans=$rszistrans->payroll; }
		if (sizeof($rszisbonus)>0){ $payroll_zisbonus=$rszisbonus->payroll; }
		if (sizeof($rsnonsistem)>0){ $payroll_nonsistem=$rsnonsistem->payroll; }
		$totpayroll_dalam+=$payroll_dalam;
		$totpayroll_kacab+=$payroll_kacab;
		$totpayroll_zistrans+=$payroll_zistrans;
		$totpayroll_zisbonus+=$payroll_zisbonus;
		$totpayroll_nonsistem+=$payroll_nonsistem;
		echo "<tr>";
		echo "<td>".$i."</td>";
		echo "<td>".$rowcab->kota."</td>";
		echo "<td>Rp.&nbsp;".number_format($payroll_dalam,2,',','.')."</td>";
		echo "<td>".$rowcab->kota."</td>";
		echo "<td>Rp.&nbsp;".number_format($payroll_kacab,2,',','.')."</td>";
		echo "<td>".$rowcab->kota."</td>";
		echo "<td>Rp.&nbsp;".number_format($payroll_zistrans,2,',','.')."</td>";
		echo "<td>Rp.&nbsp;".number_format($payroll_zisbonus,2,',','.')."</td>";
		echo "<td>".$rowcab->kota."</td>";
		echo "<td>Rp.&nbsp;".number_format($payroll_nonsistem,2,',','.')."</td>";
		echo "</tr>";
		$i++;
	}

echo "<tr><th colspan=2>Total</th><th>Rp.&nbsp;".number_format($totpayroll_dalam,2,',','.')." </th><th>&nbsp;</th><th>Rp.&nbsp;".number_format($totpayroll_kacab,2,',','.')." </th><th>&nbsp;</th><th>Rp.&nbsp;".number_format($totpayroll_zistrans,2,',','.')."</th><th>Rp.&nbsp;".number_format($totpayroll_zisbonus,2,',','.')."</th><th>&nbsp;</th><th>Rp.&nbsp;".number_format($totpayroll_nonsistem,2,',','.')."</th></tr>";
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