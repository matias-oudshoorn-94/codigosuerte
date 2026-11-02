
<script type="text/javascript">
$(document).ready(function()
{
	// if all checkbox are selected, check the selectall checkbox
	// and viceversa
	$("#sub_all").click(function () {
		$('.sub_ele').attr('checked', this.checked);
	});
	$(".sub_ele").click(function(){
		if($(".sub_ele").length == $(".sub_ele:checked").length) {
			$("#sub_all").attr("checked", "checked");
		} else {
			$("#sub_all").removeAttr("checked");
		}
	});
	
	$('#sub_nuevo').click(function()
	{		
		$( "#pedidos" ).load( "<?php echo base_url('admin/'.$dir.'/sub_edit/0/')?>", { sub_id: '<?php echo $sub_id?>' } );	
	});	
	$('.sub_edit').click(function(event)
	{
		$( "#pedidos" ).load( $(this).attr('href'),{ sub_id: '<?php echo $sub_id?>' } );	
		return false;
	});

	$('#sub_eliminar').click(function()
	{
		var selectedItems = new Array();
		$("input[@name='sub_ele[]']:checked").each(function(){
			selectedItems.push($(this).val());
		});
		if (selectedItems.length > 0)
		{
			$( "#pedidos" ).load( "<?php echo base_url('admin/'.$dir.'/sub_delete/')?>", { sub_id: '<?php echo $sub_id?>', sub_ele: selectedItems  } );
			$(".close").click();
			return false;			
		}	
		else
		{
			$(".close").click();
		}
	});	
	
});
</script>

<div class="modal fade hide" id="myModal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3>Eliminar</h3>
  </div>
  <div class="modal-body">
    <p>¿Desea eliminar los registros?</p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancelar</a>
    <a href="#" class="btn btn-primary" id="sub_eliminar">Aceptar</a>
  </div>
</div>	
	
<div id="page-title" class="clearfix">
	<span style="font-size:24px;font-weight:bold;float:left;margin-top:5px;">Impresoras</span>
	<div class="item-content" style="float:right;">							
		<button class="btn btn-inverse" id="sub_nuevo">Nueva</button>	
		<a href="#myModal" data-toggle="modal" class="btn btn-inverse">Eliminar</a>
	</div>
</div> <!-- /.page-title -->

<div class="row">


	<div class="span12">
		<div class="widget widget-table">
			<div class="widget-content">					
				<div id="example_wrapper" class="dataTables_wrapper form-inline" role="grid">
					<?php // if(isset($message)) echo $message;?>
					
					<table id="list" class="table table-bordered table-striped table-highlight">
						<thead>
							<th style="width:30px;text-align:center;">
								<input type="checkbox" name="sub_all" id="sub_all" value="" />
							</th>
							<?php foreach($fields as $field_name => $field_display): ?>
								<th tabindex="0" rowspan="1" colspan="1">
									<?php echo 	$field_display; ?>
								</th>
							<?php endforeach; ?>
						</thead>
						<form action="<?php echo base_url('admin/'.$dir.'/delete/');?>" id="delete-form" method="POST" class="form-horizontal">
							<tbody>

								<?php

								 foreach($rows as $row): 
									
									?>

								<tr>
									<td style="text-align:center;"><input type="checkbox" name="sub_ele[]" id="sub_ele_<?php echo $row->id;?>" class="sub_ele" value="<?php echo $row->id;?>" /></td>
									<?php 
									foreach($fields as $field_name => $field_display): 
										if($field_display == 'Fecha')
										{	?>
											<td>
													<a class="sub_edit" href="<?php echo site_url('admin/'.$dir.'/sub_edit/'.$row->id);?>"><?php echo character_limiter(date("d/m/Y", strtotime($row->fecha)), 120); ?></a>
												</td>
											<?php 
										} 

										elseif($field_display == 'Total')
										{	?>
											<td>
													<a class="sub_edit" href="<?php echo site_url('admin/'.$dir.'/sub_edit/'.$row->id);?>"><?php echo '$'.character_limiter(number_format($row->total, 2, ",", "."), 120); ?></a>
												</td>
											<?php 
										}


										else
										{ ?>
											<td>
											<a class="sub_edit" href="<?php echo site_url('admin/'.$dir.'/sub_edit/'.$row->id);?>"><?php echo character_limiter($row->$field_name, 120); ?></a>
											</td>
										<?php 
										}
									endforeach; ?>
								</tr>
								<?php endforeach; ?>			
							</tbody>
						</form>
					</table>
					
				</div>
			</div>
		</div>
	</div>	
</div>	



<style>
	a
	{
		font-size: 12px !important;
	}
	.modal
	{
		top: 90% !important;
		left: 70%;
	}
</style>