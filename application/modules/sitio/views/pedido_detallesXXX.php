<?php
$base_url = base_url()."application/views/layouts";

if (!isset($this->session->userdata("user_data")[0]->idusuario))
{
    redirect('login');
}

$sucursal_id = (int)$this->session->userdata("user_data")[0]->idusuario;
$id_pedido = (int)$this->uri->segment(2);

// Solo se pueden ver ventas de la propia sucursal
$pedido = $this->db->where('idpedido', $id_pedido)->where('cod_clie', $sucursal_id)->get('pedidos')->row();
if (!$pedido)
{
    redirect('pedidos');
}

// Productos de la venta (con el precio actual de cada uno)
$items = $this->db->select('pedidosdetalles.id_articu, pedidosdetalles.cantidad, productos.nombre, productos.codigo, productos.precio_final')
    ->from('pedidosdetalles')
    ->join('productos', 'productos.id = pedidosdetalles.id_articu', 'left')
    ->where('pedidosdetalles.idpedido', $id_pedido)
    ->order_by('pedidosdetalles.iddetalle', 'asc')
    ->get()->result();

$unidades = 0;
$suma_precios_actuales = 0;
foreach ($items as $item)
{
    $unidades += $item->cantidad;
    $suma_precios_actuales += $item->cantidad * $item->precio_final;
}
// La venta no guarda el precio de cada producto: los precios actuales solo se
// muestran si coinciden con el total cobrado (si no, cambiaron desde la venta).
$mostrar_precios = count($items) > 0 && abs($suma_precios_actuales - $pedido->total) < 1;

// Venta anterior y siguiente del mismo día
$anterior = $this->db->select('idpedido')->where('cod_clie', $sucursal_id)->where('fecha', $pedido->fecha)
    ->where('idpedido <', $id_pedido)->order_by('idpedido', 'desc')->limit(1)->get('pedidos')->row();
$siguiente = $this->db->select('idpedido')->where('cod_clie', $sucursal_id)->where('fecha', $pedido->fecha)
    ->where('idpedido >', $id_pedido)->order_by('idpedido', 'asc')->limit(1)->get('pedidos')->row();

$medios = array(
    'Efectivo'                  => array('icono' => 'ti-money',        'color' => '#47b475'),
    'Tarjeta de crédito/débito' => array('icono' => 'ti-credit-card',  'color' => '#3b82c4'),
    'Medio electrónico'         => array('icono' => 'ti-mobile',       'color' => '#e0a43a'),
    'Transferencia/QR'          => array('icono' => 'ti-layout-grid2', 'color' => '#8b6cc4'),
);
$medio = isset($medios[$pedido->modo_de_pago]) ? $medios[$pedido->modo_de_pago] : array('icono' => 'ti-wallet', 'color' => '#6b7785');

function dv_pesos($valor)
{
    return '$ '.number_format($valor, 2, ',', '.');
}

$dias  = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
$meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
$momento = strtotime($pedido->fecha_hora);
$fecha_texto = $dias[date('w', $momento)].' '.date('j', $momento).' de '.$meses[date('n', $momento)].' de '.date('Y', $momento);
$hora_texto = date('H:i', $momento);
$es_hoy = $pedido->fecha == date('Y-m-d');
?>

<style type="text/css">
.dv {
    --dv-verde: #47b475;
    --dv-verde-oscuro: #3a9862;
    --dv-verde-claro: #eaf6ef;
    --dv-texto: #1f2933;
    --dv-texto-suave: #6b7785;
    --dv-borde: #e3e8ee;
    --dv-fondo: #f5f7f9;
    background: var(--dv-fondo);
    padding: 32px 0 60px;
    color: var(--dv-texto);
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dv * { box-sizing: border-box; }
/* El tema global agrega márgenes y mayúsculas a botones y títulos: se anulan acá. */
.dv button, .dv h1, .dv h2, .dv p { margin-top: 0; margin-bottom: 0; }
.dv button { font-family: inherit; line-height: 1.2; letter-spacing: 0; text-transform: none; }

.dv-contenedor { max-width: 860px; margin: 0 auto; }
.dv-volver { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; color: var(--dv-texto-suave); margin-bottom: 14px; }
.dv-volver:hover { color: var(--dv-verde-oscuro); text-decoration: none; }

.dv-encabezado { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.dv-encabezado h1 { font-size: 26px; font-weight: 600; color: var(--dv-texto); letter-spacing: 0; text-transform: none; }
.dv-subtitulo { color: var(--dv-texto-suave); font-size: 15px; margin-top: 4px; }
.dv-acciones { display: flex; gap: 8px; }
.dv-boton {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 40px; min-width: 40px; padding: 0 14px; border-radius: 10px;
    font-size: 14px; font-weight: 600; border: 1px solid var(--dv-borde); background: #fff; color: var(--dv-texto); cursor: pointer; text-decoration: none;
}
.dv-boton:hover { border-color: #b8c2cc; color: var(--dv-texto); text-decoration: none; }
.dv-boton.deshabilitado { opacity: .4; pointer-events: none; }

.dv-panel { background: #fff; border: 1px solid var(--dv-borde); border-radius: 12px; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); overflow: hidden; }

.dv-datos { display: grid; grid-template-columns: repeat(4, 1fr); border-bottom: 1px solid var(--dv-borde); }
.dv-dato { padding: 16px 20px; border-right: 1px solid var(--dv-borde); min-width: 0; }
.dv-dato:last-child { border-right: 0; }
.dv-etiqueta { font-size: 12px; font-weight: 600; color: var(--dv-texto-suave); text-transform: uppercase; letter-spacing: .04em; }
.dv-dato-valor { font-size: 16px; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 8px; font-variant-numeric: tabular-nums; }
.dv-dato-valor i { font-size: 16px; }
@media (max-width: 767px) {
    .dv-datos { grid-template-columns: 1fr 1fr; }
    .dv-dato:nth-child(2) { border-right: 0; }
    .dv-dato:nth-child(-n+2) { border-bottom: 1px solid var(--dv-borde); }
    .dv-dato-valor { font-size: 14px; overflow-wrap: anywhere; }
}

.dv-tabla { width: 100%; border-collapse: collapse; margin: 0; font-size: 14px; }
.dv-tabla th { text-align: left; font-size: 12px; font-weight: 600; color: var(--dv-texto-suave); text-transform: uppercase; letter-spacing: .04em; padding: 12px 20px; border-bottom: 1px solid var(--dv-borde); background: #fbfcfd; }
.dv-tabla td { padding: 14px 20px; border-bottom: 1px solid #f0f2f5; vertical-align: middle; }
.dv-tabla tr:last-child td { border-bottom: 0; }
.dv-tabla .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.dv-producto { font-weight: 600; text-transform: uppercase; font-size: 14px; }
.dv-codigo { font-size: 12px; color: var(--dv-texto-suave); margin-top: 2px; font-variant-numeric: tabular-nums; }
.dv-cantidad { font-weight: 600; }

.dv-pie { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 18px 20px; border-top: 1px solid var(--dv-borde); background: #fbfcfd; flex-wrap: wrap; }
.dv-nota { font-size: 12px; color: var(--dv-texto-suave); max-width: 420px; }
.dv-total { display: flex; align-items: baseline; gap: 12px; margin-left: auto; }
.dv-total span { font-size: 14px; color: var(--dv-texto-suave); }
.dv-total strong { font-size: 28px; font-weight: 700; font-variant-numeric: tabular-nums; }
.dv-vacio { padding: 40px 20px; text-align: center; color: var(--dv-texto-suave); font-size: 14px; }

@media (max-width: 600px) {
    .dv { padding-top: 20px; }
    .dv-tabla th, .dv-tabla td, .dv-dato, .dv-pie { padding-left: 14px; padding-right: 14px; }
    .dv-tabla .ocultar-celular { display: none; }
}

@media print {
    header, footer, nav, .dv-volver, .dv-acciones { display: none !important; }
    .dv { background: #fff; padding: 0; }
    .dv-panel { box-shadow: none; }
}
</style>

<section class="dv">
    <div class="container">
        <div class="dv-contenedor">

            <a class="dv-volver" href="<?php echo base_url() ?>pedidos"><i class="ti-arrow-left"></i> <?php echo $es_hoy ? 'Ventas de hoy' : 'Ventas' ?></a>

            <div class="dv-encabezado">
                <div>
                    <h1>Venta #<?php echo (int)$pedido->idpedido ?></h1>
                    <div class="dv-subtitulo"><?php echo $fecha_texto ?> · <?php echo $hora_texto ?></div>
                </div>
                <div class="dv-acciones">
                    <a class="dv-boton<?php echo $anterior ? '' : ' deshabilitado' ?>" href="<?php echo $anterior ? base_url().'pedido_detalles/'.(int)$anterior->idpedido : '#' ?>" title="Venta anterior"><i class="ti-angle-left"></i></a>
                    <a class="dv-boton<?php echo $siguiente ? '' : ' deshabilitado' ?>" href="<?php echo $siguiente ? base_url().'pedido_detalles/'.(int)$siguiente->idpedido : '#' ?>" title="Venta siguiente"><i class="ti-angle-right"></i></a>
                    <button type="button" class="dv-boton" onclick="window.print()"><i class="ti-printer"></i> Imprimir</button>
                </div>
            </div>

            <div class="dv-panel">
                <div class="dv-datos">
                    <div class="dv-dato">
                        <div class="dv-etiqueta">Hora</div>
                        <div class="dv-dato-valor"><i class="ti-time" style="color: var(--dv-texto-suave)"></i><?php echo $hora_texto ?></div>
                    </div>
                    <div class="dv-dato">
                        <div class="dv-etiqueta">Medio de pago</div>
                        <div class="dv-dato-valor"><i class="<?php echo $medio['icono'] ?>" style="color: <?php echo $medio['color'] ?>"></i><?php echo htmlspecialchars($pedido->modo_de_pago) ?></div>
                    </div>
                    <div class="dv-dato">
                        <div class="dv-etiqueta">Productos</div>
                        <div class="dv-dato-valor"><?php echo count($items) ?></div>
                    </div>
                    <div class="dv-dato">
                        <div class="dv-etiqueta">Unidades</div>
                        <div class="dv-dato-valor"><?php echo number_format($unidades, 0, ',', '.') ?></div>
                    </div>
                </div>

                <?php if (count($items) == 0) { ?>
                    <div class="dv-vacio">Esta venta no tiene productos registrados.</div>
                <?php } else { ?>
                    <table class="dv-tabla">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="num">Cantidad</th>
                                <?php if ($mostrar_precios) { ?>
                                    <th class="num ocultar-celular">Precio</th>
                                    <th class="num">Subtotal</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item) { ?>
                                <tr>
                                    <td>
                                        <div class="dv-producto"><?php echo htmlspecialchars($item->nombre ? $item->nombre : 'Producto #'.$item->id_articu) ?></div>
                                        <div class="dv-codigo"><?php echo htmlspecialchars($item->codigo) ?></div>
                                    </td>
                                    <td class="num dv-cantidad"><?php echo number_format($item->cantidad, 0, ',', '.') ?></td>
                                    <?php if ($mostrar_precios) { ?>
                                        <td class="num ocultar-celular"><?php echo dv_pesos($item->precio_final) ?></td>
                                        <td class="num"><?php echo dv_pesos($item->cantidad * $item->precio_final) ?></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>

                <div class="dv-pie">
                    <?php if (!$mostrar_precios && count($items) > 0) { ?>
                        <div class="dv-nota"><i class="ti-info-alt"></i> Los precios de los productos cambiaron desde esta venta, por eso se muestra solo el total cobrado.</div>
                    <?php } ?>
                    <div class="dv-total">
                        <span>Total cobrado</span>
                        <strong><?php echo dv_pesos($pedido->total) ?></strong>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
