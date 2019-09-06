<?php echo form_open('thr_zisco/save_thr_zisco',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success">
	Informasi : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Open = Sudah ada data yang disimpan dan masih bisa diedit. <br>
	&nbsp;&nbsp;3. Closed = Melewati Tahun aktif<br>
	"Slip-Email All" yang dikirim hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut terkirim<br>
	
	</div>
	
		<div class="panel panel-default" >
			<div class="panel-heading">Daftar THR Zisco</div><!-- /.panel-heading -->
				<div class="panel-body" >
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover" style="max-height:650px;overflow:scroll;" id="myTable">
                           <thead>
                           <tr >
							<th rowspan=2>NO</th>
							<th rowspan=2>NIK</th>
							<th rowspan=2>NAMA</th>
							<th  rowspan=2>KANTOR</th>
							<th  rowspan=2>JABATAN</th>
							<th  rowspan=2>TGL MASUK</th>
							<th  rowspan=2>KONTRAK KERJA</th>
							<th  rowspan=2>MASA KERJA</th>
							<th  rowspan=2>STATUS PEGAWAI</th>
							<th  rowspan=2>KETIDAKHADIRAN ZISCO</th>
<?	$strMaster="select * from mst_komp_gaji  where isactive=1 and is_thr=1 and is_zisco='on' order by flag, ID";
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
		//echo "<th rowspan=2>".$rowmaster->NAMA."(".$rowmaster->FLAG.")"."</th>";
		
	}
	$colspanAll=$p+8;
?>														
							<th colspan="<?php echo $p-1?>">PENDAPATAN</th>
							<th rowspan=2>TOTAL PENDAPATAN</th>
							<th colspan="<?php echo $t-1?>">POTONGAN</th>
							<th rowspan=2>TOTAL POTONGAN</th>
							<th rowspan=2>TOTAL TERIMA</th>
							<th rowspan=2>SLIP</th>
						  </tr>
						   <tr>							
						  <?php echo $thPend.$thPot?>
						  </tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan='".$colspanAll."'>sql= $str</td></tr>";
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
		$rscab=$this->gate_db->query("select * from mst_cabang where id_cabang=".$hasil->ID_CABANG )->row();
		$rs_stspeg=$this->db->query("select value1 from gen_reff where reff='STSPEGAWAI' and id_reff=".$hasil->STATUS_PEGAWAI)->row();
		$jml_tidak_hadir=0;
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

<?	//get key yg dipakai smua komp
	$sumPer_row=0; 
	$gapok=0;$THT=0;
	$sumPotPer_row=0; 
	$j=0;
	$display_angsuran=0; $cicilke=0; $id_header=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	/*$rsgrade=$this->db->query("select * from mst_grade_cabang where id_cabang=".$id_cabang)->row();
	$grade=$rsgrade->grade;*/
	$rsklaster=$this->gate_db->query("select klaster, kelompok_gaji from mst_jabatan where id_jab=".$id_jab)->row();
	$klaster=$rsklaster->klaster;
	
	$sts_pegawai=$hasil->STATUS_PEGAWAI;	

	foreach($master as $rowmaster){
		$bgcolor=""; $readonly=true;
		$cekmasuk="";$str1=$str2=$str3=$str4=$str5=$str6="";
		if ($rowmaster->FLAG=='+'){
		switch ($rowmaster->ID){
			case "9":	//Tj. Transport, Tabel
				$nominal=$hasil->uang_transport;				
				break;
			case "10":	//Tj. Jabatan zisco, var
				$nominal=$hasil->tunj_jabatan;	
				break;
			case "13":	//Penyesuaian
				$nominal=$hasil->penyesuaian;		
				$bgcolor="#ffffcc";$readonly=false;
				break;			
			
			case "23":	//Koreksi
				$nominal=$hasil->koreksi;		
				$bgcolor="#ffffcc";$readonly=false;
				break;
			
			default: 
				$nominal=0;
				$bgcolor="#ffffcc";$readonly=false;
				break;

		}
		echo "<td>".form_input(array('name'=>'komp_'.$i.'_'.$j,'id'=>'komp_'.$i.'_'.$j,'class'=>'myform-control','size'=>10, 'value'=>$nominal))."</td>";
		if ($rowmaster->FLAG=='+'){ 
			$sumPer_row+=$nominal;
		}else{
			$sumPer_row-=$nominal;
		}
		
		
	
	if ($rowmaster->ID==23){?>
			<td><?=form_input(array('name'=>'subTotal_'.$i,'id'=>'subTotal_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($sumPer_row,0)));?></td>

		<? }

	}else{
		//POTONGAN
			
			switch ($rowmaster->ID){
				case "15":	//dansos
					$nominal=$hasil->potongan_dansos;	
					$bgcolor="#ffffcc";$readonly=false;
					break;
				/*case "24":	//Angsuran Pinjaman
					$jmlcicilan=$rsPinj->JML_CICILAN;
					$cicilke=$rsPinj->CICILAN_KE;
					$display_angsuran=1;
					$nominal=$jmlcicilan;
					$bgcolor="#ffffcc";$readonly=false;
					break;*/
				case "36":	//Pot lain2
					$nominal=$hasil->potongan_lain;			
					$bgcolor="#ffffcc";$readonly=false;
					break;
			}
			echo "<td>";
			/*if ($display_angsuran==1){
				echo 'Cicilan ke:'.$cicilke.'<input type="hidden" name="cicilke_'.$i.'" id="cicilke_'.$i.'" value="'.$cicilke.'"><input type="hidden" name="id_header_'.$i.'" id="id_header_'.$i.'" value="'.$id_header.'">';
			}*/
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

<td><?=form_input(array('name'=>'subGrandTotal_'.$i,'id'=>'subGrandTotal_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>round($sumPer_row+$sumPotPer_row,0) ) );?>

</td><td>&nbsp;-</td></tr>	
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
									
							?>								
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">
										<input type="hidden" name="tahun" id="tahun" value="<?=$tahun?>">	
										<!-- <input type="hidden" name="id_cabang" id="id_cabang" value="<?=$id_cabang?>"> -->
										<input type="hidden" name="id_validasi" id="id_validasi" value="<?=$id_validasi?>">
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">
									<? if ($sts=="new" || $sts=="edit"){ ?>
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan"  <?php echo ($sts_validasi==1?"disabled":"")?>>		
							<?
									}
									$disabled="disabled"; 
										if (date('d')>=21 && date('d')<=date('t')){ //21-last day && role admin/mgr pusat
												$disabled="";
											}
										
										
									if( $sts=="edit" || $sts=="disabled"){
											$btCSV = array(
												'type'=>'button',
												'name'=>'btCsvBank',
												'id'=>'btCsvBank',
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
										<input type="button" id="doprogress" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Cetak Slip untuk data terakhir yang sudah tersimpan saja" onclick="cetakprogress()" value="generate Slip All ">
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
												'onclick'=>"backTo('".base_url('thr_zisco/index')."');return false;",
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
function countRevenue(idr, idc, obj){

		var total= parseFloat( $("#komp_"+idr+"_"+0).val() ) +  parseFloat( $("#komp_"+idr+"_"+1).val() ) +  parseFloat( $("#komp_"+idr+"_"+2).val() ) +  parseFloat( $("#komp_"+idr+"_"+3).val() );

		$("#subTotal_"+idr).val(total);
		$("#subGrandTotal_"+idr).val( parseFloat( $("#subTotal_"+idr).val() ) - parseFloat(  $("#subPotTotal_"+idr).val()) ) ;
	}
function countExpense(idr, idc, obj){
		var total= parseFloat( $("#komp_"+idr+"_"+4).val() ) +  parseFloat( $("#komp_"+idr+"_"+5).val() )  ;

		$("#subPotTotal_"+idr).val(total);
		$("#subGrandTotal_"+idr).val( parseFloat( $("#subTotal_"+idr).val() ) - parseFloat(  $("#subPotTotal_"+idr).val()) ) ;
	}
$('#btsimpankel').click(function(){		
		var form_data = $('#myform').serialize();
		$().showMessage('Sedang diproses.. Harap tunggu..');
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('thr_zisco/save_thr_zisco');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data THR Zisco berhasil disimpan.','success',1000);
					//setInterval(window.location.reload(), 3000);					
					
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
		var id_validasi=$('#id_validasi').val();
		
		var pilih=confirm('Export File ke CSV?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('thr_zisco/exportCsv'); ?>",
				dataType: 'json',
				type: 'POST',
				data: "thn="+thn+"&id_validasi="+id_validasi,				
				success: function(data,  textStatus, jqXHR){					
					//alert("File csv sudah tersimpan");
					$().showMessage('File csv sudah digenerate.','success',1000);
					window.open('<?=base_url("'+data.isi+'")?>','_blank');
					//alert(data.isi);
					
				} 
			});			
		}
		
	});
$('#btXls').click(function() {		 
		//var id_validasi=$('#id_validasi').val();		
		var pilih=confirm('Export File ke Excel ?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('thr_zisco/thr_list'); ?>",
				dataType: 'json',
				type: 'POST',
				data: "tahun="+$("#tahun").val()+"&isXls=1",				
				success: function(data,  textStatus, jqXHR){					
					//alert("File csv sudah tersimpan");
					$().showMessage('File excel sudah digenerate.','success',1000);
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
		var data = {id_validasi:<?=$id_validasi?>, thn: <?="'".$tahun."'"?>};
	} else {
		var data = {id_validasi:<?=$id_validasi?>, thn: <?="'".$tahun."'"?>,cnt:cnt,step:step};
	}
	$.ajax({
		url: "<?php echo base_url('thr_zisco/slipLoop'); ?>",
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

$('#btvalidasi').click(function(){	
	$.ajax({
			type: 'POST',
			url: '<?php echo base_url('thr_staff/validasi');?>',
			data: { id_validasi: $("#id_validasi").val()},				
			dataType: 'json',
			success: function(msg) {
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data  THR zisco berhasil divalidasi.','success',1000);
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
</script>