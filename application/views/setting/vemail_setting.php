<div id="errorHandler" class="alert alert-danger no-display"></div>
<?php echo form_open(null,array('class'=>'form-horizontal','id'=>'myform'));?>

<div id="divformkel" class="no-display"> 
<div class="row">
	<div class="col-xs-12 col-sm-6 widget-container-col" id="widget-container-col-1">
        <div class="panel panel-default">
          <div class="panel-heading"> Form Setting Email-Sender</div>
          <div class="panel-body">           


<div class="row">
	<div class="col-md-8">
		<div class="form-group"><label class="col-sm-4 control-label">Email Host </label>
			<div class="col-sm-8"><?php echo form_input(array('name'=>'email_host','id'=>'email_host','class'=>'form-control ', 'value'=> $row->email_host));?>	</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="form-group"><label class="col-sm-4 control-label">Email Port </label>
			<div class="col-sm-8"><?php echo form_input(array('name'=>'email_port','id'=>'email_port','class'=>'form-control ', 'value'=> $row->email_port));?>	</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="form-group"><label class="col-sm-4 control-label">Email User </label>
			<div class="col-sm-8"><?php echo form_input(array('name'=>'email_user','id'=>'email_user','class'=>'form-control ', 'value'=> $row->email_user));?>	</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="form-group"><label class="col-sm-4 control-label">Email Password </label>
			<div class="col-sm-8"><?php echo form_input(array('name'=>'email_pass','id'=>'email_pass','class'=>'form-control ', 'value'=> $row->email_pass));?>	</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="form-group"><label class="col-sm-4 control-label">Email From </label>
			<div class="col-sm-8"><?php echo form_input(array('name'=>'email_from','id'=>'email_from','class'=>'form-control ', 'value'=> $row->email_from));?>	</div>
		</div>
	</div>
</div>			

<div class="row">
	<div class="col-md-8">
		<div class="form-group"><label class="col-sm-4 control-label">Email From Display </label>
			<div class="col-sm-8"><?php echo form_input(array('name'=>'email_from_name','id'=>'email_from_name','class'=>'form-control col-10', 'value'=> $row->email_from_name));?>	</div>
		</div>
	</div>
</div>			
			

			<label class="col-sm-4 control-label">&nbsp; </label>
			 <div class="form-actions ">
				<input type="hidden" name="id" id="id" value="1">
				<input type="hidden" name="state" id="state" value="1">
				<input type="button" class="btn btn-primary" id="btsimpan" value="Simpan">
				<button type="button" class="btn btn-default" id="btcancel">Batal</button>
            </div>
            
          </div>
      
      </div>
      </div>
    </div>
  </div>

</div> <!-- divformkel -->

<?php echo form_close();?>  

<div class="row">
	<div class="col-lg-12 card">          
		 <div class="widget-content nopadding">
			<table class="table table-bordered data-table" id="dataTables">
				<thead>
				<tr>
				<th>ID</th>
				<th>EMAIL HOST</th>				
				<th>EMAIL PORT</th>
				<th>EMAIL USER</th>
				<th>EMAIL FROM NAME</th>
				<th>Action</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
		</div>
	</div>	
</div>	
<!-- /.table-responsive -->





<script>
    $(document).ready(function() {
        $('#dataTables').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",			
			"bProcessing": true,
			"bServerSide": true,
			"iDisplayLength": 25,
			"aoColumns": [
				{"mData": "id" },
				{"mData": "email_host" },
				{"mData": "email_port" },
				{"mData": "email_user" },
				{"mData": "email_from_name" },
				{"mData": "ACTION", "sortable":false }
			],
			"sAjaxSource": "<?php echo base_url('setting/json_data_email');?>"
		});

		});
	

	$('#btcancel').click(function(){
		$("#divformkel").fadeSlide("hide");
	});
	
	$('#btsimpan').click(function(){		
		var form_data = $('#myform').serialize();
		
		$().showMessage('Sedang diproses.. Harap tunggu..');
		
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url('setting/saveData_email');?>",
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				 $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data Setting email sender berhasil disimpan.','success',1000);
					$("#divformkel").fadeSlide("hide");
					$('#dataTables').dataTable().fnReloadAjax();
					//window.location.reload();
				} else {
					bootbox.alert("Terjadi kesalahan. "+ msg.errormsg+". Data gagal disimpan.");
					//$().showMessage('Terjadi kesalahan. Data gagal disimpan.','danger',700);
					$("#errorHandler").html(msg.errormsg).show();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				//$().showMessage('Terjadi kesalahan.<br />'+	textStatus + ' - ' + errorThrown ,'danger');
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
	});




	function editThis(obj){
		var id = $(obj).attr('data-id');	//id as nik
		$('#myform input[name="state"]').val(id);		
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('setting/editThis');?>',
			data: "id="+id+"&tabel=email_setting"+"&field=id",
			dataType: 'json',
			success: function(msg) {
				
				if(msg.status =='success'){
					console.log(msg.data);
					
					$('#email_host').val(msg.data.email_host);
					$('#email_port').val(msg.data.email_port);
					$('#email_user').val(msg.data.email_user);
					$('#email_pass').val(msg.data.email_pass);
					$('#email_from').val(msg.data.email_from);
					$('#email_from_name').val(msg.data.email_from_name);
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