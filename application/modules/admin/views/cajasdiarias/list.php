<?php
// Listado de cierres de caja. Datos de Cajasdiarias::index().
$cerradas = array_filter($sucursales, function ($s) { return $s->activo == 1 && $s->caja_cerrada == 1; });
$hay_filtros = $filtros['sucursal'] || $filtros['desde'] || $filtros['hasta'];
?>

<?php if ($cerradas) { ?>
	<div class="pn-aviso info">
		<i class="ti-lock"></i>
		<span style="flex:1">
			Cajas cerradas ahora: <?php echo p_e(implode(', ', array_map(function ($s) { return $s->etiqueta; }, $cerradas))) ?>.
			Se reabren solas al día siguiente; si una sucursal necesita seguir vendiendo hoy, podés reabrirla.
		</span>
		<?php foreach ($cerradas as $s) { ?>
			<form method="post" action="<?php echo site_url('admin/cajasdiarias/reabrir') ?>" class="en-linea" data-confirmar="¿Reabrir la caja de <?php echo p_e($s->etiqueta) ?>? La sucursal va a poder volver a vender hoy.">
				<input type="hidden" name="sucursal" value="<?php echo (int) $s->idusuario ?>">
				<button type="submit" class="pn-boton chico"><i class="ti-unlock"></i>Reabrir <?php echo p_e($s->usuario) ?></button>
			</form>
		<?php } ?>
	</div>
<?php } ?>

<form method="get" class="pn-filtros">
	<div class="pn-campo">
		<label for="f-sucursal">Sucursal</label>
		<select id="f-sucursal" name="sucursal" data-autoenviar>
			<option value="">Todas</option>
			<?php foreach ($sucursales as $s) { ?>
				<option value="<?php echo (int) $s->idusuario ?>"<?php echo $filtros['sucursal'] == $s->idusuario ? ' selected' : '' ?>><?php echo p_e($s->etiqueta) ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="pn-campo">
		<label for="f-desde">Desde</label>
		<input type="date" id="f-desde" name="desde" value="<?php echo p_e($filtros['desde']) ?>">
	</div>
	<div class="pn-campo">
		<label for="f-hasta">Hasta</label>
		<input type="date" id="f-hasta" name="hasta" value="<?php echo p_e($filtros['hasta']) ?>">
	</div>
	<button type="submit" class="pn-boton principal"><i class="ti-filter"></i>Filtrar</button>
	<?php if ($hay_filtros) { ?><a class="pn-boton fantasma" href="<?php echo site_url('admin/cajasdiarias') ?>">Limpiar</a><?php } ?>
</form>

<div class="pn-kpis">
	<div class="pn-kpi"><div class="pn-kpi-etiqueta">Cierres</div><div class="pn-kpi-valor"><?php echo p_numero($pag['total']) ?></div></div>
	<div class="pn-kpi"><div class="pn-kpi-etiqueta">Total vendido</div><div class="pn-kpi-valor"><?php echo p_pesos($totales->total) ?></div></div>
	<div class="pn-kpi"><div class="pn-kpi-etiqueta">Ventas</div><div class="pn-kpi-valor"><?php echo p_numero($totales->ventas) ?></div></div>
	<div class="pn-kpi"><div class="pn-kpi-etiqueta">Impresiones declaradas</div><div class="pn-kpi-valor"><?php echo p_numero($totales->impresiones) ?></div></div>
</div>

<section class="pn-tarjeta">
	<?php if (empty($cajas)) { ?>
		<div class="pn-vacio"><i class="ti-wallet"></i>No hay cierres de caja<?php echo $hay_filtros ? ' con esos filtros' : '' ?>.</div>
	<?php } else { ?>
		<div class="pn-tabla-contenedor">
			<table class="pn-tabla">
				<thead>
					<tr>
						<th>Fecha</th><th>Sucursal</th><th class="num">Ventas</th><th class="num">Total</th>
						<th class="num">Efectivo</th><th class="num">Tarjeta</th><th class="num">Electrónico</th><th class="num">Transf./QR</th>
						<th class="num">Impresiones</th><th class="num">Desperdicio</th><th class="compacto"><span class="sr">Acciones</span></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($cajas as $c) { $s = isset($sucursales[(int) $c->id_usuario]) ? $sucursales[(int) $c->id_usuario] : NULL; ?>
						<tr>
							<td><a class="principal-celda" href="<?php echo site_url('admin/cajasdiarias/ver/'.(int) $c->id) ?>"><?php echo p_fecha($c->fecha) ?></a></td>
							<td><?php echo $s ? p_e($s->etiqueta) : '<span class="tenue">Sucursal #'.(int) $c->id_usuario.'</span>' ?></td>
							<td class="num"><?php echo p_numero($c->cantidad_ventas) ?></td>
							<td class="num"><b><?php echo p_pesos($c->total) ?></b></td>
							<td class="num"><?php echo p_pesos($c->total_efectivo) ?></td>
							<td class="num"><?php echo p_pesos($c->total_tarjeta) ?></td>
							<td class="num"><?php echo p_pesos($c->total_medio_electronico) ?></td>
							<td class="num"><?php echo p_pesos($c->total_transferencia) ?></td>
							<td class="num"><?php echo p_numero($c->impresiones_reales) ?></td>
							<td class="num"><?php echo $c->impresiones_reales > 0 ? p_porcentaje($c->desperdicio) : '—' ?></td>
							<td class="acciones">
								<a class="pn-boton chico" href="<?php echo site_url('admin/cajasdiarias/ver/'.(int) $c->id) ?>">Ver</a>
								<form method="post" action="<?php echo site_url('admin/cajasdiarias/eliminar') ?>" class="en-linea" data-confirmar="¿Eliminar el cierre del <?php echo p_fecha($c->fecha) ?>? Las ventas no se borran, solo el resumen del cierre.">
									<input type="hidden" name="id" value="<?php echo (int) $c->id ?>">
									<button type="submit" class="pn-boton chico icono peligro" title="Eliminar" aria-label="Eliminar"><i class="ti-trash"></i></button>
								</form>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="pn-tarjeta-pie" style="display:block"><?php $this->load->view('admin/partials/paginacion', array('pag' => $pag)) ?></div>
	<?php } ?>
</section>
