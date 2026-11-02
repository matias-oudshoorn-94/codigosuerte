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
					<table id="list" class="table table-bordered table-striped table-highlight">
						<?php $this->load->view('partials/table_header.php'); ?>
						<form action="<?php echo base_url('admin/'.$dir.'/delete/');?>" id="delete-form" method="POST" class="form-horizontal">
							<tbody>
							<?php foreach($rows as $row): ?>
							<tr>
								<td style="text-align:center;">
									<input type="checkbox" name="ele[]" id="ele_<?php echo $row->id;?>" class="ele" value="<?php echo $row->id;?>" />
								</td>

								<td><a href="<?php echo site_url('admin/'.$dir.'/edit/'.$row->id);?>"><?php echo $row->nombre; ?></a></td>
								<td><?php echo $row->codigo; ?></td>
								<td><?php echo $row->precio; ?></td>
								<td><?php echo $row->porcentaje_marcado; ?></td>
								<td><?php echo $row->precio_final; ?></td>

								<td>
									<?php if (!empty($stocks_por_producto[$row->id])): ?>
										<ul style="margin:0; padding-left: 15px;">
											<?php foreach($stocks_por_producto[$row->id] as $stock): ?>
												<li style="font-size: 11px;">
													<?php echo $stock->sucursal . ': ' . $stock->stock; ?>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php else: ?>
										<em>Sin stock ❌</em>
									<?php endif; ?>
								</td>

								<td><?php echo $row->fecha_vencimiento; ?></td>
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