<?php
// Reporte: productos mas vendidos. Datos de Reportes::productos().
$this->load->view('admin/reportes/_filtros', array('con_csv' => TRUE));
$top = array_slice($productos, 0, 15);
$unidades_total = 0;
foreach ($productos as $p) $unidades_total += $p->unidades;
$recortar = function ($texto) { $texto = (string) $texto; return strlen($texto) > 34 ? substr($texto, 0, 32).'…' : $texto; };
?>

<p class="suave" style="margin-bottom:16px"><?php echo p_rango($f['desde'], $f['hasta']) ?> · <?php echo $f['sucursal'] ? p_e($nombre_sucursal($f['sucursal'])) : 'Todas las sucursales' ?></p>

<?php if (empty($productos)) { ?>
	<section class="pn-tarjeta"><div class="pn-vacio"><i class="ti-package"></i>No hay ventas en este período.</div></section>
<?php } else { ?>
<div class="pn-grilla">
	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera"><h2>Los 15 más vendidos</h2><span class="pn-extra">Por unidades</span></div>
		<div class="pn-tarjeta-cuerpo">
			<div class="pn-grafico" style="height:<?php echo max(220, count($top) * 30 + 40) ?>px"><canvas id="g-productos" role="img" aria-label="Unidades vendidas de los 15 productos más vendidos"></canvas></div>
		</div>
	</section>

	<section class="pn-tarjeta c-12">
		<div class="pn-tarjeta-cabecera"><h2>Ranking</h2><span class="pn-extra">Hasta 100 productos</span></div>
		<div class="pn-aviso info" style="margin:16px 20px 0"><i class="ti-info-alt"></i><span>Las ventas no guardan el precio de cada producto, así que el importe se <b>estima</b> con el precio de venta actual.</span></div>
		<div class="pn-tabla-contenedor" style="max-height:620px;overflow-y:auto;margin-top:12px">
			<table class="pn-tabla">
				<thead><tr><th class="compacto">#</th><th>Producto</th><th>Código</th><th class="num">Unidades</th><th class="num">% de unidades</th><th class="num">Ventas en que aparece</th><th class="num">Precio actual</th><th class="num">Importe estimado</th></tr></thead>
				<tbody>
					<?php foreach ($productos as $i => $p) { ?>
						<tr>
							<td class="compacto tenue"><?php echo $i + 1 ?></td>
							<td><?php echo $p->nombre !== NULL ? '<a class="principal-celda" href="'.site_url('admin/productos/edit/'.(int) $p->id).'">'.p_e($p->nombre).'</a>' : '<span class="tenue">Producto eliminado</span>' ?></td>
							<td class="suave"><?php echo p_e($p->codigo) ?></td>
							<td class="num"><b><?php echo p_numero($p->unidades) ?></b></td>
							<td class="num suave"><?php echo p_porcentaje($unidades_total ? $p->unidades / $unidades_total * 100 : 0) ?></td>
							<td class="num"><?php echo p_numero($p->ventas) ?></td>
							<td class="num suave"><?php echo $p->precio_final !== NULL ? p_pesos($p->precio_final) : '—' ?></td>
							<td class="num"><?php echo $p->precio_final !== NULL ? p_pesos($p->importe_estimado) : '—' ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</section>
</div>

<script>
PanelGraficos.barras('g-productos',
	<?php echo json_encode(array_map(function ($p) use ($recortar) { return $p->nombre !== NULL ? $recortar($p->nombre) : 'Producto eliminado'; }, $top)) ?>,
	[{ nombre: 'Unidades', datos: <?php echo json_encode(array_map(function ($p) { return (int) $p->unidades; }, $top)) ?> }],
	{ formato: 'numero', horizontal: true });
</script>
<?php } ?>
