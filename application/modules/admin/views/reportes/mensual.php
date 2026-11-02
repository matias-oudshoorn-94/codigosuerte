<?php
// Reporte: mes a mes de un año contra el anterior. Datos de Reportes::mensual().
$this->load->view('admin/reportes/_filtros', array('con_csv' => TRUE, 'sin_fechas' => TRUE, 'anio' => $anio));
$meses = p_meses();
$cortos = array_map(function ($m) { return ucfirst(substr($m, 0, 3)); }, array_values($meses));
$mes_actual = $anio == date('Y') ? (int) date('n') : 12;
$t_actual = 0; $t_anterior = 0; $v_actual = 0; $v_anterior = 0;
foreach ($actual as $i => $m)
{
	if ($i + 1 > $mes_actual) continue;
	$t_actual += $m->total; $v_actual += $m->ventas;
	$t_anterior += $anterior[$i]->total; $v_anterior += $anterior[$i]->ventas;
}
?>

<p class="suave" style="margin-bottom:16px">
	<?php echo $f['sucursal'] ? p_e($nombre_sucursal($f['sucursal'])) : 'Todas las sucursales' ?> ·
	<?php echo $anio == date('Y') ? 'enero a '.$meses[$mes_actual].' de '.$anio.', comparado con el mismo tramo de '.($anio - 1) : 'año completo '.$anio.' contra '.($anio - 1) ?>
</p>

<div class="pn-kpis">
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Vendido en <?php echo $anio ?></div>
		<div class="pn-kpi-valor"><?php echo p_pesos($t_actual) ?></div>
		<div class="pn-kpi-nota"><?php echo p_delta($t_actual, $t_anterior, 'vs. '.($anio - 1)) ?></div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Ventas en <?php echo $anio ?></div>
		<div class="pn-kpi-valor"><?php echo p_numero($v_actual) ?></div>
		<div class="pn-kpi-nota"><?php echo p_delta($v_actual, $v_anterior, 'vs. '.($anio - 1)) ?></div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Ticket promedio</div>
		<div class="pn-kpi-valor"><?php echo p_pesos($v_actual ? $t_actual / $v_actual : 0) ?></div>
		<div class="pn-kpi-nota"><?php echo p_delta($v_actual ? $t_actual / $v_actual : 0, $v_anterior ? $t_anterior / $v_anterior : 0, 'vs. '.($anio - 1)) ?></div>
	</div>
</div>

<div class="pn-grilla">
	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera"><h2>Vendido por mes</h2><span class="pn-extra">Montos en pesos de cada momento (sin ajustar por inflación)</span></div>
		<div class="pn-tarjeta-cuerpo"><div class="pn-grafico"><canvas id="g-mensual-total" role="img" aria-label="Total vendido por mes, <?php echo $anio ?> contra <?php echo $anio - 1 ?>"></canvas></div></div>
	</section>
	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera"><h2>Cantidad de ventas por mes</h2><span class="pn-extra">No depende de la inflación: sirve para ver si se vende más o menos</span></div>
		<div class="pn-tarjeta-cuerpo"><div class="pn-grafico bajo"><canvas id="g-mensual-ventas" role="img" aria-label="Cantidad de ventas por mes, <?php echo $anio ?> contra <?php echo $anio - 1 ?>"></canvas></div></div>
	</section>

	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera"><h2>Detalle</h2></div>
		<div class="pn-tabla-contenedor">
			<table class="pn-tabla">
				<thead><tr><th>Mes</th><th class="num">Ventas <?php echo $anio ?></th><th class="num">Total <?php echo $anio ?></th><th class="num">Ventas <?php echo $anio - 1 ?></th><th class="num">Total <?php echo $anio - 1 ?></th><th class="num">Variación del total</th></tr></thead>
				<tbody>
					<?php foreach ($actual as $i => $m) { $futuro = $i + 1 > $mes_actual; ?>
						<tr<?php echo $futuro ? ' class="tenue"' : '' ?>>
							<td><?php echo ucfirst($meses[$i + 1]) ?></td>
							<td class="num"><?php echo $futuro ? '—' : p_numero($m->ventas) ?></td>
							<td class="num"><b><?php echo $futuro ? '—' : p_pesos($m->total) ?></b></td>
							<td class="num"><?php echo p_numero($anterior[$i]->ventas) ?></td>
							<td class="num"><?php echo p_pesos($anterior[$i]->total) ?></td>
							<td class="num"><?php echo $futuro ? '' : p_delta($m->total, $anterior[$i]->total) ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</section>
</div>

<script>
(function () {
	var meses = <?php echo json_encode($cortos) ?>;
	var hasta = <?php echo $mes_actual ?>;
	function recortar(datos) { return datos.map(function (v, i) { return i < hasta ? v : null; }); }
	PanelGraficos.barras('g-mensual-total', meses, [
		{ nombre: '<?php echo $anio ?>', datos: recortar(<?php echo json_encode(array_map(function ($m) { return round((float) $m->total); }, $actual)) ?>) },
		{ nombre: '<?php echo $anio - 1 ?>', datos: <?php echo json_encode(array_map(function ($m) { return round((float) $m->total); }, $anterior)) ?> }
	], { formato: 'pesos' });
	PanelGraficos.barras('g-mensual-ventas', meses, [
		{ nombre: '<?php echo $anio ?>', datos: recortar(<?php echo json_encode(array_map(function ($m) { return (int) $m->ventas; }, $actual)) ?>) },
		{ nombre: '<?php echo $anio - 1 ?>', datos: <?php echo json_encode(array_map(function ($m) { return (int) $m->ventas; }, $anterior)) ?> }
	], { formato: 'numero' });
})();
</script>
