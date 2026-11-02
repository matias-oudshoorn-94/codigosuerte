<?php
// Pestañas + filtros comunes de los reportes.
// Variables: $pestana, $f, $atajos, $sucursales; opcionales: $con_csv (bool), $sin_fechas (bool), $anio
$pn_pestanas = array(
	'resumen'     => array('admin/reportes',             'Resumen',     'ti-dashboard'),
	'ventas'      => array('admin/reportes/ventas',      'Ventas',      'ti-stats-up'),
	'mensual'     => array('admin/reportes/mensual',     'Mensual',     'ti-calendar'),
	'sucursales'  => array('admin/reportes/sucursales',  'Sucursales',  'ti-location-pin'),
	'productos'   => array('admin/reportes/productos',   'Productos',   'ti-package'),
	'horarios'    => array('admin/reportes/horarios',    'Horarios',    'ti-time'),
	'impresiones' => array('admin/reportes/impresiones', 'Impresiones', 'ti-printer'),
);
// Al cambiar de pestaña se conservan el periodo y la sucursal
$pn_base = array_filter(array(
	'periodo'  => $f['periodo'],
	'desde'    => $f['periodo'] ? '' : $f['desde'],
	'hasta'    => $f['periodo'] ? '' : $f['hasta'],
	'sucursal' => $f['sucursal'] ?: '',
));
$pn_link = function ($ruta, $extra = array()) use ($pn_base) {
	$q = array_filter(array_merge($pn_base, $extra), function ($v) { return $v !== '' && $v !== NULL; });
	return site_url($ruta).($q ? '?'.http_build_query($q) : '');
};
$pn_ruta_actual = $pn_pestanas[$pestana][0];
?>
<nav class="pn-pestanas" aria-label="Tipos de reporte">
	<?php foreach ($pn_pestanas as $clave => $p) { ?>
		<a href="<?php echo p_e($pn_link($p[0])) ?>" class="<?php echo $pestana === $clave ? 'activo' : '' ?>"<?php echo $pestana === $clave ? ' aria-current="page"' : '' ?>><i class="<?php echo $p[2] ?>"></i><?php echo $p[1] ?></a>
	<?php } ?>
</nav>

<form method="get" action="<?php echo site_url($pn_ruta_actual) ?>" class="pn-filtros" style="margin-bottom:12px">
	<?php if (empty($sin_fechas)) { ?>
		<div class="pn-campo">
			<label for="f-desde">Desde</label>
			<input type="date" id="f-desde" name="desde" value="<?php echo p_e($f['desde']) ?>" max="<?php echo date('Y-m-d') ?>">
		</div>
		<div class="pn-campo">
			<label for="f-hasta">Hasta</label>
			<input type="date" id="f-hasta" name="hasta" value="<?php echo p_e($f['hasta']) ?>" max="<?php echo date('Y-m-d') ?>">
		</div>
	<?php } else { ?>
		<div class="pn-campo">
			<label for="f-anio">Año</label>
			<select id="f-anio" name="anio" data-autoenviar>
				<?php for ($a = (int) date('Y'); $a >= 2021; $a--) { ?>
					<option value="<?php echo $a ?>"<?php echo $a === (int) $anio ? ' selected' : '' ?>><?php echo $a ?></option>
				<?php } ?>
			</select>
		</div>
	<?php } ?>
	<div class="pn-campo">
		<label for="f-sucursal">Sucursal</label>
		<select id="f-sucursal" name="sucursal" data-autoenviar>
			<option value="">Todas las sucursales</option>
			<?php foreach ($sucursales as $s) { ?>
				<option value="<?php echo (int) $s->idusuario ?>"<?php echo $f['sucursal'] == $s->idusuario ? ' selected' : '' ?>><?php echo p_e($s->etiqueta).($s->activo == 1 ? '' : ' (inactiva)') ?></option>
			<?php } ?>
		</select>
	</div>
	<?php if (empty($sin_fechas)) { ?><button type="submit" class="pn-boton principal"><i class="ti-filter"></i>Aplicar</button><?php } ?>
	<?php if ( ! empty($con_csv)) { ?>
		<a class="pn-boton" style="margin-left:auto" href="<?php echo p_e(current_url().'?'.http_build_query(array_merge($_GET, array('csv' => 1)))) ?>"><i class="ti-download"></i>Descargar Excel (CSV)</a>
	<?php } ?>
</form>

<?php if (empty($sin_fechas)) { ?>
	<div class="pn-atajos" style="margin-bottom:20px">
		<?php foreach ($atajos as $clave => $a) { ?>
			<a href="<?php echo p_e(site_url($pn_ruta_actual).'?'.http_build_query(array_filter(array('periodo' => $clave, 'sucursal' => $f['sucursal'] ?: '', 'agrupar' => $this->input->get('agrupar'))))) ?>" class="<?php echo $f['periodo'] === $clave ? 'activo' : '' ?>"><?php echo $a[0] ?></a>
		<?php } ?>
	</div>
<?php } ?>
