<?php echo form_open('gaji_zisco_transport/gaji_list',array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">	
	<div class="col-xs-12">
		<div class="panel panel-default"><div class="panel-heading">Pilih Kategori 
		</div><div class="panel-body">
	<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label  class="col-sm-4 control-label">BULAN</label>
					<div class="col-sm-8"><?=form_dropdown('cbBulan',$arrBulan,date('m'),'id="cbBulan" class="form-control"');?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label  class="col-sm-4 control-label">TAHUN</label>
					<div class="col-sm-8"><?=form_dropdown('cbTahun',$arrThn, date('Y'),'id="cbTahun" class="form-control"');?></div>
				</div>
			</div>
		</div>

		</div></div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-md-6">
		
			<?php 
			$btsubmit = array(
			        'type'=>'button',
					'name'=>'btsubmit',
					'id'=>'btsubmit',
					'value'=>'Lanjutkan',
					'class'=>'btn btn-primary'
				);
			echo form_input($btsubmit);?> 
			<input type="hidden" name="isXls" id="isXls" value="0">
	</div>
</div>
<?php echo form_close();?>
<script type="text/javascript">
$('#btsubmit').click(function(){		
		var form_data = $('#myform').serialize();
		
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url('gaji_zisco_transport/check_rekap_absen');?>",
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				 
				 console.log(msg);
				if(msg.status =='success'){
					$('#myform').submit();					
				} else{
					bootbox.alert(msg.pesan);				
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
	});
</script>

