<?php echo form_open('hrdReportRekapHRD/rekapResultMap',array('class'=>'form-horizontal','id'=>'myform'));?>
	<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label  class="col-sm-4 control-label">JENIS DATA</label>
					<div class="col-sm-8"><?=form_dropdown('cbJenis',$options, '','id="cbJenis" class="form-control"');?></div>
				</div>
			</div>
	</div>
	<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label  class="col-sm-4 control-label">BULAN</label>
					<div class="col-sm-8"><?=form_dropdown('cbBulan',$arrBulan,date('m'),'id="cbBulan" class="form-control"');?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label  class="col-sm-4 control-label">TAHUN</label>
					<div class="col-sm-8"><?=form_dropdown('cbTahun',$arrThn, date('Y'),'id="cbTahun" class="form-control"');?>
					<input type="hidden" name="display" id="display" value="0">	
					</div>
				</div>
			</div>
		</div>
<div class="row">
	<div class="col-xs-12">
	
		<?
		$btsubmit = array(
			'name'=>'btLanjut',
			'id'=>'btLanjut',
			'value'=>'Lanjutkan',					
			'class'=>'btn btn-primary'
			);
		echo form_submit($btsubmit);?> 
	
	</div>
</div>
 <?php echo form_close();?>