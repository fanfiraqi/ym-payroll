<!-- <p><?php echo anchor('cuti/create','Buat Permohonan',array('id'=>'btsubmit','class'=>'btn btn-primary'));?> </p> -->
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
			"sAjaxSource": "<?php echo base_url('cuti/appv_data');?>"
		});
    });
	
	function detail(obj){
		var id = $(obj).attr('data-id');
		$.ajax({
			url: "<?php echo base_url('cuti/view/'); ?>/"+id,
			dataType: 'html',
			type: 'POST',
			data: {ajax:'true'},
			success:
				function(data){
					bootbox.dialog({
					  message: data,
					  title: "Persetujuan Data",
					  buttons: {
						success: {
						  label: "Setuju",
						  className: "btn-success",
						  callback: function() {
							approve(obj);
						  }
						},
						button: {
						  label: "Tolak",
						  className: "btn-danger",
						  callback: function() {
							denied(obj);
						  }
						},
						main: {
						  label: "Kembali",
						  className: "btn-warning",
						  callback: function() {
							console.log("Primary button");
						  }
						}
					  }
					});
				}
		});
		
	}
</script>