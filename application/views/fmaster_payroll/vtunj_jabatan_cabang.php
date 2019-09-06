<div class="control-group"><a href="javascript:void(0)" id="btcreate_cab" class="btn btn-primary">Tambah </a></div>
<div id="errorHandler_cab" class="alert alert-danger no-display"></div>
<br>
<div id="divformkel_cab" class="no-display">
<?php echo form_open(null,array('class'=>'form-horizontal','id'=>'myform_cab'));?>
<div class="row">	
	<div class="col-md-10">
		<div class="panel panel-default card-view"><div class="panel-heading">DATA TUNJANGAN JABATAN CABANG</div>
		<div class="panel-body">


			<div class="row">
				<div class="col-md-10">
					<div class="form-group"><label  class="col-sm-5 control-label">Level Jabatan</label>
						<div class="col-sm-7"><?=form_input(array('name'=>'jabatan','id'=>'jabatan','class'=>'form-control', 'value'=>0));?>	</div>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-10">
					<div class="form-group"><label  class="col-sm-5 control-label">Tunjangan Jabatan  </label>
						<div class="col-sm-7"><?=form_input(array('name'=>'nominal','id'=>'nominal','class'=>'form-control','onkeypress'=>"return numericVal(this,event)", "onblur"=>"blurObj(this)","onclick"=>"clickObj(this)", 'value'=>0));?>	</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-10">
					<div class="form-group"><label class="col-sm-5 control-label">Status</label>
						<div class="col-sm-7"><?=form_dropdown('status_cab',array('1'=>'Aktif','0'=>'Tidak Aktif'),'','id="status_cab" class="form-control"');?>	</div>
					</div>
				</div>
			</div>


			<div class="row">
				<div class="col-md-10">
					<input type="hidden" name="id_cab" id="id_cab">
					<input type="hidden" name="state_cab" id="state_cab" value="add">
					<input type="button" class="btn btn-primary" id="btsimpan_cab" value="Simpan">
					<button type="button" class="btn btn-default" id="btcancel_cab">Batal</button>
				</div>
			</div>


		</div>
	</div>
	
</div>
</div>
<?php echo form_close();?>

</div>
<hr />
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="dataTables_cab">
		<thead>
			<tr>
				<th>ID</th>
				<th>JABATAN</th>				
				<th>TUNJANGAN JABATAN</th>				
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
        $('#dataTables_cab').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"iDisplayLength": 25,
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",			
			"aoColumns": [
				{"mData": "ID" },
				{"mData": "JABATAN" },			
				{"mData": "NOMINAL"},			
				{"mData": "ISACTIVE" },
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('master_payroll/json_data_tunj_jab_cab');?>"
		});

		

    });
	
	$('#btcreate_cab').click(function(){
		$("#divformkel_cab").fadeSlide("show");
		$('#state_cab').val('add');
		$('#myform_cab').reset();		
		
	});
	$('#btcancel_cab').click(function(){
		$("#divformkel_cab").fadeSlide("hide");
	});

function changeStat_cab(idx,  str, sts){	//nik, nama
			var pilih=confirm('Apakah data tunjangan jabatan '+str+ ' akan diubah status ?');
			if (pilih==true) {
					$.ajax({
					type	: "POST",
					url		: "<?php echo base_url('master_payroll/changeStat');?>",
					data	: "idx="+idx+"&nmtable=mst_tunj_jabatan_cabang"+"&field=id"+"&status="+sts,
					timeout	: 3000,  
					success	: function(res){
						//alert(res);
						alert("data berhasil diubah statusnya");
						window.location.reload();
						}
				});
			}
		}

function editThis_cab(obj){
		var id = $(obj).attr('data-id');	//id as nik
		$('#myform_cab input[name="state_cab"]').val(id);		
		$('#lbltitle').text('Edit Data');
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('master_payroll/editThis');?>',
			data: "id="+id+"&tabel=mst_tunj_jabatan_cabang"+"&field=id",
			dataType: 'json',
			success: function(msg) {
				
				if(msg.status =='success'){
					console.log(msg.data);
					
					$('#jabatan').val(msg.data.jabatan);
					$('#nominal').val(msg.data.level_jabatan);
					$('#status_cab').val(msg.data.isactive);
							
					$("#divformkel_cab").fadeSlide("show");
					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$().showMessage('Terjadi kesalahan.<br />'+	textStatus + ' - ' + errorThrown ,'danger',2000);
			},
			cache: false
		});
	}
	

	$('#btsimpan_cab').click(function(){		
		var form_data = $('#myform_cab').serialize();
		
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url('master_payroll/saveData_tunj_jab_cabang');?>",
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				 $("#errorHandler_cab").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data berhasil disimpan.','success',1000);
					$("#divformkel_cab").fadeSlide("hide");
					$('#dataTables_cab').dataTable().fnReloadAjax();
					//window.location.reload();				
				} else{
					bootbox.alert("Terjadi kesalahan. "+ msg.errormsg+". Data gagal disimpan.");				
					$("#errorHandler_cab").html(msg.errormsg).show();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
	});
	
    </script>