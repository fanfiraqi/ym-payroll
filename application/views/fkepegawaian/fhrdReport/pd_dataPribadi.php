<? if ($display==0){
	$viewKop=$this->commonlib->tableKop('HRD-DK','Data Karyawan', '00', 1,3);
	echo $viewKop;
}
?>
<table class="bordered">
    <thead>
    <tr>
        <th colspan=2>DATA PERSONAL</th>        
    </tr>
    </thead>
    <tbody>
	<tr><td>NAMA KARYAWAN</td><td><?=$row->NAMA;?></td></tr>      
	<tr><td>NIK</td><td><?=$row->NIK;?></td></tr>      
	<tr><td>JENIS KELAMIN</td><td><?=$row->NAMA_SEX;?></td></tr>      
	<tr><td>JABATAN/DIVISI/CABANG</td><td><?=$row->NAMA_JAB." / ".$row->NAMA_DIV." / ".$row->NAMA_CABANG;?></td></tr>      
	<tr><td>TANGGAL AKTIF KERJA</td><td><?=strftime('%d %B %Y',strtotime($row->TGL_AKTIF));?></td></tr>      
	<tr><td>NAMA PENDIDIKAN TERAKHIR</td><td><?=$row->NAMA_DIDIK;?></td></tr>      
	<tr><td>ALAMAT</td><td><?=$row->ALAMAT;?></td></tr>      
	<tr><td>TEMPAT, TANGGAL LAHIR</td><td><?=$row->TEMPAT_LAHIR.", ".strftime('%d %B %Y',strtotime($row->TGL_LAHIR));?></td></tr> 
	<tr><td>NO. HP</td><td><?=$row->TELEPON;?></td></tr>
	<tr><td>ALAMAT E-MAIL</td><td><?=$row->EMAIL;?></td></tr>
	<tr><td>STATUS PERNIKAHAN</td><td><?=$row->NIKAH;?></td></tr>
	<tr><td>NO. REKENING/PAYROLL</td><td><?=$row->REKENING;?></td></tr>
	<tr><td>STATUS KARYAWAN</td><td><?=$row->STS_PEGAWAI;?></td></tr>	
</tbody>
</table><br>
<? if ($display==0){
	$param=$nik."_pribadi_1";
?>
<div class="row" style="text-align:center">
	<div class="col-md-12">	
		<a href="<?=base_url('hrdReportPersonal/personalData/'.$param)?>" class="btn btn-success">Print Data Pribadi</a><br>
		<!-- <button id="btPrint_dp" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Cetak Data Pribadi">Print Data Pribadi</button> -->
	</div>
</div>	
<script type="text/javascript">

$('#btPrint_dp').click(function() {		
	//	var pilih=confirm('Cetak Data Ini?');		
	//	if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('hrdReportPersonal/personalData'); ?>",
				dataType: 'json',
				type: 'GET',
				data: "nik="+<?=$nik?>+"&sub=pribadi"+"&display=1&kunci=1",				
				success: function(data,  textStatus, jqXHR){					
					//data				
				} 
			});			
	//	}
		
	});
</script>

<?
}
?>
