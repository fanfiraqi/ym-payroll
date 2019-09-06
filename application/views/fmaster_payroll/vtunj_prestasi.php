<div class="control-group"><a href="javascript:void(0)" id="btcreate" class="btn btn-primary">Tambah </a></div>
<div id="errorHandler" class="alert alert-danger no-display"></div>
<br>
<div id="divformkel" class="no-display">
<?php echo form_open(null,array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">	
	<div class="col-md-8">
		<div class="panel panel-default card-view"><div class="panel-heading">MASTER DATA TUNJANGAN PRESTASI ZISCO</div>
		<div class="panel-body">

			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label  class="col-sm-4 control-label">Batas Bawah</label>
						<div class="col-sm-8"><?=form_input(array('name'=>'batas_bawah','id'=>'batas_bawah','class'=>'form-control','onkeypress'=>"return numericVal(this,event)", "onblur"=>"blurObj(this)","onclick"=>"clickObj(this)", 'value'=>0));?>	</div>
					</div>
				</div>
			</div>	
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label  class="col-sm-4 control-label">Batas Atas</label>
						<div class="col-sm-8"><?=form_input(array('name'=>'batas_atas','id'=>'batas_atas','class'=>'form-control','onkeypress'=>"return numericVal(this,event)", "onblur"=>"blurObj(this)","onclick"=>"clickObj(this)", 'value'=>0));?>	</div>
					</div>
				</div>
			</div>	
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label  class="col-sm-4 control-label">Nominal Tunjangan</label>
						<div class="col-sm-8"><?=form_input(array('name'=>'nominal','id'=>'nominal','class'=>'form-control','onkeypress'=>"return numericVal(this,event)", "onblur"=>"blurObj(this)","onclick"=>"clickObj(this)", 'value'=>0));?>	</div>
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

</div>
<hr />
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="dataTables">
		<thead>
			<tr>
				<th>ID</th>
				<th>BATAS BAWAH</th>				
				<th>BATAS ATAS</th>				
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
			"aoColumns": [
				{"mData": "ID" },
				{"mData": "BATAS_BAWAH" },			
				{"mData": "BATAS_ATAS" },			
				{"mData": "NOMINAL" },			
				{"mData": "ISACTIVE" },
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('master_payroll/json_data_prestasi');?>"
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
			var pilih=confirm('Apakah data Tunjangan Prestasi '+str+ ' akan diubah status ?');
			if (pilih==true) {
					$.ajax({
					type	: "POST",
					url		: "<?php echo base_url('master_payroll/changeStat');?>",
					data	: "idx="+idx+"&nmtable=mst_tunj_prestasi"+"&field=id"+"&status="+sts,
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
			data: "id="+id+"&tabel=mst_tunj_prestasi"+"&field=id",
			dataType: 'json',
			success: function(msg) {
				
				if(msg.status =='success'){
					console.log(msg.data);
					
					$('#batas_bawah').val(msg.data.batas_bawah);
					$('#batas_atas').val(msg.data.batas_atas);
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
			url: "<?php echo base_url('master_payroll/saveData_prestasi');?>",
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