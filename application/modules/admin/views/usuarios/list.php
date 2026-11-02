<?php // Usuarios del panel. Datos de Usuarios::index(). ?>
<section class="pn-tarjeta">
	<div class="pn-tabla-contenedor">
		<table class="pn-tabla">
			<thead><tr><th>Usuario</th><th>Nombre</th><th>Email</th><th>Permisos</th><th>Estado</th><th>Último ingreso</th><th class="compacto"><span class="sr">Acciones</span></th></tr></thead>
			<tbody>
				<?php foreach ($usuarios as $u) { $es_yo = (int) $u->id === (int) $admin_usuario->id; ?>
					<tr<?php echo $u->active ? '' : ' class="tenue"' ?>>
						<td><a class="principal-celda" href="<?php echo site_url('admin/usuarios/edit/'.(int) $u->id) ?>"><?php echo p_e($u->username) ?></a><?php echo $es_yo ? ' <span class="tenue">(vos)</span>' : '' ?></td>
						<td><?php echo p_e(trim($u->first_name.' '.$u->last_name)) ?></td>
						<td class="suave"><?php echo p_e($u->email) ?></td>
						<td><?php echo $u->es_admin ? '<span class="pn-etiqueta verde">Administrador</span>' : '<span class="pn-etiqueta gris">Sin acceso</span>' ?></td>
						<td><?php echo $u->active ? '<span class="pn-etiqueta verde">Activo</span>' : '<span class="pn-etiqueta gris">Inactivo</span>' ?></td>
						<td class="suave"><?php echo $u->last_login ? p_fecha(date('Y-m-d', $u->last_login)).' '.date('H:i', $u->last_login) : 'Nunca' ?></td>
						<td class="acciones">
							<a class="pn-boton chico" href="<?php echo site_url('admin/usuarios/edit/'.(int) $u->id) ?>">Editar</a>
							<?php if ( ! $es_yo) { ?>
								<form method="post" action="<?php echo site_url('admin/usuarios/eliminar') ?>" class="en-linea" data-confirmar="¿Eliminar el usuario <?php echo p_e($u->username) ?>?">
									<input type="hidden" name="id" value="<?php echo (int) $u->id ?>">
									<button type="submit" class="pn-boton chico icono peligro" title="Eliminar" aria-label="Eliminar"><i class="ti-trash"></i></button>
								</form>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</section>
<p class="suave" style="font-size:13px">Solo los usuarios con permiso de <b>Administrador</b> y estado <b>Activo</b> pueden entrar al panel. Las sucursales se manejan en <a href="<?php echo site_url('admin/clientes') ?>">Sucursales</a>.</p>
