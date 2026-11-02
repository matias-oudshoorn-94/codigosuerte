<?php
// Reporte: comparacion entre sucursales. Datos de Reportes::sucursales().
$this->load->view('admin/reportes/_filtros', array('con_csv' => TRUE));
$nombre = $nombre_sucursal;
$total = 0;
foreach ($totales as $t) $total += $t->total;

$etiquetas = array();
$series = array();
foreach ($principales as $id) $series[$id] = array('nombre' => $nombre($id), 'datos' => array());
if ($hay_otras) $series['otras'] = array('nombre' => 'Otras', 'datos' => array());
foreach ($periodos as $periodo => $valores)
{
	$etiquetas[] = p_etiqueta_periodo($periodo, $f['agrupar']);
	foreach ($series as $clave => $s) $series[$clave]['datos'][] = isset($valores[$clave]) ? round($valores[$clave]) : 0;
}
?>

<p class="suave" style="margin-bottom:16px"><?php echo p_rango($f['desde'], $f['hasta']) ?> · la variación compara con <?php echo p_rango($f['desde_anterior'], $f['hasta_anterior']) ?></p>

<?php if (empty($totales)) { ?>
	<section class="pn-tarjeta"><div class="pn-vacio"><i class="ti-location-pin"></i>No hay ventas en este período.</div></section>
<?php } else { ?>
<div class="pn-grilla">
	<section class="pn-tarjeta c-12">
		<div class="pn-tabla-contenedor">
			<table class="pn-tabla">
				<thead><tr><th>Sucursal</th><th class="num">Total</th><th>Participación</th><th class="num">Ventas</th><th class="num">Ticket promedio</th><th class="num">Promedio por día</th><th class="num">Variación del total</th></tr></thead>
				<tbody>
					<?php foreach ($totales as $t) {
						$parte = $total > 0 ? $t->total / $total * 100 : 0;
						$antes = isset($anteriores[(int) $t->sucursal]) ? $anteriores[(int) $t->sucursal]->total : 0; ?>
						<tr>
							<td><a class="principal-celda" href="<?php echo p_e(site_url('admin/reportes').'?'.http_build_query(array_merge($_GET, array('sucursal' => (int) $t->sucursal)))) ?>"><?php echo p_e($nombre($t->sucursal)) ?></a></td>
							<td class="num"><b><?php echo p_pesos($t->total) ?></b></td>
							<td style="min-width:160px">
								<div style="display:flex;align-items:center;gap:8px">
									<span class="pn-barra-pista" style="flex:1"><span class="pn-barra-relleno" style="display:block;width:<?php echo round($parte, 1) ?>%"></span></span>
									<span class="suave" style="width:40px;text-align:right"><?php echo p_porcentaje($parte, 0) ?></span>
								</div>
							</td>
							<td class="num"><?php echo p_numero($t->ventas) ?></td>
							<td class="num"><?php echo p_pesos($t->ventas ? $t->total / $t->ventas : 0) ?></td>
							<td class="num"><?php echo p_pesos($t->dias ? $t->total / $t->dias : 0) ?></td>
							<td class="num"><?php echo p_delta($t->total, $antes) ?></td>
						</tr>
					<?php } ?>
				</tbody>
				<tfoot><tr><td>Total</td><td class="num"><?php echo p_pesos($total) ?></td><td></td><td class="num"><?php echo p_numero(array_sum(array_map(function ($t) { return $t->ventas; }, $totales))) ?></td><td colspan="3"></td></tr></tfoot>
			</table>
		</div>
	</section>

	<?php if (count($periodos) > 1) { ?>
		<section class="pn-tarjeta c-12">
			<div class="pn-tarjeta-cabecera">
				<h2>Vendido por <?php echo $f['agrupar'] === 'mes' ? 'mes' : 'día' ?> en cada sucursal</h2>
				<?php if ($hay_otras) { ?><span class="pn-extra">Se muestran las 3 que más vendieron; el resto se suma en «Otras»</span><?php } ?>
			</div>
			<div class="pn-tarjeta-cuerpo"><div class="pn-grafico alto"><canvas id="g-sucursales" role="img" aria-label="Total vendido por período en cada sucursal"></canvas></div></div>
		</section>
		<script>
		PanelGraficos.lineas('g-sucursales', <?php echo json_encode($etiquetas) ?>, <?php echo json_encode(array_values($series)) ?>, { formato: 'pesos' });
		</script>
	<?php } ?>
</div>
<?php } ?>
