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
									<td style="text-align:center;"><input type="checkbox" name="ele[]" id="ele_<?php echo $row->id;?>" class="ele" value="<?php echo $row->id;?>" /></td>
									<?php foreach($fields as $field_name => $field_display):
										if($field_display == 'Logo'): ?>
												<td>
													<a  class="sub_edit" href="<?php echo site_url('admin/'.$dir.'/edit/'.$row->id);?>"><img class="min-back" src="<?php echo base_url('assets/archivos/'.$dir.'/original/'.$row->logo);?>" alt="" style="opacity: 1; ">			</a>
												</td>
										<?php
										else: ?>
											<td>
												<a href="<?php echo site_url('admin/'.$dir.'/edit/')."/".$row->id;?>"><?php echo character_limiter($row->$field_name, 120); ?></a>
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