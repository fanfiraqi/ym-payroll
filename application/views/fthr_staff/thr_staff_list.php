<?php echo form_open('thr_staff/save_thr_staff',array('class'=>'form-horizontal','id'=>'myform'));?>
 <div class="row">
	<div class="col-xs-12">
	<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> 
	Informasi : <br>
	&nbsp;&nbsp;1. New = Belum Pernah disimpan, <br>
	&nbsp;&nbsp;2. Open = Sudah ada data yang disimpan dan masih bisa diedit. <br>
	&nbsp;&nbsp;3. Closed = Melewati Tahun aktif<br>
	"Slip-Email All" yang dikirim hanya data terakhir yang tersimpan, jika ada data baru tapi belum diupdate maka tidak ikut terkirim<br>
	
	</div>
		<div class="panel panel-default" >
			<div class="panel-heading">Daftar THR Staff</div><!-- /.panel-heading -->
				<div class="panel-body" >
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover" style="max-height:650px;overflow:scroll;" id="myTable">
                           <thead>
                           <tr >
							<th >NO</th>
							<th >NIK</th>
							<th >NAMA</th>
							<th >JABATAN</th>
							<th >TGL MASUK</th>
							<th >MASA KERJA</th>
							<th >S/M/GM</th>
<?	$strMaster="select * from mst_komp_gaji  where isactive=1 and is_thr=1 and is_staff='on' order by ID";
	$master=$this->db->query($strMaster)->result();
	$p=1;	$th=""; $nominal=0;
	foreach($master as $rowmaster){		
		//$th.="<th>".$rowmaster->NAMA."</th>";
		echo "<th>".$rowmaster->NAMA."(".$rowmaster->FLAG.")</th>";
		if ($rowmaster->ID=="7"){
			echo "<th >TOTAL PENDAPATAN</th>";
		}
		$p++;
	}
	$colspanAll=$p+8;
?>														
							<th >TOTAL TERIMA</th>
							<th >SLIP</th>
						  </tr>
						  
                         </thead>
                                    <tbody>
<?	
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan=\"".$colspanAll."\">Data Belum Ada</td></tr>";
	}else{
		$i=1;
		foreach($row as $hasil){
			$masakerja=$hasil->SELISIH;		//dlm bulan
			//hitung masa kerja
			if ($masakerja<12){				
				$strmasakerja=number_format($hasil->SELISIH,0,',','')." Bln";	
				$thnMasakerja=0;
			}else{
				$strmasakerja=floor($hasil->SELISIH/12)." Thn, ".($hasil->SELISIH%12)." Bln";
				$thnMasakerja=floor($hasil->SELISIH/12);
			}

		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
		//echo "<tr><td colspan=$p>select * from mst_jabatan where id_jab=".$hasil->ID_JAB."<br>idjab=".$rsjab->id_jab."</td></tr>";
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><?=str_replace(" ","&nbsp;",$rsjab->nama_jab)?><input type="hidden" name="id_cabang_<?=$i?>" id="id_cabang_<?=$i?>" value="<?=$hasil->ID_CABANG?>"></td>
		<td><?=str_replace(" ","&nbsp;", strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF)))?><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td><?=form_hidden(array('name'=>'strMasaKerja_'.$i,'id'=>'strMasaKerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$strmasakerja)).str_replace(" ","&nbsp;",$strmasakerja);?><input type="hidden" name="masakerja_<?=$i?>" id="masakerja_<?=$i?>" value="<?=$masakerja?>"></td>
<?	//get key yg dipakai smua komp
	$sumPer_row=0;
	$j=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	$rsgrade=$this->db->query("select * from mst_grade_cabang where id_cabang=".$id_cabang)->row();
	$rsklaster=$this->gate_db->query("select klaster, kelompok_gaji from mst_jabatan where id_jab=".$id_jab)->row();
	$klaster=$rsklaster->klaster;
	$grade=$rsgrade->grade;
	$gapok=0;

	$kelompok_gaji=$rsklaster->kelompok_gaji;
	echo '<td>'.$rsklaster->kelompok_gaji.'</td>';

	foreach($master as $rowmaster){
		$bgcolor="";$readonly=false;
		$cekmasuk="";$str1=$str2=$str3=$str4=$str5=$str6="";
		switch ($rowmaster->ID){
			case "1":	//gapok, table
				$nominal=0;
				//nik, id_cabang, id_jab, get grade_cabang
				$str1="select ifnull(nominal,0) nominal from mst_gapok where id_jabatan=".$id_jab." and grade_cabang='".$grade."' and (".$masakerja." between lama_kerja_awal and lama_kerja_akhir)";
				$rsgapok=$this->db->query($str1)->row();
				if (sizeof($rsgapok)>0){
					$nominal = $rsgapok->nominal;	
				}
				$gapok = $nominal;	
				$cekmasuk="1";
				break;
			case "2":	//Acuan Uang makan, table
				$str2="select per_bulan from mst_acuan_makan where	id_cabang=".$id_cabang;
				$rsmakan=$this->db->query($str2)->row();
				if (sizeof($rsmakan)>0){
					$nominal = $rsmakan->per_bulan;
				}
				$cekmasuk="2";
				break;
			case "5":	//Tj. keluarga
				if ($hasil->STATUS_PEGAWAI >=5){	//PRA KARY dan KARY. TETAP
					if ($hasil->STATUS_NIKAH == 'SN' || $hasil->SEX=1){
						$nominal = (((1*0.1)+($hasil->JUMLAH_ANAK*0.05))*$gapok);
					}
					if ($hasil->STATUS_NIKAH == 'JN' || $hasil->STATUS_NIKAH == 'DN'){
						$nominal =($hasil->JUMLAH_ANAK*0.05*$gapok);
					}					
				}
				$cekmasuk="5";
				break;
			case "6":	//Tj. Masa Kerja, table
				$str4="select ifnull(nominal,0) nominal from mst_tunj_masa_kerja where tahun_ke <=".$thnMasakerja." ORDER BY tahun_ke DESC LIMIT 1";
				$rsTmk=$this->db->query($str4)->row();
				if (sizeof($rsTmk)>0){
					$nominal = $rsTmk->nominal;
				}
				$cekmasuk="6";
				break;
			case "7":	//Tj. Jabatan, entri manual
				//cek id_jab sbg kepala cabang/bukan (16,17)				
				$nominal=0;
				$bgcolor="#ffffcc";$readonly=false;
				$cekmasuk="7";
				break;
			case "15":	//dansos
				//IF(AH7<=3203846;AH7*2,5%;IF(AND(AH7>=3203846;V7="S");AH7*1%;IF(AND(AH7>=3203846;V7="M");AH4*2%;IF(AND(AH7>=3203846;V7="GM");AH7*2,5%;"CEK LAGI"))))
				$str3="select ifnull(nominal,0) nominal from mst_komp_var where	id_komp=15";
				$rsdansos=$this->db->query($str3)->row();
				if (sizeof($rsdansos)>0){
					$dansos = $rsdansos->nominal;
					$acuan=$dansos;
					if ($sumPer_row< $acuan){
						$nominal =	$sumPer_row * 0.025;
						//$nominal = ($sumPer_row< $acuan ? $sumPer_row * 0.025 :  ( $sumPer_row >= $acuan && $kelompok_gaji=="S" ?  $sumPer_row * 0.01: () ) );
					}else{
						if ($sumPer_row >= $acuan){
							switch ($kelompok_gaji){
								case "S":
									$nominal =	$sumPer_row * 0.01;
									break;
								case "M":
									$nominal =	$sumPer_row * 0.02;
									break;
								case "GM":
									$nominal =	$sumPer_row * 0.025;
									break;
								default: 
									$nominal=0;
									break;
							}

						}
					}
				}
				break;
			default: 
				$nominal=0;$cekmasuk="default";
				break;

		}
		$nominal=round($nominal,0);
		echo "<td>".form_input(array('name'=>'komp_'.$i.'_'.$j,'id'=>'komp_'.$i.'_'.$j,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$nominal))."</td>";
		if ($rowmaster->FLAG=='+'){ 
			$sumPer_row+=$nominal;
		}else{
			$sumPer_row-=$nominal;
		}
		
		if ($rowmaster->ID=="7"){
			echo "<td>".form_input(array('name'=>'tot_pend_'.$i,'id'=>'tot_pend_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$sumPer_row))."</td>";
		}
		
		$j++;
		

	}


?>
	<td><?=form_input(array('name'=>'subTotal_'.$i,'id'=>'subTotal_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$sumPer_row));?></td>

	
	<!-- GENERATE/DOWNLOAD -->
		<?	if( $sts=="edit" || $sts=="disabled"){
			$param=$tahun."_".$hasil->NIK;
			//CEK FILE_SLIP
			$strFile="select count(*) CKFILE from file_slip_thr where thn='$tahun' and nik='".$hasil->NIK."'";
			$rsFile=$this->db->query($strFile)->row();
			echo "<td>";
			if ($rsFile->CKFILE >=1){	//ada -> tombol download
				$rsPath=$this->db->query("select * from file_slip_thr where thn='$tahun' and nik='".$hasil->NIK."'")->row();
		?>	&nbsp;-&nbsp;<br>
			<a href="javascript:void(0)" onclick="window.open('<?=base_url($rsPath->PATH."/".$rsPath->NAMA_FILE)?>','_blank')"><i class="fa fa-print" title="Print/Download File">Download/Print</i></a>
		<?
			}else{	
			
		?>
		<a href="javascript:void(0)" data-url="<?=base_url('thr_staff/cetak_slip/')?>" data-id="<?=$param?>" onclick="singleSlip(this)"><i class="fa fa-edit" title="Generate Slip to .pdf File">Generate&nbsp;pdf</i></a>
		<?}
				echo "</td>";
			}else{
				echo "<td>&nbsp;</td>";
			}
			?>

	</tr>			
			
<?
		$i++;
		}
	}
?>			

                                    </tbody>
                                </table>
                            </div> <!-- /.table-responsive -->
                        </div><!-- /.panel-body -->
                    </div> <!-- /.panel -->
					
                        
						<div class="row">
							<div class="col-md-12">
							<? 	if (sizeof($row)>0){ 
									if ($sts=="new" || $sts=="edit"){
							?>								
										<input type="hidden" name="jmlRow" id="jmlRow" value="<?=($i-1)?>">
										<input type="hidden" name="tahun" id="tahun" value="<?=$tahun?>">	
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">	
										<!-- <input type="hidden" name="id_cabang" id="id_cabang" value="<?=$id_cabang?>"> -->
										<input type="hidden" name="laz_tasharuf" id="laz_tasharuf" value="<?=$laz_tasharuf?>">
										<input type="hidden" name="wilayah" id="wilayah" value="<?=$wilayah?>">
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan">		
							<?
									}
											
								}
								
								$btback = array(
												'name'=>'btback',
												'id'=>'btback',
												'content'=>'Kembali',
												'onclick'=>"backTo('".base_url('thr_staff/form')."');return false;",
												'class'=>'btn btn-danger'
											);
										echo "&nbsp;".form_button($btback);
							?>
							</div><!-- col -->
						</div><!-- row -->

						
                </div>
</div>
<hr />
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
			url: '<?php echo base_url('thr_staff/save_thr_staff');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					$().showMessage('Data THR Staff berhasil disimpan.','success',1000);
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


</script>