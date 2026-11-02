<?php
$base_url = base_url()."application/views/layouts";

if (!isset($this->session->userdata("user_data")[0]->idusuario))
{
    redirect('login');
}

$sucursal_id = $this->session->userdata("user_data")[0]->idusuario;

// Si ya se cerró la caja del día, redireccionamos a caja cerrada
$fila_caja = $this->db->select('caja_cerrada')->where('idusuario', $sucursal_id)->get('usuarios')->row();
if ($fila_caja && $fila_caja->caja_cerrada == 1)
{
    redirect('/caja_cerrada', 'refresh');
}

// Productos para el buscador (se filtran en el navegador, sin recargar)
$productos_js = array();
foreach ($rows_productos as $producto)
{
    $productos_js[] = array(
        'id'     => (int)$producto->id,
        'codigo' => (string)$producto->codigo,
        'nombre' => (string)$producto->nombre,
        'precio' => (float)$producto->precio_final,
        'stock'  => (float)$producto->stock_actual,
    );
}
$productos_json = json_encode($productos_js, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
if ($productos_json === false)
{
    // Algún nombre con caracteres no UTF-8: convertirlos y reintentar.
    foreach ($productos_js as &$p)
    {
        $p['nombre'] = iconv('ISO-8859-1', 'UTF-8', $p['nombre']);
        $p['codigo'] = iconv('ISO-8859-1', 'UTF-8', $p['codigo']);
    }
    unset($p);
    $productos_json = json_encode($productos_js, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

// Carrito actual
$carro_js = array();
foreach ($rows_carro as $carro)
{
    $carro_js[] = array(
        'product'      => (int)$carro->product,
        'nombre'       => (string)$carro->nombre,
        'precio_final' => (float)$carro->precio_final,
        'quantity'     => (int)$carro->quantity,
    );
}
$carro_json = json_encode($carro_js, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
if ($carro_json === false)
{
    $carro_json = '[]';
}

// Resumen de las ventas de hoy (solo informativo)
$resumen_medios = array(
    'Efectivo'                  => array('titulo' => 'Efectivo',      'color' => '#47b475', 'total' => 0, 'ventas' => 0),
    'Tarjeta de crédito/débito' => array('titulo' => 'Tarjeta',       'color' => '#3b82c4', 'total' => 0, 'ventas' => 0),
    'Medio electrónico'         => array('titulo' => 'Electrónico',   'color' => '#e0a43a', 'total' => 0, 'ventas' => 0),
    'Transferencia/QR'          => array('titulo' => 'Transf./QR',    'color' => '#8b6cc4', 'total' => 0, 'ventas' => 0),
);
$hoy_total = 0;
$hoy_ventas = 0;
$pedidos_hoy = $this->db->select('total, modo_de_pago')
    ->where('cod_clie', $sucursal_id)
    ->where('fecha', date('Y-m-d'))
    ->get('pedidos')->result();
foreach ($pedidos_hoy as $pedido_hoy)
{
    $hoy_total += $pedido_hoy->total;
    $hoy_ventas++;
    if (isset($resumen_medios[$pedido_hoy->modo_de_pago]))
    {
        $resumen_medios[$pedido_hoy->modo_de_pago]['total'] += $pedido_hoy->total;
        $resumen_medios[$pedido_hoy->modo_de_pago]['ventas']++;
    }
}
function nv_pesos($valor)
{
    return '$ '.number_format($valor, 0, ',', '.');
}

$medios_pago = array(
    array('valor' => 'Efectivo',                  'titulo' => 'Efectivo',         'icono' => 'ti-money'),
    array('valor' => 'Tarjeta de crédito/débito', 'titulo' => 'Tarjeta',          'icono' => 'ti-credit-card'),
    array('valor' => 'Medio electrónico',         'titulo' => 'Medio electrónico','icono' => 'ti-mobile'),
    array('valor' => 'Transferencia/QR',          'titulo' => 'Transferencia/QR', 'icono' => 'ti-layout-grid2'),
);
?>

<style type="text/css">
.nv {
    --nv-verde: #47b475;
    --nv-verde-oscuro: #3a9862;
    --nv-verde-claro: #eaf6ef;
    --nv-texto: #1f2933;
    --nv-texto-suave: #6b7785;
    --nv-borde: #e3e8ee;
    --nv-fondo: #f5f7f9;
    --nv-rojo: #d64545;
    --nv-ambar: #c98a0b;
    background: var(--nv-fondo);
    padding: 32px 0 60px;
    color: var(--nv-texto);
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.nv * { box-sizing: border-box; }
/* El tema global agrega márgenes y mayúsculas a botones, campos y títulos: se anulan acá. */
.nv button, .nv input, .nv h1, .nv h2, .nv p { margin-top: 0; margin-bottom: 0; }
.nv button { font-family: inherit; line-height: 1.2; letter-spacing: 0; text-transform: none; }
.nv input::-webkit-input-placeholder { text-transform: none; font-weight: 400; letter-spacing: 0; font-size: 15px; color: #8a95a1; }
.nv input::-moz-placeholder { text-transform: none; font-weight: 400; letter-spacing: 0; font-size: 15px; color: #8a95a1; }
.nv-encabezado { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.nv-encabezado h1 { font-size: 26px; font-weight: 600; margin: 0; color: var(--nv-texto); letter-spacing: 0; text-transform: none; }
.nv-encabezado p { margin: 0; color: var(--nv-texto-suave); font-size: 14px; }

/* Resumen del día (informativo) */
.nv-hoy {
    display: flex; align-items: center; gap: 24px; padding: 12px 18px; margin-bottom: 20px;
    background: #fff; border: 1px solid var(--nv-borde); border-radius: 12px;
}
.nv-hoy-cifras { display: flex; align-items: baseline; gap: 10px; white-space: nowrap; }
.nv-hoy-etiqueta { font-size: 12px; font-weight: 600; color: var(--nv-texto-suave); text-transform: uppercase; letter-spacing: .04em; }
.nv-hoy-total { font-size: 18px; font-weight: 700; font-variant-numeric: tabular-nums; }
.nv-hoy-ventas { font-size: 13px; color: var(--nv-texto-suave); }
.nv-hoy-reparto { flex: 1 1 auto; min-width: 0; }
.nv-hoy-barra { display: flex; height: 8px; border-radius: 999px; overflow: hidden; background: #eef1f4; gap: 2px; }
.nv-hoy-barra div { height: 100%; }
.nv-hoy-leyenda { display: flex; flex-wrap: wrap; gap: 4px 16px; margin-top: 6px; font-size: 12px; color: var(--nv-texto-suave); }
.nv-hoy-leyenda span { display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
.nv-hoy-leyenda i { width: 8px; height: 8px; border-radius: 2px; display: inline-block; }
.nv-hoy-leyenda b { color: var(--nv-texto); font-weight: 600; font-variant-numeric: tabular-nums; }
.nv-hoy-leyenda .cero { opacity: .5; }
.nv-hoy-cerrar { font-size: 13px; font-weight: 600; color: var(--nv-texto-suave); white-space: nowrap; }
.nv-hoy-cerrar:hover { color: var(--nv-verde-oscuro); }
@media (max-width: 767px) {
    .nv-hoy { flex-wrap: wrap; gap: 10px 16px; }
    .nv-hoy-reparto { flex-basis: 100%; order: 3; }
    .nv-hoy-cerrar { margin-left: auto; }
}

.nv-grid { display: grid; grid-template-columns: minmax(0, 1fr) 400px; gap: 24px; align-items: start; }
@media (max-width: 991px) { .nv-grid { grid-template-columns: 1fr; } }

.nv-panel { background: #fff; border: 1px solid var(--nv-borde); border-radius: 12px; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
.nv-panel-cabecera { padding: 18px 20px 14px; border-bottom: 1px solid var(--nv-borde); display: flex; align-items: center; justify-content: space-between; gap: 10px; }
.nv-panel-cabecera h2 { font-size: 16px; font-weight: 600; margin: 0; color: var(--nv-texto); text-transform: none; letter-spacing: 0; }

/* Buscador */
.nv-buscador { position: relative; padding: 16px 20px; border-bottom: 1px solid var(--nv-borde); }
.nv-buscador i.ti-search { position: absolute; left: 36px; top: 50%; transform: translateY(-50%); color: var(--nv-texto-suave); font-size: 16px; pointer-events: none; }
.nv .nv-buscador input {
    width: 100%; height: 48px; margin: 0; padding: 0 44px 0 46px;
    border: 1px solid var(--nv-borde); border-radius: 10px; background: var(--nv-fondo);
    font-size: 16px; color: var(--nv-texto); transition: border-color .15s, background .15s, box-shadow .15s;
}
.nv .nv-buscador input:focus { outline: none; background: #fff; border-color: var(--nv-verde); box-shadow: 0 0 0 3px rgba(71, 180, 117, .18); }
.nv-limpiar { position: absolute; right: 30px; top: 50%; transform: translateY(-50%); border: 0; background: transparent; color: var(--nv-texto-suave); font-size: 14px; cursor: pointer; padding: 6px; display: none; }
.nv-buscador.con-texto .nv-limpiar { display: block; }
.nv-ayuda { padding: 8px 20px; font-size: 12px; color: var(--nv-texto-suave); border-bottom: 1px solid var(--nv-borde); background: #fbfcfd; }
.nv-ayuda kbd { font-family: inherit; font-size: 11px; background: #fff; border: 1px solid var(--nv-borde); border-bottom-width: 2px; border-radius: 4px; padding: 1px 5px; color: var(--nv-texto); box-shadow: none; }

.nv-lista { max-height: calc(100vh - 300px); min-height: 320px; overflow-y: auto; }
.nv-producto {
    display: grid; grid-template-columns: minmax(0, 1fr) auto auto 44px; gap: 14px; align-items: center;
    padding: 12px 20px; border-bottom: 1px solid #f0f2f5; cursor: pointer; transition: background .1s;
}
.nv-producto:hover, .nv-producto.activo { background: var(--nv-verde-claro); }
.nv-producto-nombre { font-size: 15px; font-weight: 600; color: var(--nv-texto); line-height: 1.3; text-transform: uppercase; overflow-wrap: anywhere; }
.nv-producto-codigo { font-size: 12px; color: var(--nv-texto-suave); margin-top: 2px; font-variant-numeric: tabular-nums; }
.nv-producto-precio { font-size: 15px; font-weight: 600; text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.nv-stock { font-size: 12px; font-weight: 600; padding: 3px 8px; border-radius: 999px; background: #eef1f4; color: var(--nv-texto-suave); white-space: nowrap; }
.nv-stock.bajo { background: #fdf3dc; color: var(--nv-ambar); }
.nv-stock.sin { background: #fbe9e9; color: var(--nv-rojo); }
.nv-agregar {
    width: 40px; height: 40px; border-radius: 10px; border: 0; background: var(--nv-verde); color: #fff;
    font-size: 16px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background .15s, transform .1s;
}
.nv-agregar:hover { background: var(--nv-verde-oscuro); }
.nv-agregar:active { transform: scale(.94); }
.nv-producto.recien { animation: nv-flash .6s ease; }
@keyframes nv-flash { from { background: #c9ebd6; } to { background: transparent; } }
.nv-vacio { padding: 48px 20px; text-align: center; color: var(--nv-texto-suave); font-size: 14px; }
.nv-vacio i { display: block; font-size: 30px; margin-bottom: 10px; opacity: .5; }
.nv-mas { padding: 12px 20px; font-size: 13px; color: var(--nv-texto-suave); text-align: center; }

/* Venta */
.nv-venta { position: sticky; top: 80px; display: flex; flex-direction: column; max-height: calc(100vh - 96px); }
@media (max-width: 991px) { .nv-venta { position: static; max-height: none; } }
.nv-contador { font-size: 12px; font-weight: 600; background: var(--nv-verde-claro); color: var(--nv-verde-oscuro); padding: 3px 10px; border-radius: 999px; }
.nv-vaciar { border: 0; background: transparent; color: var(--nv-texto-suave); font-size: 13px; cursor: pointer; padding: 4px 6px; border-radius: 6px; }
.nv-vaciar:hover { color: var(--nv-rojo); background: #fbe9e9; }
.nv-items { overflow-y: auto; flex: 1 1 auto; min-height: 120px; }
.nv-item { padding: 14px 20px; border-bottom: 1px solid #f0f2f5; }
.nv-item-fila { display: flex; justify-content: space-between; gap: 10px; align-items: flex-start; }
.nv-item-nombre { font-size: 14px; font-weight: 600; text-transform: uppercase; line-height: 1.3; overflow-wrap: anywhere; }
.nv-item-unitario { font-size: 12px; color: var(--nv-texto-suave); margin-top: 2px; }
.nv-item-subtotal { font-size: 15px; font-weight: 600; white-space: nowrap; font-variant-numeric: tabular-nums; }
.nv-item-controles { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; }
.nv-cantidad { display: inline-flex; align-items: center; border: 1px solid var(--nv-borde); border-radius: 10px; overflow: hidden; background: #fff; }
.nv-cantidad button { width: 36px; height: 36px; border: 0; background: #fff; color: var(--nv-texto); font-size: 18px; line-height: 1; cursor: pointer; }
.nv-cantidad button:hover { background: var(--nv-fondo); }
.nv .nv-cantidad input {
    width: 72px; height: 36px; margin: 0; padding: 0 4px; border: 0; border-left: 1px solid var(--nv-borde); border-right: 1px solid var(--nv-borde);
    border-radius: 0; text-align: center; font-size: 15px; font-weight: 600; color: var(--nv-texto); background: #fff; -moz-appearance: textfield;
}
.nv .nv-cantidad input::-webkit-outer-spin-button, .nv .nv-cantidad input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.nv .nv-cantidad input:focus { outline: none; background: var(--nv-verde-claro); }
.nv-quitar { border: 0; background: transparent; color: var(--nv-texto-suave); font-size: 15px; cursor: pointer; padding: 8px; border-radius: 8px; }
.nv-quitar:hover { color: var(--nv-rojo); background: #fbe9e9; }
.nv-aviso-stock { font-size: 12px; color: var(--nv-ambar); margin-top: 6px; }
.nv-item.guardando .nv-item-subtotal { opacity: .5; }

.nv-pie { border-top: 1px solid var(--nv-borde); padding: 16px 20px 20px; background: #fff; border-radius: 0 0 12px 12px; }
.nv-etiqueta { font-size: 12px; font-weight: 600; color: var(--nv-texto-suave); margin-bottom: 8px; text-transform: uppercase; letter-spacing: .04em; }
.nv-pagos { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; }
.nv-pago {
    display: flex; align-items: center; gap: 8px; min-height: 48px; padding: 8px 12px; border: 1px solid var(--nv-borde); border-radius: 10px;
    background: #fff; color: var(--nv-texto); font-size: 14px; font-weight: 600; text-align: left; cursor: pointer; transition: border-color .15s, background .15s;
}
.nv-pago i { font-size: 18px; color: var(--nv-texto-suave); }
.nv-pago:hover { border-color: #b8c2cc; }
.nv-pago.seleccionado { border-color: var(--nv-verde); background: var(--nv-verde-claro); box-shadow: inset 0 0 0 1px var(--nv-verde); }
.nv-pago.seleccionado i { color: var(--nv-verde-oscuro); }
.nv-pagos.error .nv-pago { border-color: var(--nv-rojo); }
.nv-total { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 14px; }
.nv-total span { font-size: 14px; color: var(--nv-texto-suave); }
.nv-total strong { font-size: 30px; font-weight: 700; font-variant-numeric: tabular-nums; }
.nv-finalizar {
    width: 100%; height: 54px; border: 0; border-radius: 12px; background: var(--nv-verde); color: #fff; font-size: 17px; font-weight: 600;
    cursor: pointer; transition: background .15s; display: flex; align-items: center; justify-content: center; gap: 8px;
}
.nv-finalizar:hover { background: var(--nv-verde-oscuro); }
.nv-finalizar:disabled { background: #c5ccd3; cursor: not-allowed; }
.nv-mensaje { font-size: 13px; color: var(--nv-rojo); margin-top: 8px; min-height: 18px; text-align: center; }

/* Avisos */
.nv-toasts { position: fixed; right: 20px; bottom: 20px; display: flex; flex-direction: column; gap: 8px; z-index: 10000; }
.nv-toast {
    background: #1f2933; color: #fff; padding: 12px 16px; border-radius: 10px; font-size: 14px; box-shadow: 0 8px 24px rgba(16, 24, 40, .2);
    display: flex; align-items: center; gap: 10px; max-width: 360px; animation: nv-entrar .2s ease;
}
.nv-toast.ok i { color: #7ee2a8; }
.nv-toast.error { background: var(--nv-rojo); }
@keyframes nv-entrar { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }

@media (max-width: 600px) {
    .nv { padding-top: 20px; }
    .nv-producto { grid-template-columns: minmax(0, 1fr) auto 44px; padding: 12px 14px; }
    .nv-producto .nv-stock { grid-column: 1; justify-self: start; grid-row: 2; }
    .nv-buscador { padding: 12px 14px; }
    .nv-buscador i.ti-search { left: 30px; }
    .nv-limpiar { right: 24px; }
    .nv-item, .nv-pie, .nv-panel-cabecera { padding-left: 14px; padding-right: 14px; }
}
</style>

<section class="nv">
    <div class="container">

        <div class="nv-encabezado">
            <h1>Nueva venta</h1>
            <p><a href="<?php echo base_url() ?>">Inicio</a> / Nueva venta</p>
        </div>

        <div class="nv-hoy" aria-label="Resumen de ventas de hoy">
            <div class="nv-hoy-cifras">
                <span class="nv-hoy-etiqueta">Hoy</span>
                <span class="nv-hoy-total"><?php echo nv_pesos($hoy_total) ?></span>
                <span class="nv-hoy-ventas"><?php echo $hoy_ventas ?> venta<?php echo $hoy_ventas == 1 ? '' : 's' ?></span>
            </div>
            <div class="nv-hoy-reparto">
                <div class="nv-hoy-barra">
                    <?php foreach ($resumen_medios as $medio) {
                        if ($medio['total'] <= 0 || $hoy_total <= 0) continue; ?>
                        <div style="width: <?php echo round($medio['total'] * 100 / $hoy_total, 2) ?>%; background: <?php echo $medio['color'] ?>" title="<?php echo $medio['titulo'].': '.nv_pesos($medio['total']) ?>"></div>
                    <?php } ?>
                </div>
                <div class="nv-hoy-leyenda">
                    <?php foreach ($resumen_medios as $medio) {
                        $porcentaje = $hoy_total > 0 ? round($medio['total'] * 100 / $hoy_total) : 0; ?>
                        <span class="<?php echo $medio['total'] <= 0 ? 'cero' : '' ?>" title="<?php echo $medio['ventas'] ?> venta<?php echo $medio['ventas'] == 1 ? '' : 's' ?>">
                            <i style="background: <?php echo $medio['color'] ?>"></i><?php echo $medio['titulo'] ?> <b><?php echo nv_pesos($medio['total']) ?></b> <?php echo $porcentaje ?>%
                        </span>
                    <?php } ?>
                </div>
            </div>
            <a class="nv-hoy-cerrar" href="<?php echo base_url() ?>cerrar_caja">Cerrar caja <i class="ti-arrow-right"></i></a>
        </div>

        <div class="nv-grid">

            <!-- Columna izquierda: productos -->
            <div class="nv-panel">
                <div class="nv-panel-cabecera">
                    <h2>Productos</h2>
                    <span class="nv-contador" id="nv-cantidad-productos"></span>
                </div>
                <div class="nv-buscador" id="nv-buscador">
                    <i class="ti-search"></i>
                    <input type="text" id="texto_buscar" autocomplete="off" placeholder="Buscar por nombre o código…" autofocus>
                    <button type="button" class="nv-limpiar" id="nv-limpiar" title="Limpiar búsqueda"><i class="ti-close"></i></button>
                </div>
                <div class="nv-ayuda">
                    <kbd>↑</kbd> <kbd>↓</kbd> para moverte, <kbd>Enter</kbd> para agregar. También podés escanear el código de barras.
                </div>
                <div class="nv-lista" id="nv-lista"></div>
            </div>

            <!-- Columna derecha: detalle de la venta -->
            <div class="nv-panel nv-venta">
                <div class="nv-panel-cabecera">
                    <h2>Detalle de la venta <span class="nv-contador" id="nv-cantidad-items">0</span></h2>
                    <button type="button" class="nv-vaciar" id="nv-vaciar"><i class="ti-trash"></i> Vaciar</button>
                </div>

                <div class="nv-items" id="nv-items"></div>

                <div class="nv-pie">
                    <div class="nv-etiqueta">Medio de pago</div>
                    <div class="nv-pagos" id="nv-pagos">
                        <?php foreach ($medios_pago as $medio) { ?>
                            <button type="button" class="nv-pago" data-valor="<?php echo htmlspecialchars($medio['valor']) ?>">
                                <i class="<?php echo $medio['icono'] ?>"></i><?php echo $medio['titulo'] ?>
                            </button>
                        <?php } ?>
                    </div>

                    <div class="nv-total">
                        <span>Total</span>
                        <strong id="nv-total">$ 0,00</strong>
                    </div>

                    <button type="button" class="nv-finalizar" id="nv-finalizar" disabled>
                        <i class="ti-check"></i> Finalizar venta
                    </button>
                    <div class="nv-mensaje" id="mensaje"></div>
                </div>
            </div>

        </div>
    </div>
</section>

<div class="nv-toasts" id="nv-toasts"></div>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>

<script type="text/javascript">
(function () {
    'use strict';

    var URL_BASE = '<?php echo base_url() ?>';
    var PRODUCTOS = <?php echo $productos_json ?>;
    var MAX_RESULTADOS = 60;

    var carrito = <?php echo $carro_json ?>;
    var medioPago = '';
    var indiceActivo = 0;
    var resultados = [];
    var esperas = {};        // temporizadores de cambio de cantidad por producto
    var enviando = false;

    var productosPorId = {};
    var productosPorCodigo = {};
    PRODUCTOS.forEach(function (p) {
        p.busqueda = normalizar(p.nombre + ' ' + p.codigo);
        productosPorId[p.id] = p;
        if (p.codigo) productosPorCodigo[String(p.codigo).trim().toLowerCase()] = p;
    });

    var $buscar = document.getElementById('texto_buscar');
    var $lista = document.getElementById('nv-lista');
    var $items = document.getElementById('nv-items');

    // ---------- Utilidades ----------

    function normalizar(texto) {
        return String(texto || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    }

    function escapar(texto) {
        return String(texto == null ? '' : texto)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    var formatoPesos = new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' });
    function pesos(valor) {
        return formatoPesos.format(Number(valor) || 0);
    }

    function aviso(texto, tipo) {
        var contenedor = document.getElementById('nv-toasts');
        var el = document.createElement('div');
        el.className = 'nv-toast ' + (tipo || 'ok');
        el.innerHTML = '<i class="' + (tipo === 'error' ? 'ti-alert' : 'ti-check') + '"></i><span>' + escapar(texto) + '</span>';
        contenedor.appendChild(el);
        setTimeout(function () { el.remove(); }, 2600);
    }

    // Normaliza la respuesta del servidor (distintos endpoints devuelven campos distintos).
    function aCarrito(datos) {
        var filas = typeof datos === 'string' ? JSON.parse(datos) : datos;
        return (filas || []).map(function (f) {
            return {
                product: parseInt(f.product, 10),
                nombre: f.nombre,
                precio_final: parseFloat(f.precio_final) || 0,
                quantity: parseInt(f.quantity, 10) || 0
            };
        });
    }

    // ---------- Buscador ----------

    function buscar() {
        var texto = normalizar($buscar.value.trim());
        var palabras = texto.split(/\s+/).filter(Boolean);
        document.getElementById('nv-buscador').classList.toggle('con-texto', texto.length > 0);

        var encontrados = palabras.length === 0 ? PRODUCTOS : PRODUCTOS.filter(function (p) {
            return palabras.every(function (w) { return p.busqueda.indexOf(w) !== -1; });
        });

        // Coincidencia exacta de código primero
        var exacto = productosPorCodigo[texto];
        if (exacto) {
            encontrados = [exacto].concat(encontrados.filter(function (p) { return p !== exacto; }));
        }

        resultados = encontrados.slice(0, MAX_RESULTADOS);
        indiceActivo = 0;
        pintarResultados(encontrados.length);
    }

    function pintarResultados(totalEncontrados) {
        document.getElementById('nv-cantidad-productos').textContent = totalEncontrados + ' productos';

        if (resultados.length === 0) {
            $lista.innerHTML = '<div class="nv-vacio"><i class="ti-search"></i>No se encontraron productos para "' + escapar($buscar.value) + '"</div>';
            return;
        }

        var html = resultados.map(function (p, i) {
            var claseStock = p.stock <= 0 ? 'sin' : (p.stock < 5 ? 'bajo' : '');
            return '<div class="nv-producto' + (i === indiceActivo ? ' activo' : '') + '" data-indice="' + i + '">' +
                       '<div>' +
                           '<div class="nv-producto-nombre">' + escapar(p.nombre) + '</div>' +
                           '<div class="nv-producto-codigo">' + escapar(p.codigo) + '</div>' +
                       '</div>' +
                       '<span class="nv-stock ' + claseStock + '">Stock ' + escapar(p.stock) + '</span>' +
                       '<div class="nv-producto-precio">' + pesos(p.precio) + '</div>' +
                       '<button type="button" class="nv-agregar" title="Agregar"><i class="ti-plus"></i></button>' +
                   '</div>';
        }).join('');

        if (totalEncontrados > resultados.length) {
            html += '<div class="nv-mas">Mostrando ' + resultados.length + ' de ' + totalEncontrados + '. Escribí más para afinar la búsqueda.</div>';
        }
        $lista.innerHTML = html;
    }

    function marcarActivo(nuevo) {
        if (resultados.length === 0) return;
        indiceActivo = Math.max(0, Math.min(resultados.length - 1, nuevo));
        var filas = $lista.querySelectorAll('.nv-producto');
        filas.forEach(function (f, i) { f.classList.toggle('activo', i === indiceActivo); });
        if (filas[indiceActivo]) filas[indiceActivo].scrollIntoView({ block: 'nearest' });
    }

    $buscar.addEventListener('input', buscar);

    $buscar.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown') { e.preventDefault(); marcarActivo(indiceActivo + 1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); marcarActivo(indiceActivo - 1); }
        else if (e.key === 'Escape') { $buscar.value = ''; buscar(); }
        else if (e.key === 'Enter') {
            e.preventDefault();
            var texto = $buscar.value.trim();
            if (texto === '') return;
            var exacto = productosPorCodigo[texto.toLowerCase()];
            if (exacto) { agregar(exacto.id, 1); }
            else if (resultados[indiceActivo]) { agregar(resultados[indiceActivo].id, 1); }
            else { agregarPorCodigoServidor(texto); }
            $buscar.value = '';
            buscar();
        }
    });

    document.getElementById('nv-limpiar').addEventListener('click', function () {
        $buscar.value = '';
        buscar();
        $buscar.focus();
    });

    $lista.addEventListener('click', function (e) {
        var fila = e.target.closest('.nv-producto');
        if (!fila) return;
        var p = resultados[parseInt(fila.getAttribute('data-indice'), 10)];
        if (!p) return;
        fila.classList.remove('recien');
        void fila.offsetWidth;
        fila.classList.add('recien');
        agregar(p.id, 1);
    });

    // Lector de código de barras cuando el foco no está en un campo de texto
    var codigoLeido = '';
    var tiempoUltimaTecla = 0;
    document.addEventListener('keypress', function (e) {
        var destino = e.target;
        if (destino && (destino.tagName === 'INPUT' || destino.tagName === 'TEXTAREA' || destino.tagName === 'SELECT')) return;

        var ahora = Date.now();
        if (ahora - tiempoUltimaTecla > 100) codigoLeido = '';
        tiempoUltimaTecla = ahora;

        if (e.key === 'Enter') {
            var codigo = codigoLeido.trim();
            codigoLeido = '';
            if (codigo === '') return;
            e.preventDefault();
            var local = productosPorCodigo[codigo.toLowerCase()];
            if (local) agregar(local.id, 1);
            else agregarPorCodigoServidor(codigo);
            return;
        }
        codigoLeido += e.key;
    });

    function agregarPorCodigoServidor(codigo) {
        $.post(URL_BASE + 'sitio/caja/producto_por_codigo', { codigo: codigo })
            .done(function (data) {
                var producto = null;
                try { producto = data && data.trim() !== '' ? JSON.parse(data) : null; } catch (err) { producto = null; }
                if (producto && producto.id) agregar(producto.id, 1);
                else aviso('No se encontró ningún producto con el código ' + codigo, 'error');
            })
            .fail(function () { aviso('Error al conectar con el servidor', 'error'); });
    }

    // ---------- Carrito ----------

    function agregar(idProducto, cantidad) {
        var p = productosPorId[idProducto];
        $.get(URL_BASE + 'sitio/getData', { idArt: idProducto, quantity: cantidad })
            .done(function (data) {
                carrito = aCarrito(data);
                pintarCarrito();
                aviso('Agregado: ' + (p ? p.nombre : 'producto'));
            })
            .fail(function () { aviso('No se pudo agregar el producto', 'error'); });
    }

    function quitar(idProducto) {
        $.get(URL_BASE + 'sitio/quitarData', { idArt: idProducto, delAll: 0 })
            .done(function (data) { carrito = aCarrito(data); pintarCarrito(); })
            .fail(function () { aviso('No se pudo quitar el producto', 'error'); });
    }

    function vaciar() {
        if (carrito.length === 0) return;
        if (!confirm('¿Vaciar el detalle de la venta?')) return;
        $.get(URL_BASE + 'sitio/quitarData', { idArt: 0, delAll: 1 })
            .done(function (data) { carrito = aCarrito(data); pintarCarrito(); })
            .fail(function () { aviso('No se pudo vaciar la venta', 'error'); });
    }

    // Cambia la cantidad en pantalla al instante y la guarda en el servidor
    // cuando el usuario deja de tocar (así no se manda un pedido por cada clic).
    function cambiarCantidad(idProducto, cantidad, inmediato) {
        var item = carrito.filter(function (i) { return i.product === idProducto; })[0];
        if (!item) return;
        if (!(cantidad >= 1)) cantidad = 1;
        item.quantity = cantidad;
        pintarCarrito();

        var fila = $items.querySelector('[data-id="' + idProducto + '"]');
        if (fila) fila.classList.add('guardando');

        clearTimeout(esperas[idProducto]);
        esperas[idProducto] = setTimeout(function () {
            delete esperas[idProducto];
            $.get(URL_BASE + 'sitio/modificarData', { idArt: idProducto, quantity: cantidad })
                .done(function (data) {
                    // Si el usuario siguió cambiando la cantidad, esperar a la última respuesta.
                    if (esperas[idProducto]) return;
                    carrito = aCarrito(data);
                    pintarCarrito();
                })
                .fail(function () { aviso('No se pudo actualizar la cantidad', 'error'); });
        }, inmediato ? 0 : 400);
    }

    function pintarCarrito() {
        var totalItems = 0;
        var total = 0;

        if (carrito.length === 0) {
            $items.innerHTML = '<div class="nv-vacio"><i class="ti-shopping-cart"></i>Todavía no agregaste productos.<br>Buscalos a la izquierda o escaneá el código.</div>';
        } else {
            // Se actualiza fila por fila para no perder el foco del campo que se está editando.
            var existentes = {};
            Array.prototype.forEach.call($items.querySelectorAll('.nv-item'), function (f) { existentes[f.getAttribute('data-id')] = f; });
            var vacio = $items.querySelector('.nv-vacio');
            if (vacio) vacio.remove();

            carrito.forEach(function (item, posicion) {
                var subtotal = item.precio_final * item.quantity;
                totalItems += item.quantity;
                total += subtotal;

                var stock = productosPorId[item.product] ? productosPorId[item.product].stock : null;
                var avisoStock = (stock !== null && item.quantity > stock)
                    ? '<div class="nv-aviso-stock"><i class="ti-alert"></i> Stock disponible: ' + escapar(stock) + '</div>' : '';

                var fila = existentes[item.product];
                delete existentes[item.product];

                if (!fila) {
                    fila = document.createElement('div');
                    fila.className = 'nv-item';
                    fila.setAttribute('data-id', item.product);
                    fila.innerHTML =
                        '<div class="nv-item-fila">' +
                            '<div>' +
                                '<div class="nv-item-nombre">' + escapar(item.nombre) + '</div>' +
                                '<div class="nv-item-unitario"></div>' +
                            '</div>' +
                            '<div class="nv-item-subtotal"></div>' +
                        '</div>' +
                        '<div class="nv-item-controles">' +
                            '<div class="nv-cantidad">' +
                                '<button type="button" data-accion="menos" title="Restar">−</button>' +
                                '<input type="number" min="1" step="1" inputmode="numeric" aria-label="Cantidad">' +
                                '<button type="button" data-accion="mas" title="Sumar">+</button>' +
                            '</div>' +
                            '<button type="button" class="nv-quitar" data-accion="quitar" title="Quitar"><i class="ti-trash"></i></button>' +
                        '</div>' +
                        '<div class="nv-stock-aviso"></div>';
                }
                // Mover la fila solo si cambió de lugar: moverla le quita el foco
                // al campo de cantidad mientras se está escribiendo.
                var enSuLugar = $items.children[posicion];
                if (enSuLugar !== fila) $items.insertBefore(fila, enSuLugar || null);

                fila.querySelector('.nv-item-unitario').textContent = pesos(item.precio_final) + ' c/u';
                fila.querySelector('.nv-item-subtotal').textContent = pesos(subtotal);
                fila.querySelector('.nv-stock-aviso').innerHTML = avisoStock;
                var input = fila.querySelector('input');
                if (document.activeElement !== input) input.value = item.quantity;
                if (!esperas[item.product]) fila.classList.remove('guardando');
            });

            Object.keys(existentes).forEach(function (id) { existentes[id].remove(); });
        }

        document.getElementById('nv-cantidad-items').textContent = totalItems;
        document.getElementById('nv-total').textContent = pesos(total);
        actualizarBotonFinalizar();
    }

    $items.addEventListener('click', function (e) {
        var boton = e.target.closest('button[data-accion]');
        if (!boton) return;
        var id = parseInt(boton.closest('.nv-item').getAttribute('data-id'), 10);
        var item = carrito.filter(function (i) { return i.product === id; })[0];
        if (!item) return;

        var accion = boton.getAttribute('data-accion');
        if (accion === 'mas') cambiarCantidad(id, item.quantity + 1);
        else if (accion === 'menos') {
            if (item.quantity <= 1) quitar(id);
            else cambiarCantidad(id, item.quantity - 1);
        }
        else if (accion === 'quitar') quitar(id);
    });

    $items.addEventListener('input', function (e) {
        if (e.target.tagName !== 'INPUT') return;
        var cantidad = parseInt(e.target.value, 10);
        if (cantidad >= 1) cambiarCantidad(parseInt(e.target.closest('.nv-item').getAttribute('data-id'), 10), cantidad);
    });

    $items.addEventListener('change', function (e) {
        if (e.target.tagName !== 'INPUT') return;
        var id = parseInt(e.target.closest('.nv-item').getAttribute('data-id'), 10);
        var cantidad = parseInt(e.target.value, 10);
        if (!(cantidad >= 1)) {
            e.target.value = 1;
            cantidad = 1;
        }
        cambiarCantidad(id, cantidad, true);
    });

    $items.addEventListener('keydown', function (e) {
        if (e.target.tagName === 'INPUT' && e.key === 'Enter') {
            e.preventDefault();
            e.target.blur();
            $buscar.focus();
        }
    });

    document.getElementById('nv-vaciar').addEventListener('click', vaciar);

    // ---------- Medio de pago y finalizar ----------

    document.getElementById('nv-pagos').addEventListener('click', function (e) {
        var boton = e.target.closest('.nv-pago');
        if (!boton) return;
        medioPago = boton.getAttribute('data-valor');
        Array.prototype.forEach.call(this.querySelectorAll('.nv-pago'), function (b) {
            b.classList.toggle('seleccionado', b === boton);
        });
        this.classList.remove('error');
        document.getElementById('mensaje').textContent = '';
        $.post(URL_BASE + 'set_pago', { pago: medioPago });
        actualizarBotonFinalizar();
    });

    function actualizarBotonFinalizar() {
        document.getElementById('nv-finalizar').disabled = carrito.length === 0 || enviando;
    }

    document.getElementById('nv-finalizar').addEventListener('click', function () {
        if (carrito.length === 0 || enviando) return;
        if (!medioPago) {
            document.getElementById('nv-pagos').classList.add('error');
            document.getElementById('mensaje').textContent = 'Seleccioná un medio de pago para finalizar la venta';
            return;
        }
        if (Object.keys(esperas).length > 0) {
            aviso('Guardando cantidades, intentá de nuevo en un segundo', 'error');
            return;
        }
        var total = document.getElementById('nv-total').textContent;
        if (!confirm('¿Confirmar venta por ' + total + ' (' + medioPago + ')?')) return;

        enviando = true;
        actualizarBotonFinalizar();
        location.href = URL_BASE + 'enviar_pedido?pago=' + encodeURIComponent(medioPago);
    });

    // ---------- Inicio ----------
    buscar();
    pintarCarrito();
})();
</script>
