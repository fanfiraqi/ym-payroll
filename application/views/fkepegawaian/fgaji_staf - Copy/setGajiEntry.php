<ul class="nav nav-tabs" id="myTab">
  <li><a href="#termin1" data-toggle="tab">BELUM DI SET</a></li>
  <li><a href="#termin2" data-toggle="tab">SUDAH DI SET</a></li>
</ul>


<div class="tab-content">
  <div class="tab-pane" id="termin1">
<!-- tab 1 -->
<?php echo form_open('payroll_staff/set_saveList',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">Entri Master Gaji / Tunjangan Staff yang <b><font size="3" color="red">belum</font></b> di seting</div><!-- /.panel-heading -->
				<div class="panel-body" style="overflow:scroll;">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th ><input type="checkbox"  class="checkbox-inline" onClick="EW_selectKey(this,'cbPilih[]');">&nbsp;NO</th>
                                            <th>JABATAN</th>
                                            <th>NIK</th>
                                            <th>NAMA</th>
<?	//loop id tunj
	
	foreach($rsTunj as $rowTunj){
		echo "<th ".($rowTunj->ID=='1'? 'width=10%':'').">".$rowTunj->NAMA."</th>";		
	}
		
	?>
                                        </tr>
                                    </thead>
                                    <tbody>
<?	//echo "<tr><td colspan=20>".$str."</td></tr>";
	if (sizeof($rowBelum)==0){
		$j=1;
		$i=1;
		echo "<tr align=center><td colspan=".(sizeof($rsTunj)+4).">Belum ada Karyawan untuk diseting </td></tr>";
	}else{
	$i=1;
	foreach($rowBelum as $hasil){ ?>
<tr>
<td><input type="checkbox" value="<?=$i?>" name="cbPilih[]">&nbsp;<?=$i?></td>
<td><?=str_replace(" ","&nbsp;",$hasil->NAMA_JAB)?></td>
<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?></td>
<?	//loop id tunj
	$j=1;
	foreach($rsTunj as $rowBelumTunj){
		$qres = $this->db->query("select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=".$rowBelumTunj->ID);
		if ($qres->num_rows() > 0)	{
			 $rsNom = $qres->row(); 
			 $nominal=$rsNom->NOMINAL;
		}else{
			$nominal=0;
		}
		?><td><?=form_input(array('name'=>'valTunj_'.$i.'_'.$j,'id'=>'valTunj_'.$i.'_'.$j,'class'=>'myform-control','size'=>12, 'value'=>$nominal,'onkeypress'=>"return numericVal(this,event)", 'onblur'=>"blurObj(this)",'onclick'=>"clickObj(this)"));?><input type="hidden" name="hidIdTunj_<?=$i.'_'.$j?>" id="hidIdTunj_<?=$i.'_'.$j?>" value="<?=$rowBelumTunj->ID;?>"></td><?
		$j++;
	}
		
	?>
</tr>
<? $i++; 
	}
}
	

	
	?>

                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
							<? 	if (sizeof($rowBelum)>0){ ?>
								<div class="row">
									<div class="col-md-6">
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">
										<input type="hidden" name="jml_j" id="jml_j" value="<?=($j-1)?>">
											<?php 
											$btsubmit = array(
													'name'=>'btsubmit',
													'id'=>'btsubmit',
													'value'=>'Simpan',
													'class'=>'btn btn-primary'
												);
											echo form_submit($btsubmit);
											$btback = array(
													'name'=>'btback',
													'id'=>'btback',
													'content'=>'Batal',
													'onclick'=>"backTo('".base_url('payroll_staff/index')."');return false;",
													'class'=>'btn btn-danger'
												);
											echo "&nbsp;".form_button($btback);
											?> 

									</div>
								</div>
						<?}else{?>
								<?php 
								$btback = array(
										'name'=>'btback',
										'id'=>'btback',
										'content'=>'Kembali',
										'onclick'=>"backTo('".base_url('payroll_staff/index')."');return false;",
										'class'=>'btn btn-danger'
									);
								echo form_button($btback); }?>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
</div><?php echo form_close();?>

</div><!-- //tab 1 -->

<!-- tab 2 -->
<div class="tab-pane" id="termin2">
	<?php $this->load->view('fpayroll_staff/setGajiEdit'); ?>	
		</div>
	
	
</div>

<!-- //tab 2 -->
<hr />
<script type="text/javascript">
$(document).ready(function(){
	$('#myTab a:first').tab('show');

});
$('#myform').submit(function(event) {
	$(this).saveForm('<?php echo base_url('payroll_staff/set_saveList');?>','<?php echo base_url('payroll_staff');?>');
	event.preventDefault();
});
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

</script>
