<? if ($display==0){
	$viewKop=$this->commonlib->tableKop('HRD-DK','Data Karyawan', '00', 3,3);
	echo $viewKop;
	}
?>
<table class="bordered"><thead><tr><th colspan=2>DATA PEKERJAAN</th> </tr></thead><tbody>
 <tr><td width="35%">JABATAN/DIVISI/CABANG</td><td><?=$row->NAMA_JAB." / ".$row->NAMA_DIV." / ".$row->NAMA_CABANG;?></td></tr>      
 <tr><td>TANGGAL AKTIF KERJA</td><td><?=strftime('%d %B %Y',strtotime($row->TGL_AKTIF));?></td></tr>      
 <tr><td>STATUS KARYAWAN</td><td><?=$row->STS_PEGAWAI;?></td></tr>	
 </tbody></table><br>
 <table class="bordered"><thead><tr><th colspan=4>DATA PRESTASI</th> </tr></thead><tbody>
<?  if (sizeof($rowPrestasi)<=0){
	echo "<tr><td style=\"text-align:center\" colspan=4>DATA PRESTASI TIDAK DITEMUKAN</td></tr>";
}else{
	$i=1;
?><tr><th width="5%">NO</th><th width="20%">TANGGAL PRESTASI</th><th width="30%">PRESTASI</th><th>KETERANGAN</th></tr><?
	foreach ($rowPrestasi as $rowPres){?>
 
<?
	echo "<tr>";
	echo "<td>$i</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowPres->TANGGAL))."</td>";
	echo "<td>".$rowPres->NAMA_PRESTASI."</td>";
	echo "<td>".$rowPres->KETERANGAN."</td>";
	echo "</tr>";
	$i++;
	}
}
	?>
  </tbody></table>
<br>
 <table class="bordered"><thead><tr><th colspan=4>DATA PELATIHAN</th> </tr></thead><tbody>
<?  if (sizeof($rowPelatihan)<=0){
	echo "<tr><td style=\"text-align:center\" colspan=4>DATA PELATIHAN TIDAK DITEMUKAN</td></tr>";
}else{
	$i=1;
	?><tr><th width="5%">NO</th><th width="20%">TANGGAL PELATIHAN</th><th  width="30%">NAMA PELATIHAN</th><th>KETERANGAN</th></tr><?
	foreach ($rowPelatihan as $rowLatih){?>
 
<?
	echo "<tr>";
	echo "<td>$i</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowLatih->TANGGAL))."</td>";
	echo "<td>".$rowLatih->NAMA_PELATIHAN."</td>";
	echo "<td>".$rowLatih->KETERANGAN."</td>";
	echo "</tr>";
	$i++;
	}
}
	?>
  </tbody></table><br>
  <table class="bordered"><thead><tr><th colspan=4>DATA PELANGGARAN</th> </tr></thead><tbody>
<?  if (sizeof($rowPelanggaran)<=0){
	echo "<tr><td style=\"text-align:center\" colspan=4>DATA PELANGGARAN TIDAK DITEMUKAN</td></tr>";
}else{
	$i=1;
?><tr><th width="5%">NO</th><th width="20%">TANGGAL PELANGGARAN</th><th  width="30%">NAMA PELANGGARAN</th><th>KETERANGAN</th></tr><?
	foreach ($rowPelanggaran as $rowLanggar){?>
 
<?
	echo "<tr>";
	echo "<td>$i</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowLanggar->TANGGAL))."</td>";
	echo "<td>".$rowLanggar->NAMA_PELANGGARAN."</td>";
	echo "<td>".$rowLanggar->KETERANGAN."</td>";
	echo "</tr>";
	$i++;
	}
}
	?>
  </tbody></table>
 <br>
  <table class="bordered"><thead><tr><th colspan=8>DATA LEMBUR</th> </tr></thead><tbody>
<?  if (sizeof($rowLembur)<=0){
	echo "<tr><td style=\"text-align:center\" COLSPAN=8>DATA LEMBUR TIDAK DITEMUKAN</td></tr>";
}else{
	$i=1;
	?><tr><th width="3%">NO</th><th width="10%">TGL PENGAJUAN </th><th width="10%">TGL LEMBUR</th><th width="7%">MULAI</th><th width="7%">SELESAI</th><th  width="7%">JML JAM</th><th WIDTH="15%">KETERANGAN</th><th >APPROVED STATUS</th></tr><?
	foreach ($rowLembur as $rowLem){?>
 
<?
	echo "<tr VALIGN=TOP>";
	echo "<td>$i</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowLem->CREATED_DATE))."</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowLem->TGL_LEMBUR))."</td>";
	echo "<td>".strftime('%H:%M:%S',strtotime($rowLem->JAM_MULAI))."</td>";
	echo "<td>".strftime('%H:%M:%S',strtotime($rowLem->JAM_SELESAI))."</td>";
	echo "<td>".$rowLem->JML_JAM."</td>";
	echo "<td>".$rowLem->KETERANGAN."</td>";
	echo "<td>".($rowLem->APPROVED==0?"BLM DISETUJUI":"SUDAH DISETUJUI<BR>OLEH : ".$rowLem->APPROVED_BY."<BR>PADA : ".$rowLem->APPROVED_DATE)."</td>";
	echo "</tr>";
	$i++;
	}
}
	?>
  </tbody></table>

 <br>
  <table class="bordered"><thead><tr><th colspan=8>DATA PERMOHONAN CUTI</th> </tr></thead><tbody>
<?  if (sizeof($rowCuti)<=0){
	echo "<tr><td style=\"text-align:center\"  colspan=8>DATA PERMOHONAN CUTI TIDAK DITEMUKAN</td></tr>";
}else{
	$i=1;
?><tr><th width="3%">NO</th><th width="10%">TGL PENGAJUAN </th><th  width="7%">JENIS CUTI</th><th width="10%">TGL AWAL CUTI</th><th width="7%">TGL AKHIR CUTI</th><th width="7%">JML HARI</th><th WIDTH="15%">KETERANGAN</th><th >APPROVED STATUS</th></tr><?
	foreach ($rowCuti as $rowC){?>
 
<?
	echo "<tr VALIGN=TOP>";
	echo "<td>$i</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowC->TGL_TRANS))."</td>";
	echo "<td>".$rowC->JENISCUTI1." - ".$rowC->JENISCUTI2."</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowC->TGL_AWAL))."</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowC->TGL_AKHIR))."</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowC->JML_HARI))."</td>";
	echo "<td>".$rowC->KETERANGAN."</td>";
	echo "<td>".($rowC->APPROVED==0?"BLM DISETUJUI":"SUDAH DISETUJUI<BR>OLEH : ".$rowC->APPROVED_BY."<BR>PADA : ".$rowC->APPROVED_DATE)."</td>";
	echo "</tr>";
	$i++;
	}
}
	?>
  </tbody></table>

<br>
   <table class="bordered"><thead><tr><th colspan=7>DATA MUTASI</th> </tr></thead><tbody>
<?  if (sizeof($rowMutasi)<=0){
	echo "<tr><td style=\"text-align:center\" colspan=7>DATA MUTASI TIDAK DITEMUKAN</td></tr>";
}else{
	$i=1;
	?><tr><th width="3%">NO</th> <th width="15%">MUTASI KE</th> <th  width="15%">DARI</th> <th width="15%">TGL PENETAPAN </th> <th  width="20%">KETERANGAN</th><th width="10%">MENGETAHUI</th><th>MENYETUJUI</th></tr><?
	foreach ($rowMutasi as $rowMutation){?>
 
<?
	echo "<tr valign=top>";
	echo "<td>$i</td>";
	echo "<td>".$rowMutation->MUTASI_BARU."</td>";
	echo "<td>".$rowMutation->MUTASI_LAMA."</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowMutation->TGL_PENETAPAN))."</td>";
	echo "<td>".$rowMutation->KETERANGAN."</td>";
	echo "<td>".$rowMutation->MENGETAHUI."</td>";
	echo "<td>".$rowMutation->MENYETUJUI."</td>";
	echo "</tr>";
	$i++;
	}
}
	?>
</tbody></table>
 <br>
 <table class="bordered"><thead><tr><th colspan=5>DATA RESIGN</th> </tr></thead><tbody>
<?  if (sizeof($rowResign)<=0){
	echo "<tr><td style=\"text-align:center\" colspan=5>DATA RESIGN TIDAK DITEMUKAN / STATUS KARYAWAN : AKTIF</td></tr>";
}else{
	$i=1;
	?><tr><th width="5%">NO</th><th width="20%">TANGGAL RESIGN</th><th width="30%">ALASAN</th><th width="10%">MENGETAHUI</th><th>MENYETUJUI</th></tr><?
	foreach ($rowResign as $rowPensiun){?>
 
<?
	echo "<tr>";
	echo "<td>$i</td>";
	echo "<td>".strftime('%d %B %Y',strtotime($rowPensiun->TGL))."</td>";
	echo "<td>".$rowPensiun->ALASAN."</td>";
	echo "<td>".$rowPensiun->MENGETAHUI."</td>";
	echo "<td>".$rowPensiun->MENYETUJUI."</td>";
	echo "</tr>";
	$i++;
	}
}
	?>
  </tbody></table>
<br>
<? if ($display==0){
	  $param=$nik."_pekerjaan_1";
?>
<div class="row" style="text-align:center">
	<div class="col-md-12">	
		<a href="<?=base_url('hrdReportPersonal/personalData/'.$param)?>" class="btn btn-success">Print Data Pekerjaan</a>
		
	</div>
</div>	
<?
}
?>