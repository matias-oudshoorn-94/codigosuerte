<?php
// Reporte: impresiones declaradas vs vendidas. Datos de Reportes::impresiones().
// $dias: por fecha, de los cierres de caja. $impresoras: declaraciones por impresora.
$this->load->view('admin/reportes/_filtros', array('con_csv' => TRUE));
$tot = array('reales' => 0, 'vendidas' => 0, 'cierres' => 0);
foreach ($dias as $d)
{
	$tot['reales'] += $d->reales;
	$tot['vendidas'] += $d->vendidas;
	$tot['cierres'] += $d->cierres;
}
$diferencia = $tot['reales'] - $tot['vendidas'];
$desperdicio = $tot['reales'] > 0 ? $diferencia / $tot['reales'] * 100 : NULL;
?>

<p class="suave" style="margin-bottom:16px"><?php echo p_rango($f['desde'], $f['hasta']) ?> · <?php echo $f['sucursal'] ? p_e($nombre_sucursal($f['sucursal'])) : 'Todas las sucursales' ?> · según los cierres de caja</p>

<div class="pn-kpis">
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Impresiones declaradas</div>
		<div class="pn-kpi-valor"><?php echo p_numero($tot['reales']) ?></div>
		<div class="pn-kpi-nota"><?php echo p_numero($tot['cierres']) ?> cierre<?php echo $tot['cierres'] == 1 ? '' : 's' ?> de caja</div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Impresiones vendidas</div>
		<div class="pn-kpi-valor"><?php echo p_numero($tot['vendidas']) ?></div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Diferencia</div>
		<div class="pn-kpi-valor"><?php echo p_numero($diferencia) ?></div>
		<div class="pn-kpi-nota">Declaradas menos vendidas</div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Desperdicio</div>
		<div class="pn-kpi-valor"><?php echo $desperdicio === NULL ? '—' : p_porcentaje($desperdicio) ?></div>
	</div>
</div>

<?php if (empty($dias)) { ?>
	<section class="pn-tarjeta"><div class="pn-vacio"><i class="ti-printer"></i>No hay cierres de caja en este período.</div></section>
<?php } else { ?>
<div class="pn-grilla">
	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera"><h2>Declaradas y vendidas por día</h2></div>
		<div class="pn-tarjeta-cuerpo"><div class="pn-grafico alto"><canvas id="g-impresiones" role="img" aria-label="Impresiones declaradas y vendidas por día"></canvas></div></div>
	</section>

	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera"><h2>Detalle por día</h2><span class="pn-extra"><?php echo count($dias) ?> días</span></div>
		<div class="pn-tabla-contenedor" style="max-height:560px;overflow-y:auto">
			<table class="pn-tabla">
				<thead><tr><th>Día</th><th class="num">Declaradas</th><th class="num">Vendidas</th><th class="num">Diferencia</th><th class="num">Desperdicio</th><th class="num">Cierres</th></tr></thead>
				<tbody>
					<?php foreach (array_reverse($dias) as $d) { ?>
						<tr>
							<td><?php echo p_fecha($d->fecha) ?></td>
							<td class="num"><?php echo p_numero($d->reales) ?></td>
							<td class="num"><?php echo p_numero($d->vendidas) ?></td>
							<td class="num"><b><?php echo p_numero($d->reales - $d->vendidas) ?></b></td>
							<td class="num"><?php echo $d->reales > 0 ? p_porcentaje(($d->reales - $d->vendidas) / $d->reales * 100) : '—' ?></td>
							<td class="num suave"><?php echo p_numero($d->cierres) ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</section>
</div>

<script>
PanelGraficos.barras('g-impresiones',
	<?php echo json_encode(array_map(function ($d) { return p_etiqueta_periodo($d->fecha, 'dia'); }, $dias)) ?>,
	[
		{ nombre: 'Declaradas', datos: <?php echo json_encode(array_map(function ($d) { return (int) $d->reales; }, $dias)) ?> },
		{ nombre: 'Vendidas', datos: <?php echo json_encode(array_map(function ($d) { return (int) $d->vendidas; }, $dias)) ?> }
	],
	{ formato: 'numero' });
</script>
<?php } ?>

<?php if ( ! empty($impresoras)) { ?>
<section class="pn-tarjeta" style="margin-top:20px">
	<div class="pn-tarjeta-cabecera"><h2>Por impresora</h2><span class="pn-extra">Declaraciones de contador en el período</span></div>
	<div class="pn-tabla-contenedor">
		<table class="pn-tabla">
			<thead><tr><th>Impresora</th><th>Sucursal</th><th class="num">Impresiones declaradas</th><th class="num">Declaraciones</th><th class="num">Contador actual</th><th>Última declaración</th></tr></thead>
			<tbody>
				<?php foreach ($impresoras as $i) { ?>
					<tr<?php echo $i->declaraciones ? '' : ' class="tenue"' ?>>
						<td><b><?php echo p_e($i->codigo) ?></b></td>
						<td><?php echo p_e($nombre_sucursal($i->sucursal)) ?></td>
						<td class="num"><?php echo p_numero($i->impresiones) ?></td>
						<td class="num"><?php echo p_numero($i->declaraciones) ?></td>
						<td class="num suave"><?php echo p_numero($i->contador) ?></td>
						<td class="suave"><?php echo $i->ultima ? p_fecha($i->ultima) : '—' ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</section>
<?php } ?>
