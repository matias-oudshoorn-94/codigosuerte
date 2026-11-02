<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{
    private $dir  = 'reportes';
    private $nom  = 'Reportes';
    private $dir2 = 'Reportes';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cajasdiarias_model', 'cajas', TRUE);
        $this->data['dir']  = $this->dir;
        $this->data['dir2'] = $this->dir2;
    }

    /**
     * Pantalla principal de reportes
     * URL: /admin/reportes
     */
    public function index()
{
    $this->data['usuarios'] = $this->cajas->getUsuariosActivos();

    $this->template->title("Reportes", 'Administración');
    $this->template->build('admin/reportes/list', $this->data);
}

public function parcial()
{
    $id_usuario  = $this->input->get('id_usuario');
    $fecha_desde = $this->input->get('fecha_desde');
    $fecha_hasta = $this->input->get('fecha_hasta');

    if (!$id_usuario || !$fecha_desde || !$fecha_hasta) {
        show_error("Faltan parámetros para generar el reporte parcial");
    }

    // Obtener detalle diario
    $reporte = $this->cajas->getTotalesParcialDetalle($id_usuario, $fecha_desde, $fecha_hasta);

    $this->data['reporte']      = $reporte;
    $this->data['id_usuario']   = $id_usuario;
    $this->data['fecha_desde']  = $fecha_desde;
    $this->data['fecha_hasta']  = $fecha_hasta;

    $this->template->title("Reporte parcial", 'Administración');
    $this->template->build('admin/reportes/parcial', $this->data);
}

public function mensual()
{
    $id_usuario = $this->input->get('id_usuario');
    $mes_anio   = $this->input->get('mes_anio'); // nuevo campo

    if (!$id_usuario || !$mes_anio) {
        show_error("Faltan parámetros para generar el reporte mensual");
    }

    // Separar año y mes
    list($anio, $mes) = explode('-', $mes_anio);

    $this->data['reporte'] = $this->cajas->getTotalesPorMes($id_usuario, $anio, $mes);

    $this->template->title("Reporte mensual", 'Administración');
    $this->template->build('admin/reportes/mensual', $this->data);
}

public function grafico($id_usuario = null, $limite = 7)
{
    if ($this->input->get('id_usuario')) {
        $id_usuario = $this->input->get('id_usuario');
        $limite     = $this->input->get('limite');
    }

    if (!$id_usuario) {
        show_error("Falta seleccionar una sucursal");
    }

    $ventas = $this->cajas->getVentasPorDia($id_usuario, $limite);
    $this->data['etiquetas'] = array_column($ventas, 'fecha');
    $this->data['valores']   = array_column($ventas, 'total');

    $this->template->title("Reporte gráfico", 'Administración');
    $this->template->build('admin/reportes/grafico', $this->data);
}

    public function comparativo()
{
    // Limite de días a mostrar (por GET o default 7)
    $limite = $this->input->get('limite') ?: 7;

    // Traer usuarios activos (sucursales)
    $usuarios = $this->cajas->getUsuariosActivos();

    $this->data['etiquetas'] = [];
    $this->data['datasets']  = [];

    foreach ($usuarios as $u) {
        $ventas = $this->cajas->getVentasPorDia($u->idusuario, $limite);

        // Tomar las fechas de la primera sucursal como etiquetas
        if (empty($this->data['etiquetas'])) {
            $this->data['etiquetas'] = array_column($ventas, 'fecha');
        }

        $this->data['datasets'][] = [
            "label" => $u->nombre . " " . $u->apellido,
            "data"  => array_column($ventas, 'total'),
        ];
    }

    $this->data['limite'] = $limite; // Pasamos el limite a la vista por si queremos mostrarlo

    $this->template->title("Reporte comparativo", 'Administración');
    $this->template->build('admin/reportes/comparativo', $this->data);
}

}