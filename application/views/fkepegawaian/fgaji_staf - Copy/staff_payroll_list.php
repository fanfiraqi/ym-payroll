<?php echo form_open('payroll_staff/save_gaji_staff',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success">
	Form Rekap Gaji : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Edit = Sudah ada data yang disimpan, dan ada data baru sebagai update (mis. Jml hadir, jml alpa dari rekap absensi) <br>
	&nbsp;&nbsp;3. View/Disabled = Melewati Bulan aktif<br>
	"Slip-Email All" yang dikirim hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut terkirim<br>
	
	</div>
		<div class="panel panel-default">
			<div class="panel-heading">Daftar Gaji & Tunjangan Staff</div><!-- /.panel-heading -->
				<div class="panel-body" style="overflow:scroll;">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
                           <thead>
                           <tr >
							<th rowspan="2">NO</th>
							<th rowspan="2">NIK</th>
							<th rowspan="2">NAMA</th>
							<th rowspan="2">JABATAN</th>
							<th colspan="4" align="center">1</th>
							<th colspan="3" align="center">2</th>
							<th colspan="2" align="center">3</th>
							<th align="center" colspan="2"> 4</th>
							<th align="center" colspan="2"> 5</th>
							<th colspan="7" align="center">6 (POTONGAN)</th>
							<th rowspan="2">TOTAL&nbsp;GAJI</th>
							<th rowspan="2">SLIP</th>
						  </tr>
						  <tr>
							<th>MASA&nbsp;KERJA</th>
							<th>GAPOK</th>
							<th>T.&nbsp;JABATAN</th>
							<th>T.&nbsp;MASA&nbsp;KERJA</th>							
							<th>JML&nbsp;HADIR</th>
							<th>UANG&nbsp;MAKAN</th>
							<th>T.&nbsp;TRANSPORT</th>
							<th>ANAK DAPAT TUNJANGAN</th>
							<th>TOTAL T.&nbsp;ANAK</th>
							<th>NILAI ISO</th>
							<th>T.&nbsp;ISO</th>
							<th>NILAI UBUDIAH</th>
							<th>T.&nbsp;UBUDIAH</th>
							<th>JML&nbsp;ALPA</th>
							<th>POT.&nbsp;GAJI</th>
							<th>JML&nbsp;ANGSURAN</th>
							<th>ANGSURAN&nbsp;KE</th>
							<th>TA'AWUN</th>
							<th>POT. PREMI JHT</th>
							<th>POT. PREMI JHT KE</th>
						   
						  </tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr><td colspan=20>".sizeof($row)."</td></tr>";
$i=1;
	if (sizeof($row)==0){		
		
		echo "<tr align=center><td colspan=20>Data Belum Ada</td></tr>";
	}else{
	$blnIdk=$this->arrIntBln;
	//cek var bln tahun rekap absensi value DARI BULAN SEBLMNYA
	if ($bln==1){
		$bln_pre=12;
		$thn_pre=$thn-1;
	}else{
		$bln_pre=$blnIdk[$bln-1];
		$thn_pre=$thn;
	}
	foreach($row as $hasil){ 
		
		//get set_gaji_staff value, if belum di set, tampilkan row message data master gaji staff nik ini belum di set
		$strMaster="select * from set_gaji_staff where nik='".$hasil->NIK."'";
		$qMaster=$this->db->query($strMaster);
		if ($qMaster->num_rows()>=1){
			
			$masakerja=$hasil->SELISIH;
			//hitung masa kerja
			if ($masakerja<12){				
				$strmasakerja=number_format($hasil->SELISIH,0,',','')." Bln";	
				$thnMasakerja=0;
			}else{
				$strmasakerja=floor($hasil->SELISIH/12)." Thn, ".($hasil->SELISIH%12)." Bln";
				$thnMasakerja=floor($hasil->SELISIH/12);
			}

			$rsGapok=$this->db->query("select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=1")->row();			
			//get rekap absensi value DARI BULAN SEBLMNYA
			//hari kerja ambil dari database
			$jml_hadir=0;
			$jml_alpa=0;
			$skerja="select JML_MASUK, JML_ALPA from rekap_absensi where periode='".$thn_pre.$bln_pre."' and nik='".$hasil->NIK."'";
			if ($this->db->query($skerja)->num_rows()>0){
				$rskerja=$this->db->query($skerja)->row();
				$jml_hadir=$rskerja->JML_MASUK;
				$jml_alpa=$rskerja->JML_ALPA;
			}
			
			//penambahan cuti_khusus_alpa
			/*$sIjin="select sum(JML_HARI) ALPA_IJIN from ijin_khusus_alpa where thn='".$thn_pre."' and bln='".$bln_pre."' and nik='".$hasil->NIK."'";
			$alpaijin=0;
			if ($this->db->query($sIjin)->num_rows()>0){
				$rsIjin=$this->db->query($sIjin)->row();
				$alpaijin=$rsIjin->ALPA_IJIN;
				$jml_alpa-=$rsIjin->ALPA_IJIN;
			}*/ 
			//diedit untuk dimasukkan ke proses rekap absensi

			$rsUangMakan=$this->db->query("select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=4")->row();
			//$rsTransport=$this->db->query("select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=5")->row();
			$rsJabatan=$this->db->query("select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=2")->row();
			$rsMasaKerja=$this->db->query("select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=3")->row();
			
			$rsTaawun=$this->db->query("select ifnull(NOMINAL,0) NOM from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=9")->row();
			if (sizeof($rsTaawun)<=0){
				$taawun=0;
			}else{
				$taawun=($masakerja>=10?$rsTaawun->NOM:0);
			}

			$premi_jht=0;
			$subsidi_jht=0;
			$jht_ke=0;
			//cek pegawai ikut/tidak jht
			$rsCekJht=$this->db->query("select PROGRAM_JHT from pegawai where nik='".$hasil->NIK."'")->row();
			if ($rsCekJht->PROGRAM_JHT==1){
				$rsPremijht=$this->db->query("select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=11")->row();
				$rsSubsidi=$this->db->query("select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=10")->row();
				$premi_jht=($masakerja>=37?(sizeof($rsPremijht)<=0?0:$rsPremijht->NOMINAL):0);
				$subsidi_jht=($masakerja>=37?(sizeof($rsSubsidi)<=0?0:$rsSubsidi->NOMINAL):0);
				$jht_ke=($masakerja>=37?$hasil->JHT_KE + 1:0);	//krn new, jht_ke + 1
				
			}
			
			$transport=0;
			$gapok=($masakerja<3?($rsGapok->NOMINAL)*0.8:$rsGapok->NOMINAL);
			$uangMakan=$rsUangMakan->NOMINAL*$jml_hadir;
			//$transport=$rsTransport->NOMINAL*$jml_hadir;
			$tunjJabatan=$rsJabatan->NOMINAL;
			
			$potGapok=$jml_alpa*($rsGapok->NOMINAL/25);
		//get tunjangan anak value
		$strTunjAnak="SELECT `NIK` , `ID_HUBKEL` , `ANAK_KE` , `ID_PENDIDIKAN`,ms.KEYWORD, ms.NOMINAL, ms.ISACTIVE
				FROM `adm_hubkel` adm, mst_tunjangan_anak ms
				WHERE `ID_HUBKEL` =2 and (id_pendidikan between 1 and 5) and nik='".$hasil->NIK."'
				and ms.id=adm.id_pendidikan 
				ORDER BY NIK , anak_ke ASC limit 4";
		$rsTunjAnak=$this->db->query($strTunjAnak)->result();
		
		//KOLOM PINJAMAN, IF GAJI=NEW CEK PINJ_HEADER NIK, TGL PINJAM, STATUS, CEK CICILAN KE 1, JIKA JMLBAYAR=0, MK PEMBAYARAN PERTAMA BARU BISA JIKA TGL MULAI PINJAM + 1 BLN
		$cicilke=0;
		$jmlcicilan=0;
		$id_header="";
		$cekPinj="select h.nik,a.ID_HEADER, a.CICILAN_KE, a.JML_CICILAN, a.JML_BAYAR, h.tgl, DATE_FORMAT(h.tgl, '%Y%m') thnblnpinjam,
				DATE_ADD(tgl, INTERVAL 1 MONTH) tglhrsbayar, DATE_FORMAT(DATE_ADD(tgl, INTERVAL 1 MONTH),'%Y%m') thnblnhrsbayar
				FROM pinjaman_header h,  `pinjaman_angsuran` a 
				WHERE h.id=a.id_header and h.nik='".$hasil->NIK."' and h.status='Belum Lunas' and a.cicilan_ke=1 ";
		$qPinjaman=$this->db->query($cekPinj)->row();
		if (sizeof($qPinjaman)>=1){
			//cek jmlbayar, if =0 maka masih cicilan ke 1
			if ($qPinjaman->JML_BAYAR<=0 && $thn.$bln>=$qPinjaman->thnblnhrsbayar){				
				//cek lagi tgl mulai pinjam
				$cicilke=1;
				$id_header=$qPinjaman->ID_HEADER;
				$jmlcicilan=$qPinjaman->JML_CICILAN;
			}else{
				//jmlbayar cicilke1 >=0 maka cari cicilan ke berapa
				$strPinj="SELECT a.ID_HEADER, a.CICILAN_KE, a.JML_CICILAN
				FROM pinjaman_header h,  `pinjaman_angsuran` a 
				WHERE h.id=a.id_header and h.status='Belum Lunas' and h.nik='".$hasil->NIK."' and jml_bayar=0 and ISNULL(tgl_bayar)=1
				order by cicilan_ke asc	limit 1		";
				$rsPinj=$this->db->query($strPinj)->row();
				$cicilke=$rsPinj->CICILAN_KE;
				$jmlcicilan=$rsPinj->JML_CICILAN;
				$id_header=$rsPinj->ID_HEADER;

			}
		}
		
		//tunjangan masa kerja
		//$thnMasakerja
		$mstTunjMasaKerja=$rsMasaKerja->NOMINAL;		
		if ($thnMasakerja>=0 && $thnMasakerja<=5){			
			$tunMasaKerja=$thnMasakerja*$mstTunjMasaKerja;
		}else{			
			$thnMasakerja=$thnMasakerja-5;
			$tunMasaKerja=(5*$mstTunjMasaKerja)+$thnMasakerja*($mstTunjMasaKerja*2);
		}
		
		?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td><?=form_input(array('name'=>'strMasaKerja_'.$i,'id'=>'strMasaKerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$strmasakerja));?><input type="hidden" name="masakerja_<?=$i?>" id="masakerja_<?=$i?>" value="<?=$masakerja?>"></td>
		<td style="background-color:<?=($masakerja<3?"#fecba5":"#c4ecb3")?>"><?=form_input(array('name'=>'gapok_'.$i,'id'=>'gapok_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$gapok));?></td>
		<td><?=form_input(array('name'=>'tunjJabatan_'.$i,'id'=>'tunjJabatan_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$tunjJabatan));?></td>
		<td><?=form_input(array('name'=>'tunjMasakerja_'.$i,'id'=>'tunjMasakerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$tunMasaKerja));?></td>
		
		<td><?=form_input(array('name'=>'jmlhadir_'.$i,'id'=>'jmlhadir_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$jml_hadir));?></td>
		<td><?=form_input(array('name'=>'uangMakan_'.$i,'id'=>'uangMakan_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$uangMakan));?></td>
		<td><?=form_input(array('name'=>'transport_'.$i,'id'=>'transport_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$transport));?></td>
		<td><?


		$jumTa=0;
		$jumAnak=0;

		if ($masakerja<3){
			echo "Blm Dpt";
		}else{			
			foreach($rsTunjAnak as $rsta){
				if ($rsta->ISACTIVE==0){
					echo "Tunj ".$rsta->KEYWORD." nonaktif<br>";
					$jumTa+=0;
					$jumAnak++;
				}else{
					echo $rsta->KEYWORD.":".$rsta->NOMINAL."<br>";
					$jumTa+=$rsta->NOMINAL;
					$jumAnak++;
				}
			}
		}
		//tunj fungsional = daily report * nominal t.fungsional master
		$dailyReport=0;
		$strDR="select JML_HARI from daily_report where bln='".$bln_pre."' and thn='".$thn_pre."' and nik='".$hasil->NIK."'";
			if ($this->db->query($strDR)->num_rows()>0){
				$rskerja=$this->db->query($strDR)->row();
				$dailyReport=$rskerja->JML_HARI;
			}
			//awalnya tunjangan fungsional -> judul berubah menjadi T. ISO
		$tunjFungsional=0;
		if ($rsFungsional=$this->db->query("select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=6")->row()){
			$tunjFungsional=$dailyReport*$rsFungsional->NOMINAL;
		}
		?></td>
		<td><?=form_input(array('name'=>'tunjAnak_'.$i,'id'=>'tunjAnak_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>($masakerja<3?0:$jumTa)));?><input type="hidden" name="jml_anak_<?=$i?>" id="jml_anak_<?=$i?>" value="<?=$jumAnak?>"></td>
		
		<td><?=form_input(array('name'=>'dailyReport_'.$i,'id'=>'dailyReport_'.$i,'class'=>'myform-control','size'=>5,'readonly'=>true, 'value'=>$dailyReport));?></td>
		<td><?=form_input(array('name'=>'tunjFungsional_'.$i,'id'=>'tunjFungsional_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$tunjFungsional));?></td>
		<?
		//tunj ubudiah = nilai * nominal t.ubudiah master
		$ubudiah=0;
		$strDR="select JML_HARI from ubudiah_staff where concat( thn, bln )='".$thn_pre.$bln_pre."' and nik='".$hasil->NIK."'";
			if ($this->db->query($strDR)->num_rows()>0){
				$rskerja=$this->db->query($strDR)->row();
				$ubudiah=$rskerja->JML_HARI;
			}
		// tunjangan  -> judul berubah menjadi T. UBUDIAH
		$tunjUbudiah=0;
		$strkomp="select NOMINAL from set_gaji_staff where nik='".$hasil->NIK."' and id_komp_gaji=8";
		if ($rsFungsional=$this->db->query($strkomp)->row()){
			$tunjUbudiah=$ubudiah*$rsFungsional->NOMINAL;
		}
		
		?>
	<td><?=form_input(array('name'=>'ubudiah_'.$i,'id'=>'ubudiah_'.$i,'class'=>'myform-control','size'=>5,'readonly'=>true, 'value'=>$ubudiah));?></td>
		<td><?=form_input(array('name'=>'tunjUbudiah_'.$i,'id'=>'tunjUbudiah_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$tunjUbudiah));?></td>
		
		<td><?=form_input(array('name'=>'jmlAlpa_'.$i,'id'=>'jmlAlpa_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$jml_alpa));?></td>
		<td><?=form_input(array('name'=>'potGapok_'.$i,'id'=>'potGapok_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$potGapok));?></td>
		<td><?=form_input(array('name'=>'jmlcicilan_'.$i,'id'=>'jmlcicilan_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$jmlcicilan));?></td>
		<td><?=form_input(array('name'=>'cicilke_'.$i,'id'=>'cicilke_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$cicilke));?><input type="hidden" name="pinj_idheader_<?=$i?>" id="pinj_idheader_<?=$i?>" value="<?=$id_header?>"></td>

		<td title="taawun tidak mengurangi gaji (tunjangan dan sedekah)"><?=form_input(array('name'=>'taawun_'.$i,'id'=>'taawun_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$taawun));?></td>

		<td><?=form_input(array('name'=>'premi_jht_'.$i,'id'=>'premi_jht_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$premi_jht));?><input type="hidden" name="hiddenSubsidi_jht_<?=$i?>" id="hiddenSubsidi_jht_<?=$i?>" value="<?=$subsidi_jht?>"></td>

		<td><?=form_input(array('name'=>'jht_ke_'.$i,'id'=>'jht_ke_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$jht_ke));?></td>


		<?	//hitung total sementara
			$total=($gapok+$uangMakan+$transport+$tunjJabatan+$tunMasaKerja+$jumTa+$tunjFungsional+$tunjUbudiah)-($potGapok+$jmlcicilan+$premi_jht);
			?>
		<td><?=form_input(array('name'=>'totalGaji_'.$i,'id'=>'totalGaji_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$total));?><input type="hidden" name="hidTotAwal_<?=$i?>" id="hidTotAwal_<?=$i?>" value="<?=$total?>">
		
		</td><td>&nbsp;</td>
		
	  </tr>		
		
	<? 
		}else{
			echo "<tr ><td>$i";
			?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="0"><?
			echo "</td>";
			echo "<td colspan=20>Data master gaji staff : <b>".$hasil->NIK." - ".$hasil->NAMA."</b> ini belum di set</td></tr>";
		}
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
										<input type="hidden" name="penanda_jht_ke" id="penanda_jht_ke" value="new">										
										<input type="hidden" name="bln" id="bln" value="<?=$digitBln?>">										
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">	
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan">
											<?php 
											/*$btsubmit = array(
													'name'=>'btsubmit',
													'id'=>'btsubmit',
													'value'=>'Simpan',
													'class'=>'btn btn-primary'
												);
											echo form_submit($btsubmit);*/
											$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Batal',
												'onclick'=>"backTo('".base_url('payroll_staff/payroll_filter')."');return false;",
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
												'onclick'=>"backTo('".base_url('payroll_staff/payroll_filter')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "<br>".form_button($btback);
						}?>
                </div>
</div>
<hr />
<?php echo form_close();?>
<script type="text/javascript">
/*$('#myform').submit(function(event) {
	$(this).saveForm('<?php echo base_url('payroll_staff/save_gaji_staff');?>','<?php echo base_url('payroll_staff/payroll_filter');?>');
	event.preventDefault();
});*/
$('#btsimpankel').click(function(){		
		var form_data = $('#myform').serialize();
		$().showMessage('Sedang diproses.. Harap tunggu..');
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('payroll_staff/save_gaji_staff');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data Penggajian berhasil disimpan.','success',1000);
					window.location.reload();
					//$("#divformkel").fadeSlide("hide");
					//$('#dataTables-cab').dataTable().fnReloadAjax();
					
				} else {
					$().showMessage('Terjadi kesalahan. Data gagal disimpan.','danger',700);
					//bootbox.alert("Terjadi kesalahan. Data gagal disimpan.");
					//$("#errorHandler").html(msg.errormsg).show();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				//$().showMessage('Terjadi kesalahan.<br />'+	textStatus + ' - ' + errorThrown ,'danger');
				bootbox.alert("Terjadi kesalahan. Data gagal disimpan."+	textStatus + " - " + errorThrown );
			},
			cache: false
		});
		//$().showMessage('Data pembelian berhasil disimpan, data order akan dikirim melalui sms','success',1000);
	});
function updateTotal(obj, idk){	
	
	//update total
	$('#totalGaji_'+idk).val( parseFloat($('#hidTotAwal_'+idk).val())+parseFloat(obj.value));

}
</script>
