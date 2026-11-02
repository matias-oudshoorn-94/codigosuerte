<?php
// Reporte: ventas por dia o mes con desglose por medio de pago. Datos de Reportes::ventas().
$this->load->view('admin/reportes/_filtros', array('con_csv' => TRUE));
$medios = array_values(Reportes_model::$medios);
$etiquetas = array_map(function ($s) use ($agrupar) { return p_etiqueta_periodo($s->periodo, $agrupar); }, $serie);
$series = array();
foreach ($medios as $i => $nombre_medio)
{
	$series[] = array('nombre' => $nombre_medio, 'datos' => array_map(function ($s) use ($i) { return round((float) $s->{'medio_'.$i}); }, $serie));
}
$tot = array('ventas' => 0, 'total' => 0, 0, 0, 0, 0);
foreach ($serie as $s)
{
	$tot['ventas'] += $s->ventas; $tot['total'] += $s->total;
	for ($i = 0; $i < 4; $i++) $tot[$i] += $s->{'medio_'.$i};
}
$sin_medio = $tot['total'] - ($tot[0] + $tot[1] + $tot[2] + $tot[3]);
$q_agrupar = function ($valor) { $q = $_GET; $q['agrupar'] = $valor; unset($q['csv']); return '?'.http_build_query($q); };
?>

<div class="pn-grilla">
	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera">
			<h2>Vendido por <?php echo $agrupar === 'mes' ? 'mes' : 'día' ?>, por medio de pago</h2>
			<span class="pn-extra">
				<span class="pn-atajos">
					<a href="<?php echo p_e($q_agrupar('dia')) ?>" class="<?php echo $agrupar === 'dia' ? 'activo' : '' ?>">Por día</a>
					<a href="<?php echo p_e($q_agrupar('mes')) ?>" class="<?php echo $agrupar === 'mes' ? 'activo' : '' ?>">Por mes</a>
				</span>
			</span>
		</div>
		<div class="pn-tarjeta-cuerpo">
			<?php if ($tot['ventas'] == 0) { ?>
				<div class="pn-vacio"><i class="ti-stats-up"></i>No hay ventas en este período.</div>
			<?php } else { ?>
				<div class="pn-grafico alto"><canvas id="g-ventas" role="img" aria-label="Total vendido por período, apilado por medio de pago"></canvas></div>
				<?php if ($sin_medio > 0.5) { ?>
					<p class="suave" style="font-size:12px;margin-top:8px"><?php echo p_pesos($sin_medio) ?> vendidos sin medio de pago registrado no aparecen en el gráfico (sí en el total de la tabla).</p>
				<?php } ?>
			<?php } ?>
		</div>
	</section>

	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera"><h2>Detalle</h2><span class="pn-extra"><?php echo count($serie) ?> <?php echo $agrupar === 'mes' ? 'meses' : 'días' ?></span></div>
		<div class="pn-tabla-contenedor" style="max-height:560px;overflow-y:auto">
			<table class="pn-tabla">
				<thead>
					<tr>
						<th><?php echo $agrupar === 'mes' ? 'Mes' : 'Día' ?></th><th class="num">Ventas</th><th class="num">Total</th>
						<?php foreach ($medios as $m) { ?><th class="num"><?php echo p_e($m) ?></th><?php } ?>
						<th class="num">Ticket promedio</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach (array_reverse($serie) as $s) { ?>
						<tr<?php echo $s->ventas ? '' : ' class="tenue"' ?>>
							<td><?php echo $agrupar === 'mes' ? ucfirst(p_meses()[(int) substr($s->periodo, 5, 2)]).' '.substr($s->periodo, 0, 4) : p_fecha($s->periodo) ?></td>
							<td class="num"><?php echo p_numero($s->ventas) ?></td>
							<td class="num"><b><?php echo p_pesos($s->total) ?></b></td>
							<?php for ($i = 0; $i < 4; $i++) { ?><td class="num"><?php echo $s->{'medio_'.$i} > 0 ? p_pesos($s->{'medio_'.$i}) : '<span class="tenue">—</span>' ?></td><?php } ?>
							<td class="num"><?php echo $s->ventas ? p_pesos($s->total / $s->ventas) : '—' ?></td>
						</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td>Total</td><td class="num"><?php echo p_numero($tot['ventas']) ?></td><td class="num"><?php echo p_pesos($tot['total']) ?></td>
						<?php for ($i = 0; $i < 4; $i++) { ?><td class="num"><?php echo p_pesos($tot[$i]) ?></td><?php } ?>
						<td class="num"><?php echo $tot['ventas'] ? p_pesos($tot['total'] / $tot['ventas']) : '—' ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</section>
</div>

<?php if ($tot['ventas'] > 0) { ?>
<script>
PanelGraficos.barras('g-ventas', <?php echo json_encode($etiquetas) ?>, <?php echo json_encode($series) ?>, { formato: 'pesos', apilado: true });
</script>
<?php } ?>
