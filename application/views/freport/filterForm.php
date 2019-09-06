<?php echo form_open('rptPayroll/rekapPayroll',array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">	
	<div class="col-xs-12">
		<div class="panel panel-default"><div class="panel-heading">Pilih Kategori 
		</div><div class="panel-body">
<? if ($jenis=="staff") {
	$wil=array('Pusat'=>'Pusat', 'Cabang'=>'Cabang');
	if ($role==20 || $role==26){
		$wil=array( 'Cabang'=>'Cabang');
	}
	?>		
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">LAZ / TASHARUF</label>
					<div class="col-sm-8"><?=form_dropdown('penggajian',array('laz'=>'LAZ/AMIL', 'tasharuf'=>'TASHARUF'),'','id="penggajian" class="form-control"');?></div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">WILAYAH</label>
					<div class="col-sm-8"><?=form_dropdown('wilayah',$wil,'','id="wilayah" class="form-control"');?></div>
				</div>
			</div>
		</div>	
<? } ?>
<? if ($jenis=="zisco") {?>		
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">PENGGAJIAN</label>
					<div class="col-sm-8"><?=form_dropdown('penggajian',array('zisco_transport'=>'Transport', 'zisco_bonus'=>'Bonus'),'','id="penggajian" class="form-control"');?></div>
				</div>
			</div>
		</div>

		
<? } ?>
<? if ($jenis<>"thr") {?>		
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label  class="col-sm-4 control-label">BULAN</label>
					<div class="col-sm-8"><?=form_dropdown('cbBulan',$arrBulan,date('m'),'id="cbBulan" class="form-control"');?>
					</div>
				</div>
			</div>
		</div>
<? } ?>
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label  class="col-sm-4 control-label">TAHUN</label>
					<div class="col-sm-8"><?=form_dropdown('cbTahun',$arrThn, date('Y'),'id="cbTahun" class="form-control"');?></div>
				</div>
			</div>
		</div>

<?  if ( $jenis<>"nasional") {
	if ($role!=26 ){?>
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">DAFTAR LAPORAN</label>
					<div class="col-sm-8"><?=form_dropdown('daftar',array('karyawan'=>'Rekap Karyawan', 'cabang'=>'Rekap Cabang'),'','id="daftar" class="form-control"');?></div>
				</div>
			</div>
		</div>
<? } }?>

<?  if ( $jenis=="thr") { ?>

		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">THR</label>
					<div class="col-sm-8"><?=form_dropdown('penggajian',array('staff'=>'Staff Dalam', 'zisco'=>'zisco'),'','id="penggajian" class="form-control"');?></div>
				</div>
			</div>
		</div>
<? } ?>
		</div></div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-md-6">
		<input type="hidden" name="jenis" id="jenis" value="<?=$jenis?>">	
		<input type="hidden" name="display" id="display" value="0">	
		<? if ($jenis<>"staff" && $jenis<>"zisco"&& $jenis<>"thr") {?>	
		<input type="hidden" name="penggajian" id="penggajian" value="<?=$jenis?>">	
		<? } ?>
			<?php 
			$btsubmit = array(
					'name'=>'btsubmit',
					'id'=>'btsubmit',
					'value'=>'Lanjutkan',
					'class'=>'btn btn-primary'
				);
			echo form_submit($btsubmit);?> 
	</div>
</div>
<?php echo form_close();?>
<script type="text/javascript">
</script>

