<?php // Alta/edicion de sucursal. Datos de Clientes::edit(). ?>
<?php if ($errores) { ?>
	<div class="pn-aviso error" role="alert"><i class="ti-alert"></i><span>Revisá los campos marcados.</span></div>
<?php } ?>

<form method="post" class="pn-form" autocomplete="off">
	<section class="pn-tarjeta">
		<div class="pn-tarjeta-cabecera"><h2>Datos de la sucursal</h2></div>
		<div class="pn-tarjeta-cuerpo pn-campos">
			<?php echo p_campo('nombre', 'Nombre', $valores, $errores, array('requerido' => TRUE, 'ayuda' => 'Es el nombre que se ve en el panel y en el sitio. Ej.: PDF Color Ayacucho.', 'attrs' => 'maxlength="60"')) ?>
			<?php echo p_campo('apellido', 'Detalle (opcional)', $valores, $errores, array('attrs' => 'maxlength="60"', 'ayuda' => 'Se agrega al final del nombre.')) ?>
			<?php echo p_campo('domicilio', 'Dirección', $valores, $errores, array('attrs' => 'maxlength="250"')) ?>
			<?php echo p_campo('localidad', 'Localidad', $valores, $errores, array('attrs' => 'maxlength="250"')) ?>
			<?php echo p_campo('telefono', 'Teléfono', $valores, $errores, array('attrs' => 'maxlength="30"')) ?>
			<?php echo p_campo('email', 'Email', $valores, $errores, array('tipo' => 'email', 'attrs' => 'maxlength="40"')) ?>
		</div>
	</section>

	<section class="pn-tarjeta">
		<div class="pn-tarjeta-cabecera"><h2>Acceso al sistema de ventas</h2></div>
		<div class="pn-tarjeta-cuerpo pn-campos">
			<?php echo p_campo('usuario', 'Usuario', $valores, $errores, array('requerido' => TRUE, 'attrs' => 'maxlength="250" autocapitalize="none" spellcheck="false"')) ?>
			<?php echo p_campo('clave', $id ? 'Contraseña nueva' : 'Contraseña', array(), $errores, array(
				'tipo' => 'password', 'requerido' => ! $id, 'attrs' => 'autocomplete="new-password" minlength="6"',
				'ayuda' => $id ? 'Dejala vacía para no cambiarla. Las contraseñas se guardan encriptadas y no se pueden ver.' : 'Mínimo 6 caracteres.',
			)) ?>
			<?php echo p_opciones('activo', 'Estado', array(1 => 'Activa', 2 => 'Inactiva'), isset($valores['activo']) ? $valores['activo'] : 1, 'Una sucursal inactiva no puede ingresar, pero sus ventas siguen en los reportes.') ?>
			<?php echo p_opciones('caja_cerrada', 'Caja de hoy', array(2 => 'Abierta', 1 => 'Cerrada'), isset($valores['caja_cerrada']) ? $valores['caja_cerrada'] : 2, 'Con la caja cerrada la sucursal no puede vender hasta el día siguiente.') ?>
		</div>
	</section>

	<div class="pn-acciones-form">
		<button type="submit" class="pn-boton principal"><i class="ti-check"></i><?php echo $id ? 'Guardar cambios' : 'Crear sucursal' ?></button>
		<a class="pn-boton" href="<?php echo site_url('admin/clientes') ?>">Cancelar</a>
	</div>
</form>

<?php if ($id) { ?>
	<form method="post" action="<?php echo site_url('admin/clientes/eliminar') ?>" class="pn-form" style="margin-top:32px" data-confirmar="¿Eliminar esta sucursal? Solo se puede si no tiene ventas registradas.">
		<input type="hidden" name="id" value="<?php echo (int) $id ?>">
		<button type="submit" class="pn-boton peligro"><i class="ti-trash"></i>Eliminar sucursal</button>
		<span class="suave" style="margin-left:8px;font-size:13px">Si tiene ventas, conviene marcarla como inactiva.</span>
	</form>
<?php } ?>
