<?php
// Paginacion. Recibe $pag (de MY_Controller::paginacion) y conserva los filtros del GET.
if ($pag['paginas'] <= 1 && $pag['total'] <= $pag['por_pagina'])
{
	echo '<div class="pn-paginas"><span>'.p_numero($pag['total']).' resultado'.($pag['total'] == 1 ? '' : 's').'</span></div>';
	return;
}
$pn_query = $_GET;
$pn_url = function ($pagina) use ($pn_query) {
	$pn_query['pagina'] = $pagina;
	return '?'.http_build_query($pn_query);
};
$pn_desde = $pag['offset'] + 1;
$pn_hasta = min($pag['total'], $pag['offset'] + $pag['por_pagina']);
?>
<div class="pn-paginas">
	<span><?php echo p_numero($pn_desde) ?>–<?php echo p_numero($pn_hasta) ?> de <?php echo p_numero($pag['total']) ?></span>
	<div class="pn-paginas-botones">
		<?php if ($pag['pagina'] > 1) { ?>
			<a class="pn-boton chico" href="<?php echo p_e($pn_url(1)) ?>" aria-label="Primera página"><i class="ti-angle-double-left"></i></a>
			<a class="pn-boton chico" href="<?php echo p_e($pn_url($pag['pagina'] - 1)) ?>"><i class="ti-angle-left"></i>Anterior</a>
		<?php } ?>
		<span class="pn-boton chico fantasma" aria-current="page">Página <?php echo $pag['pagina'] ?> de <?php echo $pag['paginas'] ?></span>
		<?php if ($pag['pagina'] < $pag['paginas']) { ?>
			<a class="pn-boton chico" href="<?php echo p_e($pn_url($pag['pagina'] + 1)) ?>">Siguiente<i class="ti-angle-right"></i></a>
			<a class="pn-boton chico" href="<?php echo p_e($pn_url($pag['paginas'])) ?>" aria-label="Última página"><i class="ti-angle-double-right"></i></a>
		<?php } ?>
	</div>
</div>
