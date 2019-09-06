<div id="errorHandler" class="alert alert-danger no-display"></div>

<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			<label for="cabang" class="col-sm-4 control-label">Tipe Donasi</label>
			<div class="col-sm-8">
				<?php
					echo form_dropdown('tipe',$tipe,'','id="tipe" class="form-control" ');
			
				?>
			</div>
		</div>
	</div>	
</div>
<hr/>
<?php echo form_open(null,array('class'=>'form-horizontal','id'=>'frmSetAkun'));?>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="dataTables-list">
		<thead>
			<tr>
				<th>ID</th>
				<th>TIPE</th>
				<th>JENIS DONASI</th>
				<th>KOLOM PENGGAJIAN</th>
				<th>KETERANGAN</th>
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
</div><br>
<div class="row">
	<div class="col-md-12">
		<input type="button" class="btn btn-primary" id="btSimpan" value="Simpan">	
		
	</div>
</div>
</form>
<!-- /.table-responsive -->
<script>
    $(document).ready(function() {
        $('#dataTables-list').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"iDisplayLength": 25,
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"fnServerParams": function ( aoData ) {
				aoData.push({ "name": "tipe", "value": $('#tipe').val()} 
				);
			},
			"aoColumns": [
				{"mData": "ID" },
				{"mData": "TIPE"},
				{"mData": "TITLE"},
				{"mData": "PAYROLL_COLUMN"},				
				{"mData": "NOTES"}
			],
			"sAjaxSource": "<?php echo base_url('komponengaji/json_data_donasi');?>"
		});
    });

	$('#tipe').change(function(){
		$('#dataTables-list').dataTable().fnReloadAjax();

	});


	$('#btSimpan').click(function(){		
		var form_data = $('#frmSetAkun').serialize();
		$().showMessage('Sedang diproses.. Harap tunggu..');
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url('komponengaji/saveAkunDonasi');?>",
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				 $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data berhasil disimpan.','success',1000);
					$('#dataTables-list').dataTable().fnReloadAjax();
					window.location.reload();				
				} else{
					bootbox.alert("Terjadi kesalahan. "+ msg.errormsg+". Data gagal disimpan.");				
					$("#errorHandler").html(msg.errormsg).show();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
	});

    </script>