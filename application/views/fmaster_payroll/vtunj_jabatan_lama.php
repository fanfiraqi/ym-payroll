<div class="control-group"><a href="javascript:void(0)" id="btcreate" class="btn btn-primary">Tambah </a></div>
<div id="errorHandler" class="alert alert-danger no-display"></div>
<br>
<div id="divformkel" class="no-display">
<?php echo form_open(null,array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">	
	<div class="col-md-10">
		<div class="panel panel-default card-view"><div class="panel-heading">SETTING DATA TUNJANGAN JABATAN</div>
		<div class="panel-body">


			<div class="row">
				<div class="col-md-10">
					<div class="form-group"><label class="col-sm-5 control-label">Level Jabatan</label>
						<div class="col-sm-7"><?=form_dropdown('level_jabatan',$level_jabatan,'','id="level_jabatan" class="form-control"');?></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-10">
					<div class="form-group"><label class="col-sm-5 control-label">Tipe </label>
						<div class="col-sm-7"><?=form_dropdown('tipe_level',array('Kepala Cabang'=>'Kepala Cabang', 'Level Jabatan'=>'Level Jabatan'),'','id="tipe_level" class="form-control"');?></div>
					</div>
				</div>
			</div>

			<div class="row">
							<div class="col-md-10">
								<div class="form-group"><label class="col-sm-5 control-label">Grade</label>
									<div class="col-sm-7"><?=form_dropdown('grade',$grade,'','id="grade" class="form-control"');?></div>
								</div>
							</div>
			</div>
			<div class="row">
				<div class="col-md-10">
					<div class="form-group"><label  class="col-sm-5 control-label">Nominal Direktorat/Cabang</label>
						<div class="col-sm-7"><?=form_input(array('name'=>'nominal_cabang','id'=>'nominal_cabang','class'=>'form-control','onkeypress'=>"return numericVal(this,event)", "onblur"=>"blurObj(this)","onclick"=>"clickObj(this)", 'value'=>0));?>	</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-10">
					<div class="form-group"><label  class="col-sm-5 control-label">Nominal Pusat</label>
						<div class="col-sm-7"><?=form_input(array('name'=>'nominal_pusat','id'=>'nominal_pusat','class'=>'form-control','onkeypress'=>"return numericVal(this,event)", "onblur"=>"blurObj(this)","onclick"=>"clickObj(this)", 'value'=>0));?>	</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-10">
					<div class="form-group"><label class="col-sm-5 control-label">Status</label>
						<div class="col-sm-7"><?=form_dropdown('status',array('1'=>'Aktif','0'=>'Tidak Aktif'),'','id="status" class="form-control"');?>	</div>
					</div>
				</div>
			</div>


			<div class="row">
				<div class="col-md-10">
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

</div
<hr />
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="dataTables">
		<thead>
			<tr>
				<th>ID</th>
				<th>LEVEL JABATAN</th>				
				<th>TIPE </th>				
				<th>GRADE</th>				
				<th>DIREKTORAT/CABANG</th>				
				<th>PUSAT</th>				
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
			"aoColumns": [
				{"mData": "ID" },
				{"mData": "LEVEL_JABATAN" },			
				{"mData": "JENIS"},			
				{"mData": "GRADE" },			
				{"mData": "NOMINAL_CABANG" },			
				{"mData": "NOMINAL_PUSAT" },			
				{"mData": "ISACTIVE" },
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('master_payroll/json_data_tunj_jab');?>"
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

function changeStat(idx,  str, sts){	//nik, nama
			var pilih=confirm('Apakah data tunjangan jabatan'+str+ ' akan diubah status ?');
			if (pilih==true) {
					$.ajax({
					type	: "POST",
					url		: "<?php echo base_url('master_payroll/changeStat');?>",
					data	: "idx="+idx+"&nmtable=mst_tunj_jabatan"+"&field=id"+"&status="+sts,
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
			data: "id="+id+"&tabel=mst_tunj_jabatan"+"&field=id",
			dataType: 'json',
			success: function(msg) {
				
				if(msg.status =='success'){
					console.log(msg.data);
					
					$('#grade').val(msg.data.grade);
					$('#level_jabatan').val(msg.data.level_jabatan);
					$('#tipe_level').val(msg.data.jenis);
					$('#nominal_cabang').val(msg.data.nominal_cabang);
					$('#nominal_pusat').val(msg.data.nominal_pusat);
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
			url: "<?php echo base_url('master_payroll/saveData_tunj_jab');?>",
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