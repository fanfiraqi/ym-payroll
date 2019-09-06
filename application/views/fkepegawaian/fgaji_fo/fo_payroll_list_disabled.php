<div class="alert alert-success">
	Form Rekap Gaji : <br>
	&nbsp;&nbsp;1. Penggajian dimulai dari tgl 26 blnTerpilih-1 sampai dengan tgl 25 bln terpilih (bln=sesuai tgl sistem)<br>
	&nbsp;&nbsp;2. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;3. Edit = Sudah ada data yang disimpan, dan ada data baru sebagai update (mis. Jml hadir, jml alpa dari rekap absensi) <br>
	&nbsp;&nbsp;4. View/Disabled = Melewati Bulan aktif<br>
	"Cetak Slip All", slip yang dicetak hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut tercetak<br>
	
</div>
 <div class="row">
	<div class=".col-lg-12" >
		<!-- <div class="panel panel-default"> -->
			<!--<div class="panel-heading">Entri Master Gaji / Tunjangan Staff</div> /.panel-heading -->
				<!-- <div class="panel-body" style="overflow:scroll; height:500px"> -->
					<div class="table-responsive" style="overflow:scroll; height:550px">
						<table class="table table-striped table-bordered table-hover">
                           <thead>
                           <tr >
							<th ROWSPAN=2>NO</th>
							<th ROWSPAN=2>NIK</th>
							<th ROWSPAN=2>NAMA</th>
							<th COLSPAN=4>LEVEL</th>                            
							<th ROWSPAN=2>GAPOK (60%=full)</th>                            
							<th COLSPAN=5>PEROLEHAN</th>
							<th COLSPAN=4>KUNJUNGAN</th>
                            <th COLSPAN=2>TUNJ. ANAK</th>
							<th COLSPAN=2>UBUDIAH</th>
                            <th COLSPAN=2>PINJAMAN</th>
							<th ROWSPAN=2>TA'AWUN</th>							
                            <th ROWSPAN=2>PREMI JHT</th>	
							<th ROWSPAN=2>PREMI JHT KE</th>
							<th ROWSPAN=2>TOTAL GAJI</th>							
							<th ROWSPAN=2>SLIP</th>							
						  </tr>
						   <tr >
							<th >TINGKAT</th>						
							<th >TGL MULAI</th>						
							<th >DURASI</th>							
							<th >BLN KE</th>							
							<TH>TARGET</TH>
							<TH>REALISASI</TH>
							<TH>INSENTIF 50%</TH>
							<TH>PENCAPAIAN</TH>
                            <TH>BONUS 100%/80%</TH>
							<TH>TARGET</TH>
							<TH>REALISASI</TH>
							<TH>PENCAPAIAN (%)</TH>
							<TH>TUNJ TRANSPORT</TH>
                            <TH>DAPAT TUNJ</TH>
                            <TH>JML TUNJ</TH>
							<TH>NILAI UBUDIAH</TH>
                            <TH>TUNJ. UBUDIAH</TH>
                            <TH>JML ANGSURAN</TH>
                            <TH>CICILAN KE</TH>
						  </tr>
						</thead>
						<tbody>
<?	$perolehan=0;
	$insentif_50=0;
	$total_bonus=0;
	$total=0;
	$jml_kunjungan=0;
	$masaKerja="";

$i=1;
//echo "<tr><td colspan=10>$strRes</td></tr>";
if (sizeof($row)==0){		
	echo "<tr align=center><td colspan=20>Belum Ada Data Pegawai FO di Cabang ini</td></tr>";
}else{
	$jmlbln=0;
	//echo var_dump($row)."<br><br>".$strRes."<br>";
	foreach($row as $hasil){
	$umurKerja=$hasil->SELISIH;			
		$interval = date_diff(date_create(), date_create($hasil->TGL_MULAI));
		$masaKerja= $interval->format(" %m Bulan, %d Hari");
		$jmlbln=$interval->format("%m")+1;	
	

	//if ($jmlbln<=$hasil->TERMIN && $hasil->TGL_MULAI<=date("Y-m-d")){
		$periodThnBlnMulai=substr($hasil->TGL_MULAI,0,4).substr($hasil->TGL_MULAI,5,2);
		$periodThnBlnAkhir=$periodThnBlnMulai+($hasil->TERMIN-1);
		
		//tunj anak
		$strTunjAnak="SELECT `NIK` , `ID_HUBKEL` , `ANAK_KE` , `ID_PENDIDIKAN`,ms.KEYWORD, ms.NOMINAL
				FROM `adm_hubkel` adm, mst_tunjangan_anak ms
				WHERE `ID_HUBKEL` =2 and (id_pendidikan between 1 and 5) and nik='".$hasil->NIK."'
				and ms.id=adm.id_pendidikan 
				ORDER BY NIK , anak_ke ASC limit 4";
		$rsTunjAnak=$this->db->query($strTunjAnak)->result();
		
		//pinjaman
		//cek pinjaman, jika di tabel gaji_staff sdh ada tampilkan jika belum ada ambil dari tabel pinjaman
		$strPinj="SELECT a.ID_HEADER, a.CICILAN_KE, a.JML_CICILAN
				FROM pinjaman_header h,  `pinjaman_angsuran` a 
				WHERE h.id=a.id_header and h.nik='".$hasil->NIK."' and jml_bayar=0
				order by cicilan_ke asc		limit 1		";
		$qPinjaman=$this->db->query($strPinj);
		$cicilke=0;
		$jmlcicilan=0;
		$id_header="";
		if ($qPinjaman->num_rows()>=1){
			$rsPinj=$qPinjaman->row();
			$cicilke=$rsPinj->CICILAN_KE;
			$jmlcicilan=$rsPinj->JML_CICILAN;
			$id_header=$rsPinj->ID_HEADER;
		}
		//perolehan
		$strPerolehan="SELECT pf.NIK,(select level from mst_gaji_fo where id=pf.id_level) NAMALEVEL, PEROLEHAN, JML_KUNJUNGAN, pf.BLN_KE, ev.TGL_MULAI, ev.TGL_AKHIR
				FROM perolehan_fo pf left join evaluasi_fo ev
				on pf.nik = ev.nik
				WHERE ev.status_eval=1 and ( concat( thn, bln )='".$thnPerolehan.$digitBlnPerolehan."' AND  pf.nik='".$hasil->NIK."'	)";
		//echo $strPerolehan."<br>";
		if ($rsResult=$this->db->query($strPerolehan)->num_rows()<=0){
			$perolehan=0;
			$insentif_50=0;
			$persenPrlh=0;
			$kunjungan=0;
			$persenKunj=0;
			$blnKe=0;
		}else{
			$rsResult=$this->db->query($strPerolehan)->row();
			$perolehan=$rsResult->PEROLEHAN;
			$insentif_50=$perolehan*0.5;
			$persenPrlh=round(($perolehan/$hasil->TARGET_P)*100,2);
			$kunjungan=$rsResult->JML_KUNJUNGAN;
			$persenKunj=round(($kunjungan/$hasil->TARGET_K)*100,2);
			$blnKe=$rsResult->BLN_KE;
		}
		if ($persenPrlh>=60){
			$gapok=$hasil->GAPOK;
		}else{
			$gapok=($hasil->GAPOK*$persenPrlh)/100;
		}
		if ($kunjungan>=$hasil->TARGET_K){
			$tunjTransport=$hasil->TRANSPORT;
		}else{
			$tunjTransport=round(($kunjungan/$hasil->TARGET_K)*$hasil->TRANSPORT,0);
		}
		
		
		$target_P=$hasil->TARGET_P;
		$target_K=$hasil->TARGET_K;
		//echo "<tr align=center><td colspan=20>$strPerolehan</td></tr>";
		//echo "<tr align=center><td colspan=20>".$hasil->SELISIH."#".$hasil->TGL_AKTIF."</td></tr>";
	?>

	<tr >
	<td><?=$i?></td>
	<td><?=$hasil->NIK?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
	<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
	<td><?=$hasil->LEVEL?></td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	
	<td><?=form_input(array('name'=>'gapok_'.$i,'id'=>'gapok_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>7, 'value'=>$gapok));?></td>

	<td><?=form_input(array('name'=>'target_p_'.$i,'id'=>'target_p_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>7, 'value'=>$target_P));?></td>	
	<td><?=form_input(array('name'=>'real_p_'.$i,'id'=>'real_p_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>7, 'value'=>$perolehan));?></td>
	<td><?=form_input(array('name'=>'insentif50_'.$i,'id'=>'insentif50_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>7, 'value'=>$insentif_50));?></td>
	<td><?=form_input(array('name'=>'persen_capai_p_'.$i,'id'=>'persen_capai_p_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>7, 'value'=>$persenPrlh));?></td>	
<?	//bonus 100-80, blm cek akhir termin
	$persenTarget=($hasil->ID==6?80:100);
	$sket="";
	$sGetSum=""; $soo="";
	if ($blnKe==$hasil->TERMIN){
		//akhir termin
		$sket="akhir termin";
		//perbandingan perolehan & target level jika tercapai, maka cek perolehan bonus 100-80 tiap bulannya (persen capai)
		$sGetSum="SELECT pf.NIK, SUM( PEROLEHAN ) PRLH, SUM( JML_KUNJUNGAN ) KUNJ
				FROM perolehan_fo pf, evaluasi_fo ev
				WHERE pf.nik = ev.nik 
				AND (
				concat( thn, bln )
				BETWEEN '$periodThnBlnMulai'
				AND '$periodThnBlnAkhir' and pf.nik='".$hasil->NIK."'	)";
		$rsSum=$this->db->query($sGetSum)->row();
		if ($rsSum->PRLH>=($target_P*$hasil->TERMIN)){
			//cek brp bln sisa yg belum dpt bonus
			$soo="select count(*) cnt from gaji_fo where persen_capai_p<$persenTarget and nik='".$hasil->NIK."' and concat( thn, bln ) BETWEEN '$periodThnBlnMulai' AND '$periodThnBlnAkhir' ";
			$rssoo=$this->db->query($soo)->row();
			$bonus_100_80=($hasil->BONUS_100_80)*($rssoo->cnt+1);
		}else{
			if ($persenPrlh>=$persenTarget){
				$bonus_100_80=$hasil->BONUS_100_80;
			}else{
				$bonus_100_80=0;
			};
		}
	}else{
		if ($persenPrlh>=$persenTarget){
			$bonus_100_80=$hasil->BONUS_100_80;
		}else{
			$bonus_100_80=0;
		}
	}

?>
	<td><?=form_input(array('name'=>'bonus_100_80_'.$i,'id'=>'bonus_100_80_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>7, 'value'=>$bonus_100_80));?></td>
	
	
	<td><?=form_input(array('name'=>'target_k_'.$i,'id'=>'target_k_'.$i,'class'=>'myform-control','size'=>3,'readonly'=>true, 'value'=>$target_K));?></td>
	<td><?=form_input(array('name'=>'real_k_'.$i,'id'=>'real_k_'.$i,'class'=>'myform-control','size'=>3, 'readonly'=>true, 'value'=>$kunjungan));?></td>
	<td><?=form_input(array('name'=>'persen_capai_k_'.$i,'id'=>'persen_capai_k_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>7, 'value'=>$persenKunj));?></td>
	<td><?=form_input(array('name'=>'tunj_transport_'.$i,'id'=>'tunj_transport_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>10, 'value'=>$tunjTransport));?></td>

	
	<td><?
		$jumTa=0;
		$jumAnak=0;
		if ($umurKerja<3){
			echo "Blm Dpt";
		}else{	
			if ($this->db->query($strTunjAnak)->num_rows()<=0){
				echo "Tngkt Pddkn tdk sesuai";
			}else{
			foreach($rsTunjAnak as $rsta){
				echo $rsta->KEYWORD.":".$rsta->NOMINAL."<br>";
				$jumTa+=$rsta->NOMINAL;
				$jumAnak++;
			}
			}
		}

		?></td>
	<td><?=form_input(array('name'=>'tunjAnak_'.$i,'id'=>'tunjAnak_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>7, 'value'=>$jumTa));?><input type="hidden" name="jml_anak_<?=$i?>" id="jml_anak_<?=$i?>" value="<?=$jumAnak?>"></td>
	
	<?	//tunj fungsional = daily report * nominal t.fungsional master
		$ubudiah=0;
		$strDR="select JML_HARI from ubudiah_fo where concat( thn, bln )='".$thnPerolehan.$digitBlnPerolehan."'  and nik='".$hasil->NIK."'";
			if ($this->db->query($strDR)->num_rows()>0){
				$rskerja=$this->db->query($strDR)->row();
				$ubudiah=$rskerja->JML_HARI;
			}
		//awalnya tunjangan fungsional -> judul berubah menjadi T. UBUDIAH
		$tunjUbudiah=0;
		if ($rsFungsional=$this->db->query("select UBUDIAH from mst_gaji_fo where ID =".$hasil->ID)->row()){
			$tunjUbudiah=$ubudiah*$rsFungsional->UBUDIAH;
		}
			
	?>
	<td><?=form_input(array('name'=>'ubudiah_'.$i,'id'=>'ubudiah_'.$i,'class'=>'myform-control','size'=>5,'readonly'=>true, 'value'=>$ubudiah));?></td>
		<td><?=form_input(array('name'=>'tunjUbudiah_'.$i,'id'=>'tunjUbudiah_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$tunjUbudiah));?></td>
	<td><?=form_input(array('name'=>'pinjaman_'.$i,'id'=>'pinjaman_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>10, 'value'=>$jmlcicilan));?><input type="hidden" name="pinj_idheader_<?=$i?>" id="pinj_idheader_<?=$i?>" value="<?=$id_header?>"></td>
	<?  
		//cek pegawai ikut/tidak jht
			$rsCekJht=$this->db->query("select PROGRAM_JHT from pegawai where nik='".$hasil->NIK."'")->row();
			$altTeks="Masa Kerja ".$umurKerja." Bulan. Tidak Ikut Program JHT";
			if ($rsCekJht->PROGRAM_JHT==1){				
				
				$altTeks="Masa Kerja ".$umurKerja." Bulan. Ikut Program JHT";
			}		
	?>
	<td><?=form_input(array('name'=>'pinj_cicil_ke_'.$i,'id'=>'pinj_cicil_ke_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>7, 'value'=>$cicilke));?></td>

	<td title="taawun tidak mengurangi gaji (tunjangan dan sedekah)"><?=form_input(array('name'=>'taawun_'.$i,'id'=>'taawun_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$hasil->SEDEKAH_TAAWUN));?></td>

	<td title="<?=$altTeks?>"><?=form_input(array('name'=>'premi_jht_'.$i,'id'=>'premi_jht_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$hasil->PREMI_JHT));?></td>
	<td><?=form_input(array('name'=>'jht_ke_'.$i,'id'=>'jht_ke_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$hasil->JHT_KE));?></td>
	<td><?=form_input(array('name'=>'total_'.$i,'id'=>'total_'.$i,'class'=>'myform-control','readonly'=>true,'size'=>12, 'value'=>$hasil->TOTAL_GAJI));?></td>
	<td>
	<? if ($cek->CEK<=0){
		echo "&nbsp;-";
	}else {
		$param=$thn."_".$digitBln."_".$hasil->NIK;
			//CEK FILE_SLIP
			$strFile="select count(*) CKFILE from file_slip where thn='$thn' and bln='$digitBln' and nik='".$hasil->NIK."'";
			$rsFile=$this->db->query($strFile)->row();
			
			if ($rsFile->CKFILE >=1){	//ada -> tombol download
				$rsPath=$this->db->query("select * from file_slip where thn='$thn' and bln='$digitBln' and nik='".$hasil->NIK."'")->row();
		?>	&nbsp;-&nbsp;<br>
			<a href="javascript:void(0)" onclick="window.open('<?=base_url($rsPath->PATH."/".$rsPath->NAMA_FILE)?>','_blank')"><i class="fa fa-print" title="Print/Download File">Download/Print</i></a>			
		<?
			}else{	
			
		?>
		<a href="javascript:void(0)" data-url="<?=base_url('penggajian_fo/cetak_slip/')?>" data-id="<?=$param?>" onclick="singleSlip(this)"><i class="fa fa-edit" title="Generate Slip to .pdf File">Generate pdf</i></a><br>&nbsp;-&nbsp;
		<?}
			
	}?>
	</td>
	</tr>

<?
		
		$i++; 
	}
}
	
	?>
									</tbody>
                                </table>
<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">										
										<input type="hidden" name="bln" id="bln" value="<?=$digitBln?>">									
										<input type="hidden" name="penanda_jht_ke" id="penanda_jht_ke" value="disabled">	
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">		
										<input type="hidden" name="id_cabang" id="id_cabang" value="<?=$id_cabang?>">	
<? $btsubmit = array(
												'type'=>'button',
												'name'=>'btCsvBank',
												'id'=>'btCsvBank',
												'value'=>'Export CSV Bank',
												'class'=>'btn btn-primary'
											);
										echo "&nbsp;".form_input($btsubmit);
										$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('penggajian_fo/index')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
?>
   &nbsp;<button id="doprogress" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Cetak Slip untuk data terakhir yang sudah tersimpan saja">Generate pdf Slip All </button><br>			
										. 
										<div class="row">
											<div class="col-md-6">
												<div class="progress no-display">
													<div class="progress-bar" id="progressBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
													0%
													</div>
												</div>
											</div>
										</div> 

							</div>   <!-- /.table-responsive -->

		
                </div>
</div>
<hr />

<script type="text/javascript">
 $(document).ready(function() {  
	

 });

$('#btCsvBank').click(function() {
		var bln=$('#bln').val();
		var thn=$('#thn').val();
		var cabang=$('#id_cabang').val();
		var pilih=confirm('Export File ke CSV?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('penggajian_fo/exportCsv'); ?>",
				dataType: 'json',
				type: 'POST',
				data: "bln="+bln+"&thn="+thn+"&id_cabang="+cabang,				
				success: function(data,  textStatus, jqXHR){					
					//alert("File csv sudah tersimpan");
					window.open('<?=base_url("'+data.isi+'")?>','_blank');
				} 
			});			
		}
		
	});

$('#myform').submit(function(event) {
	$(this).saveForm('<?php echo base_url('penggajian_fo/set_saveList');?>','<?php echo base_url('penggajian_fo');?>');
	event.preventDefault();
});


$('#doprogress').click(function(){
	cetakprogress();
	});

function cetakprogress(cnt,step){
	if(typeof cnt === 'undefined'){
		var data = {cabang:<?=$id_cabang?>,divisi:14, thn: <?="'".$thn."'"?>, bln: <?="'".$digitBln."'"?>};
	} else {
		var data = {cabang:<?=$id_cabang?>,divisi:14, thn: <?="'".$thn."'"?>, bln: <?="'".$digitBln."'"?> ,cnt:cnt,step:step};
	}
	$.ajax({
		url: "<?php echo base_url('penggajian_fo/slipLoop'); ?>",
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
