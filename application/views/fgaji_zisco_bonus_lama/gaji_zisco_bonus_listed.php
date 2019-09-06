<?php echo form_open('gaji_zisco_bonus/save_gaji_zisco_bonus',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	Informasi : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Open = Sudah ada data yang disimpan dan masih bisa diedit. <br>
	&nbsp;&nbsp;3. Closed = Melewati Periode aktif<br>
	"Slip-Email All" yang dikirim hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut terkirim<br>
	
	</div>
	
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover"  style="max-height:650px;overflow:scroll;" id="myTable">
                           <thead>
                          <tr >
							<th  rowspan=3 >NO</th>
							<th  rowspan=3>NIK</th>
							<th  rowspan=3>NAMA</th>
							<th  rowspan=3>KANTOR</th>
							<th  rowspan=3>REGIONAL</th>
							<th  rowspan=3>JABATAN</th>
							<th  rowspan=3>DONASI RUTIN MUNDUR < 6 BLN</th>							
							<th colspan=4>PENGAMBILAN RUTIN</th>
							<th colspan=5>REALISASI PENGEMBANGAN NON TERIKAT</th>
							<th colspan=7>PENGEMBANGAN INSIDENTIL  TERIKAT</th>
							<th rowspan=3>PRESENTASE PENGAMBILAN</th>
						
							<th colspan="13">PENDAPATAN</th>
							<th rowspan=3>TOTAL PENDAPATAN</th>
							<th colspan="4">POTONGAN</th>
							<th rowspan=3>TOTAL POTONGAN</th>
							<th rowspan=3>TOTAL TERIMA</th>
							<th rowspan=3>TOTAL FUNDRAISING</th>
							<th rowspan=3>SLIP</th>

							<th rowspan=3>PAYROLL TRANSPORT</th>
							<th colspan=2>PAYROLL BONUS</th>
							<th rowspan=3>TOTAL TERIMA BONUS</th>
						  </tr>

						   <tr>	
							<th colspan=2 >Infaq</th>
							<th colspan=2 >Zakat</th>
							<th colspan=2 >Infaq</th>
							<th colspan=2 >Zakat Mal</th>
							<th rowspan=2 >Zakat Fithrah</th>							
							<th colspan=3 >wakaf</th>
							<th colspan=4 >dana terikat non wakaf</th>
							
							<th rowspan=2>BANTUAN TRANSPORT</th>
							<th rowspan=2> TUNJANGAN PENGAMBILAN </th>
							<th rowspan=2> INSENTIF PENGAMBILAN </th>
							<th rowspan=2> BONUS PRESTASI PENGAMBILAN  </th>
							<th rowspan=2> BONUS PENGEMBANGAN INFAQ RUTIN </th>
							<th rowspan=2> INSIDENTIL, ZAKAT, WAKAF TUNAI  </th>
							<th rowspan=2> BONUS PATUNGAN SAPI </th>
							<th rowspan=2> BONUS QURBAN SAPI </th>
							<th rowspan=2> KOREKSI</th>
							<th rowspan=2> PENYESUAIAN</th>
							<th rowspan=2> PENGEMBALIAN BONUS 40%</th>
							<th rowspan=2> TUNJANGAN JABATAN</th>
							<th rowspan=2> TUNJANGAN PRESTASI</th>

							<th rowspan=2> DANSOS</th>
							<th rowspan=2> ZAKAT</th>
							<th rowspan=2> MYM</th>
							<th rowspan=2> LAIN-LAIN</th>

							<th rowspan=2> BONUS</th>
							<th rowspan=2> POTONGAN</th>
						  </tr>
						  <tr>
							<th>Target</th>
							<th>Realisasi</th>
							<th>Target</th>
							<th>Realisasi</th>
							<th>Rutin</th>
							<th>Insidental</th>
							<th>Rutin</th>
							<th>Insidental</th>

							<th>ICMBS</th>
							<th>STAINIM</th>
							<th>MASJID</th>
							<th>Al&nbsp;quran</th>
							<th>Qurban</th>
							<th>Bencana</th>
							<th>fidyah</th>

							</tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan=46>Data Belum Ada</td></tr>";
	}else{
		$i=1;	//as row
		$blnIdk=$this->arrIntBln;
		//cek var bln tahun  value DARI BULAN SEBLMNYA
		if ($bln==1){
			$bln_pre=12;
			$thn_pre=$thn-1;
		}else{
			$bln_pre=$blnIdk[$bln-1];
			$thn_pre=$thn;
		}

		foreach($row as $hasil){
			
		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
		$rscab=$this->gate_db->query("SELECT m.*, (SELECT DISTINCT kota FROM mst_cabang WHERE mst_cabang.`id_cabang`=m.ID_cabang_PARENT) nama_parent FROM mst_cabang m where id_cabang=".$hasil->ID_CABANG )->row();
		$rs_stspeg=$this->db->query("select value1 from gen_reff where reff='STSPEGAWAI' and id_reff=".$hasil->STATUS_PEGAWAI)->row();
				
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><?=str_replace(" ","&nbsp;",$rscab->kota)?></td>
		<td><?=str_replace(" ","&nbsp;",$rscab->nama_parent)?></td>
		<td><?=str_replace(" ","&nbsp;",$rsjab->nama_jab)?><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td bgcolor="#ffffcc"><input type="text" name="zisco_mundur_<?=$i?>" id="zisco_mundur_<?=$i?>" value="<?php echo $hasil->DONASI_RUTIN_MUNDUR?>" onkeyup="goPengembalian40(this, <?=$i?>)"  onkeypress="return numericVal(this,event)" ></td>			
<?	//get data target&pengambilan	
	$rutin_infaq_target=$hasil->RUTIN_INFAQ_TARGET;
	$rutin_infaq_realisasi=$hasil->RUTIN_INFAQ_REALISASI;
	$rutin_infaq_selisih=$hasil->RUTIN_INFAQ_PENGEMBANGAN;
	
	$rutin_zakat_target=$hasil->RUTIN_ZAKAT_TARGET;
	$rutin_zakat_realisasi= $hasil->RUTIN_ZAKAT_REALISASI;
	$rutin_zakat_selisih=$hasil->RUTIN_ZAKAT_PENGEMBANGAN;

	$insi_infaq_paid=$hasil->INSI_INFAQ;
	$insi_zakat_paid=$hasil->INSI_ZAKAT_MAL;
	$insi_zakat_fitrah=$hasil->INSI_ZAKAT_FITHRAH;
	$insi_wakaf_icmb=$hasil->WAKAF_ICMB;
	$insi_wakaf_masjid=$hasil->WAKAF_STAINIM;
	$insi_wakaf_stainim=$hasil->WAKAF_MASJID;
	$non_wakaf_quran=$hasil->NON_WAKAF_QURAN;
	$non_wakaf_qurban=$hasil->NON_WAKAF_QURBAN;
	$non_wakaf_bencana=$hasil->NON_WAKAF_BENCANA;
	$non_wakaf_fidyah=$hasil->NON_WAKAF_FIDYAH;

$persen_ambil=0;
$str_persen_ambil="";
$sumrutin_target=$rutin_infaq_target + $rutin_zakat_target;
$sumrutin_real=$rutin_infaq_realisasi + $rutin_zakat_realisasi;
if ($sumrutin_target <=0){
	$str_persen_ambil="CEK TARGET";
}elseif($sumrutin_real / $sumrutin_target > 1 ){
	$str_persen_ambil="CEK REALISASI";
}else{
	$persen_ambil= ($sumrutin_real / $sumrutin_target)*100;
	$str_persen_ambil= $persen_ambil."%";
}
?>
		<td><input type="text" name="rutin_infaq_target_<?=$i?>" id="rutin_infaq_target_<?=$i?>" value="<?php echo $rutin_infaq_target?>"></td>
		<td><input type="text" name="rutin_infaq_realisasi_<?=$i?>" id="rutin_infaq_realisasi_<?=$i?>" value="<?php echo $rutin_infaq_realisasi?>"></td>
		<td><input type="text" name="rutin_zakat_target_<?=$i?>" id="rutin_zakat_target_<?=$i?>" value="<?php echo $rutin_zakat_target?>"></td>
		<td><input type="text" name="rutin_zakat_realisasi_<?=$i?>" id="rutin_zakat_realisasi_<?=$i?>" value="<?php echo $rutin_zakat_realisasi?>"></td>
		<td><input type="text" name="rutin_infaq_selisih_<?=$i?>" id="rutin_infaq_selisih_<?=$i?>" value="<?php echo $rutin_infaq_selisih?>"></td>
		<td><input type="text" name="insi_infaq_paid_<?=$i?>" id="insi_infaq_paid_<?=$i?>" value="<?php echo $insi_infaq_paid?>"></td>
		<td><input type="text" name="rutin_zakat_selisih_<?=$i?>" id="rutin_zakat_selisih_<?=$i?>" value="<?php echo $rutin_zakat_selisih?>"></td>
		<td><input type="text" name="insi_zakat_paid_<?=$i?>" id="insi_zakat_paid_<?=$i?>" value="<?php echo $insi_zakat_paid?>"></td>
		<td><input type="text" name="insi_zakat_fitrah_<?=$i?>" id="insi_zakat_fitrah_<?=$i?>" value="<?php echo $insi_zakat_fitrah?>"></td>
		<td  ><input type="text" name="wakaf_icmb_<?=$i?>" id="wakaf_icmb_<?=$i?>" value="<?php echo $insi_wakaf_icmb?>"></td>
		<td  ><input type="text" name="wakaf_stainim_<?=$i?>" id="wakaf_stainim_<?=$i?>" value="<?php echo $insi_wakaf_stainim?>"></td>
		<td  ><input type="text" name="wakaf_masjid_<?=$i?>" id="wakaf_masjid_<?=$i?>" value="<?php echo $insi_wakaf_masjid?>"></td>

		<td  ><input type="text" name="non_wakaf_quran_<?=$i?>" id="non_wakaf_quran_<?=$i?>" value="<?php echo $non_wakaf_quran?>"></td>
		<td  ><input type="text" name="non_wakaf_qurban_<?=$i?>" id="non_wakaf_qurban_<?=$i?>" value="<?php echo $non_wakaf_qurban?>"></td>
		<td  ><input type="text" name="non_wakaf_bencana_<?=$i?>" id="non_wakaf_bencana_<?=$i?>" value="<?php echo $non_wakaf_bencana?>"></td>
		<td  ><input type="text" name="non_wakaf_fidyah_<?=$i?>" id="non_wakaf_fidyah_<?=$i?>" value="<?php echo $non_wakaf_fidyah?>"></td>
		
		<td ><?php echo $str_persen_ambil?></td> 
<?	

	//get key yg dipakai smua komp
	$totPendapatan=0; 
	$totPotongan=0;
	
	$acuan_trans = $hasil->ACUAN_TRANSPORT;					
	$tunj_transport = $hasil->TUNJ_TRANSPORT;
	$tunj_pengambilan= $hasil->TUNJ_PENGAMBILAN; $str_tunj_pengambilan="";
	$insentif_pengambilan= $hasil->INSENTIF_PENGAMBILAN; $str_insentif_pengambilan="";
	$bonus_prestasi= $hasil->BONUS_PRESTASI; $str_bonus_prestasi="";
	$bonus_pengembangan= $hasil->BONUS_PENGEMBANGAN;
	$insi_zakat_wakaf_tunai= $hasil->INSI_ZAKAT_WAKAF_TUNAI;
	$pengembalian_40= $hasil->PENGEMBALIAN_40;
	$tunj_jabatan = $hasil->TUNJ_JABATAN;
	$tunj_prestasi = $hasil->TUNJ_PRESTASI;
	$bonus_patungan_sapi = $hasil->BONUS_PATUNGAN_SAPI;
	$bonus_qurban_sapi = $hasil->BONUS_QURBAN_SAPI;
	$koreksi = $hasil->KOREKSI;
	$penyesuaian = $hasil->PENYESUAIAN;

$total_fundraising = $sumrutin_real+$rutin_infaq_selisih+$rutin_zakat_selisih +$insi_infaq_paid + $insi_zakat_paid+$insi_zakat_fitrah+$insi_wakaf_icmb+$insi_wakaf_stainim+$insi_wakaf_masjid+$non_wakaf_quran+$non_wakaf_qurban+$non_wakaf_bencana+$non_wakaf_fidyah;


$totPendapatan=$tunj_transport+$tunj_pengambilan+$insentif_pengambilan+$bonus_prestasi+$bonus_pengembangan+$insi_zakat_wakaf_tunai+$tunj_jabatan+$tunj_prestasi +$bonus_patungan_sapi + $bonus_qurban_sapi+ $koreksi+ $penyesuaian;

$pot_dansos=$hasil->POT_DANSOS;
$zakat=$hasil->POT_ZAKAT;
$lain_lain=$hasil->LAIN_LAIN;
$angsuran=$hasil->ANGSURAN;
$totPotongan=$pot_dansos+$zakat+$lain_lain+$angsuran;

?>

<td><input type="hidden" name="acuan_transport_<?=$i?>" id="acuan_transport_<?=$i?>" value="<?php echo $acuan_trans ?>"><?php echo number_format($acuan_trans,0,',','.')?>
<?=form_input(array('name'=>'tunj_transport_'.$i,'id'=>'tunj_transport_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$tunj_transport, "readonly"=>true ));?></td>
<td>
<input type="hidden" name="tunj_pengambilan_<?=$i?>" id="tunj_pengambilan_<?=$i?>" value="<?php echo $tunj_pengambilan ?>">
<?=form_input(array('name'=>'strtunj_pengambilan_'.$i,'id'=>'strtunj_pengambilan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>($str_tunj_pengambilan==""?$tunj_pengambilan:$str_tunj_pengambilan), "readonly"=>true ));?>
</td>
<td>
<input type="hidden" name="insentif_pengambilan_<?=$i?>" id="insentif_pengambilan_<?=$i?>" value="<?php echo $insentif_pengambilan ?>">
<?=form_input(array('name'=>'str_insentif_pengambilan_'.$i,'id'=>'str_insentif_pengambilan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>($str_insentif_pengambilan==""?$insentif_pengambilan:$str_insentif_pengambilan), "readonly"=>true ));?>
</td>
<td><input type="hidden" name="bonus_prestasi_<?=$i?>" id="bonus_prestasi_<?=$i?>" value="<?php echo $bonus_prestasi ?>">
<?=form_input(array('name'=>'str_bonus_prestasi_'.$i,'id'=>'str_bonus_prestasi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>($str_bonus_prestasi==""?$bonus_prestasi:$str_bonus_prestasi), "readonly"=>true ));?>
</td>
<td><?=form_input(array('name'=>'bonus_pengembangan_'.$i,'id'=>'bonus_pengembangan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$bonus_pengembangan, "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'insi_zakat_wakaf_tunai_'.$i,'id'=>'insi_zakat_wakaf_tunai_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$insi_zakat_wakaf_tunai, "readonly"=>true ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'bonus_patungan_sapi_'.$i,'id'=>'bonus_patungan_sapi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$bonus_patungan_sapi, "onkeyup"=>"countRevenue(".$i.", this)", "onkeypress"=>"return numericVal(this,event)"   ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'bonus_qurban_sapi_'.$i,'id'=>'bonus_qurban_sapi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$bonus_qurban_sapi,"onkeyup"=>"countRevenue(".$i.", this)", "onkeypress"=>"return numericVal(this,event)"  ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'koreksi_'.$i,'id'=>'koreksi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$koreksi,"onkeyup"=>"countRevenue(".$i.", this)", "onkeypress"=>"return numericVal(this,event)"  ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'penyesuaian_'.$i,'id'=>'penyesuaian_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$penyesuaian, "onkeyup"=>"countRevenue(".$i.", this)", "onkeypress"=>"return numericVal(this,event)"   ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'pengembalian_40_'.$i,'id'=>'pengembalian_40_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$pengembalian_40, "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'tunj_jabatan_'.$i,'id'=>'tunj_jabatan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$tunj_jabatan, "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'tunj_prestasi_'.$i,'id'=>'tunj_prestasi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$tunj_prestasi, "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'totPendapatan_'.$i,'id'=>'totPendapatan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan,0) ));?></td>

<td><?=form_input(array('name'=>'pot_dansos_'.$i,'id'=>'pot_dansos_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$pot_dansos, "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'pot_zakat_'.$i,'id'=>'pot_zakat_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$zakat, "readonly"=>true ));?></td>
<!-- angsuran/cicilan MYM saat ini di set di gaji transport -->
<td><input type="hidden" name="cicilke_<?=$i?>" id="cicilke_<?=$i?>" value="0">
<?=form_input(array('name'=>'angsuran_'.$i,'id'=>'angsuran_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$angsuran, "readonly"=>true ));?></td>
<td bgcolor="#ffffcc"><?=form_input(array('name'=>'lain_lain_'.$i,'id'=>'lain_lain_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$lain_lain ,  "onkeyup"=>"countExpense(".$i.", this)", "onkeypress"=>"return numericVal(this,event)"));?></td>
<td><?=form_input(array('name'=>'totPotongan_'.$i,'id'=>'totPotongan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPotongan,0) , "readonly"=>true));?></td>
<td><?=form_input(array('name'=>'totTerima_'.$i,'id'=>'totTerima_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan-$totPotongan,0), "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'total_fundraising_'.$i,'id'=>'total_fundraising_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($total_fundraising,0), "readonly"=>true ));?></td>

<!-- GENERATE/DOWNLOAD -->
		<?	if( $sts=="edit" || $sts=="disabled"){
			$param=$tahun."_".$hasil->NIK;
			//CEK FILE_SLIP
			$strFile="select count(*) CKFILE from file_slip where thn='$thn' and bln='$bln' and jenis='zisco_bonus' and nik='".$hasil->NIK."'";
			$rsFile=$this->db->query($strFile)->row();
			echo "<td>";
			if ($rsFile->CKFILE >=1){	//ada -> tombol download
				$rsPath=$this->db->query("select * from file_slip where thn='$thn' and bln='$bln' and jenis='zisco_bonus' and nik='".$hasil->NIK."'")->row();
		?>	&nbsp;-&nbsp;<br>
			<a href="javascript:void(0)" onclick="window.open('<?=base_url($rsPath->PATH."/".$rsPath->NAMA_FILE)?>','_blank')"><i class="fa fa-print" title="Print/Download File">Download/Print</i></a>
		<?
			}else{	
			
		?>
		<a href="javascript:void(0)" data-url="<?=base_url('gaji_staf/cetak_slip/')?>" data-id="<?=$param?>" onclick="singleSlip(this)"><i class="fa fa-edit" title="Generate Slip to .pdf File">Generate&nbsp;pdf</i></a>
		<?}
				echo "</td>";
			}else{
				echo "<td>&nbsp; link</td>";
			}
			?>

<td><?php echo number_format($tunj_transport,0,',','.')?></td>
<td><?=form_input(array('name'=>'payroll_bonus_'.$i,'id'=>'payroll_bonus_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan-($tunj_transport+$tunj_jabatan),0), "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'pot_payroll_bonus_'.$i,'id'=>'pot_payroll_bonus_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPotongan,0), "readonly"=>true ));?></td>
<td><?=form_input(array('name'=>'total_payroll_bonus_'.$i,'id'=>'total_payroll_bonus_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan-($totPotongan+$tunj_transport+$tunj_jabatan),0), "readonly"=>true ));?></td>


<?		


		$i++;
		}
	}
?>			

                                    </tbody>
                                </table>
                            </div> <!-- /.table-responsive -->
                       
                        
						<div class="row">
							<div class="col-md-12">
							<? 	if (sizeof($row)>0){ 
									
							?>								
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">	
										<input type="hidden" name="bln" id="bln" value="<?=$bln?>">	
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">
										<input type="hidden" name="id_validasi" id="id_validasi" value="<?=$id_validasi?>">
										<input type="hidden" name="id_cabang" id="id_cabang" value="<?=$id_cabang?>">
									<? if ($sts=="new" || $sts=="edit"){ ?>
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan">		
							<?
									}

									if( $sts=="edit" || $sts=="disabled"){
												$btCSV = array(
													'type'=>'button',
													'name'=>'btCsvBank',
													'id'=>'btCsvBank',
													'title'=>'Cetak CSV Transport Zisco',
													'value'=>'Export CSV Bank',
													'class'=>'btn btn-primary'
												);
												$btXls = array(
												'type'=>'button',
												'name'=>'btXls',
												'id'=>'btXls',
												'title'=>'Ekspor Xls',
												'value'=>'Ekspor Xls',
												'class'=>'btn btn-primary'
											);
											echo "&nbsp;".form_input($btCSV)."&nbsp;".form_input($btXls);
											?>
											<input type="button" id="doprogress" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Cetak Slip untuk data terakhir yang sudah tersimpan saja" onclick="cetakprogress()" value="Buat Slip gaji ">
											<? if( $sts=="edit"){ ?>
											&nbsp;
											<input type="button" id="btvalidasi" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Validasi Penggajian" value="Validasi">
											<? }
										}	
											
								}
								
								$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('gaji_zisco_bonus/index')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
							?>
							</div><!-- col -->
						</div><!-- row -->

						<br>
								<div class="row">
											<div class="col-md-8">
												<div class="progress no-display progress-lg">
													<div class="progress-bar progress-bar-success" id="progressBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
													0%
													</div>
												</div>
											</div>
										</div> 	
             
</div>
<hr />
</div>

<?php echo form_close();?>
<script type="text/javascript">

$('#myTable').DataTable( {
	//"bJQueryUI": true,
	"scrollY": "500px",
	"scrollX": true,
	"scrollCollapse": true,
	"paging": false, 
	"searching": false, 
	fixedColumns:   {
            leftColumns: 3
        },
    fixedHeader: true
} );
$('#btsimpankel').click(function(){
	
		var form_data = $('#myform').serialize();
		$().showMessage('Sedang diproses.. Harap tunggu..');
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('gaji_zisco_bonus/save_gaji_zisco_bonus');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					
					$().showMessage('Data Gaji Bonus zisco berhasil disimpan.','success',1000);
					setInterval(window.location.reload(), 3000);
					
				} else {
					$().showMessage('Terjadi kesalahan. Data gagal disimpan.','danger',700);
					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
		
	});
	
	function goPengembalian40(obj, idx){
		var myval=parseFloat(obj.value) * 0.4 * -1;
		$("#pengembalian_40_"+idx).val( Math.floor(myval) );
		countRevenue(idx, obj);
	}
	function countRevenue(idx, obj){

		var total= parseFloat( $("#tunj_transport_"+idx ).val()) +  parseFloat( $("#tunj_pengambilan_"+idx ).val()) +  parseFloat( $("#insentif_pengambilan_"+idx ).val())
			+  parseFloat( $("#bonus_prestasi_"+idx).val())	+ parseFloat( $("#bonus_pengembangan_"+idx ).val()) +  parseFloat( $("#insi_zakat_wakaf_tunai_"+idx ).val())
			+  parseFloat( $("#bonus_patungan_sapi_"+idx ).val()) +  parseFloat( $("#bonus_qurban_sapi_"+idx).val())+  parseFloat( $("#koreksi_"+idx ).val())
			+  parseFloat( $("#penyesuaian_"+idx ).val()) +  parseFloat( $("#pengembalian_40_"+idx ).val()) +  parseFloat( $("#tunj_jabatan_"+idx).val()) +  parseFloat( $("#tunj_prestasi_"+idx).val())	;
		
		//total pendptn bruto, total pend diterima, tot pend netto (payroll bonus diterima), dansos zakat update
		$("#totPendapatan_"+idx).val(total);

		var dansos=0;
		if (total<3203846){
			dansos=Math.ceil(total * 0.025);
		}else if (total>=3203846){
			dansos=Math.ceil(total * 0.01);
		}
		$("#pot_dansos_"+idx).val(dansos);
		countExpense(idx,obj);
		//setInterval(countExpense(idx,obj), 3000);

		$("#totTerima_"+idx).val( parseFloat( $("#totPendapatan_"+idx).val() ) - parseFloat(  $("#totPotongan_"+idx).val()) ) ;
		var payroll_bonus = total - parseFloat( $("#tunj_transport_"+idx ).val()) ;
		var payroll_bonus_diterima = total - ( parseFloat( $("#totPotongan_"+idx ).val()) + parseFloat( $("#tunj_transport_"+idx ).val()));
		$("#payroll_bonus_"+idx).val(payroll_bonus);
		$("#pot_payroll_bonus_"+idx).val( parseFloat(  $("#totPotongan_"+idx).val())  );
		$("#total_payroll_bonus_"+idx).val( payroll_bonus_diterima ) ;
	}
	function countExpense(idx,  obj){
		var total_pot= parseFloat( $("#pot_dansos_"+idx).val() ) +  parseFloat( $("#pot_zakat_"+idx).val() ) +  parseFloat( $("#angsuran_"+idx).val() ) +  parseFloat( $("#lain_lain_"+idx).val() );
		var total = parseFloat( $("#totPendapatan_"+idx).val() ) ;
		
		$("#totPotongan_"+idx).val(total_pot);
		$("#totTerima_"+idx).val( parseFloat( $("#totPendapatan_"+idx).val() ) - parseFloat(  $("#totPotongan_"+idx).val()) ) ;
		var payroll_bonus = total - parseFloat( $("#tunj_transport_"+idx ).val()) ;
		var payroll_bonus_diterima = total - ( parseFloat( $("#totPotongan_"+idx ).val()) + parseFloat( $("#tunj_transport_"+idx ).val()));
		$("#payroll_bonus_"+idx).val(payroll_bonus);
		$("#pot_payroll_bonus_"+idx).val( parseFloat(  $("#totPotongan_"+idx).val())  );
		$("#total_payroll_bonus_"+idx).val( payroll_bonus_diterima ) ;
	}


	$('#btvalidasi').click(function(){	
	$.ajax({
			type: 'POST',
			url: '<?php echo base_url('gaji_zisco_bonus/validasi');?>',
			data: { id_validasi: $("#id_validasi").val()},				
			dataType: 'json',
			success: function(msg) {
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data  Gaji Transport zisco berhasil divalidasi.','success',1000);
					setInterval(window.location.reload(), 3000);
				} else {
					$().showMessage('Terjadi kesalahan. Data gagal disimpan.','danger',700);
					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
});	
$('#btXls').click(function() {		 
		//var id_validasi=$('#id_validasi').val();		
		var pilih=confirm('Export File ke Excel ?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('gaji_zisco_bonus/gaji_list'); ?>",
				dataType: 'json',
				type: 'POST',
				data: "cbBulan="+$("#bln").val()+"&cbTahun="+$("#thn").val()+"&id_cabang="+$("#id_cabang").val()+"&isXls=1",				
				success: function(data,  textStatus, jqXHR){					
					//alert("File csv sudah tersimpan");
					$().showMessage('File excel sudah digenerate.','success',1000);
					window.open('<?=base_url("'+data.isi+'")?>','_blank');
					//alert(data.isi);
					
				} 
			});			
		}
		
	});
$('#btCsvBank').click(function() {		 
		var id_validasi=$('#id_validasi').val();
		
		var pilih=confirm('Export File ke CSV?');
		
		if (pilih==true) {
			$.ajax({
				url: "<?php echo base_url('gaji_zisco_bonus/exportCsv'); ?>",
				dataType: 'json',
				type: 'POST',
				data: "id_validasi="+id_validasi,				
				success: function(data,  textStatus, jqXHR){					
					//alert("File csv sudah tersimpan");
					$().showMessage('File csv sudah digenerate.','success',1000);
					window.open('<?=base_url("'+data.isi+'")?>','_blank');
					//alert(data.isi);
					
				} 
			});			
		}
		
	});

$('#doprogress').click(function(){
	cetakprogress();
	});

function cetakprogress(cnt,step){
	if(typeof cnt === 'undefined'){
		var data = {id_validasi:<?=$id_validasi?>};
	} else {
		var data = {id_validasi:<?=$id_validasi?>,cnt:cnt,step:step};
	}
	$.ajax({
		url: "<?php echo base_url('gaji_zisco_bonus/slipLoop'); ?>",
		dataType: 'json',
		type: 'GET',
		data: data,
		success: function(respon){
			if (respon.status==1){
				$('.progress').fadeSlide("show");
				if(respon.jumlah){
					nextcnt = respon.jumlah;
				} else {
					nextcnt = cnt;
				}
				
				if(typeof respon.nextstep === 'undefined'){
					nextstep = 1;
				} else {
					nextstep = respon.nextstep;
				}
				
				if(typeof respon.percent === 'undefined'){
					percent = 0;
				} else {
					percent = respon.percent;
				}
				
				
				if (respon.complete != 1){
					$('#progressBar').css('width',percent+'%').html(percent+'%');
					cetakprogress(nextcnt,nextstep);
				}else{
					bootbox.alert("Progress Done.");
					$('.progress').fadeSlide("hide");
					window.location.reload();
				}
				/*var jum = respon.jumlah;
				$('.progress').show();
				for(var i=1;i<=jum;i++){
					
				}*/
			} else {
				$('.progress').fadeSlide("hide");
			}
		}
	});
}
</script>