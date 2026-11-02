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
			cantidad: {
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
}); // end document.ready



function agregar_cantidad()
{
	var mensaje = '';
	var agregado = $('#agregar').val();
	if(agregado <= 0 || agregado == '')
	{
		mensaje = '<p style="padding: 10px;text-align: center;margin-bottom:0px;font-size: 14px;background-color: #ffdcdc;color: red;font-weight: 600;">Por favor ingrese una cantidad correcta</p>';
	}
	else
	{
		var stock = $('#cantidad').val();
		var total = parseInt(agregado) + parseInt(stock);
		$('#cantidad').val(parseInt(total));
		mensaje = '<p style="padding: 10px;text-align: center;margin-bottom:0px;font-size: 14px;background-color: #e2ffd0;color: #5a9656;font-weight: 600;">Se agregaron: ' +agregado+ ' al stock actual del producto.<br>Por favor presione Guardar para efectuar los cambios de stock.</p>';
	}

	document.getElementById("mensaje").innerHTML = mensaje;
}


function quitar_cantidad()
{
	var mensaje = '';
	var retirado = $('#retirar').val();
	var stock = $('#cantidad').val();
	if(retirado <= 0 || retirado == '' || retirado > stock)
	{
		mensaje = '<p style="padding: 10px;text-align: center;margin-bottom:0px;font-size: 14px;background-color: #ffdcdc;color: red;font-weight: 600;">Por favor ingrese una cantidad correcta</p>';
	}
	else
	{
		var total = parseInt(stock) - parseInt(retirado);
		$('#cantidad').val(parseInt(total));
		mensaje = '<p style="padding: 10px;text-align: center;margin-bottom:0px;font-size: 14px;background-color: #e2ffd0;color: #5a9656;font-weight: 600;">Se retiraron: ' +retirado+ ' al stock actual del producto.<br>Por favor presione Guardar para efectuar los cambios de stock.</p>';
	}

	document.getElementById("mensaje").innerHTML = mensaje;
}


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
								<div id="mensaje"></div>

								<div class="control-group">
									<label class="control-label" for="nombre">Nombre</label>
									<div class="controls">
										<?php $nombre = (isset($row->nombre))?$row->nombre:''; ?>
										<input type="text" class="input-xxlarge" name="nombre" id="nombre" value="<?php  echo set_value('nombre', $nombre); ?>">
									</div>
								</div>

								<div class="control-group" style="pointer-events: none;">
									<label class="control-label" for="cantidad" style="pointer-events: none;">Stock disponible</label>
									<div class="controls" style="pointer-events: none;">
										<?php $cantidad = (isset($row->cantidad))?$row->cantidad:''; ?>
										<input type="number" class="input-xxlarge" name="cantidad" id="cantidad" value="<?php  echo set_value('cantidad', $cantidad); ?>" style="pointer-events: none;">
									</div>
								</div>


								<div class="control-group">
									<div class="controls">
										<input type="number" class="input-small" name="agregar" id="agregar">
										<div class="btn btn-inverse" onclick="agregar_cantidad()">Agregar</div>
									</div>
								</div>

								<div class="control-group">
									<div class="controls">
										<input type="number" class="input-small" name="retirar" id="retirar">
										<div class="btn btn-inverse" onclick="quitar_cantidad()">Retirar</div>
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