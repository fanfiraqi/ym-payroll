<div class="control-group"><a href="javascript:void(0)" id="btcreate" class="btn btn-primary">Tambah Komponen</a></div>
<div id="errorHandler" class="alert alert-danger no-display"></div>
<br>
<div id="divformkel" class="no-display">
<?php echo form_open('komponengaji/create',array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">	
	<div class="col-md-12">
		<div class="panel panel-default card-view"><div class="panel-heading">DATA KOMPONEN GAJI</div>
		<div class="panel-body">


<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label class="col-sm-4 control-label">NAMA KOMPONEN</label>
			<div class="col-sm-8"><?=form_input(array('name'=>'nama','id'=>'nama','class'=>'form-control'));?></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label  class="col-sm-4 control-label">KETERANGAN</label>
			<div class="col-sm-8"><?=form_textarea(array('name'=>'keterangan','id'=>'keterangan','class'=>'form-control'));?>	</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label class="col-sm-4 control-label">FUNGSI</label>
			<div class="col-sm-8"><?=form_dropdown('fungsi',array('+'=>'[ + ] Tunjangan/Insentif','-'=>'[ - ] Potongan'),'','id="fungsi" class="form-control"');?>	</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-inline"><label  class="col-sm-4 control-label">KELOMPOK KOMPONEN</label>
			<div class="col-sm-8">
			<div class="checkbox checkbox-primary"><input type="checkbox" name="ck_staff" id="ck_staff" ><label for="ck_staff">STAFF</label></div>
			<div class="checkbox checkbox-primary"><input type="checkbox" name="ck_zisco" id="ck_zisco"><label for="ck_zisco">ZISCO</label></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label class="col-sm-4 control-label">TIPE PENYIMPANAN DATA</label>
			<div class="col-sm-8"><?=form_dropdown('tipe_simpan',array('Tabel'=>'Tabel','Variabel'=>'Variabel','Lain-lain'=>'Lain-lain/Entrian'),'','id="tipe_simpan" class="form-control"');?>	</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label class="col-sm-4 control-label">KOMPONEN THR</label>
			<div class="col-sm-8"><?=form_dropdown('is_thr',array('1'=>'Ya','0'=>'Tidak'),'','id="is_thr" class="form-control"');?>	</div>
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
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover" id="dataTables-example">
		<thead>
			<tr>
				<th>ID</th>
				<th>NAMA KOMPONEN</th>				
				<th>KETERANGAN</th>				
				<th>FUNGSI</th>				
				<th>STAFF</th>				
				<th>ZISCO</th>				
				<th>KOMPONEN THR</th>				
				<th>TIPE SIMPAN</th>				
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
        $('#dataTables-example').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"iDisplayLength": 25,
			"aoColumns": [
				{"mData": "ID" },
				{"mData": "NAMA" },			
				{"mData": "KETERANGAN" },			
				{"mData": "FUNGSI" },			
				{"mData": "STAFF" },			
				{"mData": "ZISCO" },			
				{"mData": "IS_THR" },			
				{"mData": "TIPE_SIMPAN" },			
				{"mData": "ISACTIVE" },
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('komponengaji/json_data');?>"
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

function delThis(idx,  str){	//nik, nama
			var pilih=confirm('Apakah data pelanggaran  '+str+ ' akan dihapus ?');
			if (pilih==true) {
					$.ajax({
					type	: "POST",
					url		: "<?php echo base_url('komponengaji/delThis');?>",
					data	: "idx="+idx+"&proses=mst_komp_gaji"+"&field=id",
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
			url: '<?php echo base_url('komponengaji/editThis');?>',
			data: {
				id:id
			},
			dataType: 'json',
			success: function(msg) {
				
				if(msg.status =='success'){
					console.log(msg.data);
					
					$('#nama').val(msg.data.NAMA);
					$('#keterangan').val(msg.data.KETERANGAN);
					$('#fungsi').val(msg.data.FLAG);
					if (msg.data.IS_STAFF=='on')	{
						$('#ck_staff').prop('checked',true);      
					}else{
						$('#ck_staff').prop('checked',false);  
					}
					if (msg.data.IS_ZISCO=='on')	{
						$('#ck_zisco').prop('checked',true);      
					}else{
						$('#ck_zisco').prop('checked',false);  
					}
					$('#status').val(msg.data.ISACTIVE);
							
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
			url: "<?php echo base_url('komponengaji/saveData');?>",
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				 $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data berhasil disimpan.','success',1000);
					$("#divformkel").fadeSlide("hide");
					$('#dataTables-list').dataTable().fnReloadAjax();
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
	function ubahStatus(idx, sts){
			var pilih=confirm('Apakah data Komponen Gaji/Tunjangan Staff '+idx+ ' akan '+(sts=='1'?"dinon-aktifkan":"diaktifkan")+' ?');
			if (pilih==true) {
					$.ajax({
					type	: "POST",
					url		: "<?php echo base_url('komponengaji/ubahStatus');?>",
					data	: "idx="+idx+"&status="+sts,
					timeout	: 3000,  
					success	: function(res){
						//alert(res);
						alert("data berhasil "+(sts=='1'?"dinon-aktifkan":"diaktifkan"));
						window.location.reload();
						}
					
				});
			}
		}
    </script>