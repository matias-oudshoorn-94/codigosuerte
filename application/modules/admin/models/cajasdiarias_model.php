<?php
class Cajasdiarias_model extends CI_Model{
	private $tabla = 'cajas_diarias';
	private $id = 'id';
	private $busqueda = 'id';
	private $order =  array('cajas_diarias.fecha');

	function __construct()
	{
		parent::__construct();
	}

	function getAll($objeto = array())
	{

		$this->db->select("cajas_diarias.id,
							cajas_diarias.id_usuario,
							cajas_diarias.fecha,
							cajas_diarias.cantidad_ventas,
							cajas_diarias.total,
							cajas_diarias.total_efectivo,
							cajas_diarias.total_transferencia,
							cajas_diarias.total_medio_electronico,
							cajas_diarias.total_tarjeta,
							cajas_diarias.impresiones_reales,
							cajas_diarias.impresiones_aprox,
							cajas_diarias.desperdicio,
							usuarios.idusuario,
							usuarios.nombre as nombre,
							usuarios.apellido as apellido,
							usuarios.localidad as localidad,
							usuarios.telefono as telefono,
							usuarios.email as email,
							usuarios.domicilio as domicilio
							");

		$this->db->join('usuarios', 'usuarios.idusuario = cajas_diarias.id_usuario', 'left');
		if(isset($objeto['sort_order']))
			$sort_order = ($objeto['sort_order'] == 'desc') ? 'desc' : 'asc';
		$sort_columns = $this->order;
		if(isset($objeto['sort_by']))
			$sort_by = (in_array($objeto['sort_by'], $sort_columns)) ? $objeto['sort_by'] : $this->id;

		// Qualification
		//if(isset($objeto['bnombre'])) $this->db->like('n_rubro', $objeto['bnombre']);
		//if(isset($objeto['id'])) $this->db->where($this->id, $objeto['id']);
		if(isset($objeto['search'])) $this->db->where("(usuarios.nombre LIKE '%".$objeto['search']."%' OR usuarios.localidad LIKE '%".$objeto['search']."%')");
		if(isset($objeto['id'])) $this->db->where('id', $objeto['id']);
		if(isset($objeto['fecha'])) $this->db->where('fecha', $objeto['fecha']);

		//if(isset($objeto['where'])) $this->db->where('rubros.borrado', $objeto['where']);

		// limit / offset
		if(isset($objeto['limit']) && isset($objeto['offset']))
			$this->db->limit($objeto['limit'], ($objeto['limit']*($objeto['offset']-1)));
		else if(isset($objeto['limit']))
			$this->db->limit($objeto['limit']);

		// order
		if(isset($sort_by))
			$this->db->order_by($sort_by, $sort_order);

		$ret['rows'] = $this->db->get($this->tabla)->result();
		//echo $this->db->last_query();
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from($this->tabla);

		$tmp = $q->get()->result();

		$ret['num_rows'] = $tmp[0]->count;

		return $ret;
	}

/**
 * Reporte parcial detallado por día
 * Compatible con columna DATETIME
 */
public function getTotalesParcialDetalle($id_usuario, $fecha_desde, $fecha_hasta) {
    return $this->db
        ->select("DATE(fecha) as fecha, SUM(total) as total, SUM(cantidad_ventas) as cantidad")
        ->from($this->tabla)
        ->where('id_usuario', $id_usuario)
        ->where("fecha >= '$fecha_desde 00:00:00'", NULL, FALSE)
        ->where("fecha <= '$fecha_hasta 23:59:59'", NULL, FALSE)
        ->group_by("DATE(fecha)")
        ->order_by('fecha', 'ASC')
        ->get()
        ->result_array();
}


/**
 * Reporte total por mes
 * Compatible con columna DATE/DATETIME
 */
public function getTotalesPorMes($id_usuario, $anio, $mes) {
    // Generamos desde y hasta para el mes
    $fecha_desde = "$anio-$mes-01";
    $fecha_hasta = date("Y-m-t", strtotime($fecha_desde)); // último día del mes

    return $this->db
        ->select("SUM(total) as total_vendido,
                  SUM(total_efectivo) as efectivo,
                  SUM(total_transferencia) as transferencia,
                  SUM(total_medio_electronico) as medio_electronico,
                  SUM(total_tarjeta) as tarjeta")
        ->from($this->tabla)
        ->where('id_usuario', $id_usuario)
        ->where("fecha >= '$fecha_desde'", NULL, FALSE)
        ->where("fecha <= '$fecha_hasta'", NULL, FALSE)
        ->get()
        ->row_array(); // devuelvo array para que sea más fácil en la vista
}

/**
 * Ventas por día (para gráficos)
 */
public function getVentasPorDia($id_usuario, $limite = 7) {
    return $this->db
        ->select("DATE(fecha) as fecha, SUM(total) as total")
        ->from($this->tabla)
        ->where('id_usuario', $id_usuario)
        ->group_by("DATE(fecha)")
        ->order_by('fecha', 'DESC')
        ->limit($limite)
        ->get()
        ->result_array();
}

	public function getUsuariosActivos() {
    return $this->db
        ->select("idusuario, nombre, apellido, localidad")
        ->from("usuarios")
        ->order_by("nombre", "asc")
        ->get()
        ->result();
}

}