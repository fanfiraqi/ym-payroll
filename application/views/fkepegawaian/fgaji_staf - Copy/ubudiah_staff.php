<?php echo form_open('payroll_staff/save_ubudiah_staff',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success">
	Form Entry Penilaian Ubudiah Staff: <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Edit = Sudah ada data yang disimpan ditampilkan untuk diedit, atau ada data yang belum dientrikan<br>
	&nbsp;&nbsp;3. View/Disabled = Melewati Bulan aktif<br>
	</div>
		<div class="panel panel-default">
			<div class="panel-heading">Entry Penilaian Ubudiah Staff Bulanan</div><!-- /.panel-heading -->
				<div class="panel-body" style="overflow:scroll;">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
                           <thead>
                           <tr >
							<th rowspan="2">NO</th>
							<th rowspan="2">NIK</th>
							<th rowspan="2">NAMA</th>
							<th rowspan="2">JABATAN</th>
							<th rowspan="2">JUMLAH HARI</th>							
						  </tr>						 
                         </thead>
                                    <tbody>
<?	//echo "<tr><td colspan=20>".sizeof($row)."</td></tr>";
$i=1;
	if (sizeof($row)==0){		
		
		echo "<tr align=center><td colspan=20>Data Belum Ada</td></tr>";
	}else{
	
	foreach($row as $hasil){ 
		$jmlHari=0;
		$strGet="select JML_HARI from ubudiah_staff where bln='".$digitBln."' and thn='".$thn."' and nik='".$hasil->NIK."'";
			if ($this->db->query($strGet)->num_rows()>0){
				$rskerja=$this->db->query($strGet)->row();
				$jmlHari=$rskerja->JML_HARI;
			}
		?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA_JAB)?></td>		
		<td><?=form_input(array('name'=>'jmlHari_'.$i,'id'=>'jmlHari_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$jmlHari));?></td>		
		
	  </tr>		
		
	<? 
		
		$i++; 
	}
}	
	?>

                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
							

                        </div>
                    </div>
                    <!-- /.panel -->
					
                        <!-- /.panel-body -->
						<? 	if (sizeof($row)>0){ ?>
								<div class="row">
									<div class="col-md-6">
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">										
										<input type="hidden" name="bln" id="bln" value="<?=$digitBln?>">										
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">										
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
												'onclick'=>"backTo('".base_url('payroll_staff/entri_ubudiah')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
											?> 
									</div>
								</div>
						<?}else {
						$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('payroll_staff/entri_ubudiah')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "<br>".form_button($btback);
						}?>
                </div>
</div>
<hr />
<?php echo form_close();?>
<script type="text/javascript">
$('#myform').submit(function(event) {
	$(this).saveForm('<?php echo base_url('payroll_staff/save_ubudiah_staff');?>','<?php echo base_url('payroll_staff/entri_ubudiah');?>');
	event.preventDefault();
});


</script>