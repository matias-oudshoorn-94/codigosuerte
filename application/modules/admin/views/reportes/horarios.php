<?php
// Reporte: cuándo se vende (día de la semana x hora). Datos de Reportes::horarios().
// $matriz[dia][hora] con dia de MySQL DAYOFWEEK (1 = domingo).
$this->load->view('admin/reportes/_filtros');
$dias = array(2 => 'Lun', 3 => 'Mar', 4 => 'Mié', 5 => 'Jue', 6 => 'Vie', 7 => 'Sáb', 1 => 'Dom');

// Solo las horas en que hubo ventas (de la primera a la última)
$max = 0; $h_min = 23; $h_max = 0; $ventas_total = 0;
$por_hora = array_fill(0, 24, 0);
$por_dia = array_fill_keys(array_keys($dias), 0);
$total_por_dia = array_fill_keys(array_keys($dias), 0);
foreach ($matriz as $dia => $horas)
{
	foreach ($horas as $hora => $c)
	{
		$max = max($max, (int) $c->ventas);
		$h_min = min($h_min, $hora);
		$h_max = max($h_max, $hora);
		$ventas_total += $c->ventas;
		$por_hora[$hora] += $c->ventas;
		$por_dia[$dia] += $c->ventas;
		$total_por_dia[$dia] += $c->total;
	}
}
$horas = $ventas_total ? range($h_min, $h_max) : array();

// Color de la celda: de celeste claro a azul oscuro segun la cantidad de ventas
$color = function ($n) use ($max) {
	if ( ! $n || ! $max) return 'background:#f3f5f7';
	$t = $n / $max;
	$de = array(0xcd, 0xe2, 0xfb); $a = array(0x18, 0x4f, 0x95);
	$rgb = array();
	for ($i = 0; $i < 3; $i++) $rgb[] = round($de[$i] + ($a[$i] - $de[$i]) * $t);
	return 'background:rgb('.implode(',', $rgb).');color:'.($t > 0.5 ? '#fff' : 'inherit');
};
?>

<p class="suave" style="margin-bottom:16px"><?php echo p_rango($f['desde'], $f['hasta']) ?> · <?php echo $f['sucursal'] ? p_e($nombre_sucursal($f['sucursal'])) : 'Todas las sucursales' ?></p>

<?php if ( ! $ventas_total) { ?>
	<section class="pn-tarjeta"><div class="pn-vacio"><i class="ti-time"></i>No hay ventas en este período.</div></section>
<?php } else { ?>
<div class="pn-grilla">
	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera"><h2>Ventas por día y hora</h2><span class="pn-extra"><?php echo p_numero($ventas_total) ?> ventas</span></div>
		<div class="pn-tarjeta-cuerpo" style="overflow-x:auto">
			<div class="pn-calor" style="--columnas:<?php echo count($horas) ?>;min-width:<?php echo 44 + count($horas) * 30 ?>px">
				<div class="eje"></div>
				<?php foreach ($horas as $h) { ?><div class="eje"><?php echo $h ?>h</div><?php } ?>
				<?php foreach ($dias as $d => $nombre_dia) { ?>
					<div class="eje"><?php echo $nombre_dia ?></div>
					<?php foreach ($horas as $h) {
						$c = isset($matriz[$d][$h]) ? $matriz[$d][$h] : NULL;
						$n = $c ? (int) $c->ventas : 0; ?>
						<div class="celda" style="<?php echo $color($n) ?>" title="<?php echo p_e($nombre_dia.' '.$h.' a '.($h + 1).' h: '.p_numero($n).' ventas'.($c ? ' · '.p_pesos($c->total) : '')) ?>"><?php echo $n ?: '' ?></div>
					<?php } ?>
				<?php } ?>
			</div>
			<div class="pn-calor-escala">Menos <span></span> Más</div>
		</div>
	</section>

	<section class="pn-tarjeta c-6">
		<div class="pn-tarjeta-cabecera"><h2>Por hora</h2><span class="pn-extra">Cantidad de ventas</span></div>
		<div class="pn-tarjeta-cuerpo"><div class="pn-grafico"><canvas id="g-horas" role="img" aria-label="Cantidad de ventas por hora del día"></canvas></div></div>
	</section>

	<section class="pn-tarjeta c-6">
		<div class="pn-tarjeta-cabecera"><h2>Por día de la semana</h2><span class="pn-extra">Total vendido</span></div>
		<div class="pn-tarjeta-cuerpo"><div class="pn-grafico"><canvas id="g-dias" role="img" aria-label="Total vendido por día de la semana"></canvas></div></div>
	</section>
</div>

<script>
PanelGraficos.barras('g-horas',
	<?php echo json_encode(array_map(function ($h) { return $h.'h'; }, $horas)) ?>,
	[{ nombre: 'Ventas', datos: <?php echo json_encode(array_map(function ($h) use ($por_hora) { return $por_hora[$h]; }, $horas)) ?> }],
	{ formato: 'numero' });
PanelGraficos.barras('g-dias',
	<?php echo json_encode(array_values($dias)) ?>,
	[{ nombre: 'Total vendido', datos: <?php echo json_encode(array_map('round', array_values($total_por_dia))) ?> }],
	{ formato: 'pesos' });
</script>
<?php } ?>
