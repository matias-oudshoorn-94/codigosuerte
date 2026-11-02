<?php
// Stock por sucursal. Datos de Stockproductos::index().
$pn_volver = $_GET ? '?'.http_build_query($_GET) : '';
$pn_filtro = function ($valor, $texto) use ($q, $filtro, $umbral) {
	$params = array_filter(array('q' => $q, 'filtro' => $valor, 'umbral' => $valor === 'bajo' ? $umbral : ''));
	return '<a href="'.site_url('admin/stockproductos').($params ? '?'.p_e(http_build_query($params)) : '').'" class="'.($filtro === $valor ? 'activo' : '').'">'.$texto.'</a>';
};
?>
<form method="get" class="pn-filtros">
	<div class="pn-campo ancho">
		<label for="f-q">Buscar</label>
		<input type="search" id="f-q" name="q" value="<?php echo p_e($q) ?>" placeholder="Nombre o código">
	</div>
	<?php if ($filtro) { ?><input type="hidden" name="filtro" value="<?php echo p_e($filtro) ?>"><?php } ?>
	<?php if ($filtro === 'bajo') { ?>
		<div class="pn-campo" style="min-width:110px">
			<label for="f-umbral">Stock hasta</label>
			<input type="number" id="f-umbral" name="umbral" min="1" value="<?php echo (int) $umbral ?>">
		</div>
	<?php } ?>
	<button type="submit" class="pn-boton principal"><i class="ti-search"></i>Buscar</button>
	<div class="pn-atajos" style="margin-left:auto">
		<?php echo $pn_filtro('', 'Todos') ?>
		<?php echo $pn_filtro('bajo', 'Stock bajo') ?>
		<?php echo $pn_filtro('sin', 'Sin stock') ?>
	</div>
</form>

<?php if (empty($sucursales)) { ?>
	<div class="pn-aviso info"><i class="ti-info-alt"></i><span>No hay sucursales activas.</span></div>
<?php } ?>

<form method="post" action="<?php echo site_url('admin/stockproductos/guardar') ?>" class="pn-tarjeta">
	<input type="hidden" name="volver" value="<?php echo p_e($pn_volver) ?>">
	<div class="pn-tarjeta-cabecera">
		<h2><?php echo $filtro === 'sin' ? 'Productos sin stock en alguna sucursal' : ($filtro === 'bajo' ? 'Productos con stock bajo en alguna sucursal' : 'Stock de productos') ?></h2>
		<span class="pn-extra">Cambiá las cantidades y guardá</span>
	</div>
	<?php if (empty($productos)) { ?>
		<div class="pn-vacio"><i class="ti-layers"></i>No hay productos para mostrar.</div>
	<?php } else { ?>
		<div class="pn-tabla-contenedor">
			<table class="pn-tabla">
				<thead>
					<tr>
						<th>Producto</th>
						<?php foreach ($sucursales as $s) { ?><th class="num"><?php echo p_e($s->etiqueta) ?></th><?php } ?>
						<th class="num">Total</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($productos as $p) { $total = 0; ?>
						<tr>
							<td>
								<a class="principal-celda" href="<?php echo site_url('admin/productos/edit/'.(int) $p->id) ?>"><?php echo p_e($p->nombre) ?></a>
								<div class="tenue"><?php echo p_e($p->codigo) ?></div>
							</td>
							<?php foreach ($sucursales as $s) {
								$cantidad = isset($stock[(int) $p->id][(int) $s->idusuario]) ? $stock[(int) $p->id][(int) $s->idusuario] : 0;
								$total += $cantidad;
								$clase = $cantidad <= 0 ? 'rojo' : ($cantidad <= $umbral ? 'ambar' : ''); ?>
								<td class="num">
									<input type="hidden" name="original[<?php echo (int) $p->id ?>][<?php echo (int) $s->idusuario ?>]" value="<?php echo $cantidad ?>">
									<span class="pn-campo"><input type="number" min="0" step="1" style="width:90px;height:34px;text-align:right<?php echo $clase === 'rojo' ? ';border-color:#f2c4c6;background:#fffafa' : '' ?>"
										name="stock[<?php echo (int) $p->id ?>][<?php echo (int) $s->idusuario ?>]" value="<?php echo $cantidad ?>"
										aria-label="Stock de <?php echo p_e($p->nombre) ?> en <?php echo p_e($s->etiqueta) ?>"></span>
								</td>
							<?php } ?>
							<td class="num"><b><?php echo p_numero($total) ?></b></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="pn-tarjeta-pie">
			<button type="submit" class="pn-boton principal"><i class="ti-check"></i>Guardar stock</button>
			<span class="suave" style="font-size:13px">Solo se guardan las cantidades que cambiaste.</span>
		</div>
	<?php } ?>
	<div class="pn-tarjeta-pie" style="display:block"><?php $this->load->view('admin/partials/paginacion', array('pag' => $pag)) ?></div>
</form>
