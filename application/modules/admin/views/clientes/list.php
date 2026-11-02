<script type="text/javascript">
$(document).ready(function()
{
	<?php $this->load->view('partials/script_list.php'); ?>
});
	
	
</script>
<?php 
	$this->load->view('partials/delete_modal.php'); 
 	$this->load->view('partials/page_header.php'); 
 ?>

  <?php
 $cantidad = 0; 
 foreach($rows as $row){ $cantidad++;} ?>

<div class="row">
	<div class="span12">
		<div class="widget widget-table">
			<div class="widget-content">
				<div id="example_wrapper" class="dataTables_wrapper form-inline" role="grid">
					<?php // if(isset($message)) echo $message;?>
					<div class="row">
						<?php 
							$this->load->view('partials/table_perpage.php');
							$this->load->view('partials/search.php');
						?>
					</div>

					<div class="row">
						<p style="padding-left: 10px;margin-bottom: 0px;"> Cantidad de sucursales: <?php echo $num_results; ?> </p>
					</div>
					
					<table id="list" class="table table-bordered table-striped table-highlight">
						<?php $this->load->view('partials/table_header.php'); ?>
						<form action="<?php echo base_url('admin/'.$dir.'/delete/');?>" id="delete-form" method="POST" class="form-horizontal">
							<tbody>
								<?php foreach($rows as $row): ?>

									<?php

									$estado = '';
									$caja = '';

									if($row->activo == 1)
									{
										$estado = 'Habilitado';
									}
									else
									{
										$estado = 'Deshabilitado';
									}

									if($row->caja_cerrada == 1)
									{
										$caja = 'Cerrada';
									}
									else
									{
										$caja = 'Abierta';
									}

									?>
								<tr>
									<td style="text-align:center;"><input type="checkbox" name="ele[]" id="ele_<?php echo $row->idusario;?>" class="ele" value="<?php echo $row->idusuario;?>" /></td>
									<?php foreach($fields as $field_name => $field_display):
										if($field_display == 'Estado'): ?>
												<td>
													<a href="<?php echo site_url('admin/'.$dir.'/edit/')."/".$row->idusuario;?>"><?php echo character_limiter($estado, 120); ?></a>			</a>
												</td>
										<?php 
										elseif($field_display == 'Caja diaria'): ?>
												<td>
													<a href="<?php echo site_url('admin/'.$dir.'/edit/')."/".$row->idusuario;?>"><?php echo character_limiter($caja, 120); ?></a>			</a>
												</td>
										<?php 
										else: ?>
											<td>
												<a href="<?php echo site_url('admin/'.$dir.'/edit/')."/".$row->idusuario;?>"><?php echo character_limiter($row->$field_name, 120); ?></a>
											</td>
										<?php
										endif;
									endforeach; ?>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</form>
					</table>
					<?php $this->load->view('partials/table_footer.php'); ?>
				</div>
			</div>
		</div>
	</div>	
</div>	