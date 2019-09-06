<?php echo form_open('gaji_zisco_transport/save_gaji_zisco_transport',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
<!--	<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	Informasi : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Open = Sudah ada data yang disimpan dan masih bisa diedit. <br>
	&nbsp;&nbsp;3. Closed = Melewati Periode aktif<br>
	"Slip-Email All" yang dikirim hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut terkirim<br>
	
	</div> -->
	
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover"  style="max-height:650px;overflow:scroll;" id="myTable">
                           <thead>
                             <tr >
							<th  rowspan=2 >NO</th>
							<th  rowspan=2>NIK</th>
							<th  rowspan=2>NAMA</th>
							<th  rowspan=2>KANTOR</th>
							<th  rowspan=2>JABATAN</th>
							<th  rowspan=2>TGL MASUK</th>
							<th  rowspan=2>KONTRAK KERJA</th>
							<th  rowspan=2>MASA KERJA</th>
							<th  rowspan=2>STATUS PEGAWAI</th>
							<th  colspan=2>KETIDAKHADIRAN ZISCO</th>

							
<?	$strMaster="select * from mst_komp_gaji  where isactive=1 and  is_zisco='on' and jns_zisco='transport' order by ID";
	$master=$this->db->query($strMaster)->result();
	$p=1;
	$t=1;
	$thPend=""; 
	$thPot=""; $nominal=0;
	foreach($master as $rowmaster){	
		if ($rowmaster->FLAG=='+'){
			$thPend.="<th>".$rowmaster->NAMA."</th>";
			$p++;
		}else{
			$thPot.="<th>".$rowmaster->NAMA."</th>";
			$t++;
		}
		//echo "<th>".$rowmaster->NAMA."(".$rowmaster->FLAG.")</th>";
		
	}
	$colspanAll=$p+8+$t;
?>							
							<th colspan="<?php echo $p-1?>">PENDAPATAN</th>
							<th rowspan=2>TOTAL PENDAPATAN</th>
							<th colspan="<?php echo $t-1?>">POTONGAN</th>
							<th rowspan=2>TOTAL POTONGAN</th>
							<th rowspan=2>TOTAL TERIMA</th>
							<th rowspan=2>SLIP</th>
						  </tr>
						  <tr>
						  <th>JML IJIN+SAKIT</th>
						  <th>JML ALPA</th>
						  <?php echo $thPend.$thPot?>
						  </tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan=\"".$colspanAll."\">Data Belum Ada</td></tr>";
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
		$rs_stspeg=$this->db->query("select value1 from gen_reff where reff='STSPEGAWAI' and id_reff=".$hasil->STATUS_PEGAWAI)->row();
		

		//GET ABSEN VALUE
		$jml_tidak_hadir=$hasil->JML_IJIN;
		$jml_alpa=$hasil->JML_ALPA;
		
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><?=str_replace(" ","&nbsp;",$rscab->kota)?><input type="hidden" name="id_cabang_<?=$i?>" id="id_cabang_<?=$i?>" value="<?php echo $hasil->ID_CABANG?>"></td>
		<td><?=str_replace(" ","&nbsp;",$rsjab->nama_jab)?><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td><?=str_replace(" ","&nbsp;", strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF)))?></td>
		<td><?=str_replace(" ","&nbsp;", strftime("%d %B %Y",strtotime($hasil->TGL_AWAL_KONTRAK)))?></td>
		<td><?=form_hidden(array('name'=>'strMasaKerja_'.$i,'id'=>'strMasaKerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$strmasakerja)).str_replace(" ","&nbsp;",$strmasakerja);?><input type="hidden" name="masakerja_<?=$i?>" id="masakerja_<?=$i?>" value="<?=$masakerja?>"></td>
		<td><input type="hidden" name="stspeg_<?=$i?>" id="stspeg_<?=$i?>" value="<?php echo $hasil->STATUS_PEGAWAI?>"><?=str_replace(" ","&nbsp;", $rs_stspeg->value1)?></td>
		<td><input type="hidden" name="jml_tdkmasuk_<?=$i?>" id="jml_tdkmasuk_<?=$i?>" value="<?=$jml_tidak_hadir?>"><?php echo $jml_tidak_hadir?></td>		
		<td><input type="hidden" name="jml_alpa_<?=$i?>" id="jml_alpa_<?=$i?>" value="<?=$jml_alpa?>"><?php echo $jml_alpa?></td>		
<?	
	//get key yg dipakai smua komp
	$sumPer_row=0; 
	$gapok=0;$THT=0;
	$sumPotPer_row=0;
	$j=0;
	$display_acuan=0; $acuan_trans =0;
	$acuan_motor=0;
	$display_angsuran=0; $cicilke=0; $id_header=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	$rsgrade=$this->db->query("select * from mst_grade_cabang where id_cabang=".$id_cabang)->row();
	$grade=$rsgrade->grade;	
	$rsklaster=$this->gate_db->query("select klaster, kelompok_gaji from mst_jabatan where id_jab=".$id_jab)->row();
	$klaster=$rsklaster->klaster;
	$kelompok_gaji=$rsklaster->kelompok_gaji;

	foreach($master as $rowmaster){
		$bgcolor=""; $readonly=true;
		//PENDAPATAN
		if ($rowmaster->FLAG=='+'){
		$cekmasuk="";$str1=$str2=$str3=$str4=$str5=$str6="";
		switch ($rowmaster->ID){
			case "9":	//Tj. Transport, Tabel
				$acuan_trans = $hasil->ACUAN_TRANSPORT;					
				$display_acuan=1;					
				$nominal=$hasil->U_TRANS_DITERIMA;
				break;
			case "10":	//Tj. Jabatan zisco, var
				$nominal=$hasil->T_JABATAN;
				break;
			case "11":	//BPJS Kesehatan
				$nominal=$hasil->BPJS_KESEHATAN;
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "12":	//BPJS Ketenagakerjaan
				$nominal=$hasil->BPJS_NAKER;
				$bgcolor="#ffffcc";$readonly=false;
				break;
			
			case "13":	//Penyesuaian
				$nominal=$hasil->T_PENYESUAIAN;
				$bgcolor="#ffffcc";$readonly=false;
				break;			
			case "14":	//bantuan servis motor, var, zisco dg masa kerja 3 tahun k atas dan dberikan setiap 3 bln sekali
				$nominal=$hasil->SERVIS_MOTOR;
				break;
			case "23":	//Koreksi
				$nominal=$hasil->KOREKSI;
				$bgcolor="#ffffcc";$readonly=false;
				break;
			
			default: 
				$nominal=0;
				$bgcolor="#ffffcc";$readonly=false;
				break;

		}

		$nominal=round($nominal, 0);
		echo "<td>";
		if ($display_acuan==1){
			echo number_format($acuan_trans,0,',','.').'<input type="hidden" name="acu_transport_'.$i.'" id="acu_transport_'.$i.'" value="'.$acuan_trans.'">';
		}
		
		echo form_input(array('name'=>'komp_'.$i.'_'.$j,'id'=>'komp_'.$i.'_'.$j,'class'=>'myform-control','size'=>10,  'value'=>$nominal, "style"=>"background-color:".$bgcolor, "onkeyup"=>"countRevenue(".$i.", ".$j.", this)", "onkeypress"=>"return numericVal(this,event)"))."</td>";
		if ($rowmaster->FLAG=='+'){ 
			$sumPer_row+=$nominal;
		}else{
			$sumPer_row-=$nominal;
		}
		if ($rowmaster->ID==23){?>
			<td><?=form_input(array('name'=>'subTotal_'.$i,'id'=>'subTotal_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($sumPer_row,0)));?></td>

		<? }
		//$j++; => bertambah 1 utk komp
		}else{	//end pendapatan
			//POTONGAN
			
			switch ($rowmaster->ID){
			
				case "24":	//Angsuran Pinjaman
					$display_angsuran=1;
					$nominal=$hasil->JML_ANGSURAN;
					$cicilke=$hasil->ANGSURAN_KE;
					$bgcolor="#ffffcc";$readonly=false;
					break;
				case "36":	//Pot lain2
					$nominal=$hasil->POT_LAIN;
					$bgcolor="#ffffcc";$readonly=false;
					break;
			}
			echo "<td>";
			if ($display_angsuran==1){
				echo 'Cicilan ke:'.$cicilke.'<input type="hidden" name="cicilke_'.$i.'" id="cicilke_'.$i.'" value="'.$cicilke.'"><input type="hidden" name="id_header_'.$i.'" id="id_header_'.$i.'" value="'.$id_header.'">';
			}
			echo form_input(array('name'=>'komp_'.$i.'_'.$j,'id'=>'komp_'.$i.'_'.$j,'class'=>'myform-control','size'=>10,  "style"=>"background-color:".$bgcolor, "onkeypress"=>"return numericVal(this,event)","onkeyup"=>"countExpense(".$i.", ".$j.", this)", 'value'=>round($nominal,0)))."</td>";
			if ($rowmaster->FLAG=='-'){ 
				$sumPotPer_row-=$nominal;
			}else{
				$sumPotPer_row+=$nominal;
			}
		}
		$j++;
		$display_acuan=0;
	}

	

if ($rowmaster->ID==36){
	?>
	<td><?=form_input(array('name'=>'subPotTotal_'.$i,'id'=>'subPotTotal_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>round($sumPotPer_row*-1,0) ) );?></td>
	<?
	
}?>		

<td><?=form_input(array('name'=>'subGrandTotal_'.$i,'id'=>'subGrandTotal_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>round($sumPer_row+$sumPotPer_row,0) ) );?></td>
	<!-- GENERATE/DOWNLOAD -->
		<?	if( $sts=="edit" || $sts=="disabled"){
			$param=$tahun."_".$hasil->NIK;
			//CEK FILE_SLIP
			$strFile="select count(*) CKFILE from  file_slip where thn='$thn' and bln='$bln' and jenis='zisco_transport' and nik='".$hasil->NIK."'";
			$rsFile=$this->db->query($strFile)->row();
			echo "<td>";
			if ($rsFile->CKFILE >=1){	//ada -> tombol download
				$rsPath=$this->db->query("select * from  file_slip where thn='$thn' and bln='$bln' and jenis='zisco_transport' and nik='".$hasil->NIK."'")->row();
		?>	&nbsp;-&nbsp;<br>
			<a href="javascript:void(0)" onclick="window.open('<?=base_url($rsPath->PATH."/".$rsPath->NAMA_FILE)?>','_blank')"><i class="fa fa-print" title="Print/Download File">Download/Print</i></a>
		<?
			}else{	
			
		?>
		<a href="javascript:void(0)" data-url="<?=base_url('gaji_zisco_transport/cetak_slip/')?>" data-id="<?=$param?>" onclick="singleSlip(this)"><i class="fa fa-edit" title="Generate Slip to .pdf File">Generate&nbsp;pdf</i></a>
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
							<? 	if (sizeof($row)>0){ 
									
							?>								
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">	
										<input type="hidden" name="bln" id="bln" value="<?=$bln?>">																	
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">
										<input type="hidden" name="id_validasi" id="id_validasi" value="<?=$id_validasi?>">
									<? if ($sts=="new" || $sts=="edit"){ ?>
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan" <?php echo ($sts_validasi==1?"disabled":"")?>>		
									<?}
							
								
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
												'onclick'=>"backTo('".base_url('gaji_zisco_transport/index')."');return false;",
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
$('#btXls').click(function() {		 
		//var id_validasi=$('#id_validasi').val();		
		var pilih=confirm('Export File ke Excel ?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('gaji_zisco_transport/gaji_list'); ?>",
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
$('#btvalidasi').click(function(){	
	$.ajax({
			type: 'POST',
			url: '<?php echo base_url('gaji_zisco_transport/validasi');?>',
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
			url: '<?php echo base_url('gaji_zisco_transport/save_gaji_zisco_transport');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data  Gaji Transport zisco berhasil disimpan.','success',1000);
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

		var total= parseFloat( $("#komp_"+idr+"_"+0).val() ) +  parseFloat( $("#komp_"+idr+"_"+1).val() ) +  parseFloat( $("#komp_"+idr+"_"+2).val() ) +  parseFloat( $("#komp_"+idr+"_"+3).val() ) +  parseFloat( $("#komp_"+idr+"_"+4).val() ) +  parseFloat( $("#komp_"+idr+"_"+5).val() ) +  parseFloat( $("#komp_"+idr+"_"+6).val() ) ;

		$("#subTotal_"+idr).val(total);
		$("#subGrandTotal_"+idr).val( parseFloat( $("#subTotal_"+idr).val() ) - parseFloat(  $("#subPotTotal_"+idr).val()) ) ;
	}
	function countExpense(idr, idc, obj){
		var total= parseFloat( $("#komp_"+idr+"_"+7).val() ) +  parseFloat( $("#komp_"+idr+"_"+8).val() );

		$("#subPotTotal_"+idr).val(total);
		$("#subGrandTotal_"+idr).val( parseFloat( $("#subTotal_"+idr).val() ) - parseFloat(  $("#subPotTotal_"+idr).val()) ) ;
	}


	$('#btCsvBank').click(function() {		 
		var id_validasi=$('#id_validasi').val();
		
		var pilih=confirm('Export File ke CSV?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('gaji_zisco_transport/exportCsv'); ?>",
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
		url: "<?php echo base_url('gaji_zisco_transport/slipLoop'); ?>",
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