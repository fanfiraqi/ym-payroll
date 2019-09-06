<?php errorHandler();?>
<?php echo form_open('thr_staff/save_master',array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label  class="col-sm-4 control-label">TAHUN</label>
			<div class="col-sm-5"><?=form_input(array('name'=>'tahun','id'=>'tahun','class'=>'form-control','onkeypress'=>"return numericVal(this,event)"));?></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label class="col-sm-4 control-label">KOTA CABANG</label>
			<div class="col-sm-8"><?=form_dropdown('cabang',$cabang,'','id="cabang" class="form-control"');?></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label  class="col-sm-4 control-label">DIVISI</label>
			<div class="col-sm-8"><?=form_dropdown('divisi',$divisi,'','id="divisi" class="form-control"');?></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">Centang Komponen Gaji untuk THR Staff</div><!-- /.panel-heading -->
				<div class="panel-body" style="overflow:scroll;">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
                                 <th ><input type="checkbox"  class="checkbox-inline" onClick="EW_selectKey(this,'cbPilih[]');">&nbsp;NO</th>
                                 <th>NAMA KOMPONEN GAJI</th>
                                 <th>KETERANGAN</th>
								</tr>
                            </thead>
							<tbody>
<?	$i=1;	
	foreach($komponen as $row){
		echo "<tr>";		
		echo "<td><input type=\"checkbox\" value=\"$i\" name=\"cbPilih[]\">&nbsp;$i</td>";		
		echo "<td><input type=\"hidden\" name=\"idkomp_$i\" id=\"idkomp_$i\" value=\"".$row->ID."\">".$row->NAMA."</td>";
		echo "<td>".$row->KETERANGAN."</td>";
		echo "</tr>";		
		$i++;
	}

?>
							</tbody>
						</table>
					 </div>
				</div><!-- /.panel-body -->
            </div><!-- /.panel -->
       </div>
</div>
<hr />
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
					'content'=>'Batal',
					'onclick'=>"backTo('".base_url('thr_staff/index')."');return false;",
					'class'=>'btn btn-danger'
				);
			echo form_button($btback);?>
	</div>
</div>

<?php echo form_close();?>
<script type="text/javascript">
$('#myform').submit(function(event) {
	$(this).saveForm('<?php echo base_url('thr_staff/save_master');?>','<?php echo base_url('thr_staff');?>');
	event.preventDefault();
});

$('#cabang').change(function(){
	if ($(this).val()==''){
		$('#divisi').find('option').remove().end().attr('disabled',true);
		//$('#dataTables').dataTable().fnReloadAjax();
		return false;
	}
		
	$.ajax({
		url: "<?php echo base_url('employee/comboDivByCab'); ?>",
		dataType: 'json',
		type: 'POST',
		data: {cabang:$(this).val()},
		success: function(respon){
			$('#divisi').find('option').remove().end();
			if (respon.status==1){
				var item = respon.data;
				for (opt=0;opt<item.length;opt++){
					$('#divisi').append('<option value="'+item[opt].ID_DIV+'">'+item[opt].NAMA_DIV+'</option>');
				}
			}
			$('#divisi').attr('disabled',false);
			//$('#dataTables').dataTable().fnReloadAjax();
		}
	});
}).trigger('change');


function EW_selectKey(frm,elem)
{
    var f = frm.form;
    if (!f.elements[elem]) return;
    if (f.elements[elem][0])
    {
    for (var i=0; i<f.elements[elem].length; i++)
        f.elements[elem][i].checked = frm.checked;
    }
    else
    {
       f.elements[elem].checked = frm.checked;
    }
}
$(document).ready(function(){
});

</script>
