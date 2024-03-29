<? //if ($display==0){
	$viewKop=$this->commonlib->tableKop('HRD-DK',$title, '00', '__','__');
	echo $viewKop;
	//}
	//echo "display=".$display;
	foreach ($arrCabang as $cabang){
		
		$str=" select ".implode(", ",$arrField)." from ".$nmTable." tbl, (select A.ID_CAB, A.KOTA, A.ID_DIV,A.NAMA_DIV, A.ID_JAB, A.NAMA_JAB, B.NIK, B.NAMA
				from
				(SELECT mc.KOTA, md.NAMA_DIV, mj.NAMA_JAB, mst.ID_CAB, mst.ID_DIV, mst.ID_JAB
				FROM `mst_struktur` mst, mst_cabang mc, mst_divisi md, mst_jabatan mj
				WHERE mst.id_cab = mc.id_cabang
				and mst.id_div=md.id_div
				and mst.id_jab=mj.id_jab) A inner join
				(select * from pegawai  ".($jenis=="resign"?"where status_aktif=0":"where status_aktif=1").") B
				on A.id_cab=B.id_cabang
				and A.id_div=B.id_div and A.id_jab=B.id_jab) P 
				where tbl.nik=P.nik and P.id_cab=".$cabang->ID_CAB." and ".(sizeof($fieldKey)<=1?"date_format(".$fieldKey[0].",'%Y%m') ='".$thn.$bln."'":"(date_format(".$fieldKey[0].",'%Y%m') ='".$thn.$bln."' or date_format(".$fieldKey[1].",'%Y%m') ='".$thn.$bln."')")." order by P.NAMA";	//blm filter bln-thn

		if ($jenis=="lembur"){
		$str=" select ".implode(", ",$arrField)." from ".$nmTable.($jenis=="lembur"?"":" tbl").", (select A.ID_CAB, A.KOTA, A.ID_DIV,A.NAMA_DIV, A.ID_JAB, A.NAMA_JAB, B.NIK, B.NAMA
				from
				(SELECT mc.KOTA, md.NAMA_DIV, mj.NAMA_JAB, mst.ID_CAB, mst.ID_DIV, mst.ID_JAB
				FROM `mst_struktur` mst, mst_cabang mc, mst_divisi md, mst_jabatan mj
				WHERE mst.id_cab = mc.id_cabang
				and mst.id_div=md.id_div
				and mst.id_jab=mj.id_jab) A inner join
				(select * from pegawai  ".($jenis=="resign"?"where status_aktif=0":"where status_aktif=1").") B
				on A.id_cab=B.id_cabang
				and A.id_div=B.id_div and A.id_jab=B.id_jab) P 
				where ".($jenis=="lembur"?"l.nik=P.nik":"tbl.nik=P.nik")." and l.no_trans=d.no_trans and P.id_cab=".$cabang->ID_CAB." and ".(sizeof($fieldKey)<=1?"date_format(".$fieldKey[0].",'%Y%m') ='".$thn.$bln."'":"(date_format(".$fieldKey[0].",'%Y%m') ='".$thn.$bln."' or date_format(".$fieldKey[1].",'%Y%m') ='".$thn.$bln."')");	//blm filter bln-thn
		}
		if ($jenis=="mutasi"){
		$str=" SELECT  p.NIK, p.NAMA, concat(c.KOTA,' - ', d.NAMA_DIV, ' - ',j.NAMA_JAB) MUTASI_BARU,
						CONCAT((select kota from mst_cabang where id_cabang=m.old_id_cab) , ' - ',
						(select nama_div from mst_divisi where id_div=m.old_id_div) , ' - ',
						(select nama_jab from mst_jabatan where id_jab=m.old_id_jab) ) MUTASI_LAMA,
						m.*
						FROM mutasi m, pegawai p, mst_cabang c, mst_divisi d, mst_jabatan j
						WHERE m.nik=p.nik  and m.id_cab=c.id_cabang and m.id_div=d.id_div and m.id_jab=j.id_jab and c.id_cabang=".$cabang->ID_CAB." and ".(sizeof($fieldKey)<=1?"date_format(".$fieldKey[0].",'%Y%m') ='".$thn.$bln."'":"(date_format(".$fieldKey[0].",'%Y%m') ='".$thn.$bln."' or date_format(".$fieldKey[1].",'%Y%m') ='".$thn.$bln."')")." and flag<>0"; //blm filter bln-thn
		}
		//echo $str;
?>
<div class="row">
	<div class="col-xs-12">
	<label  class="col-sm-1 control-label">&nbsp;</label><label  class="col-sm-10 control-label"><h3><?="Cabang : ".$cabang->KOTA?></h3></label>
	<table class="bordered">
	<thead>
	<tr><th>No</th>
	<? foreach ($arrCaption as $caption){
		echo "<th>$caption</th>";
		}
	?>
	</tr>
<?	if ($this->db->query($str)->num_rows()<=0){
		echo "<tr><td colspan=\"".(sizeof($arrCaption)+1)."\">Data Tidak Ditemukan</td></tr>";
	}else{
		$result=$this->db->query($str)->result();
		$no=1;
		foreach($result as $row){
			echo "<tr valign=top><td>$no</td>";
			echo "<td>".$row->NIK."</td>";			
			echo "<td>".$row->NAMA."</td>";			
			
			switch($jenis){
				case "prestasi":
					echo "<td>".$row->POSISI."</td>";
					echo "<td>".strftime('%d %B %Y',strtotime($row->TANGGAL))."</td>";			
					echo "<td>".$row->NAMA_PRESTASI."</td>";			
					echo "<td>".$row->KETERANGAN."</td>";
					break;
				case "pelatihan":
					echo "<td>".$row->POSISI."</td>";
					echo "<td>".strftime('%d %B %Y',strtotime($row->TANGGAL))."</td>";			
					echo "<td>".$row->NAMA_PELATIHAN."</td>";			
					echo "<td>".$row->KETERANGAN."</td>";
					break;
				case "pelanggaran":
					echo "<td>".$row->POSISI."</td>";
					echo "<td>".strftime('%d %B %Y',strtotime($row->TANGGAL))."</td>";			
					echo "<td>".$row->NAMA_PELANGGARAN."</td>";			
					echo "<td>".$row->KETERANGAN."</td>";
					break;
				case "resign":
					echo "<td>".$row->POSISI."</td>";
					echo "<td>".strftime('%d %B %Y',strtotime($row->TGL))."</td>";			
					echo "<td>".$row->ALASAN."</td>";			
					echo "<td>".$row->MENGETAHUI."</td>";
					echo "<td>".$row->MENYETUJUI."</td>";
					break;
				case "cuti":
					echo "<td>".$row->POSISI."</td>";
					echo "<td>".strftime('%d %B %Y',strtotime($row->TGL_TRANS))."</td>";			
					echo "<td>".$row->JENISCUTI1." - ".$row->JENISCUTI2."</td>";			
					echo "<td>".strftime('%d %B %Y',strtotime($row->TGL_AWAL))."</td>";
					echo "<td>".strftime('%d %B %Y',strtotime($row->TGL_AKHIR))."</td>";
					echo "<td>".$row->JML_HARI."</td>";
					echo "<td>".$row->KETERANGAN."</td>";
					echo "<td>".($row->APPROVED==0?"BLM DISETUJUI":"SUDAH DISETUJUI<BR>OLEH : ".$row->APPROVED_BY."<BR>PADA : ".$row->APPROVED_DATE)."</td>";
					break;
				case "lembur":
					echo "<td>".$row->POSISI."</td>";
					echo "<td>".strftime('%d %B %Y',strtotime($row->CREATED_DATE))."</td>";			
					echo "<td>".strftime('%d %B %Y',strtotime($row->TGL_LEMBUR))."</td>";			
					echo "<td>".strftime('%H:%M:%S',strtotime($row->JAM_MULAI))."</td>";
					echo "<td>".strftime('%H:%M:%S',strtotime($row->JAM_SELESAI))."</td>";
					echo "<td>".$row->JML_JAM."</td>";
					echo "<td>".$row->KETERANGAN."</td>";
					echo "<td>".($row->APPROVED==0?"BLM DISETUJUI":"SUDAH DISETUJUI<BR>OLEH : ".$row->APPROVED_BY."<BR>PADA : ".$row->APPROVED_DATE)."</td>";
					break;
				case "mutasi":
					echo "<td>".$row->MUTASI_BARU."</td>";
					echo "<td>".$row->MUTASI_LAMA."</td>";
					echo "<td>".strftime('%d %B %Y',strtotime($row->TGL_PENETAPAN))."</td>";
					echo "<td>".$row->KETERANGAN."</td>";
					echo "<td>".$row->MENGETAHUI."</td>";
					echo "<td>".$row->MENYETUJUI."</td>";
					break;

			}
			
			echo "</tr>";
			$no++;
		}
	}
		
	?>
	</thead><tbody>
	</tbody></table>
	</div>
</div>
<? 
	}
echo "<br>";
if ($display==0){
	  $param=$jenis."_1_".$bln."_".$thn;
?>

<div class="row" style="text-align:center">
	<div class="col-md-12">	
		<a href="<?=base_url('hrdReportRekapHRD/rekapResultMap/'.$param)?>" class="btn btn-success">Print Data Rekap</a>
		
	</div>
</div>	
<?
}
?>
