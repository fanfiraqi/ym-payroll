<?php echo form_open('absensi/rekap_save',array('class'=>'form-horizontal','id'=>'myform'));?>
<div class="row"><!-- HEADER -->
	<div class=".col-lg-8" >
		<div class="panel panel-default"> 
		<div class="panel-heading">Form Rekap Absensi</div>
		<div class="panel-body" > 
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label class="col-sm-4 control-label">PERIODE </label>
						<div class="col-sm-8"><?=form_input(array('name'=>'strperiode','id'=>'strperiode','readonly'=>true,'class'=>'form-control','value'=>$strBulan." ".$thn));?><input type="hidden" name="periode" id="periode" value="<?=$thn.$digitBln?>"></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label class="col-sm-4 control-label">START DATE </label>
						<div class="col-sm-8"><?=form_input(array('name'=>'mindate','id'=>'mindate','readonly'=>true,'class'=>'myform-control','value'=>$start_date));?></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label class="col-sm-4 control-label">END DATE </label>
						<div class="col-sm-4"><?=form_input(array('name'=>'maxdate','id'=>'maxdate','readonly'=>true,'class'=>'myform-control','value'=>$end_date));?></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label class="col-sm-4 control-label">JUMLAH HARI EFEKTIF</label>
						<div class="col-sm-8"><?=form_input(array('name'=>'jmlefektif','id'=>'jmlefektif','readonly'=>true,'class'=>'myform-control','value'=>$jmlhari));?></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label class="col-sm-4 control-label">JUMLAH LIBUR NASIONAL </label>
						<div class="col-sm-8"><?=form_input(array('name'=>'jml_libnas','id'=>'jml_libnas','readonly'=>true,'class'=>'myform-control','value'=>$libnas));?></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group"><label class="col-sm-4 control-label">JUMLAH HARI KERJA </label>
						<div class="col-sm-8"><?=form_input(array('name'=>'jml_harikerja','id'=>'jml_harikerja','readonly'=>true,'class'=>'myform-control','value'=>$jmlHariKerja));?></div>
					</div>
				</div>
			</div>

		</div>
		</div>
	</div>
</div>

<div class="alert alert-success">
<ol><li>Jml Hari Masuk default ambil dari csv absensi atau dari rekap absensi yang sudah tersimpan
<li>Jml Hari Masuk dan Alpa editable untuk koreksi manual oleh manager jika ada karyawan yg tidak absen melalui fingerprint
<li>Jml Cuti/Ijin untuk cuti selain cuti khusus

</ol>
</div>

<div class="row"><!-- DETAIL -->
	<div class=".col-lg-12" style="overflow:scroll; ">
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
        <thead>
        <tr >
		<th >NO</th>
		<th >NIK</th>
		<th >NAMA</th>
		<th >JML MASUK (HARI)</th>                            
		<th >CUTI/IJIN (HARI)</th>                            
		<th >LEMBUR (JAM)</th>
		<th >TERLAMBAT (MENIT)</th>
        <th >JML ALPA</th>
		</tr>
		</thead>
		<tbody>
<? $i=1;
//echo "<tr><td colspan=8>".$sCek."</td></tr>";
if (sizeof($row)==0){		
	echo "<tr align=center><td colspan=20>Belum Ada Data Absensi</td></tr>";
}else{
	foreach($row as $hasil){
		//echo "<tr><td colspan=8>".$sCek."</td></tr>";
		//cuti
		$strcuti="SELECT NIK, SUM( JML_HARI ) HR_CUTI , SUBSTR( no_trans, 1, 6 ), jenis_cuti, tgl_awal, tgl_akhir
					FROM `cuti`
					WHERE (tgl_awal BETWEEN '$start_date' AND '$end_date') and (tgl_akhir BETWEEN '$start_date' AND '$end_date')
				and  nik = '".$hasil->NIK."' GROUP BY nik";
		$jml_cuti=0;
		//echo "<tr><td colspan=8>".$strcuti."</td></tr>";
		if ($this->db->query($strcuti)->num_rows()){
			$rsCuti=$this->db->query($strcuti)->row();
			if ($rsCuti->jenis_cuti!='2'){	//cuti khusus tidak memotong
				$jml_cuti=$rsCuti->HR_CUTI;
			}
		}
		
		$sIjin="select ifnull(sum(JML_HARI),0) ALPA_IJIN from ijin_khusus_alpa where (TGL_AWAL BETWEEN '$start_date' AND '$end_date') and (TGL_AKHIR BETWEEN '$start_date' AND '$end_date') and nik='".$hasil->NIK."'";
			$alpaijin=0;
			//echo "<tr><td colspan=8>".$sIjin."</td></tr>";
			if ($this->db->query($sIjin)->num_rows()>0){
				$rsIjin=$this->db->query($sIjin)->row();
				$alpaijin=$rsIjin->ALPA_IJIN;
				$jml_cuti+=$rsIjin->ALPA_IJIN;
			}

		//lembur
		$strlembur="SELECT NIK, SUM( JML_JAM ) J_JAM , substr( l.no_trans, 1, 6 )
				FROM `lembur` l, lembur_d d
				WHERE l.no_trans=d.no_trans and nik = '".$hasil->NIK."'
				AND substr( l.no_trans, 1, 6 ) = '".$thn.$digitBln."'
				GROUP BY nik";
		$jml_lembur=0;
		if ($this->db->query($strlembur)->num_rows()>0){
			$rsLembur=$this->db->query($strlembur)->row();
			$jml_lembur=$rsLembur->J_JAM;
		}else{
			$jml_lembur=0;
		}
		$hrLembur=floor($jml_lembur>=7?$jml_lembur/7:0);
		$hrTerlambat=0;
		
		
		//hari kerja ambil dari database
		$jmlmasuk=$jmlHariKerja;
		$skerja="select JML_MASUK, JML_ALPA from rekap_absensi where periode='".$thn.$digitBln."' and nik='".$hasil->NIK."'";
		if ($this->db->query($skerja)->num_rows()>0){
			$rskerja=$this->db->query($skerja)->row();
			$jmlmasuk=$rskerja->JML_MASUK;
			$alpa=$rskerja->JML_ALPA;
		}else{
			$jmlmasuk=$jmlHariKerja;
			$alpa=$jmlHariKerja-($jmlmasuk+$jml_cuti);
			$alpa=($alpa<0?0:$alpa);
		}
		
		//echo "<tr><td colspan=8>".$hrLembur."#".$skerja."</td></tr>";
	?>
	<tr >
	<td><?=$i?><input type="hidden" name="awal_jml_masuk_<?=$i?>" id="awal_jml_masuk_<?=$i?>" value="<?=$jmlHariKerja?>"></td>
	<td><?=$hasil->NIK?><input type="hidden" name="awal_alpa_<?=$i?>" id="awal_alpa_<?=$i?>" value="<?=$alpa?>"></td>
	<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
	<td><?=form_input(array('name'=>'jml_masuk_'.$i,'id'=>'jml_masuk_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$jmlmasuk, 'onchange'=>"setAlpa(this, '$i')"));?></td>
	<td><?=form_input(array('name'=>'cuti_'.$i,'id'=>'cuti_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$jml_cuti));?></td>
	<td><?=form_input(array('name'=>'lembur_'.$i,'id'=>'lembur_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$jml_lembur));?></td>
	<td><?=form_input(array('name'=>'terlambat_'.$i,'id'=>'terlambat_'.$i,'class'=>'myform-control','size'=>10, 'value'=>0));?></td>
	<td><?=form_input(array('name'=>'alpa_'.$i,'id'=>'alpa_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$alpa, 'onchange'=>"setJmlMasuk(this, '$i')"));?></td>
	</tr>
	<?
		$i++;
	}
}	
?></table><?

 	if (sizeof($row)>0){ ?>
								<div class="row">
									<div class="col-md-6">
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">										
										<input type="hidden" name="bln" id="bln" value="<?=$digitBln?>">										
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">		
										<input type="hidden" name="id_cabang" id="id_cabang" value="<?=$id_cabang?>">
										<?php 
											$btsubmit = array(
													'name'=>'btsubmit',
													'id'=>'btsubmit',
													'value'=>'Simpan',
													'class'=>'btn btn-primary'
												);
											echo form_submit($btsubmit);
											if ($cek->CEK>=1){
											$btsubmit = array(
												'type'=>'button',
												'name'=>'btCsvBank',
												'id'=>'btCsvBank',
												'value'=>'Export CSV Bank',
												'class'=>'btn btn-primary'
											);
										//echo "&nbsp;".form_input($btsubmit)."&nbsp;";
										
										$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Batal',
												'onclick'=>"backTo('".base_url('absensi/rekapAbsen')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
											}?> 
									 
									</div>
								</div>
						<?}else{
								$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('absensi/setFilter')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "<div style=\"text-align:center\">".form_button($btback)."</div>";			
											
						}?>
		</tbody>
	</div>
</div>
<?php echo form_close();?>
<script type="text/javascript">
$('#myform').submit(function(event) {
	$(this).saveForm('<?php echo base_url('absensi/rekap_save');?>','<?php echo base_url('absensi/rekapAbsen');?>');
	event.preventDefault();
});

function setAlpa(obj, idk){		
	//update total
	$('#alpa_'+idk).val( parseFloat($('#jml_harikerja').val())-(parseFloat(obj.value)+parseFloat($('#cuti_'+idk).val())));
}
function setJmlMasuk(obj, idk){		
	//update total
	$('#jml_masuk_'+idk).val( parseFloat($('#jml_harikerja').val())-(parseFloat(obj.value)+parseFloat($('#cuti_'+idk).val())));
}
</script>