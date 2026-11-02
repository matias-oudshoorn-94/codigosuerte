<?php
// Confirmacion de venta. Datos que llegan del controlador (Sitio::venta_generada):
// $pedido (idpedido, fecha_hora, modo_de_pago, total) y $items (cantidad, nombre, codigo, precio_final)

if (!function_exists('vg_pesos'))
{
    function vg_pesos($valor)
    {
        return '$ '.number_format((float)$valor, 0, ',', '.');
    }
}

$vg_unidades = 0;
foreach ($items as $item) $vg_unidades += $item->cantidad;
?>

<style type="text/css">
.vg {
    --vg-verde: #47b475;
    --vg-verde-oscuro: #3a9862;
    --vg-verde-claro: #eaf6ef;
    --vg-texto: #1f2933;
    --vg-texto-suave: #6b7785;
    --vg-borde: #e3e8ee;
    --vg-fondo: #f5f7f9;
    background: var(--vg-fondo);
    padding: 40px 0 60px;
    color: var(--vg-texto);
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
    min-height: calc(100vh - 64px);
}
.vg * { box-sizing: border-box; }
.vg h1, .vg h2, .vg p { margin: 0; letter-spacing: 0; text-transform: none; }
.vg a { text-decoration: none; }

.vg-tarjeta { max-width: 560px; margin: 0 auto; background: #fff; border: 1px solid var(--vg-borde); border-radius: 16px; box-shadow: 0 8px 24px rgba(16, 24, 40, .06); overflow: hidden; }

.vg-exito { text-align: center; padding: 32px 24px 24px; }
.vg-check { width: 72px; height: 72px; border-radius: 50%; background: var(--vg-verde); color: #fff; box-shadow: 0 0 0 8px var(--vg-verde-claro); display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 16px; animation: vg-aparecer .35s ease-out; }
@keyframes vg-aparecer { from { transform: scale(.6); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.vg-exito h1 { font-size: 24px; font-weight: 700; }
.vg-exito p { color: var(--vg-texto-suave); font-size: 14px; margin-top: 6px; }
.vg-total { font-size: 38px; font-weight: 700; margin-top: 18px; font-variant-numeric: tabular-nums; }
.vg-pago { display: inline-flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 13px; font-weight: 600; padding: 5px 12px; border-radius: 999px; background: var(--vg-fondo); color: var(--vg-texto-suave); }

.vg-detalle { border-top: 1px solid var(--vg-borde); padding: 8px 24px; }
.vg-detalle-titulo { display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; color: var(--vg-texto-suave); text-transform: uppercase; letter-spacing: .04em; padding: 12px 0 4px; }
.vg-item { display: flex; align-items: baseline; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f0f2f5; font-size: 14px; }
.vg-item:last-child { border-bottom: 0; }
.vg-item-cantidad { font-weight: 700; color: var(--vg-texto-suave); width: 36px; flex: none; font-variant-numeric: tabular-nums; }
.vg-item-nombre { flex: 1; min-width: 0; overflow-wrap: anywhere; }
.vg-item-subtotal { font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }

.vg-acciones { display: flex; gap: 12px; padding: 20px 24px 24px; border-top: 1px solid var(--vg-borde); background: #fbfcfd; }
.vg-boton { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 50px; border-radius: 10px; font-size: 15px; font-weight: 600; border: 1px solid var(--vg-borde); background: #fff; color: var(--vg-texto); transition: background .12s ease, border-color .12s ease; }
.vg-boton:hover, .vg-boton:focus { border-color: #cfd6de; background: var(--vg-fondo); color: var(--vg-texto); }
.vg-boton.principal { background: var(--vg-verde); border-color: var(--vg-verde); color: #fff; flex: 1.4; }
.vg-boton.principal:hover, .vg-boton.principal:focus { background: var(--vg-verde-oscuro); border-color: var(--vg-verde-oscuro); color: #fff; }
.vg-boton:focus-visible { outline: 3px solid rgba(71, 180, 117, .45); outline-offset: 2px; }
.vg-atajo { text-align: center; font-size: 12px; color: var(--vg-texto-suave); margin-top: 14px; }
.vg-atajo kbd { font-family: inherit; font-size: 11px; font-weight: 600; padding: 2px 6px; border: 1px solid var(--vg-borde); border-bottom-width: 2px; border-radius: 4px; background: #fff; color: var(--vg-texto); }

@media (max-width: 600px) {
    .vg { padding-top: 20px; }
    .vg-tarjeta { border-radius: 12px; }
    .vg-acciones { flex-direction: column; }
    .vg-boton, .vg-boton.principal { flex: none; }
    .vg-boton.principal { order: -1; }
    .vg-detalle, .vg-acciones { padding-left: 16px; padding-right: 16px; }
    .vg-total { font-size: 32px; }
    .vg-atajo { display: none; }
}
</style>

<section class="vg">
    <div class="container">
        <div class="vg-tarjeta">

            <div class="vg-exito">
                <div class="vg-check"><i class="ti-check"></i></div>
                <h1>Venta registrada</h1>
                <p>Venta N° <?php echo (int)$pedido->idpedido ?> · <?php echo date('d/m/Y H:i', strtotime($pedido->fecha_hora)) ?></p>
                <div class="vg-total"><?php echo vg_pesos($pedido->total) ?></div>
                <span class="vg-pago"><i class="ti-wallet"></i><?php echo htmlspecialchars($pedido->modo_de_pago) ?></span>
            </div>

            <?php if (!empty($items)) { ?>
                <div class="vg-detalle">
                    <div class="vg-detalle-titulo">
                        <span>Detalle</span>
                        <span><?php echo $vg_unidades ?> unidad<?php echo $vg_unidades == 1 ? '' : 'es' ?></span>
                    </div>
                    <?php foreach ($items as $item) { ?>
                        <div class="vg-item">
                            <span class="vg-item-cantidad"><?php echo (int)$item->cantidad ?>×</span>
                            <span class="vg-item-nombre"><?php echo htmlspecialchars($item->nombre !== null ? $item->nombre : 'Producto eliminado') ?></span>
                            <span class="vg-item-subtotal"><?php echo $item->precio_final !== null ? vg_pesos($item->precio_final * $item->cantidad) : '' ?></span>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="vg-acciones">
                <a class="vg-boton" href="<?php echo base_url() ?>pedidos"><i class="ti-receipt"></i>Ver ventas</a>
                <a class="vg-boton principal" id="vg-nueva" href="<?php echo base_url() ?>nueva_venta"><i class="ti-plus"></i>Nueva venta</a>
            </div>
        </div>
        <div class="vg-atajo">Presioná <kbd>Enter</kbd> para empezar una nueva venta</div>
    </div>
</section>

<script type="text/javascript">
// Foco en "Nueva venta" para seguir vendiendo solo con Enter
document.getElementById('vg-nueva').focus();
</script>
