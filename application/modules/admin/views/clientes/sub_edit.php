<?php
// Alta/edicion de impresora con su historial. Datos de Clientes::sub_edit().
$ultimos = array_reverse(array_slice($historial, 0, 60));
$total_historial = 0;
foreach ($historial as $h) $total_historial += $h->impresiones;
?>
<?php if ($errores) { ?>
	<div class="pn-aviso error" role="alert"><i class="ti-alert"></i><span>Revisá los campos marcados.</span></div>
<?php } ?>

<div class="pn-grilla">
	<form method="post" class="pn-tarjeta c-5">
		<div class="pn-tarjeta-cabecera"><h2>Datos de la impresora</h2><span class="pn-extra"><?php echo p_e(p_nombre_sucursal($sucursal)) ?></span></div>
		<input type="hidden" name="sucursal" value="<?php echo (int) $sucursal->idusuario ?>">
		<div class="pn-tarjeta-cuerpo pn-campos" style="grid-template-columns:1fr">
			<?php echo p_campo('codigo', 'Código', $valores, $errores, array('requerido' => TRUE, 'attrs' => 'maxlength="200"', 'ayuda' => 'Es el que la sucursal ve al declarar la impresora.')) ?>
			<?php echo p_campo('copias', 'Contador actual', $valores, $errores, array('tipo' => 'number', 'attrs' => 'min="0" step="1"', 'ayuda' => $id ? 'Se actualiza solo con cada cierre de caja. Cambialo solo si se reemplazó o reinició la impresora.' : 'El número que marca hoy el contador de la impresora.')) ?>
			<?php echo p_campo('fecha', 'Fecha de alta', $valores, $errores, array('tipo' => 'date')) ?>
		</div>
		<div class="pn-tarjeta-pie">
			<button type="submit" class="pn-boton principal"><i class="ti-check"></i><?php echo $id ? 'Guardar' : 'Agregar impresora' ?></button>
			<a class="pn-boton" href="<?php echo site_url('admin/clientes/sub_index/'.(int) $sucursal->idusuario) ?>">Cancelar</a>
		</div>
	</form>

	<?php if ($id) { ?>
		<section class="pn-tarjeta c-7">
			<div class="pn-tarjeta-cabecera">
				<h2>Historial de impresiones</h2>
				<span class="pn-extra"><?php echo p_numero(count($historial)) ?> declaraciones · <?php echo p_numero($total_historial) ?> impresiones</span>
			</div>
			<?php if (empty($historial)) { ?>
				<div class="pn-vacio"><i class="ti-printer"></i>Esta impresora todavía no se declaró en ningún cierre.</div>
			<?php } else { ?>
				<div class="pn-tarjeta-cuerpo">
					<p class="suave" style="font-size:13px;margin-bottom:10px">Impresiones por declaración<?php echo count($historial) > 60 ? ' (últimas 60)' : '' ?></p>
					<div class="pn-grafico bajo"><canvas id="grafico-impresora" role="img" aria-label="Impresiones por declaración"></canvas></div>
					<details class="pn-ver-tabla">
						<summary>Ver todas las declaraciones</summary>
						<div class="pn-tabla-contenedor" style="max-height:360px;overflow-y:auto">
							<table class="pn-tabla">
								<thead><tr><th>Fecha</th><th class="num">Impresiones</th></tr></thead>
								<tbody>
									<?php foreach ($historial as $h) { ?>
										<tr><td><?php echo p_fecha($h->fecha) ?></td><td class="num"><?php echo p_numero($h->impresiones) ?></td></tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</details>
				</div>
				<script>
				PanelGraficos.barras('grafico-impresora',
					<?php echo json_encode(array_map(function ($h) { return date('j/n/y', strtotime($h->fecha)); }, $ultimos)) ?>,
					[{ nombre: 'Impresiones', datos: <?php echo json_encode(array_map(function ($h) { return (int) $h->impresiones; }, $ultimos)) ?> }],
					{ formato: 'numero' });
				</script>
			<?php } ?>
		</section>
	<?php } ?>
</div>
