<?php
// Reporte: resumen del periodo. Datos de Reportes::index().
$this->load->view('admin/reportes/_filtros');
$nombre = $nombre_sucursal;
$total_medios = 0;
foreach ($medios as $m) $total_medios += $m->total;
$total_sucursales = 0;
foreach ($por_sucursal as $s) $total_sucursales += $s->total;
$etiquetas = array_map(function ($s) use ($f) { return p_etiqueta_periodo($s->periodo, $f['agrupar']); }, $serie);
$valores = array_map(function ($s) { return round((float) $s->total); }, $serie);
?>

<p class="suave" style="margin-bottom:16px">
	<?php echo p_rango($f['desde'], $f['hasta']) ?> · <?php echo $f['sucursal'] ? p_e($nombre($f['sucursal'])) : 'Todas las sucursales' ?>
	· comparado con <?php echo p_rango($f['desde_anterior'], $f['hasta_anterior']) ?>
</p>

<div class="pn-kpis">
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Total vendido</div>
		<div class="pn-kpi-valor"><?php echo p_pesos($actual->total) ?></div>
		<div class="pn-kpi-nota"><?php echo p_delta($actual->total, $anterior->total) ?> <span class="tenue">antes <?php echo p_pesos($anterior->total) ?></span></div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Ventas</div>
		<div class="pn-kpi-valor"><?php echo p_numero($actual->ventas) ?></div>
		<div class="pn-kpi-nota"><?php echo p_delta($actual->ventas, $anterior->ventas) ?> <span class="tenue">antes <?php echo p_numero($anterior->ventas) ?></span></div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Ticket promedio</div>
		<div class="pn-kpi-valor"><?php echo p_pesos($actual->ticket) ?></div>
		<div class="pn-kpi-nota"><?php echo p_delta($actual->ticket, $anterior->ticket) ?> <span class="tenue">antes <?php echo p_pesos($anterior->ticket) ?></span></div>
	</div>
	<div class="pn-kpi">
		<div class="pn-kpi-etiqueta">Promedio por día</div>
		<div class="pn-kpi-valor"><?php echo p_pesos($actual->promedio_diario) ?></div>
		<div class="pn-kpi-nota"><?php echo p_numero($actual->dias) ?> día<?php echo $actual->dias == 1 ? '' : 's' ?> con ventas</div>
	</div>
</div>

<?php if ($actual->ventas == 0) { ?>
	<section class="pn-tarjeta"><div class="pn-vacio"><i class="ti-bar-chart"></i>No hay ventas en este período.</div></section>
<?php } else { ?>
<div class="pn-grilla">
	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera">
			<h2>Vendido por <?php echo $f['agrupar'] === 'mes' ? 'mes' : 'día' ?></h2>
			<span class="pn-extra"><a href="<?php echo p_e(site_url('admin/reportes/ventas').'?'.http_build_query($_GET)) ?>">Ver detalle por medio de pago</a></span>
		</div>
		<div class="pn-tarjeta-cuerpo">
			<div class="pn-grafico"><canvas id="g-serie" role="img" aria-label="Total vendido por período"></canvas></div>
		</div>
	</section>

	<section class="pn-tarjeta <?php echo $por_sucursal ? 'c-6' : 'c-12' ?>">
		<div class="pn-tarjeta-cabecera"><h2>Medios de pago</h2></div>
		<div class="pn-tarjeta-cuerpo">
			<div class="pn-barras">
				<?php foreach ($medios as $m) { $parte = $total_medios > 0 ? $m->total / $total_medios * 100 : 0; ?>
					<div class="pn-barra-fila">
						<span class="pn-barra-nombre"><?php echo p_e($m->medio) ?></span>
						<span class="pn-barra-pista"><span class="pn-barra-relleno" style="display:block;width:<?php echo round($parte, 1) ?>%"></span></span>
						<span class="pn-barra-valor"><?php echo p_pesos($m->total) ?><small><?php echo p_porcentaje($parte, 0) ?> · <?php echo p_numero($m->ventas) ?> ventas</small></span>
					</div>
				<?php } ?>
			</div>
		</div>
	</section>

	<?php if ($por_sucursal) { ?>
		<section class="pn-tarjeta c-6">
			<div class="pn-tarjeta-cabecera">
				<h2>Por sucursal</h2>
				<span class="pn-extra"><a href="<?php echo p_e(site_url('admin/reportes/sucursales').'?'.http_build_query($_GET)) ?>">Comparar sucursales</a></span>
			</div>
			<div class="pn-tarjeta-cuerpo">
				<div class="pn-barras">
					<?php foreach ($por_sucursal as $s) { $parte = $total_sucursales > 0 ? $s->total / $total_sucursales * 100 : 0; ?>
						<div class="pn-barra-fila">
							<span class="pn-barra-nombre" title="<?php echo p_e($nombre($s->sucursal)) ?>"><?php echo p_e($nombre($s->sucursal)) ?></span>
							<span class="pn-barra-pista"><span class="pn-barra-relleno" style="display:block;width:<?php echo round($parte, 1) ?>%"></span></span>
							<span class="pn-barra-valor"><?php echo p_pesos($s->total) ?><small><?php echo p_porcentaje($parte, 0) ?></small></span>
						</div>
					<?php } ?>
				</div>
			</div>
		</section>
	<?php } ?>

	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera">
			<h2>Productos más vendidos</h2>
			<span class="pn-extra"><a href="<?php echo p_e(site_url('admin/reportes/productos').'?'.http_build_query($_GET)) ?>">Ver ranking completo</a></span>
		</div>
		<table class="pn-tabla">
			<thead><tr><th class="compacto">#</th><th>Producto</th><th class="num">Unidades</th><th class="num">Ventas en que aparece</th></tr></thead>
			<tbody>
				<?php foreach ($productos as $i => $p) { ?>
					<tr>
						<td class="compacto tenue"><?php echo $i + 1 ?></td>
						<td><?php echo $p->nombre !== NULL ? p_e($p->nombre) : '<span class="tenue">Producto eliminado</span>' ?></td>
						<td class="num"><b><?php echo p_numero($p->unidades) ?></b></td>
						<td class="num"><?php echo p_numero($p->ventas) ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</section>
</div>

<script>
PanelGraficos.barras('g-serie', <?php echo json_encode($etiquetas) ?>, [{ nombre: 'Total vendido', datos: <?php echo json_encode($valores) ?> }], { formato: 'pesos' });
</script>
<?php } ?>
