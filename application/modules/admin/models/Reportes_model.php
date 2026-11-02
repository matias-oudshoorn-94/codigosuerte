<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Consultas de reportes y del inicio del panel.
 *
 * Fuente principal: pedidos (una fila por venta) y pedidosdetalles.
 * Las impresiones y el desperdicio salen de cajas_diarias (se cargan al cerrar caja).
 * Todas las fechas son 'YYYY-MM-DD'. $sucursal = 0 significa todas.
 */
class Reportes_model extends CI_Model
{
	/** Medios de pago en el orden en que se muestran. */
	public static $medios = array(
		'Efectivo'                  => 'Efectivo',
		'Tarjeta de crédito/débito' => 'Tarjeta',
		'Medio electrónico'         => 'Medio electrónico',
		'Transferencia/QR'          => 'Transferencia/QR',
	);

	/** Filtro comun sobre pedidos (alias p). */
	private function filtrar($desde, $hasta, $sucursal)
	{
		$this->db->where('p.fecha >=', $desde)
			->where('p.fecha <=', $hasta)
			->where("p.cod_clie IS NOT NULL AND p.cod_clie <> ''", NULL, FALSE);
		if ($sucursal) $this->db->where('p.cod_clie', (string) (int) $sucursal);
	}

	/** Totales del periodo: ventas, total, dias con ventas. */
	public function resumen($desde, $hasta, $sucursal = 0)
	{
		$this->db->select('COUNT(*) AS ventas, COALESCE(SUM(p.total), 0) AS total, COUNT(DISTINCT p.fecha) AS dias', FALSE)
			->from('pedidos p');
		$this->filtrar($desde, $hasta, $sucursal);
		$fila = $this->db->get()->row();
		$fila->ticket = $fila->ventas > 0 ? $fila->total / $fila->ventas : 0;
		$fila->promedio_diario = $fila->dias > 0 ? $fila->total / $fila->dias : 0;
		return $fila;
	}

	/**
	 * Ventas agrupadas por dia o por mes, con el desglose por medio de pago.
	 * $agrupar: 'dia' | 'mes'
	 */
	public function serie($desde, $hasta, $sucursal = 0, $agrupar = 'dia')
	{
		$clave = $agrupar === 'mes' ? "DATE_FORMAT(p.fecha, '%Y-%m')" : 'p.fecha';
		$select = $clave.' AS periodo, COUNT(*) AS ventas, COALESCE(SUM(p.total), 0) AS total';
		$i = 0;
		foreach (self::$medios as $medio => $etiqueta)
		{
			$select .= ', COALESCE(SUM(CASE WHEN p.modo_de_pago = '.$this->db->escape($medio).' THEN p.total END), 0) AS medio_'.$i++;
		}
		$this->db->select($select, FALSE)->from('pedidos p');
		$this->filtrar($desde, $hasta, $sucursal);
		$filas = $this->db->group_by('periodo')->order_by('periodo', 'ASC')->get()->result();

		// Completar los periodos sin ventas con cero, para que el grafico no salte dias
		$por_clave = array();
		foreach ($filas as $fila) $por_clave[$fila->periodo] = $fila;
		$resultado = array();
		$actual = new DateTime($agrupar === 'mes' ? substr($desde, 0, 7).'-01' : $desde);
		$fin = new DateTime($hasta);
		$paso = new DateInterval($agrupar === 'mes' ? 'P1M' : 'P1D');
		while ($actual <= $fin)
		{
			$k = $actual->format($agrupar === 'mes' ? 'Y-m' : 'Y-m-d');
			if (isset($por_clave[$k]))
			{
				$resultado[] = $por_clave[$k];
			}
			else
			{
				$vacia = (object) array('periodo' => $k, 'ventas' => 0, 'total' => 0);
				for ($j = 0; $j < count(self::$medios); $j++) $vacia->{'medio_'.$j} = 0;
				$resultado[] = $vacia;
			}
			$actual->add($paso);
		}
		return $resultado;
	}

	/** Totales por medio de pago (incluye "Sin especificar"). */
	public function por_medio($desde, $hasta, $sucursal = 0)
	{
		$this->db->select("CASE WHEN p.modo_de_pago IN ('', '0') OR p.modo_de_pago IS NULL THEN 'Sin especificar' ELSE p.modo_de_pago END AS medio, COUNT(*) AS ventas, COALESCE(SUM(p.total), 0) AS total", FALSE)
			->from('pedidos p');
		$this->filtrar($desde, $hasta, $sucursal);
		return $this->db->group_by('medio')->order_by('total', 'DESC')->get()->result();
	}

	/** Totales por sucursal. */
	public function por_sucursal($desde, $hasta)
	{
		$this->db->select('p.cod_clie AS sucursal, COUNT(*) AS ventas, COALESCE(SUM(p.total), 0) AS total, COUNT(DISTINCT p.fecha) AS dias', FALSE)
			->from('pedidos p');
		$this->filtrar($desde, $hasta, 0);
		return $this->db->group_by('p.cod_clie')->order_by('total', 'DESC')->get()->result();
	}

	/** Total por periodo y sucursal (para comparar sucursales en el tiempo). */
	public function serie_por_sucursal($desde, $hasta, $agrupar = 'dia')
	{
		$clave = $agrupar === 'mes' ? "DATE_FORMAT(p.fecha, '%Y-%m')" : 'p.fecha';
		$this->db->select($clave.' AS periodo, p.cod_clie AS sucursal, COALESCE(SUM(p.total), 0) AS total', FALSE)
			->from('pedidos p');
		$this->filtrar($desde, $hasta, 0);
		return $this->db->group_by(array('periodo', 'p.cod_clie'))->order_by('periodo', 'ASC')->get()->result();
	}

	/**
	 * Productos mas vendidos por unidades.
	 * El detalle de venta no guarda el precio, asi que el importe se estima
	 * con el precio actual del producto.
	 */
	public function productos($desde, $hasta, $sucursal = 0, $limite = 50)
	{
		$this->db->select('d.id_articu AS id, pr.nombre, pr.codigo, pr.precio_final, SUM(d.cantidad) AS unidades, COUNT(DISTINCT d.idpedido) AS ventas, SUM(d.cantidad) * COALESCE(pr.precio_final, 0) AS importe_estimado', FALSE)
			->from('pedidosdetalles d')
			->join('pedidos p', 'p.idpedido = d.idpedido')
			->join('productos pr', 'pr.id = d.id_articu', 'left');
		$this->filtrar($desde, $hasta, $sucursal);
		return $this->db->group_by('d.id_articu')->order_by('unidades', 'DESC')->limit($limite)->get()->result();
	}

	/** Cantidad de ventas por dia de la semana (1 = domingo) y hora. */
	public function por_dia_y_hora($desde, $hasta, $sucursal = 0)
	{
		$this->db->select('DAYOFWEEK(p.fecha_hora) AS dia, HOUR(p.fecha_hora) AS hora, COUNT(*) AS ventas, COALESCE(SUM(p.total), 0) AS total', FALSE)
			->from('pedidos p');
		$this->filtrar($desde, $hasta, $sucursal);
		return $this->db->group_by(array('dia', 'hora'))->get()->result();
	}

	/** Impresiones declaradas vs vendidas y desperdicio, por dia (de los cierres de caja). */
	public function impresiones($desde, $hasta, $sucursal = 0)
	{
		$this->db->select('c.fecha, SUM(c.impresiones_reales) AS reales, SUM(c.impresiones_aprox) AS vendidas, COUNT(*) AS cierres', FALSE)
			->from('cajas_diarias c')
			->where('c.fecha >=', $desde)->where('c.fecha <=', $hasta);
		if ($sucursal) $this->db->where('c.id_usuario', (int) $sucursal);
		return $this->db->group_by('c.fecha')->order_by('c.fecha', 'ASC')->get()->result();
	}

	/** Impresiones agregadas por impresora en el periodo (de las declaraciones). */
	public function impresiones_por_impresora($desde, $hasta, $sucursal = 0)
	{
		$this->db->select('i.codigo, i.sub_id AS sucursal, i.copias AS contador, COALESCE(SUM(d.impresiones), 0) AS impresiones, COUNT(d.id) AS declaraciones, MAX(d.fecha) AS ultima', FALSE)
			->from('impresoras i')
			->join('detalles_impresoras d', "d.id_impresora = i.codigo AND d.fecha >= ".$this->db->escape($desde)." AND d.fecha <= ".$this->db->escape($hasta), 'left', FALSE);
		if ($sucursal) $this->db->where('i.sub_id', (int) $sucursal);
		return $this->db->group_by('i.id')->order_by('impresiones', 'DESC')->get()->result();
	}

	/** Ultimos cierres de caja. */
	public function ultimos_cierres($limite = 6)
	{
		return $this->db->select('c.id, c.id_usuario, c.fecha, c.total, c.cantidad_ventas')
			->from('cajas_diarias c')
			->order_by('c.fecha', 'DESC')->order_by('c.id', 'DESC')
			->limit($limite)->get()->result();
	}

	/** Ventas de hoy por sucursal. */
	public function hoy_por_sucursal()
	{
		return $this->db->select('p.cod_clie AS sucursal, COUNT(*) AS ventas, COALESCE(SUM(p.total), 0) AS total, MAX(p.fecha_hora) AS ultima', FALSE)
			->from('pedidos p')
			->where('p.fecha', date('Y-m-d'))
			->where("p.cod_clie IS NOT NULL AND p.cod_clie <> ''", NULL, FALSE)
			->group_by('p.cod_clie')->get()->result();
	}

	/** Cantidad de codigos de producto repetidos. */
	public function codigos_repetidos()
	{
		$fila = $this->db->query("SELECT COUNT(*) AS cantidad FROM (SELECT codigo FROM productos WHERE codigo <> '' GROUP BY codigo HAVING COUNT(*) > 1) x")->row();
		return (int) $fila->cantidad;
	}
}
