<?php echo form_open('gaji_staf/save_gaji_staff',array('class'=>'form-horizontal','id'=>'myform'));?>
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
							<th rowspan=2>NO</th>
							<th rowspan=2>NIK</th>
							<th rowspan=2>NAMA</th>
							<th rowspan=2>KANTOR</th>
							<th rowspan=2>JABATAN</th>
							<th rowspan=2>TGL MASUK</th>

							<th rowspan=2>CUTI</th>
							<th rowspan=2>SAKIT</th>
							<th rowspan=2>IJIN</th>
							<th rowspan=2>ALPA</th>
							<th rowspan=2>LEMBUR</th>
							<th colspan=3>T10 MENIT</th>							
							<th colspan=2>PULANG CEPAT</th>

							<th rowspan=2>MASA KERJA</th>
							<th rowspan=2>S/M/GM</th>
							
<?	$strMaster="select * from mst_komp_gaji  where isactive=1 and  is_staff='on' order by ID";
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
<?php //echo "<tr><td colspan=30>$strMaster</td></tr>"?>
						  <tr>
							<th >I</th>
							<th >II</th>
							<th>III</th>
							<th >JUMLAH</th>
							<th >MENIT</th>
						  <?php echo $thPend.$thPot?>
						  </tr>
						  
                         </thead>
                                    <tbody>
<?	//echo "<tr align=center><td colspan=\"".$colspanAll."\">$str</td></tr>";
	$keyDisabled=0;
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
		
		foreach($row as $hasil){
			$masakerja=0;
			$strmasakerja="-";
			if ($hasil->SELISIH=="" || empty($hasil->SELISIH)){
				$masakerja=0;
				$strmasakerja="-";
				$keyDisabled=1;
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
		//echo "<tr><td colspan=$p>select * from mst_jabatan where id_jab=".$hasil->ID_JAB."<br>idjab=".$rsjab->id_jab."</td></tr>";

		//GET ABSEN VALUE
		//hari kerja ambil dari database
			$jml_hadir=0;
			$jml_ijin=0;
			$jml_cuti=0;
			$jml_sakit=0;
			$jml_alpa=0;
			$T10_1=0;
			$T10_2=0;
			$T10_3=0;
			$PC_jml=0;
			$PC_mnt=0;
			$jam_lembur=0;
			$skerja="select * from rekap_absensi where periode='".$thn.$bln."' and nik='".$hasil->NIK."'";
			if ($this->db->query($skerja)->num_rows()>0){
				$rskerja=$this->db->query($skerja)->row();
				$jml_hadir=$rskerja->JML_MASUK;
				$jml_ijin=$rskerja->JML_IJIN;
				$jml_cuti=$rskerja->JML_CUTI;
				$jml_sakit=$rskerja->JML_SAKIT;
				$jml_alpa=$rskerja->JML_ALPA;
				$T10_1=$rskerja->T10_1;
				$T10_2=$rskerja->T10_2;
				$T10_3=$rskerja->T10_3;
				$PC_jml=$rskerja->PULANG_AWAL_JML;
				$PC_mnt=$rskerja->PULANG_AWAL_MENIT;
				$jam_lembur=$rskerja->JAM_LEMBUR;
			}
			//echo "<tr><td colspan=20>$skerja</td></tr>";

			$tanggal_aktif=($hasil->TGL_AKTIF==""||empty($hasil->TGL_AKTIF)?"TGL MASUK BLM DI SET":strftime("%d %B %Y",strtotime($hasil->TGL_AKTIF) ) );
?>
	<tr>
		<td><?=$i?></td>
		<td><?=$hasil->NIK?><input type="hidden" name="nik_<?=$i?>" id="nik_<?=$i?>" value="<?=$hasil->NIK?>"></td>
		<td><?=str_replace(" ","&nbsp;",$hasil->NAMA)?><input type="hidden" name="flag_<?=$i?>" id="flag_<?=$i?>" value="1"></td>
		<td><?=str_replace(" ","&nbsp;",$rscab->kota)?><input type="hidden" name="id_cabang_<?=$i?>" id="id_cabang_<?=$i?>" value="<?php echo $hasil->ID_CABANG?>"></td>
		<td><?=str_replace(" ","&nbsp;",$rsjab->nama_jab)?><input type="hidden" name="idjab_<?=$i?>" id="idjab_<?=$i?>" value="<?=$hasil->ID_JAB?>"></td>
		<td><?=str_replace(" ","&nbsp;", $tanggal_aktif)?></td>
		<!--  -->
		<td ><input type="hidden" name="jml_cuti_<?=$i?>" id="jml_cuti_<?=$i?>" value="<?=$jml_cuti;?>"><?php echo $jml_cuti?></td>
		<td ><input type="hidden" name="jml_sakit_<?=$i?>" id="jml_sakit_<?=$i?>" value="<?=$jml_sakit;?>"><?php echo $jml_sakit?></td>
		<td ><input type="hidden" name="jml_ijin_<?=$i?>" id="jml_ijin_<?=$i?>" value="<?=$jml_ijin;?>"><?php echo $jml_ijin?></td>
		<td ><input type="hidden" name="jml_alpa_<?=$i?>" id="jml_alpa_<?=$i?>" value="<?=$jml_alpa;?>"><?php echo $jml_alpa?></td>
		<td ><input type="hidden" name="jam_lembur_<?=$i?>" id="jam_lembur_<?=$i?>" value="<?=$jam_lembur;?>"><?php echo $jam_lembur?></td>
		<td ><input type="hidden" name="T10_1_<?=$i?>" id="T10_1_<?=$i?>" value="<?=$T10_1;?>"><?php echo $T10_1?></td>
		<td ><input type="hidden" name="T10_2_<?=$i?>" id="T10_2_<?=$i?>" value="<?=$T10_2;?>"><?php echo $T10_2?></td>
		<td ><input type="hidden" name="T10_3_<?=$i?>" id="T10_3_<?=$i?>" value="<?=$T10_3;?>"><?php echo $T10_3?></td>		
		<td ><input type="hidden" name="PC_jml_<?=$i?>" id="PC_jml_<?=$i?>" value="<?=$PC_jml;?>"><?php echo $PC_jml?></td>
		<td ><input type="hidden" name="jml_hadir_<?=$i?>" id="jml_hadir_<?=$i?>" value="<?=$jml_hadir;?>">
		<input type="hidden" name="PC_mnt_<?=$i?>" id="PC_mnt_<?=$i?>" value="<?=$PC_mnt;?>"><?php echo $PC_mnt?></td>
		<!--  -->
		<td><?=form_hidden(array('name'=>'strMasaKerja_'.$i,'id'=>'strMasaKerja_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>$strmasakerja)).str_replace(" ","&nbsp;",$strmasakerja);?><input type="hidden" name="masakerja_<?=$i?>" id="masakerja_<?=$i?>" value="<?=$masakerja?>"></td>
<?	//get key yg dipakai smua komp
	$msk="";
	$cekgapok="";
	$cekmakan="";
	$cekmasakerja="";
	$cekTHT="";
	$sumPer_row=0; 
	$gapok=0;$THT=0;
	$sumPotPer_row=0;
	$j=0;
	$display_acuan=0; $acuan=0;
	$display_angsuran=0; $cicilke=0; $id_header=0;
	$id_cabang=$hasil->ID_CABANG;
	$id_jab=$hasil->ID_JAB;	
	$grade="";	//grade sdh difilter
	$sqlgrade=$this->db->query("select * from mst_grade_cabang where id_cabang=".$id_cabang);
	if ($sqlgrade->num_rows()>0){
    	$rsgrade=$sqlgrade->row();
	    $grade=$rsgrade->grade;
	}	
	$rsklaster=$this->gate_db->query("select klaster, kelompok_gaji from mst_jabatan where id_jab=".$id_jab)->row();
	$klaster="";
	$kelompok_gaji="";
	if (sizeof($rsklaster)>0){
		$klaster=$rsklaster->klaster;
		$kelompok_gaji=$rsklaster->kelompok_gaji;
	}
	//$bgcolor="";
	echo '<td><input type="hidden" name="kelompok_gaji_'.$i.'" id="kelompok_gaji_'.$i.'" value="'.$rsklaster->kelompok_gaji.'">'.$rsklaster->kelompok_gaji.'</td>';
	foreach($master as $rowmaster){
		$nominal=0;
		$bgcolor=""; $readonly=true;
		//PENDAPATAN
		if ($rowmaster->FLAG=='+'){
		$cekmasuk="";$str1=$str2=$str3=$str4=$str5=$str6="";
		switch ($rowmaster->ID){
			case "1":	//gapok, table
				$nominal=0;
				//nik, id_cabang, id_jab, get grade_cabang
				$str1="select ifnull(nominal,0) nominal from mst_gapok where id_jabatan=".$id_jab." and grade_cabang='".$grade."' and (".$masakerja." between lama_kerja_awal and lama_kerja_akhir)";
				$rsgapok=$this->db->query($str1)->row();
				
				if (sizeof($rsgapok)>0){
					$nominal = $rsgapok->nominal;	
					$gapok = $nominal;	
				}else{
					$cekgapok="Gapok blm diset";
					$keyDisabled=1;
				}
				$cekmasuk="1";
				break;
			case "2":	//Acuan Uang makan, table
			//W7-((H7+I7+(J7*2)+M7)*(W7/25))-(K7*5000)-(L7*10000)
				$str2="select per_bulan from mst_acuan_makan where	id_cabang=".$id_cabang;
				$rsmakan=$this->db->query($str2)->row();
				if (sizeof($rsmakan)>0){
					$display_acuan=1;
					$acuan = $rsmakan->per_bulan;
					if ($kelompok_gaji=='M'){
						$nominal = $acuan - ( ($jml_sakit + $jml_ijin + (2 * $jml_alpa) ) * ($acuan/25) );
					}else{
						$nominal = $acuan - ( ($jml_sakit + $jml_ijin + (2 * $jml_alpa) + $T10_3) * ($acuan/25) ) - ( $T10_1*5000 ) - ( $T10_2 * 10000 );
					}

					
				}else{
					$cekmakan="Acuan makan blm diset";
					$keyDisabled=1;
				}
				$cekmasuk="2";
				break;
			case "3":	//Insentif Kehadiran, var
			//IF(H7>1;0;IF(I7>0;0;IF(J7>0;0;IF((K7+L7+M7)>3;0;150000))))
				$str3="select ifnull(nominal,0) nominal from mst_komp_var where	id_komp=3";
				$rshadir=$this->db->query($str3)->row();
				if (sizeof($rshadir)>0){
					$insentif_hadir = $rshadir->nominal;
					$acuan=$insentif_hadir;
					if ($hasil->STATUS_PEGAWAI >=3){
						if ($kelompok_gaji=='S'){
						$nominal = ($jml_sakit>1?0:($jml_ijin>0?0:($jml_alpa>0?0:(($T10_1+$T10_2+$T10_3)>3?0:$insentif_hadir) ) ) );
						}else{
							$nominal=0;
						}
					}
				}
				$cekmasuk="3";
				break;
			case "4":	//Insentif Lembur, var
				//=IF(V25="S";N25*8500;0)
				//jam lembur tabel absen atau jam lembur dari permohonan ijin ?
				$str3="select ifnull(nominal,0) nominal from mst_komp_var where	id_komp=4";
				$rshadir=$this->db->query($str3)->row();
				if (sizeof($rshadir)>0){
					$uang_lembur = $rshadir->nominal;
					$nominal=($kelompok_gaji=='S'?$jam_lembur * $uang_lembur:0);
				}
				$cekmasuk="3";
				break;
			case "5":	//Tj. Keluarga, lain2
				//IF(T7="PRA";0;IF(AND(P7="SN";O7="L");(((Q7*0,1)+(R7*0,05))*X7);IF(P7="JN";(R7*0,05*X7);IF(P7="DN";(R7*0,05*X7);0))))
				//Diberikan untuk Karyawan dengan Status PRA KARYAWAN TETAP atau yang telah menyelesaikan masa Kontrak ke II. 
				$nominal=0;
				if ($hasil->STATUS_PEGAWAI >=5){	//PRA KARY dan KARY. TETAP
					if ($hasil->STATUS_NIKAH == 'SN' && $hasil->SEX==1){
						$nominal = (((1*0.1)+($hasil->JUMLAH_ANAK*0.05))*$gapok);	//1 jumlah istri 
						$msk="lk2";
					}elseif ($hasil->STATUS_NIKAH == 'JN' || $hasil->STATUS_NIKAH == 'DN'){
						$nominal =($hasil->JUMLAH_ANAK*0.05*$gapok);
						$msk="JNDN";
					}					
				}
				
				$cekmasuk="3";
				break;
			case "6":	//Tj. Masa Kerja, table
				$str4="select ifnull(nominal,0) nominal from mst_tunj_masa_kerja where tahun_ke <=".$thnMasakerja." ORDER BY tahun_ke DESC LIMIT 1";
				$rsTmk=$this->db->query($str4)->row();
				if (sizeof($rsTmk)>0){
					$nominal = $rsTmk->nominal;
				}else{
					$cekmasakerja="Tj Masa kerja blm diset";
					//$keyDisabled=1;
					//jika masa kerja < 1 thn =0;
				}
				$cekmasuk="6";
				break;
			case "7":	//Tj. Jabatan, table
				//cek id_jab sbg kepala cabang/bukan (16,17)
				/*if ($id_jab==16 || $id_jab==17){	
					$str5="select ifnull(".($id_cabang<=1?"nominal_pusat":"nominal_cabang").",0) nominal from mst_tunj_jabatan where jenis='Kepala Cabang' and grade='".$grade."'";
					$rsTjab=$this->db->query($str5)->row();
					if (sizeof($rsTjab)>0){
						$nominal = $rsTjab->nominal;
					}

				}else{	//get klaster , klo cabang kolom nominal_cabang, pusat = nominal_pusat
					$str6="select ifnull(".($id_cabang<=1?"nominal_pusat":"nominal_cabang").", 0) nominal from mst_tunj_jabatan where jenis='Level Jabatan' and grade='".$klaster."'";
					$rsTjab=$this->db->query($str6)->row();
					if (sizeof($rsTjab)>0){
						$nominal = $rsTjab->nominal;
					}
				}*/
				//entri manual
				$nominal=0;
				$bgcolor="#ffffcc";
				$readonly=false;
				break;
			case "8":	//Tj. Hari Tua (THT), table
				//IF(AND(V10="S";S10="SMA");50000;IF(AND(V10="S";S10="SARJANA");100000;IF(OR(V10="M";V10="GM");250000)))
				$pddk=(in_array($hasil->PENDIDIKAN, ['SMA','D1', 'D2','D3']) ? "SMA":"SARJANA");
				$str4="select ifnull(nominal,0) nominal from mst_tht where kelompok_jab='".$kelompok_gaji."' and pendidikan='".$pddk."' ORDER BY id DESC LIMIT 1";
				$rsTHT=$this->db->query($str4)->row();
				if (sizeof($rsTHT)>0){
					$nominal = $rsTHT->nominal;
					$THT = $rsTHT->nominal;
				}else{
					$cekTHT="THT pendidikan ".$hasil->PENDIDIKAN." blm diset";
					$keyDisabled=1;
				}
				$cekmasuk="8";
				
				break;
			case "11":	//BPJS Kesehatan
				$nominal = 0;	
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "12":	//BPJS Ketenagakerjaan
				$nominal = 0;	
				$bgcolor="#ffffcc";$readonly=false;
				break;
			//case "12":	//Penyesuaian
			case "13":	//Penyesuaian
				$nominal = 0;	
				$bgcolor="#ffffcc";$readonly=false;
				break;
			default: 
				$nominal=0;$cekmasuk="default";
				$bgcolor="#ffffcc";$readonly=false;
				break;

		}
		$nominal=round($nominal, 0);
		echo "<td>";
		if ($display_acuan==1){
			echo number_format($acuan,0,',','.').'<input type="hidden" name="acu_makan_'.$i.'" id="acu_makan_'.$i.'" value="'.$acuan.'">';
		}
		
		switch ($rowmaster->ID){
			case "1": echo $cekgapok; break;
			case "2": echo $cekmakan; break;
			case "6": echo $cekmasakerja; break;
			case "8": echo $cekTHT; break;
		}
		echo form_input(array('name'=>'komp_'.$i.'_'.$j,'id'=>'komp_'.$i.'_'.$j,'class'=>'myform-control','size'=>10,  'value'=>$nominal, "style"=>"background-color:".$bgcolor, "onkeyup"=>"countRevenue(".$i.", ".$j.", this)", "onkeypress"=>"return numericVal(this,event)"))."</td>";
		if ($rowmaster->FLAG=='+'){ 
			$sumPer_row+=$nominal;
		}else{
			$sumPer_row-=$nominal;
		}
		if ($rowmaster->ID==13){?>
			<td><?=form_input(array('name'=>'subTotal_'.$i,'id'=>'subTotal_'.$i,'class'=>'myform-control subtotal','size'=>10, 'value'=>round($sumPer_row,0)));?></td>

		<? }
		//$j++; => bertambah 1 utk komp
		}else{	//end pendapatan
			//POTONGAN
			$nominal=0;
			switch ($rowmaster->ID){
			case "15":	//Dana Sosial
				$nominal=0;
				//IF(AH10<=3203846;AH10*2,5%;IF(AND(AH10>=3203846;V10="S");AH10*1%;IF(AND(AH10>=3203846;V10="M");AH7*2%;IF(AND(AH10>=3203846;V10="GM");AH10*2,5%;"CEK LAGI"))))
				$str1="select ifnull(nominal,0) nominal from mst_komp_var where id_komp=15";
				$rsdansos=$this->db->query($str1)->row();
				if (sizeof($rsdansos)>0){
					$dansos = $rsdansos->nominal;	
					$nominal = $sumPer_row*0.025;	
					//$nominal=round($nominal, 0);
				}
				$cekmasuk="1";
				break;
			case "16":	//zakat
				$nominal= ($hasil->KESEDIAAN_ZAKAT=='1'?$sumPer_row*0.025:0);
								
				break;
			case "17":	//THT
				$nominal=$THT;				
				break;
			case "19":	// Lain-lain
				$nominal=0;	
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "20":	//Family Gathering
				$nominal=0;				
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "21":	//Iuran Qurban
				$nominal=0;		
				$bgcolor="#ffffcc";$readonly=false;
				break;
			case "22":	//Angsuran Pinjaman
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
			}
			echo "<td>";
			if ($display_angsuran==1){
				echo 'Cicilan ke:'.$cicilke.'<input type="hidden" name="cicilke_'.$i.'" id="cicilke_'.$i.'" value="'.$cicilke.'"><input type="hidden" name="id_header_'.$i.'" id="id_header_'.$i.'" value="'.$id_header.'">';
			}
			echo '<input type="hidden" name="sedia_zakat_'.$i.'" id="sedia_zakat_'.$i.'" value="'.$hasil->KESEDIAAN_ZAKAT.'">';
			echo form_input(array('name'=>'komp_'.$i.'_'.$j,'id'=>'komp_'.$i.'_'.$j,'class'=>'myform-control','size'=>10,  "style"=>"background-color:".$bgcolor, "onkeypress"=>"return numericVal(this,event)","onkeyup"=>"countExpense(".$i.", ".$j.", this)", 'value'=>round($nominal,0)))."</td>";
			if ($rowmaster->FLAG=='-'){ 
				$sumPotPer_row-=$nominal;
			}else{
				$sumPotPer_row+=$nominal;
			}
		}
		$j++;
		$display_acuan=0;
	}

	

if ($rowmaster->ID==22){
	?>
	<td><?=form_input(array('name'=>'subPotTotal_'.$i,'id'=>'subPotTotal_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>round($sumPotPer_row*-1,0) ) );?></td>
	<?
	
}?>	

<td><?=form_input(array('name'=>'subGrandTotal_'.$i,'id'=>'subGrandTotal_'.$i,'class'=>'myform-control','size'=>10,'readonly'=>true, 'value'=>round($sumPer_row+$sumPotPer_row,0) ) );?></td><td>&nbsp; -</td></tr>			
			
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
										<input type="hidden" name="laz_tasharuf" id="laz_tasharuf" value="<?=$laz_tasharuf?>">
										<input type="hidden" name="wilayah" id="wilayah" value="<?=$wilayah?>">
										<input type="hidden" name="sts" id="sts" value="<?=$sts?>">
										<input type="hidden" name="id_validasi" id="id_validasi" value="<?=$id_validasi?>">
										<input type="button" class="btn btn-primary" id="btsimpankel" value="Simpan" <?php echo ($keyDisabled==1?"disabled":"")?>>		
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
			url: '<?php echo base_url('gaji_staf/save_gaji_staff');?>',
			data: form_data,				
			dataType: 'json',
			success: function(msg) {
				// $("#errorHandler").html('&nbsp;').hide();
				 console.log(msg);
				if(msg.status =='success'){
					
					$().showMessage('Data Gaji Staff berhasil disimpan.','success',1000);
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

		var total= parseFloat( $("#komp_"+idr+"_"+0).val() ) +  parseFloat( $("#komp_"+idr+"_"+1).val() ) +  parseFloat( $("#komp_"+idr+"_"+2).val() ) +  parseFloat( $("#komp_"+idr+"_"+3).val() ) +  parseFloat( $("#komp_"+idr+"_"+4).val() ) +  parseFloat( $("#komp_"+idr+"_"+5).val() ) +  parseFloat( $("#komp_"+idr+"_"+6).val() ) +  parseFloat( $("#komp_"+idr+"_"+7).val() ) +  parseFloat( $("#komp_"+idr+"_"+8).val() ) +  parseFloat( $("#komp_"+idr+"_"+9).val() ) +  parseFloat( $("#komp_"+idr+"_"+10).val() ) ;
		
		

		var isidansos=Math.round((total*0.025));
		$("#komp_"+idr+"_"+11).val(isidansos );
		
		if ($("#sedia_zakat_"+idr).val()=="1")	{	//zakat
			$("#komp_"+idr+"_"+12).val(isidansos);
		}
		
		
		$("#subTotal_"+idr).val(total);
		$("#subGrandTotal_"+idr).val( parseFloat( $("#subTotal_"+idr).val() ) - parseFloat(  $("#subPotTotal_"+idr).val()) ) ;
		countExpense(idr, idc, obj);
	}
	
	/*$(document).on('change','.subtotal', function(){
	    var od=$(this);
	    alert(od.attr('id'));
	    
	});*/
	function countExpense(idr, idc, obj){
		var total= parseFloat( $("#komp_"+idr+"_"+11).val() ) +  parseFloat( $("#komp_"+idr+"_"+12).val() ) +  parseFloat( $("#komp_"+idr+"_"+13).val() ) +  parseFloat( $("#komp_"+idr+"_"+14).val() ) +  parseFloat( $("#komp_"+idr+"_"+15).val() ) +  parseFloat( $("#komp_"+idr+"_"+16).val())+  parseFloat( $("#komp_"+idr+"_"+17).val() ) ;

		$("#subPotTotal_"+idr).val(total);
		$("#subGrandTotal_"+idr).val( parseFloat( $("#subTotal_"+idr).val() ) - parseFloat(  $("#subPotTotal_"+idr).val()) ) ;
	}


	
</script>