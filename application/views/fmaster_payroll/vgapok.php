<div class="control-group"><a href="javascript:void(0)" id="btcreate" class="btn btn-primary">Tambah </a></div>
<div id="errorHandler" class="alert alert-danger no-display"></div>
<br>
<div id="divformkel" class="no-display">
<?php echo form_open(null,array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">	
	<div class="col-md-8">
		<div class="panel panel-default card-view"><div class="panel-heading">DATA MASTER GAJI POKOK</div>
		<div class="panel-body">			
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label class="col-sm-4 control-label">Grade</label>
						<div class="col-sm-8"><?=form_dropdown('grade_cabang',$grade,'','id="grade_cabang" class="form-control"');?></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label class="col-sm-4 control-label">Jabatan</label>
						<div class="col-sm-8"><?=form_dropdown('id_jabatan',$jabatan,'','id="id_jabatan" class="form-control"');?></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group form-inline"><label class="col-sm-4 control-label">Kolom Lama Kerja</label>
						<div class="col-sm-8">
						<?=form_dropdown('kelompok_lama',array("PRA"=>"PRA KARYAWAN", "A"=>"A", "B"=>"B", "C"=>"C", "D"=>"D"),'','id="kelompok_lama" class="form-control"');?>&nbsp;
						<?php 
							$var=array("3 Bulan"=>"3 Bulan", 
							"0-1 th"=>"0-1 th",
							"2-3 th"=>"2-3 th",
							"4-5 th"=>"4-5 th",
							"6-7 th"=>"6-7 th",
							"8-9 th"=>"8-9 th",
							"12-13 th"=>"12-13 th",
							"14-15 th"=>"14-15 th",
							"16-17 th"=>"16-17 th",
							"18-19 th"=>"18-19 th",
							"20-21 th"=>"20-21 th",
							"22-23 th"=>"22-23 th",
							);
						echo form_dropdown('label_lama',$var,'','id="label_lama" class="form-control"');?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label  class="col-sm-4 control-label">Batas Awal Lama Kerja (Bulan) </label>
						<div class="col-sm-8"><?=form_input(array('name'=>'lama_kerja_awal','id'=>'lama_kerja_awal','class'=>'form-control', 'value'=>0));?>	</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label  class="col-sm-4 control-label">Batas Akhir Lama Kerja (Bulan) </label>
						<div class="col-sm-8"><?=form_input(array('name'=>'lama_kerja_akhir','id'=>'lama_kerja_akhir','class'=>'form-control', 'value'=>0));?>	</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label  class="col-sm-4 control-label">Nominal Gaji Pokok</label>
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

</div><hr />
<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			<label for="cabang" class="col-sm-4 control-label">Grade Cabang</label>
			<div class="col-sm-8"><?php echo form_dropdown('grade_cabang_filter',$grade,'','id="grade_cabang_filter" class="form-control" ');?>
				
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
				<th>GRADE</th>				
				<th>JABATAN</th>				
				<th>LAMA KERJA</th>				
				<th>BATAS AWAL (BLN)</th>				
				<th>BATAS AKHIR (BLN)</th>				
				<th>NOMINAL GAPOK</th>				
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
				aoData.push( { "name": "grade_cabang", "value": $('#grade_cabang_filter').val() } )
			},
			"aoColumns": [
				{"mData": "ID" },
				{"mData": "GRADE_CABANG" },			
				{"mData": "ID_JABATAN" },			
				{"mData": "LABEL_LAMA" },			
				{"mData": "LAMA_KERJA_AWAL" },			
				{"mData": "LAMA_KERJA_AKHIR" },			
				{"mData": "NOMINAL" },			
				{"mData": "ISACTIVE" },
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('master_payroll/json_data_gapok');?>"
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
$('#grade_cabang_filter').change(function(){
    $('#dataTables').dataTable().fnReloadAjax();
});
function changeStat(idx,  str, sts){	//nik, nama
			var pilih=confirm('Apakah data gaji pokok   '+str+ ' akan diubah status ?');
			if (pilih==true) {
					$.ajax({
					type	: "POST",
					url		: "<?php echo base_url('master_payroll/changeStat');?>",
					data	: "idx="+idx+"&nmtable=mst_gapok"+"&field=id"+"&status="+sts,
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
			data: "id="+id+"&tabel=mst_gapok"+"&field=id",
			dataType: 'json',
			success: function(msg) {
				
				if(msg.status =='success'){
					console.log(msg.data);
					
					$('#grade_cabang').val(msg.data.grade_cabang);
					$('#id_jabatan').val(msg.data.id_jabatan);
					$('#kelompok_lama').val(msg.data.kelompok_lama);
					$('#label_lama').val(msg.data.label_lama);
					$('#lama_kerja_awal').val(msg.data.lama_kerja_awal);
					$('#lama_kerja_akhir').val(msg.data.lama_kerja_akhir);
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
			url: "<?php echo base_url('master_payroll/saveData_gapok');?>",
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