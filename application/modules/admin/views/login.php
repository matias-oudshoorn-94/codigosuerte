<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Ingresar | Administración PDF Color</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(AADMINPATH.'img/candado_a/favicon.ico') ?>">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">
	<link href="<?php echo base_url('application/views/layouts/css/themify-icons.css') ?>" rel="stylesheet">
	<link href="<?php echo base_url(AADMINPATH.'css/panel.css?v=1') ?>" rel="stylesheet">
</head>
<body class="pn pn-login">
	<div class="pn-login-tarjeta">
		<img src="<?php echo base_url('application/views/layouts/img/logo2.png') ?>" alt="PDF Color">
		<h1>Panel de administración</h1>
		<p class="sub">Ingresá con tu usuario de administrador</p>

		<?php if ($error !== '') { ?>
			<div class="pn-aviso error" role="alert"><i class="ti-alert"></i><span><?php echo p_e($error) ?></span></div>
		<?php } ?>

		<form method="post" action="<?php echo site_url('admin/login') ?>">
			<div class="pn-campo">
				<label for="identity">Usuario</label>
				<input type="text" name="identity" id="identity" value="<?php echo p_e($usuario) ?>" autocomplete="username" autocapitalize="none" spellcheck="false" required>
			</div>
			<div class="pn-campo">
				<label for="password">Contraseña</label>
				<input type="password" name="password" id="password" autocomplete="current-password" required>
			</div>
			<label class="suave" style="display:flex;gap:8px;align-items:center;font-size:13px;margin:4px 0 8px">
				<input type="checkbox" name="recordar" value="1"> Mantener la sesión iniciada
			</label>
			<button type="submit" class="pn-boton principal">Ingresar</button>
		</form>
	</div>
	<script>
		document.getElementById(<?php echo json_encode($usuario !== '' ? 'password' : 'identity') ?>).focus();
	</script>
</body>
</html>
