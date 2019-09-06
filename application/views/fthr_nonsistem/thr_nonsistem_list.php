<?php echo form_open('thr_nonsistem/save_gaji',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	Informasi : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Open = Sudah ada data yang disimpan dan masih bisa diedit. <br>
	&nbsp;&nbsp;3. Closed = Melewati Tahun aktif<br>
	"Slip-Email All" yang dikirim hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut terkirim<br>
	
	</div>
	
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover"  style="max-height:650px;overflow:scroll;" id="myTable">
                           <thead>
                           <tr >
							<th  >NO</th>
							<th  >NIK</th>
							<th  >NAMA</th>
							<th  >KANTOR</th>
							<th  >JABATAN</th>
							<th  >TGL MASUK</th>
							<th  >MASA KERJA</th>
							<th  >S/M/GM</th>							
							<th >PENDAPATAN</th>
							<th >POTONGAN</th>							
							<th  >TOTAL TERIMA</th>
							<th  >SLIP</th>
						  </tr>
						  </thead>
                       <tbody>
<?	$colspanAll=12;
	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";

	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan=\"".$colspanAll."\">Data Belum Ada</td></tr>";
	}else{
		$i=1;	//as row
		
		foreach($row as $hasil){
			$masakerja=$hasil->SELISIH;		//dlm bulan
			//hitung masa kerja
			if ($masakerja<12){				
				$strmasakerja=number_format($hasil->SELISIH,0,',','')." Bln";	
				$thnMasakerja=0;
			}else{
				$strmasakerja=floor($hasil->SELISIH/12)." Thn, ".($hasil->SELISIH%12)." Bln";
				$thnMasakerja=floor($hasil->SELISIH/12);
			}

		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
		$rscab=$this->gate_db->query("select * from mst_cabang where id_cabang=".$hasil->ID_CABANG )->row();
		//echo "<tr><td colspan=$p>select * from mst_jabatan where id_jab=".$hasil->ID_JAB."<br>idjab=".$rsjab->id_jab."</td></tr>";

?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="id_cab_<?=$i?>" id="id_cab_<?=$i?>" value="1"></td>
		<td><?=str_replace(" ","&nbsp;",$rscab->kota)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><?=str_replace(" ","&nbsp;",$rsjab->nama_jab)?><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td><?=str_replace(" ","&nbsp;", strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF)))?></td>
		<td><?=form_hidden(array('name'=>'strMasaKerja_'.$i,'id'=>'strMasaKerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$strmasakerja)).str_replace(" ","&nbsp;",$strmasakerja);?><input type="hidden" name="masakerja_<?=$i?>" id="masakerja_<?=$i?>" value="<?=$masakerja?>"></td>
<?	//get key yg dipakai smua komp
	$sumPer_row=0; 
	$sumPotPer_row=0;
	$j=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	$rsklaster=$this->gate_db->query("select klaster, kelompok_gaji from mst_jabatan where id_jab=".$id_jab)->row();
	$klaster=$rsklaster->klaster;
	$kelompok_gaji=$rsklaster->kelompok_gaji;
	
	echo '<td>'.$rsklaster->kelompok_gaji.'</td>';
	echo "<td>".form_input(array('name'=>'pendapatan_'.$i,'id'=>'pendapatan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>0 , "onkeypress"=>"return numericVal(this,event)", "onkeyup"=>"countResult(this, ".$i.")"))."</td>";
	echo "<td>".form_input(array('name'=>'potongan_'.$i,'id'=>'potongan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>0 , "onkeypress"=>"return numericVal(this,event)", "onkeyup"=>"countResult(this, ".$i.")" ) )."</td>";
	echo "<td>".form_input(array('name'=>'total_'.$i,'id'=>'total_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>0 ) )."</td>";
	// GENERATE/DOWNLOAD 
			if( $sts=="edit" || $sts=="disabled"){
			$param=$tahun."_".$hasil->NIK;
			//CEK FILE_SLIP
			$strFile="select count(*)	CKFILE from file_slip where thn='$tahun' and bln='$bln' and nik='".$hasil->NIK."'";
			$rsFile=$this->db->query($strFile)->row();
			echo "<td>";
			if ($rsFile->CKFILE >=1){	//ada -> tombol download
				$rsPath=$this->db->query("select * from file_slip where thn='$tahun'  and bln='$bln'  and nik='".$hasil->NIK."'")->row();
			echo '&nbsp;-&nbsp;<br><a href="javascript:void(0)" onclick="window.open("'.base_url($rsPath->PATH."/".$rsPath->NAMA_FILE).'","_blank")"><i class="fa fa-print" title="Print/Download File">Download/Print</i></a>';
		
			}else{	
			
			echo '<a href="javascript:void(0)" data-url="'.base_url('thr_nonsistem/cetak_slip/').'" data-id="<?=$param?>" onclick="singleSlip(this)"><i class="fa fa-edit" title="Generate Slip to .pdf File">Generate&nbsp;pdf</i></a>';
			}
			
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
									if ($sts=="new" || $sts=="edit"){
							?>								
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">	
										<input type="hidden" name="tahun" id="tahun" value="<?=$tahun?>">	
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan">		
							<?
									}
											
								}
								
								$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('thr_nonsistem/index')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
							?>
							</div><!-- col -->
						</div><!-- row -->

						
             
</div>
<hr />
<?php echo form_close();?>
<script type="text/javascript">

$('#myTable').DataTable( {
	"bJQueryUI": true,
//	"scrollY": true,
//	"scrollX": "800px",
	"scrollCollapse": true,
	"paging": false, 
	"searching": false, 
	/*fixedColumns:   {
            leftColumns: 3
        },*/
    fixedHeader: false
} );
$('#btsimpankel').click(function(){		
		var form_data = $('#myform').serialize();
		$().showMessage('Sedang diproses.. Harap tunggu..');
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('thr_nonsistem/save_thr_nonsistem');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data THR Karyawan berhasil disimpan.','success',1000);
					window.location.reload();
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

function countResult(obj, idx){		
	$("#total_"+idx).val( parseFloat($("#pendapatan_"+idx).val() ) - parseFloat($("#potongan_"+idx).val() ) );		
		
}
</script>