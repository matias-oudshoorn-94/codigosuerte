<script type="text/javascript">
$(document).ready(function()
{
	$("#list .orden").click(function()
	{
       $(window.location).attr('href', '<?php echo base_url();?>'+$(this).attr('rel'));
	});   
	
	$("#per_page").change(function () {
		$("#per_page option:selected").each(function () {
			$(window.location).attr('href', '<?php echo site_url('admin/'.$dir.'/index/')."/".$this->session->userdata('sort_by')."/".$this->session->userdata('sort_order')."/1/";?>'+$(this).val());
		});	  
	});
	
	 
	// add multiple select / deselect functionality
	$("#all").click(function () {
		$('.ele').attr('checked', this.checked);
	});
	// if all checkbox are selected, check the selectall checkbox
	// and viceversa
	$(".ele").click(function(){
		if($(".ele").length == $(".ele:checked").length) {
			$("#all").attr("checked", "checked");
		} else {
			$("#all").removeAttr("checked");
		}
	});
	$('#nuevo').click(function()
	{
		$(window.location).attr('href', '<?php echo site_url('admin/'.$dir.'/add')?>');
	});	
	$('#eliminar').click(function()
	{
		var selectedItems = new Array();
		$("input[@name='ele[]']:checked").each(function(){
			selectedItems.push($(this).val());
		});
		if (selectedItems.length > 0)
		{
			 $('#delete-form').submit();
		}	
		else
		{
			$(".close").click();
		}
	});	
	$("#breset").click(function()
	{
		$(window.location).attr('href', '<?php echo site_url('admin/'.$dir)?>');		
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
    <a href="#" class="btn btn-primary" id="eliminar">Aceptar</a>
  </div>
</div>	
	
<div id="page-title" class="clearfix">
	<span style="font-size:24px;font-weight:bold;float:left;margin-top:5px;"><?php echo humanize($dir2);?></span>
	<div class="item-content" style="float:right;">							
		<button class="btn btn-inverse" id="nuevo">Nuevo</button>	
		<a href="#myModal" data-toggle="modal" class="btn btn-inverse">Eliminar</a>
	</div>
</div> <!-- /.page-title -->

<div class="row">
	<div class="span12">
		<div class="widget widget-table">
			<div class="widget-content">					
				<div id="example_wrapper" class="dataTables_wrapper form-inline" role="grid">
					<?php //if(isset($message)) echo $message;?>
					<div class="row">
						<div class="span6">
							<div id="r_pag" class="dataTables_length">
								<label>
									<select size="1" name="r_pag" id="per_page" >
										<option value="10" <?php if($this->session->userdata('per_page')==10) echo "selected";?>>10</option>
										<option value="25" <?php if($this->session->userdata('per_page')==25) echo "selected";?>>25</option>
										<option value="50" <?php if($this->session->userdata('per_page')==50) echo "selected";?>>50</option>
										<option value="100" <?php if($this->session->userdata('per_page')==100) echo "selected";?>>100</option>
									</select> 
									Resultado por página
								</label>
							</div>
						</div>
						<form action="<?php echo base_url('admin/'.$dir.'/index/');?>" id="search-form" class="form-horizontal">
							<div class="span6">
								<div class="pull-right">
									<div class="input-append">
										<input class="span2" id="bnombre" name="bnombre" value="<?php if(isset($bnombre)) echo $bnombre;?>" size="16" type="text"><button class="btn" id="bsearch" type="submit">Buscar</button>
									</div>
								</div>								
							</div>
						</form>	
					</div>
					<table id="list" class="table table-bordered table-striped table-highlight">
						<thead>
							<th style="width:30px;text-align:center;">
								<input type="checkbox" name="all" id="all" value="" />
							</th>
							<?php foreach($fields as $field_name => $field_display): ?>
								<th <?php echo ($this->session->userdata('sort_by') == $field_name)?"class='orden sorting_".$this->session->userdata('sort_order')."'":"class='orden sorting'";?> rel="admin/<?php echo $dir;?>/index/<?php echo $field_name."/";?><?php echo ($this->session->userdata('sort_order') == 'asc') ? 'desc' : 'asc'; ?>" tabindex="0" rowspan="1" colspan="1">
									<?php echo 	$field_display; ?>
								</th>
							<?php endforeach; ?>
						</thead>
						<form action="<?php echo base_url('admin/'.$dir.'/delete/');?>" id="delete-form" method="POST" class="form-horizontal">
							<tbody>
								<?php foreach($rows as $row): ?>
								<tr>
									<td style="text-align:center;">
										<?php 
										if($row->id!=1)
										{?>	
											<input type="checkbox" name="ele[]" id="ele_<?php echo $row->id;?>" class="ele" value="<?php echo $row->id;?>" />
										<?php
										} ?>	
										</td>
									<?php foreach($fields as $field_name => $field_display): ?>
									<td>
                                    	<?php if ($field_name == 'active') {echo '<img src="'.base_url('assets/admin/img')."/".$row->$field_name.".png".'" />';}
										 else {?>
                                         <a href="<?php echo site_url('admin/'.$dir.'/edit/')."/".$row->id;?>"><?php echo character_limiter($row->$field_name, 120); ?></a><?php } ?>
								  </td>
									<?php endforeach; ?>
								</tr>
								<?php endforeach; ?>			
							</tbody>
						</form>
					</table>
										
					<div class="row">
						<div class="span6">
							<div class="dataTables_info" id="example_info">
								<?php  echo ($cur_page>0)?"Página $cur_page de $pagination_tot":"Página $pagination_tot"; ?>									
							</div>
						</div>
						<div class="span6">
							<?php if (strlen($pagination)): 
								echo $pagination; 
							endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>	