<? 
$setMenu=$this->config->item('mymenu');
$mySubMenu=$this->config->item('mySubMenu');
//$sess=$this->session->userdata('gate');
$group_id=$this->session->userdata('group_id');
$role=$group_id;
/*
1	superadmin
4	administrator_payroll		v
60  STAFF PAYROL HRD-PERSONALIA	v
65  MANAGER HR & GA				v

11	direktur_divisi				L
12	direktur_laz				L
13	direktur_lpp				L
18	gm_keuangan					L

20	gm_ro						L

26	kepala_cabang				L

*/

?>
<!-- Left Sidebar Menu -->
		<div class="fixed-sidebar-left">
			<ul class="nav navbar-nav side-nav nicescroll-bar">
			<li><hr class="light-grey-hr mb-10"/></li>
			<li class="navigation-header">
				<?php echo anchor("",'<div class="pull-left"><i class="fa fa-home mr-20"></i><span class="right-nav-text">Beranda</span></div>');?>
			</li>
			<li><hr class="light-grey-hr mb-10"/></li>
			<?php if (in_array($role, [1,4,60,65])){?>
			<li <?=($setMenu=="mn1"?"class='open'":"")?>>
				<a href="javascript:void(0);" data-toggle="collapse" data-target="#pengaturan_dr"><div class="pull-left"><i class="fa fa-gear mr-20"></i><span class="right-nav-text">Pengaturan</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>

				<ul id="pengaturan_dr" class="collapse collapse-level-1">
					<?php if (in_array($role, [1,4,60,65])){?>
					<!-- <li ><?php echo anchor('setting/parameter','Rekening Payroll ',($mySubMenu=="mn122"?"class='active-page'":""));?></li> -->									
					<li ><?php echo anchor('setting/email','Set Email Sender',($mySubMenu=="mn123"?"class='active-page'":""));?></li>									
					<li ><?php echo anchor('komponengaji/index','Komponen Gaji ',($mySubMenu=="mn11"?"class='active-page'":""));?></li>									
					<li ><?php echo anchor('komponengaji/parameter','Set Variabel Komponen ',($mySubMenu=="mn12"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('master_payroll/grade','Grade Cabang',($mySubMenu=="mn13"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('komponengaji/jenisdonasi','Kelompok Jenis Donasi',($mySubMenu=="mn124"?"class='active-page'":""));?></li>
					<!-- <li ><?php echo anchor('komponengaji/setnominal','Set Nominal Variabel',($mySubMenu=="mn13"?"class='active-page'":""));?></li> -->		
					<li><hr class="light-grey-hr mb-10"/></li>
					<li ><?php echo anchor('master_payroll/gapok','Gaji Pokok ',($mySubMenu=="mn14"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('master_payroll/acuan_makan','Acuan Makan',($mySubMenu=="mn15"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('master_payroll/acuan_transport','Acuan Transport',($mySubMenu=="mn16"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('master_payroll/tunj_jabatan','Tunjangan Jabatan ',($mySubMenu=="mn17"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('master_payroll/tunj_masakerja','Tunjangan Masa Kerja ',($mySubMenu=="mn18"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('master_payroll/tunj_haritua','Tunjangan Hari Tua',($mySubMenu=="mn19"?"class='active-page'":""));?></li>
					<!-- <li ><?php echo anchor('master_payroll/tunj_pengambilan','Tunjangan Pengambilan',($mySubMenu=="mn120"?"class='active-page'":""));?></li> -->
					<li ><?php echo anchor('master_payroll/tunj_prestasi','Tunjangan Prestasi Zisco',($mySubMenu=="mn121"?"class='active-page'":""));?></li>
					<?}?>				
				</ul>
			</li>
				<!-- /.nav-second-level -->
			<li><hr class="light-grey-hr mb-10"/></li>
			<? } ?>
				
			<?php if  (in_array($role, [1,4,60,65])){?>
			<!-- <li><hr class="light-grey-hr mb-10"/></li> -->
			<li <?=($setMenu=="mn3"?"class='open'":"")?>>
				<a href="javascript:void(0);" data-toggle="collapse" data-target="#payroll_dr"><div class="pull-left"><i class="fa fa-money mr-20"></i><span class="right-nav-text">Penggajian</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
				<ul id="payroll_dr" class="collapse collapse-level-1">				
					<li ><?php echo anchor('gaji_staf/index','Staff Dalam',($mySubMenu=="mn31"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('gaji_zisco_transport/index','Transport Zisco',($mySubMenu=="mn32"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('gaji_zisco_bonus/index','Bonus Zisco',($mySubMenu=="mn33"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('gaji_kacab_bonus/index','Bonus Kacab',($mySubMenu=="mn34"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('gaji_manual/index','Non Sistem',($mySubMenu=="mn36"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('emailPage/index','Kirim Email Slip',($mySubMenu=="mn35"?"class='active-page'":""));?></li>				
				</ul>
			</li>
			<?}?>
			
			<?php if (in_array($role, [1,4,60,65])){?>
			<li><hr class="light-grey-hr mb-10"/></li>
			<li <?=($setMenu=="mn4"?"class='open'":"")?>>
				<a href="javascript:void(0);" data-toggle="collapse" data-target="#thr_dr"><div class="pull-left"><i class="fa fa-gift mr-20"></i><span class="right-nav-text">THR</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>

				<ul id="thr_dr" class="collapse collapse-level-1">					
					<li ><?php echo anchor('thr_staff/index','THR Staff Dalam',($mySubMenu=="mn41"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('thr_zisco/index','THR ZISCO',($mySubMenu=="mn42"?"class='active-page'":""));?></li>
					<li ><?php echo anchor('thr_nonsistem/index','THR Non Sistem',($mySubMenu=="mn44"?"class='active-page'":""));?></li>
					<li><?php echo anchor('emailPageTHR/index','Kirim Email THR',($mySubMenu=="mn43"?"class='active-page'":""));?></li>
				</ul>
			</li>
			<?	} ?>

		
			<?php if (in_array($role, [1,4,60,65, 11,12,13,18,20,26])){?>
			<li><hr class="light-grey-hr mb-10"/></li>
			<li <?=($setMenu=="mn5"?"class='open'":"")?>>
				
				<a href="javascript:void(0);" data-toggle="collapse" data-target="#reporting_dr"><div class="pull-left"><i class="fa fa-print mr-20"></i><span class="right-nav-text">Laporan</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
		
				<ul id="reporting_dr" class="collapse collapse-level-1">
						
						<li ><?php echo anchor('rptPayroll/staff','Rekap Gaji Staff',($mySubMenu=="mn51"?"class='active-page'":""));?></li>
						<li ><?php echo anchor('rptPayroll/zisco','Rekap Gaji Zisco',($mySubMenu=="mn52"?"class='active-page'":""));?></li>
				<?php if (in_array($role, [1,4,60,65, 11,12,13,18])){?>
						<li ><?php echo anchor('rptPayroll/bonus_kacab','Rekap Gaji Bonus Kacab',($mySubMenu=="mn53"?"class='active-page'":""));?></li>
						<li ><?php echo anchor('rptPayroll/nonsistem','Rekap Gaji Non Sistem',($mySubMenu=="mn54"?"class='active-page'":""));?></li>
				<?}?>
						<li ><?php echo anchor('rptPayroll/thr','Rekap THR',($mySubMenu=="mn55"?"class='active-page'":""));?></li>
					<?php if (!in_array($role, [20,26])){?>
						<li ><?php echo anchor('rptPayroll/nasional','Rekap Gaji Nasional',($mySubMenu=="mn56"?"class='active-page'":""));?></li>
					<?}?>	
				</ul>

			</li>
			<li><hr class="light-grey-hr mb-10"/></li>
			<?}?>
			</ul>
			<div  style="position:fixed; top:95%;z-index:1080;margin-left:20px;"><label  style="text-align:center; font-size:x-small;"><b>Best Running with Firefox @2018</b></label></div> 
		</div>
		<!-- /Left Sidebar Menu -->