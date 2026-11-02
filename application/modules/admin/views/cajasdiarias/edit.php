<?php


// Ventas del dia de esta caja
$ventas = isset($row) ? $this->db->where('fecha', $row->fecha)
	->where('cod_clie', $row->id_usuario)
	->order_by('idpedido', 'ASC')
	->get('pedidos')
	->result_array() : array();

?>


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

								<div class="contenedor_detalles" style="padding:20px">
									<div class="encabezado">
										<span style="font-size:24px;font-weight:bold;margin-top:5px;">Resumen de la caja</span>
									</div>
									<div class="contenido">
										<div><font style="font-size: 13px;font-weight: 600">Fecha:</font> <?php echo $row->fecha ?></div>
										<div><font style="font-size: 13px;font-weight: 600">Cantidad de ventas: </font><?php echo $row->cantidad_ventas ?></div>
										<div><font style="font-size: 13px;font-weight: 600">Total efectivo:</font> $<?php echo number_format($row->total_efectivo, 2, ',', '.') ?></div>
										<div><font style="font-size: 13px;font-weight: 600">Total tarjeta:</font> $<?php echo number_format($row->total_tarjeta, 2, ',', '.') ?></div>
										<div><font style="font-size: 13px;font-weight: 600">Total medio electrónico:</font> $<?php echo number_format($row->total_medio_electronico, 2, ',', '.') ?></div>
										<div><font style="font-size: 13px;font-weight: 600">Total transferencia/QR:</font> $<?php echo number_format($row->total_transferencia, 2, ',', '.') ?></div>
										<div><font style="font-size: 13px;font-weight: 600">Total de caja:</font> $<?php echo number_format($row->total, 2, ',', '.') ?></div>
										<div><font style="font-size: 13px;font-weight: 600">Impresiones reales (declaradas):</font> <?php echo $row->impresiones_reales ?></div>
										<div><font style="font-size: 13px;font-weight: 600">Impresiones aproximadas (de ventas reales):</font> <?php echo $row->impresiones_aprox ?></div>
										<div><font style="font-size: 13px;font-weight: 600">Desperdicio:</font> <?php echo number_format($row->desperdicio, 2)?>%</div>
									</div>
								</div>

								<div class="contenedor_detalles" style="padding:20px">
									<div class="encabezado">
										<span style="font-size:24px;font-weight:bold;margin-top:5px;">Detalles de las ventas</span>
									</div>
									<div class="contenido">
										<?php

										$contador_ventas = 0;

										foreach ($ventas as $fila)
									    {
									      $fecha_hora = $fila['fecha_hora'];
									      $modo_pago = $fila['modo_de_pago'];
									      $total = $fila['total'];
									      $contador_ventas++;
									      ?>

									      <div class="contenedor_venta" style="padding:10px;border: 1px solid #d8d8d8; margin-bottom: 10px">

									      	<div class="contador" style="float: left;height: 100%;width: 15px;background-color: #263849;color: #fff;padding: 10px;margin-right: 16px;"><?php echo $contador_ventas?></div>

									      	<div class="encabezado_ventas">
									      		<?php echo $fecha_hora ?> , <?php echo $modo_pago ?>, Total: $<?php echo number_format($total, 2, ',', '.') ?>
									      	</div>
									      	<div class="contenido_ventas">

									      		<?php

									      		$detalles_ventas = $this->db->select('pedidosdetalles.id_articu, pedidosdetalles.cantidad, productos.nombre AS nombre')
													->from('pedidosdetalles')
													->join('productos', 'pedidosdetalles.id_articu = productos.id', 'left')
													->where('idpedido', $fila['idpedido'])
													->get()
													->result_array();

												foreach ($detalles_ventas as $fila2)
											    {
											      $articulo = $fila2['id_articu'];
											      $cantidad = $fila2['cantidad'];
											      $nombre = $fila2['nombre'];
											      ?>

											      <div><font style="font-size: 12px;font-weight: 600">Articulo: <?php echo html_escape($nombre) ?>, Cantidad: <?php echo $cantidad?> </font></div>

											      <?php

											    }

									      		?>

									      	</div>


									      </div>

									      <?php
									  	}

										?>
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