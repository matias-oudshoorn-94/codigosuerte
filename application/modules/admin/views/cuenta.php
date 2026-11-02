<?php // Mi cuenta. Datos de Admin::cuenta(). ?>
<?php if ( ! empty($errores)) { ?>
	<div class="pn-aviso error" role="alert"><i class="ti-alert"></i><span><?php echo p_e(implode(' ', $errores)) ?></span></div>
<?php } ?>

<div class="pn-grilla">
	<section class="pn-tarjeta c-6">
		<div class="pn-tarjeta-cabecera"><h2>Mis datos</h2></div>
		<form method="post" class="pn-tarjeta-cuerpo">
			<input type="hidden" name="accion" value="datos">
			<div class="pn-campos">
				<div class="pn-campo">
					<label for="first_name">Nombre</label>
					<input type="text" id="first_name" name="first_name" value="<?php echo p_e($admin_usuario->first_name) ?>" maxlength="50">
				</div>
				<div class="pn-campo">
					<label for="last_name">Apellido</label>
					<input type="text" id="last_name" name="last_name" value="<?php echo p_e($admin_usuario->last_name) ?>" maxlength="50">
				</div>
				<div class="pn-campo">
					<label>Usuario</label>
					<input type="text" value="<?php echo p_e($admin_usuario->username) ?>" disabled>
					<div class="pn-ayuda">El nombre de usuario no se puede cambiar.</div>
				</div>
				<div class="pn-campo">
					<label for="email">Email</label>
					<input type="email" id="email" name="email" value="<?php echo p_e($admin_usuario->email) ?>" maxlength="100">
				</div>
			</div>
			<div class="pn-acciones-form"><button type="submit" class="pn-boton principal"><i class="ti-check"></i>Guardar datos</button></div>
		</form>
	</section>

	<section class="pn-tarjeta c-6">
		<div class="pn-tarjeta-cabecera"><h2>Cambiar contraseña</h2></div>
		<form method="post" class="pn-tarjeta-cuerpo" autocomplete="off">
			<input type="hidden" name="accion" value="clave">
			<div class="pn-campos">
				<div class="pn-campo ancho">
					<label for="actual">Contraseña actual</label>
					<input type="password" id="actual" name="actual" autocomplete="current-password" required>
				</div>
				<div class="pn-campo">
					<label for="nueva">Contraseña nueva</label>
					<input type="password" id="nueva" name="nueva" autocomplete="new-password" minlength="<?php echo (int) $minimo_clave ?>" required>
					<div class="pn-ayuda">Al menos <?php echo (int) $minimo_clave ?> caracteres.</div>
				</div>
				<div class="pn-campo">
					<label for="repetir">Repetir contraseña nueva</label>
					<input type="password" id="repetir" name="repetir" autocomplete="new-password" required>
				</div>
			</div>
			<div class="pn-acciones-form"><button type="submit" class="pn-boton principal"><i class="ti-lock"></i>Cambiar contraseña</button></div>
		</form>
	</section>
</div>
