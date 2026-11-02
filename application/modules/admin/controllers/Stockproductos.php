<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stock: todos los productos con su stock en cada sucursal activa, editable en la tabla.
 */
class Stockproductos extends MY_Controller
{
	const POR_PAGINA = 50;

	function index()
	{
		$sucursales = array_filter($this->sucursales(), function ($s) { return $s->activo == 1; });
		$q = trim((string) $this->input->get('q'));
		$filtro = in_array($this->input->get('filtro'), array('sin', 'bajo'), TRUE) ? $this->input->get('filtro') : '';
		$umbral = max(1, (int) ($this->input->get('umbral') ?: 5));

		$ids = array_keys($sucursales);
		$this->filtrar($q, $filtro, $umbral, $ids);
		$pag = $this->paginacion($this->db->count_all_results('productos p'), self::POR_PAGINA);

		$this->filtrar($q, $filtro, $umbral, $ids);
		$productos = $this->db->select('p.id, p.nombre, p.codigo, p.precio_final')
			->order_by('p.nombre', 'ASC')->limit($pag['por_pagina'], $pag['offset'])
			->get('productos p')->result();

		// Stock de los productos de esta pagina: [producto][sucursal] => cantidad
		$stock = array();
		if ($productos && $ids)
		{
			$filas = $this->db->where_in('producto_id', array_map(function ($p) { return (int) $p->id; }, $productos))
				->where_in('idusuario', $ids)->get('stock_sucursal')->result();
			foreach ($filas as $f) $stock[(int) $f->producto_id][(int) $f->idusuario] = (int) $f->stock;
		}

		$this->data['sucursales'] = $sucursales;
		$this->data['productos'] = $productos;
		$this->data['stock'] = $stock;
		$this->data['pag'] = $pag;
		$this->data['q'] = $q;
		$this->data['filtro'] = $filtro;
		$this->data['umbral'] = $umbral;
		$this->mostrar('admin/stockproductos/list', 'Stock', 'stock');
	}

	/**
	 * Filtros: busqueda por nombre/codigo y "sin stock" / "stock bajo" en alguna sucursal activa.
	 * (Un producto sin fila en stock_sucursal cuenta como stock 0.)
	 */
	private function filtrar($q, $filtro, $umbral, $ids)
	{
		if ($q !== '') $this->db->group_start()->like('p.nombre', $q)->or_like('p.codigo', $q)->group_end();
		if ($filtro && $ids)
		{
			$limite = $filtro === 'sin' ? 0 : (int) $umbral;
			$condiciones = array();
			foreach ($ids as $id)
			{
				$condiciones[] = 'COALESCE((SELECT s.stock FROM stock_sucursal s WHERE s.producto_id = p.id AND s.idusuario = '.(int) $id.' LIMIT 1), 0) <= '.$limite;
			}
			$this->db->where('('.implode(' OR ', $condiciones).')', NULL, FALSE);
		}
	}

	/** Guarda el stock de la pagina: stock[producto][sucursal] = cantidad. */
	function guardar()
	{
		$this->solo_post();
		$this->load->model('admin/stockproductos_model', 'stock_m');
		$sucursales = $this->sucursales();
		$cambios = 0;

		$this->db->trans_start();
		foreach ((array) $this->input->post('stock') as $producto => $por_sucursal)
		{
			foreach ((array) $por_sucursal as $sucursal => $cantidad)
			{
				if ( ! isset($sucursales[(int) $sucursal]) || $cantidad === '') continue;
				$original = $this->input->post('original')[$producto][$sucursal] ?? NULL;
				if ((string) $original === (string) (int) $cantidad) continue; // sin cambios
				$this->stock_m->save_or_update((int) $producto, (int) $sucursal, max(0, (int) $cantidad));
				$cambios++;
			}
			// Mantener el stock total del producto como suma de las sucursales
			$this->db->query('UPDATE productos SET stock = (SELECT COALESCE(SUM(stock), 0) FROM stock_sucursal WHERE producto_id = ?) WHERE id = ?', array((int) $producto, (int) $producto));
		}
		$this->db->trans_complete();

		p_flash($cambios ? 'Se actualizó el stock ('.$cambios.' cambio'.($cambios == 1 ? '' : 's').').' : 'No había cambios para guardar.');
		$volver = (string) $this->input->post('volver');
		redirect('admin/stockproductos'.(preg_match('/^\?[A-Za-z0-9_=&%+.-]*$/', $volver) ? $volver : ''));
	}
}
