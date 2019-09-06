<div class="control-group"><a href="javascript:void(0)" id="btcreate" class="btn btn-primary">Set Nominal Baru</a></div>
<div id="errorHandler" class="alert alert-danger no-display"></div> 
<br>
<div id="divformkel" class="no-display">
<?php echo form_open(null,array('class'=>'form-horizontal','id'=>'myform'));?>

<div class="row">	
	<div class="col-md-12">
		<div class="panel panel-default card-view"><div class="panel-heading">DATA SET VARIABEL</div>
		<div class="panel-body">

<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label class="col-sm-4 control-label">Pilih Komponen</label>
			<div class="col-sm-8"><?=form_dropdown('komponen',$komponen,'','id="komponen" class="form-control"');?></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label  class="col-sm-4 control-label">Pilih Variabel</label>
			<div class="col-sm-8"><?=form_dropdown('variabel',$variabel,'','id="variabel" class="form-control"');?>	</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label  class="col-sm-4 control-label">Satuan</label>
			<div class="col-sm-8"><?=form_input(array('name'=>'satuan','id'=>'satuan','class'=>'form-control'));?>	</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label  class="col-sm-4 control-label">Nominal</label>
			<div class="col-sm-8"><?=form_input(array('name'=>'nominal','id'=>'nominal','class'=>'form-control'));?>	</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label class="col-sm-4 control-label">STATUS</label>
			<div class="col-sm-8"><?=form_dropdown('status',array('1'=>'Aktif','0'=>'Tidak Aktif'),'','id="status" class="form-control"');?>	</div>
		</div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-md-6">
		<input type="hidden" name="id" id="id">
		<input type="hidden" name="state" id="state" value="add">
		<input type="button" class="btn btn-primary" id="btsimpan" value="Simpan">
		<button type="button" class="btn btn-default" id="btcancel">Batal</button>
	</div>
</div>


</div>
</div>
</div>
</div>

<?php echo form_close();?>
</div>
<hr />
<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			<label for="cabang" class="col-sm-4 control-label">Komponen Gaji </label>
			<div class="col-sm-8"><?php echo form_dropdown('komponenfilter',$komponen,'','id="komponenfilter" class="form-control" ');?>
				
			</div>
		</div>
	</div>	
	<div class="col-sm-6">
		<div class="form-group">
			<label for="cabang" class="col-sm-4 control-label">Variabel</label>
			<div class="col-sm-8"><?php echo form_dropdown('variabelfilter',$variabel,'','id="variabelfilter" class="form-control" ');?>
				
			</div>
		</div>
	</div>	
</div>
<hr />
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="dataTables">
		<thead>
			<tr>
				<th>ID</th>
				<th>KOMPONEN</th>				
				<th>NAMA VARIABEL</th>				
				<th>SATUAN</th>				
				<th>NOMINAL</th>				
				<th>STATUS</th>
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
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"fnServerParams": function ( aoData ) {
				aoData.push( { "name": "komponen", "value": $('#komponenfilter').val() },  { "name": "variabel", "value": $('#variabelfilter').val() } )
			},
			"aoColumns": [
				{"mData": "ID" },
				{"mData": "KOMPONEN" },			
				{"mData": "VARIABEL" },			
				{"mData": "SATUAN" },			
				{"mData": "NOMINAL" },			
				{"mData": "ISACTIVE" },
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('komponengaji/json_data_set_var');?>"
		});

		

    });
	
	$('#btcreate').click(function(){
		$("#divformkel").fadeSlide("show");
		$('#state').val('add');
		$('#myform').reset();
		
		
	});
	$('#btcancel').click(function(){
		$("#divformkel").fadeSlide("hide");
	});
$('#komponen').change(function(){
    if ($(this).val()==''){
		$('#variabel').find('option').remove().end().attr('disabled',true);
		return false;
	}
		
	$.ajax({
		url: "<?php echo base_url('komponengaji/fillComboVar'); ?>",
		dataType: 'json',
		type: 'POST',
		data: {komponen:$(this).val()},
		success: function(respon){
			$('#variabel').find('option').remove().end();
			if (respon.status==1){
				var item = respon.data;
				for (opt=0;opt<item.length;opt++){
					$('#variabel').append('<option value="'+item[opt].id+'">'+item[opt].nama+'</option>');
				}
			}
			$('#variabel').attr('disabled',false);
		}
	});
}).trigger('change');

$('#komponenfilter').change(function(){
    $('#dataTables').dataTable().fnReloadAjax();
});
$('#variabelfilter').change(function(){
    $('#dataTables').dataTable().fnReloadAjax();
});
function delThis(idx,  str){	//nik, nama
			var pilih=confirm('Apakah data pelanggaran  '+str+ ' akan dihapus ?');
			if (pilih==true) {
					$.ajax({
					type	: "POST",
					url		: "<?php echo base_url('komponengaji/delThis');?>",
					data	: "idx="+idx+"&proses=mst_set_kom_var"+"&field=id",
					timeout	: 3000,  
					success	: function(res){
						//alert(res);
						alert("data berhasil dihapus");
						window.location.reload();
						}
				});
			}
		}

function editThis(obj){
		var id = $(obj).attr('data-id');	//id as nik
		$('#myform input[name="state"]').val(id);		
		$('#lbltitle').text('Edit Data');
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('komponengaji/editThis_set_var');?>',
			data: {
				id:id
			},
			dataType: 'json',
			success: function(msg) {
				
				if(msg.status =='success'){
					console.log(msg.data);
					
					$('#komponen').val(msg.data.id_komp);
					$('#variabel').val(msg.data.id_var);
					$('#satuan').val(msg.data.satuan);
					$('#nominal').val(msg.data.nominal);				
					$('#status').val(msg.data.isactive);							
					$("#divformkel").fadeSlide("show");
					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$().showMessage('Terjadi kesalahan.<br />'+	textStatus + ' - ' + errorThrown ,'danger',2000);
			},
			cache: false
		});
	}
	

	$('#btsimpan').click(function(){		
		var form_data = $('#myform').serialize();
		
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url('komponengaji/saveData_set_var');?>",
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				 $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data berhasil disimpan.','success',1000);
					$("#divformkel").fadeSlide("hide");
					$('#dataTables').dataTable().fnReloadAjax();
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