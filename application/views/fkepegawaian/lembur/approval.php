<!-- <p><?php echo anchor('lembur/create','Buat Permohonan Lembur',array('id'=>'btsubmit','class'=>'btn btn-primary'));?> </p> -->
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="dataTables">
		<thead>
			<tr>
				<th>No Permohonan</th>
				<th>Tanggal Pemohonan</th>
				<th>NIK</th>
				<th>Nama</th>
				<th>Cabang/Div/Jab</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
</div>
<!-- /.table-responsive -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mnotrans" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="mnotrans">Modal title</h4>
      </div>
      <div class="modal-body" id="mcontent">
        ...
      </div>
      <div class="modal-footer">       
		<a href="javascript:void(0)" data-url="<?php echo base_url('lembur/approve');?>" data-id="" onclick="approve(this)" data-base="<?php echo base_url();?>" class="btn btn-success" id="mbtappv"><i class="fa fa-check" title="Setuju"></i> Setuju</a>
		<a href="javascript:void(0)" data-url="<?php echo base_url('lembur/approve');?>" data-id="" onclick="denied(this)" data-base="<?php echo base_url();?>" class="btn btn-danger" id="mbtden"><i class="fa fa-check" title="Tolak"></i> Tolak</a>
		 <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

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
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('lembur/appv_data');?>"
		});
    });

function view(obj){
	var notrans = $(obj).attr('data-id');
			$.ajax({
				type: 'GET',
				url: $(obj).attr('data-url'),
				data: {view:'true',notrans:notrans},
				dataType: 'html',
				success: function(msg) {
					$('#mnotrans').html('No. Dokumen Lembur : '+ notrans);
					$('#mcontent').html(msg);
					$('#mbtappv').attr('data-id',notrans);
					$('#mbtden').attr('data-id',notrans);
					$('#myModal').modal({'show':true,backdrop: 'static'});
				},
				complete: function(msg){
					$('html').animate({
						scrollTop: $('#page-wrapper').offset().top
					}, 500);
					
					return false;
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					$().showMessage('Terjadi kesalahan.<br />'+	textStatus + ' - ' + errorThrown ,'danger',2000);
				},
				cache: false
			});
}	
</script>