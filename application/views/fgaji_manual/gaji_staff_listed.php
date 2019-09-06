<?php echo form_open('thr_staff/save_thr_staff',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success">
	Informasi : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Open = Sudah ada data yang disimpan dan masih bisa diedit. <br>
	&nbsp;&nbsp;3. Closed = Melewati Tahun aktif<br>
	"Slip-Email All" yang dikirim hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut terkirim<br>
	
	</div> 
		<div class="panel panel-default" style="max-height:560px;overflow:scroll;">
			<div class="panel-heading">Daftar THR Staff</div><!-- /.panel-heading -->
				<div class="panel-body" >
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
                           <thead>
                           <tr >
							<th >NO</th>
							<th >NIK</th>
							<th >NAMA</th>
							<th >JABATAN</th>
							<th >TGL MASUK</th>
							<th >MASA KERJA</th>
							<th >S/M/GM</th>
<?	$strMaster="select * from mst_komp_gaji  where isactive=1 and  is_staff='on' order by ID";
	$master=$this->db->query($strMaster)->result();
	$p=1;	$th=""; $nominal=0;
	foreach($master as $rowmaster){		
		//$th.="<th>".$rowmaster->NAMA."</th>";
		echo "<th>".$rowmaster->NAMA."(".$rowmaster->FLAG.")</th>";
		$p++;
	}
	$colspanAll=$p+8;
?>														
							<th >TOTAL PENDAPATAN</th>
							<th >SLIP</th>
						  </tr>
						  
                         </thead>
                                    <tbody>
<?	
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan=\"".$colspanAll."\">Data Belum Ada</td></tr>";
	}else{
		$i=1;
		foreach($row as $hasil){
			$masakerja=$hasil->masa_kerja_bln;		//dlm bulan
			//hitung masa kerja
			if ($masakerja<12){				
				$strmasakerja=number_format($hasil->masa_kerja_bln,0,',','')." Bln";	
				$thnMasakerja=0;
			}else{
				$strmasakerja=floor($hasil->masa_kerja_bln/12)." Thn, ".($hasil->masa_kerja_bln%12)." Bln";
				$thnMasakerja=floor($hasil->masa_kerja_bln/12);
			}

		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><?=str_replace(" ","&nbsp;",$rsjab->nama_jab)?><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td><?=str_replace(" ","&nbsp;", strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF)))?></td>
		<td><?=form_hidden(array('name'=>'strMasaKerja_'.$i,'id'=>'strMasaKerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$strmasakerja)).str_replace(" ","&nbsp;",$strmasakerja);?><input type="hidden" name="masakerja_<?=$i?>" id="masakerja_<?=$i?>" value="<?=$masakerja?>"></td>
<?	//get key yg dipakai smua komp
	$sumPer_row=0;
	$j=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	$rsgrade=$this->db->query("select * from mst_grade_cabang where id_cabang=".$id_cabang)->row();
	$rsklaster=$this->gate_db->query("select klaster, kelompok_gaji from mst_jabatan where id_jab=".$id_jab)->row();
	$klaster=$rsklaster->klaster;
	$grade=$rsgrade->grade;
	echo '<td>'.$rsklaster->kelompok_gaji.'</td>';
	foreach($master as $rowmaster){
		
		$cekmasuk="";$str1=$str2=$str3=$str4=$str5=$str6="";
		switch ($rowmaster->ID){
			case "1":	//gapok, table
				$nominal = $hasil->gapok;				
				$cekmasuk="1";
				break;
			case "2":	//Acuan Uang makan, table
				$nominal = $hasil->uang_makan;	
				$cekmasuk="2";
				break;
			case "3":	//Insentif Kehadiran, var
				$nominal = $hasil->tunj_kehadiran;	
				$cekmasuk="3";
				break;
			case "6":	//Tj. Masa Kerja, table
				$nominal = $hasil->tunj_masakerja;	
				$cekmasuk="6";
				break;
			case "7":	//Tj. Jabatan, table
				$nominal = $hasil->tunj_jabatan;	
				$cekmasuk="7";
				break;
			default: 
				$nominal=0;$cekmasuk="default";
				break;

		}
		if ($sts=="edit"){
			echo "<td>".form_input(array('name'=>'komp_'.$i.'_'.$j,'id'=>'komp_'.$i.'_'.$j,'class'=>'myform-control','size'=>10,  'value'=>$nominal))."</td>";
		}else{
			echo "<td>".form_input(array('name'=>'komp_'.$i.'_'.$j,'id'=>'komp_'.$i.'_'.$j,'class'=>'myform-control','size'=>10, "readonly"=>true, 'value'=>$nominal))."</td>";
		}
		if ($rowmaster->FLAG=='+'){ 
			$sumPer_row+=$nominal;
		}else{
			$sumPer_row-=$nominal;
		}
		$j++;
		
	}


?>
	<td><?=form_input(array('name'=>'subTotal_'.$i,'id'=>'subTotal_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$sumPer_row));?></td>

	
	<!-- GENERATE/DOWNLOAD -->
		<?	if( $sts=="edit" || $sts=="disabled"){
			$param=$tahun."_".$hasil->NIK;
			//CEK FILE_SLIP
			$strFile="select count(*) CKFILE from file_slip_thr where thn='$tahun' and nik='".$hasil->NIK."'";
			$rsFile=$this->db->query($strFile)->row();
			echo "<td>";
			if ($rsFile->CKFILE >=1){	//ada -> tombol download
				$rsPath=$this->db->query("select * from file_slip_thr where thn='$tahun' and nik='".$hasil->NIK."'")->row();
		?>	&nbsp;-&nbsp;<br>
			<a href="javascript:void(0)" onclick="window.open('<?=base_url($rsPath->PATH."/".$rsPath->NAMA_FILE)?>','_blank')"><i class="fa fa-print" title="Print/Download File">Download/Print</i></a>
		<?
			}else{	
			
		?>
		<a href="javascript:void(0)" data-url="<?=base_url('thr_staff/cetak_slip/')?>" data-id="<?=$param?>" onclick="singleSlip(this)"><i class="fa fa-edit" title="Generate Slip to .pdf File">Generate&nbsp;pdf</i></a>
		<?}
				echo "</td>";
			}else{
				echo "<td>&nbsp;</td>";
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
                        </div><!-- /.panel-body -->
                    </div> <!-- /.panel -->
					
                        
						<div class="row">
							<div class="col-md-12">
							<? 	if (sizeof($row)>0){ 
									if ($sts=="new" || $sts=="edit"){
							?>								
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">
										<input type="hidden" name="tahun" id="tahun" value="<?=$tahun?>">	
										<input type="hidden" name="id_cabang" id="id_cabang" value="<?=$id_cabang?>">
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan">		
							<?
									}
									$disabled="disabled"; 
										if (date('d')>=21 && date('d')<=date('t')){ //21-last day && role admin/mgr pusat
												$disabled="";
											}
										
										if ($cek->CEK >0 && $cek->VALIDASI !=1){
											echo '<input type="button" class="btn btn-primary" id="btvalidasi" value="Validasi Rekap" '.$disabled.'>';
										} 
									if( $sts=="edit" || $sts=="disabled"){
											$btCSV = array(
												'type'=>'button',
												'name'=>'btCsvBank',
												'id'=>'btCsvBank',
												'value'=>'Export CSV Bank',
												'class'=>'btn btn-primary'
											);
										echo "&nbsp;".form_input($btCSV)."&nbsp;";
										?>
										<input type="button" id="doprogress" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Cetak Slip untuk data terakhir yang sudah tersimpan saja" onclick="cetakprogress()" value="generate Slip All ">
										<?
									}		
											
								}
								
								$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('thr_staff/index')."');return false;",
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
$('#btsimpankel').click(function(){		
		var form_data = $('#myform').serialize();
		$().showMessage('Sedang diproses.. Harap tunggu..');
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('thr_staff/save_thr_staff');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data THR Staff berhasil disimpan.','success',1000);
					window.location.reload();
					
					
				} else {
					$().showMessage('Terjadi kesalahan. Data gagal disimpan.','danger',700);
					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				//$().showMessage('Terjadi kesalahan.<br />'+	textStatus + ' - ' + errorThrown ,'danger');
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
		
	});
$('#btCsvBank').click(function() {		 
		var thn=$('#tahun').val();
		var cabang=$('#id_cabang').val();
		
		var pilih=confirm('Export File ke CSV?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('thr_staff/exportCsv'); ?>",
				dataType: 'json',
				type: 'POST',
				data: "thn="+thn+"&id_cabang="+cabang,				
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
		var data = {cabang:<?=$id_cabang?>, thn: <?="'".$tahun."'"?>};
	} else {
		var data = {cabang:<?=$id_cabang?>, thn: <?="'".$tahun."'"?>,cnt:cnt,step:step};
	}
	$.ajax({
		url: "<?php echo base_url('thr_staff/slipLoop'); ?>",
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