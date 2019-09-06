<?php errorHandler();?>
<?php echo form_open('lembur/edit',array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="no_trans" class="col-sm-4 control-label">No Dokumen</label>
			<div class="col-sm-8">
				<?php
					$no_trans = array(
						'name'=>'no_trans',
						'id'=>'no_trans',
						'class'=>'form-control',
						'value'=>$row->NO_TRANS,
						'readonly'=>'readonly'
					);
					echo form_input($no_trans);
				?>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="nama" class="col-sm-4 control-label">Nama</label>
			<div class="col-sm-8">
				<?php
					$nama = array(
						'name'=>'nama',
						'id'=>'nama',
						'class'=>'form-control',
						'value'=>$row->NAMA,
						'readonly'=>'readonly'
					);
					echo form_input($nama);
				?>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="nik" class="col-sm-4 control-label">NIK</label>
			<div class="col-sm-8">
				<?php
					$nik = array(
						'name'=>'nik',
						'id'=>'nik',
						'class'=>'form-control',
						'readonly'=>'readonly',
						'value'=>$row->NIK
					);
					echo form_input($nik);
				?>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="cabang" class="col-sm-4 control-label">Cabang</label>
			<div class="col-sm-8">
				<?php
					$cabang = array(
						'name'=>'cabang',
						'id'=>'cabang',
						'class'=>'form-control',
						'readonly'=>'readonly',
						'value'=>$row->NAMA_CABANG
					);
					echo form_input($cabang);
				?>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="divisi" class="col-sm-4 control-label">Divisi</label>
			<div class="col-sm-8">
				<?php
					$divisi = array(
						'name'=>'divisi',
						'id'=>'divisi',
						'class'=>'form-control',
						'readonly'=>'readonly',
						'value'=>$row->NAMA_DIV
					);
					echo form_input($divisi);
				?>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
<div class="table-responsive">
	<table class="table table-bordered" id="tblembur">
		<thead>
			<tr>
				<th rowspan="2" class="text-center" width="120">Tanggal Lembur</th>
				<th rowspan="2" class="text-center">Alasan Lembur</th>
				<th colspan="2" class="text-center">Waktu</th>
				<th rowspan="2" class="text-center" width="48"></th>
			</tr>
			<tr>
				<th class="text-center" width="130">Mulai</th>
				<th class="text-center" width="130">Selesai</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($rowd as $detail){ 
			$TANGGAL = revdate($detail->TGL_LEMBUR);
			$mulai_h = date("H",strtotime($detail->JAM_MULAI));
			$mulai_m = date("i",strtotime($detail->JAM_MULAI));
			$selesai_h = date("H",strtotime($detail->JAM_SELESAI));
			$selesai_m = date("i",strtotime($detail->JAM_SELESAI));
		?>
			<tr>
				<td>
					<?php
						$tgllembur = array(
							'name'=>'tanggal[]',
							'class'=>'form-control tgllembur',
							'readonly'=>'readonly',
							'value'=>$TANGGAL
						);
						echo form_input($tgllembur);
					?>
				</td>
				<td>
					<?php
						$alasan = array(
							'name'=>'alasan[]',
							'class'=>'form-control',
							'value'=>$detail->KETERANGAN
						);
						echo form_input($alasan);
					?>
				</td>
				<td class="form-inline">
					<?php echo form_dropdown('mulai_h[]',range(00,23),$mulai_h,' class="form-control" style="width:50px"');?> <label> :</label> 
					<?php echo form_dropdown('mulai_m[]',range(00,59),$mulai_m,' class="form-control" style="width:50px"');?>
				</td>
				<td class="form-inline">
					<?php echo form_dropdown('selesai_h[]',range(00,23),$selesai_h,' class="form-control" style="width:50px"');?> <label> : </label>
					<?php echo form_dropdown('selesai_m[]',range(00,59),$selesai_m,' class="form-control" style="width:50px"');?>
				</td>
				<td>
					
					<a href="javascript:void(0)" onclick="delrow(this)" class="btn btn-danger"><i class="fa fa-trash-o"></i></a> 
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<div class="pull-right"><a href="javascript:void(0)" onclick="addrow()" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Hari Lembur</a>
</div>


<div class="row">
	<div class="col-md-6">
		
			<?php 
			$btsubmit = array(
					'name'=>'btsubmit',
					'id'=>'btsubmit',
					'value'=>'Simpan',
					'class'=>'btn btn-primary'
				);
			echo form_submit($btsubmit);?> 
			<?php 
			$btback = array(
					'name'=>'btback',
					'id'=>'btback',
					'content'=>'Kembali',
					'onclick'=>"backTo('".base_url('lembur/index')."');return false;",
					'class'=>'btn btn-danger'
				);
			echo form_button($btback);?>
	</div>
</div>
<?php echo form_close();?>
<script type="text/javascript">
function addrow(){
	
	$( ".tgllembur" ).datepicker('destroy');
	var clone = $('#tblembur tbody tr:first-child').clone(true);
	clone.find('input').val('');
	clone.find('td').removeClass('has-error');
	clone.appendTo('#tblembur tbody');
	var cnt=1;
	$('#tblembur tbody').find('input.tgllembur').each(function(){
		$(this).attr('id','tgllemburdp'+cnt);
		cnt++;
	});
	$( ".tgllembur" ).datepicker({
		minDate: 'today',
		dateFormat: 'dd-mm-yy'
	});
}

function delrow(obj){
	if($('#tblembur tbody').children().length > 1){
		$(obj).parent().parent().remove();
	}
}

$( ".tgllembur" ).datepicker({
	minDate: 'today',
	dateFormat: 'dd-mm-yy'
});

  
$("#nama").autocomplete({
	minLength: 2,
	source:
	function(req, add){
		$.ajax({
			url: "<?php echo base_url('lembur/lookupemp'); ?>",
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
		$("#nik").val(ui.item.id);  
		$("#cabang").val(ui.item.cabang);
		$("#divisi").val(ui.item.divisi);
		$("#jabatan").val(ui.item.jabatan);
	}
}); 

$('#myform').submit(function(event) {
	event.preventDefault();
	var data = $(this).serializeObject();
	var tanggal = data['tanggal[]'];
	var mulai_h = data['mulai_h[]'];
	var mulai_m = data['mulai_m[]'];
	var selesai_h = data['selesai_h[]'];
	var selesai_m = data['selesai_m[]'];
	
	$('#tblembur tbody').find('input.tgllembur').each(function(){
		if ($(this).val()=='') {
			console.log('error tanggal');
			$(this).parent().addClass('has-error');
		} else {
			$(this).parent().removeClass('has-error');
		}
		var txtalasan = $(this).parent().parent().find('input[name^=alasan]');
		var tdalasan = txtalasan.parent();
		if (txtalasan.val()=='') {
			console.log('error alasan');
			tdalasan.addClass('has-error');
		} else {
			tdalasan.removeClass('has-error');
		}
	});
	$(this).saveForm('<?php echo base_url('lembur/edit');?>','<?php echo base_url('lembur');?>');
	
});


</script>