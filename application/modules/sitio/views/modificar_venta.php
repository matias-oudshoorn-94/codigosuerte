<?php
$base_url = base_url()."application/views/layouts";

$json_flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

// Productos para el buscador
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
$productos_json = json_encode($productos_js, $json_flags);
if ($productos_json === false)
{
    foreach ($productos_js as &$p) { $p['nombre'] = utf8_encode($p['nombre']); $p['codigo'] = utf8_encode($p['codigo']); }
    unset($p);
    $productos_json = json_encode($productos_js, $json_flags);
}

// Productos de la venta tal como está guardada
$items_js = array();
foreach ($items as $item)
{
    $items_js[] = array('id' => (int)$item->id_articu, 'cantidad' => (int)$item->cantidad);
}

$iconos_pago = array(
    'Efectivo'                  => array('titulo' => 'Efectivo',          'icono' => 'ti-money'),
    'Tarjeta de crédito/débito' => array('titulo' => 'Tarjeta',           'icono' => 'ti-credit-card'),
    'Medio electrónico'         => array('titulo' => 'Medio electrónico', 'icono' => 'ti-mobile'),
    'Transferencia/QR'          => array('titulo' => 'Transferencia/QR',  'icono' => 'ti-layout-grid2'),
);
?>

<style type="text/css">
.mv {
    --mv-verde: #47b475;
    --mv-verde-oscuro: #3a9862;
    --mv-verde-claro: #eaf6ef;
    --mv-texto: #1f2933;
    --mv-texto-suave: #6b7785;
    --mv-borde: #e3e8ee;
    --mv-fondo: #f5f7f9;
    --mv-rojo: #d64545;
    --mv-ambar: #c98a0b;
    --mv-azul: #2f6fb0;
    background: var(--mv-fondo);
    padding: 32px 0 60px;
    color: var(--mv-texto);
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.mv * { box-sizing: border-box; }
.mv button, .mv input, .mv h1, .mv h2, .mv p { margin-top: 0; margin-bottom: 0; }
.mv button { font-family: inherit; line-height: 1.2; letter-spacing: 0; text-transform: none; }
.mv input::-webkit-input-placeholder { text-transform: none; font-weight: 400; letter-spacing: 0; font-size: 15px; color: #8a95a1; }
.mv input::-moz-placeholder { text-transform: none; font-weight: 400; letter-spacing: 0; font-size: 15px; color: #8a95a1; }

.mv-volver { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; color: var(--mv-texto-suave); margin-bottom: 14px; }
.mv-volver:hover { color: var(--mv-verde-oscuro); text-decoration: none; }
.mv-encabezado { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.mv-encabezado h1 { font-size: 26px; font-weight: 600; color: var(--mv-texto); letter-spacing: 0; text-transform: none; }
.mv-subtitulo { color: var(--mv-texto-suave); font-size: 15px; margin-top: 4px; }
.mv-aviso { display: flex; gap: 10px; align-items: flex-start; padding: 12px 16px; border-radius: 10px; background: #eaf2fb; color: var(--mv-azul); font-size: 13px; margin-bottom: 20px; }
.mv-aviso i { margin-top: 2px; }

.mv-grid { display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 24px; align-items: start; }
@media (max-width: 991px) { .mv-grid { grid-template-columns: 1fr; } }

.mv-panel { background: #fff; border: 1px solid var(--mv-borde); border-radius: 12px; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
.mv-panel-cabecera { padding: 16px 20px; border-bottom: 1px solid var(--mv-borde); display: flex; align-items: center; justify-content: space-between; gap: 10px; }
.mv-panel-cabecera h2 { font-size: 16px; font-weight: 600; color: var(--mv-texto); text-transform: none; letter-spacing: 0; }
.mv-chip { font-size: 12px; font-weight: 600; background: var(--mv-verde-claro); color: var(--mv-verde-oscuro); padding: 3px 10px; border-radius: 999px; }

/* Buscador */
.mv-buscador { position: relative; padding: 14px 20px; border-bottom: 1px solid var(--mv-borde); }
.mv-buscador i { position: absolute; left: 36px; top: 50%; transform: translateY(-50%); color: var(--mv-texto-suave); pointer-events: none; }
.mv .mv-buscador input { width: 100%; height: 46px; padding: 0 16px 0 46px; border: 1px solid var(--mv-borde); border-radius: 10px; background: var(--mv-fondo); font-size: 16px; color: var(--mv-texto); }
.mv .mv-buscador input:focus { outline: none; background: #fff; border-color: var(--mv-verde); box-shadow: 0 0 0 3px rgba(71, 180, 117, .18); }
.mv-lista { max-height: calc(100vh - 330px); min-height: 280px; overflow-y: auto; }
.mv-producto { display: grid; grid-template-columns: minmax(0, 1fr) auto auto 40px; gap: 14px; align-items: center; padding: 11px 20px; border-bottom: 1px solid #f0f2f5; cursor: pointer; }
.mv-producto:hover { background: var(--mv-verde-claro); }
.mv-producto-nombre { font-size: 14px; font-weight: 600; text-transform: uppercase; overflow-wrap: anywhere; }
.mv-producto-codigo { font-size: 12px; color: var(--mv-texto-suave); margin-top: 2px; }
.mv-producto-precio { font-size: 14px; font-weight: 600; white-space: nowrap; font-variant-numeric: tabular-nums; }
.mv-stock { font-size: 12px; font-weight: 600; padding: 3px 8px; border-radius: 999px; background: #eef1f4; color: var(--mv-texto-suave); white-space: nowrap; }
.mv-stock.sin { background: #fbe9e9; color: var(--mv-rojo); }
.mv-agregar { width: 36px; height: 36px; border-radius: 10px; border: 0; background: var(--mv-verde); color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.mv-agregar:hover { background: var(--mv-verde-oscuro); }
.mv-mas { padding: 12px 20px; font-size: 13px; color: var(--mv-texto-suave); text-align: center; }
.mv-vacio { padding: 40px 20px; text-align: center; color: var(--mv-texto-suave); font-size: 14px; }

/* Venta */
.mv-venta { position: sticky; top: 80px; }
@media (max-width: 991px) { .mv-venta { position: static; } }
.mv-item { padding: 14px 20px; border-bottom: 1px solid #f0f2f5; }
.mv-item-fila { display: flex; justify-content: space-between; gap: 10px; }
.mv-item-nombre { font-size: 14px; font-weight: 600; text-transform: uppercase; overflow-wrap: anywhere; }
.mv-item-unitario { font-size: 12px; color: var(--mv-texto-suave); margin-top: 2px; }
.mv-item-subtotal { font-size: 15px; font-weight: 600; white-space: nowrap; font-variant-numeric: tabular-nums; }
.mv-item-controles { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; gap: 10px; }
.mv-cantidad { display: inline-flex; align-items: center; border: 1px solid var(--mv-borde); border-radius: 10px; overflow: hidden; }
.mv-cantidad button { width: 36px; height: 36px; border: 0; background: #fff; font-size: 18px; cursor: pointer; color: var(--mv-texto); }
.mv-cantidad button:hover { background: var(--mv-fondo); }
.mv .mv-cantidad input { width: 72px; height: 36px; padding: 0 4px; border: 0; border-left: 1px solid var(--mv-borde); border-right: 1px solid var(--mv-borde); border-radius: 0; text-align: center; font-size: 15px; font-weight: 600; color: var(--mv-texto); background: #fff; -moz-appearance: textfield; }
.mv .mv-cantidad input::-webkit-outer-spin-button, .mv .mv-cantidad input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.mv .mv-cantidad input:focus { outline: none; background: var(--mv-verde-claro); }
.mv-cambio { font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 999px; }
.mv-cambio.nuevo { background: var(--mv-verde-claro); color: var(--mv-verde-oscuro); }
.mv-cambio.editado { background: #eaf2fb; color: var(--mv-azul); }
.mv-quitar { border: 0; background: transparent; color: var(--mv-texto-suave); font-size: 15px; cursor: pointer; padding: 8px; border-radius: 8px; margin-left: auto; }
.mv-quitar:hover { color: var(--mv-rojo); background: #fbe9e9; }
.mv-aviso-stock { font-size: 12px; color: var(--mv-ambar); margin-top: 6px; }
.mv-quitados { padding: 10px 20px; font-size: 12px; color: var(--mv-texto-suave); border-bottom: 1px solid #f0f2f5; background: #fcfcfd; }
.mv-quitados button { border: 0; background: none; color: var(--mv-azul); font-weight: 600; cursor: pointer; padding: 0 2px; font-size: 12px; }

.mv-pie { padding: 16px 20px 20px; border-top: 1px solid var(--mv-borde); }
.mv-etiqueta { font-size: 12px; font-weight: 600; color: var(--mv-texto-suave); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 8px; }
.mv-pagos { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; }
.mv-pago { display: flex; align-items: center; gap: 8px; min-height: 46px; padding: 8px 12px; border: 1px solid var(--mv-borde); border-radius: 10px; background: #fff; color: var(--mv-texto); font-size: 14px; font-weight: 600; text-align: left; cursor: pointer; }
.mv-pago i { font-size: 17px; color: var(--mv-texto-suave); }
.mv-pago:hover { border-color: #b8c2cc; }
.mv-pago.seleccionado { border-color: var(--mv-verde); background: var(--mv-verde-claro); box-shadow: inset 0 0 0 1px var(--mv-verde); }
.mv-pago.seleccionado i { color: var(--mv-verde-oscuro); }

.mv-totales { border-radius: 10px; background: var(--mv-fondo); padding: 12px 14px; margin-bottom: 14px; font-size: 14px; }
.mv-totales div { display: flex; justify-content: space-between; padding: 3px 0; font-variant-numeric: tabular-nums; }
.mv-totales .mv-original { color: var(--mv-texto-suave); }
.mv-totales .mv-nuevo { font-size: 22px; font-weight: 700; padding-top: 6px; }
.mv-totales .mv-nuevo span:first-child { font-size: 14px; font-weight: 600; align-self: center; }
.mv-diferencia { font-weight: 600; }
.mv-diferencia.sube { color: var(--mv-verde-oscuro); }
.mv-diferencia.baja { color: var(--mv-rojo); }

.mv-botones { display: grid; grid-template-columns: auto 1fr; gap: 8px; }
.mv-boton { height: 50px; padding: 0 18px; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; }
.mv-boton.secundario { border: 1px solid var(--mv-borde); background: #fff; color: var(--mv-texto); }
.mv-boton.secundario:hover { border-color: #b8c2cc; color: var(--mv-texto); text-decoration: none; }
.mv-boton.principal { border: 0; background: var(--mv-verde); color: #fff; }
.mv-boton.principal:hover { background: var(--mv-verde-oscuro); }
.mv-boton.principal:disabled { background: #c5ccd3; cursor: not-allowed; }
.mv-mensaje { font-size: 13px; margin-top: 10px; text-align: center; min-height: 18px; color: var(--mv-texto-suave); }
.mv-mensaje.error { color: var(--mv-rojo); }

@media (max-width: 600px) {
    .mv { padding-top: 20px; }
    .mv-producto { grid-template-columns: minmax(0, 1fr) auto 40px; padding: 11px 14px; }
    .mv-producto .mv-stock { grid-row: 2; grid-column: 1; justify-self: start; }
    .mv-item, .mv-pie, .mv-panel-cabecera, .mv-quitados { padding-left: 14px; padding-right: 14px; }
    .mv-buscador { padding: 12px 14px; }
    .mv-buscador i { left: 30px; }
}
</style>

<section class="mv">
    <div class="container">

        <a class="mv-volver" href="<?php echo base_url() ?>pedido_detalles/<?php echo (int)$pedido->idpedido ?>"><i class="ti-arrow-left"></i> Volver al detalle</a>

        <div class="mv-encabezado">
            <div>
                <h1>Modificar venta #<?php echo (int)$pedido->idpedido ?></h1>
                <div class="mv-subtitulo">Hoy a las <?php echo date('H:i', strtotime($pedido->fecha_hora)) ?> · Total original <?php echo '$ '.number_format($pedido->total, 2, ',', '.') ?></div>
            </div>
        </div>

        <div class="mv-aviso">
            <i class="ti-info-alt"></i>
            <span>Los cambios se aplican recién al tocar <b>Guardar cambios</b>. El total se recalcula con los precios actuales, y el stock y la caja del día se ajustan solos.</span>
        </div>

        <div class="mv-grid">
            <!-- Agregar productos -->
            <div class="mv-panel">
                <div class="mv-panel-cabecera">
                    <h2>Agregar productos</h2>
                </div>
                <div class="mv-buscador">
                    <i class="ti-search"></i>
                    <input type="text" id="mv-buscar" autocomplete="off" placeholder="Buscar por nombre o código…">
                </div>
                <div class="mv-lista" id="mv-lista"></div>
            </div>

            <!-- Venta editada -->
            <div class="mv-panel mv-venta">
                <div class="mv-panel-cabecera">
                    <h2>Productos de la venta</h2>
                    <span class="mv-chip" id="mv-unidades">0</span>
                </div>
                <div id="mv-quitados"></div>
                <div id="mv-items"></div>

                <div class="mv-pie">
                    <div class="mv-etiqueta">Medio de pago</div>
                    <div class="mv-pagos" id="mv-pagos">
                        <?php foreach ($medios_pago as $valor) {
                            $info = isset($iconos_pago[$valor]) ? $iconos_pago[$valor] : array('titulo' => $valor, 'icono' => 'ti-wallet'); ?>
                            <button type="button" class="mv-pago<?php echo $valor == $pedido->modo_de_pago ? ' seleccionado' : '' ?>" data-valor="<?php echo htmlspecialchars($valor) ?>">
                                <i class="<?php echo $info['icono'] ?>"></i><?php echo $info['titulo'] ?>
                            </button>
                        <?php } ?>
                    </div>

                    <div class="mv-totales">
                        <div class="mv-original"><span>Total original</span><span><?php echo '$ '.number_format($pedido->total, 2, ',', '.') ?></span></div>
                        <div><span>Diferencia</span><span class="mv-diferencia" id="mv-diferencia">$ 0,00</span></div>
                        <div class="mv-nuevo"><span>Total nuevo</span><span id="mv-total">$ 0,00</span></div>
                    </div>

                    <div class="mv-botones">
                        <a class="mv-boton secundario" href="<?php echo base_url() ?>pedido_detalles/<?php echo (int)$pedido->idpedido ?>">Cancelar</a>
                        <button type="button" class="mv-boton principal" id="mv-guardar" disabled><i class="ti-check"></i> Guardar cambios</button>
                    </div>
                    <div class="mv-mensaje" id="mv-mensaje">Todavía no hiciste cambios.</div>
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
    var ID_PEDIDO = <?php echo (int)$pedido->idpedido ?>;
    var TOTAL_ORIGINAL = <?php echo (float)$pedido->total ?>;
    var PAGO_ORIGINAL = <?php echo json_encode((string)$pedido->modo_de_pago, $json_flags) ?>;
    var PRODUCTOS = <?php echo $productos_json ?>;
    var ORIGINAL = <?php echo json_encode($items_js) ?>;
    var MAX_RESULTADOS = 60;

    var productosPorId = {};
    PRODUCTOS.forEach(function (p) {
        p.busqueda = normalizar(p.nombre + ' ' + p.codigo);
        productosPorId[p.id] = p;
    });

    // Estado editable: [{id, cantidad}] en el orden en que se muestran
    var cantidadOriginal = {};
    ORIGINAL.forEach(function (i) { cantidadOriginal[i.id] = (cantidadOriginal[i.id] || 0) + i.cantidad; });
    var items = Object.keys(cantidadOriginal).map(function (id) { return { id: parseInt(id, 10), cantidad: cantidadOriginal[id] }; });
    var medioPago = PAGO_ORIGINAL;
    var guardando = false;

    var $lista = document.getElementById('mv-lista');
    var $buscar = document.getElementById('mv-buscar');
    var $items = document.getElementById('mv-items');

    function normalizar(t) { return String(t || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, ''); }
    function escapar(t) {
        return String(t == null ? '' : t).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    var formato = new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' });
    function pesos(v) { return formato.format(Number(v) || 0); }
    function producto(id) { return productosPorId[id] || { id: id, nombre: 'Producto #' + id, codigo: '', precio: 0, stock: 0 }; }

    // ---------- Buscador ----------
    var resultados = [];
    function buscar() {
        var palabras = normalizar($buscar.value.trim()).split(/\s+/).filter(Boolean);
        var encontrados = palabras.length === 0 ? PRODUCTOS : PRODUCTOS.filter(function (p) {
            return palabras.every(function (w) { return p.busqueda.indexOf(w) !== -1; });
        });
        resultados = encontrados.slice(0, MAX_RESULTADOS);
        if (resultados.length === 0) {
            $lista.innerHTML = '<div class="mv-vacio">No se encontraron productos.</div>';
            return;
        }
        var html = resultados.map(function (p, i) {
            return '<div class="mv-producto" data-indice="' + i + '">' +
                '<div><div class="mv-producto-nombre">' + escapar(p.nombre) + '</div><div class="mv-producto-codigo">' + escapar(p.codigo) + '</div></div>' +
                '<span class="mv-stock' + (p.stock <= 0 ? ' sin' : '') + '">Stock ' + escapar(p.stock) + '</span>' +
                '<span class="mv-producto-precio">' + pesos(p.precio) + '</span>' +
                '<button type="button" class="mv-agregar" title="Agregar"><i class="ti-plus"></i></button>' +
            '</div>';
        }).join('');
        if (encontrados.length > resultados.length) {
            html += '<div class="mv-mas">Mostrando ' + resultados.length + ' de ' + encontrados.length + '. Escribí más para afinar la búsqueda.</div>';
        }
        $lista.innerHTML = html;
    }
    $buscar.addEventListener('input', buscar);
    $buscar.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && resultados[0]) {
            e.preventDefault();
            var exacto = PRODUCTOS.filter(function (p) { return String(p.codigo).toLowerCase() === $buscar.value.trim().toLowerCase(); })[0];
            agregar((exacto || resultados[0]).id);
            $buscar.value = '';
            buscar();
        }
    });
    $lista.addEventListener('click', function (e) {
        var fila = e.target.closest('.mv-producto');
        if (fila) agregar(resultados[parseInt(fila.getAttribute('data-indice'), 10)].id);
    });

    // ---------- Edición ----------
    function buscarItem(id) { return items.filter(function (i) { return i.id === id; })[0]; }

    function agregar(id) {
        var item = buscarItem(id);
        if (item) item.cantidad += 1;
        else items.push({ id: id, cantidad: 1 });
        pintar();
    }

    function cambiar(id, cantidad) {
        var item = buscarItem(id);
        if (!item) return;
        item.cantidad = Math.max(1, cantidad || 1);
        pintar();
    }

    function quitar(id) {
        items = items.filter(function (i) { return i.id !== id; });
        pintar();
    }

    function pintar() {
        var total = 0, unidades = 0;

        // Filas: se actualizan en el lugar para no perder el foco del campo que se edita
        var existentes = {};
        Array.prototype.forEach.call($items.querySelectorAll('.mv-item'), function (f) { existentes[f.getAttribute('data-id')] = f; });

        items.forEach(function (item, posicion) {
            var p = producto(item.id);
            var subtotal = p.precio * item.cantidad;
            total += subtotal;
            unidades += item.cantidad;

            var fila = existentes[item.id];
            delete existentes[item.id];
            if (!fila) {
                fila = document.createElement('div');
                fila.className = 'mv-item';
                fila.setAttribute('data-id', item.id);
                fila.innerHTML =
                    '<div class="mv-item-fila"><div><div class="mv-item-nombre">' + escapar(p.nombre) + '</div><div class="mv-item-unitario"></div></div><div class="mv-item-subtotal"></div></div>' +
                    '<div class="mv-item-controles">' +
                        '<div class="mv-cantidad"><button type="button" data-accion="menos">−</button><input type="number" min="1" inputmode="numeric" aria-label="Cantidad"><button type="button" data-accion="mas">+</button></div>' +
                        '<span class="mv-cambio"></span>' +
                        '<button type="button" class="mv-quitar" data-accion="quitar" title="Quitar"><i class="ti-trash"></i></button>' +
                    '</div><div class="mv-stock-aviso"></div>';
            }
            var enSuLugar = $items.children[posicion];
            if (enSuLugar !== fila) $items.insertBefore(fila, enSuLugar || null);

            fila.querySelector('.mv-item-unitario').textContent = pesos(p.precio) + ' c/u';
            fila.querySelector('.mv-item-subtotal').textContent = pesos(subtotal);
            var input = fila.querySelector('input');
            if (document.activeElement !== input) input.value = item.cantidad;

            var original = cantidadOriginal[item.id] || 0;
            var etiqueta = fila.querySelector('.mv-cambio');
            if (!original) { etiqueta.className = 'mv-cambio nuevo'; etiqueta.textContent = 'Nuevo'; }
            else if (original !== item.cantidad) { etiqueta.className = 'mv-cambio editado'; etiqueta.textContent = 'Antes: ' + original; }
            else { etiqueta.className = 'mv-cambio'; etiqueta.textContent = ''; }

            // Stock disponible = stock actual + lo que esta venta ya había descontado
            var disponible = p.stock + original;
            fila.querySelector('.mv-stock-aviso').innerHTML = item.cantidad > disponible
                ? '<div class="mv-aviso-stock"><i class="ti-alert"></i> Stock disponible: ' + escapar(disponible) + '</div>' : '';
        });
        Object.keys(existentes).forEach(function (id) { existentes[id].remove(); });

        if (items.length === 0) {
            $items.innerHTML = '<div class="mv-vacio">La venta no tiene productos. Agregá al menos uno.</div>';
        } else {
            var vacio = $items.querySelector('.mv-vacio');
            if (vacio) vacio.remove();
        }

        // Productos que estaban y se quitaron (con opción de recuperarlos)
        var quitados = Object.keys(cantidadOriginal).filter(function (id) { return !buscarItem(parseInt(id, 10)); });
        document.getElementById('mv-quitados').innerHTML = quitados.length === 0 ? '' :
            '<div class="mv-quitados">Quitados: ' + quitados.map(function (id) {
                return escapar(producto(parseInt(id, 10)).nombre) + ' <button type="button" data-recuperar="' + id + '">deshacer</button>';
            }).join(' · ') + '</div>';

        total = Math.round(total);
        var diferencia = total - TOTAL_ORIGINAL;
        document.getElementById('mv-unidades').textContent = unidades + ' u.';
        document.getElementById('mv-total').textContent = pesos(total);
        var $dif = document.getElementById('mv-diferencia');
        $dif.textContent = (diferencia > 0 ? '+ ' : diferencia < 0 ? '− ' : '') + pesos(Math.abs(diferencia));
        $dif.className = 'mv-diferencia' + (diferencia > 0 ? ' sube' : diferencia < 0 ? ' baja' : '');

        var hayCambios = medioPago !== PAGO_ORIGINAL || huboCambiosEnItems();
        var $guardar = document.getElementById('mv-guardar');
        $guardar.disabled = guardando || !hayCambios || items.length === 0;
        var $mensaje = document.getElementById('mv-mensaje');
        $mensaje.className = 'mv-mensaje';
        $mensaje.textContent = items.length === 0 ? 'Agregá al menos un producto.' : (hayCambios ? '' : 'Todavía no hiciste cambios.');
    }

    function huboCambiosEnItems() {
        var ids = Object.keys(cantidadOriginal);
        if (ids.length !== items.length) return true;
        return items.some(function (i) { return cantidadOriginal[i.id] !== i.cantidad; });
    }

    $items.addEventListener('click', function (e) {
        var boton = e.target.closest('button[data-accion]');
        if (!boton) return;
        var id = parseInt(boton.closest('.mv-item').getAttribute('data-id'), 10);
        var item = buscarItem(id);
        var accion = boton.getAttribute('data-accion');
        if (accion === 'mas') cambiar(id, item.cantidad + 1);
        else if (accion === 'menos') { if (item.cantidad <= 1) quitar(id); else cambiar(id, item.cantidad - 1); }
        else if (accion === 'quitar') quitar(id);
    });
    $items.addEventListener('input', function (e) {
        if (e.target.tagName !== 'INPUT') return;
        var cantidad = parseInt(e.target.value, 10);
        if (cantidad >= 1) cambiar(parseInt(e.target.closest('.mv-item').getAttribute('data-id'), 10), cantidad);
    });
    $items.addEventListener('change', function (e) {
        if (e.target.tagName !== 'INPUT') return;
        if (!(parseInt(e.target.value, 10) >= 1)) e.target.value = 1;
        cambiar(parseInt(e.target.closest('.mv-item').getAttribute('data-id'), 10), parseInt(e.target.value, 10));
    });
    document.getElementById('mv-quitados').addEventListener('click', function (e) {
        var id = e.target.getAttribute('data-recuperar');
        if (!id) return;
        items.push({ id: parseInt(id, 10), cantidad: cantidadOriginal[id] });
        pintar();
    });

    document.getElementById('mv-pagos').addEventListener('click', function (e) {
        var boton = e.target.closest('.mv-pago');
        if (!boton) return;
        medioPago = boton.getAttribute('data-valor');
        Array.prototype.forEach.call(this.querySelectorAll('.mv-pago'), function (b) { b.classList.toggle('seleccionado', b === boton); });
        pintar();
    });

    // ---------- Guardar ----------
    document.getElementById('mv-guardar').addEventListener('click', function () {
        if (guardando || items.length === 0) return;
        var total = document.getElementById('mv-total').textContent;
        var diferencia = document.getElementById('mv-diferencia').textContent;
        if (!confirm('¿Guardar los cambios de la venta #' + ID_PEDIDO + '?\n\nTotal nuevo: ' + total + ' (' + diferencia + ')\nMedio de pago: ' + medioPago)) return;

        guardando = true;
        pintar();
        var $mensaje = document.getElementById('mv-mensaje');
        $mensaje.textContent = 'Guardando…';

        $.post(URL_BASE + 'guardar_venta', {
            idpedido: ID_PEDIDO,
            modo_de_pago: medioPago,
            items: JSON.stringify(items)
        }, null, 'json')
        .done(function (r) {
            if (r && r.ok) {
                location.href = URL_BASE + 'pedido_detalles/' + ID_PEDIDO + '?modificada=1';
                return;
            }
            fallo(r && r.error ? r.error : 'No se pudo guardar la venta.');
        })
        .fail(function () { fallo('Error al conectar con el servidor.'); });
    });

    function fallo(texto) {
        guardando = false;
        pintar();
        var $mensaje = document.getElementById('mv-mensaje');
        $mensaje.className = 'mv-mensaje error';
        $mensaje.textContent = texto;
    }

    // Avisar si se sale con cambios sin guardar
    window.addEventListener('beforeunload', function (e) {
        if (!guardando && (medioPago !== PAGO_ORIGINAL || huboCambiosEnItems())) { e.preventDefault(); e.returnValue = ''; }
    });

    buscar();
    pintar();
})();
</script>
