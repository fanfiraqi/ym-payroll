<ul class="nav nav-tabs" id="myTab">
  <li><a href="#termin1" data-toggle="tab">SINGLE SLIP EMAIL</a></li>
  <li><a href="#termin2" data-toggle="tab">MASS SLIP EMAIL</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane" id="termin1">

<div class="row form-horizontal">	
	<div class="col-xs-12">
		<div class="panel panel-default">
		<div class="panel-heading">Single THR Slip Email</div>
		<div class="panel-body">
		
		<div class="row">
			<div class="col-md-8">
				<div class="form-group">
					<label  class="col-md-4  control-label">Tahun</label>
					<div class="col-md-6"><?=form_dropdown('cbTahun1',$arrThn, date('Y'),'id="cbTahun1" class="form-control"');?></div>	
				</div>
			</div>			
		</div>
		
		<div class="row">
			<div class="col-md-8">
				<div class="form-group">
				<label for="nama" class="col-md-4  control-label">Ketik NIK/NAMA</label>	
					<div class="col-md-6">
							<?php
							$nama = array(
								'name'=>'nama',
								'id'=>'nama',
								'class'=>'form-control'
							);
							echo form_input($nama);
						?>
					<input type="hidden" name="nik" id="nik">
					</div>
			</div>
		</div>
		</div>

		<div class="row">
			<div class="col-md-8"><label  class="col-md-4  control-label"></label>
				<div class="col-md-4 form-group no-display" id ="hasil">
				
				</div>
			</div>
		</div> 


			</div>
		</div>
	</div>
</div>

</div><!-- / tab termin1 -->

<div class="tab-pane" id="termin2">
<!-- <?php echo form_open('emailPage/massResList',array('class'=>'form-horizontal','id'=>'myform'));?>
 --><div class="row  form-horizontal">	
	<div class="col-xs-12">
		<div class="panel panel-default">
		<div class="panel-heading">Kirim Email Slip THR Massal </div>
		<div class="panel-body">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">JENIS THR</label>
					<div class="col-sm-8"><?=form_dropdown('jenis',$jenis,'','id="jenis" class="form-control" ');?></div>
				</div>
			</div>
		</div>
		<div class="row" id = "slide_out" class="no-display">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">WILAYAH</label>
					<div class="col-sm-8"><?=form_dropdown('wilayah',array('Pusat'=>'Pusat', 'Cabang'=>'Cabang'),'','id="wilayah" class="form-control"');?></div>
				</div>
			</div>
		</div>	
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label  class="col-sm-4 control-label">TAHUN</label>
					<div class="col-sm-8"><?=form_dropdown('cbTahun',$arrThn, date('Y'),'id="cbTahun" class="form-control"');?></div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">		
					<button id="doprogress" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Email Slip untuk data terakhir yang sudah tersimpan saja">Email Slip All </button>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">&nbsp;<br>
				<div class="progress no-display progress-lg">
				<div class="progress-bar progress-bar-success" id="progressBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
				0%
				</div>
				</div>
			</div>
		</div> 

			</div>
		</div>
	</div>
</div>
<!-- <?php echo form_close();?>
 -->	
</div> <!-- // tab 2 -->

</div>	<!-- tab head -->

<script type="text/javascript">
$(document).ready(function(){
	$('#myTab a:first').tab('show');

});
$('#jenis').change(function(){
		if($(this).val()=="laz" || $(this).val()=="tasharuf"){
			$("#slide_out").fadeSlide("show");
		}else{
			$("#slide_out").fadeSlide("hide");
		}
	});

$("#nama").autocomplete({
	minLength: 2,
	source:
	function(req, add){
		$.ajax({
			url: "<?php echo base_url('emailPageTHR/getNik'); ?>",
			dataType: 'json',
			type: 'POST',
			data: req,
			success:   
			function(data){
				if(data.response =="true"){
					add(data.message);
				}
			}
		});
	},
	select:
	function(event, ui) {    
		//alert(ui.item.id);
		$("#nik").val(ui.item.id); 
		showResultSingle(ui.item.id, $('#cbTahun1').val());
		
	}
}); 

function showResultSingle(nik, thn){
	//alert(nik+"#"+bln+"#"+thn);
	$.ajax({
		url: "<?php echo base_url('emailPageTHR/showResultSingle'); ?>",
		dataType: 'json',
		type: 'POST',
		data: "thn="+thn+"&nik="+nik,				
		success: function(data,  textStatus, jqXHR){					
			//alert(data.hasil);
			//alert(data.str);
			$('#hasil').html(data.hasil);
			$('#hasil').show();
			} 
		});	
}


$('#doprogress').click(function(){
	cetakprogress();
	});

function cetakprogress(cnt,step){
	if(typeof cnt === 'undefined'){
		var data = {jenis:$('#jenis').val(),wilayah:$('#wilayah').val(), thn: $('#cbTahun').val()};
	} else {
		var data = {jenis:$('#jenis').val(),wilayah:$('#wilayah').val(), thn: $('#cbTahun').val(), cnt:cnt,step:step};
	}
	$.ajax({
		url: "<?php echo base_url('emailPageTHR/slipLoop'); ?>",
		dataType: 'json',
		type: 'GET',
		data: data,
		success: function(respon){
			if (respon.status==1){
				$('.progress').show();
				if(respon.jumlah){
					nextcnt = respon.jumlah;
				} else {
					nextcnt = cnt;
				}
				
				if(typeof respon.nextstep === 'undefined'){
					nextstep = 1;
				} else {
					nextstep = respon.nextstep;
				}
				
				if(typeof respon.percent === 'undefined'){
					percent = 0;
				} else {
					percent = respon.percent;
				}
				
				
				if (respon.complete != 1){
					$('#progressBar').css('width',percent+'%').html(percent+'%');
					cetakprogress(nextcnt,nextstep);
				}
				/*var jum = respon.jumlah;
				$('.progress').show();
				for(var i=1;i<=jum;i++){
					
				}*/
			} else {
				$('.progress').hide();
			}
		}
	});
}

</script>

