<?php
$base_url = base_url()."application/views/layouts";

if (!isset($this->session->userdata("user_data")[0]->idusuario))
{
    redirect('login');
}

$sucursal_id = (int)$this->session->userdata("user_data")[0]->idusuario;

// Datos actuales de la base (los de la sesión son del momento del login)
$usuario = $this->db->select('usuario, nombre, apellido, telefono, email, domicilio, localidad, caja_cerrada')
    ->where('idusuario', $sucursal_id)->get('usuarios')->row();
if (!$usuario)
{
    $usuario = $this->session->userdata("user_data")[0];
}

$caja_cerrada_hoy = isset($usuario->caja_cerrada) && $usuario->caja_cerrada == 1;

// Última declaración de cada impresora
$ultimas_declaraciones = array();
$codigos = array();
foreach ($rows_impresoras as $impresora) $codigos[] = $impresora->codigo;
if (count($codigos) > 0)
{
    $filas = $this->db->select('id_impresora, MAX(fecha) AS ultima', FALSE)
        ->where_in('id_impresora', $codigos)->group_by('id_impresora')
        ->get('detalles_impresoras')->result();
    foreach ($filas as $fila) $ultimas_declaraciones[$fila->id_impresora] = $fila->ultima;
}

// Resumen del mes (desde las cajas diarias)
$mes = $this->db->select('COUNT(*) AS dias, SUM(total) AS total, SUM(cantidad_ventas) AS ventas, MAX(total) AS mejor', FALSE)
    ->where('id_usuario', $sucursal_id)
    ->where('fecha >=', date('Y-m-01'))
    ->where('fecha <=', date('Y-m-d'))
    ->get('cajas_diarias')->row();
$mes_total = $mes ? (float)$mes->total : 0;
$mes_dias = $mes ? (int)$mes->dias : 0;
$mes_ventas = $mes ? (int)$mes->ventas : 0;
$mes_promedio = $mes_dias > 0 ? $mes_total / $mes_dias : 0;
$mejor_dia = null;
if ($mes && $mes->mejor > 0)
{
    $mejor_dia = $this->db->select('fecha, total')->where('id_usuario', $sucursal_id)
        ->where('fecha >=', date('Y-m-01'))->where('total', $mes->mejor)
        ->order_by('fecha', 'desc')->limit(1)->get('cajas_diarias')->row();
}

$meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');

function pf_pesos($valor)
{
    return '$ '.number_format($valor, 0, ',', '.');
}
function pf_fecha($fecha)
{
    if (!$fecha || $fecha == '0000-00-00') return '—';
    $t = strtotime($fecha);
    $nombres = array('', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
    return date('j', $t).' '.$nombres[date('n', $t)].' '.date('Y', $t);
}
function pf_valor($texto)
{
    $texto = trim((string)$texto);
    return $texto === '' ? '<span class="pf-vacio-dato">Sin cargar</span>' : htmlspecialchars($texto);
}

$nombre_completo = trim($usuario->nombre.' '.$usuario->apellido);
// Iniciales: primera letra de las dos primeras palabras (sin signos)
$palabras = preg_split('/\s+/', trim(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $nombre_completo)), -1, PREG_SPLIT_NO_EMPTY);
$iniciales = '';
foreach (array_slice($palabras, 0, 2) as $palabra) $iniciales .= substr($palabra, 0, 1);
if ($iniciales === '') $iniciales = substr($usuario->usuario, 0, 2);
$iniciales = strtoupper($iniciales);
?>

<style type="text/css">
.pf {
    --pf-verde: #47b475;
    --pf-verde-oscuro: #3a9862;
    --pf-verde-claro: #eaf6ef;
    --pf-texto: #1f2933;
    --pf-texto-suave: #6b7785;
    --pf-borde: #e3e8ee;
    --pf-fondo: #f5f7f9;
    --pf-ambar: #b7790a;
    --pf-ambar-claro: #fdf3dc;
    background: var(--pf-fondo);
    padding: 32px 0 60px;
    color: var(--pf-texto);
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.pf * { box-sizing: border-box; }
/* El tema global agrega márgenes y mayúsculas a botones y títulos: se anulan acá. */
.pf button, .pf h1, .pf h2, .pf p, .pf dl, .pf dd { margin-top: 0; margin-bottom: 0; }
.pf button { font-family: inherit; line-height: 1.2; letter-spacing: 0; text-transform: none; }

.pf-cabecera { display: flex; align-items: center; gap: 18px; margin-bottom: 24px; flex-wrap: wrap; }
.pf-avatar { width: 64px; height: 64px; border-radius: 50%; background: var(--pf-verde); color: #fff; font-size: 24px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex: none; }
.pf-cabecera h1 { font-size: 26px; font-weight: 600; color: var(--pf-texto); letter-spacing: 0; text-transform: none; }
.pf-cabecera-sub { color: var(--pf-texto-suave); font-size: 15px; margin-top: 4px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.pf-estado { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: var(--pf-verde-claro); color: var(--pf-verde-oscuro); }
.pf-estado.cerrada { background: var(--pf-ambar-claro); color: var(--pf-ambar); }
.pf-estado i { width: 7px; height: 7px; border-radius: 50%; background: currentColor; display: inline-block; }

.pf-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; }
.pf-ancho { grid-column: 1 / -1; }
@media (max-width: 991px) { .pf-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 767px) { .pf-grid { grid-template-columns: 1fr; } .pf-ancho { grid-column: auto; } }

.pf-panel { background: #fff; border: 1px solid var(--pf-borde); border-radius: 12px; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
.pf-panel-cabecera { padding: 16px 20px; border-bottom: 1px solid var(--pf-borde); display: flex; align-items: center; gap: 10px; }
.pf-panel-cabecera i { color: var(--pf-texto-suave); font-size: 16px; }
.pf-panel-cabecera h2 { font-size: 16px; font-weight: 600; color: var(--pf-texto); text-transform: none; letter-spacing: 0; }
.pf-panel-cabecera .pf-extra { margin-left: auto; font-size: 13px; color: var(--pf-texto-suave); }
.pf-cuerpo { padding: 6px 20px; }

.pf-datos div { display: flex; justify-content: space-between; gap: 16px; padding: 12px 0; border-bottom: 1px solid #f0f2f5; font-size: 14px; align-items: center; }
.pf-datos div:last-child { border-bottom: 0; }
.pf-datos dt { color: var(--pf-texto-suave); font-weight: 400; white-space: nowrap; }
.pf-datos dd { font-weight: 600; text-align: right; overflow-wrap: anywhere; min-width: 0; }
.pf-datos dd a { color: var(--pf-texto); }
.pf-datos dd a:hover { color: var(--pf-verde-oscuro); }
.pf-vacio-dato { font-weight: 400; color: #a3adb8; font-style: italic; }

.pf-clave { display: inline-flex; align-items: center; gap: 8px; }
.pf-clave code { font-family: inherit; font-size: 14px; font-weight: 600; background: none; color: var(--pf-texto); padding: 0; letter-spacing: .08em; }

/* Este mes */
.pf-mes { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); }
.pf-mes > div { padding: 18px 20px; border-right: 1px solid var(--pf-borde); min-width: 0; }
.pf-mes > div:last-child { border-right: 0; }
.pf-etiqueta { font-size: 12px; font-weight: 600; color: var(--pf-texto-suave); text-transform: uppercase; letter-spacing: .04em; }
.pf-mes-valor { font-size: 22px; font-weight: 600; margin-top: 6px; font-variant-numeric: tabular-nums; white-space: nowrap; }
.pf-mes-valor.grande { font-size: 26px; font-weight: 700; }
.pf-mes-nota { font-size: 12px; color: var(--pf-texto-suave); margin-top: 2px; }
@media (max-width: 767px) {
    .pf-mes { grid-template-columns: 1fr 1fr; }
    .pf-mes > div:nth-child(2) { border-right: 0; }
    .pf-mes > div:nth-child(-n+2) { border-bottom: 1px solid var(--pf-borde); }
    .pf-mes-valor { font-size: 18px; }
    .pf-mes-valor.grande { font-size: 20px; }
}

/* Impresoras */
.pf-impresora { padding: 14px 0; border-bottom: 1px solid #f0f2f5; }
.pf-impresora:last-child { border-bottom: 0; }
.pf-impresora-cabecera { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
.pf-impresora-codigo { font-weight: 600; font-size: 15px; display: flex; align-items: center; gap: 8px; }
.pf-impresora-contador { font-size: 20px; font-weight: 700; font-variant-numeric: tabular-nums; }
.pf-impresora-meta { display: flex; justify-content: space-between; gap: 10px; margin-top: 4px; font-size: 12px; color: var(--pf-texto-suave); flex-wrap: wrap; }
.pf-sin { padding: 24px 0; text-align: center; color: var(--pf-texto-suave); font-size: 14px; }

@media (max-width: 600px) {
    .pf { padding-top: 20px; }
    .pf-cuerpo, .pf-panel-cabecera, .pf-mes > div { padding-left: 14px; padding-right: 14px; }
    .pf-avatar { width: 52px; height: 52px; font-size: 20px; }
    .pf-cabecera h1 { font-size: 22px; }
}
</style>

<section class="pf">
    <div class="container">

        <div class="pf-cabecera">
            <div class="pf-avatar"><?php echo htmlspecialchars($iniciales) ?></div>
            <div>
                <h1><?php echo htmlspecialchars($nombre_completo !== '' ? $nombre_completo : $usuario->usuario) ?></h1>
                <div class="pf-cabecera-sub">
                    <span>Sucursal <b><?php echo htmlspecialchars($usuario->usuario) ?></b></span>
                    <span class="pf-estado<?php echo $caja_cerrada_hoy ? ' cerrada' : '' ?>"><i></i><?php echo $caja_cerrada_hoy ? 'Caja cerrada hoy' : 'Caja abierta' ?></span>
                </div>
            </div>
        </div>

        <div class="pf-grid">

            <!-- Este mes -->
            <div class="pf-panel pf-ancho">
                <div class="pf-panel-cabecera">
                    <i class="ti-stats-up"></i>
                    <h2>Este mes</h2>
                    <span class="pf-extra"><?php echo ucfirst($meses[date('n')]).' '.date('Y') ?></span>
                </div>
                <div class="pf-mes">
                    <div>
                        <div class="pf-etiqueta">Total vendido</div>
                        <div class="pf-mes-valor grande"><?php echo pf_pesos($mes_total) ?></div>
                    </div>
                    <div>
                        <div class="pf-etiqueta">Ventas</div>
                        <div class="pf-mes-valor"><?php echo number_format($mes_ventas, 0, ',', '.') ?></div>
                        <div class="pf-mes-nota">en <?php echo $mes_dias ?> día<?php echo $mes_dias == 1 ? '' : 's' ?></div>
                    </div>
                    <div>
                        <div class="pf-etiqueta">Promedio diario</div>
                        <div class="pf-mes-valor"><?php echo pf_pesos($mes_promedio) ?></div>
                    </div>
                    <div>
                        <div class="pf-etiqueta">Mejor día</div>
                        <div class="pf-mes-valor"><?php echo $mejor_dia ? pf_pesos($mejor_dia->total) : '—' ?></div>
                        <?php if ($mejor_dia) { ?><div class="pf-mes-nota"><?php echo pf_fecha($mejor_dia->fecha) ?></div><?php } ?>
                    </div>
                </div>
            </div>

            <!-- Encargado -->
            <div class="pf-panel">
                <div class="pf-panel-cabecera"><i class="ti-user"></i><h2>Encargado</h2></div>
                <div class="pf-cuerpo">
                    <dl class="pf-datos">
                        <div><dt>Nombre</dt><dd><?php echo pf_valor($nombre_completo) ?></dd></div>
                        <div><dt>Teléfono</dt><dd><?php echo trim($usuario->telefono) !== '' ? '<a href="tel:'.htmlspecialchars(preg_replace('/[^0-9+]/', '', $usuario->telefono)).'">'.htmlspecialchars($usuario->telefono).'</a>' : pf_valor('') ?></dd></div>
                        <div><dt>Email</dt><dd><?php echo trim($usuario->email) !== '' ? '<a href="mailto:'.htmlspecialchars($usuario->email).'">'.htmlspecialchars($usuario->email).'</a>' : pf_valor('') ?></dd></div>
                    </dl>
                </div>
            </div>

            <!-- Sucursal y acceso -->
            <div class="pf-panel">
                <div class="pf-panel-cabecera"><i class="ti-home"></i><h2>Sucursal y acceso</h2></div>
                <div class="pf-cuerpo">
                    <dl class="pf-datos">
                        <div><dt>Localidad</dt><dd><?php echo pf_valor($usuario->localidad) ?></dd></div>
                        <div><dt>Dirección</dt><dd><?php echo pf_valor($usuario->domicilio) ?></dd></div>
                        <div><dt>Usuario</dt><dd><?php echo pf_valor($usuario->usuario) ?></dd></div>
                        <div>
                            <dt>Contraseña</dt>
                            <dd>
                                <span class="pf-clave">
                                    <code>••••••••</code>
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Impresoras -->
            <div class="pf-panel">
                <div class="pf-panel-cabecera">
                    <i class="ti-printer"></i>
                    <h2>Impresoras</h2>
                    <span class="pf-extra"><?php echo count($rows_impresoras) ?></span>
                </div>
                <div class="pf-cuerpo">
                    <?php if (count($rows_impresoras) == 0) { ?>
                        <div class="pf-sin">No hay impresoras asignadas.</div>
                    <?php } ?>
                    <?php foreach ($rows_impresoras as $impresora) {
                        $ultima = isset($ultimas_declaraciones[$impresora->codigo]) ? $ultimas_declaraciones[$impresora->codigo] : null; ?>
                        <div class="pf-impresora">
                            <div class="pf-impresora-cabecera">
                                <span class="pf-impresora-codigo"><?php echo htmlspecialchars($impresora->codigo) ?></span>
                                <span class="pf-impresora-contador"><?php echo number_format($impresora->copias, 0, ',', '.') ?></span>
                            </div>
                            <div class="pf-impresora-meta">
                                <span>Alta: <?php echo pf_fecha($impresora->fecha) ?></span>
                                <span>Última declaración: <?php echo pf_fecha($ultima) ?></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</section>

