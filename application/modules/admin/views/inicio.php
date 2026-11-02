<?php
// Inicio del panel. Datos de Admin::index().

$etiquetas_30 = array();
$valores_30 = array();
foreach ($ultimos_30 as $fila)
{
	$etiquetas_30[] = date('j/n', strtotime($fila->periodo));
	$valores_30[] = round((float) $fila->total);
}
$total_medios = 0;
foreach ($medios_mes as $m) $total_medios += $m->total;
$meses = p_meses();
?>

<?php if ($codigos_repetidos > 0) { ?>
	<div class="pn-aviso info">
		<i class="ti-info-alt"></i>
		<span>Hay <b><?php echo $codigos_repetidos ?></b> códigos de producto repetidos: al escanearlos se agrega siempre el mismo producto. <a href="<?php echo site_url('admin/productos?repetidos=1') ?>">Revisarlos</a></span>
	</div>
<?php } ?>

<div class="pn-kpis">
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Vendido hoy</div>
		<div class="pn-kpi-valor"><?php echo p_pesos($hoy->total) ?></div>
		<div class="pn-kpi-nota"><?php echo p_delta($hoy->total, $ayer->total, 'vs. ayer') ?></div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Ventas hoy</div>
		<div class="pn-kpi-valor"><?php echo p_numero($hoy->ventas) ?></div>
		<div class="pn-kpi-nota">Ticket promedio <?php echo p_pesos($hoy->ticket) ?></div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Vendido en <?php echo $meses[(int) date('n')] ?></div>
		<div class="pn-kpi-valor"><?php echo p_pesos($mes->total) ?></div>
		<div class="pn-kpi-nota"><?php echo p_delta($mes->total, $mes_anterior->total, 'vs. mismo tramo del mes pasado') ?></div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Ventas del mes</div>
		<div class="pn-kpi-valor"><?php echo p_numero($mes->ventas) ?></div>
		<div class="pn-kpi-nota">Promedio diario <?php echo p_pesos($mes->promedio_diario) ?></div>
	</div>
</div>

<div class="pn-grilla">
	<section class="pn-tarjeta c-7">
		<div class="pn-tarjeta-cabecera">
			<h2>Sucursales hoy</h2>
			<span class="pn-extra"><?php echo ucfirst(p_fecha_larga(date('Y-m-d'))) ?></span>
		</div>
		<?php if (empty($sucursales)) { ?>
			<div class="pn-vacio"><i class="ti-location-pin"></i>No hay sucursales activas.</div>
		<?php } else { ?>
			<div class="pn-tabla-contenedor">
				<table class="pn-tabla">
					<thead>
						<tr><th>Sucursal</th><th>Caja</th><th class="num">Ventas</th><th class="num">Total</th><th class="num">Última venta</th></tr>
					</thead>
					<tbody>
						<?php foreach ($sucursales as $s) { ?>
							<tr>
								<td><a class="principal-celda" href="<?php echo site_url('admin/cajasdiarias?sucursal='.(int) $s->idusuario) ?>"><?php echo p_e($s->etiqueta) ?></a><div class="tenue"><?php echo p_e($s->domicilio) ?></div></td>
								<td><?php echo $s->caja_cerrada == 1 ? '<span class="pn-etiqueta ambar">Cerrada</span>' : '<span class="pn-etiqueta verde">Abierta</span>' ?></td>
								<td class="num"><?php echo $s->hoy ? p_numero($s->hoy->ventas) : '0' ?></td>
								<td class="num"><b><?php echo $s->hoy ? p_pesos($s->hoy->total) : p_pesos(0) ?></b></td>
								<td class="num suave"><?php echo $s->hoy ? p_hora($s->hoy->ultima) : '—' ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		<?php } ?>
	</section>

	<section class="pn-tarjeta c-5">
		<div class="pn-tarjeta-cabecera">
			<h2>Medios de pago del mes</h2>
			<span class="pn-extra"><?php echo p_pesos($total_medios) ?></span>
		</div>
		<div class="pn-tarjeta-cuerpo">
			<?php if ($total_medios <= 0) { ?>
				<div class="pn-vacio"><i class="ti-wallet"></i>Todavía no hay ventas este mes.</div>
			<?php } else { ?>
				<div class="pn-barras">
					<?php foreach ($medios_mes as $m) { $parte = $m->total / $total_medios * 100; ?>
						<div class="pn-barra-fila" title="<?php echo p_e($m->medio).': '.p_numero($m->ventas).' ventas' ?>">
							<span class="pn-barra-nombre"><?php echo p_e($m->medio) ?></span>
							<span class="pn-barra-pista"><span class="pn-barra-relleno" style="display:block;width:<?php echo round($parte, 1) ?>%"></span></span>
							<span class="pn-barra-valor"><?php echo p_pesos($m->total) ?><small><?php echo p_porcentaje($parte, 0) ?></small></span>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</section>

	<section class="pn-tarjeta c-8">
		<div class="pn-tarjeta-cabecera">
			<h2>Vendido por día</h2>
			<span class="pn-extra">Últimos 30 días · todas las sucursales</span>
		</div>
		<div class="pn-tarjeta-cuerpo">
			<div class="pn-grafico"><canvas id="grafico-30" aria-label="Total vendido por día en los últimos 30 días" role="img"></canvas></div>
			<details class="pn-ver-tabla">
				<summary>Ver los datos en una tabla</summary>
				<div class="pn-tabla-contenedor">
					<table class="pn-tabla">
						<thead><tr><th>Día</th><th class="num">Ventas</th><th class="num">Total</th></tr></thead>
						<tbody>
							<?php foreach (array_reverse($ultimos_30) as $fila) { ?>
								<tr><td><?php echo p_fecha($fila->periodo) ?></td><td class="num"><?php echo p_numero($fila->ventas) ?></td><td class="num"><?php echo p_pesos($fila->total) ?></td></tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</details>
		</div>
	</section>

	<section class="pn-tarjeta c-4">
		<div class="pn-tarjeta-cabecera">
			<h2>Últimos cierres de caja</h2>
			<span class="pn-extra"><a href="<?php echo site_url('admin/cajasdiarias') ?>">Ver todos</a></span>
		</div>
		<?php if (empty($ultimos_cierres)) { ?>
			<div class="pn-vacio"><i class="ti-wallet"></i>Todavía no hay cierres.</div>
		<?php } else { ?>
			<table class="pn-tabla">
				<tbody>
					<?php foreach ($ultimos_cierres as $c) { ?>
						<tr>
							<td>
								<a class="principal-celda" href="<?php echo site_url('admin/cajasdiarias/ver/'.(int) $c->id) ?>"><?php echo p_e(isset($nombres[(int) $c->id_usuario]) ? $nombres[(int) $c->id_usuario] : 'Sucursal #'.$c->id_usuario) ?></a>
								<div class="tenue"><?php echo p_fecha($c->fecha) ?> · <?php echo p_numero($c->cantidad_ventas) ?> ventas</div>
							</td>
							<td class="num"><b><?php echo p_pesos($c->total) ?></b></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
	</section>
</div>

<script>
PanelGraficos.barras('grafico-30', <?php echo json_encode($etiquetas_30) ?>, [{ nombre: 'Total vendido', datos: <?php echo json_encode($valores_30) ?> }], { formato: 'pesos' });
</script>
