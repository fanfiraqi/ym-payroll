 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success">
	Form Rekap Gaji : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Edit = Sudah ada data yang disimpan, dan ada data baru sebagai update (mis. Jml hadir, jml alpa dari rekap absensi) <br>
	&nbsp;&nbsp;3. View/Disabled = Melewati Bulan aktif<br>
	"Cetak Slip All", slip yang dicetak hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut tercetak<br>
	
	</div>
		<div class="panel panel-default">
			<div class="panel-heading">Daftar Gaji & Tunjangan Staff</div><!-- /.panel-heading -->
				<div class="panel-body" style="overflow:scroll;height:550px;">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
                           <thead>
                           <tr >
							<th rowspan="2">NO</th>
							<th rowspan="2">NIK</th>
							<th rowspan="2">NAMA</th>
							<th rowspan="2">JABATAN</th>
							<th colspan="4" align="center">1</th>
							<th colspan="3" align="center">2</th>
							<th colspan="2" align="center">3</th>
							<th align="center" > 4</th>
							<th align="center" colspan="2"> 5</th>
							<th colspan="7" align="center">6 (POTONGAN)</th>
							<th rowspan="2">TOTAL&nbsp;GAJI</th>
							<th rowspan="2">SLIP</th>
						  </tr>
						  <tr>
							<th>MASA&nbsp;KERJA</th>
							<th>GAPOK</th>
							<th>T.&nbsp;JABATAN</th>
							<th>T.&nbsp;MASA&nbsp;KERJA</th>							
							<th>JML&nbsp;HADIR</th>
							<th>UANG&nbsp;MAKAN</th>
							<th>T.&nbsp;TRANSPORT</th>
							<th>ANAK DAPAT TUNJANGAN</th>
							<th>TOTAL T.&nbsp;ANAK</th>							
							<th>T.&nbsp;ISO</th>
							<th>NILAI UBUDIAH</th>
							<th>T.&nbsp;UBUDIAH</th>
							<th>JML&nbsp;ALPA</th>
							<th>POT.&nbsp;GAJI</th>
							<th>JML&nbsp;ANGSURAN</th>
							<th>ANGSURAN&nbsp;KE</th>
							<th>TA'AWUN</th>
							<th>POT. PREMI JHT</th>
							<th>POT. PREMI JHT KE</th>
						  </tr>
						   
						  </tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr><td colspan=20>$strRes</td></tr>";
$i=1;
	if (sizeof($row)==0){		
		
		echo "<tr align=center><td colspan=20>Data Belum Ada</td></tr>";
	}else{
	
	foreach($row as $hasil){ 
		
		//ambil dari gaji_staff
		//$rsGaji=$this->db->query("select * from gaji_staff where nik='".$hasil->NIK."' and bln='$digitBln' and thn='$thn'")->row();
		$masakerja=$hasil->MASA_KERJA;
			//hitung masa kerja
			if ($masakerja<12){				
				$strmasakerja=$masakerja." Bln";	
				$thnMasakerja=0;
			}else{
				$strmasakerja=($masakerja/12)." Thn, ".($masakerja%12)." Bln";
				$thnMasakerja=floor($masakerja/12);
			}
		$gapok=$hasil->GAPOK;
		$jml_hadir=$hasil->JML_HADIR;
		$uangMakan=$hasil->T_MAKAN;
		$transport=$hasil->T_TRANSPORT;
		$tunjJabatan=$hasil->T_JABATAN;
		$tunMasaKerja=$hasil->T_MASAKERJA;
		$jumTa=$hasil->T_ANAK;
		$jumAnak=$hasil->JML_ANAK;
		$tunjFungsional=$hasil->T_FUNGSIONAL;
		$jml_alpa=$hasil->JML_ALPA;
		$potGapok=$hasil->JML_POT_GAPOK;
		$cicilke=$hasil->ANGSURAN_KE;
		$jmlcicilan=$hasil->JML_ANGSURAN;
		$taawun=$hasil->SEDEKAH_TAAWUN;
		$premi_jht=$hasil->PREMI_JHT;
		$jht_ke=$hasil->JHT_KE;
		$total=$hasil->TOTAL;
		$id_header="";
		?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td><?=form_input(array('name'=>'strMasaKerja_'.$i,'id'=>'strMasaKerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$strmasakerja));?><input type="hidden" name="masakerja_<?=$i?>" id="masakerja_<?=$i?>" value="<?=$masakerja?>"></td>
		<td style="background-color:<?=($masakerja<3?"#fecba5":"#c4ecb3")?>"><?=form_input(array('name'=>'gapok_'.$i,'id'=>'gapok_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$gapok));?></td>
		<td><?=form_input(array('name'=>'tunjJabatan_'.$i,'id'=>'tunjJabatan_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$tunjJabatan));?></td>
		<td><?=form_input(array('name'=>'tunjMasakerja_'.$i,'id'=>'tunjMasakerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$tunMasaKerja));?></td>
		
		
		<td><?=form_input(array('name'=>'jmlhadir_'.$i,'id'=>'jmlhadir_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$jml_hadir));?></td>
		<td><?=form_input(array('name'=>'uangMakan_'.$i,'id'=>'uangMakan_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$uangMakan));?></td>
		<td><?=form_input(array('name'=>'transport_'.$i,'id'=>'transport_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$transport));?></td>
		<td><?
		//get tunjangan anak value
		$strTunjAnak="SELECT `NIK` , `ID_HUBKEL` , `ANAK_KE` , `ID_PENDIDIKAN`,ms.KEYWORD, ms.NOMINAL
				FROM `adm_hubkel` adm, mst_tunjangan_anak ms
				WHERE `ID_HUBKEL` =2 and (id_pendidikan between 1 and 5) and nik='".$hasil->NIK."'
				and ms.id=adm.id_pendidikan 
				ORDER BY NIK , anak_ke ASC limit 4";
		$rsTunjAnak=$this->db->query($strTunjAnak)->result();
		if ($masakerja<3){
			echo "Blm Dpt";
		}else{			
			foreach($rsTunjAnak as $rsta){
				echo $rsta->KEYWORD.":".$rsta->NOMINAL."<br>";				
			}
		}
		//$param=$thn."_".$digitBln."_".$hasil->NIK;
		?></td>
		<td><?=form_input(array('name'=>'tunjAnak_'.$i,'id'=>'tunjAnak_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>($masakerja<3?0:$jumTa)));?><input type="hidden" name="jml_anak_<?=$i?>" id="jml_anak_<?=$i?>" value="<?=$jumAnak?>"></td>
		<td><?=form_input(array('name'=>'tunjFungsional_'.$i,'id'=>'tunjFungsional_'.$i,'class'=>'myform-control','readonly'=>true, 'size'=>10, 'value'=>$tunjFungsional,'onchange'=>'updateTotal(this, '.$i.')'));?></td>
		
		<td><?=form_input(array('name'=>'ubudiah_'.$i,'id'=>'ubudiah_'.$i,'class'=>'myform-control','size'=>5,'readonly'=>true, 'value'=>$hasil->NILAI_UBUDIAH));?></td>
		<td><?=form_input(array('name'=>'tunjUbudiah_'.$i,'id'=>'tunjUbudiah_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$hasil->TUNJ_UBUDIAH));?></td>
		
		<td><?=form_input(array('name'=>'jmlAlpa_'.$i,'id'=>'jmlAlpa_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$jml_alpa));?></td>
		<td><?=form_input(array('name'=>'potGapok_'.$i,'id'=>'potGapok_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$potGapok));?></td>
		<td><?=form_input(array('name'=>'jmlcicilan_'.$i,'id'=>'jmlcicilan_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$jmlcicilan));?></td>
		<td><?=form_input(array('name'=>'cicilke_'.$i,'id'=>'cicilke_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$cicilke));?><input type="hidden" name="pinj_idheader_<?=$i?>" id="pinj_idheader_<?=$i?>" value="<?=$id_header?>"></td>
		<td  title="taawun tidak mengurangi gaji (tunjangan dan sedekah)"><?=form_input(array('name'=>'taawun_'.$i,'id'=>'taawun_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$taawun));?></td>
		<td><?=form_input(array('name'=>'premi_jht_'.$i,'id'=>'premi_jht_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$premi_jht));?></td>
		<td><?=form_input(array('name'=>'jht_ke_'.$i,'id'=>'jht_ke_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$jht_ke));?></td>
		
		<td><?=form_input(array('name'=>'totalGaji_'.$i,'id'=>'totalGaji_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$total));?><input type="hidden" name="hidTotAwal_<?=$i?>" id="hidTotAwal_<?=$i?>" value="<?=$total?>"></td>
		
		<!-- <td><a href="<?=base_url('payroll_staff/cetak_slip/'.$param)?>"><i class="fa fa-print" title="Lihat Detail">&nbsp;Cetak</i></a></td> -->
		<!-- GENERATE/DOWNLOAD -->
		<?	$param=$thn."_".$digitBln."_".$hasil->NIK;
			//CEK FILE_SLIP
			$strFile="select count(*) CKFILE from file_slip where thn='$thn' and bln='$digitBln' and nik='".$hasil->NIK."'";
			$rsFile=$this->db->query($strFile)->row();
			echo "<td>";
			if ($rsFile->CKFILE >=1){	//ada -> tombol download
				$rsPath=$this->db->query("select * from file_slip where thn='$thn' and bln='$digitBln' and nik='".$hasil->NIK."'")->row();
		?>	&nbsp;-&nbsp;<br>
			<a href="javascript:void(0)" onclick="window.open('<?=base_url($rsPath->PATH."/".$rsPath->NAMA_FILE)?>','_blank')"><i class="fa fa-print" title="Print/Download File">Download/Print</i></a>
			<!-- <a href="payroll_staff/print_down/<?=$param?>" ><i class="fa fa-print" title="Print/Download File">Download/Print</i></a>
			<a href="javascript:void(0)" data-url="<?=base_url('payroll_staff/print_down/')?>" data-id="<?=$param?>" onclick="print_down(this)"><i class="fa fa-print" title="Print/Download File">Download/Print</i></a> --> 
		<?
			}else{	
			
		?>
		<a href="javascript:void(0)" data-url="<?=base_url('payroll_staff/cetak_slip/')?>" data-id="<?=$param?>" onclick="singleSlip(this)"><i class="fa fa-edit" title="Generate Slip to .pdf File">Generate pdf</i></a><br>&nbsp;-&nbsp;
		<?}
			echo "</td>";
			?>
	  </tr>		
		
	<? 
		
		$i++; 
	}
}	
	?>

                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
							

                        </div>
                    </div>
                    <!-- /.panel -->
					
                        <!-- /.panel-body -->
						<? 	if (sizeof($row)>0){ ?>
								<div class="row">
									<div class="col-md-6">
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">									<input type="hidden" name="penanda_jht_ke" id="penanda_jht_ke" value="disabled">	
										<input type="hidden" name="bln" id="bln" value="<?=$digitBln?>">										
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">	
										<input type="hidden" name="id_cabang" id="id_cabang" value="<?=$id_cabang?>">	
										<input type="hidden" name="id_divisi" id="id_divisi" value="<?=$id_divisi?>">								<input type="button" id="doprogress" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Cetak Slip untuk data terakhir yang sudah tersimpan saja" onclick="cetakprogress()" value="generate Slip All ">	
										<?php 
											$btCSV = array(
												'type'=>'button',
												'name'=>'btCsvBank',
												'id'=>'btCsvBank',
												'value'=>'Export CSV Bank',
												'class'=>'btn btn-primary'
											);
										echo "&nbsp;".form_input($btCSV);
										$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('payroll_staff/payroll_filter')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
							?>
					&nbsp;<br>			
										. 
										<div class="row">
											<div class="col-md-10">
												<div class="progress no-display">
													<div class="progress-bar" id="progressBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
													0%
													</div>
												</div>
											</div>
										</div> 



									</div>
								</div>
						<?}?>
                </div>
</div>

<hr />
<script type="text/javascript">

$('#btCsvBank').click(function() {
		var bln=$('#bln').val();
		var thn=$('#thn').val();
		var cabang=$('#id_cabang').val();
		var divisi=$('#id_divisi').val();
		var pilih=confirm('Export File ke CSV?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('payroll_staff/exportCsv'); ?>",
				dataType: 'json',
				type: 'POST',
				data: "bln="+bln+"&thn="+thn+"&id_cabang="+cabang+"&id_divisi="+divisi,				
				success: function(data,  textStatus, jqXHR){					
					//alert("File csv sudah tersimpan");
					var sF=data.isi;
					//alert(sF);
					window.open('<?=base_url("'+data.isi+'")?>','_blank');
					
				} 
			});			
		}
		
	});

$('#doprogress').click(function(){
	cetakprogress();
	});

function cetakprogress(cnt,step){
	if(typeof cnt === 'undefined'){
		var data = {cabang:<?=$id_cabang?>,divisi:<?=$id_divisi?>, thn: <?="'".$thn."'"?>, bln: <?="'".$digitBln."'"?>};
	} else {
		var data = {cabang:<?=$id_cabang?>,divisi:<?=$id_divisi?>, thn: <?="'".$thn."'"?>, bln: <?="'".$digitBln."'"?> ,cnt:cnt,step:step};
	}
	$.ajax({
		url: "<?php echo base_url('payroll_staff/slipLoop'); ?>",
		dataType: 'json',
		type: 'GET',
		data: data,
		success: function(respon){
			if (respon.status==1){
				$('.progress').show();
				if(respon.jumlah){
					nextcnt = respon.jumlah;
				} else {
					nextcnt = cnt;
				}
				
				if(typeof respon.nextstep === 'undefined'){
					nextstep = 1;
				} else {
					nextstep = respon.nextstep;
				}
				
				if(typeof respon.percent === 'undefined'){
					percent = 0;
				} else {
					percent = respon.percent;
				}
				
				
				if (respon.complete != 1){
					$('#progressBar').css('width',percent+'%').html(percent+'%');
					cetakprogress(nextcnt,nextstep);
				}else{
					bootbox.alert("Progress Done.");
					window.location.reload();
					$('.progress').hide();
				}
				/*var jum = respon.jumlah;
				$('.progress').show();
				for(var i=1;i<=jum;i++){
					
				}*/
			} else {
				$('.progress').hide();
			}
		}
	});
}

</script>
