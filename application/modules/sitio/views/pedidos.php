<?php
$base_url = base_url()."application/views/layouts";

if (!isset($this->session->userdata("user_data")[0]->idusuario))
{
    redirect('login');
}

// Ventas del día, de la más reciente a la más antigua
$ventas = $rows_pedidos;
usort($ventas, function ($a, $b) {
    return strcmp($b->fecha_hora, $a->fecha_hora) ?: ($b->idpedido - $a->idpedido);
});

// Productos de todas las ventas del día en una sola consulta
$detalles = array();
$productos_vendidos = array();
$unidades_total = 0;
$ids_pedidos = array();
foreach ($ventas as $venta) $ids_pedidos[] = (int)$venta->idpedido;

if (count($ids_pedidos) > 0)
{
    $filas = $this->db->select('pedidosdetalles.idpedido, pedidosdetalles.id_articu, pedidosdetalles.cantidad, productos.nombre, productos.codigo')
        ->from('pedidosdetalles')
        ->join('productos', 'productos.id = pedidosdetalles.id_articu', 'left')
        ->where_in('pedidosdetalles.idpedido', $ids_pedidos)
        ->order_by('pedidosdetalles.iddetalle', 'asc')
        ->get()->result();

    foreach ($filas as $fila)
    {
        $detalles[$fila->idpedido][] = $fila;
        $unidades_total += $fila->cantidad;

        if (!isset($productos_vendidos[$fila->id_articu]))
        {
            $productos_vendidos[$fila->id_articu] = array('nombre' => $fila->nombre ? $fila->nombre : 'Producto #'.$fila->id_articu, 'unidades' => 0, 'ventas' => 0);
        }
        $productos_vendidos[$fila->id_articu]['unidades'] += $fila->cantidad;
        $productos_vendidos[$fila->id_articu]['ventas']++;
    }
}

// Más vendidos: los que aparecen en más ventas
uasort($productos_vendidos, function ($a, $b) {
    return ($b['ventas'] - $a['ventas']) ?: ($b['unidades'] - $a['unidades']);
});
$mas_vendidos = array_slice($productos_vendidos, 0, 5, true);

// Totales por medio de pago
$medios = array(
    'Efectivo'                  => array('titulo' => 'Efectivo',          'icono' => 'ti-money',        'color' => '#47b475', 'total' => 0, 'ventas' => 0),
    'Tarjeta de crédito/débito' => array('titulo' => 'Tarjeta',           'icono' => 'ti-credit-card',  'color' => '#3b82c4', 'total' => 0, 'ventas' => 0),
    'Medio electrónico'         => array('titulo' => 'Medio electrónico', 'icono' => 'ti-mobile',       'color' => '#e0a43a', 'total' => 0, 'ventas' => 0),
    'Transferencia/QR'          => array('titulo' => 'Transferencia/QR',  'icono' => 'ti-layout-grid2', 'color' => '#8b6cc4', 'total' => 0, 'ventas' => 0),
);
$total_dia = 0;
foreach ($ventas as $venta)
{
    $total_dia += $venta->total;
    if (isset($medios[$venta->modo_de_pago]))
    {
        $medios[$venta->modo_de_pago]['total'] += $venta->total;
        $medios[$venta->modo_de_pago]['ventas']++;
    }
}
$cantidad_ventas = count($ventas);
$ticket_promedio = $cantidad_ventas > 0 ? $total_dia / $cantidad_ventas : 0;
// Medio de pago más usado (por monto)
$medio_principal = null;
foreach ($medios as $medio)
{
    if ($medio['total'] > 0 && ($medio_principal === null || $medio['total'] > $medio_principal['total'])) $medio_principal = $medio;
}
$ultima_venta = $cantidad_ventas > 0 ? date('H:i', strtotime($ventas[0]->fecha_hora)) : '—';

function pd_pesos($valor, $decimales = 2)
{
    return '$ '.number_format($valor, $decimales, ',', '.');
}
function pd_numero($valor)
{
    return number_format($valor, 0, ',', '.');
}

$dias  = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
$meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
$fecha_hoy = $dias[date('w')].' '.date('j').' de '.$meses[date('n')];
?>

<style type="text/css">
.pd {
    --pd-verde: #47b475;
    --pd-verde-oscuro: #3a9862;
    --pd-verde-claro: #eaf6ef;
    --pd-texto: #1f2933;
    --pd-texto-suave: #6b7785;
    --pd-borde: #e3e8ee;
    --pd-fondo: #f5f7f9;
    background: var(--pd-fondo);
    padding: 32px 0 60px;
    color: var(--pd-texto);
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.pd * { box-sizing: border-box; }
/* El tema global agrega márgenes y mayúsculas a botones, campos y títulos: se anulan acá. */
.pd button, .pd input, .pd h1, .pd h2, .pd p, .pd ul { margin-top: 0; margin-bottom: 0; }
.pd button { font-family: inherit; line-height: 1.2; letter-spacing: 0; text-transform: none; }
.pd input::-webkit-input-placeholder { text-transform: none; font-weight: 400; letter-spacing: 0; font-size: 14px; color: #8a95a1; }
.pd input::-moz-placeholder { text-transform: none; font-weight: 400; letter-spacing: 0; font-size: 14px; color: #8a95a1; }

.pd-encabezado { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.pd-encabezado h1 { font-size: 26px; font-weight: 600; color: var(--pd-texto); letter-spacing: 0; text-transform: none; }
.pd-fecha { color: var(--pd-texto-suave); font-size: 15px; margin-top: 4px; }
.pd-acciones { display: flex; gap: 8px; flex-wrap: wrap; }
.pd-boton { display: inline-flex; align-items: center; gap: 8px; height: 42px; padding: 0 16px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; border: 1px solid var(--pd-borde); background: #fff; color: var(--pd-texto); }
.pd-boton:hover { border-color: #b8c2cc; color: var(--pd-texto); text-decoration: none; }
.pd-boton.principal { background: var(--pd-verde); border-color: var(--pd-verde); color: #fff; }
.pd-boton.principal:hover { background: var(--pd-verde-oscuro); color: #fff; }

.pd-panel { background: #fff; border: 1px solid var(--pd-borde); border-radius: 12px; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
.pd-panel-cabecera { padding: 16px 20px; border-bottom: 1px solid var(--pd-borde); display: flex; align-items: center; justify-content: space-between; gap: 10px; }
.pd-panel-cabecera h2 { font-size: 16px; font-weight: 600; color: var(--pd-texto); text-transform: none; letter-spacing: 0; }
.pd-etiqueta { font-size: 12px; font-weight: 600; color: var(--pd-texto-suave); text-transform: uppercase; letter-spacing: .04em; }
.pd-chip { font-size: 12px; font-weight: 600; background: var(--pd-verde-claro); color: var(--pd-verde-oscuro); padding: 3px 10px; border-radius: 999px; white-space: nowrap; }

/* Indicadores */
.pd-kpis { display: grid; grid-template-columns: 1.4fr repeat(4, 1fr); gap: 1px; background: var(--pd-borde); border: 1px solid var(--pd-borde); border-radius: 12px; overflow: hidden; margin-bottom: 20px; }
.pd-kpi { background: #fff; padding: 16px 20px; min-width: 0; }
.pd-kpi-valor { font-size: 22px; font-weight: 600; margin-top: 6px; line-height: 1.3; font-variant-numeric: tabular-nums; white-space: nowrap; }
.pd-kpi-valor small { font-size: 13px; font-weight: 400; color: var(--pd-texto-suave); }
.pd-kpi.destacado .pd-kpi-valor { font-size: 30px; font-weight: 700; }
@media (max-width: 991px) { .pd-kpis { grid-template-columns: repeat(2, 1fr); } .pd-kpi.destacado { grid-column: 1 / -1; } }

.pd-fila { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr); gap: 20px; margin-bottom: 20px; }
@media (max-width: 991px) { .pd-fila { grid-template-columns: 1fr; } }
.pd-cuerpo { padding: 18px 20px; }

/* Medios de pago */
.pd-medios { display: flex; flex-direction: column; gap: 14px; }
.pd-medio { display: grid; grid-template-columns: 36px minmax(0, 1fr) auto; gap: 12px; align-items: center; }
.pd-medio-icono { width: 36px; height: 36px; border-radius: 10px; background: var(--pd-fondo); display: flex; align-items: center; justify-content: center; font-size: 16px; }
.pd-medio-nombre { font-size: 14px; font-weight: 600; display: flex; justify-content: space-between; gap: 8px; }
.pd-medio-nombre span { font-weight: 400; color: var(--pd-texto-suave); font-size: 12px; white-space: nowrap; }
.pd-barra { height: 6px; background: #eef1f4; border-radius: 999px; margin-top: 6px; overflow: hidden; }
.pd-barra div { height: 100%; border-radius: 999px; }
.pd-medio-monto { font-size: 15px; font-weight: 600; text-align: right; font-variant-numeric: tabular-nums; min-width: 110px; }
.pd-medio.cero { opacity: .5; }

/* Más vendidos */
.pd-top { list-style: none; padding: 0; }
.pd-top li { display: grid; grid-template-columns: 24px minmax(0, 1fr) auto; gap: 10px; align-items: center; padding: 9px 0; border-bottom: 1px solid #f0f2f5; font-size: 14px; }
.pd-top li:last-child { border-bottom: 0; }
.pd-top-pos { width: 24px; height: 24px; border-radius: 50%; background: var(--pd-fondo); color: var(--pd-texto-suave); font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; }
.pd-top-nombre { font-weight: 600; text-transform: uppercase; font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.pd-top-dato { font-size: 12px; color: var(--pd-texto-suave); text-align: right; white-space: nowrap; }
.pd-top-dato b { color: var(--pd-texto); font-size: 13px; }

/* Lista de ventas */
.pd-filtros { display: flex; gap: 12px; padding: 14px 20px; border-bottom: 1px solid var(--pd-borde); flex-wrap: wrap; align-items: center; }
.pd-chips { display: flex; gap: 6px; flex-wrap: wrap; }
.pd-filtro { height: 34px; padding: 0 12px; border-radius: 999px; border: 1px solid var(--pd-borde); background: #fff; color: var(--pd-texto); font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
.pd-filtro:hover { border-color: #b8c2cc; }
.pd-filtro.activo { background: var(--pd-texto); border-color: var(--pd-texto); color: #fff; }
.pd-filtro small { font-weight: 400; opacity: .7; font-size: 12px; }
.pd-buscar { position: relative; flex: 1 1 220px; max-width: 320px; margin-left: auto; }
.pd-buscar i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--pd-texto-suave); pointer-events: none; }
.pd .pd-buscar input { width: 100%; height: 36px; padding: 0 12px 0 34px; border: 1px solid var(--pd-borde); border-radius: 10px; background: var(--pd-fondo); font-size: 14px; color: var(--pd-texto); }
.pd .pd-buscar input:focus { outline: none; background: #fff; border-color: var(--pd-verde); box-shadow: 0 0 0 3px rgba(71, 180, 117, .18); }
@media (max-width: 767px) { .pd-buscar { max-width: none; margin-left: 0; flex-basis: 100%; } }

.pd-venta { border-bottom: 1px solid #f0f2f5; }
.pd-venta:last-child { border-bottom: 0; }
.pd-venta summary {
    list-style: none; cursor: pointer; display: grid; grid-template-columns: 56px 90px minmax(0, 1fr) auto auto 20px; gap: 14px; align-items: center;
    padding: 13px 20px; font-size: 14px;
}
.pd-venta summary::-webkit-details-marker { display: none; }
.pd-venta summary:hover { background: #fafbfc; }
.pd-venta[open] summary { background: var(--pd-verde-claro); }
.pd-hora { font-weight: 600; font-variant-numeric: tabular-nums; }
.pd-numero { color: var(--pd-texto-suave); font-variant-numeric: tabular-nums; }
.pd-resumen-items { color: var(--pd-texto-suave); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.pd-medio-tag { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 999px; background: var(--pd-fondo); white-space: nowrap; }
.pd-medio-tag i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
.pd-total-venta { font-weight: 700; text-align: right; font-variant-numeric: tabular-nums; min-width: 100px; }
.pd-flecha { color: var(--pd-texto-suave); transition: transform .15s; font-size: 12px; }
.pd-venta[open] .pd-flecha { transform: rotate(90deg); }
.pd-items { padding: 4px 20px 16px 180px; }
.pd-item { display: grid; grid-template-columns: 60px minmax(0, 1fr) auto; gap: 10px; padding: 7px 0; font-size: 14px; border-bottom: 1px dashed #edf0f3; }
.pd-item:last-child { border-bottom: 0; }
.pd-item-cantidad { font-weight: 600; font-variant-numeric: tabular-nums; }
.pd-item-nombre { text-transform: uppercase; font-size: 13px; font-weight: 600; }
.pd-item-codigo { color: var(--pd-texto-suave); font-size: 12px; font-variant-numeric: tabular-nums; }
.pd-items-pie { margin-top: 8px; font-size: 13px; }
.pd-items-pie a { color: var(--pd-verde-oscuro); font-weight: 600; }

.pd-vacio { padding: 48px 20px; text-align: center; color: var(--pd-texto-suave); font-size: 14px; }
.pd-vacio i { display: block; font-size: 30px; margin-bottom: 10px; opacity: .5; }

@media (max-width: 767px) {
    .pd { padding-top: 20px; }
    .pd-venta summary { grid-template-columns: 48px minmax(0, 1fr) auto 14px; gap: 10px; padding: 12px 14px; }
    .pd-numero, .pd-resumen-items { display: none; }
    .pd-medio-tag { grid-column: 2; grid-row: 1; justify-self: start; }
    .pd-items { padding: 4px 14px 14px 14px; }
    .pd-cuerpo, .pd-panel-cabecera, .pd-filtros { padding-left: 14px; padding-right: 14px; }
    .pd-kpi { padding: 14px; }
    .pd-medio-monto { min-width: 0; }
}
</style>

<section class="pd">
    <div class="container">

        <div class="pd-encabezado">
            <div>
                <h1>Ventas de hoy</h1>
                <div class="pd-fecha"><?php echo $fecha_hoy ?></div>
            </div>
            <div class="pd-acciones">
                <a class="pd-boton" href="<?php echo base_url() ?>nueva_venta"><i class="ti-plus"></i> Nueva venta</a>
                <a class="pd-boton principal" href="<?php echo base_url() ?>cerrar_caja"><i class="ti-lock"></i> Cerrar caja</a>
            </div>
        </div>

        <!-- Indicadores -->
        <div class="pd-kpis">
            <div class="pd-kpi destacado">
                <div class="pd-etiqueta">Total del día</div>
                <div class="pd-kpi-valor"><?php echo pd_pesos($total_dia) ?></div>
            </div>
            <div class="pd-kpi">
                <div class="pd-etiqueta">Ventas</div>
                <div class="pd-kpi-valor"><?php echo pd_numero($cantidad_ventas) ?></div>
            </div>
            <div class="pd-kpi">
                <div class="pd-etiqueta">Ticket promedio</div>
                <div class="pd-kpi-valor"><?php echo pd_pesos($ticket_promedio, 0) ?></div>
            </div>
            <div class="pd-kpi">
                <div class="pd-etiqueta">Medio más usado</div>
                <div class="pd-kpi-valor">
                    <?php if ($medio_principal) { ?>
                        <?php echo $medio_principal['titulo'] == 'Transferencia/QR' ? 'Transf./QR' : $medio_principal['titulo'] ?>
                        <small><?php echo round($medio_principal['total'] * 100 / $total_dia) ?>%</small>
                    <?php } else { ?>—<?php } ?>
                </div>
            </div>
            <div class="pd-kpi">
                <div class="pd-etiqueta">Última venta</div>
                <div class="pd-kpi-valor"><?php echo $ultima_venta ?></div>
            </div>
        </div>

        <div class="pd-fila">
            <!-- Medios de pago -->
            <div class="pd-panel">
                <div class="pd-panel-cabecera"><h2>Medios de pago</h2></div>
                <div class="pd-cuerpo">
                    <div class="pd-medios">
                        <?php foreach ($medios as $medio) {
                            $porcentaje = $total_dia > 0 ? round($medio['total'] * 100 / $total_dia) : 0; ?>
                            <div class="pd-medio<?php echo $medio['total'] == 0 ? ' cero' : '' ?>">
                                <div class="pd-medio-icono" style="color: <?php echo $medio['color'] ?>"><i class="<?php echo $medio['icono'] ?>"></i></div>
                                <div>
                                    <div class="pd-medio-nombre">
                                        <?php echo $medio['titulo'] ?>
                                        <span><?php echo $medio['ventas'] ?> venta<?php echo $medio['ventas'] == 1 ? '' : 's' ?> · <?php echo $porcentaje ?>%</span>
                                    </div>
                                    <div class="pd-barra"><div style="width: <?php echo $porcentaje ?>%; background: <?php echo $medio['color'] ?>"></div></div>
                                </div>
                                <div class="pd-medio-monto"><?php echo pd_pesos($medio['total']) ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Más vendidos -->
            <div class="pd-panel">
                <div class="pd-panel-cabecera"><h2>Más vendidos hoy</h2></div>
                <div class="pd-cuerpo">
                    <?php if (count($mas_vendidos) == 0) { ?>
                        <div class="pd-vacio" style="padding: 20px 0;">Sin productos vendidos todavía.</div>
                    <?php } else { ?>
                        <ul class="pd-top">
                            <?php $posicion = 0; foreach ($mas_vendidos as $producto) { $posicion++; ?>
                                <li>
                                    <span class="pd-top-pos"><?php echo $posicion ?></span>
                                    <span class="pd-top-nombre" title="<?php echo htmlspecialchars($producto['nombre']) ?>"><?php echo htmlspecialchars($producto['nombre']) ?></span>
                                    <span class="pd-top-dato"><b><?php echo $producto['ventas'] ?></b> venta<?php echo $producto['ventas'] == 1 ? '' : 's' ?> · <?php echo pd_numero($producto['unidades']) ?> u.</span>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Lista de ventas -->
        <div class="pd-panel">
            <div class="pd-panel-cabecera">
                <h2>Detalle de ventas</h2>
                <span class="pd-chip" id="pd-contador"><?php echo $cantidad_ventas ?> de <?php echo $cantidad_ventas ?></span>
            </div>

            <?php if ($cantidad_ventas == 0) { ?>
                <div class="pd-vacio"><i class="ti-receipt"></i>Todavía no hay ventas hoy.<br><a href="<?php echo base_url() ?>nueva_venta">Cargar una venta</a></div>
            <?php } else { ?>
                <div class="pd-filtros">
                    <div class="pd-chips" id="pd-filtros">
                        <button type="button" class="pd-filtro activo" data-medio="">Todas <small><?php echo $cantidad_ventas ?></small></button>
                        <?php foreach ($medios as $valor => $medio) { if ($medio['ventas'] == 0) continue; ?>
                            <button type="button" class="pd-filtro" data-medio="<?php echo htmlspecialchars($valor) ?>"><?php echo $medio['titulo'] ?> <small><?php echo $medio['ventas'] ?></small></button>
                        <?php } ?>
                    </div>
                    <div class="pd-buscar">
                        <i class="ti-search"></i>
                        <input type="text" id="pd-buscar" autocomplete="off" placeholder="Buscar producto o número de venta…">
                    </div>
                </div>

                <div id="pd-lista">
                    <?php foreach ($ventas as $venta) {
                        $items = isset($detalles[$venta->idpedido]) ? $detalles[$venta->idpedido] : array();
                        $nombres = array();
                        $texto_busqueda = '#'.$venta->idpedido;
                        foreach ($items as $item)
                        {
                            $nombres[] = ($item->cantidad > 1 ? pd_numero($item->cantidad).'× ' : '').$item->nombre;
                            $texto_busqueda .= ' '.$item->nombre.' '.$item->codigo;
                        }
                        $color = isset($medios[$venta->modo_de_pago]) ? $medios[$venta->modo_de_pago]['color'] : '#b8c2cc';
                        $titulo_medio = isset($medios[$venta->modo_de_pago]) ? $medios[$venta->modo_de_pago]['titulo'] : $venta->modo_de_pago;
                    ?>
                        <details class="pd-venta" data-medio="<?php echo htmlspecialchars($venta->modo_de_pago) ?>" data-busqueda="<?php echo htmlspecialchars($texto_busqueda) ?>">
                            <summary>
                                <span class="pd-hora"><?php echo date('H:i', strtotime($venta->fecha_hora)) ?></span>
                                <span class="pd-numero">#<?php echo (int)$venta->idpedido ?></span>
                                <span class="pd-resumen-items"><?php echo htmlspecialchars(implode(', ', $nombres)) ?></span>
                                <span class="pd-medio-tag"><i style="background: <?php echo $color ?>"></i><?php echo htmlspecialchars($titulo_medio) ?></span>
                                <span class="pd-total-venta"><?php echo pd_pesos($venta->total) ?></span>
                                <i class="ti-angle-right pd-flecha"></i>
                            </summary>
                            <div class="pd-items">
                                <?php if (count($items) == 0) { ?>
                                    <div class="pd-item-codigo">Sin productos registrados.</div>
                                <?php } ?>
                                <?php foreach ($items as $item) { ?>
                                    <div class="pd-item">
                                        <span class="pd-item-cantidad"><?php echo pd_numero($item->cantidad) ?> ×</span>
                                        <span>
                                            <span class="pd-item-nombre"><?php echo htmlspecialchars($item->nombre ? $item->nombre : 'Producto #'.$item->id_articu) ?></span>
                                            <span class="pd-item-codigo"> · <?php echo htmlspecialchars($item->codigo) ?></span>
                                        </span>
                                    </div>
                                <?php } ?>
                                <div class="pd-items-pie">
                                    Venta #<?php echo (int)$venta->idpedido ?> · <?php echo htmlspecialchars($venta->modo_de_pago) ?> ·
                                    <a href="<?php echo base_url() ?>pedido_detalles/<?php echo (int)$venta->idpedido ?>">Ver detalle</a>
                                </div>
                            </div>
                        </details>
                    <?php } ?>
                    <div class="pd-vacio" id="pd-sin-resultados" style="display: none;"><i class="ti-search"></i>No hay ventas que coincidan.</div>
                </div>
            <?php } ?>
        </div>

    </div>
</section>

<script type="text/javascript">
(function () {
    'use strict';
    var lista = document.getElementById('pd-lista');
    if (!lista) return;

    var ventas = Array.prototype.slice.call(lista.querySelectorAll('.pd-venta'));
    var buscar = document.getElementById('pd-buscar');
    var medioActivo = '';

    function normalizar(texto) {
        return String(texto || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    }
    ventas.forEach(function (v) { v._busqueda = normalizar(v.getAttribute('data-busqueda')); });

    function filtrar() {
        var palabras = normalizar(buscar.value.trim()).split(/\s+/).filter(Boolean);
        var visibles = 0;
        ventas.forEach(function (v) {
            var coincideMedio = !medioActivo || v.getAttribute('data-medio') === medioActivo;
            var coincideTexto = palabras.every(function (w) { return v._busqueda.indexOf(w) !== -1; });
            var mostrar = coincideMedio && coincideTexto;
            v.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });
        document.getElementById('pd-contador').textContent = visibles + ' de ' + ventas.length;
        document.getElementById('pd-sin-resultados').style.display = visibles ? 'none' : '';
    }

    document.getElementById('pd-filtros').addEventListener('click', function (e) {
        var boton = e.target.closest('.pd-filtro');
        if (!boton) return;
        medioActivo = boton.getAttribute('data-medio');
        Array.prototype.forEach.call(this.querySelectorAll('.pd-filtro'), function (b) { b.classList.toggle('activo', b === boton); });
        filtrar();
    });
    buscar.addEventListener('input', filtrar);
})();
</script>
