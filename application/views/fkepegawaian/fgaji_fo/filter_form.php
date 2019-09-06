<?php echo form_open('penggajian_fo/fo_list_entry',array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">	
	<div class="col-xs-12">
		<div class="panel panel-default"><div class="panel-heading">Pilih Kategori Cabang 
		</div><div class="panel-body">
		
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group"><label class="col-sm-4 control-label">KOTA CABANG</label>
					<div class="col-sm-8"><?=form_dropdown('id_cabang',$cabang,'','id="id_cabang" class="form-control"');?></div>
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
					<div class="col-sm-8"><?=form_dropdown('cbTahun',$arrThn, date('Y'),'id="cbTahun" class="form-control"');?></div>
				</div>
			</div>
		</div>

		
		
		</div></div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-md-6">
		
			<!-- <?php 
			$btsubmit = array(
					'name'=>'btsubmit',
					'id'=>'btsubmit',
					'value'=>'Lanjutkan',
					'class'=>'btn btn-primary'
				);
			echo form_submit($btsubmit);?>  -->
			<?php 
			$btsubmit = array(
					'name'=>'btTurun',
					'id'=>'btTurun',
					'value'=>'Lanjutkan',					
					'class'=>'btn btn-primary'
				);
			echo form_submit($btsubmit);?> 
	</div>
</div>
<!-- <IFRAME ID="myIframe" FRAMEBORDER=1 style="overlow:auto;width:750px;height:900px" SRC=""></IFRAME>
 -->

<?php echo form_close();?>
<script type="text/javascript">
 $(document).ready(function() {  
	// $("#list_res").hide();

 });


/*$('#myform').submit(function(event) {
	$(this).saveForm('<?php echo base_url('penggajian_fo/fo_list_entry');?>','<?php echo base_url('penggajian_fo');?>');
	event.preventDefault();
});*/
</script>

