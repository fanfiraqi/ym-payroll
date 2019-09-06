<p><?php echo anchor('absensi/upload','Upload Data Absensi',array('id'=>'btsubmit','class'=>'btn btn-primary'));?> </p>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="dataTables-example">
		<thead>
			<tr>
				<th>CABANG</th>
				<th>NIK</th>
				<th>NAMA</th>
				<th>TANGGAL</th>
				<th>JAM_MASUK</th>				
				<th>SCAN_MASUK</th>
				<th>TERLAMBAT</th>				
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
</div>
<!-- /.table-responsive -->
<script>
    $(document).ready(function() {
        $('#dataTables-example').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"iDisplayLength": 25,
			"aoColumns": [
				{"mData": "KOTA"},
				{"mData": "NIK"},
				{"mData": "NAMA"},
				{"mData": "TANGGAL"},
				{"mData": "JAM_MASUK"},
				{"mData": "SCAN_MASUK"},
				{"mData": "TERLAMBAT"}

			],
			"sAjaxSource": "<?php echo base_url('absensi/json_data');?>"
		});
    });
    </script>