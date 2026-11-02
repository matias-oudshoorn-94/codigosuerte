<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cierres de caja (tabla cajas_diarias: una fila por sucursal y dia).
 */
class Cajasdiarias_model extends CI_Model
{
	private function filtrar($filtros)
	{
		if ( ! empty($filtros['sucursal'])) $this->db->where('c.id_usuario', (int) $filtros['sucursal']);
		if ( ! empty($filtros['desde']))    $this->db->where('c.fecha >=', $filtros['desde']);
		if ( ! empty($filtros['hasta']))    $this->db->where('c.fecha <=', $filtros['hasta']);
	}

	public function contar($filtros)
	{
		$this->db->from('cajas_diarias c');
		$this->filtrar($filtros);
		return $this->db->count_all_results();
	}

	public function totales($filtros)
	{
		$this->db->select('COALESCE(SUM(c.total),0) AS total, COALESCE(SUM(c.cantidad_ventas),0) AS ventas, COALESCE(SUM(c.impresiones_reales),0) AS impresiones', FALSE)
			->from('cajas_diarias c');
		$this->filtrar($filtros);
		return $this->db->get()->row();
	}

	public function listar($filtros, $limite, $offset)
	{
		$this->db->select('c.*')->from('cajas_diarias c');
		$this->filtrar($filtros);
		return $this->db->order_by('c.fecha', 'DESC')->order_by('c.id', 'DESC')
			->limit($limite, $offset)->get()->result();
	}

	public function obtener($id)
	{
		return $this->db->where('id', (int) $id)->get('cajas_diarias')->row();
	}

	/** Ventas de una sucursal en un dia, con sus productos. */
	public function ventas_del_dia($id_usuario, $fecha)
	{
		$ventas = $this->db->select('idpedido, fecha_hora, modo_de_pago, total')
			->where('cod_clie', (string) (int) $id_usuario)->where('fecha', $fecha)
			->order_by('idpedido', 'ASC')->get('pedidos')->result();
		if ( ! $ventas) return array();

		$por_id = array();
		foreach ($ventas as $v) { $v->items = array(); $por_id[(int) $v->idpedido] = $v; }

		$items = $this->db->select('d.idpedido, d.cantidad, pr.nombre')
			->from('pedidosdetalles d')
			->join('productos pr', 'pr.id = d.id_articu', 'left')
			->where_in('d.idpedido', array_keys($por_id))
			->order_by('d.iddetalle', 'ASC')->get()->result();
		foreach ($items as $item) $por_id[(int) $item->idpedido]->items[] = $item;

		return array_values($por_id);
	}

	public function eliminar($id)
	{
		$this->db->where('id', (int) $id)->delete('cajas_diarias');
		return $this->db->affected_rows() > 0;
	}
}
