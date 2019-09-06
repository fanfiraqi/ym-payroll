<div class="control-group"><a href="javascript:void(0)" id="btcreate" class="btn btn-primary">Tambah </a></div>
<div id="errorHandler" class="alert alert-danger no-display"></div>
<br>
<div id="divformkel" class="no-display">
<?php echo form_open(null,array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">	
	<div class="col-md-8">
		<div class="panel panel-default card-view"><div class="panel-heading">SETTING ACUAN UANG MAKAN</div>
		<div class="panel-body">


			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label class="col-sm-4 control-label">Cabang</label>
						<div class="col-sm-8"><?=form_dropdown('cabang',$cabang,'','id="cabang" class="form-control"');?></div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label  class="col-sm-4 control-label">Nominal Per hari</label>
						<div class="col-sm-8"><?=form_input(array('name'=>'per_hari','id'=>'per_hari','class'=>'form-control','onkeypress'=>"return numericVal(this,event)", "onblur"=>"blurObj(this)","onclick"=>"clickObj(this)", 'value'=>0));?>	</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label  class="col-sm-4 control-label">Nominal Per bulan</label>
						<div class="col-sm-8"><?=form_input(array('name'=>'per_bulan','id'=>'per_bulan','onkeypress'=>"return numericVal(this,event)", "onblur"=>"blurObj(this)","onclick"=>"clickObj(this)",'class'=>'form-control', 'value'=>0));?>	</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label class="col-sm-4 control-label">Status</label>
						<div class="col-sm-8"><?=form_dropdown('status',array('1'=>'Aktif','0'=>'Tidak Aktif'),'','id="status" class="form-control"');?>	</div>
					</div>
				</div>
			</div>


			<div class="row">
				<div class="col-md-8">
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

</div><hr />
<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			<label for="cabang" class="col-sm-4 control-label">Cabang</label>
			<div class="col-sm-8"><?php echo form_dropdown('cabang_filter',$cabang,'','id="cabang_filter" class="form-control" ');?>
				
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
				<th>CABANG</th>				
				<th>NOMINAL PER HARI</th>		
				<th>NOMINAL PER BULAN</th>
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
				aoData.push( { "name": "cabang", "value": $('#cabang_filter').val() } )
			},
			"aoColumns": [
				{"mData": "ID" },							
				{"mData": "CABANG", "sortable":false },	
				{"mData": "PER_HARI" },
				{"mData": "PER_BULAN" },			
				{"mData": "ISACTIVE" },
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('master_payroll/json_data_uang_makan');?>"
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
$('#cabang_filter').change(function(){
    $('#dataTables').dataTable().fnReloadAjax();
});
function changeStat(idx,  str, sts){	//nik, nama
			var pilih=confirm('Apakah data acuan uang makan cabang  '+str+ ' akan diubah status ?');
			if (pilih==true) {
					$.ajax({
					type	: "POST",
					url		: "<?php echo base_url('master_payroll/changeStat');?>",
					data	: "idx="+idx+"&nmtable=mst_acuan_makan"+"&field=id"+"&status="+sts,
					timeout	: 3000,  
					success	: function(res){
						//alert(res);
						alert("data berhasil diubah statusnya");
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
			url: '<?php echo base_url('master_payroll/editThis');?>',
			data: "id="+id+"&tabel=mst_acuan_makan"+"&field=id",
			dataType: 'json',
			success: function(msg) {
				
				if(msg.status =='success'){
					console.log(msg.data);
					
					$('#per_hari').val(msg.data.per_hari);
					$('#cabang').val(msg.data.id_cabang);
					$('#per_bulan').val(msg.data.per_bulan);
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
			url: "<?php echo base_url('master_payroll/saveData_uangMakan');?>",
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				 $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data berhasil disimpan.','success',1000);
					$("#divformkel").fadeSlide("hide");
					$('#dataTables').dataTable().fnReloadAjax();
					//window.location.reload();				
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