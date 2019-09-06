 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success">
	Form Entry Penilaian ISO Staff: <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Edit = Sudah ada data yang disimpan ditampilkan untuk diedit, atau ada data yang belum dientrikan<br>
	&nbsp;&nbsp;3. View/Disabled = Melewati Bulan aktif<br>
	</div>
		<div class="panel panel-default">
			<div class="panel-heading">Entry Penilaian ISO Staff Bulanan</div><!-- /.panel-heading -->
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
		
		?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA_JAB)?></td>		
		<td><?=form_input(array('name'=>'jmlHari_'.$i,'id'=>'jmlHari_'.$i,'class'=>'myform-control','size'=>10, 'readonly'=>true, 'value'=>$hasil->JML_HARI));?></td>		
		
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
						
								<div class="row">
									<div class="col-md-6">																	
											<?php 
										
											$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Batal',
												'onclick'=>"backTo('".base_url('payroll_staff/daily_report')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
											?> 
									</div>
								</div>
						
                </div>
</div>
<hr />
