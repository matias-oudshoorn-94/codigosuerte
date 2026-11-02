<?php
// Detalle de un cierre de caja. Datos de Cajasdiarias::ver().
$medios = array(
	array('Efectivo', $caja->total_efectivo),
	array('Tarjeta', $caja->total_tarjeta),
	array('Medio electrónico', $caja->total_medio_electronico),
	array('Transferencia/QR', $caja->total_transferencia),
);
$total_ventas_detalle = 0;
foreach ($ventas as $v) $total_ventas_detalle += $v->total;
?>

<p class="suave" style="margin:-8px 0 20px">
	<?php echo $sucursal ? '<b>'.p_e($sucursal->etiqueta).'</b> · ' : '' ?><?php echo ucfirst(p_fecha_larga($caja->fecha)) ?>
</p>

<div class="pn-kpis">
	<div class="pn-kpi"><div class="pn-kpi-etiqueta">Total de la caja</div><div class="pn-kpi-valor"><?php echo p_pesos($caja->total) ?></div></div>
	<div class="pn-kpi"><div class="pn-kpi-etiqueta">Ventas</div><div class="pn-kpi-valor"><?php echo p_numero($caja->cantidad_ventas) ?></div>
		<div class="pn-kpi-nota">Ticket promedio <?php echo p_pesos($caja->cantidad_ventas > 0 ? $caja->total / $caja->cantidad_ventas : 0) ?></div></div>
	<div class="pn-kpi"><div class="pn-kpi-etiqueta">Impresiones declaradas</div><div class="pn-kpi-valor"><?php echo p_numero($caja->impresiones_reales) ?></div>
		<div class="pn-kpi-nota"><?php echo p_numero($caja->impresiones_aprox) ?> vendidas</div></div>
	<div class="pn-kpi"><div class="pn-kpi-etiqueta">Desperdicio</div><div class="pn-kpi-valor"><?php echo $caja->impresiones_reales > 0 ? p_porcentaje($caja->desperdicio) : '—' ?></div></div>
</div>

<div class="pn-grilla">
	<section class="pn-tarjeta c-4">
		<div class="pn-tarjeta-cabecera"><h2>Medios de pago</h2></div>
		<table class="pn-tabla">
			<tbody>
				<?php foreach ($medios as $m) { ?>
					<tr><td><?php echo $m[0] ?></td><td class="num"><?php echo $m[1] > 0 ? '<b>'.p_pesos($m[1]).'</b>' : '<span class="tenue">—</span>' ?></td></tr>
				<?php } ?>
			</tbody>
			<tfoot><tr><td>Total</td><td class="num"><?php echo p_pesos($caja->total) ?></td></tr></tfoot>
		</table>
		<?php if (count($ventas) > 0 && round($total_ventas_detalle) != round($caja->total)) { ?>
			<div class="pn-tarjeta-pie suave" style="font-size:13px">
				<i class="ti-info-alt"></i> La suma de las ventas del día (<?php echo p_pesos($total_ventas_detalle) ?>) no coincide con el total del cierre.
			</div>
		<?php } ?>
	</section>

	<section class="pn-tarjeta c-8">
		<div class="pn-tarjeta-cabecera">
			<h2>Ventas del día</h2>
			<span class="pn-extra"><?php echo count($ventas) ?> venta<?php echo count($ventas) == 1 ? '' : 's' ?></span>
		</div>
		<?php if (empty($ventas)) { ?>
			<div class="pn-vacio"><i class="ti-receipt"></i>No hay ventas registradas ese día.</div>
		<?php } else { ?>
			<div class="pn-tabla-contenedor">
				<table class="pn-tabla">
					<thead><tr><th class="compacto">Hora</th><th>Productos</th><th>Pago</th><th class="num">Total</th></tr></thead>
					<tbody>
						<?php foreach ($ventas as $v) { ?>
							<tr>
								<td class="compacto"><b><?php echo p_hora($v->fecha_hora) ?></b><div class="tenue">N° <?php echo (int) $v->idpedido ?></div></td>
								<td>
									<?php foreach ($v->items as $item) { ?>
										<div><span class="suave"><?php echo (int) $item->cantidad ?>×</span> <?php echo p_e($item->nombre !== NULL ? $item->nombre : 'Producto eliminado') ?></div>
									<?php } ?>
									<?php if (empty($v->items)) echo '<span class="tenue">Sin detalle</span>' ?>
								</td>
								<td class="suave"><?php echo p_e($v->modo_de_pago !== '' ? $v->modo_de_pago : 'Sin especificar') ?></td>
								<td class="num"><b><?php echo p_pesos($v->total) ?></b></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		<?php } ?>
	</section>
</div>
