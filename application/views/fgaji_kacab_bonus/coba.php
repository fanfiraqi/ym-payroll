<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover"  style="max-height:650px;overflow:scroll;" id="myTable">
                           <thead>
                           <tr >
							<th  rowspan=3 >NO</th>
							<th  rowspan=3>NIK</th>
							<th  rowspan=3>NAMA</th>
							<th  rowspan=3>KANTOR</th>
							<th  rowspan=3>REGIONAL</th>
							<th  rowspan=3>JABATAN</th>
							<th  rowspan=3>DONASI RUTIN MUNDUR < 6 BLN</th>							
							<th colspan=4>PENGAMBILAN RUTIN</th>
							<th colspan=5>REALISASI PENGEMBANGAN INSIDENTIL NON TERIKAT</th>
							<th colspan=7>PENGEMBANGAN INSIDENTIL  TERIKAT</th>
							<th rowspan=3>Presentase Pengambilan</th>
<?	$strMaster="select * from mst_komp_gaji  where isactive=1 and  is_zisco='on' and jns_zisco='bonus' order by ID";
	$master=$this->db->query($strMaster)->result();
	$p=1;
	$t=1;
	$thPend=""; 
	$thPot=""; $nominal=0;
	foreach($master as $rowmaster){	
		if ($rowmaster->FLAG=='+'){
			$thPend.="<th rowspan=2>".$rowmaster->NAMA."</th>";
			$p++;
		}else{
			$thPot.="<th rowspan=2>".$rowmaster->NAMA."</th>";
			$t++;
		}
		//echo "<th>".$rowmaster->NAMA."(".$rowmaster->FLAG.")</th>";
		
	}
	$colspanAll=$p+8+$t;
?>							
							<th colspan="<?php echo $p-1?>">PENDAPATAN</th>
							<th rowspan=3>TOTAL PENDAPATAN</th>
							<th colspan="<?php echo $t-1?>">POTONGAN</th>
							<th rowspan=3>TOTAL POTONGAN</th>
							<th rowspan=3>TOTAL TERIMA</th>
							<th rowspan=3>SLIP</th>
						  </tr>

						  <tr>	
						  <th colspan=2 >Infaq</th>
							<th colspan=2 >Zakat</th>

							<th colspan=2 >Infaq</th>
							<th colspan=2 >Zakat Mal</th>
							<th rowspan=2 >Zakat Fithrah</th>
							
							<th colspan=3 >wakaf</th>
							<th colspan=4 >dana terikat non wakaf</th>
						  <?php echo $thPend.$thPot?>
						  </tr>
						  <tr>
							<th>Target</th>
							<th>Realisasi</th>
							<th>Target</th>
							<th>Realisasi</th>
							<th>Rutin</th>
							<th>Insidental</th>
							<th>Rutin</th>
							<th>Insidental</th>

							<th>ICMBS</th>
							<th>STAINIM</th>
							<th>MASJID</th>
							<th>Al&nbsp;quran</th>
							<th>Qurban</th>
							<th>Bencana</th>
							<th>fidyah</th>

							</tr>
                         </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div> <!-- /.table-responsive -->
