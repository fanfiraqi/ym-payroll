<?php echo form_open('thr_staff/thr_list',array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">	
	<div class="col-xs-12">
		<div class="panel panel-default"><div class="panel-heading">Pilih Kategori 
		</div><div class="panel-body">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">LAZ / TASHARUF</label>
					<div class="col-sm-8"><?=form_dropdown('laz_tasharuf',array('laz'=>'LAZ/AMIL', 'tasharuf'=>'TASHARUF'),'','id="laz_tasharuf" class="form-control"');?></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">WILAYAH</label>
					<div class="col-sm-8"><?=form_dropdown('wilayah',array('Pusat'=>'Pusat', 'Cabang'=>'Cabang'),'','id="wilayah" class="form-control"');?></div>
				</div>
			</div>
		</div>	
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">TAHUN</label>
					<div class="col-sm-8"><?=form_dropdown('tahun',$thn,date('Y'),'id="tahun" class="form-control"');?></div>
				</div>
			</div>
		</div>
		
		
		</div></div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-md-6">
		
			<?php 
			$btsubmit = array(
					'name'=>'btsubmit',
					'id'=>'btsubmit',
					'value'=>'Lanjutkan',
					'class'=>'btn btn-primary'
				);
			echo form_submit($btsubmit);?> 
			<input type="hidden" name="isXls" id="isXls" value="0">
	</div>
</div>
<?php echo form_close();?>
<script type="text/javascript">

</script>

