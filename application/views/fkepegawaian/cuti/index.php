<p><?php echo anchor('cuti/create','Permohonan Ijin',array('id'=>'btsubmit','class'=>'btn btn-primary'));?> </p>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="dataTables">
		<thead>
			<tr>
				<th>No Permohonan</th>
				<th>Tanggal Pemohonan</th>
				<th>NIK</th>
				<th>Nama</th>
				<th>Cabang/Div/Jab</th>
				<th>Tanggal Ijin</th>
				<th>Keterangan</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
</div>
<!-- /.table-responsive -->
<script>
    $(document).ready(function() {
        $('#dataTables').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"iDisplayLength": 25,
			"aoColumns": [
				{"mData": "NO_TRANS" },
				{"mData": "TGL_TRANS" },
				{"mData": "NIK" },
				{"mData": "NAMA" },
				{"mData": "CABANG", "sortable":false },
				{"mData": "TGL_IJIN", "sortable":false},
				{"mData": "KETERANGAN", "sortable":false},
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('cuti/json_data');?>"
		});
    });
	setInterval(function(){$('#dataTables').dataTable().fnReloadAjax();},60000);
</script>