<script type="text/javascript">
$(document).ready(function()
{
	$('#form').validate(
	{
		rules: {
			producto_id: {
				required: true
			},
			idusuario: {
				required: true
			},
			stock: {
				required: true
			},
	    },

	    focusCleanup: false,

	    highlight: function(label) {
	    	$(label).closest('.control-group').removeClass ('success').addClass('error');
	    },
	    success: function(label) {
	    	label
	    		.text('OK!').addClass('valid')
	    		.closest('.control-group').addClass('success');
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parents ('.controls') );
		}
	});
	<?php $this->load->view('partials/script_edit.php'); ?>

$('.form').eq(0).find('input').eq(0).focus();
        $('#fecha_vencimiento').datepicker();



}); // end document.ready

</script>


<!-- CKEDITOR-->

<script src="<?php echo site_url('assets');?>/ckeditor/ckeditor.js"></script>

<script src="<?php echo site_url('assets');?>/ckeditor/samples/js/sample.js"></script>

<link rel="stylesheet" href="<?php echo site_url('assets');?>/ckeditor/samples/css/samples.css">

<link rel="stylesheet" href="<?php echo site_url('assets');?>/ckeditor/samples/toolbarconfigurator/lib/codemirror/neo.css">


	<form action="<?php echo $action?>" id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" novalidate="novalidate">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($row->id))?$row->id:"0"; ?>"/>
		<div id="page-title" class="clearfix">
			<span style="font-size:24px;font-weight:bold;float:left;margin-top:5px;"><?php echo humanize($dir2);?></span>
			<div class="item-content" style="float:right;">
				<button type="submit" class="btn btn-inverse">Guardar</button>
				<button class="btn cancel">Cancelar</button>
			</div>
		</div> <!-- /.page-title -->
		<div class="row">
			<div class="span12">
				<div id="validation" class="widget highlight widget-form">

					<div class="widget-content">

							<fieldset>
								<?php

								//echo (isset($message))?$message:'';?>

		                        <div class="control-group">
									<label class="control-label" for="producto_id">Producto</label>
									<div class="controls">
										<?php $producto_id = (isset($row->producto_id))?$row->producto_id:''; ?>
										<input type="text" class="input-xxlarge" name="producto_id" id="producto_id" value="<?php  echo set_value('producto_id', $producto_id); ?>">
									</div>
								</div>

                                <div class="control-group">
									<label class="control-label" for="idusuario">Sucursal</label>
									<div class="controls">
										<?php $idusuario = (isset($row->idusuario))?$row->idusuario:''; ?>
										<input type="text" class="input-xxlarge" name="idusuario" id="idusuario" value="<?php  echo set_value('idusuario', $idusuario); ?>">
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="stock">Stock</label>
									<div class="controls">
										<?php $stock = (isset($row->stock))?$row->stock:''; ?>
										<input type="number" class="input-xxlarge" name="stock" id="stock" value="<?php  echo set_value('stock', $stock); ?>">
									</div>
								</div>

								<div class="form-actions">
									<button type="submit" class="btn btn-inverse">Guardar</button>
									<button class="btn cancel" >Cancelar</button>
								</div>
							</fieldset>

					</div> <!-- /widget-content -->
				</div>
			</div>
		</div>
	</form>