<?php
// Panel de la sucursal. Datos que llegan del controlador (Sitio::_mostrar_inicio):
// $sucursal, $caja_cerrada_hoy, $ventas_por_medio, $ultimas_ventas

if (!function_exists('in_pesos'))
{
    function in_pesos($valor)
    {
        return '$ '.number_format((float)$valor, 0, ',', '.');
    }
}

$in_nombre = trim($sucursal->nombre.' '.$sucursal->apellido, ' .');
$in_lugar = trim($sucursal->localidad.($sucursal->domicilio !== '' ? ' · '.$sucursal->domicilio : ''), ' ·');

$in_hora = (int)date('G');
$in_saludo = $in_hora < 13 ? 'Buen día' : ($in_hora < 20 ? 'Buenas tardes' : 'Buenas noches');

$in_dias = array('domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado');
$in_meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
$in_fecha = ucfirst($in_dias[date('w')]).' '.date('j').' de '.$in_meses[date('n')];

// Totales del dia por medio de pago
$in_medios = array(
    'Efectivo'                  => array('icono' => 'ti-money',       'texto' => 'Efectivo'),
    'Tarjeta de crédito/débito' => array('icono' => 'ti-credit-card', 'texto' => 'Tarjeta'),
    'Medio electrónico'         => array('icono' => 'ti-mobile',      'texto' => 'Medio electrónico'),
    'Transferencia/QR'          => array('icono' => 'ti-exchange-vertical', 'texto' => 'Transferencia/QR'),
);
$in_por_medio = array();
$in_total = 0;
$in_cantidad = 0;
foreach ($ventas_por_medio as $fila)
{
    $in_por_medio[$fila->modo_de_pago] = $fila;
    $in_total += $fila->total;
    $in_cantidad += $fila->ventas;
}
?>

<style type="text/css">
.in {
    --in-verde: #47b475;
    --in-verde-oscuro: #3a9862;
    --in-verde-claro: #eaf6ef;
    --in-texto: #1f2933;
    --in-texto-suave: #6b7785;
    --in-borde: #e3e8ee;
    --in-fondo: #f5f7f9;
    --in-ambar: #b7790a;
    --in-ambar-claro: #fdf3dc;
    background: var(--in-fondo);
    padding: 32px 0 60px;
    color: var(--in-texto);
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
    min-height: calc(100vh - 64px);
}
.in * { box-sizing: border-box; }
/* El tema global agrega márgenes y mayúsculas a botones y títulos: se anulan acá. */
.in h1, .in h2, .in h3, .in p { margin: 0; letter-spacing: 0; text-transform: none; }
.in a { text-decoration: none; }

.in-cabecera { display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.in-fecha { font-size: 13px; font-weight: 600; color: var(--in-texto-suave); text-transform: uppercase; letter-spacing: .04em; }
.in-cabecera h1 { font-size: 26px; font-weight: 600; color: var(--in-texto); margin-top: 4px; }
.in-lugar { color: var(--in-texto-suave); font-size: 15px; margin-top: 4px; display: flex; align-items: center; gap: 6px; }
.in-estado { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; padding: 7px 14px; border-radius: 999px; background: var(--in-verde-claro); color: var(--in-verde-oscuro); }
.in-estado.cerrada { background: var(--in-ambar-claro); color: var(--in-ambar); }
.in-estado i { width: 8px; height: 8px; border-radius: 50%; background: currentColor; display: inline-block; }

/* Acciones principales */
.in-acciones { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.in-accion { display: flex; align-items: center; gap: 20px; padding: 26px 24px; border-radius: 14px; background: #fff; border: 1px solid var(--in-borde); box-shadow: 0 1px 2px rgba(16, 24, 40, .04); color: var(--in-texto); transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease; }
.in-accion:hover, .in-accion:focus { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16, 24, 40, .08); border-color: #cfd6de; color: var(--in-texto); }
.in-accion:focus-visible { outline: 3px solid rgba(71, 180, 117, .45); outline-offset: 2px; }
.in-accion-icono { width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 26px; flex: none; background: var(--in-fondo); color: var(--in-texto); }
.in-accion.principal { background: var(--in-verde); border-color: var(--in-verde); color: #fff; }
.in-accion.principal:hover, .in-accion.principal:focus { background: var(--in-verde-oscuro); border-color: var(--in-verde-oscuro); color: #fff; }
.in-accion.principal .in-accion-icono { background: rgba(255, 255, 255, .18); color: #fff; }
.in-accion-titulo { font-size: 20px; font-weight: 700; }
.in-accion-texto { font-size: 14px; opacity: .8; margin-top: 3px; }
.in-accion-flecha { margin-left: auto; font-size: 18px; opacity: .6; }
.in-accion.deshabilitada { pointer-events: none; opacity: .55; box-shadow: none; }

.in-aviso { display: flex; align-items: center; gap: 14px; padding: 14px 18px; border-radius: 12px; background: var(--in-ambar-claro); color: #7a5208; margin-bottom: 20px; font-size: 14px; flex-wrap: wrap; }
.in-aviso i { font-size: 18px; }
.in-aviso a { margin-left: auto; font-weight: 600; color: #7a5208; text-decoration: underline; }

/* Paneles */
.in-grid { display: grid; grid-template-columns: minmax(0, 3fr) minmax(0, 2fr); gap: 20px; }
.in-panel { background: #fff; border: 1px solid var(--in-borde); border-radius: 12px; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
.in-panel-cabecera { padding: 16px 20px; border-bottom: 1px solid var(--in-borde); display: flex; align-items: center; gap: 10px; }
.in-panel-cabecera i { color: var(--in-texto-suave); font-size: 16px; }
.in-panel-cabecera h2 { font-size: 16px; font-weight: 600; }
.in-panel-cabecera a { margin-left: auto; font-size: 13px; font-weight: 600; color: var(--in-verde-oscuro); }
.in-panel-cabecera a:hover { text-decoration: underline; }

.in-hoy { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid var(--in-borde); }
.in-hoy > div { padding: 18px 20px; }
.in-hoy > div + div { border-left: 1px solid var(--in-borde); }
.in-etiqueta { font-size: 12px; font-weight: 600; color: var(--in-texto-suave); text-transform: uppercase; letter-spacing: .04em; }
.in-valor { font-size: 26px; font-weight: 700; margin-top: 6px; font-variant-numeric: tabular-nums; white-space: nowrap; }

.in-medios { padding: 6px 20px 10px; }
.in-medio { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid #f0f2f5; font-size: 14px; }
.in-medio:last-child { border-bottom: 0; }
.in-medio i { width: 32px; height: 32px; border-radius: 8px; background: var(--in-fondo); display: flex; align-items: center; justify-content: center; color: var(--in-texto-suave); flex: none; }
.in-medio-nombre { flex: 1; min-width: 0; }
.in-medio-cantidad { color: var(--in-texto-suave); font-size: 13px; }
.in-medio-total { font-weight: 600; font-variant-numeric: tabular-nums; min-width: 90px; text-align: right; }
.in-medio.vacio { color: #a3adb8; }
.in-medio.vacio .in-medio-total { font-weight: 400; }

.in-ventas { padding: 4px 20px 8px; }
.in-venta { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f0f2f5; color: var(--in-texto); font-size: 14px; }
.in-venta:last-child { border-bottom: 0; }
.in-venta:hover { color: var(--in-verde-oscuro); }
.in-venta-hora { font-weight: 600; font-variant-numeric: tabular-nums; width: 48px; flex: none; }
.in-venta-medio { flex: 1; min-width: 0; color: var(--in-texto-suave); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.in-venta-total { font-weight: 600; font-variant-numeric: tabular-nums; }
.in-sin { padding: 34px 20px; text-align: center; color: var(--in-texto-suave); font-size: 14px; }
.in-sin i { display: block; font-size: 28px; margin-bottom: 8px; color: #c3cad3; }

@media (max-width: 991px) { .in-grid { grid-template-columns: 1fr; } }
@media (max-width: 767px) {
    .in { padding-top: 20px; }
    .in-acciones { grid-template-columns: 1fr; gap: 12px; }
    .in-accion { padding: 20px 18px; }
    .in-accion-icono { width: 50px; height: 50px; font-size: 22px; }
    .in-cabecera h1 { font-size: 22px; }
    .in-valor { font-size: 22px; }
    .in-panel-cabecera, .in-hoy > div, .in-medios, .in-ventas { padding-left: 14px; padding-right: 14px; }
}
</style>

<section class="in">
    <div class="container">

        <div class="in-cabecera">
            <div>
                <div class="in-fecha"><?php echo $in_fecha ?></div>
                <h1><?php echo $in_saludo.($in_nombre !== '' ? ', '.htmlspecialchars($in_nombre) : '') ?></h1>
                <?php if ($in_lugar !== '') { ?>
                    <div class="in-lugar"><i class="ti-location-pin"></i>Sucursal <?php echo htmlspecialchars($in_lugar) ?></div>
                <?php } ?>
            </div>
            <span class="in-estado<?php echo $caja_cerrada_hoy ? ' cerrada' : '' ?>"><i></i><?php echo $caja_cerrada_hoy ? 'Caja cerrada' : 'Caja abierta' ?></span>
        </div>

        <?php if ($caja_cerrada_hoy) { ?>
            <div class="in-aviso">
                <i class="ti-lock"></i>
                <span>La caja de hoy ya está cerrada. Vas a poder vender de nuevo cuando se reabra la caja.</span>
                <a href="<?php echo base_url() ?>caja_cerrada">Ver resumen del cierre</a>
            </div>
        <?php } ?>

        <div class="in-acciones">
            <a class="in-accion principal<?php echo $caja_cerrada_hoy ? ' deshabilitada' : '' ?>" href="<?php echo base_url() ?>nueva_venta"<?php echo $caja_cerrada_hoy ? ' aria-disabled="true" tabindex="-1"' : '' ?>>
                <span class="in-accion-icono"><i class="ti-shopping-cart"></i></span>
                <span>
                    <span class="in-accion-titulo">Nueva venta</span>
                    <span class="in-accion-texto" style="display:block">Cargar productos y cobrar</span>
                </span>
                <i class="ti-arrow-right in-accion-flecha"></i>
            </a>
            <?php if ($caja_cerrada_hoy) { ?>
                <a class="in-accion" href="<?php echo base_url() ?>caja_cerrada">
                    <span class="in-accion-icono"><i class="ti-check-box"></i></span>
                    <span>
                        <span class="in-accion-titulo">Caja cerrada</span>
                        <span class="in-accion-texto" style="display:block">Ver el resumen del día</span>
                    </span>
                    <i class="ti-arrow-right in-accion-flecha"></i>
                </a>
            <?php } else { ?>
                <a class="in-accion" href="<?php echo base_url() ?>cerrar_caja">
                    <span class="in-accion-icono"><i class="ti-lock"></i></span>
                    <span>
                        <span class="in-accion-titulo">Cerrar caja</span>
                        <span class="in-accion-texto" style="display:block">Declarar impresoras y cerrar el día</span>
                    </span>
                    <i class="ti-arrow-right in-accion-flecha"></i>
                </a>
            <?php } ?>
        </div>

        <div class="in-grid">

            <!-- Hoy -->
            <div class="in-panel">
                <div class="in-panel-cabecera">
                    <i class="ti-stats-up"></i>
                    <h2>Hoy</h2>
                </div>
                <div class="in-hoy">
                    <div>
                        <div class="in-etiqueta">Total vendido</div>
                        <div class="in-valor"><?php echo in_pesos($in_total) ?></div>
                    </div>
                    <div>
                        <div class="in-etiqueta">Ventas</div>
                        <div class="in-valor"><?php echo number_format($in_cantidad, 0, ',', '.') ?></div>
                    </div>
                </div>
                <div class="in-medios">
                    <?php foreach ($in_medios as $clave => $medio) {
                        $fila = isset($in_por_medio[$clave]) ? $in_por_medio[$clave] : null; ?>
                        <div class="in-medio<?php echo $fila ? '' : ' vacio' ?>">
                            <i class="<?php echo $medio['icono'] ?>"></i>
                            <span class="in-medio-nombre"><?php echo $medio['texto'] ?></span>
                            <span class="in-medio-cantidad"><?php echo $fila ? $fila->ventas.' venta'.($fila->ventas == 1 ? '' : 's') : '' ?></span>
                            <span class="in-medio-total"><?php echo $fila ? in_pesos($fila->total) : '—' ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Ultimas ventas -->
            <div class="in-panel">
                <div class="in-panel-cabecera">
                    <i class="ti-receipt"></i>
                    <h2>Últimas ventas</h2>
                    <a href="<?php echo base_url() ?>pedidos">Ver todas</a>
                </div>
                <?php if (empty($ultimas_ventas)) { ?>
                    <div class="in-sin"><i class="ti-shopping-cart"></i>Todavía no hay ventas hoy</div>
                <?php } else { ?>
                    <div class="in-ventas">
                        <?php foreach ($ultimas_ventas as $venta) { ?>
                            <a class="in-venta" href="<?php echo base_url() ?>pedido_detalles/<?php echo (int)$venta->idpedido ?>">
                                <span class="in-venta-hora"><?php echo date('H:i', strtotime($venta->fecha_hora)) ?></span>
                                <span class="in-venta-medio"><?php echo htmlspecialchars($venta->modo_de_pago) ?></span>
                                <span class="in-venta-total"><?php echo in_pesos($venta->total) ?></span>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
</section>
