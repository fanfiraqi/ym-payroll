<?php echo form_open('ubudiah_fo/save_ubudiahEntri',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success">
	Form Entry Penilaian Ubudiah FO <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Edit = Sudah ada data yang disimpan ditampilkan untuk diedit, atau ada data yang belum dientrikan<br>
	&nbsp;&nbsp;3. View/Disabled = Melewati Bulan aktif<br>
	</div>
		<div class="panel panel-default">
			<div class="panel-heading">Entry Penilaian Ubudiah FO Bulanan</div><!-- /.panel-heading -->
				<div class="panel-body" style="overflow:scroll;">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
                           <thead>
                           <tr >
							<th rowspan="2">NO</th>
							<th rowspan="2">NIK</th>
							<th rowspan="2">NAMA</th>
							<th rowspan="2">JABATAN</th>
							<th rowspan="2">JUMLAH HARI</th>							
						  </tr>						 
                         </thead>
                                    <tbody>
<?	//echo "<tr><td colspan=20>".sizeof($row)."</td></tr>";
$i=1;
	if (sizeof($row)==0){		
		
		echo "<tr align=center><td colspan=20>Data Belum Ada</td></tr>";
	}else{
	
	foreach($row as $hasil){ 
		$jmlHari=0;
		$strGet="select JML_HARI from ubudiah_fo where bln='".$digitBln."' and thn='".$thn."' and nik='".$hasil->NIK."'";
			if ($this->db->query($strGet)->num_rows()>0){
				$rskerja=$this->db->query($strGet)->row();
				$jmlHari=$rskerja->JML_HARI;
			}
		?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA_JAB)?></td>		
		<td><?=form_input(array('name'=>'jmlHari_'.$i,'id'=>'jmlHari_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$jmlHari));?></td>		
		
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
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">	
										<input type="hidden" name="bln" id="bln" value="<?=$digitBln?>">
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">	
										<input type="button" class="btn btn-primary" id="btsubmit" value="Simpan">
											<?php
											$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Batal',
												'onclick'=>"backTo('".base_url('ubudiah_fo/index')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
											?> 
									</div>
								</div>
						<?}else {
						$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('ubudiah_fo/index')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "<br>".form_button($btback);
						}?>
                </div>
</div>
<hr />
<?php echo form_close();?>
<script type="text/javascript">
/*$('#myform').submit(function(event) {
	$(this).saveForm('<?php echo base_url('ubudiah_fo/save_ubudiahEntri');?>','<?php echo base_url('ubudiah_fo/save_ubudiahEntri');?>');
	event.preventDefault();
});
*/
$('#btsubmit').click(function(){		
		var form_data = $('#myform').serialize();
		$().showMessage('Sedang diproses.. Harap tunggu..');
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('ubudiah_fo/save_ubudiahEntri');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Entri nilai ubudiah berhasil disimpan.','success',1000);
					window.location.reload();
					//$("#divformkel").fadeSlide("hide");
					//$('#dataTables-cab').dataTable().fnReloadAjax();
					
				} else {
					$().showMessage('Terjadi kesalahan. Data gagal disimpan.','danger',700);
					//bootbox.alert("Terjadi kesalahan. Data gagal disimpan.");
					//$("#errorHandler").html(msg.errormsg).show();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				//$().showMessage('Terjadi kesalahan.<br />'+	textStatus + ' - ' + errorThrown ,'danger');
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
		//$().showMessage('Data pembelian berhasil disimpan, data order akan dikirim melalui sms','success',1000);
	});
</script>