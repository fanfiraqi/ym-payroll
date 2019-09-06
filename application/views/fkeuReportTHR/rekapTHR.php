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
	
	$subtotal=0;
	echo "<tr><td align=center><h3>Cabang : ".$cabang->KOTA."</h3></td></tr>";
	?>
	<tr><td>
	<table class='bordered' >
	<thead>	
	<tr><th >KELOMPOK</th><th>NOMINAL THR</th></tr>
	</thead>
	<tbody>	
	<?	$thrStaff=$this->report_model->getStaff_thr($cabang->ID_CAB, $thn);
		if($thrStaff->TOT>0){ 
		echo "<tr><td >STAFF</td><TD>Rp.&nbsp;".number_format($thrStaff->TOT,2,',','.')."</TD></TR>";
		}
		
		$thrFR=$this->report_model->getFR_thr($cabang->ID_CAB, $thn);
		if ($thrFR->TOT>0){
		echo "<tr><td >FR</td><TD>Rp.&nbsp;".number_format($thrFR->TOT,2,',','.')."</TD></TR>";
		}

		$thrFO=$this->report_model->getFO_thr($cabang->ID_CAB, $thn);
		if ($thrFO->TOT>0){
		echo "<tr><td >FO</td><TD>Rp.&nbsp;".number_format($thrFO->TOT,2,',','.')."</TD></TR>";
		}
		$subtotal=$thrStaff->TOT+$thrFR->TOT+$thrFO->TOT;

		$rsDiv =$this->db->query("SELECT DISTINCT j.ID_DIV,j.NAMA_DIV FROM mst_divisi j LEFT JOIN mst_struktur s ON j.id_div=s.id_div WHERE s.id_cab=".$cabang->ID_CAB." AND j.id_div NOT IN (SELECT id_div FROM mst_divisi WHERE (id_div_parent IN (1,2)) OR (id_div IN (1,2))) AND j.id_div<>1")->result();
		
		
		foreach ($rsDiv as $rsUsaha){
			$thrUsaha=$this->report_model->getUsaha_thr($cabang->ID_CAB, $rsUsaha->ID_DIV, $thn);
			if ($thrUsaha->TOT>0){
			echo "<tr><td >".$rsUsaha->NAMA_DIV."</td><TD>Rp.&nbsp;".number_format($thrUsaha->TOT,2,',','.')."</TD></TR>";
			}
			$subtotal+=$thrUsaha->TOT;
		}
		
	
?>
	<tr><th >TOTAL</th><th>Rp. <?php echo number_format($subtotal,2,',','.')?></th></tr>

</tbody></table>
</td></tr>

<?
	$grandTotal+=$subtotal;
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
