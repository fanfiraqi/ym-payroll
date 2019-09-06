<?php echo form_open('payroll_staff/set_saveList',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">Entri Master Gaji / Tunjangan Staff</div><!-- /.panel-heading -->
				<div class="panel-body" style="overflow:scroll;">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
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
<?	//echo "<tr><td colspan=20>".sizeof($row)."</td></tr>";
	if (sizeof($row)==0){
		$j=1;
		$i=1;
		echo "<tr align=center><td colspan=".(sizeof($rsTunj)+4).">Data Belum Ada</td></tr>";
	}else{
	$i=1;
	foreach($row as $hasil){ ?>
<tr>
<td><?=$i?></td>
<td><?=$hasil->NAMA_JAB?></td>
<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
<td><?=$hasil->NAMA?></td>
<?	//loop id tunj
	$j=1;
	foreach($rsTunj as $rowTunj){
		$qres = $this->db->query("select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=".$rowTunj->ID);
		if ($qres->num_rows() > 0)	{
			 $rsNom = $qres->row(); 
			 $nominal=$rsNom->NOMINAL;
		}else{
			$nominal=0;
		}
		?><td><?=form_input(array('name'=>'valTunj_'.$i.'_'.$j,'id'=>'valTunj_'.$i.'_'.$j,'class'=>'myform-control', 'value'=>$nominal,'onkeypress'=>"return numericVal(this,event)", 'onblur'=>"blurObj(this)",'onclick'=>"clickObj(this)"));?><input type="hidden" name="hidIdTunj_<?=$i.'_'.$j?>" id="hidIdTunj_<?=$i.'_'.$j?>" value="<?=$rowTunj->ID;?>"></td><?
		$j++;
	}
		
	?>
</tr>
<? $i++; }
	}
	

	
	?>

                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
							<? 	if (sizeof($row)>0){ ?>
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
											echo form_submit($btsubmit);?> 
									</div>
								</div>
						<?}?>

                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
</div>
<hr />
<?php echo form_close();?>
<script type="text/javascript">
$('#myform').submit(function(event) {
	$(this).saveForm('<?php echo base_url('payroll_staff/set_saveList');?>','<?php echo base_url('payroll_staff');?>');
	event.preventDefault();
});
</script>
