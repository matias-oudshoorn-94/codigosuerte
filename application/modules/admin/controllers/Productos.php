<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Productos: listado con busqueda, alta/edicion (precio y stock por sucursal) y baja.
 */
class Productos extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('admin/productos_model', 'productos');
	}

	function index()
	{
		$q = trim((string) $this->input->get('q'));
		$repetidos = (bool) $this->input->get('repetidos');
		$orden = (string) $this->input->get('orden') ?: 'nombre';
		$direccion = $this->input->get('dir') === 'desc' ? 'desc' : 'asc';

		$pag = $this->paginacion($this->productos->contar_panel($q, $repetidos), 50);
		$this->data['lista'] = $this->productos->listar_panel($q, $repetidos, $orden, $direccion, $pag['por_pagina'], $pag['offset']);
		$this->data['pag'] = $pag;
		$this->data['q'] = $q;
		$this->data['repetidos'] = $repetidos;
		$this->data['orden'] = $orden;
		$this->data['direccion'] = $direccion;
		$this->data['acciones'] = array(
			array('url' => site_url('admin/productos/edit/0'), 'texto' => 'Nuevo producto', 'icono' => 'ti-plus', 'clase' => 'principal'),
		);
		$this->mostrar('admin/productos/list', 'Productos', 'productos');
	}

	/** Alta (id 0) o edicion. */
	function edit($id = 0)
	{
		$id = (int) $id;
		$producto = $id ? $this->productos->obtener($id) : NULL;
		if ($id && ! $producto)
		{
			p_flash('Ese producto no existe.', 'error');
			redirect('admin/productos');
		}

		$sucursales = array_filter($this->sucursales(), function ($s) { return $s->activo == 1; });
		$stock = $id ? $this->productos->stock_por_sucursal($id) : array();
		$errores = array();
		$valores = $producto ? (array) $producto : array('porcentaje_marcado' => 50);

		if ($this->input->method() === 'post')
		{
			$datos = array(
				'nombre'             => trim((string) $this->input->post('nombre')),
				'codigo'             => trim((string) $this->input->post('codigo')),
				'precio'             => str_replace(',', '.', (string) $this->input->post('precio')),
				'porcentaje_marcado' => str_replace(',', '.', (string) $this->input->post('porcentaje_marcado')),
				'precio_final'       => str_replace(',', '.', (string) $this->input->post('precio_final')),
			);

			if ($datos['nombre'] === '') $errores['nombre'] = 'El nombre es obligatorio.';
			if ($datos['precio'] === '') $datos['precio'] = 0;
			if ( ! is_numeric($datos['precio']) || $datos['precio'] < 0) $errores['precio'] = 'Tiene que ser un número.';
			if ($datos['porcentaje_marcado'] === '') $datos['porcentaje_marcado'] = 0;
			if ( ! is_numeric($datos['porcentaje_marcado'])) $errores['porcentaje_marcado'] = 'Tiene que ser un número.';
			if ($datos['precio_final'] === '' && ! $errores)
			{
				// Si no se carga el precio de venta, se calcula con el costo + el porcentaje
				$datos['precio_final'] = round($datos['precio'] * (1 + $datos['porcentaje_marcado'] / 100), 2);
			}
			if ( ! is_numeric($datos['precio_final']) || $datos['precio_final'] < 0) $errores['precio_final'] = 'Tiene que ser un número.';

			$stock_nuevo = array();
			foreach ((array) $this->input->post('stock') as $sucursal => $cantidad)
			{
				if ( ! isset($sucursales[(int) $sucursal])) continue;
				$stock_nuevo[(int) $sucursal] = max(0, (int) $cantidad);
			}

			if ( ! $errores)
			{
				$datos['precio'] = (float) $datos['precio'];
				$datos['porcentaje_marcado'] = (int) round($datos['porcentaje_marcado']);
				$datos['precio_final'] = (float) $datos['precio_final'];
				// Stock total = suma de las sucursales (el sistema de ventas usa el de cada sucursal)
				$datos['stock'] = array_sum($stock_nuevo + $stock);
				if ( ! $id)
				{
					$datos['fecha_vencimiento'] = date('Y-m-d'); // campo sin uso, se completa por compatibilidad
					$datos['orden'] = 0;
				}

				$this->db->trans_start();
				$id_guardado = $this->productos->guardar($id, $datos);
				$this->load->model('admin/stockproductos_model', 'stock_m');
				foreach ($stock_nuevo as $sucursal => $cantidad) $this->stock_m->save_or_update($id_guardado, $sucursal, $cantidad);
				$this->db->trans_complete();

				$repetido = $this->productos->mismo_codigo($datos['codigo'], $id_guardado);
				if ($repetido)
					p_flash('Se guardó el producto, pero su código también lo tiene: '.implode(', ', array_map(function ($p) { return $p->nombre; }, $repetido)).'.', 'error');
				else
					p_flash($id ? 'Se guardó el producto.' : 'Se creó el producto.');
				redirect('admin/productos/edit/'.$id_guardado);
			}
			$valores = array_merge($valores, $datos);
			$stock = $stock_nuevo + $stock;
		}

		$this->data['id'] = $id;
		$this->data['valores'] = $valores;
		$this->data['errores'] = $errores;
		$this->data['sucursales'] = $sucursales;
		$this->data['stock'] = $stock;
		$this->data['repetidos'] = $id ? $this->productos->mismo_codigo(isset($valores['codigo']) ? $valores['codigo'] : '', $id) : array();
		$this->data['vendido'] = $id ? $this->productos->veces_vendido($id) : 0;
		$this->data['acciones'] = array(
			array('url' => site_url('admin/productos'), 'texto' => 'Productos', 'icono' => 'ti-arrow-left'),
		);
		if ($id) $this->data['acciones'][] = array('url' => site_url('admin/productos/edit/0'), 'texto' => 'Nuevo producto', 'icono' => 'ti-plus');
		$this->mostrar('admin/productos/edit', $id ? 'Editar producto' : 'Nuevo producto', 'productos');
	}

	function eliminar()
	{
		$this->solo_post();
		$producto = $this->productos->obtener($this->input->post('id'));
		if ($producto)
		{
			$this->productos->eliminar($producto->id);
			p_flash('Se eliminó "'.$producto->nombre.'".');
		}
		redirect('admin/productos');
	}
}
