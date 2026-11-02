<?php
// Listado de productos. Datos de Productos::index().
$pn_orden = function ($columna, $texto, $num = FALSE) use ($orden, $direccion) {
	$q = $_GET;
	$activa = $orden === $columna;
	$q['orden'] = $columna;
	$q['dir'] = $activa && $direccion === 'asc' ? 'desc' : 'asc';
	unset($q['pagina']);
	$flecha = $activa ? ($direccion === 'asc' ? ' <i class="ti-arrow-up"></i>' : ' <i class="ti-arrow-down"></i>') : '';
	return '<th'.($num ? ' class="num"' : '').($activa ? ' aria-sort="'.($direccion === 'asc' ? 'ascending' : 'descending').'"' : '').'><a href="?'.p_e(http_build_query($q)).'">'.$texto.$flecha.'</a></th>';
};
?>
<form method="get" class="pn-filtros">
	<div class="pn-campo ancho">
		<label for="f-q">Buscar</label>
		<input type="search" id="f-q" name="q" value="<?php echo p_e($q) ?>" placeholder="Nombre o código de barras">
	</div>
	<?php if ($repetidos) { ?><input type="hidden" name="repetidos" value="1"><?php } ?>
	<button type="submit" class="pn-boton principal"><i class="ti-search"></i>Buscar</button>
	<?php if ($q !== '') { ?><a class="pn-boton fantasma" href="<?php echo site_url('admin/productos'.($repetidos ? '?repetidos=1' : '')) ?>">Limpiar</a><?php } ?>
	<div class="pn-atajos" style="margin-left:auto">
		<a href="<?php echo site_url('admin/productos'.($q !== '' ? '?q='.urlencode($q) : '')) ?>" class="<?php echo $repetidos ? '' : 'activo' ?>">Todos</a>
		<a href="<?php echo site_url('admin/productos?repetidos=1'.($q !== '' ? '&q='.urlencode($q) : '')) ?>" class="<?php echo $repetidos ? 'activo' : '' ?>">Códigos repetidos</a>
	</div>
</form>

<?php if ($repetidos) { ?>
	<div class="pn-aviso info"><i class="ti-info-alt"></i><span>Estos productos comparten código con otros. Al escanear un código repetido se agrega siempre el mismo producto, así que conviene corregirlos.</span></div>
<?php } ?>

<section class="pn-tarjeta">
	<?php if (empty($lista)) { ?>
		<div class="pn-vacio"><i class="ti-package"></i><?php echo $q !== '' ? 'No hay productos que coincidan con "'.p_e($q).'".' : 'No hay productos.' ?></div>
	<?php } else { ?>
		<div class="pn-tabla-contenedor">
			<table class="pn-tabla">
				<thead>
					<tr>
						<?php echo $pn_orden('codigo', 'Código') ?>
						<?php echo $pn_orden('nombre', 'Producto') ?>
						<?php echo $pn_orden('precio', 'Costo', TRUE) ?>
						<th class="num">Marcado</th>
						<?php echo $pn_orden('precio_final', 'Precio de venta', TRUE) ?>
						<?php echo $pn_orden('stock', 'Stock', TRUE) ?>
						<th class="compacto"><span class="sr">Acciones</span></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($lista as $p) { ?>
						<tr>
							<td>
								<code style="font-size:13px"><?php echo $p->codigo !== '' ? p_e($p->codigo) : '<span class="tenue">—</span>' ?></code>
								<?php if ($p->repetidos > 0) { ?><span class="pn-etiqueta ambar" title="Otros <?php echo (int) $p->repetidos ?> producto(s) tienen este código">Repetido</span><?php } ?>
							</td>
							<td><a class="principal-celda" href="<?php echo site_url('admin/productos/edit/'.(int) $p->id) ?>"><?php echo p_e($p->nombre) ?></a></td>
							<td class="num suave"><?php echo p_pesos($p->precio) ?></td>
							<td class="num suave"><?php echo p_porcentaje($p->porcentaje_marcado, 0) ?></td>
							<td class="num"><b><?php echo p_pesos($p->precio_final) ?></b></td>
							<td class="num"><?php echo $p->stock_total > 0 ? p_numero($p->stock_total) : '<span class="tenue">0</span>' ?></td>
							<td class="acciones"><a class="pn-boton chico" href="<?php echo site_url('admin/productos/edit/'.(int) $p->id) ?>">Editar</a></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	<?php } ?>
	<div class="pn-tarjeta-pie" style="display:block"><?php $this->load->view('admin/partials/paginacion', array('pag' => $pag)) ?></div>
</section>
