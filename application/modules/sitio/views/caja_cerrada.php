<?php
// Resumen de la caja cerrada. Datos que llegan del controlador (Sitio::caja_cerrada):
// $caja (fila de cajas_diarias del ultimo cierre, puede ser null)

if (!function_exists('cz_pesos'))
{
    function cz_pesos($valor)
    {
        return '$ '.number_format((float)$valor, 0, ',', '.');
    }
}

$cz_dias = array('domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado');
$cz_meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
$cz_fecha = '';
if ($caja)
{
    $t = strtotime($caja->fecha);
    $cz_fecha = $cz_dias[date('w', $t)].' '.date('j', $t).' de '.$cz_meses[date('n', $t)].' de '.date('Y', $t);
}

$cz_medios = $caja ? array(
    array('icono' => 'ti-money',             'texto' => 'Efectivo',          'valor' => $caja->total_efectivo),
    array('icono' => 'ti-credit-card',       'texto' => 'Tarjeta',           'valor' => $caja->total_tarjeta),
    array('icono' => 'ti-mobile',            'texto' => 'Medio electrónico', 'valor' => $caja->total_medio_electronico),
    array('icono' => 'ti-exchange-vertical', 'texto' => 'Transferencia/QR',  'valor' => $caja->total_transferencia),
) : array();
?>

<style type="text/css">
.cz {
    --cz-verde: #47b475;
    --cz-verde-oscuro: #3a9862;
    --cz-verde-claro: #eaf6ef;
    --cz-texto: #1f2933;
    --cz-texto-suave: #6b7785;
    --cz-borde: #e3e8ee;
    --cz-fondo: #f5f7f9;
    --cz-ambar: #b7790a;
    background: var(--cz-fondo);
    padding: 40px 0 60px;
    color: var(--cz-texto);
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
    min-height: calc(100vh - 64px);
}
.cz * { box-sizing: border-box; }
.cz h1, .cz h2, .cz p { margin: 0; letter-spacing: 0; text-transform: none; }
.cz a { text-decoration: none; }

.cz-tarjeta { max-width: 640px; margin: 0 auto; background: #fff; border: 1px solid var(--cz-borde); border-radius: 16px; box-shadow: 0 8px 24px rgba(16, 24, 40, .06); overflow: hidden; }

.cz-encabezado { text-align: center; padding: 32px 24px 26px; }
.cz-candado { width: 72px; height: 72px; border-radius: 50%; background: var(--cz-verde-claro); color: var(--cz-verde); display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 16px; }
.cz-encabezado h1 { font-size: 24px; font-weight: 700; }
.cz-encabezado p { color: var(--cz-texto-suave); font-size: 14px; margin-top: 6px; }

.cz-total { border-top: 1px solid var(--cz-borde); display: grid; grid-template-columns: 1fr 1fr; }
.cz-total > div { padding: 18px 24px; }
.cz-total > div + div { border-left: 1px solid var(--cz-borde); }
.cz-etiqueta { font-size: 12px; font-weight: 600; color: var(--cz-texto-suave); text-transform: uppercase; letter-spacing: .04em; }
.cz-valor { font-size: 26px; font-weight: 700; margin-top: 6px; font-variant-numeric: tabular-nums; white-space: nowrap; }

.cz-seccion { border-top: 1px solid var(--cz-borde); padding: 8px 24px 10px; }
.cz-seccion-titulo { font-size: 12px; font-weight: 600; color: var(--cz-texto-suave); text-transform: uppercase; letter-spacing: .04em; padding: 12px 0 2px; }
.cz-fila { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid #f0f2f5; font-size: 14px; }
.cz-fila:last-child { border-bottom: 0; }
.cz-fila i { width: 32px; height: 32px; border-radius: 8px; background: var(--cz-fondo); display: flex; align-items: center; justify-content: center; color: var(--cz-texto-suave); flex: none; }
.cz-fila span:first-of-type { flex: 1; }
.cz-fila b { font-weight: 600; font-variant-numeric: tabular-nums; }
.cz-fila.vacio { color: #a3adb8; }
.cz-fila.vacio b { font-weight: 400; }

.cz-nota { display: flex; gap: 12px; align-items: flex-start; margin: 0 24px 22px; padding: 14px 16px; border-radius: 10px; background: var(--cz-fondo); color: var(--cz-texto-suave); font-size: 13px; line-height: 1.5; }
.cz-nota i { font-size: 16px; margin-top: 1px; }

.cz-acciones { display: flex; gap: 12px; padding: 20px 24px 24px; border-top: 1px solid var(--cz-borde); background: #fbfcfd; }
.cz-boton { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 50px; border-radius: 10px; font-size: 15px; font-weight: 600; border: 1px solid var(--cz-borde); background: #fff; color: var(--cz-texto); transition: background .12s ease, border-color .12s ease; }
.cz-boton:hover, .cz-boton:focus { border-color: #cfd6de; background: var(--cz-fondo); color: var(--cz-texto); }
.cz-boton.principal { background: var(--cz-verde); border-color: var(--cz-verde); color: #fff; }
.cz-boton.principal:hover, .cz-boton.principal:focus { background: var(--cz-verde-oscuro); border-color: var(--cz-verde-oscuro); color: #fff; }
.cz-boton:focus-visible { outline: 3px solid rgba(71, 180, 117, .45); outline-offset: 2px; }

@media (max-width: 600px) {
    .cz { padding-top: 20px; }
    .cz-tarjeta { border-radius: 12px; }
    .cz-total > div, .cz-seccion, .cz-acciones { padding-left: 16px; padding-right: 16px; }
    .cz-nota { margin-left: 16px; margin-right: 16px; }
    .cz-valor { font-size: 22px; }
    .cz-acciones { flex-direction: column; }
    .cz-boton { flex: none; }
}
</style>

<section class="cz">
    <div class="container">
        <div class="cz-tarjeta">

            <div class="cz-encabezado">
                <div class="cz-candado"><i class="ti-lock"></i></div>
                <h1>Caja cerrada</h1>
                <p><?php echo $caja ? 'Resumen del '.$cz_fecha : 'La caja del día se cerró correctamente.' ?></p>
            </div>

            <?php if ($caja) { ?>
                <div class="cz-total">
                    <div>
                        <div class="cz-etiqueta">Total del día</div>
                        <div class="cz-valor"><?php echo cz_pesos($caja->total) ?></div>
                    </div>
                    <div>
                        <div class="cz-etiqueta">Ventas</div>
                        <div class="cz-valor"><?php echo number_format($caja->cantidad_ventas, 0, ',', '.') ?></div>
                    </div>
                </div>

                <div class="cz-seccion">
                    <div class="cz-seccion-titulo">Por medio de pago</div>
                    <?php foreach ($cz_medios as $medio) { $vacio = (float)$medio['valor'] == 0; ?>
                        <div class="cz-fila<?php echo $vacio ? ' vacio' : '' ?>">
                            <i class="<?php echo $medio['icono'] ?>"></i>
                            <span><?php echo $medio['texto'] ?></span>
                            <b><?php echo $vacio ? '—' : cz_pesos($medio['valor']) ?></b>
                        </div>
                    <?php } ?>
                </div>

                <div class="cz-seccion">
                    <div class="cz-seccion-titulo">Impresiones</div>
                    <div class="cz-fila">
                        <i class="ti-printer"></i>
                        <span>Declaradas en impresoras</span>
                        <b><?php echo number_format($caja->impresiones_reales, 0, ',', '.') ?></b>
                    </div>
                    <div class="cz-fila">
                        <i class="ti-shopping-cart"></i>
                        <span>Vendidas</span>
                        <b><?php echo number_format($caja->impresiones_aprox, 0, ',', '.') ?></b>
                    </div>
                    <div class="cz-fila">
                        <i class="ti-trash"></i>
                        <span>Desperdicio</span>
                        <b><?php echo number_format($caja->desperdicio, 1, ',', '.') ?>%</b>
                    </div>
                </div>
            <?php } ?>

            <div class="cz-nota">
                <i class="ti-info-alt"></i>
                <span>Solo se puede cerrar una caja por día. Las ventas se habilitan otra vez cuando se reabre la caja al día siguiente.</span>
            </div>

            <div class="cz-acciones">
                <a class="cz-boton" href="<?php echo base_url() ?>pedidos"><i class="ti-receipt"></i>Ver ventas</a>
                <a class="cz-boton principal" href="<?php echo base_url() ?>inicio"><i class="ti-home"></i>Ir al inicio</a>
            </div>
        </div>
    </div>
</section>
