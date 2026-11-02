<?php // Impresoras de una sucursal. Datos de Clientes::sub_index(). ?>
<p class="suave" style="margin:-8px 0 20px">Las impresoras se declaran al cerrar caja: la sucursal carga el contador y el sistema calcula cuántas impresiones se hicieron en el día.</p>

<section class="pn-tarjeta">
	<?php if (empty($impresoras)) { ?>
		<div class="pn-vacio"><i class="ti-printer"></i>Esta sucursal no tiene impresoras cargadas.</div>
	<?php } else { ?>
		<div class="pn-tabla-contenedor">
			<table class="pn-tabla">
				<thead><tr><th>Código</th><th class="num">Contador actual</th><th>Alta</th><th>Última declaración</th><th class="compacto"><span class="sr">Acciones</span></th></tr></thead>
				<tbody>
					<?php foreach ($impresoras as $i) { ?>
						<tr>
							<td><a class="principal-celda" href="<?php echo site_url('admin/clientes/sub_edit/'.(int) $i->id) ?>"><?php echo p_e($i->codigo) ?></a></td>
							<td class="num"><b><?php echo p_numero($i->copias) ?></b></td>
							<td class="suave"><?php echo p_fecha($i->fecha) ?></td>
							<td><?php echo $i->ultima_declaracion ? p_fecha($i->ultima_declaracion) : '<span class="tenue">Nunca</span>' ?></td>
							<td class="acciones">
								<a class="pn-boton chico" href="<?php echo site_url('admin/clientes/sub_edit/'.(int) $i->id) ?>">Editar e historial</a>
								<form method="post" action="<?php echo site_url('admin/clientes/sub_eliminar') ?>" class="en-linea" data-confirmar="¿Eliminar la impresora <?php echo p_e($i->codigo) ?>? Su historial de declaraciones se conserva.">
									<input type="hidden" name="id" value="<?php echo (int) $i->id ?>">
									<button type="submit" class="pn-boton chico icono peligro" title="Eliminar" aria-label="Eliminar"><i class="ti-trash"></i></button>
								</form>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	<?php } ?>
</section>
