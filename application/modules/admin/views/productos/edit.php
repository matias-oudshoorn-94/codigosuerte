<?php // Alta/edicion de producto. Datos de Productos::edit(). ?>
<?php if ($errores) { ?>
	<div class="pn-aviso error" role="alert"><i class="ti-alert"></i><span>Revisá los campos marcados.</span></div>
<?php } ?>
<?php if ($repetidos) { ?>
	<div class="pn-aviso error" role="status"><i class="ti-alert"></i>
		<span>Este código también lo tiene<?php echo count($repetidos) > 1 ? 'n' : '' ?>:
			<?php foreach ($repetidos as $i => $r) { echo ($i ? ', ' : '').'<a href="'.site_url('admin/productos/edit/'.(int) $r->id).'">'.p_e($r->nombre).'</a>'; } ?>.
			Al escanearlo se agrega uno solo; cambiá el código del que esté mal.
		</span>
	</div>
<?php } ?>

<form method="post" class="pn-form" id="form-producto">
	<section class="pn-tarjeta">
		<div class="pn-tarjeta-cabecera"><h2>Producto</h2><?php if ($id) { ?><span class="pn-extra">Vendido en <?php echo p_numero($vendido) ?> ventas</span><?php } ?></div>
		<div class="pn-tarjeta-cuerpo pn-campos">
			<?php echo p_campo('nombre', 'Nombre', $valores, $errores, array('requerido' => TRUE, 'clase' => 'ancho', 'attrs' => 'maxlength="250"')) ?>
			<?php echo p_campo('codigo', 'Código de barras', $valores, $errores, array('attrs' => 'maxlength="250" inputmode="numeric" autocomplete="off"', 'ayuda' => 'Es el que lee el escáner en la venta. Tiene que ser único.')) ?>
		</div>
	</section>

	<section class="pn-tarjeta">
		<div class="pn-tarjeta-cabecera"><h2>Precio</h2></div>
		<div class="pn-tarjeta-cuerpo pn-campos" style="grid-template-columns:repeat(3,minmax(0,1fr))">
			<?php echo p_campo('precio', 'Costo', $valores, $errores, array('tipo' => 'number', 'prefijo' => '$', 'attrs' => 'min="0" step="0.01"')) ?>
			<?php echo p_campo('porcentaje_marcado', 'Marcado', $valores, $errores, array('tipo' => 'number', 'attrs' => 'step="1"', 'ayuda' => 'Porcentaje sobre el costo.')) ?>
			<?php echo p_campo('precio_final', 'Precio de venta', $valores, $errores, array('tipo' => 'number', 'prefijo' => '$', 'attrs' => 'min="0" step="0.01"', 'ayuda' => 'Se calcula solo con el costo y el marcado; podés cambiarlo a mano.')) ?>
		</div>
	</section>

	<section class="pn-tarjeta">
		<div class="pn-tarjeta-cabecera"><h2>Stock por sucursal</h2><span class="pn-extra">Se descuenta solo con cada venta</span></div>
		<?php if (empty($sucursales)) { ?>
			<div class="pn-vacio">No hay sucursales activas.</div>
		<?php } else { ?>
			<div class="pn-tarjeta-cuerpo pn-campos">
				<?php foreach ($sucursales as $s) { ?>
					<div class="pn-campo">
						<label for="stock-<?php echo (int) $s->idusuario ?>"><?php echo p_e($s->etiqueta) ?></label>
						<input type="number" id="stock-<?php echo (int) $s->idusuario ?>" name="stock[<?php echo (int) $s->idusuario ?>]" min="0" step="1" value="<?php echo isset($stock[(int) $s->idusuario]) ? (int) $stock[(int) $s->idusuario] : 0 ?>">
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</section>

	<div class="pn-acciones-form">
		<button type="submit" class="pn-boton principal"><i class="ti-check"></i><?php echo $id ? 'Guardar cambios' : 'Crear producto' ?></button>
		<a class="pn-boton" href="<?php echo site_url('admin/productos') ?>">Cancelar</a>
	</div>
</form>

<?php if ($id) { ?>
	<form method="post" action="<?php echo site_url('admin/productos/eliminar') ?>" class="pn-form" style="margin-top:32px"
		data-confirmar="¿Eliminar este producto?<?php echo $vendido ? ' Se vendió '.p_numero($vendido).' veces: en esas ventas va a figurar como «Producto eliminado».' : '' ?>">
		<input type="hidden" name="id" value="<?php echo (int) $id ?>">
		<button type="submit" class="pn-boton peligro"><i class="ti-trash"></i>Eliminar producto</button>
	</form>
<?php } ?>

<script>
// Precio de venta = costo + marcado, mientras no se cambie a mano
(function () {
	var costo = document.getElementById('c-precio');
	var marcado = document.getElementById('c-porcentaje_marcado');
	var venta = document.getElementById('c-precio_final');
	var manual = venta.value !== '' && Math.abs(parseFloat(venta.value) - calcular()) > 0.01;
	function calcular() {
		var c = parseFloat(costo.value) || 0, m = parseFloat(marcado.value) || 0;
		return Math.round(c * (1 + m / 100) * 100) / 100;
	}
	function actualizar() { if (!manual) venta.value = calcular() || ''; }
	costo.addEventListener('input', actualizar);
	marcado.addEventListener('input', actualizar);
	venta.addEventListener('input', function () { manual = venta.value !== ''; });
})();
</script>
