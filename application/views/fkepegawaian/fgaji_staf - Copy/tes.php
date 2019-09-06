<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">Entri Master Gaji / Tunjangan Staff</div><!-- /.panel-heading -->
				<div class="panel-body">
				<? echo var_dump($arrID)."<br>";
					foreach($strDel as $del ){
						echo $del;
					}
					foreach($strQ as $loop ){
						echo $loop;
					}
					foreach($arrID as $row ){
						foreach ($row as $col){
							echo $col."#";
						}
						echo "<br>";
					}
				
				?>
				</div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
</div>