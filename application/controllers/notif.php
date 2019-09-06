<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class notif extends MY_App {

	function __construct()
	{
		parent::__construct();
		$this->auth->authorize();
	}
	
	
	function index(){
		$role=$this->session->userdata('auth')->ROLE;
		$respon = array();
		$respon['status'] = 0;
		
		// query cek permohonan ijin baru
		$query = $this->db->query('select count(id) cnt from cuti where ISACTIVE=1')->row();
		if ($query->cnt > 0){
			$respon['status'] = 1;
			$respon['data']['cuti'] = array(
				'text' => 'Permohonan Ijin/Cuti Baru',
				'count'=> $query->cnt,
				'url'=>base_url('cuti/approval')
			);
		}
		
		// query cek permohonan lembur baru
		$query = $this->db->query('select count(no_trans) cnt from lembur where ISACTIVE=1')->row();
		if ($query->cnt > 0){
			$respon['status'] = 1;
			$respon['data']['lembur'] = array(
				'text' => 'Permohonan Lembur Baru',
				'count'=> $query->cnt,
				'url'=>base_url('lembur/approval')
			);
		}
		
		// query cek gaji staff yg blm di set
		$query = $this->db->query('select count(distinct(nik)) cnt from pegawai where nik not in (select distinct(nik) from set_gaji_staff) and status_aktif=1 and id_jab<>13 and id_jab<>14')->row();
		//$role=$this->session->userdata('auth')->ROLE;
		if ($query->cnt > 0){
			$url=($role=="Manager HRD" || $role=="Direktur HRD" || $role=="Operator HRD"?"":base_url('payroll_staff/notif_unset_gaji'));
			$respon['status'] = 1;
			$respon['data']['set_gaji'] = array(
				'text' => 'Staff yang belum di set gaji',
				'count'=> $query->cnt,
				'url'=>$url
			);
		}
		
		// query cek jml karyawan yg mau ultah
		$query = $this->db->query("select count(distinct nik) cnt  from pegawai where status_aktif=1 and date_format(tgl_lahir,'%m')=date_format(now(), '%m')")->row();
		if ($query->cnt > 0){
			$respon['status'] = 1;
			$respon['data']['ultah'] = array(
				'text' => 'Pegawai Berulang tahun bulan ini',
				'count'=> $query->cnt,
				'url'=>base_url('sendEmail/ultah_peg')
			);
		}
		
		//notif : 2 mgg sblm kontrak berakhir, pengingat cuti habis/sdh waktunya dpt cuti
		if ($role=="Admin" || $role=="Manager HRD" || $role=="Direktur HRD" ){

			if ($respon['status']==1){
			?>
			
			<ul class="dropdown-menu dropdown-alerts" id="notifitem">
			<?php 
				foreach ($respon['data'] as $data=>$item){
			?>
				<li>
					<a href="<?php echo $item['url'];?>">
						<div>
							<i class="fa fa-comment fa-fw"></i> <?php echo $item['text'];?>
							<span class="pull-right small"><?php echo $item['count'];?></span>
						</div>
					</a>
				</li>
			<?php } ?>
			</ul>
			<?php
			} else {
				echo 'none';
			}
		}
		//echo json_encode($respon);
	}
	
}