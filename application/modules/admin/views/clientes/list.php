<?php // Listado de sucursales. Datos de Clientes::index(). ?>
<section class="pn-tarjeta">
	<?php if (empty($lista)) { ?>
		<div class="pn-vacio"><i class="ti-location-pin"></i>Todavía no hay sucursales. <a href="<?php echo site_url('admin/clientes/edit/0') ?>">Crear la primera</a></div>
	<?php } else { ?>
		<div class="pn-tabla-contenedor">
			<table class="pn-tabla">
				<thead>
					<tr><th>Sucursal</th><th>Dirección</th><th>Contacto</th><th>Estado</th><th>Caja</th><th class="num">Impresoras</th><th>Último cierre</th><th class="compacto"><span class="sr">Acciones</span></th></tr>
				</thead>
				<tbody>
					<?php foreach ($lista as $s) { ?>
						<tr<?php echo $s->activo == 1 ? '' : ' class="tenue"' ?>>
							<td>
								<a class="principal-celda" href="<?php echo site_url('admin/clientes/edit/'.(int) $s->idusuario) ?>"><?php echo p_e(p_nombre_sucursal($s)) ?></a>
								<div class="tenue">Usuario: <?php echo p_e($s->usuario) ?></div>
							</td>
							<td><?php echo p_e($s->domicilio) ?><div class="tenue"><?php echo p_e($s->localidad) ?></div></td>
							<td><?php echo p_e($s->telefono) ?><div class="tenue"><?php echo p_e($s->email) ?></div></td>
							<td><?php echo $s->activo == 1 ? '<span class="pn-etiqueta verde">Activa</span>' : '<span class="pn-etiqueta gris">Inactiva</span>' ?></td>
							<td><?php echo $s->caja_cerrada == 1 ? '<span class="pn-etiqueta ambar">Cerrada</span>' : '<span class="pn-etiqueta verde">Abierta</span>' ?></td>
							<td class="num"><?php echo (int) $s->impresoras ?></td>
							<td class="suave"><?php echo $s->ultimo_cierre ? p_fecha($s->ultimo_cierre) : '—' ?></td>
							<td class="acciones">
								<a class="pn-boton chico" href="<?php echo site_url('admin/clientes/sub_index/'.(int) $s->idusuario) ?>"><i class="ti-printer"></i>Impresoras</a>
								<a class="pn-boton chico" href="<?php echo site_url('admin/clientes/edit/'.(int) $s->idusuario) ?>">Editar</a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	<?php } ?>
</section>
