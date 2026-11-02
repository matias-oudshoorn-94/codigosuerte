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

// Totales del día por medio de pago
$medios = array(
    'Efectivo'                  => array('titulo' => 'Efectivo',          'icono' => 'ti-money',        'total' => 0, 'ventas' => 0),
    'Tarjeta de crédito/débito' => array('titulo' => 'Tarjeta',           'icono' => 'ti-credit-card',  'total' => 0, 'ventas' => 0),
    'Medio electrónico'         => array('titulo' => 'Medio electrónico', 'icono' => 'ti-mobile',       'total' => 0, 'ventas' => 0),
    'Transferencia/QR'          => array('titulo' => 'Transferencia/QR',  'icono' => 'ti-layout-grid2', 'total' => 0, 'ventas' => 0),
);

$cantidad_pedidos = 0;
$total = 0;
foreach ($rows_pedidos as $pedido)
{
    $cantidad_pedidos++;
    $total += $pedido->total;
    if (isset($medios[$pedido->modo_de_pago]))
    {
        $medios[$pedido->modo_de_pago]['total'] += $pedido->total;
        $medios[$pedido->modo_de_pago]['ventas']++;
    }
}

$total_efectivo      = $medios['Efectivo']['total'];
$total_tarjeta       = $medios['Tarjeta de crédito/débito']['total'];
$total_electronico   = $medios['Medio electrónico']['total'];
$total_transferencia = $medios['Transferencia/QR']['total'];

$cantidad_impresoras_a_declarar = count($rows_impresoras);

function cc_pesos($valor)
{
    return '$ '.number_format($valor, 2, ',', '.');
}

$dias  = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
$meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
$fecha_hoy = $dias[date('w')].' '.date('j').' de '.$meses[date('n')];
?>

<style type="text/css">
.cc {
    --cc-verde: #47b475;
    --cc-verde-oscuro: #3a9862;
    --cc-verde-claro: #eaf6ef;
    --cc-texto: #1f2933;
    --cc-texto-suave: #6b7785;
    --cc-borde: #e3e8ee;
    --cc-fondo: #f5f7f9;
    --cc-rojo: #d64545;
    --cc-rojo-claro: #fbe9e9;
    background: var(--cc-fondo);
    padding: 32px 0 60px;
    color: var(--cc-texto);
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.cc * { box-sizing: border-box; }
/* El tema global agrega márgenes y mayúsculas a botones, campos y títulos: se anulan acá. */
.cc button, .cc input, .cc h1, .cc h2, .cc h3, .cc p { margin-top: 0; margin-bottom: 0; }
.cc button { font-family: inherit; line-height: 1.2; letter-spacing: 0; text-transform: none; }
.cc input::-webkit-input-placeholder { text-transform: none; font-weight: 400; letter-spacing: 0; font-size: 15px; color: #8a95a1; }
.cc input::-moz-placeholder { text-transform: none; font-weight: 400; letter-spacing: 0; font-size: 15px; color: #8a95a1; }

.cc-encabezado { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.cc-encabezado h1 { font-size: 26px; font-weight: 600; color: var(--cc-texto); letter-spacing: 0; text-transform: none; }
.cc-encabezado .cc-fecha { color: var(--cc-texto-suave); font-size: 15px; margin-top: 4px; }
.cc-encabezado p { color: var(--cc-texto-suave); font-size: 14px; }

.cc-grid {
    display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 24px; align-items: start;
    grid-template-areas: "resumen cierre" "ventas cierre";
}
.cc-area-resumen { grid-area: resumen; min-width: 0; }
.cc-area-ventas { grid-area: ventas; min-width: 0; }
.cc-cierre { grid-area: cierre; }
/* En pantallas chicas los pasos para cerrar van antes de la lista de ventas */
@media (max-width: 991px) { .cc-grid { grid-template-columns: 1fr; grid-template-areas: "resumen" "cierre" "ventas"; } }

.cc-panel { background: #fff; border: 1px solid var(--cc-borde); border-radius: 12px; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
.cc-panel-cabecera { padding: 18px 20px 14px; border-bottom: 1px solid var(--cc-borde); display: flex; align-items: center; justify-content: space-between; gap: 10px; }
.cc-panel-cabecera h2 { font-size: 16px; font-weight: 600; color: var(--cc-texto); text-transform: none; letter-spacing: 0; }
.cc-cuerpo { padding: 20px; }
.cc-etiqueta { font-size: 12px; font-weight: 600; color: var(--cc-texto-suave); text-transform: uppercase; letter-spacing: .04em; }
.cc-chip { font-size: 12px; font-weight: 600; background: var(--cc-verde-claro); color: var(--cc-verde-oscuro); padding: 3px 10px; border-radius: 999px; white-space: nowrap; }

/* Resumen */
.cc-total { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.cc-total-monto { font-size: 40px; font-weight: 700; line-height: 1.1; margin-top: 6px; font-variant-numeric: tabular-nums; }
.cc-metricas { display: flex; gap: 28px; }
.cc-metrica-valor { font-size: 22px; font-weight: 600; margin-top: 4px; font-variant-numeric: tabular-nums; }

.cc-medios { margin-top: 24px; display: flex; flex-direction: column; gap: 14px; }
.cc-medio-fila { display: grid; grid-template-columns: 36px minmax(0, 1fr) auto; gap: 12px; align-items: center; }
.cc-medio-icono { width: 36px; height: 36px; border-radius: 10px; background: var(--cc-fondo); display: flex; align-items: center; justify-content: center; color: var(--cc-texto-suave); font-size: 16px; }
.cc-medio-nombre { font-size: 14px; font-weight: 600; display: flex; justify-content: space-between; gap: 8px; }
.cc-medio-nombre span { font-weight: 400; color: var(--cc-texto-suave); font-size: 12px; }
.cc-barra { height: 6px; background: #eef1f4; border-radius: 999px; margin-top: 6px; overflow: hidden; }
.cc-barra div { height: 100%; background: var(--cc-verde); border-radius: 999px; }
.cc-medio-monto { font-size: 15px; font-weight: 600; text-align: right; font-variant-numeric: tabular-nums; min-width: 110px; }
.cc-medio-fila.cero { opacity: .55; }

/* Ventas del día */
.cc-ventas { max-height: 360px; overflow-y: auto; }
.cc-venta { display: grid; grid-template-columns: 70px minmax(0, 1fr) auto; gap: 12px; padding: 11px 20px; border-bottom: 1px solid #f0f2f5; font-size: 14px; align-items: center; }
.cc-venta:last-child { border-bottom: 0; }
.cc-venta-hora { color: var(--cc-texto-suave); font-variant-numeric: tabular-nums; }
.cc-venta-medio { color: var(--cc-texto-suave); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cc-venta a { color: var(--cc-texto); }
.cc-venta-total { font-weight: 600; text-align: right; font-variant-numeric: tabular-nums; }
.cc-vacio { padding: 36px 20px; text-align: center; color: var(--cc-texto-suave); font-size: 14px; }
.cc-vacio i { display: block; font-size: 28px; margin-bottom: 8px; opacity: .5; }

/* Pasos */
.cc-cierre { position: sticky; top: 80px; }
@media (max-width: 991px) { .cc-cierre { position: static; } }
.cc-paso { padding: 20px; border-bottom: 1px solid var(--cc-borde); }
.cc-paso:last-child { border-bottom: 0; }
.cc-paso-titulo { display: flex; align-items: center; gap: 10px; font-size: 15px; font-weight: 600; margin-bottom: 4px; }
.cc-paso-numero { width: 26px; height: 26px; border-radius: 50%; background: var(--cc-texto); color: #fff; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; flex: none; }
.cc-paso.hecho .cc-paso-numero { background: var(--cc-verde); }
.cc-paso.bloqueado { opacity: .5; }
.cc-paso-ayuda { font-size: 13px; color: var(--cc-texto-suave); margin: 0 0 14px 36px; }

.cc-impresora { border: 1px solid var(--cc-borde); border-radius: 10px; padding: 14px; margin-top: 10px; }
.cc-impresora-cabecera { display: flex; justify-content: space-between; gap: 10px; align-items: baseline; }
.cc-impresora-codigo { font-weight: 600; font-size: 15px; }
.cc-impresora-actual { font-size: 13px; color: var(--cc-texto-suave); }
.cc-impresora-actual b { color: var(--cc-texto); font-variant-numeric: tabular-nums; }
.cc-impresora-form { display: flex; gap: 8px; margin-top: 12px; }
.cc .cc-impresora-form input {
    flex: 1 1 auto; min-width: 0; height: 44px; padding: 0 12px; border: 1px solid var(--cc-borde); border-radius: 10px; background: var(--cc-fondo);
    font-size: 16px; font-weight: 600; color: var(--cc-texto); -moz-appearance: textfield;
}
.cc .cc-impresora-form input::-webkit-outer-spin-button, .cc .cc-impresora-form input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.cc .cc-impresora-form input:focus { outline: none; background: #fff; border-color: var(--cc-verde); box-shadow: 0 0 0 3px rgba(71, 180, 117, .18); }
.cc-impresora-form input.error { border-color: var(--cc-rojo); background: var(--cc-rojo-claro); }
.cc-boton { height: 44px; padding: 0 18px; border: 0; border-radius: 10px; background: var(--cc-verde); color: #fff; font-size: 15px; font-weight: 600; cursor: pointer; white-space: nowrap; transition: background .15s; }
.cc-boton:hover { background: var(--cc-verde-oscuro); }
.cc-boton:disabled { background: #c5ccd3; cursor: not-allowed; }
.cc-impresora-diferencia { font-size: 13px; color: var(--cc-texto-suave); margin-top: 8px; min-height: 18px; }
.cc-impresora-diferencia.error { color: var(--cc-rojo); }
.cc-impresora.hecha { background: var(--cc-verde-claro); border-color: #bfe3cd; }
.cc-impresora-hecha { display: flex; gap: 10px; align-items: center; margin-top: 8px; font-size: 14px; color: var(--cc-verde-oscuro); }
.cc-impresora-hecha i { font-size: 18px; }

.cc-desperdicio { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px; border-radius: 10px; margin-bottom: 14px; background: var(--cc-verde-claro); color: var(--cc-verde-oscuro); }
.cc-desperdicio.alto { background: var(--cc-rojo-claro); color: var(--cc-rojo); }
.cc-desperdicio strong { font-size: 22px; font-variant-numeric: tabular-nums; }
.cc-desperdicio span { font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
.cc-cerrar { width: 100%; height: 54px; font-size: 17px; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 8px; }
.cc-mensaje { font-size: 13px; color: var(--cc-texto-suave); margin-top: 10px; text-align: center; }
.cc-mensaje.error { color: var(--cc-rojo); }

.cc-cargando { position: fixed; inset: 0; background: rgba(245, 247, 249, .75); display: none; align-items: center; justify-content: center; z-index: 9999; }
.cc-cargando.visible { display: flex; }
.cc-cargando div { width: 40px; height: 40px; border: 4px solid var(--cc-borde); border-top-color: var(--cc-verde); border-radius: 50%; animation: cc-girar .8s linear infinite; }
@keyframes cc-girar { to { transform: rotate(360deg); } }

@media (max-width: 600px) {
    .cc { padding-top: 20px; }
    .cc-total-monto { font-size: 32px; }
    .cc-cuerpo, .cc-paso, .cc-panel-cabecera { padding-left: 14px; padding-right: 14px; }
    .cc-venta { padding-left: 14px; padding-right: 14px; grid-template-columns: 52px minmax(0, 1fr) auto; }
    .cc-medio-monto { min-width: 0; }
    .cc-paso-ayuda { margin-left: 0; }
}
</style>

<div class="cc-cargando" id="loader"><div></div></div>

<section class="cc">
    <div class="container">

        <div class="cc-encabezado">
            <div>
                <h1>Cerrar caja</h1>
                <div class="cc-fecha"><?php echo $fecha_hoy ?></div>
            </div>
            <p><a href="<?php echo base_url() ?>">Inicio</a> / Cerrar caja</p>
        </div>

        <div class="cc-grid">

            <!-- Resumen del día -->
                <div class="cc-panel cc-area-resumen">
                    <div class="cc-cuerpo">
                        <div class="cc-total">
                            <div>
                                <div class="cc-etiqueta">Total del día</div>
                                <div class="cc-total-monto"><?php echo cc_pesos($total) ?></div>
                            </div>
                            <div class="cc-metricas">
                                <div>
                                    <div class="cc-etiqueta">Ventas</div>
                                    <div class="cc-metrica-valor"><?php echo $cantidad_pedidos ?></div>
                                </div>
                                <div>
                                    <div class="cc-etiqueta">Impresiones aprox.</div>
                                    <div class="cc-metrica-valor" id="impresiones">…</div>
                                </div>
                            </div>
                        </div>

                        <div class="cc-medios">
                            <?php foreach ($medios as $medio) {
                                $porcentaje = $total > 0 ? round($medio['total'] * 100 / $total) : 0; ?>
                                <div class="cc-medio-fila<?php echo $medio['total'] == 0 ? ' cero' : '' ?>">
                                    <div class="cc-medio-icono"><i class="<?php echo $medio['icono'] ?>"></i></div>
                                    <div>
                                        <div class="cc-medio-nombre">
                                            <?php echo $medio['titulo'] ?>
                                            <span><?php echo $medio['ventas'] ?> venta<?php echo $medio['ventas'] == 1 ? '' : 's' ?> · <?php echo $porcentaje ?>%</span>
                                        </div>
                                        <div class="cc-barra"><div style="width: <?php echo $porcentaje ?>%"></div></div>
                                    </div>
                                    <div class="cc-medio-monto"><?php echo cc_pesos($medio['total']) ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Ventas de hoy -->
                <div class="cc-panel cc-area-ventas">
                    <div class="cc-panel-cabecera">
                        <h2>Ventas de hoy</h2>
                        <span class="cc-chip"><?php echo $cantidad_pedidos ?></span>
                    </div>
                    <?php if ($cantidad_pedidos == 0) { ?>
                        <div class="cc-vacio"><i class="ti-receipt"></i>Todavía no hay ventas hoy.</div>
                    <?php } else { ?>
                        <div class="cc-ventas">
                            <?php foreach (array_reverse($rows_pedidos) as $pedido) { ?>
                                <div class="cc-venta">
                                    <span class="cc-venta-hora"><?php echo $pedido->fecha_hora ? date('H:i', strtotime($pedido->fecha_hora)) : '' ?></span>
                                    <span class="cc-venta-medio">
                                        <a href="<?php echo base_url() ?>pedido_detalles/<?php echo (int)$pedido->idpedido ?>">#<?php echo (int)$pedido->idpedido ?></a>
                                        · <?php echo htmlspecialchars($pedido->modo_de_pago) ?>
                                    </span>
                                    <span class="cc-venta-total"><?php echo cc_pesos($pedido->total) ?></span>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

            <!-- Pasos para cerrar -->
            <div class="cc-panel cc-cierre">
                <div class="cc-paso<?php echo $cantidad_impresoras_a_declarar == 0 ? ' hecho' : '' ?>" id="paso-impresoras">
                    <div class="cc-paso-titulo"><span class="cc-paso-numero">1</span> Declarar impresoras</div>
                    <p class="cc-paso-ayuda">Ingresá el contador que muestra cada impresora ahora.</p>

                    <?php if ($cantidad_impresoras_a_declarar == 0) { ?>
                        <div class="cc-impresora-actual">No hay impresoras para declarar.</div>
                    <?php } ?>

                    <?php foreach ($rows_impresoras as $impresora) { ?>
                        <div class="cc-impresora" data-codigo="<?php echo htmlspecialchars($impresora->codigo) ?>" data-copias="<?php echo (int)$impresora->copias ?>">
                            <div class="cc-impresora-cabecera">
                                <span class="cc-impresora-codigo"><i class="ti-printer"></i> <?php echo htmlspecialchars($impresora->codigo) ?></span>
                                <span class="cc-impresora-actual">Último contador: <b><?php echo number_format($impresora->copias, 0, ',', '.') ?></b></span>
                            </div>
                            <div class="cc-impresora-form">
                                <input type="number" inputmode="numeric" min="<?php echo (int)$impresora->copias + 1 ?>" placeholder="Contador actual" aria-label="Contador actual de <?php echo htmlspecialchars($impresora->codigo) ?>">
                                <button type="button" class="cc-boton">Declarar</button>
                            </div>
                            <div class="cc-impresora-diferencia"></div>
                        </div>
                    <?php } ?>
                </div>

                <div class="cc-paso<?php echo $cantidad_impresoras_a_declarar > 0 ? ' bloqueado' : '' ?>" id="paso-cerrar">
                    <div class="cc-paso-titulo"><span class="cc-paso-numero">2</span> Cerrar la caja</div>
                    <p class="cc-paso-ayuda">Revisá el resumen y confirmá. Después del cierre no se pueden cargar más ventas hoy.</p>

                    <div id="desperdicio"></div>

                    <button type="button" class="cc-boton cc-cerrar" id="boton_cerrar" <?php echo $cantidad_impresoras_a_declarar > 0 ? 'disabled' : '' ?>>
                        <i class="ti-lock"></i> Cerrar caja de hoy · <?php echo cc_pesos($total) ?>
                    </button>
                    <div class="cc-mensaje" id="cc-mensaje">
                        <?php if ($cantidad_impresoras_a_declarar > 0) { ?>
                            Declará <?php echo $cantidad_impresoras_a_declarar == 1 ? 'la impresora' : 'las '.$cantidad_impresoras_a_declarar.' impresoras' ?> para continuar.
                        <?php } ?>
                    </div>
                    <div id="cierre_de_caja" style="display: none;"></div>
                </div>
            </div>

        </div>
    </div>
</section>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>

<script type="text/javascript">
(function () {
    'use strict';

    var URL_BASE = '<?php echo base_url() ?>';
    var CODIGO_CLIENTE = '<?php echo (int)$sucursal_id ?>';
    var RESUMEN = {
        efectivo: '<?php echo $total_efectivo ?>',
        tarjeta: '<?php echo $total_tarjeta ?>',
        electronico: '<?php echo $total_electronico ?>',
        transferencia: '<?php echo $total_transferencia ?>',
        total: '<?php echo $total ?>',
        cantidad_ventas: '<?php echo $cantidad_pedidos ?>'
    };

    var pendientes = <?php echo (int)$cantidad_impresoras_a_declarar ?>;
    var impresionesReales = 0;   // suma de lo declarado hoy en todas las impresoras
    var impresionesAprox = 0;
    var desperdicio = 0;
    var cerrando = false;

    var loader = document.getElementById('loader');
    function cargando(si) { loader.classList.toggle('visible', !!si); }
    function numero(n) { return new Intl.NumberFormat('es-AR').format(n); }

    // Impresiones aproximadas según las ventas del día
    $.post(URL_BASE + 'sitio/caja/impresiones_aprox', { codigo_cliente: CODIGO_CLIENTE })
        .done(function (data) {
            impresionesAprox = parseInt(data, 10) || 0;
            document.getElementById('impresiones').textContent = numero(impresionesAprox);
        })
        .fail(function () { document.getElementById('impresiones').textContent = '—'; });

    // ---------- Paso 1: impresoras ----------

    Array.prototype.forEach.call(document.querySelectorAll('.cc-impresora'), function (tarjeta) {
        var codigo = tarjeta.getAttribute('data-codigo');
        var copias = parseInt(tarjeta.getAttribute('data-copias'), 10) || 0;
        var input = tarjeta.querySelector('input');
        var boton = tarjeta.querySelector('button');
        var diferencia = tarjeta.querySelector('.cc-impresora-diferencia');

        // Muestra en vivo cuántas impresiones suma el valor ingresado
        input.addEventListener('input', function () {
            input.classList.remove('error');
            diferencia.classList.remove('error');
            var valor = parseInt(input.value, 10);
            if (!valor) { diferencia.textContent = ''; return; }
            if (valor <= copias) {
                diferencia.textContent = 'Tiene que ser mayor a ' + numero(copias);
                diferencia.classList.add('error');
            } else {
                diferencia.textContent = 'Suma ' + numero(valor - copias) + ' impresiones de hoy';
            }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); declarar(); }
        });
        boton.addEventListener('click', declarar);

        function declarar() {
            var valor = parseInt(input.value, 10);
            if (!valor || valor <= copias) {
                input.classList.add('error');
                diferencia.classList.add('error');
                diferencia.textContent = 'El contador tiene que ser mayor a ' + numero(copias);
                input.focus();
                return;
            }
            if (!confirm('¿Confirmar contador ' + numero(valor) + ' para la impresora ' + codigo + '?')) return;

            boton.disabled = true;
            cargando(true);
            $.post(URL_BASE + 'sitio/caja/declarar_impresora', {
                codigo_cliente: CODIGO_CLIENTE,
                codigo_impresora: codigo,
                cantidad_impresiones_hoy: copias,
                cantidad_total: valor
            })
            .done(function (data) {
                cargando(false);
                if (String(data).indexOf('Error:') !== -1) {
                    boton.disabled = false;
                    diferencia.classList.add('error');
                    diferencia.textContent = 'No se pudo declarar la impresora. Intentá de nuevo.';
                    return;
                }
                var sumadas = valor - copias;
                impresionesReales += sumadas;
                tarjeta.classList.add('hecha');
                tarjeta.querySelector('.cc-impresora-form').remove();
                diferencia.remove();
                var hecho = document.createElement('div');
                hecho.className = 'cc-impresora-hecha';
                hecho.innerHTML = '<i class="ti-check"></i><span>Contador <b>' + numero(valor) + '</b> · ' + numero(sumadas) + ' impresiones hoy</span>';
                tarjeta.appendChild(hecho);

                pendientes--;
                if (pendientes === 0) habilitarCierre();
                else {
                    var siguiente = document.querySelector('.cc-impresora:not(.hecha) input');
                    if (siguiente) siguiente.focus();
                }
            })
            .fail(function () {
                cargando(false);
                boton.disabled = false;
                diferencia.classList.add('error');
                diferencia.textContent = 'Error al conectar con el servidor.';
            });
        }
    });

    function habilitarCierre() {
        document.getElementById('paso-impresoras').classList.add('hecho');
        document.getElementById('paso-cerrar').classList.remove('bloqueado');
        document.getElementById('boton_cerrar').disabled = false;
        document.getElementById('cc-mensaje').textContent = '';

        if (impresionesReales > 0) {
            // Misma fórmula que antes: % de impresiones reales que no corresponden a ventas
            desperdicio = 100 - (impresionesAprox * 100 / impresionesReales);
            var alto = desperdicio > 10;
            document.getElementById('desperdicio').innerHTML =
                '<div class="cc-desperdicio' + (alto ? ' alto' : '') + '">' +
                    '<span><i class="' + (alto ? 'ti-face-sad' : 'ti-face-smile') + '"></i> Desperdicio de hoy</span>' +
                    '<strong>' + desperdicio.toFixed(2).replace('.', ',') + '%</strong>' +
                '</div>';
        }
    }

    // ---------- Paso 2: cerrar ----------

    document.getElementById('boton_cerrar').addEventListener('click', function () {
        if (cerrando || pendientes > 0) return;
        if (!confirm('¿Confirmás el cierre de caja de hoy?')) return;

        cerrando = true;
        this.disabled = true;
        cargando(true);

        $.post(URL_BASE + 'sitio/caja/cerrar', {
            codigo_cliente: CODIGO_CLIENTE,
            efectivo: RESUMEN.efectivo,
            tarjeta: RESUMEN.tarjeta,
            electronico: RESUMEN.electronico,
            transferencia: RESUMEN.transferencia,
            total: RESUMEN.total,
            impresiones_aprox: impresionesAprox,
            impresiones_reales: impresionesReales,
            cantidad_ventas: RESUMEN.cantidad_ventas,
            desperdicio: desperdicio
        })
        .done(function (data) {
            if (String(data).indexOf('caja_cerrada') !== -1) {
                location.href = URL_BASE + 'caja_cerrada';
                return;
            }
            fallo();
        })
        .fail(fallo);
    });

    function fallo() {
        cargando(false);
        cerrando = false;
        document.getElementById('boton_cerrar').disabled = false;
        var mensaje = document.getElementById('cc-mensaje');
        mensaje.classList.add('error');
        mensaje.textContent = 'No se pudo cerrar la caja. Intentá de nuevo.';
    }

    var primero = document.querySelector('.cc-impresora input');
    if (primero) primero.focus();
})();
</script>
