<?php
// Layout del panel de administracion.
// Variables: $titulo_pagina, $seccion, $acciones (opcional), $graficos (opcional), $admin_usuario
$pn_secciones = array(
	'General' => array(
		'inicio'   => array('url' => 'admin',          'texto' => 'Inicio',   'icono' => 'ti-home'),
		'reportes' => array('url' => 'admin/reportes', 'texto' => 'Reportes', 'icono' => 'ti-bar-chart'),
	),
	'Operación' => array(
		'cajas'     => array('url' => 'admin/cajasdiarias',   'texto' => 'Cajas diarias', 'icono' => 'ti-wallet'),
		'productos' => array('url' => 'admin/productos',      'texto' => 'Productos',     'icono' => 'ti-package'),
		'stock'     => array('url' => 'admin/stockproductos', 'texto' => 'Stock',         'icono' => 'ti-layers'),
	),
	'Configuración' => array(
		'sucursales' => array('url' => 'admin/clientes', 'texto' => 'Sucursales', 'icono' => 'ti-location-pin'),
		'usuarios'   => array('url' => 'admin/usuarios', 'texto' => 'Usuarios',   'icono' => 'ti-user'),
	),
);
$pn_seccion = isset($seccion) ? $seccion : '';
$pn_titulo = isset($titulo_pagina) ? $titulo_pagina : 'Administración';
$pn_nombre = '';
if (isset($admin_usuario) && $admin_usuario)
{
	$pn_nombre = trim($admin_usuario->first_name.' '.$admin_usuario->last_name);
	if ($pn_nombre === '') $pn_nombre = $admin_usuario->username;
}
$pn_inicial = strtoupper(substr($pn_nombre !== '' ? $pn_nombre : 'A', 0, 1));
$pn_aviso = $this->session->flashdata('panel_aviso');
?><!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title><?php echo p_e($template['title']) ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(AADMINPATH.'img/candado_a/favicon.ico') ?>">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">
	<link href="<?php echo base_url('application/views/layouts/css/themify-icons.css') ?>" rel="stylesheet">
	<link href="<?php echo base_url(AADMINPATH.'css/panel.css?v=1') ?>" rel="stylesheet">
	<?php if ( ! empty($graficos)) { ?>
		<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
		<script src="<?php echo base_url(AADMINPATH.'js/panel-graficos.js?v=1') ?>"></script>
	<?php } ?>
</head>
<body class="pn">
	<aside class="pn-lateral" id="pn-lateral">
		<a class="pn-marca" href="<?php echo site_url('admin') ?>">
			<img src="<?php echo base_url('application/views/layouts/img/logo2.png') ?>" alt="">
			<span>PDF Color<small>Administración</small></span>
		</a>
		<nav class="pn-nav" aria-label="Secciones">
			<?php foreach ($pn_secciones as $pn_grupo => $pn_links) { ?>
				<div class="pn-nav-titulo"><?php echo $pn_grupo ?></div>
				<?php foreach ($pn_links as $pn_clave => $pn_link) { ?>
					<a href="<?php echo site_url($pn_link['url']) ?>" class="<?php echo $pn_seccion === $pn_clave ? 'activo' : '' ?>"<?php echo $pn_seccion === $pn_clave ? ' aria-current="page"' : '' ?>>
						<i class="<?php echo $pn_link['icono'] ?>"></i><?php echo $pn_link['texto'] ?>
					</a>
				<?php } ?>
			<?php } ?>
		</nav>
		<div class="pn-cuenta">
			<span class="pn-avatar"><?php echo p_e($pn_inicial) ?></span>
			<div class="pn-cuenta-texto">
				<b><?php echo p_e($pn_nombre) ?></b>
				<a href="<?php echo site_url('admin/admin/cuenta') ?>">Mi cuenta</a>
			</div>
			<a class="pn-salir" href="<?php echo site_url('admin/logout') ?>" title="Cerrar sesión" aria-label="Cerrar sesión"><i class="ti-power-off"></i></a>
		</div>
	</aside>
	<div class="pn-velo" id="pn-velo"></div>

	<div class="pn-principal">
		<header class="pn-barra">
			<button type="button" class="pn-menu-movil" id="pn-menu-movil" aria-label="Abrir menú"><i class="ti-menu"></i></button>
			<h1><?php echo p_e($pn_titulo) ?></h1>
			<?php if ( ! empty($acciones)) { ?>
				<div class="pn-barra-acciones">
					<?php foreach ($acciones as $pn_accion) { ?>
						<a class="pn-boton <?php echo isset($pn_accion['clase']) ? $pn_accion['clase'] : '' ?>" href="<?php echo $pn_accion['url'] ?>">
							<?php if ( ! empty($pn_accion['icono'])) { ?><i class="<?php echo $pn_accion['icono'] ?>"></i><?php } ?>
							<?php echo p_e($pn_accion['texto']) ?>
						</a>
					<?php } ?>
				</div>
			<?php } ?>
		</header>

		<main class="pn-contenido">
			<?php if (is_array($pn_aviso) && ! empty($pn_aviso['texto'])) { ?>
				<div class="pn-aviso <?php echo $pn_aviso['tipo'] === 'error' ? 'error' : 'ok' ?>" role="status">
					<i class="<?php echo $pn_aviso['tipo'] === 'error' ? 'ti-alert' : 'ti-check' ?>"></i>
					<span><?php echo p_e($pn_aviso['texto']) ?></span>
				</div>
			<?php } ?>
			<?php echo $template['body'] ?>
		</main>
	</div>

	<div class="pn-tooltip" id="pn-tooltip"></div>
	<script src="<?php echo base_url(AADMINPATH.'js/panel.js?v=1') ?>"></script>
</body>
</html>
