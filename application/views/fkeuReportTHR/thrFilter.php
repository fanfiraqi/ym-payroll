<ul class="nav nav-tabs" id="myTab">
  <li><a href="#termin1" data-toggle="tab">REKAP THR</a></li>
  <!-- <li><a href="#termin2" data-toggle="tab">LIST</a></li> -->
</ul>

<div class="tab-content">
  <div class="tab-pane" id="termin1">
<?php echo form_open('keuReportTHR/rekapTHR',array('class'=>'form-horizontal','id'=>'myform'));?>
	<div class="row">
			<div class="col-xs-12">
				<label for="nama" class="col-md-1"></label>	
				<div class="form-group">
				<label for="nama" class="col-md-2   control-label">KOTA CABANG</label>
					<div class="col-sm-8"><?=form_dropdown('id_cabang',$cabang,'','id="id_cabang" class="form-control"');?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-xs-12">
				<label for="nama" class="col-md-1"></label>	
				<div class="form-group">
				<label for="nama" class="col-md-2   control-label">TAHUN</label>
					<div class="col-sm-8"><?=form_dropdown('cbTahun1',$arrThn, date('Y'),'id="cbTahun1" class="form-control"');?>
					<input type="hidden" name="display1" id="display1" value="0">	<br><?
					$btsubmit = array(
						'name'=>'btLanjut1',
						'id'=>'btLanjut1',
						'value'=>'Lanjutkan',					
						'class'=>'btn btn-primary'
						);
					echo form_submit($btsubmit);?></div>
				</div>
			</div>
		</div>
 <?php echo form_close();?>

</div><!-- / tab termin1 -->



</div>	<!-- tab head -->




<script type="text/javascript">
$(document).ready(function(){
	$('#myTab a:first').tab('show');

});



</script>

