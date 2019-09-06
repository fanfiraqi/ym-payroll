<?php errorHandler();
echo form_open_multipart('setting/savingParams',array('class'=>'form-horizontal','id'=>'myformkel'));
?>
<div id="divformkel" class="no-display">
<div class="row">
	<div class="col-md-8">
		<div class="form-group"><label class="col-sm-4 control-label">Nama Lembaga</label>
			<div class="col-sm-8"><?=form_input(array('name'=>'txt1','id'=>'txt1','class'=>'form-control'));?></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-8">
		<div class="form-group"><label class="col-sm-4 control-label">Nomer Rekening Payroll</label>
			<div class="col-sm-8"><?=form_input(array('name'=>'norek','id'=>'norek','class'=>'form-control'));?></div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12"><input type="hidden" name="id" id="id">
	<input type="submit" class="btn btn-primary" id="btsimpankel" value="Simpan">
	<button type="button" class="btn btn-default" id="btcancelkel">Batal</button>
	</div>
</div> <hr/>

</div><!-- end modal -->

<?php echo form_close();?>  
<div class="table-header">Results for "Info"</div>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="tabelku">
		<thead>
			<tr>
				<th>ID</th>
				<th>Nama lembaga</th>
				<th>Nomer Rekening Payroll</th>
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
        $('#tabelku').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"iDisplayLength": 25,
			"aoColumns": [
				{"mData": "id" },
				{"mData": "value1" },
				{"mData": "value2" },
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('setting/json');?>"
		});
    });
$('#btcancelkel').click(function(){
		$("#divformkel").fadeSlide("hide");
	});

function editParams(obj){
		var id = $(obj).attr('data-id');
		
		$('#myformkel input[name="state"]').val(id);
		
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('setting/getParams');?>',
			data: {
				id:id
			},
			dataType: 'json',
			success: function(msg) {
				if(msg.status =='success'){
					console.log(msg.data);
					$('#id').val(msg.data.id);
					$('#txt1').val(msg.data.value1);								
					$('#norek').val(msg.data.value2);
					$("#divformkel").fadeSlide("show");					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$().showMessage('Terjadi kesalahan.<br />'+	textStatus + ' - ' + errorThrown ,'danger',2000);
			},
			cache: false
		});
	}

</script>