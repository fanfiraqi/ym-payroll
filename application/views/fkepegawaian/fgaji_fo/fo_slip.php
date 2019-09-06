<?php errorHandler();?>

<div class="row">	
	<div class="col-md-6">
		<div class="panel panel-default"><div class="panel-heading">SLIP GAJI
		</div><div class="panel-body">

			<div class="row">
				<div class="col-xs-12">
					<div class="form-group"><label class="col-sm-4 control-label">NAMA KARYAWAN</label>
						<div class="col-sm-8"> : <?=$str."<br>".$row->NIK." - ".$row->NAMA;?></div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<div class="form-group"><label class="col-sm-4 control-label">FO LEVEL</label>
						<div class="col-sm-8"> : <?=$row->LEVEL;?></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group"><label  class="col-sm-4 control-label">GAJI BULAN</label>
						<div class="col-sm-8"> : <?=$strbulan." ".$thn;?></div>
					</div>
				</div>
			</div>
			

		</div></div>
	</div>
</div>