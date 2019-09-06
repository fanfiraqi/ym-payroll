<?php echo form_open('gaji_zisco_transport/save_gaji_zisco_transport',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
<!--	<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	Informasi : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Open = Sudah ada data yang disimpan dan masih bisa diedit. <br>
	&nbsp;&nbsp;3. Closed = Melewati Periode aktif<br>
	"Slip-Email All" yang dikirim hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut terkirim<br>
	
	</div> -->
	
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover"  style="max-height:650px;overflow:scroll;" id="myTable">
                           <thead>
                           <tr >
							<th  rowspan=2 >NO</th>
							<th  rowspan=2>NIK</th>
							<th  rowspan=2>NAMA</th>
							<th  rowspan=2>KANTOR</th>
							<th  rowspan=2>JABATAN</th>
							<th  rowspan=2>TGL MASUK</th>
							<th  rowspan=2>KONTRAK KERJA</th>
							<th  rowspan=2>MASA KERJA</th>
							<th  rowspan=2>STATUS PEGAWAI</th>
							<th  colspan=2>KETIDAKHADIRAN ZISCO</th>
							
<?	$strMaster="select * from mst_komp_gaji  where isactive=1 and  is_zisco='on' and jns_zisco='transport' order by ID";
	$master=$this->db->query($strMaster)->result();
	$p=1;
	$t=1;
	$thPend=""; 
	$thPot=""; $nominal=0;
	foreach($master as $rowmaster){	
		if ($rowmaster->FLAG=='+'){
			$thPend.="<th>".$rowmaster->NAMA."</th>";
			$p++;
		}else{
			$thPot.="<th>".$rowmaster->NAMA."</th>";
			$t++;
		}
		//echo "<th>".$rowmaster->NAMA."(".$rowmaster->FLAG.")</th>";
		
	}
	$colspanAll=$p+8+$t;
?>							
							<th colspan="<?php echo $p-1?>">PENDAPATAN</th>
							<th rowspan=2>TOTAL PENDAPATAN</th>
							<th colspan="<?php echo $t-1?>">POTONGAN</th>
							<th rowspan=2>TOTAL POTONGAN</th>
							<th rowspan=2>TOTAL TERIMA</th>
							<th rowspan=2>SLIP</th>
						  </tr>
						  <tr>	
						   <th>JML IJIN+SAKIT</th>
						  <th>JML ALPA</th>
						  <?php echo $thPend.$thPot?>
						  </tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$strCek</td></tr>";
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan=\"".$colspanAll."\">Data Belum Ada</td></tr>";
	}else{
		$i=1;	//as row
		$blnIdk=$this->arrIntBln;
		//cek var bln tahun rekap absensi value DARI BULAN SEBLMNYA
		if ($bln==1){
			$bln_pre=12;
			$thn_pre=$thn-1;
		}else{
			$bln_pre=$blnIdk[$bln-1];
			$thn_pre=$thn;
		}
		$keyDisabled=0;
		foreach($row as $hasil){
			$cektransport="";
			$masakerja=0;
			$strmasakerja="-";
			if ($hasil->SELISIH=="" || empty($hasil->SELISIH)){
				$masakerja=0;
				$strmasakerja="-";
				/*if ($hasil->ID_JAB==37){
				    $keyDisabled=0;
				}else{
				    $keyDisabled=1;
				}*/
				
				
			}else{
				$masakerja=$hasil->SELISIH;		//dlm bulan
				//hitung masa kerja
				if ($masakerja<12){				
					$strmasakerja=number_format($hasil->SELISIH,0,',','')." Bln";	
					$thnMasakerja=0;
				}else{
					$strmasakerja=floor($hasil->SELISIH/12)." Thn, ".($hasil->SELISIH%12)." Bln";
					$thnMasakerja=floor($hasil->SELISIH/12);
				}
			}

		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
		$rscab=$this->gate_db->query("select * from mst_cabang where id_cabang=".$hasil->ID_CABANG )->row();
		$rs_stspeg=$this->db->query("select value1 from gen_reff where reff='STSPEGAWAI' and id_reff=".$hasil->STATUS_PEGAWAI)->row();
		

		//GET ABSEN VALUE
		//hari kerja ambil dari database
			$jml_hadir=0;
			$jml_tidak_hadir=0;
			$jml_alpa=0;
			
			$skerja="select * from rekap_absensi_zisco where periode='".$thn.$bln."' and nik='".$hasil->NIK."'";
			if ($this->db->query($skerja)->num_rows()>0){
				$rskerja=$this->db->query($skerja)->row();
				$jml_hadir=$rskerja->JML_MASUK;
				$jml_tidak_hadir=$rskerja->JML_TIDAK_MASUK + $rskerja->JML_SAKIT;
				$jml_alpa=$rskerja->JML_ALPA;
				
			}

			$tanggal_aktif=($hasil->TGL_AKTIF==""||empty($hasil->TGL_AKTIF)?"TGL MASUK BLM DI SET":strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF) ) );
?>		
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><?=str_replace(" ","&nbsp;",$rscab->kota)?><input type="hidden" name="id_cabang_<?=$i?>" id="id_cabang_<?=$i?>" value="<?php echo $hasil->ID_CABANG?>"></td>
		<td><?=str_replace(" ","&nbsp;",$rsjab->nama_jab)?><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td><?=str_replace(" ","&nbsp;", $tanggal_aktif)?></td>
		<td><?=str_replace(" ","&nbsp;", strftime("%d %B %Y",strtotime($hasil->TGL_AWAL_KONTRAK)))?></td>
		<td><?=form_hidden(array('name'=>'strMasaKerja_'.$i,'id'=>'strMasaKerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$strmasakerja)).str_replace(" ","&nbsp;",$strmasakerja);?><input type="hidden" name="masakerja_<?=$i?>" id="masakerja_<?=$i?>" value="<?=$masakerja?>"></td>
		<td><input type="hidden" name="stspeg_<?=$i?>" id="stspeg_<?=$i?>" value="<?php echo $hasil->STATUS_PEGAWAI?>"><?=str_replace(" ","&nbsp;", $rs_stspeg->value1)?></td>
		<td><input type="hidden" name="jml_tdkmasuk_<?=$i?>" id="jml_tdkmasuk_<?=$i?>" value="<?=$jml_tidak_hadir?>"><?php echo $jml_tidak_hadir?></td>		
		<td><input type="hidden" name="jml_alpa_<?=$i?>" id="jml_alpa_<?=$i?>" value="<?=$jml_alpa?>"><?php echo $jml_alpa?></td>		
<?	
	//get key yg dipakai smua komp
	$sumPer_row=0; 
	$gapok=0;$THT=0;
	$sumPotPer_row=0;
	$j=0;
	$display_acuan=0; $acuan_trans =0;
	$acuan_motor=0;
	$display_angsuran=0; $cicilke=0; $id_header=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	

	foreach($master as $rowmaster){
		$bgcolor=""; $readonly=true;
		//PENDAPATAN
		if ($rowmaster->FLAG=='+'){
		$cekmasuk="";$str1=$str2=$str3=$str4=$str5=$str6="";
		switch ($rowmaster->ID){
			case "9":	//Tj. Transport, Tabel
				//id_cabang, if sts_pegawai<=2 => trainee
				$str1="select ifnull(".($hasil->STATUS_PEGAWAI <=2?"trainee":"penuh").",0) nominal from mst_acuan_transport where id_cabang=".$hasil->ID_CABANG ;
				$rstrans=$this->db->query($str1)->row();
				if (sizeof($rstrans)>0){
					$acuan_trans = $rstrans->nominal;					
					$display_acuan=1;
				}else{
					$cektransport="";
					$keyDisabled=1;
				}
				$nominal=$acuan_trans - ((($acuan_trans /25) * 0.5 )* $jml_tidak_hadir) - (($acuan_trans /25) * $jml_alpa)  ;				
				break;
			case "10":	//Tj. Jabatan zisco, var
				$nominal=0;
				$str3="select ifnull(nominal,0) nominal from mst_komp_var where	id_komp=10";
				$rsJab=$this->db->query($str3)->row();
				if (sizeof($rsJab)>0){
					$tunj_jab = $rsJab->nominal;					
					if ($hasil->ID_JAB ==35){	//supervisor
						$nominal = $tunj_jab;
					}
				}
				break;
			case "11":	//BPJS Kesehatan
				$nominal = 0;	
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "12":	//BPJS Ketenagakerjaan
				$nominal = 0;	
				$bgcolor="#ffffcc";$readonly=false;
				break;
			
			case "13":	//Penyesuaian
				$nominal = 0;	
				$bgcolor="#ffffcc";$readonly=false;
				break;			
			case "14":	//bantuan servis motor, var, zisco dg masa kerja 3 tahun k atas dan dberikan setiap 3 bln sekali
				$str3="select ifnull(nominal,0) nominal from mst_komp_var where	id_komp=14";
				$rsmotor=$this->db->query($str3)->row();
				
				if (sizeof($rsmotor)>0){
					$acuan_motor = $rsmotor->nominal;
					if (($masakerja/36) >= 3 && in_array($bln, [3,6, 9, 12])){
						$nominal = $acuan_motor;
					}
				}
				$cekmasuk="3";
				break;
			case "23":	//Koreksi
				$nominal = 0;	
				$bgcolor="#ffffcc";$readonly=false;
				break;
			
			default: 
				$nominal=0;
				$bgcolor="#ffffcc";$readonly=false;
				break;

		}

		$nominal=round($nominal, 0);
		echo "<td>";
		if ($display_acuan==1){
			echo number_format($acuan_trans,0,',','.').'<input type="hidden" name="acu_transport_'.$i.'" id="acu_transport_'.$i.'" value="'.$acuan_trans.'">';
		}
		
		echo form_input(array('name'=>'komp_'.$i.'_'.$j,'id'=>'komp_'.$i.'_'.$j,'class'=>'myform-control','size'=>10,  'value'=>$nominal, "style"=>"background-color:".$bgcolor, "onkeyup"=>"countRevenue(".$i.", ".$j.", this)", "onkeypress"=>"return numericVal(this,event)"))."</td>";
		if ($rowmaster->FLAG=='+'){ 
			$sumPer_row+=$nominal;
		}else{
			$sumPer_row-=$nominal;
		}
		if ($rowmaster->ID==23){?>
			<td><?=form_input(array('name'=>'subTotal_'.$i,'id'=>'subTotal_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($sumPer_row,0)));?></td>

		<? }
		//$j++; => bertambah 1 utk komp
		}else{	//end pendapatan
			//POTONGAN
			
			switch ($rowmaster->ID){
			
				case "24":	//Angsuran Pinjaman
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
						$display_angsuran=1;
					}
					$nominal=$jmlcicilan;
					$bgcolor="#ffffcc";$readonly=false;
					break;
				case "36":	//Pot lain2
					$nominal=0;		
					$bgcolor="#ffffcc";$readonly=false;
					break;
			}
			echo "<td>";
			if ($display_angsuran==1){
				echo 'Cicilan ke:'.$cicilke.'<input type="hidden" name="cicilke_'.$i.'" id="cicilke_'.$i.'" value="'.$cicilke.'"><input type="hidden" name="id_header_'.$i.'" id="id_header_'.$i.'" value="'.$id_header.'">';
			}
			echo ($cektransport==""?"":$cektransport).form_input(array('name'=>'komp_'.$i.'_'.$j,'id'=>'komp_'.$i.'_'.$j,'class'=>'myform-control','size'=>10,  "style"=>"background-color:".$bgcolor, "onkeypress"=>"return numericVal(this,event)","onkeyup"=>"countExpense(".$i.", ".$j.", this)", 'value'=>round($nominal,0)))."</td>";
			if ($rowmaster->FLAG=='-'){ 
				$sumPotPer_row-=$nominal;
			}else{
				$sumPotPer_row+=$nominal;
			}
		}
		$j++;
		$display_acuan=0;
	}

	

if ($rowmaster->ID==36){
	?>
	<td><?=form_input(array('name'=>'subPotTotal_'.$i,'id'=>'subPotTotal_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>round($sumPotPer_row*-1,0) ) );?></td>
	<?
	
}?>	

<td><?=form_input(array('name'=>'subGrandTotal_'.$i,'id'=>'subGrandTotal_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>round($sumPer_row+$sumPotPer_row,0) ) );?></td><td>&nbsp;-</td></tr>			
			
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
									if ($sts=="new" || $sts=="edit"){
							?>								
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">
										<input type="hidden" name="thn" id="thn" value="<?=$thn?>">	
										<input type="hidden" name="bln" id="bln" value="<?=$bln?>">	
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">
										<input type="hidden" name="id_validasi" id="id_validasi" value="<?=$id_validasi?>">
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan"  <?php echo ($keyDisabled==1?"disabled":"")?>>		
							<?
									}
											
								}
								
								$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('gaji_staf/index')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
							?>
							</div><!-- col -->
						</div><!-- row -->

						
             
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
			url: '<?php echo base_url('gaji_zisco_transport/save_gaji_zisco_transport');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					
					$().showMessage('Data Gaji Transport zisco berhasil disimpan.','success',1000);
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
	
	function countRevenue(idr, idc, obj){

		var total= parseFloat( $("#komp_"+idr+"_"+0).val() ) +  parseFloat( $("#komp_"+idr+"_"+1).val() ) +  parseFloat( $("#komp_"+idr+"_"+2).val() ) +  parseFloat( $("#komp_"+idr+"_"+3).val() ) +  parseFloat( $("#komp_"+idr+"_"+4).val() ) +  parseFloat( $("#komp_"+idr+"_"+5).val() ) +  parseFloat( $("#komp_"+idr+"_"+6).val() ) ;

		$("#subTotal_"+idr).val(total);
		$("#subGrandTotal_"+idr).val( parseFloat( $("#subTotal_"+idr).val() ) - parseFloat(  $("#subPotTotal_"+idr).val()) ) ;
	}
	function countExpense(idr, idc, obj){
		var total= parseFloat( $("#komp_"+idr+"_"+7).val() ) +  parseFloat( $("#komp_"+idr+"_"+8).val() );

		$("#subPotTotal_"+idr).val(total);
		$("#subGrandTotal_"+idr).val( parseFloat( $("#subTotal_"+idr).val() ) - parseFloat(  $("#subPotTotal_"+idr).val()) ) ;
	}


	
</script>