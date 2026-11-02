<?php // Alta/edicion de usuario del panel. Datos de Usuarios::edit(). ?>
<?php if ($errores) { ?>
	<div class="pn-aviso error" role="alert"><i class="ti-alert"></i><span>Revisá los campos marcados.</span></div>
<?php } ?>

<form method="post" class="pn-form" autocomplete="off">
	<section class="pn-tarjeta">
		<div class="pn-tarjeta-cabecera"><h2>Datos</h2></div>
		<div class="pn-tarjeta-cuerpo pn-campos">
			<?php echo p_campo('username', 'Usuario', $valores, $errores, array('requerido' => TRUE, 'attrs' => 'maxlength="100" autocapitalize="none" spellcheck="false"', 'ayuda' => 'Con este nombre se ingresa al panel.')) ?>
			<?php echo p_campo('email', 'Email', $valores, $errores, array('tipo' => 'email', 'requerido' => TRUE, 'attrs' => 'maxlength="100"')) ?>
			<?php echo p_campo('first_name', 'Nombre', $valores, $errores, array('attrs' => 'maxlength="50"')) ?>
			<?php echo p_campo('last_name', 'Apellido', $valores, $errores, array('attrs' => 'maxlength="50"')) ?>
		</div>
	</section>

	<section class="pn-tarjeta">
		<div class="pn-tarjeta-cabecera"><h2>Acceso</h2></div>
		<div class="pn-tarjeta-cuerpo pn-campos">
			<?php echo p_campo('clave', $id ? 'Contraseña nueva' : 'Contraseña', array(), $errores, array(
				'tipo' => 'password', 'requerido' => ! $id, 'clase' => 'ancho', 'attrs' => 'autocomplete="new-password" minlength="'.(int) $minimo_clave.'"',
				'ayuda' => ($id ? 'Dejala vacía para no cambiarla. ' : '').'Al menos '.(int) $minimo_clave.' caracteres.',
			)) ?>
			<?php if ($es_yo) { ?>
				<p class="suave ancho" style="font-size:13px">No podés quitarte los permisos ni desactivar tu propio usuario.</p>
			<?php } else { ?>
				<?php echo p_opciones('es_admin', 'Permisos', array(1 => 'Administrador', 0 => 'Sin acceso al panel'), $valores['es_admin']) ?>
				<?php echo p_opciones('active', 'Estado', array(1 => 'Activo', 0 => 'Inactivo'), $valores['active']) ?>
			<?php } ?>
		</div>
	</section>

	<div class="pn-acciones-form">
		<button type="submit" class="pn-boton principal"><i class="ti-check"></i><?php echo $id ? 'Guardar cambios' : 'Crear usuario' ?></button>
		<a class="pn-boton" href="<?php echo site_url('admin/usuarios') ?>">Cancelar</a>
	</div>
</form>
