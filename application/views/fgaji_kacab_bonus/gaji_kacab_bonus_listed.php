<?php echo form_open('gaji_kacab_bonus/save_gaji_zisco_transport',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	Informasi : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Open = Sudah ada data yang disimpan dan masih bisa diedit. <br>
	&nbsp;&nbsp;3. Closed = Melewati Periode aktif<br>
	"Slip-Email All" yang dikirim hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut terkirim<br>
	
	</div>
	
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover"  style="max-height:650px;overflow:scroll;" id="myTable">
                            <thead>
                          <tr >
							<th  rowspan=2 >NO</th>
							<th  rowspan=2>NIK</th>
							<th  rowspan=2>NAMA</th>
							<th  rowspan=2>KANTOR</th>
							<th  rowspan=2>REK BSM</th>							
							<th  rowspan=2>JABATAN</th>
							<th rowspan=2>TGL MASUK</th>						
							<th rowspan=2>MASA KERJA</th>						
							<th rowspan=2>TARGET PENGAMBILAN</th>
							<th rowspan=2>REALISASI PENGAMBILAN</th>
							<th rowspan=2>TOTAL DONASI CABANG YG MASUK</th>							
							<th colspan=3>REALISASI PENGEMBANGAN (PRIBADI)</th>
							<th rowspan=2>PRESENTASE PENGAMBILAN</th>
							<th colspan=3>BONUS</th>
							<th rowspan=2>PENYESUAIAN</th>
							<th rowspan=2>TOTAL PENDAPATAN</th>
							<th colspan=3>POTONGAN</th>
							<th rowspan=2>TOTAL POTONGAN</th>
							<th rowspan=2>TOTAL TERIMA</th>
							<th rowspan=2>SLIP</th>
						  </tr>

						
						  <tr>
							<th>INSIDENTIL</th>
							<th>ZIS RUTIN</th>
							<th>WAKAF</th>
							<th>JABATAN</th>
							<th>KACAB</th>
							<th>PRESTASI</th>
							<th>DANSOS (2%)</th>
							<th>ZAKAT (2,5%)</th>
							<th>LAIN2</th>
							</tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan='30'>Data Belum Ada</td></tr>";
	}else{
		$i=1;	//as row
		$blnIdk=$this->arrIntBln;
		//cek var bln tahun rekap absensi value DARI BULAN SEBLMNYA
		if ($bln==1){
			$bln_pre=12;
			$thn_pre=$thn-1;
		}else{
			$bln_pre=$blnIdk[$bln-1];
			$thn_pre=$thn;
		}

		foreach($row as $hasil){
			$masakerja=$hasil->MASA_KERJA_BLN;		//dlm bulan
			//hitung masa kerja
			if ($masakerja<12){				
				$strmasakerja=number_format($hasil->MASA_KERJA_BLN,0,',','')." Bln";	
				$thnMasakerja=0;
			}else{
				$strmasakerja=floor($hasil->MASA_KERJA_BLN/12)." Thn, ".($hasil->MASA_KERJA_BLN%12)." Bln";
				$thnMasakerja=floor($hasil->MASA_KERJA_BLN/12);
			}

		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
		$rscab=$this->gate_db->query("select * from mst_cabang where id_cabang=".$hasil->ID_CABANG )->row();
		
		$persen_ambil=$hasil->PERSEN_AMBIL;
		$str_persen_ambil=$persen_ambil." %" ;

		$totPendapatan=$hasil->TUNJAB + $hasil->BONUS_KACAB + $hasil->TUNJ_PRESTASI + $hasil->PENYESUAIAN;		//-penyesuaian
		$totPotongan=$hasil->DANSOS + $hasil->ZAKAT + $hasil->LAIN;	//-lain2

?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><?=str_replace(" ","&nbsp;",$rscab->kota)?><input type="hidden" name="id_cabang_<?=$i?>" id="id_cabang_<?=$i?>" value="<?php echo $hasil->ID_CABANG?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->REKENING)?></td>
		<td><?=str_replace(" ","&nbsp;",$rsjab->nama_jab)?><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->TGL_AKTIF)?></td>
		<td><?=form_hidden(array('name'=>'strMasaKerja_'.$i,'id'=>'strMasaKerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$strmasakerja)).str_replace(" ","&nbsp;",$strmasakerja);?><input type="hidden" name="masakerja_<?=$i?>" id="masakerja_<?=$i?>" value="<?=$masakerja?>"></td>	

		<td><?=form_input(array('name'=>'target_pengambilan_'.$i,'id'=>'target_pengambilan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->TARGET_PENGAMBILAN,0), "readonly"=>true ));?></td>
		<td><?=form_input(array('name'=>'real_pengambilan_'.$i,'id'=>'real_pengambilan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->REAL_PENGAMBILAN,0), "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'all_donasi_'.$i,'id'=>'all_donasi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->ALL_DONASI,0), "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'kacab_insi_'.$i,'id'=>'kacab_insi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->KACAB_INSI,0), "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'kacab_rutin_'.$i,'id'=>'kacab_rutin_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->KACAB_RUTIN,0), "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'kacab_wakaf_'.$i,'id'=>'kacab_wakaf_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->KACAB_WAKAF,0), "readonly"=>true));?></td>
		<td><input type="hidden" name="persen_ambil_<?=$i?>" id="persen_ambil_<?=$i?>" value="<?=round($persen_ambil,0)?>">
		<?php echo form_input(array('name'=>'strpersen_ambil_'.$i,'id'=>'strpersen_ambil_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$str_persen_ambil, "readonly"=>true));?></td>

		<td><?=form_input(array('name'=>'tunjab_'.$i,'id'=>'tunjab_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->TUNJAB,0), "readonly"=>true ));?></td>
		<td><?=form_input(array('name'=>'bonus_kacab_'.$i,'id'=>'bonus_kacab_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->BONUS_KACAB,0), "readonly"=>true ));?></td>
		<td><?=form_input(array('name'=>'tunj_prestasi_'.$i,'id'=>'tunj_prestasi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->TUNJ_PRESTASI,0), "readonly"=>true));?></td>
		<td bgcolor="#ffffcc"><?=form_input(array('name'=>'penyesuaian_'.$i,'id'=>'penyesuaian_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->PENYESUAIAN,0), "onkeyup"=>"countRevenue(".$i.", this)", "onkeypress"=>"return numericVal(this,event)" ));?></td>
		<td><?=form_input(array('name'=>'totPendapatan_'.$i,'id'=>'totPendapatan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan,0) ));?></td>
<!-- potongan -->
		<td><?=form_input(array('name'=>'dansos_'.$i,'id'=>'dansos_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->DANSOS,0) , "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'zakat_'.$i,'id'=>'zakat_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->ZAKAT,0) , "readonly"=>true));?></td>
		<td bgcolor="#ffffcc"><?=form_input(array('name'=>'lain_'.$i,'id'=>'lain_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($hasil->LAIN,0), "onkeyup"=>"countExpense(".$i.", this)", "onkeypress"=>"return numericVal(this,event)" ));?></td>
		<td><?=form_input(array('name'=>'totPotongan_'.$i,'id'=>'totPotongan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPotongan,0) , "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'totTerima_'.$i,'id'=>'totTerima_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan-$totPotongan,0), "readonly"=>true ));?></td>
	<!-- GENERATE/DOWNLOAD -->
		<?	if( $sts=="edit" || $sts=="disabled"){
			$param=$tahun."_".$hasil->NIK;
			//CEK FILE_SLIP
			$strFile="select count(*) CKFILE from file_slip where thn='$thn' and bln='$bln' and jenis='kacab_bonus' and nik='".$hasil->NIK."'";
			$rsFile=$this->db->query($strFile)->row();
			echo "<td>";
			if ($rsFile->CKFILE >=1){	//ada -> tombol download
				$rsPath=$this->db->query("select * from file_slip where thn='$thn' and bln='$bln' and jenis='kacab_bonus' and nik='".$hasil->NIK."'")->row();
		?>	&nbsp;-&nbsp;<br>
			<a href="javascript:void(0)" onclick="window.open('<?=base_url($rsPath->PATH."/".$rsPath->NAMA_FILE)?>','_blank')"><i class="fa fa-print" title="Print/Download File">Download/Print</i></a>
		<?
			}else{	
			
		?>
		<a href="javascript:void(0)" data-url="<?=base_url('gaji_staf/cetak_slip/')?>" data-id="<?=$param?>" onclick="singleSlip(this)"><i class="fa fa-edit" title="Generate Slip to .pdf File">Generate&nbsp;pdf</i></a>
		<?}
				echo "</td>";
			}else{
				echo "<td>&nbsp; link</td>";
			}
			?>
			</tr>
<?
		$i++;
		}
	}
?>			

                                    </tbody>
                                </table>
                            </div> <!-- /.table-responsive -->
                       
                        
						<div class="row">
							<div class="col-md-12">
							<? 	if (sizeof($row)>0){  ?>								
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">	
										<input type="hidden" name="bln" id="bln" value="<?=$bln?>">																				
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">
										<input type="hidden" name="id_validasi" id="id_validasi" value="<?=$id_validasi?>">
								<?		if ($sts=="new" || $sts=="edit"){ ?>
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan" <?php echo ($sts_validasi==1?"disabled":"")?>>		
															
									<? }
											
								
									if( $sts=="edit" || $sts=="disabled"){
											$btCSV = array(
												'type'=>'button',
												'name'=>'btCsvBank',
												'id'=>'btCsvBank',
												'title'=>'Cetak CSV Transport Zisco',
												'value'=>'Export CSV Bank',
												'class'=>'btn btn-primary'
											);
											$btXls = array(
												'type'=>'button',
												'name'=>'btXls',
												'id'=>'btXls',
												'title'=>'Ekspor Xls',
												'value'=>'Ekspor Xls',
												'class'=>'btn btn-primary'
											);
										echo "&nbsp;".form_input($btCSV)."&nbsp;".form_input($btXls);
										?>
										<input type="button" id="doprogress" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Cetak Slip untuk data terakhir yang sudah tersimpan saja" onclick="cetakprogress()" value="Buat Slip gaji ">
										<? if( $sts=="edit"){ ?>
										&nbsp;
										<input type="button" id="btvalidasi" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Validasi Penggajian" value="Validasi">
										<? }
									}		
											
								}
								$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('gaji_kacab_bonus/index')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
							?>
							</div><!-- col -->
						</div><!-- row -->
							<br>
								<div class="row">
											<div class="col-md-8">
												<div class="progress no-display progress-lg">
													<div class="progress-bar progress-bar-success" id="progressBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
													0%
													</div>
												</div>
											</div>
										</div> 	
						 
             
</div>
</div>
<hr />
<?php echo form_close();?>
<script type="text/javascript">

$('#myTable').DataTable( {
	//"bJQueryUI": true,
	"scrollY": "500px",
	"scrollX": true,
	"scrollCollapse": true,
	"paging": false, 
	"searching": false, 
	fixedColumns:   {
            leftColumns: 3
        },
    fixedHeader: true
} );
$('#btvalidasi').click(function(){	
	$.ajax({
			type: 'POST',
			url: '<?php echo base_url('gaji_kacab_bonus/validasi');?>',
			data: { id_validasi: $("#id_validasi").val()},				
			dataType: 'json',
			success: function(msg) {
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data  Gaji Transport zisco berhasil divalidasi.','success',1000);
					setInterval(window.location.reload(), 3000);
				} else {
					$().showMessage('Terjadi kesalahan. Data gagal disimpan.','danger',700);
					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
});	
$('#btsimpankel').click(function(){		
		var form_data = $('#myform').serialize();
		$().showMessage('Sedang diproses.. Harap tunggu..');
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('gaji_kacab_bonus/save_gaji_kacab_bonus');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data  Gaji Bonus Kacab berhasil disimpan.','success',1000);
					setInterval(window.location.reload(), 3000);
				} else {
					$().showMessage('Terjadi kesalahan. Data gagal disimpan.','danger',700);
					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
		
	});
	
		
	function countRevenue(idr, idc, obj){

		var total= parseFloat( $("#tunjab_"+idr ).val()) +  parseFloat( $("#bonus_kacab_"+idr ).val()) +  parseFloat( $("#tunj_prestasi_"+idr ).val()) +  parseFloat( $("#penyesuaian_"+idr).val());

		$("#totPendapatan_"+idr).val(total);
		$("#totTerima_"+idr).val( parseFloat( $("#totPendapatan_"+idr).val() ) - parseFloat(  $("#totPotongan_"+idr).val()) ) ;
	}
	function countExpense(idr, idc, obj){
		var total= parseFloat( $("#dansos_"+idr).val() ) +  parseFloat( $("#zakat_"+idr).val() ) +  parseFloat( $("#lain_"+idr).val() );

		$("#totPotongan_"+idr).val(total);
		$("#totTerima_"+idr).val( parseFloat( $("#totPendapatan_"+idr).val() ) - parseFloat(  $("#totPotongan_"+idr).val()) ) ;
	}

$('#btXls').click(function() {		 
		//var id_validasi=$('#id_validasi').val();		
		var pilih=confirm('Export File ke Excel ?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('gaji_kacab_bonus/gaji_list'); ?>",
				dataType: 'json',
				type: 'POST',
				data: "cbBulan="+$("#bln").val()+"&cbTahun="+$("#thn").val()+"&isXls=1",				
				success: function(data,  textStatus, jqXHR){					
					//alert("File csv sudah tersimpan");
					$().showMessage('File excel sudah digenerate.','success',1000);
					window.open('<?=base_url("'+data.isi+'")?>','_blank');
					//alert(data.isi);
					
				} 
			});			
		}
		
	});

	$('#btCsvBank').click(function() {		 
		var id_validasi=$('#id_validasi').val();
		
		var pilih=confirm('Export File ke CSV?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('gaji_kacab_bonus/exportCsv'); ?>",
				dataType: 'json',
				type: 'POST',
				data: "id_validasi="+id_validasi,				
				success: function(data,  textStatus, jqXHR){					
					//alert("File csv sudah tersimpan");
					$().showMessage('File csv sudah digenerate.','success',1000);
					window.open('<?=base_url("'+data.isi+'")?>','_blank');
					//alert(data.isi);
					
				} 
			});			
		}
		
	});

$('#doprogress').click(function(){
	cetakprogress();
	});

function cetakprogress(cnt,step){
	if(typeof cnt === 'undefined'){
		var data = {id_validasi:<?=$id_validasi?>};
	} else {
		var data = {id_validasi:<?=$id_validasi?>,cnt:cnt,step:step};
	}
	$.ajax({
		url: "<?php echo base_url('gaji_kacab_bonus/slipLoop'); ?>",
		dataType: 'json',
		type: 'GET',
		data: data,
		success: function(respon){
			if (respon.status==1){
				$('.progress').fadeSlide("show");
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
					$('.progress').fadeSlide("hide");
					window.location.reload();
				}
				/*var jum = respon.jumlah;
				$('.progress').show();
				for(var i=1;i<=jum;i++){
					
				}*/
			} else {
				$('.progress').fadeSlide("hide");
			}
		}
	});
}
</script>