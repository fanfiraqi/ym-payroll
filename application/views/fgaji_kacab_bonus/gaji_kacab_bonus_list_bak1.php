<?php echo form_open('gaji_kacab_bonus/save_gaji_zisco_transport',array('class'=>'form-horizontal','id'=>'myform'));?>
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
							<th  rowspan=2 >NO</th>
							<th  rowspan=2>NIK</th>
							<th  rowspan=2>NAMA</th>
							<th  rowspan=2>KANTOR</th>
							<th  rowspan=2>REK BSM</th>							
							<th  rowspan=2>JABATAN</th>
							<th rowspan=2>TGL MASUK</th>						
							<th rowspan=2>MASA KERJA</th>						
							<th rowspan=2>TARGET PENGAMBILAN</th>
							<th rowspan=2>REALISASI PENGAMBILAN</th>
							<th rowspan=2>TOTAL DONASI CABANG YG MASUK</th>							
							<th colspan=3>REALISASI PENGEMBANGAN (PRIBADI)</th>
							<th rowspan=2>PRESENTASE PENGAMBILAN</th>
							<th colspan=3>BONUS</th>
							<th rowspan=2>PENYESUAIAN</th>
							<th rowspan=2>TOTAL PENDAPATAN</th>
							<th colspan=3>POTONGAN</th>
							<th rowspan=2>TOTAL POTONGAN</th>
							<th rowspan=2>TOTAL TERIMA</th>
							<th rowspan=2>SLIP</th>
						  </tr>

						
						  <tr>
							<th>INSIDENTIL</th>
							<th>ZIS RUTIN</th>
							<th>WAKAF</th>
							<th>JABATAN</th>
							<th>KACAB</th>
							<th>PRESTASI</th>
							<th>DANSOS (2%)</th>
							<th>ZAKAT (2,5%)</th>
							<th>LAIN2</th>
							</tr>
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";
	if (sizeof($row)==0){	
		echo "<tr align=center><td colspan='30'>Data Belum Ada</td></tr>";
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
			$masakerja=0;
			$strmasakerja="-";
			if ($hasil->SELISIH=="" || empty($hasil->SELISIH)){
				$masakerja=0;
				$strmasakerja="-";
				
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
        
         $rscek=$this->donasi_db->query("select count(*) jml from donasi where status='DRAFT' and cabang_id=".$hasil->ID_CABANG." and concat(year, month)='".$thn.intval($bln)."'" )->row();
	    if ($rscek->jml>0){
	        $keyDisabled=1;
	        $pesan="Masih ada kuitansi yang belum diproses";
	    }
	    
		$rsjab=$this->gate_db->query("select * from mst_jabatan where id_jab=".$hasil->ID_JAB )->row();
		$rscab=$this->gate_db->query("SELECT m.*, (SELECT DISTINCT kota FROM mst_cabang WHERE mst_cabang.`id_cabang`=m.ID_cabang_PARENT) nama_parent FROM mst_cabang m where id_cabang=".$hasil->ID_CABANG )->row();
		$rs_stspeg=$this->db->query("select value1 from gen_reff where reff='STSPEGAWAI' and id_reff=".$hasil->STATUS_PEGAWAI)->row();
		
		$tanggal_aktif=($hasil->TGL_AKTIF==""||empty($hasil->TGL_AKTIF)?"TGL MASUK BLM DI SET":strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF) ) );
		
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><?=str_replace(" ","&nbsp;",$rscab->kota)?><input type="hidden" name="id_cabang_<?=$i?>" id="id_cabang_<?=$i?>" value="<?php echo $hasil->ID_CABANG?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->REKENING)?></td>
		<td><?=str_replace(" ","&nbsp;",$rsjab->nama_jab)?><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td><?=str_replace(" ","&nbsp;", $tanggal_aktif)?></td>
		<td><?=form_hidden(array('name'=>'strMasaKerja_'.$i,'id'=>'strMasaKerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$strmasakerja)).str_replace(" ","&nbsp;",$strmasakerja);?><input type="hidden" name="masakerja_<?=$i?>" id="masakerja_<?=$i?>" value="<?=$masakerja?>"></td>
<?	
	//get key yg dipakai smua komp
	$sumPer_row=0; 
	$sumPotPer_row=0;
	$j=0;
	$display_angsuran=0; $cicilke=0; $id_header=0;
	//get data target&pengambilan
	$target_pengambilan=0;
	$real_pengambilan=0;
	$total_donasi_masuk=0;
	//difilter selain kacab
	
/*
$strInsiZakat="SELECT   IFNULL(SUM(dd.paid),0) realisasi 
FROM donatur dnt,donasi d, detail_donasi dd	
WHERE dnt.id=d.donatur_id  AND d.id=dd.donasi_id and   d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."' 
AND d.zisco_id=".$hasil->ID."   
and d.bkm_id in (select id from bkm where status='VALIDATED')
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE  payroll_column='zakat' AND `type`='INSIDENTAL')";
	$rsInsiZakat=$this->donasi_db->query($strInsiZakat)->row();
	$insi_zakat_paid=$rsInsiZakat->realisasi;
*/	
	
$str_pengambilan="SELECT  IFNULL(SUM(dd.nominal),0) target, IFNULL(SUM(dd.paid),0) realisasi, IFNULL(SUM(dd.diff),0) selisih 
FROM donasi d, detail_donasi dd	, donatur don
WHERE  d.id=dd.donasi_id 
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."'
and don.id=d.donatur_id and d.cabang_id=".$hasil->ID_CABANG."  and d.bkm_id in (select id from bkm where status='VALIDATED')
and d.`type`='RUTIN'
and don.registered_at not like '".$thn.'-'.$bln."%' 
AND   d.donatur_id in (select id from donatur where cabang_id=".$hasil->ID_CABANG.") ";
	
	$sql_pengambilan=$this->donasi_db->query($str_pengambilan);
	if ($sql_pengambilan->num_rows>0){
		$rs_ambil=$sql_pengambilan->row();
		$target_pengambilan=$rs_ambil->target;
		$real_pengambilan=($rs_ambil->realisasi > $rs_ambil->target?$rs_ambil->target: $rs_ambil->realisasi);
	}

//difilter selain kacab
$str_alldonasi="SELECT  IFNULL(SUM(dd.nominal),0) target, IFNULL(SUM(dd.paid),0) realisasi, IFNULL(SUM(dd.diff),0) selisih 
FROM donasi d, detail_donasi dd	
WHERE  d.id=dd.donasi_id and  d.status in ('PAID', 'FAILED')
AND (case  WHEN d.paid_at is null then  CONCAT(d.year, d.month) ELSE date_format(d.paid_at,'%Y%c') end) ='".$thn.intval($bln)."'
And d.bkm_id in (select id from bkm where status='VALIDATED')
AND d.cabang_id=".$hasil->ID_CABANG;
$sql_all=$this->donasi_db->query($str_alldonasi);

	if ($sql_all->num_rows>0){
		$rs_all=$sql_all->row();
		$total_donasi_masuk=$rs_all->realisasi;
	}

$jmlkacab_nonwakaf=0;
$jmlkacab_wakaf=0;
$jmlkacab_rutin=0;
//pengambilan kacab
$str_kacab_non_wakaf ="SELECT  IFNULL(SUM(dd.nominal),0) target, IFNULL(SUM(dd.paid),0) realisasi, IFNULL(SUM(dd.diff),0) selisih 
FROM donasi d, detail_donasi dd	
WHERE d.id=dd.donasi_id and d.status in ('PAID', 'FAILED')
AND CONCAT(d.year, d.month)='".$thn.$bln."'  AND d.cabang_id=".$hasil->ID_CABANG."
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE   payroll_column <>'wakaf') 
and d.`type`='INSIDENTAL' and d.id in (select id from donasi where bkm_id in (select id from bkm where zisco_id =".$hasil->ID."))";
$sql_nonwakaf=$this->donasi_db->query($str_kacab_non_wakaf);
	if ($sql_nonwakaf->num_rows>0){
		$rs_nonwakaf=$sql_nonwakaf->row();
		$jmlkacab_nonwakaf=$rs_nonwakaf->realisasi;
	}


$str_kacab_wakaf ="SELECT  IFNULL(SUM(dd.nominal),0) target, IFNULL(SUM(dd.paid),0) realisasi, IFNULL(SUM(dd.diff),0) selisih 
FROM donasi d, detail_donasi dd	
WHERE d.id=dd.donasi_id and d.status in ('PAID', 'FAILED')
AND CONCAT(d.year, d.month)='".$thn.$bln."'  AND d.cabang_id=".$hasil->ID_CABANG."
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE   payroll_column ='wakaf') and d.id in (select id from donasi where bkm_id in (select id from bkm where zisco_id =".$hasil->ID."))";
$sql_wakaf=$this->donasi_db->query($str_kacab_wakaf);
	if ($sql_wakaf->num_rows>0){
		$rs_wakaf=$sql_wakaf->row();
		$jmlkacab_wakaf=$rs_wakaf->realisasi;
	}

$str_kacab_rutin ="SELECT  IFNULL(SUM(dd.nominal),0) target, IFNULL(SUM(dd.paid),0) realisasi, IFNULL(SUM(dd.diff),0) selisih 
FROM donasi d, detail_donasi dd	
WHERE d.id=dd.donasi_id and d.status in ('PAID', 'FAILED')
AND CONCAT(d.year, d.month)='".$thn.$bln."'  AND d.cabang_id=".$hasil->ID_CABANG."
AND dd.jenis_donasi_id IN (SELECT id FROM jenis_donasi WHERE  `type`='ROUTINE' ) and d.id in (select id from donasi where bkm_id in (select id from bkm where zisco_id =".$hasil->ID."))";

$sql_rutin=$this->donasi_db->query($str_kacab_rutin);
	if ($sql_rutin->num_rows>0){
		$rs_rutin=$sql_rutin->row();
		$jmlkacab_rutin=($rs_rutin->realisasi > $rs_rutin->target?$rs_rutin->selisih: 0);
	}


//% pengambilan, =IF(H8=0;"CEK TARGET";IF(I8/H8>1;"CEK TARGET/REALISASI";I8/H8))
$persen_ambil=($target_pengambilan==0?0:round(($real_pengambilan/$target_pengambilan),2) *100);
$str_persen_ambil=($target_pengambilan<=0?"CEK TARGET": ( $real_pengambilan/$target_pengambilan > 1? "CEK TARGET/REALISASI":$persen_ambil." %" ));

//tunj jab kacab
	$tunjab=0;
	$str5="select ifnull(nominal,0) nominal from mst_tunj_jabatan_cabang where  grade_cabang=( select distinct grade from mst_grade_cabang where id_cabang=".$hasil->ID_CABANG.")";
	$sqlJab=$this->db->query($str5);
	if ($sqlJab->num_rows()>0){
    	$rsTjab=$sqlJab->row();
					if (sizeof($rsTjab)>0){
						$tunjab = $rsTjab->nominal;
					}
	}
//bonus kacab =(rutin*0,2)+((wakaf+nonwakaf)*0,025)
$bonus_kacab=($jmlkacab_rutin*0.2) + ( ($jmlkacab_nonwakaf + $jmlkacab_wakaf) * 0.025);

//tunj prestasi =ROUND(IF(N8>=0,96;IF(J8<=100000000;J8*0,005;IF(J8<=200000000;((J8-100000000)*0,003)+500000;IF(J8<=300000000;((J8-200000000)*0,002)+800000;1000000+((J8-300000000)*0,001))));0);0)
$tunj_prestasi=0;
if ($persen_ambil > 96){
	if ($total_donasi_masuk <= 100000000){
		$tunj_prestasi=$total_donasi_masuk*0.005;
	}elseif($total_donasi_masuk <= 200000000){
		$tunj_prestasi= ( ($total_donasi_masuk - 100000000) *0.003 )+ 500000;
	}elseif($total_donasi_masuk <= 300000000){
		$tunj_prestasi= ( ($total_donasi_masuk - 200000000) *0.002 )+ 800000;
	}else{
		$tunj_prestasi= ( ($total_donasi_masuk - 300000000) *0.001 )+ 1000000;
	}
}

$totPendapatan=$tunjab + $bonus_kacab + $tunj_prestasi;		//-penyesuaian
//dansos =IF(AN7<=3203846;AN7*2,5%;IF(AN7>=3203846;AN7*1%))
$dansos=$totPendapatan * 0.02;

//zakat =IF(AN7<=3203846;AN7*0;IF(AN7>=3203846;AN7*2,5%))
$zakat=$totPendapatan * 0.025;


$totPotongan=$dansos + $zakat;	//-lain2

?>
		<td><?=form_input(array('name'=>'target_pengambilan_'.$i,'id'=>'target_pengambilan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($target_pengambilan,0), "readonly"=>true ));?></td>
		<td><?=form_input(array('name'=>'real_pengambilan_'.$i,'id'=>'real_pengambilan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($real_pengambilan,0), "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'all_donasi_'.$i,'id'=>'all_donasi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($total_donasi_masuk,0), "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'kacab_insi_'.$i,'id'=>'kacab_insi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($jmlkacab_nonwakaf,0), "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'kacab_rutin_'.$i,'id'=>'kacab_rutin_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($jmlkacab_rutin,0), "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'kacab_wakaf_'.$i,'id'=>'kacab_wakaf_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($jmlkacab_wakaf,0), "readonly"=>true));?></td>

		<td><input type="hidden" name="persen_ambil_<?=$i?>" id="persen_ambil_<?=$i?>" value="<?=round($persen_ambil,0)?>">
		<?php echo form_input(array('name'=>'strpersen_ambil_'.$i,'id'=>'strpersen_ambil_'.$i,'class'=>'myform-control','size'=>10, 'value'=>$str_persen_ambil, "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'tunjab_'.$i,'id'=>'tunjab_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($tunjab,0), "readonly"=>true ));?></td>
		<td><?=form_input(array('name'=>'bonus_kacab_'.$i,'id'=>'bonus_kacab_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($bonus_kacab,0), "readonly"=>true ));?></td>
		<td><?=form_input(array('name'=>'tunj_prestasi_'.$i,'id'=>'tunj_prestasi_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($tunj_prestasi,0), "readonly"=>true));?></td>
		<td bgcolor="#ffffcc"><?=form_input(array('name'=>'penyesuaian_'.$i,'id'=>'penyesuaian_'.$i,'class'=>'myform-control','size'=>10, 'value'=>0, "onkeyup"=>"countRevenue(".$i.", this)", "onkeypress"=>"return numericVal(this,event)" ));?></td>
		<td><?=form_input(array('name'=>'totPendapatan_'.$i,'id'=>'totPendapatan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan,0) ));?></td>
<!-- potongan -->
		<td><?=form_input(array('name'=>'dansos_'.$i,'id'=>'dansos_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($dansos,0) , "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'zakat_'.$i,'id'=>'zakat_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($zakat,0) , "readonly"=>true));?></td>
		<td bgcolor="#ffffcc"><?=form_input(array('name'=>'lain_'.$i,'id'=>'lain_'.$i,'class'=>'myform-control','size'=>10, 'value'=>0, "onkeyup"=>"countExpense(".$i.", this)", "onkeypress"=>"return numericVal(this,event)" ));?></td>
		<td><?=form_input(array('name'=>'totPotongan_'.$i,'id'=>'totPotongan_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPotongan,0) , "readonly"=>true));?></td>
		<td><?=form_input(array('name'=>'totTerima_'.$i,'id'=>'totTerima_'.$i,'class'=>'myform-control','size'=>10, 'value'=>round($totPendapatan-$totPotongan,0), "readonly"=>true ));?></td>
		<td>-</td>
		</tr>
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

							<?
								if ($keyDisabled==1 && $pesan !="") {
								    echo '<br><div class="row"><div class="col-md-6"><div class="alert alert-info alert-dismissable">'.$pesan.'</div></div></div>';
								}
							?>
             
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
			url: '<?php echo base_url('gaji_kacab_bonus/save_gaji_kacab_bonus');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					
					$().showMessage('Data Gaji Bonus Kacab berhasil disimpan.','success',1000);
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

		var total= parseFloat( $("#tunjab_"+idr ).val()) +  parseFloat( $("#bonus_kacab_"+idr ).val()) +  parseFloat( $("#tunj_prestasi_"+idr ).val()) +  parseFloat( $("#penyesuaian_"+idr).val());

		$("#totPendapatan_"+idr).val(total);
		$("#totTerima_"+idr).val( parseFloat( $("#totPendapatan_"+idr).val() ) - parseFloat(  $("#totPotongan_"+idr).val()) ) ;
	}
	function countExpense(idr, idc, obj){
		var total= parseFloat( $("#dansos_"+idr).val() ) +  parseFloat( $("#zakat_"+idr).val() ) +  parseFloat( $("#lain_"+idr).val() );

		$("#totPotongan_"+idr).val(total);
		$("#totTerima_"+idr).val( parseFloat( $("#totPendapatan_"+idr).val() ) - parseFloat(  $("#totPotongan_"+idr).val()) ) ;
	}


	
</script>