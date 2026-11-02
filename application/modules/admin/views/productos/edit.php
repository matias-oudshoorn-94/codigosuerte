<script type="text/javascript">
$(document).ready(function()
{
	$('#form').validate(
	{
		rules: {
			nombre: {
				minlength: 2,
				required: true
			},
			codigo: {
				required: true
			},
			precio: {
				required: true
			},
			porcentaje_marcado: {
				required: true
			},
			fecha_vencimiento: {
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
									<label class="control-label" for="nombre">Nombre</label>
									<div class="controls">
										<?php $nombre = (isset($row->nombre))?$row->nombre:''; ?>
										<input type="text" class="input-xxlarge" name="nombre" id="nombre" value="<?php  echo set_value('nombre', $nombre); ?>">
									</div>
								</div>

		                        <div class="control-group">
									<label class="control-label" for="codigo">Código</label>
									<div class="controls">
										<?php $codigo = (isset($row->codigo))?$row->codigo:''; ?>
										<input type="text" class="input-xxlarge" name="codigo" id="codigo" value="<?php  echo set_value('codigo', $codigo); ?>">
									</div>
								</div>



								<div class="control-group">
									<label class="control-label" for="precio">Precio de costo</label>
									<div class="controls">
										<?php $precio = (isset($row->precio))?$row->precio:''; ?>
										<input type="text" class="input-xxlarge" name="precio" id="precio" value="<?php  echo set_value('precio', $precio); ?>">
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="porcentaje_marcado">Porcentaje de marcado</label>
									<div class="controls">
										<?php $porcentaje_marcado = (isset($row->porcentaje_marcado))?$row->porcentaje_marcado:''; ?>
										<input type="number" class="input-xxlarge" name="porcentaje_marcado" id="porcentaje_marcado" value="<?php  echo set_value('porcentaje_marcado', $porcentaje_marcado); ?>">
									</div>
									<p style="margin-left: 160px;margin-bottom: 0px;">Ejemplo: para marcar un 30%, solo ingresar = 30</p>
								</div>

								<!--
								<div class="control-group">
									<label class="control-label" for="stock">Stock</label>
									<div class="controls">
										<?php $stock = (isset($row->stock))?$row->stock:''; ?>
										<input type="number" class="input-xxlarge" name="stock" id="stock" value="<?php  echo set_value('stock', $stock); ?>">
									</div>
								</div>-->

								<div class="control-group">
									<label class="control-label" for="precio_final">Precio de venta</label>
									<div class="controls">
										<?php $precio_final = (isset($row->precio_final))?$row->precio_final:''; ?>
										<input type="text" class="input-xxlarge" name="precio_final" id="precio_final" value="<?php  echo set_value('precio', $precio_final); ?>">
									</div>
									<p style="margin-left: 160px;margin-bottom: 0px;">Si se deja este campo vacío, el precio final se va a generar con el precio de costo + porcentaje de marcado</p>
								</div>

								<?php
                                $fecha_vacia = date("01/01/2021");
                                ?>

		                        <div class="control-group">
		                            <label class="control-label" for="fecha_vencimiento">Fecha de vencimiento<font style="color: red;">*</font></label>
		                            <div class="controls">
		                                <?php $fecha_vencimiento = (isset($row->fecha_vencimiento)) ? $row->fecha_vencimiento : $fecha_vacia; ?>
		                                <input type="text" class="input-xxlarge" name="fecha_vencimiento" id="fecha_vencimiento"
		                                       value="<?php echo set_value('fecha_vencimiento', date("d/m/Y", strtotime($fecha_vencimiento))); ?>">
		                            </div>
		                        </div>

		                        <div class="control-group">
									<label class="control-label" for="orden">Orden</label>
									<div class="controls">
										<?php $orden = (isset($row->orden))?$row->orden:''; ?>
										<input type="number" class="input-xxlarge" name="orden" id="orden" value="<?php  echo set_value('orden', $orden); ?>">
									</div>
									<p style="margin-left: 160px;margin-bottom: 0px;">Este campo le dará el orden a la lista de producto en el momento de la venta</p>
								</div>

								<div class="control-group">
									<label class="control-label" for="stock_sucursal">Stock por sucursal</label>
									<div class="controls">
										<table class="table table-bordered table-striped">
											<thead>
												<tr>
													<th>Sucursal</th>
													<th>Stock</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$stocks_existentes = [];
												if(isset($stocks)) {
													foreach ($stocks as $s) {
														$stocks_existentes[$s->idusuario] = $s->stock;
													}
												}
												foreach ($sucursales['rows'] as $sucursal):
													$valor_stock = isset($stocks_existentes[$sucursal->idusuario]) ? $stocks_existentes[$sucursal->idusuario] : 0;
												?>
												<tr>
													<td><?php echo $sucursal->domicilio; ?></td> <!-- Esto sí existe en $sucursales -->
													<td>
														<input type="number" name="stock_sucursal[<?php echo $sucursal->idusuario; ?>]" value="<?php echo $valor_stock; ?>" class="input-small">
													</td>
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
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