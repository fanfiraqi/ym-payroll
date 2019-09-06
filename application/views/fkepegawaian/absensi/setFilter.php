<?php echo form_open('absensi/rekap_list',array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">	
	<div class="col-xs-12">
		<div class="panel panel-default"><div class="panel-heading">Pilih Kategori Cabang & Divisi
		</div><div class="panel-body">
		
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">KOTA CABANG</label>
					<div class="col-sm-8"><?=form_dropdown('id_cabang',$cabang,'','id="id_cabang" class="form-control"');?></div>
				</div>
			</div>
		</div>
		<!-- <div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label  class="col-sm-4 control-label">DIVISI</label>
					<div class="col-sm-8"><?=form_dropdown('id_divisi',$divisi,'','id="id_divisi" class="form-control" disabled');?></div>
				</div>
			</div>
		</div> -->
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

		</div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-md-6">
		
			<?php 
			$btsubmit = array(
					'name'=>'btsubmit',
					'id'=>'btsubmit',
					'value'=>'Lanjutkan',
					'class'=>'btn btn-primary'
				);
			echo form_submit($btsubmit);?> 
	</div>
</div>
<?php echo form_close();?>
<script type="text/javascript">
$('#id_cabang').change(function(){
	//alert($('#id_cabang').val());
	$.ajax({
		url: "<?php echo base_url('payroll_staff/comboDivByCab'); ?>",
		dataType: 'json',
		type: 'POST',
		data: {id_cabang:$(this).val()},
		success: function(respon){
			$('#id_divisi').find('option').remove().end();
			//console.log('respon.data',respon.data.ID_DIV);
			if (respon.status==1){
				var item = respon.data;
				for (opt=0;opt<item.length;opt++){
					$('#id_divisi').append('<option value="'+item[opt].ID_DIV+'">'+item[opt].ID_DIV+' - '+item[opt].NAMA_DIV+'</option>');
				}
				$('#btsubmit').attr('disabled', false);
				$('#id_divisi').attr('disabled', false);
			}else{
				alert('Divisi Cabang ini belum ada');
				$('#btsubmit').attr('disabled', true);
			}
			$('#id_divisi').trigger('change');
		}
	});
	
}).trigger('change');
/*$('#myform').submit(function(event) {
	$(this).saveForm('<?php echo base_url('payroll_staff/set_listEntri');?>','<?php echo base_url('payroll_staff');?>');
	event.preventDefault();
});*/
</script>

