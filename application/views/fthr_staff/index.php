<p><?php echo anchor('thr_staff/create_master','Tambah Master',array('id'=>'btsubmit','class'=>'btn btn-primary'));?> </p>
<hr />
<?php echo form_open('thr_staff/edit_master',array('class'=>'form-horizontal','id'=>'myform'));?>

<div class="row">
	<div class="col-sm-3">
		<div class="form-group">
			<label for="cabang" class="col-sm-4 control-label">Tahun</label>
			<div class="col-sm-6">
				<?php
					echo form_dropdown('tahun',$thn,'','id="tahun" class="form-control"');
				?>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="form-group">
			<label for="cabang" class="col-sm-4 control-label">Cabang</label>
			<div class="col-sm-8">
				<?php
					echo form_dropdown('cabang',$cabang,'','id="cabang" class="form-control"');
				?>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="form-group">
			<label for="divisi" class="col-sm-4 control-label">Divisi</label>
			<div class="col-sm-8">
				<?php
					echo form_dropdown('divisi',$divisi,'','id="divisi" class="form-control"');
				?>
			</div>
		</div>
	</div>
	<div class="col-sm-1">
	<?php 
			$btedit = array(
					'name'=>'btsubmit',
					'id'=>'btsubmit',
					'value'=>'Edit',
					'class'=>'btn btn-primary'
				);
			echo form_submit($btedit);?> 
	</div>

</div>
<?php echo form_close();?>
<hr />
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="dataTables">
		<thead>
			<tr>
				<th>TAHUN</th>				
				<th>CABANG</th>				
				<th>DIVISI</th>
				<th>KOMPONEN GAJI</th>
				
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
</div>
<!-- /.table-responsive -->
<script>

 $('#dataTables').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"iDisplayLength": 25,
			"fnServerParams": function ( aoData ) {
				aoData.push( { "name": "cabang", "value": $('#cabang').val() },
							{ "name": "divisi", "value": $('#divisi').val() });
			},
			"aoColumns": [				
				{"mData": "TAHUN" },
				{"mData": "KOTA" },
				{"mData": "NAMA_DIV" },
				{"mData": "NAMA" }
			],
			"sAjaxSource": "<?php echo base_url('thr_staff/json_data_master');?>"
		});	
$('#cabang').change(function(){
	if ($(this).val()==''){
		$('#divisi').find('option').remove().end().attr('disabled',true);
		$('#dataTables').dataTable().fnReloadAjax();
		return false;
	}
		
	$.ajax({
		url: "<?php echo base_url('employee/comboDivByCab'); ?>",
		dataType: 'json',
		type: 'POST',
		data: {cabang:$(this).val()},
		success: function(respon){
			$('#divisi').find('option').remove().end();
			if (respon.status==1){
				var item = respon.data;
				for (opt=0;opt<item.length;opt++){
					$('#divisi').append('<option value="'+item[opt].ID_DIV+'">'+item[opt].NAMA_DIV+'</option>');
				}
			}
			$('#divisi').attr('disabled',false);
			$('#dataTables').dataTable().fnReloadAjax();
		}
	});
}).trigger('change');

$('#divisi').change(function(){
    $('#dataTables').dataTable().fnReloadAjax();
});

$('#tahun').change(function(){
    $('#dataTables').dataTable().fnReloadAjax();
});
</script>